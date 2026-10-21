<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-800">
<div class="max-w-4xl mx-auto py-8 px-4">
    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('employees.index') }}" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">← Back to Employees</a>
        <a href="{{ route('employees.edit', $employee) }}" class="rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-medium text-indigo-700">Edit</a>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-sm uppercase tracking-[0.2em] text-indigo-600 font-semibold">Employee</p>
        <h1 class="mt-2 text-3xl font-bold">{{ $employee->full_name }}</h1>
        <p class="mt-1 text-slate-600">TIN: {{ $employee->tin }}</p>
    </div>

    <div class="mt-6 grid md:grid-cols-2 gap-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5"><p class="text-sm text-slate-500">Employment Status</p><p class="mt-2 text-xl font-semibold">{{ $employee->employment_status }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5"><p class="text-sm text-slate-500">Tax Year</p><p class="mt-2 text-xl font-semibold">{{ $employee->tax_year ?? now()->year }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5"><p class="text-sm text-slate-500">Monthly Salary</p><p class="mt-2 text-xl font-semibold">₱{{ number_format((float) $employee->monthly_salary, 2) }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5"><p class="text-sm text-slate-500">Address</p><p class="mt-2 text-xl font-semibold">{{ $employee->address ?? 'N/A' }}</p></div>
    </div>
</div>
</body>
</html>
