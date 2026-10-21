<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Module Selection</title>
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
                    <p class="truncate text-xs text-slate-400">Payroll</p>
                </div>
            </div>

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
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Current module</p>
                <p class="mt-2 text-xl font-semibold text-white">Payroll</p>
            </div>
        </aside>

        <main class="lg:ml-72">
            <header class="border-b border-slate-200 bg-white/80 backdrop-blur-sm">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-5 sm:px-6 lg:px-8">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-indigo-600">Operations</p>
                        <h2 class="mt-1 text-2xl font-bold text-slate-900">System Modules</h2>
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
                    <div class="mb-8 rounded-2xl border border-emerald-200 bg-gradient-to-r from-emerald-50 to-indigo-50 px-5 py-4 text-sm font-medium text-emerald-800">
                        Choose a payroll or BIR Alphalist module to continue.
                    </div>

                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <a href="{{ route('employees.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-indigo-300 hover:shadow-md">
                            <p class="text-xs uppercase tracking-[0.2em] text-indigo-600 font-semibold">Module</p>
                            <h3 class="mt-3 text-lg font-bold text-slate-900">Employee Masterlist</h3>
                            <ul class="mt-3 space-y-1 text-sm text-slate-600">
                                <li>Employee Information</li>
                                <li>TIN</li>
                                <li>Employment Status</li>
                                <li>Tax Year</li>
                                <li>Compensation Information</li>
                            </ul>
                        </a>

                        <a href="{{ route('compensation.index') }}?tax_form=" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-indigo-300 hover:shadow-md">
                            <p class="text-xs uppercase tracking-[0.2em] text-indigo-600 font-semibold">Module</p>
                            <h3 class="mt-3 text-lg font-bold text-slate-900">Compensation</h3>
                            <ul class="mt-3 space-y-1 text-sm text-slate-600">
                                <li>Monthly Compensation</li>
                                <li>Tax Withheld</li>
                                <li>Government Contributions</li>
                            </ul>
                        </a>

                        <a href="{{ route('forms.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-indigo-300 hover:shadow-md">
                            <p class="text-xs uppercase tracking-[0.2em] text-indigo-600 font-semibold">Module</p>
                            <h3 class="mt-3 text-lg font-bold text-slate-900">BIR Alphalist</h3>
                            <ul class="mt-3 space-y-2 text-sm text-slate-600">
                                <li>1604-CF</li>
                                <li>MAP</li>
                                <li>1604-E</li>
                                <li>SAWT</li>
                            </ul>
                        </a>

                        <a href="{{ route('reports.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-indigo-300 hover:shadow-md">
                            <p class="text-xs uppercase tracking-[0.2em] text-indigo-600 font-semibold">Module</p>
                            <h3 class="mt-3 text-lg font-bold text-slate-900">Reports</h3>
                            <ul class="mt-3 space-y-1 text-sm text-slate-600">
                                <li>BIR Alphalist</li>
                                <li>Employee Compensation</li>
                                <li>Tax Withheld</li>
                            </ul>
                        </a>

                    </div>
            </div>
        </main>
    </div>
</body>
</html>
