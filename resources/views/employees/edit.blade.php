<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Employee</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-800">
<div class="max-w-4xl mx-auto py-8 px-4">
    <div class="mb-4">
        <a href="{{ route('employees.index') }}" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">← Back to Employees</a>
    </div>
    <h1 class="text-3xl font-bold mb-6">Edit Employee</h1>
    <form method="POST" action="{{ route('employees.update', $employee) }}" class="space-y-4 bg-white rounded-2xl border shadow-sm p-6">
        @csrf
        @method('PUT')
        <div class="grid md:grid-cols-2 gap-4">
            <label class="block">
                <span class="text-sm font-medium">TIN</span>
                <input name="tin" value="{{ old('tin', $employee->tin) }}" class="mt-1 w-full rounded-lg border px-3 py-2" required>
            </label>
            <label class="block">
                <span class="text-sm font-medium">Tax Year</span>
                <input name="tax_year" type="number" value="{{ old('tax_year', $employee->tax_year ?? now()->year) }}" class="mt-1 w-full rounded-lg border px-3 py-2" required>
            </label>
        </div>
        <div class="grid md:grid-cols-3 gap-4">
            <label class="block">
                <span class="text-sm font-medium">First Name</span>
                <input name="first_name" value="{{ old('first_name', $employee->first_name) }}" class="mt-1 w-full rounded-lg border px-3 py-2" required>
            </label>
            <label class="block">
                <span class="text-sm font-medium">Middle Name</span>
                <input name="middle_name" value="{{ old('middle_name', $employee->middle_name) }}" class="mt-1 w-full rounded-lg border px-3 py-2">
            </label>
            <label class="block">
                <span class="text-sm font-medium">Last Name</span>
                <input name="last_name" value="{{ old('last_name', $employee->last_name) }}" class="mt-1 w-full rounded-lg border px-3 py-2" required>
            </label>
        </div>
        <div class="grid md:grid-cols-2 gap-4">
            <label class="block">
                <span class="text-sm font-medium">Employment Status</span>
                <select name="employment_status" class="mt-1 w-full rounded-lg border px-3 py-2" required>
                    <option value="Regular" {{ old('employment_status', $employee->employment_status) === 'Regular' ? 'selected' : '' }}>Regular</option>
                    <option value="Probationary" {{ old('employment_status', $employee->employment_status) === 'Probationary' ? 'selected' : '' }}>Probationary</option>
                    <option value="Contractual" {{ old('employment_status', $employee->employment_status) === 'Contractual' ? 'selected' : '' }}>Contractual</option>
                    <option value="Part-time" {{ old('employment_status', $employee->employment_status) === 'Part-time' ? 'selected' : '' }}>Part-time</option>
                    <option value="Casual" {{ old('employment_status', $employee->employment_status) === 'Casual' ? 'selected' : '' }}>Casual</option>
                </select>
            </label>
            <label class="block">
                <span class="text-sm font-medium">Monthly Salary</span>
                <input name="monthly_salary" type="number" step="0.01" value="{{ old('monthly_salary', $employee->monthly_salary) }}" class="mt-1 w-full rounded-lg border px-3 py-2" required>
            </label>
        </div>
        <label class="block">
            <span class="text-sm font-medium">Address</span>
            <textarea name="address" rows="3" class="mt-1 w-full rounded-lg border px-3 py-2">{{ old('address', $employee->address) }}</textarea>
        </label>
        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-white font-medium">Update Employee</button>
    </form>
</div>
</body>
</html>
