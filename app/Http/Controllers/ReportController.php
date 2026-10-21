<?php

namespace App\Http\Controllers;

use App\Models\AtcRate;
use App\Models\CompensationRecord;
use App\TaxFormCatalog;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = CompensationRecord::with('employee');

        if ($request->filled('pay_period')) {
            $query->where('pay_period', $request->pay_period);
        }

        if ($request->filled('tax_year')) {
            $query->where('tax_year', $request->tax_year);
        }

        if ($request->filled('atc_code')) {
            $query->where('atc_code', $request->atc_code);
        }

        if ($request->filled('tax_form')) {
            $query->where('tax_form', $request->tax_form);
        }

        $records = $query->latest()->get();

        return view('reports.index', [
            'records' => $records,
            'selectedPayPeriod' => $request->pay_period,
            'selectedTaxYear' => $request->tax_year,
            'selectedAtcCode' => $request->atc_code,
            'selectedTaxForm' => $request->tax_form,
            'taxForms' => TaxFormCatalog::labels(),
            'atcCodes' => AtcRate::query()->where('active', true)->orderBy('code')->pluck('code')->values(),
            'totals' => [
                'gross' => $records->sum('gross_compensation'),
                'taxable' => $records->sum('taxable_compensation'),
                'withheld' => $records->sum('tax_withheld'),
            ],
        ]);
    }

    public function exportCsv()
    {
        $records = CompensationRecord::with('employee')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="alphalist-report.csv"',
        ];

        $callback = function () use ($records) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['TIN', 'Employee', 'Pay Period', 'Tax Form', 'Gross Compensation', 'Taxable Compensation', 'Tax Withheld']);

            foreach ($records as $record) {
                fputcsv($handle, [
                    $record->employee?->tin,
                    $record->employee?->full_name,
                    $record->pay_period,
                    $record->tax_form_label,
                    number_format($record->gross_compensation, 2, '.', ''),
                    number_format($record->taxable_compensation, 2, '.', ''),
                    number_format($record->tax_withheld, 2, '.', ''),
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportPdf()
    {
        $records = CompensationRecord::with('employee')->latest()->get();

        $options = new Options;
        $options->set('defaultFont', 'DejaVu Sans');

        $pdf = new Dompdf($options);
        $html = view('reports.pdf', [
            'records' => $records,
            'generatedAt' => now()->format('F j, Y h:i A'),
            'totals' => [
                'gross' => $records->sum('gross_compensation'),
                'taxable' => $records->sum('taxable_compensation'),
                'withheld' => $records->sum('tax_withheld'),
            ],
        ])->render();

        $pdf->loadHtml($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="alphalist-report.pdf"');
    }

    public function exportExcel()
    {
        $records = CompensationRecord::with('employee')->get();

        $content = "TIN,Employee,Pay Period,Tax Form,Gross Compensation,Taxable Compensation,Tax Withheld\n";

        foreach ($records as $record) {
            $content .= implode(',', [
                $record->employee?->tin,
                '"'.$record->employee?->full_name.'"',
                $record->pay_period,
                '"'.$record->tax_form_label.'"',
                number_format($record->gross_compensation, 2, '.', ''),
                number_format($record->taxable_compensation, 2, '.', ''),
                number_format($record->tax_withheld, 2, '.', ''),
            ])."\n";
        }

        return Response::make($content, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="alphalist-report.xls"',
        ]);
    }
}
