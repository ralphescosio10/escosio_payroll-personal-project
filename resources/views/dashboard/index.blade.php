<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-800">
    <div class="min-h-screen">
        <aside class="fixed inset-y-0 left-0 hidden w-72 border-r border-slate-200 bg-slate-950 p-6 text-slate-100 lg:block">
            <div class="mb-8 flex items-center gap-3 rounded-2xl border border-slate-800 bg-slate-900/80 p-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-violet-600 text-sm font-bold text-white shadow-lg">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-white">{{ Auth::user()->name ?? 'User' }}</p>
                    <p class="truncate text-xs text-slate-400">{{ Auth::user()->email }}</p>
                </div>
            </div>

            <div class="mb-10">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-indigo-400">Payroll</p>
                <h1 class="mt-3 text-2xl font-bold">Alphalist</h1>
            </div>

            <nav class="space-y-2 text-sm">
                <a href="{{ route('dashboard') }}" class="flex items-center rounded-xl bg-indigo-500/15 px-3 py-3 font-medium text-indigo-200">Dashboard</a>
                <a href="{{ route('employees.index') }}" class="flex items-center rounded-xl px-3 py-3 text-slate-300 transition hover:bg-slate-800 hover:text-white">Employees</a>
                <a href="{{ route('compensation.index') }}" class="flex items-center rounded-xl px-3 py-3 text-slate-300 transition hover:bg-slate-800 hover:text-white">Compensation</a>
                <a href="{{ route('reports.index') }}" class="flex items-center rounded-xl px-3 py-3 text-slate-300 transition hover:bg-slate-800 hover:text-white">Reports</a>
                <a href="{{ route('forms.choose') }}" class="flex items-center rounded-xl px-3 py-3 text-slate-300 transition hover:bg-slate-800 hover:text-white">Tax Form</a>
            </nav>

            <div class="mt-10 rounded-2xl border border-slate-800 bg-slate-900 p-4">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Current form</p>
                <p class="mt-2 text-xl font-semibold text-white">{{ $selectedTaxForm ?? session('selected_tax_form', 'SAWT') }}</p>
            </div>
        </aside>

        <main class="lg:ml-72">
            <header class="border-b border-slate-200 bg-white/80 backdrop-blur-sm">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-5 sm:px-6 lg:px-8">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-indigo-600">Operations</p>
                        <h2 class="mt-1 text-2xl font-bold text-slate-900">Dashboard</h2>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('forms.choose') }}" class="rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-2 text-sm font-medium text-indigo-700">Choose Tax Form</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700">Logout</button>
                        </form>
                    </div>
                </div>
            </header>

            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                @if (session('success'))
                    <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="mb-8 rounded-2xl border border-emerald-200 bg-gradient-to-r from-emerald-50 to-indigo-50 px-5 py-4 text-sm font-medium text-emerald-800">
                    Tax form selected: <span class="font-bold">{{ $selectedTaxForm ?? session('selected_tax_form', 'SAWT') }}</span>
                    <span class="ml-2 text-emerald-700">•</span>
                    <span>Tax year: {{ $taxYear }}</span>
                </div>

                <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-sm text-slate-500">Employees</p>
                        <p class="mt-3 text-3xl font-bold text-slate-900">{{ $employeeCount }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-sm text-slate-500">Gross compensation</p>
                        <p class="mt-3 text-3xl font-bold text-slate-900">₱{{ number_format($grossCompensation, 2) }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-sm text-slate-500">Tax withheld</p>
                        <p class="mt-3 text-3xl font-bold text-slate-900">₱{{ number_format($taxWithheld, 2) }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-sm text-slate-500">Needs review</p>
                        <p class="mt-3 text-3xl font-bold text-amber-600">{{ $recordsRequiringAttention }}</p>
                    </div>
                </section>

                <section class="mt-8 grid gap-8 lg:grid-cols-[1.2fr_0.8fr]">
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="mb-5 flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-slate-900">Recent employees</h3>
                            <a href="{{ route('employees.index') }}" class="text-sm font-medium text-indigo-600">View all</a>
                        </div>

                        <div class="overflow-hidden rounded-xl border border-slate-200">
                            <table class="min-w-full text-left text-sm">
                                <thead class="bg-slate-50 text-slate-600">
                                    <tr>
                                        <th class="px-4 py-3 font-medium">Employee</th>
                                        <th class="px-4 py-3 font-medium">TIN</th>
                                        <th class="px-4 py-3 font-medium">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($employees as $employee)
                                        <tr class="border-t border-slate-200">
                                            <td class="px-4 py-3">{{ $employee->full_name }}</td>
                                            <td class="px-4 py-3 text-slate-500">{{ $employee->tin }}</td>
                                            <td class="px-4 py-3"><span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">{{ $employee->employment_status }}</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-4 py-8 text-center text-slate-500">No recent employees found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="mb-5 flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-slate-900">Quick actions</h3>
                        </div>
                        <div class="space-y-3">
                            <a href="{{ route('employees.create') }}" class="block rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">Add employee</a>
                            <a href="{{ route('compensation.create', ['tax_form' => session('selected_tax_form', 'MAP')]) }}" class="block rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-emerald-200 hover:bg-emerald-50 hover:text-emerald-700">Add compensation record</a>
                            <a href="{{ route('reports.index') }}" class="block rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-cyan-200 hover:bg-cyan-50 hover:text-cyan-700">View reports</a>
                        </div>
                    </div>
                </section>

                @if ($reviewItems->isNotEmpty())
                    <section class="mt-8 rounded-2xl border border-amber-200 bg-amber-50 p-6 shadow-sm">
                        <div class="mb-5 flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-amber-800">Review alerts</h3>
                            <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">{{ $recordsRequiringAttention }}</span>
                        </div>

                        <div class="space-y-3">
                            @foreach ($reviewItems as $reviewItem)
                                <div class="rounded-xl border border-amber-200 bg-white px-4 py-3 text-sm text-amber-900">
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="font-semibold">{{ $reviewItem->employee?->full_name ?? 'Unknown employee' }}</span>
                                        <span class="rounded-full bg-amber-100 px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.2em] text-amber-700">{{ $reviewItem->status }}</span>
                                    </div>
                                    <p class="mt-1 text-amber-800">
                                        {{ $reviewItem->pay_period }} • {{ $reviewItem->tax_form }} • Gross ₱{{ number_format($reviewItem->gross_compensation, 2) }} • Withheld ₱{{ number_format($reviewItem->tax_withheld, 2) }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-5 flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-slate-900">Recent records</h3>
                        <a href="{{ route('compensation.index') }}" class="text-sm font-medium text-indigo-600">Open list</a>
                    </div>

                    <div class="overflow-hidden rounded-xl border border-slate-200">
                        <table class="min-w-full text-left text-sm">
                            <thead class="bg-slate-50 text-slate-600">
                                <tr>
                                    <th class="px-4 py-3 font-medium">Employee</th>
                                    <th class="px-4 py-3 font-medium">Pay period</th>
                                    <th class="px-4 py-3 font-medium">Form</th>
                                    <th class="px-4 py-3 font-medium">Gross</th>
                                    <th class="px-4 py-3 font-medium">Withheld</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($records as $record)
                                    <tr class="border-t border-slate-200">
                                        <td class="px-4 py-3">{{ $record->employee?->full_name ?? 'Unknown employee' }}</td>
                                        <td class="px-4 py-3 text-slate-500">{{ $record->pay_period }}</td>
                                        <td class="px-4 py-3"><span class="rounded-full bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700">{{ $record->tax_form }}</span></td>
                                        <td class="px-4 py-3">₱{{ number_format($record->gross_compensation, 2) }}</td>
                                        <td class="px-4 py-3">₱{{ number_format($record->tax_withheld, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-slate-500">No compensation records available for this filter.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
