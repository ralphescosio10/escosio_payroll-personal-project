<?php

namespace App\Http\Controllers;

use App\AuditLogger;
use App\Models\CompensationRecord;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class AlphalistController extends Controller
{
    public function index()
    {
        $employees = Employee::with('compensationRecords')->latest()->get();
        $compensationRecords = CompensationRecord::with('employee')->latest()->take(10)->get();
        $allCompensationRecords = CompensationRecord::query();

        $totals = [
            'employees' => $employees->count(),
            'gross_compensation' => (float) $allCompensationRecords->sum('gross_compensation'),
            'taxable_compensation' => (float) $allCompensationRecords->sum('taxable_compensation'),
            'tax_withheld' => (float) $allCompensationRecords->sum('tax_withheld'),
        ];

        return view('dashboard', compact('employees', 'compensationRecords', 'totals'));
    }

    public function storeEmployee(Request $request)
    {
        $validated = $request->validate([
            'tin' => ['required', 'string', 'regex:/^\d{3}-\d{3}-\d{3}-\d{3}$/'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'employment_status' => ['required', 'string', 'max:255'],
            'monthly_salary' => ['required', 'numeric', 'min:0'],
            'address' => ['nullable', 'string', 'max:255'],
            'rdo' => ['nullable', 'string', 'max:255'],
        ], [
            'tin.regex' => 'TIN must follow the format 123-456-789-000.',
            'monthly_salary.numeric' => 'Monthly salary must be a valid numeric value.',
        ]);

        DB::transaction(function () use ($validated, $request): void {
            $employee = Employee::create($validated);

            $auditLogger = app(AuditLogger::class);
            $auditLogger->record($request, $employee, 'created', [
                'after' => $auditLogger->snapshot($employee),
            ]);
        });

        return redirect()->route('dashboard')->with('success', 'Employee record saved successfully.');
    }

    public function storeCompensation(Request $request)
    {
        $validated = $request->validate([
            'employee_tin' => ['required', 'exists:employees,tin'],
            'pay_period' => ['required', 'date_format:Y-m'],
            'tax_form' => ['nullable', 'in:MAP,QAP,SAWT,Annual'],
            'atc_code' => ['nullable', 'string', 'max:20'],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'gross_compensation' => ['required', 'numeric', 'min:0'],
            'non_taxable_compensation' => ['required', 'numeric', 'min:0'],
            'government_contributions' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ], [
            'employee_tin.exists' => 'The selected TIN does not exist in the employee masterlist.',
        ]);

        $employee = Employee::where('tin', $validated['employee_tin'])->firstOrFail();

        $gross = (float) $validated['gross_compensation'];
        $nonTaxable = (float) $validated['non_taxable_compensation'];
        $government = (float) $validated['government_contributions'];
        $taxable = max(0, $gross - $nonTaxable - $government);
        $atcCode = $validated['atc_code'] ?? 'A010';
        $resolvedRate = (float) $validated['tax_rate'];
        $taxWithheld = round(($taxable * $resolvedRate) / 100, 2);
        $taxForm = $validated['tax_form'] ?? session('selected_tax_form', 'MAP');

        $needsReview = $gross <= 0 || $taxable > $gross || $taxWithheld > $gross || $taxWithheld > $taxable;

        DB::transaction(function () use ($employee, $validated, $atcCode, $resolvedRate, $gross, $nonTaxable, $government, $taxable, $taxWithheld, $taxForm, $needsReview, $request): void {
            $record = $employee->compensationRecords()->create([
                'tax_year' => now()->year,
                'pay_period' => $validated['pay_period'],
                'tax_form' => $taxForm,
                'atc_code' => $atcCode,
                'tax_rate' => $resolvedRate,
                'gross_compensation' => $gross,
                'non_taxable_compensation' => $nonTaxable,
                'government_contributions' => $government,
                'taxable_compensation' => $taxable,
                'tax_withheld' => $taxWithheld,
                'notes' => $validated['notes'] ?? null,
                'status' => $needsReview ? 'needs_review' : 'approved',
                'created_by' => $request->user()?->getAuthIdentifier(),
            ]);

            $auditLogger = app(AuditLogger::class);
            $auditLogger->record($request, $record, 'created', [
                'after' => $auditLogger->snapshot($record),
            ]);
        });

        return redirect()->route('dashboard')->with('success', 'Compensation record saved successfully.');
    }

    public function importEmployeesCsv(Request $request)
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $path = $request->file('csv_file')->getRealPath();
        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 7) {
                continue;
            }

            $data = array_combine($header, $row);

            DB::transaction(function () use ($data, $request): void {
                $employee = Employee::firstOrNew(['tin' => trim((string) ($data['tin'] ?? ''))]);
                $auditLogger = app(AuditLogger::class);
                $before = $employee->exists ? $auditLogger->snapshot($employee) : null;
                $employee->fill([
                    'first_name' => $data['first_name'] ?? '',
                    'last_name' => $data['last_name'] ?? '',
                    'middle_name' => $data['middle_name'] ?? null,
                    'employment_status' => $data['employment_status'] ?? '',
                    'monthly_salary' => $data['monthly_salary'] ?? 0,
                    'address' => $data['address'] ?? null,
                    'rdo' => $data['rdo'] ?? null,
                ]);
                $employee->save();

                $auditLogger->record($request, $employee, $before === null ? 'created' : 'updated', [
                    'before' => $before,
                    'after' => $auditLogger->snapshot($employee),
                ]);
            });
        }

        fclose($handle);

        return redirect()->route('dashboard')->with('success', 'CSV employee import completed.');
    }

    public function exportEmployeesCsv()
    {
        $employees = Employee::all();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="employees.csv"',
        ];

        $callback = function () use ($employees) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['tin', 'first_name', 'last_name', 'middle_name', 'employment_status', 'monthly_salary', 'address', 'rdo']);

            foreach ($employees as $employee) {
                fputcsv($handle, [
                    $employee->tin,
                    $employee->first_name,
                    $employee->last_name,
                    $employee->middle_name,
                    $employee->employment_status,
                    $employee->monthly_salary,
                    $employee->address,
                    $employee->rdo,
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportCompensationCsv()
    {
        $records = CompensationRecord::with('employee')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="compensation-records.csv"',
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
                    $record->gross_compensation,
                    $record->taxable_compensation,
                    $record->tax_withheld,
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }
}
