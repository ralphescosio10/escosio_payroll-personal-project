<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>1604-CF Annual Alphalist</title>
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
                <a href="{{ route('reports.index') }}" class="flex items-center rounded-xl px-3 py-3 text-slate-300 transition hover:bg-slate-800 hover:text-white">Reports</a>
                <a href="{{ route('forms.index') }}" class="flex items-center rounded-xl bg-indigo-500/15 px-3 py-3 font-medium text-indigo-200">Tax Form</a>
            </nav>

            <div class="mt-10 rounded-2xl border border-slate-800 bg-slate-900 p-4">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Current form</p>
                <p class="mt-2 text-xl font-semibold text-white">1604-CF</p>
            </div>
        </aside>

        <main class="lg:ml-72">
            <header class="border-b border-slate-200 bg-white/80 backdrop-blur-sm">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-5 sm:px-6 lg:px-8">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-indigo-600">Operations</p>
                        <h2 class="mt-1 text-2xl font-bold text-slate-900">1604-CF Annual Alphalist</h2>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('forms.index') }}" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700">Back to Forms</a>
                        <a href="{{ route('compensation.create', ['tax_form' => 'Annual']) }}" class="rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-2 text-sm font-medium text-indigo-700">Add / Update</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700">Logout</button>
                        </form>
                    </div>
                </div>
            </header>

            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <div class="mb-8 rounded-2xl border border-emerald-200 bg-gradient-to-r from-emerald-50 to-indigo-50 px-5 py-4 text-sm font-medium text-emerald-800">
                    Annual 1604-CF alphalist summary
                </div>

                <div class="mb-8 grid gap-4 md:grid-cols-4">
                    @foreach ($years as $year)
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="text-lg font-semibold text-slate-900">{{ $year }}</div>
                            <div class="mt-2 text-sm text-slate-500">Annual summary</div>
                        </div>
                    @endforeach
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th class="px-4 py-3 font-medium">TIN</th>
                                <th class="px-4 py-3 font-medium">Employee</th>
                                <th class="px-4 py-3 font-medium">Tax Year</th>
                                <th class="px-4 py-3 font-medium">Gross</th>
                                <th class="px-4 py-3 font-medium">Tax Withheld</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($records as $record)
                                <tr class="border-t border-slate-200">
                                    <td class="px-4 py-3">{{ $record->employee?->tin }}</td>
                                    <td class="px-4 py-3">{{ $record->employee?->full_name }}</td>
                                    <td class="px-4 py-3">{{ $record->tax_year }}</td>
                                    <td class="px-4 py-3">₱{{ number_format($record->gross_compensation, 2) }}</td>
                                    <td class="px-4 py-3">₱{{ number_format($record->tax_withheld, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-slate-500">No annual records available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
