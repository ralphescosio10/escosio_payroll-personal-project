<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alphalist Reports</title>
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
                <a href="{{ route('compensation.index') }}?tax_form=" class="flex items-center rounded-xl px-3 py-3 text-slate-300 transition hover:bg-slate-800 hover:text-white">Compensation</a>
                <a href="{{ route('reports.index') }}" class="flex items-center rounded-xl bg-indigo-500/15 px-3 py-3 font-medium text-indigo-200">Reports</a>
                <a href="{{ route('forms.index') }}" class="flex items-center rounded-xl px-3 py-3 text-slate-300 transition hover:bg-slate-800 hover:text-white">Tax Form</a>
            </nav>
            
            <div class="mt-10 rounded-2xl border border-slate-800 bg-slate-900 p-4">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Current view</p>
                <p class="mt-2 text-xl font-semibold text-white">Reports</p>
            </div>
        </aside>

        <main class="lg:ml-72">
            <header class="border-b border-slate-200 bg-white/80 backdrop-blur-sm">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-5 sm:px-6 lg:px-8">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-indigo-600">Operations</p>
                        <h2 class="mt-1 text-2xl font-bold text-slate-900">Reports</h2>
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
                    <form method="GET" action="{{ route('reports.index') }}" class="grid gap-4 md:grid-cols-5">
                        <label class="block">
                            <span class="mb-1 block text-sm font-medium text-slate-600">Pay period</span>
                            <input type="month" name="pay_period" value="{{ request('pay_period') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10">
                        </label>
                        <label class="block">
                            <span class="mb-1 block text-sm font-medium text-slate-600">Tax year</span>
                            <input type="number" name="tax_year" value="{{ request('tax_year') }}" min="2024" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10">
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
                        <label class="block">
                            <span class="mb-1 block text-sm font-medium text-slate-600">Tax form</span>
                            <select name="tax_form" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10">
                                <option value="">All</option>
                                @foreach ($taxForms as $form => $label)
                                    <option value="{{ $form }}" {{ $selectedTaxForm === $form ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="rounded-xl bg-indigo-500 px-4 py-2.5 text-sm font-medium text-white">Apply</button>
                            <a href="{{ route('reports.index') }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700">Clear</a>
                        </div>
                    </form>
                </div>

                <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-sm uppercase tracking-[0.2em] text-indigo-600 font-semibold">Reports</p>
                        <h1 class="text-3xl font-bold text-slate-900">Alphalist Summary</h1>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('reports.export.csv') }}" class="rounded-xl bg-indigo-500 px-4 py-2 text-sm font-medium text-white">Export CSV</a>
                        <a href="{{ route('reports.export.excel') }}" class="rounded-xl bg-emerald-500 px-4 py-2 text-sm font-medium text-white">Export Excel</a>
                        <a href="{{ route('reports.export.pdf') }}" class="rounded-xl bg-rose-500 px-4 py-2 text-sm font-medium text-white">Export PDF</a>
                    </div>
                </div>

                <div class="mb-8 grid gap-4 md:grid-cols-3">
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-sm text-slate-500">Gross</p>
                        <p class="mt-3 text-3xl font-bold text-slate-900">₱{{ number_format($totals['gross'], 2) }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-sm text-slate-500">Taxable</p>
                        <p class="mt-3 text-3xl font-bold text-slate-900">₱{{ number_format($totals['taxable'], 2) }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-sm text-slate-500">Tax Withheld</p>
                        <p class="mt-3 text-3xl font-bold text-slate-900">₱{{ number_format($totals['withheld'], 2) }}</p>
                    </div>
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th class="px-4 py-3 font-medium">TIN</th>
                                <th class="px-4 py-3 font-medium">Employee</th>
                                <th class="px-4 py-3 font-medium">Pay Period</th>
                                <th class="px-4 py-3 font-medium">Tax Form</th>
                                <th class="px-4 py-3 font-medium">Gross</th>
                                <th class="px-4 py-3 font-medium">Taxable</th>
                                <th class="px-4 py-3 font-medium">Tax Withheld</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($records as $record)
                                <tr class="border-t border-slate-200">
                                    <td class="px-4 py-3">{{ $record->employee?->tin }}</td>
                                    <td class="px-4 py-3">{{ $record->employee?->full_name }}</td>
                                    <td class="px-4 py-3">{{ $record->pay_period }}</td>
                                    <td class="px-4 py-3">{{ $record->tax_form_label }}</td>
                                    <td class="px-4 py-3">₱{{ number_format($record->gross_compensation, 2) }}</td>
                                    <td class="px-4 py-3">₱{{ number_format($record->taxable_compensation, 2) }}</td>
                                    <td class="px-4 py-3">₱{{ number_format($record->tax_withheld, 2) }}</td>
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
