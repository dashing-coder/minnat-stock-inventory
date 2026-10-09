<?php

namespace App\Http\Controllers;

use App\Models\PayrollRun;
use App\Services\Payroll\PayrollService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PayrollController extends Controller
{
    public function __construct(private PayrollService $service)
    {
    }

    public function index()
    {
        return view('payrolls.index', [
            'rows' => PayrollRun::latest('id')->paginate(20),
        ]);
    }

    public function create()
    {
        return view('payrolls.form', [
            'existingPeriods' => PayrollRun::query()
                ->orderByDesc('period')
                ->pluck('period')
                ->values()
                ->all(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(
            [
                'period' => [
                    'required',
                    'date_format:Y-m',
                    Rule::unique('payroll_runs', 'period'),
                ],
                'pay_date' => ['nullable', 'date'],
                'notes' => ['nullable', 'string'],
            ],
            [
                'period.unique' => __('Payroll already exists for this period.'),
            ]
        );

        $run = $this->service->generate(
            $data['period'],
            $data['pay_date'] ?? null,
            $data['notes'] ?? null
        );

        return redirect()
            ->route('payrolls.show', $run)
            ->with('success', __('Payroll generated successfully.'));
    }

    public function show(PayrollRun $payroll)
    {
        $payroll->load('items.employee');

        return view('payrolls.show', compact('payroll'));
    }

    public function update(Request $request, PayrollRun $payroll)
    {
        $rows = $request->validate([
            'items' => ['required', 'array'],
            'items.*.bonus' => ['nullable', 'numeric', 'min:0'],
            'items.*.overtime' => ['nullable', 'numeric', 'min:0'],
            'items.*.deduction' => ['nullable', 'numeric', 'min:0'],
        ])['items'];

        $this->service->recalculate($payroll, $rows);

        return back()->with('success', __('Payroll recalculated.'));
    }

    public function post(PayrollRun $payroll)
    {
        $this->service->post($payroll);

        return back()->with('success', __('Payroll posted to accounts.'));
    }

    public function destroy(PayrollRun $payroll)
    {
        abort_unless(
            $payroll->status === 'draft',
            422,
            __('Posted payroll cannot be deleted.')
        );

        $payroll->delete();

        return redirect()->route('payrolls.index');
    }
}
