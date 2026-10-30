<?php
namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Services\Reports\SupplierLedgerReportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SupplierLedgerController extends Controller
{
    public function show(Request $request, Supplier $supplier, SupplierLedgerReportService $ledgerReport)
    {
        $validated = $request->validate([
            'from_date' => 'nullable|date_format:Y-m-d',
            'to_date' => 'nullable|date_format:Y-m-d|after_or_equal:from_date',
            'type' => 'nullable|string|max:50',
        ]);
        $report = $ledgerReport->build($supplier, $validated['from_date'] ?? null, $validated['to_date'] ?? null, $validated['type'] ?? null);

        if ($request->boolean('csv')) {
            return $this->exportCsv($supplier, $report, $validated['from_date'] ?? null, $validated['to_date'] ?? null);
        }

        return view('suppliers.ledger', [
            'supplier' => $supplier, 'entries' => $report['visible'], 'running' => $report['running'],
            'opening' => $report['opening'], 'periodDebit' => $report['periodDebit'], 'periodCredit' => $report['periodCredit'],
        ]);
    }

    private function exportCsv(Supplier $supplier, array $report, ?string $from, ?string $to): StreamedResponse
    {
        $name = 'supplier-ledger-'.preg_replace('/[^A-Za-z0-9_-]+/', '-', strtolower($supplier->code ?: $supplier->name)).'.csv';
        return response()->streamDownload(function () use ($supplier, $report, $from, $to) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Supplier Ledger']);
            fputcsv($out, ['Supplier', $this->safeCsv($supplier->name)]);
            fputcsv($out, ['Period', ($from ?: 'Beginning').' to '.($to ?: 'Latest')]);
            fputcsv($out, ['Opening Balance', number_format($report['opening'], 2, '.', '')]);
            fputcsv($out, ['Date', 'Reference', 'Type', 'Description', 'Debit', 'Credit', 'Running Balance']);
            foreach ($report['visible'] as $entry) {
                fputcsv($out, [
                    $entry->transaction_date?->format('Y-m-d'),
                    $this->safeCsv($entry->reference_no),
                    $this->safeCsv($entry->entry_type),
                    $this->safeCsv($entry->description),
                    number_format((float) $entry->debit, 2, '.', ''),
                    number_format((float) $entry->credit, 2, '.', ''),
                    number_format((float) $entry->running_balance, 2, '.', ''),
                ]);
            }
            fputcsv($out, ['Period Debit', number_format($report['periodDebit'], 2, '.', '')]);
            fputcsv($out, ['Period Credit', number_format($report['periodCredit'], 2, '.', '')]);
            fputcsv($out, ['Closing Balance', number_format($report['running'], 2, '.', '')]);
            fclose($out);
        }, $name, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // Prevent Excel/LibreOffice from evaluating user-supplied ledger fields as formulas.
    private function safeCsv(?string $value): string
    {
        $value = (string) $value;
        return preg_match('/^[\x00-\x20]*[=+@\-]/', $value) ? "'".$value : $value;
    }
}
