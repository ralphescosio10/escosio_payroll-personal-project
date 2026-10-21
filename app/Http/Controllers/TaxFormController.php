<?php

namespace App\Http\Controllers;

use App\Models\CompensationRecord;
use App\TaxFormCatalog;
use Illuminate\Http\Request;

class TaxFormController extends Controller
{
    private function formRecords(Request $request, string $taxForm)
    {
        return CompensationRecord::with('employee')
            ->where('tax_form', $taxForm)
            ->when($request->filled('tax_year'), fn ($query) => $query->where('tax_year', $request->tax_year))
            ->when($request->filled('pay_period'), fn ($query) => $query->where('pay_period', $request->pay_period))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->whereHas('employee', function ($employeeQuery) use ($search) {
                    $employeeQuery->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('tin', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();
    }

    private function detailView(Request $request, string $taxForm, string $title, string $subtitle)
    {
        $records = $this->formRecords($request, $taxForm);

        return view('forms.detail', [
            'title' => $title,
            'taxForm' => $taxForm,
            'subtitle' => $subtitle,
            'records' => $records,
            'filters' => [
                'taxYear' => $request->tax_year,
                'payPeriod' => $request->pay_period,
                'search' => $request->search,
            ],
            'totals' => [
                'gross' => (float) $records->sum('gross_compensation'),
                'taxable' => (float) $records->sum('taxable_compensation'),
                'withheld' => (float) $records->sum('tax_withheld'),
            ],
        ]);
    }

    public function index()
    {
        $forms = array_keys(TaxFormCatalog::labels());
        $totalsByForm = [];

        foreach ($forms as $form) {
            $records = CompensationRecord::with('employee')->where('tax_form', $form);
            $totalsByForm[$form] = [
                'count' => $records->count(),
                'gross' => (float) $records->sum('gross_compensation'),
                'taxable' => (float) $records->sum('taxable_compensation'),
                'withheld' => (float) $records->sum('tax_withheld'),
            ];
        }

        return view('forms.index', [
            'totalsByForm' => $totalsByForm,
        ]);
    }

    public function map(Request $request)
    {
        $request->session()->put('selected_tax_form', 'MAP');

        return $this->detailView($request, 'MAP', 'MAP Form', 'Monthly Alphalist of Payees');
    }

    public function qap()
    {
        return redirect()->route('forms.1604e');
    }

    public function form1604e(Request $request)
    {
        $request->session()->put('selected_tax_form', 'QAP');

        return $this->detailView($request, 'QAP', '1604-E', 'Expanded Withholding and Exempt Payees');
    }

    public function sawt(Request $request)
    {
        $request->session()->put('selected_tax_form', 'SAWT');

        return $this->detailView($request, 'SAWT', 'SAWT Forms', 'Summary of Withholding Tax');
    }

    public function annual(Request $request)
    {
        $request->session()->put('selected_tax_form', 'Annual');

        $records = CompensationRecord::with('employee')
            ->where('tax_form', 'Annual')
            ->when($request->filled('tax_year'), fn ($query) => $query->where('tax_year', $request->tax_year))
            ->latest()
            ->get();
        $years = $records->pluck('tax_year')->filter()->unique()->sort()->values();

        return view('forms.annual', [
            'records' => $records,
            'years' => $years,
        ]);
    }
}
