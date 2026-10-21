<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compensation Records</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-800">
    <div class="min-h-screen">
        <aside class="fixed inset-y-0 left-0 hidden w-72 border-r border-slate-200 bg-slate-950 p-6 text-slate-100 lg:block">
            <div class="mb-10">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-indigo-400">Payroll</p>
                <h1 class="mt-3 text-2xl font-bold">Alphalist</h1>
            </div>

            <nav class="space-y-2 text-sm">
                <a href="{{ route('dashboard') }}" class="flex items-center rounded-xl px-3 py-3 text-slate-300 transition hover:bg-slate-800 hover:text-white">Dashboard</a>
                <a href="{{ route('employees.index') }}" class="flex items-center rounded-xl px-3 py-3 text-slate-300 transition hover:bg-slate-800 hover:text-white">Employees</a>
                <a href="{{ route('compensation.index') }}?tax_form=" class="flex items-center rounded-xl bg-indigo-500/15 px-3 py-3 font-medium text-indigo-200">Compensation</a>
                <a href="{{ route('reports.index') }}" class="flex items-center rounded-xl px-3 py-3 text-slate-300 transition hover:bg-slate-800 hover:text-white">Reports</a>
                <a href="{{ route('forms.index') }}" class="flex items-center rounded-xl px-3 py-3 text-slate-300 transition hover:bg-slate-800 hover:text-white">Tax Form</a>
            </nav>

            <div class="mt-10 rounded-2xl border border-slate-800 bg-slate-900 p-4">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Current view</p>
                <p class="mt-2 text-xl font-semibold text-white">Compensation</p>
            </div>
        </aside>

        <main class="lg:ml-72">
            <header class="border-b border-slate-200 bg-white/80 backdrop-blur-sm">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-5 sm:px-6 lg:px-8">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-indigo-600">Operations</p>
                        <h2 class="mt-1 text-2xl font-bold text-slate-900">Compensation Records</h2>
                        <p class="mt-1 text-sm text-slate-600">Tax form: <span class="font-semibold">{{ $selectedTaxForm !== '' ? \App\TaxFormCatalog::label($selectedTaxForm) : 'All' }}</span></p>
                    </div>

                    <div class="flex items-center gap-3">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700">Logout</button>
                        </form>
                    </div>
                </div>
            </header>

            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <form method="GET" action="{{ route('compensation.index') }}" class="grid gap-4 md:grid-cols-6">
                        <label class="block md:col-span-2">
                            <span class="mb-1 block text-sm font-medium text-slate-600">Search employee</span>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="TIN or employee name" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10">
                        </label>
                        <label class="block">
                            <span class="mb-1 block text-sm font-medium text-slate-600">Pay period</span>
                            <input type="month" name="pay_period" value="{{ request('pay_period') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10">
                        </label>
                        <label class="block">
                            <span class="mb-1 block text-sm font-medium text-slate-600">Tax year</span>
                            <input type="number" name="tax_year" value="{{ request('tax_year') }}" min="2024" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10">
                        </label>
                        <label class="block">
                            <span class="mb-1 block text-sm font-medium text-slate-600">Tax form</span>
                            <select name="tax_form" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10">
                                <option value="">All</option>
                                @foreach ($taxForms as $form => $label)
                                    <option value="{{ $form }}" {{ request('tax_form') === $form ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="block">
                            <span class="mb-1 block text-sm font-medium text-slate-600">ATC code</span>
                            <select name="atc_code" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10">
                                <option value="">All</option>
                                @foreach ($atcCodes as $code)
                                    <option value="{{ $code }}" {{ request('atc_code') === $code ? 'selected' : '' }}>{{ $code }}</option>
                                @endforeach
                            </select>
                        </label>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="rounded-xl bg-indigo-500 px-4 py-2.5 text-sm font-medium text-white">Apply</button>
                            <a href="{{ route('compensation.index') }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700">Clear</a>
                        </div>
                    </form>
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th class="px-4 py-3 font-medium">Employee</th>
                                <th class="px-4 py-3 font-medium">Pay Period</th>
                                <th class="px-4 py-3 font-medium">Tax Form</th>
                                <th class="px-4 py-3 font-medium">Gross</th>
                                <th class="px-4 py-3 font-medium">Tax Withheld</th>
                                <th class="px-4 py-3 font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($records as $record)
                                <tr class="border-t border-slate-200">
                                    <td class="px-4 py-3">{{ $record->employee?->full_name }}</td>
                                    <td class="px-4 py-3">{{ $record->pay_period }}</td>
                                    <td class="px-4 py-3"><span class="rounded-full bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700">{{ $record->tax_form_label }}</span></td>
                                    <td class="px-4 py-3">₱{{ number_format($record->gross_compensation, 2) }}</td>
                                    <td class="px-4 py-3">₱{{ number_format($record->tax_withheld, 2) }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-2">
                                            <a href="{{ route('compensation.show', $record) }}" class="rounded border border-slate-300 px-2 py-1 text-xs font-medium text-slate-700">View</a>
                                            <a href="{{ route('compensation.edit', $record) }}" class="rounded border border-indigo-200 bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700">Edit</a>
                                            <form action="{{ route('compensation.destroy', $record) }}" method="POST" onsubmit="return confirm('Delete this record?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded border border-red-200 bg-red-50 px-2 py-1 text-xs font-medium text-red-700">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
