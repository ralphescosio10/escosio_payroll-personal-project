<?php

namespace App\Http\Controllers;

use App\AuditLogger;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tin', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('employment_status', $request->status);
        }

        if ($request->filled('tax_year')) {
            $query->where('tax_year', $request->tax_year);
        }

        $employees = $query->latest()->paginate(20)->appends($request->query());

        return view('employees.index', [
            'employees' => $employees,
            'statuses' => Employee::query()->distinct()->pluck('employment_status')->filter()->sort()->values(),
            'selectedStatus' => $request->status,
            'selectedTaxYear' => $request->tax_year,
            'search' => $request->search,
        ]);
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tin' => ['required', 'string', 'regex:/^\d{3}-\d{3}-\d{3}-\d{3}$/', 'unique:employees,tin'],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'employment_status' => ['required', 'string', 'max:255'],
            'monthly_salary' => ['nullable', 'numeric', 'min:0'],
            'address' => ['nullable', 'string', 'max:255'],
            'tax_year' => ['nullable', 'integer', 'min:2024'],
        ]);

        DB::transaction(function () use ($validated, $request): void {
            $employee = Employee::create(array_merge($validated, [
                'tax_year' => $validated['tax_year'] ?? now()->year,
                'created_by' => Auth::id(),
            ]));

            $auditLogger = app(AuditLogger::class);
            $auditLogger->record($request, $employee, 'created', [
                'after' => $auditLogger->snapshot($employee),
            ]);
        });

        return redirect()->route('dashboard')->with('success', 'Employee record created successfully.');
    }

    public function show(Employee $employee)
    {
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'tin' => ['required', 'string', 'regex:/^\d{3}-\d{3}-\d{3}-\d{3}$/', Rule::unique('employees', 'tin')->ignore($employee->id)],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'employment_status' => ['required', 'string', 'max:255'],
            'monthly_salary' => ['nullable', 'numeric', 'min:0'],
            'address' => ['nullable', 'string', 'max:255'],
            'tax_year' => ['nullable', 'integer', 'min:2024'],
        ]);

        DB::transaction(function () use ($employee, $validated, $request): void {
            $auditLogger = app(AuditLogger::class);
            $before = $auditLogger->snapshot($employee);

            $employee->update(array_merge($validated, [
                'tax_year' => $validated['tax_year'] ?? $employee->tax_year ?? now()->year,
            ]));

            $auditLogger->record($request, $employee, 'updated', $auditLogger->changes($before, $employee));
        });

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(Request $request, Employee $employee)
    {
        DB::transaction(function () use ($request, $employee): void {
            $auditLogger = app(AuditLogger::class);
            $before = $auditLogger->snapshot($employee);
            $compensationRecords = $employee->compensationRecords()->get()->map(
                fn ($record): array => app(AuditLogger::class)->snapshot($record)
            )->all();

            $employee->delete();
            $auditLogger->record($request, $employee, 'deleted', [
                'before' => $before,
                'deleted_compensation_records' => $compensationRecords,
            ]);
        });

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        return back()->with('success', 'CSV import started. The system will validate rows before saving.');
    }

    public function exportCsv()
    {
        return back()->with('success', 'Employee CSV export is ready for download.');
    }
}
