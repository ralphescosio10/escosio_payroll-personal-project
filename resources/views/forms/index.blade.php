<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Forms</title>
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
                <p class="mt-2 text-xl font-semibold text-white">{{ \App\TaxFormCatalog::label(session('selected_tax_form', 'SAWT')) }}</p>
            </div>
        </aside>

        <main class="lg:ml-72">
            <header class="border-b border-slate-200 bg-white/80 backdrop-blur-sm">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-5 sm:px-6 lg:px-8">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-indigo-600">Operations</p>
                        <h2 class="mt-1 text-2xl font-bold text-slate-900">Tax Forms</h2>
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
                <div class="mb-8 rounded-2xl border border-emerald-200 bg-gradient-to-r from-emerald-50 to-indigo-50 px-5 py-4 text-sm font-medium text-emerald-800">
                    BIR alphalist module selection
                </div>

                <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                    @php
                        $birModules = [
                            ['name' => '1604-CF', 'route' => 'forms.annual', 'details' => ['Employees', 'Minimum Wage Earners', 'Previous Employer', 'Final Withholding']],
                            ['name' => 'MAP', 'route' => 'forms.map', 'details' => ['Monthly Payees']],
                            ['name' => '1604-E', 'route' => 'forms.1604e', 'details' => ['Expanded Withholding', 'Exempt Payees']],
                            ['name' => 'SAWT', 'route' => 'forms.sawt', 'details' => ['Summary of Tax Withheld']],
                        ];
                    @endphp

                    @foreach ($birModules as $module)
                        <a href="{{ route($module['route']) }}" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-indigo-300 hover:shadow-md">
                            <div class="text-3xl font-bold text-slate-900">{{ $module['name'] }}</div>
                            <div class="mt-4 border-t border-slate-100 pt-3 text-xs text-slate-500">
                                @foreach ($module['details'] as $detail)
                                    <div>{{ $detail }}</div>
                                @endforeach
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </main>
    </div>
</body>
</html>
