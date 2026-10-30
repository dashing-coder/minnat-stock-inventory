<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\Reports\CustomerLedgerReportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerLedgerController extends Controller
{
    public function __construct(private CustomerLedgerReportService $ledgerReport)
    {
    }

    public function show(Request $request, Customer $customer)
    {
        $request->validate([
            'from_date' => 'nullable|date_format:Y-m-d',
            'to_date' => 'nullable|date_format:Y-m-d|after_or_equal:from_date',
            'type' => 'nullable|string|max:50',
        ]);
        $from = $request->filled('from_date') ? $request->string('from_date')->toString() : null;
        $to = $request->filled('to_date') ? $request->string('to_date')->toString() : null;
        $type = $request->filled('type') ? $request->string('type')->toString() : null;

        $ledger = $this->ledgerReport->build($customer, $from, $to, $type);
        $entryTypes = $customer->ledgerEntries()->select('entry_type')->distinct()->orderBy('entry_type')->pluck('entry_type');

        if ($request->boolean('csv')) {
            return $this->csv($customer, $ledger, $from, $to);
        }

        return view('customers.ledger', [
            'customer' => $customer,
            'entries' => $ledger['entries'],
            'openingBalance' => $ledger['opening_balance'],
            'closingBalance' => $ledger['closing_balance'],
            'periodDebit' => $ledger['period_debit'],
            'periodCredit' => $ledger['period_credit'],
            'entryTypes' => $entryTypes,
            'from' => $from,
            'to' => $to,
        ]);
    }

    private function safeLedgerCsv(?string $value): string
    {
        $value = (string) $value;
        return preg_match('/^[\x00-\x20]*[=+@\-]/', $value) ? "'".$value : $value;
    }

    private function csv(Customer $customer, array $ledger, ?string $from, ?string $to): StreamedResponse
    {
        $filename = 'client-ledger-'.preg_replace('/[^A-Za-z0-9_-]+/', '-', strtolower($customer->code ?: $customer->name)).'.csv';

        return response()->streamDownload(function () use ($customer, $ledger, $from, $to) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Client Ledger']);
            fputcsv($out, ['Customer', $this->safeLedgerCsv($customer->name)]);
            fputcsv($out, ['Code', $this->safeLedgerCsv($customer->code)]);
            fputcsv($out, ['Period', trim(($from ?: 'Beginning').' to '.($to ?: 'Latest'))]);
            fputcsv($out, []);
            fputcsv($out, ['Opening Balance', number_format((float) $ledger['opening_balance'], 2, '.', '')]);
            fputcsv($out, ['Date', 'Reference', 'Type', 'Description', 'Debit', 'Credit', 'Running Balance']);
            foreach ($ledger['entries'] as $entry) {
                fputcsv($out, [
                    optional($entry->transaction_date)->format('Y-m-d'),
                    $this->safeLedgerCsv($entry->reference_no),
                    $this->safeLedgerCsv($entry->entry_type),
                    $this->safeLedgerCsv($entry->description),
                    number_format((float) $entry->debit, 2, '.', ''),
                    number_format((float) $entry->credit, 2, '.', ''),
                    number_format((float) $entry->running_balance, 2, '.', ''),
                ]);
            }
            fputcsv($out, []);
            fputcsv($out, ['Period Debit', number_format((float) $ledger['period_debit'], 2, '.', '')]);
            fputcsv($out, ['Period Credit', number_format((float) $ledger['period_credit'], 2, '.', '')]);
            fputcsv($out, ['Closing Balance', number_format((float) $ledger['closing_balance'], 2, '.', '')]);
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
