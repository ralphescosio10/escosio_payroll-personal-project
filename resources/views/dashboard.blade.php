<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alphalist Data Entry</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-800">
    <div class="max-w-7xl mx-auto py-8 px-4">
        <header class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-600">Payroll</p>
                    <h1 class="text-3xl font-bold">Alphalist Data Entry</h1>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('reports.index') }}" class="rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-medium text-indigo-700">Reports</a>
                    <a href="{{ route('compensation.export') }}" class="rounded-lg border border-sky-200 bg-sky-50 px-4 py-2 text-sm font-medium text-sky-700">Export Comp. CSV</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium">Logout</button>
                    </form>
                </div>
            </div>
        </header>

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <section class="grid gap-4 md:grid-cols-3 mb-8">
            <div class="rounded-2xl bg-white p-5 shadow-sm border border-slate-200">
                <p class="text-sm text-slate-500">Employees</p>
                <p class="mt-2 text-3xl font-bold">{{ $totals['employees'] }}</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm border border-slate-200">
                <p class="text-sm text-slate-500">Gross Compensation</p>
                <p class="mt-2 text-3xl font-bold">₱{{ number_format($totals['gross_compensation'], 2) }}</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm border border-slate-200">
                <p class="text-sm text-slate-500">Tax Withheld</p>
                <p class="mt-2 text-3xl font-bold">₱{{ number_format($totals['tax_withheld'], 2) }}</p>
            </div>
        </section>

        <div class="grid lg:grid-cols-2 gap-8 mb-8">
            <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-200">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold">Add Employee</h2>
                    <a href="{{ route('employees.export') }}" class="text-sm text-indigo-600">Download CSV</a>
                </div>
                <form method="POST" action="{{ route('employees.import') }}" enctype="multipart/form-data" class="mb-5 space-y-3">
                    @csrf
                    <label class="block">
                        <span class="text-sm font-medium">Import Employee CSV</span>
                        <input type="file" name="csv_file" class="mt-1 block w-full text-sm" accept=".csv,.txt">
                    </label>
                    <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-white text-sm">Import CSV</button>
                </form>
                <form method="POST" action="{{ route('employees.store') }}" class="space-y-4">
                    @csrf
                    <div class="grid md:grid-cols-2 gap-4">
                        <label class="block">
                            <span class="text-sm font-medium">TIN</span>
                            <input name="tin" value="{{ old('tin') }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-indigo-500 outline-none" placeholder="123-456-789-000">
                            @error('tin') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </label>
                        <label class="block">
                            <span class="text-sm font-medium">Employment Status</span>
                            <select name="employment_status" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-indigo-500 outline-none" required>
                                <option value="" disabled {{ old('employment_status') ? '' : 'selected' }}>Select status</option>
                                <option value="Regular" {{ old('employment_status') === 'Regular' ? 'selected' : '' }}>Regular</option>
                                <option value="Probationary" {{ old('employment_status') === 'Probationary' ? 'selected' : '' }}>Probationary</option>
                                <option value="Contractual" {{ old('employment_status') === 'Contractual' ? 'selected' : '' }}>Contractual</option>
                                <option value="Part-time" {{ old('employment_status') === 'Part-time' ? 'selected' : '' }}>Part-time</option>
                                <option value="Casual" {{ old('employment_status') === 'Casual' ? 'selected' : '' }}>Casual</option>
                            </select>
                            @error('employment_status') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </label>
                    </div>
                    <div class="grid md:grid-cols-3 gap-4">
                        <label class="block">
                            <span class="text-sm font-medium">First Name</span>
                            <input name="first_name" value="{{ old('first_name') }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-indigo-500 outline-none">
                            @error('first_name') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </label>
                        <label class="block">
                            <span class="text-sm font-medium">Middle Name</span>
                            <input name="middle_name" value="{{ old('middle_name') }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-indigo-500 outline-none">
                        </label>
                        <label class="block">
                            <span class="text-sm font-medium">Last Name</span>
                            <input name="last_name" value="{{ old('last_name') }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-indigo-500 outline-none">
                            @error('last_name') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </label>
                    </div>
                    <div class="grid md:grid-cols-2 gap-4">
                        <label class="block">
                            <span class="text-sm font-medium">Monthly Salary</span>
                            <input name="monthly_salary" value="{{ old('monthly_salary') }}" type="number" step="0.01" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-indigo-500 outline-none">
                            @error('monthly_salary') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </label>
                        <label class="block">
                            <span class="text-sm font-medium">RDO</span>
                            <input name="rdo" value="{{ old('rdo') }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-indigo-500 outline-none">
                        </label>
                    </div>
                    <label class="block">
                        <span class="text-sm font-medium">Address</span>
                        <textarea name="address" rows="3" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-indigo-500 outline-none">{{ old('address') }}</textarea>
                    </label>
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-white font-medium hover:bg-indigo-700">Save Employee</button>
                </form>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-200">
                <h2 class="text-xl font-semibold mb-4">Add Compensation Record</h2>
                <form method="POST" action="{{ route('compensation-records.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="tax_form" value="{{ session('selected_tax_form', 'MAP') }}">
                    <label class="block">
                        <span class="text-sm font-medium">Employee TIN</span>
                        <input name="employee_tin" value="{{ old('employee_tin') }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-indigo-500 outline-none" placeholder="123-456-789-000">
                        @error('employee_tin') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium">Pay Period</span>
                        <input name="pay_period" value="{{ old('pay_period') }}" type="month" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-indigo-500 outline-none">
                        @error('pay_period') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </label>
                    <div class="grid md:grid-cols-2 gap-4">
                        <label class="block">
                            <span class="text-sm font-medium">Gross Compensation</span>
                            <input name="gross_compensation" value="{{ old('gross_compensation') }}" type="number" min="0" step="0.01" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-indigo-500 outline-none" data-tax-calculation-input>
                            @error('gross_compensation') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </label>
                        <label class="block">
                            <span class="text-sm font-medium">Tax Rate (%)</span>
                            <input name="tax_rate" value="{{ old('tax_rate', 0) }}" type="number" min="0" max="100" step="0.01" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-indigo-500 outline-none" required data-tax-calculation-input>
                            @error('tax_rate') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </label>
                    </div>
                    <label class="block">
                        <span class="text-sm font-medium">Estimated Tax Withheld</span>
                        <input name="tax_withheld" value="{{ old('tax_withheld') }}" type="number" step="0.01" class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2" data-tax-withheld readonly>
                        <span class="mt-1 block text-xs text-amber-700">This is an estimate based on the entered rate, not an official BIR calculation.</span>
                    </label>
                    <div class="grid md:grid-cols-2 gap-4">
                        <label class="block">
                            <span class="text-sm font-medium">Non-taxable Compensation</span>
                            <input name="non_taxable_compensation" value="{{ old('non_taxable_compensation', 0) }}" type="number" min="0" step="0.01" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-indigo-500 outline-none" data-tax-calculation-input>
                            @error('non_taxable_compensation') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </label>
                        <label class="block">
                            <span class="text-sm font-medium">Government Contributions</span>
                            <input name="government_contributions" value="{{ old('government_contributions', 0) }}" type="number" min="0" step="0.01" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-indigo-500 outline-none" data-tax-calculation-input>
                            @error('government_contributions') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </label>
                    </div>
                    <label class="block">
                        <span class="text-sm font-medium">Notes</span>
                        <textarea name="notes" rows="3" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-indigo-500 outline-none">{{ old('notes') }}</textarea>
                    </label>
                    <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-white font-medium hover:bg-emerald-700">Save Record</button>
                </form>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.querySelector('form[action="{{ route('compensation-records.store') }}"]');
                const taxRateInput = form.querySelector('input[name="tax_rate"]');
                const grossInput = form.querySelector('input[name="gross_compensation"]');
                const nonTaxableInput = form.querySelector('input[name="non_taxable_compensation"]');
                const contributionsInput = form.querySelector('input[name="government_contributions"]');
                const taxWithheldInput = form.querySelector('input[name="tax_withheld"]');

                function updateTaxWithheld() {
                    if (grossInput.value === '' || taxRateInput.value === '') {
                        taxWithheldInput.value = '';

                        return;
                    }

                    const taxable = Math.max(
                        0,
                        Number(grossInput.value)
                            - Number(nonTaxableInput.value || 0)
                            - Number(contributionsInput.value || 0)
                    );
                    taxWithheldInput.value = ((taxable * Number(taxRateInput.value)) / 100).toFixed(2);
                }

                form.querySelectorAll('[data-tax-calculation-input]').forEach(function (input) {
                    input.addEventListener('input', updateTaxWithheld);
                    input.addEventListener('change', updateTaxWithheld);
                });

                updateTaxWithheld();
            });
        </script>

        <section class="rounded-2xl bg-white p-6 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold">Employee Masterlist</h2>
                <a href="{{ route('employees.export') }}" class="text-sm text-indigo-600">Export CSV</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3">TIN</th>
                            <th class="px-4 py-3">Employee</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Monthly Salary</th>
                            <th class="px-4 py-3">RDO</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employees as $employee)
                            <tr class="border-t border-slate-200">
                                <td class="px-4 py-3 font-medium">{{ $employee->tin }}</td>
                                <td class="px-4 py-3">{{ $employee->full_name }}</td>
                                <td class="px-4 py-3">{{ $employee->employment_status }}</td>
                                <td class="px-4 py-3">₱{{ number_format($employee->monthly_salary, 2) }}</td>
                                <td class="px-4 py-3">{{ $employee->rdo ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-500">No employee records yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="mt-8 rounded-2xl bg-white p-6 shadow-sm border border-slate-200">
            <h2 class="text-xl font-semibold mb-4">Recent Compensation Entries</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3">Employee</th>
                            <th class="px-4 py-3">Pay Period</th>
                            <th class="px-4 py-3">Tax Form</th>
                            <th class="px-4 py-3">Gross</th>
                            <th class="px-4 py-3">Taxable</th>
                            <th class="px-4 py-3">Tax Withheld</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($compensationRecords as $record)
                            <tr class="border-t border-slate-200">
                                <td class="px-4 py-3">{{ $record->employee->full_name }}</td>
                                <td class="px-4 py-3">{{ $record->pay_period }}</td>
                                <td class="px-4 py-3">{{ $record->tax_form_label }}</td>
                                <td class="px-4 py-3">₱{{ number_format($record->gross_compensation, 2) }}</td>
                                <td class="px-4 py-3">₱{{ number_format($record->taxable_compensation, 2) }}</td>
                                <td class="px-4 py-3">₱{{ number_format($record->tax_withheld, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-500">No compensation records yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</body>
</html>
