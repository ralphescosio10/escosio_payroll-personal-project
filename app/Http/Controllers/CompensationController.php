<?php

namespace App\Http\Controllers;

use App\AuditLogger;
use App\Models\AtcRate;
use App\Models\CompensationRecord;
use App\Models\Employee;
use App\TaxFormCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompensationController extends Controller
{
    private function formDetailRoute(?string $taxForm): string
    {
        return match ($taxForm) {
            'MAP' => 'forms.map',
            'QAP' => 'forms.1604e',
            'SAWT' => 'forms.sawt',
            'Annual' => 'forms.annual',
            default => 'forms.index',
        };
    }

    public function index(Request $request)
    {
        $selectedTaxForm = $request->has('tax_form')
            ? (string) $request->input('tax_form', '')
            : session('selected_tax_form', 'SAWT');
        $query = CompensationRecord::with('employee');

        if ($selectedTaxForm !== '') {
            $query->where('tax_form', $selectedTaxForm);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('tin', 'like', "%{$search}%");
            });
        }

        if ($request->filled('pay_period')) {
            $query->where('pay_period', $request->pay_period);
        }

        if ($request->filled('tax_year')) {
            $query->where('tax_year', $request->tax_year);
        }

        if ($request->filled('atc_code')) {
            $query->where('atc_code', $request->atc_code);
        }

        $records = $query->latest()->paginate(20)->appends($request->query());

        return view('compensation.index', [
            'records' => $records,
            'search' => $request->search,
            'selectedPayPeriod' => $request->pay_period,
            'selectedTaxYear' => $request->tax_year,
            'selectedTaxForm' => $selectedTaxForm,
            'selectedAtcCode' => $request->atc_code,
            'taxForms' => TaxFormCatalog::labels(),
            'atcCodes' => AtcRate::query()->where('active', true)->orderBy('code')->pluck('code')->values(),
        ]);
    }

    public function create(Request $request)
    {
        $taxFormAliases = [
            'MAP Form' => 'MAP',
            'QAP Forms' => 'QAP',
            '1604-E' => 'QAP',
            'SAWT Forms' => 'SAWT',
            'Annual Form' => 'Annual',
            '1604-CF' => 'Annual',
        ];
        $requestedTaxForm = $request->query('tax_form');
        if ($requestedTaxForm !== null) {
            $request->merge([
                'tax_form' => $taxFormAliases[$requestedTaxForm] ?? $requestedTaxForm,
            ]);
        }
        $validated = $request->validate([
            'tax_form' => ['sometimes', 'required', 'in:MAP,QAP,SAWT,Annual'],
        ]);

        $selectedTaxForm = $validated['tax_form'] ?? session('selected_tax_form', 'MAP');
        $request->session()->put('selected_tax_form', $selectedTaxForm);

        return view('compensation.create', [
            'employees' => Employee::orderBy('last_name')->get(),
            'selectedTaxForm' => $selectedTaxForm,
            'selectedTaxFormLabel' => TaxFormCatalog::label($selectedTaxForm),
        ]);
    }

    public function store(Request $request)
    {
        $taxForm = $request->input('tax_form', session('selected_tax_form', 'MAP'));

        $rules = [
            'employee_id' => ['required', 'exists:employees,id'],
            'tax_year' => ['nullable', 'integer', 'min:2024'],
            'pay_period' => ['required', 'date_format:Y-m', 'unique:compensation_records,pay_period,NULL,id,employee_id,'.$request->employee_id.',tax_form,'.$taxForm],
            'tax_form' => ['nullable', 'in:MAP,QAP,SAWT,Annual'],
            'atc_code' => ['required', 'string', 'max:20'],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'gross_compensation' => ['required', 'numeric', 'min:0'],
            'non_taxable_compensation' => ['required', 'numeric', 'min:0'],
            'government_contributions' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ];

        if ($taxForm === 'SAWT') {
            $rules['amended_return'] = ['required', 'in:Y,N'];
            $rules['no_of_sheets_attached'] = ['nullable', 'integer', 'min:0'];
            $rules['tax_relief_special_law'] = ['required', 'in:Y,N'];
            $rules['quarter'] = ['required', 'in:1Q,2Q,3Q,4Q'];
        }

        $validated = $request->validate($rules);

        $employee = Employee::findOrFail($validated['employee_id']);
        $gross = (float) $validated['gross_compensation'];
        $nonTaxable = (float) $validated['non_taxable_compensation'];
        $government = (float) $validated['government_contributions'];
        $taxable = max(0, $gross - $nonTaxable - $government);

        $resolvedRate = (float) $validated['tax_rate'];
        $taxWithheld = round(($taxable * $resolvedRate) / 100, 2);
        $needsReview = $gross <= 0 || $taxable > $gross || $taxWithheld > $gross || $taxWithheld > $taxable;

        $taxForm = $validated['tax_form'] ?? session('selected_tax_form', 'MAP');

        $record = DB::transaction(function () use ($employee, $validated, $taxForm, $resolvedRate, $gross, $nonTaxable, $government, $taxable, $taxWithheld, $needsReview, $request): CompensationRecord {
            $record = $employee->compensationRecords()->create([
                'tax_year' => $validated['tax_year'] ?? now()->year,
                'pay_period' => $validated['pay_period'],
                'tax_form' => $taxForm,
                'amended_return' => $taxForm === 'SAWT' ? ($validated['amended_return'] ?? 'N') : null,
                'no_of_sheets_attached' => $taxForm === 'SAWT' ? ($validated['no_of_sheets_attached'] ?? 0) : null,
                'tax_relief_special_law' => $taxForm === 'SAWT' ? ($validated['tax_relief_special_law'] ?? 'N') : null,
                'quarter' => $taxForm === 'SAWT' ? ($validated['quarter'] ?? '1Q') : null,
                'atc_code' => $validated['atc_code'],
                'tax_rate' => $resolvedRate,
                'gross_compensation' => $gross,
                'non_taxable_compensation' => $nonTaxable,
                'government_contributions' => $government,
                'taxable_compensation' => $taxable,
                'tax_withheld' => $taxWithheld,
                'notes' => $validated['notes'] ?? null,
                'status' => $needsReview ? 'needs_review' : 'approved',
                'created_by' => Auth::id(),
            ]);

            $auditLogger = app(AuditLogger::class);
            $auditLogger->record($request, $record, 'created', [
                'after' => $auditLogger->snapshot($record),
            ]);

            return $record;
        });

        $request->session()->put('selected_tax_form', $taxForm);

        return redirect()->route($this->formDetailRoute($taxForm))->with('success', 'Compensation record saved.');
    }

    public function show(CompensationRecord $record)
    {
        return view('compensation.show', compact('record'));
    }

    public function edit(CompensationRecord $record)
    {
        return view('compensation.edit', [
            'record' => $record,
            'employees' => Employee::orderBy('last_name')->get(),
            'selectedTaxForm' => $record->tax_form,
            'selectedTaxFormLabel' => TaxFormCatalog::label($record->tax_form),
        ]);
    }

    public function update(Request $request, CompensationRecord $record)
    {
        $taxForm = $request->input('tax_form', $record->tax_form ?? 'MAP');

        $rules = [
            'employee_id' => ['required', 'exists:employees,id'],
            'tax_year' => ['nullable', 'integer', 'min:2024'],
            'pay_period' => ['required', 'date_format:Y-m', 'unique:compensation_records,pay_period,'.$record->id.',id,employee_id,'.$request->employee_id.',tax_form,'.$taxForm],
            'tax_form' => ['nullable', 'in:MAP,QAP,SAWT,Annual'],
            'atc_code' => ['required', 'string', 'max:20'],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'gross_compensation' => ['required', 'numeric', 'min:0'],
            'non_taxable_compensation' => ['required', 'numeric', 'min:0'],
            'government_contributions' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ];

        if ($taxForm === 'SAWT') {
            $rules['amended_return'] = ['required', 'in:Y,N'];
            $rules['no_of_sheets_attached'] = ['nullable', 'integer', 'min:0'];
            $rules['tax_relief_special_law'] = ['required', 'in:Y,N'];
            $rules['quarter'] = ['required', 'in:1Q,2Q,3Q,4Q'];
        }

        $validated = $request->validate($rules);

        $employee = Employee::findOrFail($validated['employee_id']);
        $gross = (float) $validated['gross_compensation'];
        $nonTaxable = (float) $validated['non_taxable_compensation'];
        $government = (float) $validated['government_contributions'];
        $taxable = max(0, $gross - $nonTaxable - $government);

        $resolvedRate = (float) $validated['tax_rate'];
        $taxWithheld = round(($taxable * $resolvedRate) / 100, 2);
        $needsReview = $gross <= 0 || $taxable > $gross || $taxWithheld > $gross || $taxWithheld > $taxable;

        DB::transaction(function () use ($record, $employee, $validated, $taxForm, $resolvedRate, $gross, $nonTaxable, $government, $taxable, $taxWithheld, $needsReview, $request): void {
            $auditLogger = app(AuditLogger::class);
            $before = $auditLogger->snapshot($record);

            $record->update([
                'employee_id' => $employee->id,
                'tax_year' => $validated['tax_year'] ?? $record->tax_year,
                'pay_period' => $validated['pay_period'],
                'tax_form' => $taxForm,
                'amended_return' => $taxForm === 'SAWT' ? ($validated['amended_return'] ?? 'N') : null,
                'no_of_sheets_attached' => $taxForm === 'SAWT' ? ($validated['no_of_sheets_attached'] ?? 0) : null,
                'tax_relief_special_law' => $taxForm === 'SAWT' ? ($validated['tax_relief_special_law'] ?? 'N') : null,
                'quarter' => $taxForm === 'SAWT' ? ($validated['quarter'] ?? '1Q') : null,
                'atc_code' => $validated['atc_code'],
                'tax_rate' => $resolvedRate,
                'gross_compensation' => $gross,
                'non_taxable_compensation' => $nonTaxable,
                'government_contributions' => $government,
                'taxable_compensation' => $taxable,
                'tax_withheld' => $taxWithheld,
                'notes' => $validated['notes'] ?? null,
                'status' => $needsReview ? 'needs_review' : 'approved',
            ]);

            $auditLogger->record($request, $record, 'updated', $auditLogger->changes($before, $record));
        });
        $request->session()->put('selected_tax_form', $taxForm);

        return redirect()->route($this->formDetailRoute($taxForm))->with('success', 'Compensation record updated.');
    }

    public function destroy(Request $request, CompensationRecord $record)
    {
        DB::transaction(function () use ($record, $request): void {
            $auditLogger = app(AuditLogger::class);
            $before = $auditLogger->snapshot($record);

            $record->delete();
            $auditLogger->record($request, $record, 'deleted', ['before' => $before]);
        });

        return redirect()->route('compensation.index')->with('success', 'Compensation record deleted.');
    }
}
