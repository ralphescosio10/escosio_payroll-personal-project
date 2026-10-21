<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Compensation Record</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-800">
<div class="max-w-4xl mx-auto py-8 px-4">
    <div class="mb-4">
        @php
            $backRoute = match ($selectedTaxForm ?? $record->tax_form ?? session('selected_tax_form', 'MAP')) {
                'MAP' => route('forms.map'),
                'QAP' => route('forms.1604e'),
                'SAWT' => route('forms.sawt'),
                'Annual' => route('forms.annual'),
                default => route('forms.index'),
            };
        @endphp
        <a href="{{ $backRoute }}" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">← Back to {{ $selectedTaxFormLabel }}</a>
    </div>
    <h1 class="text-3xl font-bold mb-6">Edit Compensation Record</h1>
    <form method="POST" action="{{ route('compensation.update', $record) }}" class="space-y-4 bg-white rounded-2xl border shadow-sm p-6">
        @csrf
        @method('PUT')
        <input type="hidden" name="tax_form" value="{{ $selectedTaxForm ?? $record->tax_form ?? session('selected_tax_form', 'MAP') }}">

        <label class="block">
            <span class="text-sm font-medium">Employee</span>
            <select name="employee_id" class="mt-1 w-full rounded-lg border px-3 py-2" required>
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}" {{ $employee->id == $record->employee_id ? 'selected' : '' }}>
                        {{ $employee->full_name }} ({{ $employee->tin }})
                    </option>
                @endforeach
            </select>
        </label>

        <div class="grid md:grid-cols-2 gap-4">
            <label class="block">
                <span class="text-sm font-medium">Tax Year</span>
                <input name="tax_year" type="number" value="{{ old('tax_year', $record->tax_year ?? now()->year) }}" class="mt-1 w-full rounded-lg border px-3 py-2" required>
            </label>
            <label class="block">
                <span class="text-sm font-medium">Pay Period</span>
                <input name="pay_period" type="month" value="{{ old('pay_period', $record->pay_period) }}" class="mt-1 w-full rounded-lg border px-3 py-2" required>
            </label>
        </div>

        @php
            $currentTaxForm = $selectedTaxForm ?? $record->tax_form ?? session('selected_tax_form', 'MAP');
        @endphp

        @if ($currentTaxForm === 'SAWT')
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="grid md:grid-cols-2 gap-4">
                    <label class="block">
                        <span class="text-sm font-medium">Amended Return</span>
                        <select name="amended_return" class="mt-1 w-full rounded-lg border px-3 py-2">
                            <option value="N" {{ old('amended_return', $record->amended_return) === 'N' ? 'selected' : '' }}>No</option>
                            <option value="Y" {{ old('amended_return', $record->amended_return) === 'Y' ? 'selected' : '' }}>Yes</option>
                        </select>
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium">No of Sheets Attached</span>
                        <input name="no_of_sheets_attached" type="number" min="0" value="{{ old('no_of_sheets_attached', $record->no_of_sheets_attached ?? 0) }}" class="mt-1 w-full rounded-lg border px-3 py-2">
                    </label>
                </div>
                <div class="mt-4 grid md:grid-cols-2 gap-4">
                    <label class="block">
                        <span class="text-sm font-medium">Are there payees availing of tax relief under Special Law or International Tax Treaty?</span>
                        <select name="tax_relief_special_law" class="mt-1 w-full rounded-lg border px-3 py-2">
                            <option value="N" {{ old('tax_relief_special_law', $record->tax_relief_special_law) === 'N' ? 'selected' : '' }}>No</option>
                            <option value="Y" {{ old('tax_relief_special_law', $record->tax_relief_special_law) === 'Y' ? 'selected' : '' }}>Yes</option>
                        </select>
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium">For the quarter of</span>
                        <select name="quarter" class="mt-1 w-full rounded-lg border px-3 py-2">
                            @foreach (['1Q', '2Q', '3Q', '4Q'] as $quarter)
                                <option value="{{ $quarter }}" {{ old('quarter', $record->quarter ?? '1Q') === $quarter ? 'selected' : '' }}>{{ $quarter }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
            </div>
        @endif

        <div class="grid md:grid-cols-2 gap-4">
            <label class="block">
                <span class="text-sm font-medium">ATC Code</span>
                <select name="atc_code" class="mt-1 w-full rounded-lg border px-3 py-2" required>
                    @foreach (['A010','A020','A030','A040','A050','A060','A070','A080','A090','A100','A110','A120','A130'] as $code)
                        <option value="{{ $code }}" {{ old('atc_code', $record->atc_code) === $code ? 'selected' : '' }}>{{ $code }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block">
                <span class="text-sm font-medium">Tax Rate (%)</span>
                <input name="tax_rate" type="number" step="0.01" min="0" max="100" value="{{ old('tax_rate', $record->tax_rate ?? 0) }}" class="mt-1 w-full rounded-lg border px-3 py-2" required data-tax-calculation-input>
                <span class="mt-1 block text-xs text-amber-700">Withholding is an estimate based on this rate, not an official BIR tax calculation. Confirm the applicable rules before filing.</span>
            </label>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <label class="block">
                <span class="text-sm font-medium">Gross Compensation</span>
                <input name="gross_compensation" type="number" step="0.01" value="{{ old('gross_compensation', $record->gross_compensation) }}" class="mt-1 w-full rounded-lg border px-3 py-2" required data-tax-calculation-input>
            </label>
            <label class="block">
                <span class="text-sm font-medium">Estimated Tax Withheld</span>
                <input name="tax_withheld" type="number" step="0.01" value="{{ old('tax_withheld', $record->tax_withheld) }}" class="mt-1 w-full rounded-lg border bg-slate-50 px-3 py-2" required data-tax-withheld readonly aria-describedby="tax-estimate-note">
                <span id="tax-estimate-note" class="mt-1 block text-xs text-slate-500">Automatically calculated from taxable compensation and the entered rate.</span>
            </label>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <label class="block">
                <span class="text-sm font-medium">Non-taxable Compensation</span>
                <input name="non_taxable_compensation" type="number" step="0.01" value="{{ old('non_taxable_compensation', $record->non_taxable_compensation) }}" class="mt-1 w-full rounded-lg border px-3 py-2" required data-tax-calculation-input>
            </label>
            <label class="block">
                <span class="text-sm font-medium">Government Contributions</span>
                <input name="government_contributions" type="number" step="0.01" value="{{ old('government_contributions', $record->government_contributions) }}" class="mt-1 w-full rounded-lg border px-3 py-2" required data-tax-calculation-input>
            </label>
        </div>

        <label class="block">
            <span class="text-sm font-medium">Notes</span>
            <textarea name="notes" rows="3" class="mt-1 w-full rounded-lg border px-3 py-2">{{ old('notes', $record->notes) }}</textarea>
        </label>

        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-white font-medium">Save Changes</button>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const taxRateInput = document.querySelector('input[name="tax_rate"]');
        const grossInput = document.querySelector('input[name="gross_compensation"]');
        const nonTaxableInput = document.querySelector('input[name="non_taxable_compensation"]');
        const contributionsInput = document.querySelector('input[name="government_contributions"]');
        const taxWithheldInput = document.querySelector('input[name="tax_withheld"]');
        const calculationInputs = document.querySelectorAll('[data-tax-calculation-input]');

        function updateTaxWithheld() {
            const gross = Number(grossInput.value);
            const taxRate = Number(taxRateInput.value);

            if (grossInput.value === '' || taxRateInput.value === '') {
                taxWithheldInput.value = '';

                return;
            }

            const nonTaxable = Number(nonTaxableInput.value || 0);
            const contributions = Number(contributionsInput.value || 0);
            const taxableCompensation = Math.max(0, gross - nonTaxable - contributions);
            taxWithheldInput.value = ((taxableCompensation * taxRate) / 100).toFixed(2);
        }

        calculationInputs.forEach(function (input) {
            input.addEventListener('input', updateTaxWithheld);
            input.addEventListener('change', updateTaxWithheld);
        });

        updateTaxWithheld();
    });
</script>
</body>
</html>
