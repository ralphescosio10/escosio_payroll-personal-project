<?php

namespace App\Http\Controllers;

use App\Models\CompensationRecord;
use App\Models\Employee;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $taxYear = $request->query('tax_year', now()->year);
        $selectedTaxForm = session('selected_tax_form', 'SAWT');

        $records = CompensationRecord::query()->where('tax_form', $selectedTaxForm);
        $employeeIds = $records->clone()->pluck('employee_id')->unique()->values();

        $employees = Employee::query()->whereIn('id', $employeeIds);
        $recentEmployees = Employee::query()->latest()->take(5);

        if ($taxYear) {
            $employees->where('tax_year', $taxYear);
            $records->where('tax_year', $taxYear);
            $recentEmployees->where('tax_year', $taxYear);
        }

        $employeeCount = $employees->count();
        $gross = (float) $records->sum('gross_compensation');
        $taxWithheld = (float) $records->sum('tax_withheld');

        $attentionQuery = CompensationRecord::query()
            ->where('tax_form', $selectedTaxForm)
            ->where(function ($query) {
                $query->where('status', 'needs_review')
                    ->orWhereRaw('tax_withheld > gross_compensation')
                    ->orWhereRaw('taxable_compensation > gross_compensation');
            });

        if ($taxYear) {
            $attentionQuery->where('tax_year', $taxYear);
        }

        $attention = $attentionQuery->count();
        $reviewItems = $attentionQuery->with('employee')->latest()->take(3)->get();

        return view('dashboard.index', [
            'taxYear' => (int) $taxYear,
            'selectedTaxForm' => $selectedTaxForm,
            'employeeCount' => $employeeCount,
            'grossCompensation' => $gross,
            'taxWithheld' => $taxWithheld,
            'recordsRequiringAttention' => $attention,
            'reviewItems' => $reviewItems,
            'employees' => $recentEmployees->get(),
            'records' => CompensationRecord::with('employee')->where('tax_form', $selectedTaxForm)->latest()->take(5)->get(),
            'years' => range(2024, now()->year + 2),
        ]);
    }
}
