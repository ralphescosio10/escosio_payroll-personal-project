<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compensation Record Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-800">
<div class="max-w-4xl mx-auto py-8 px-4">
    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('compensation.index') }}" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">← Back to Records</a>
        <a href="{{ route('compensation.edit', $record) }}" class="rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-medium text-indigo-700">Edit</a>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-sm uppercase tracking-[0.2em] text-indigo-600 font-semibold">Compensation</p>
        <h1 class="mt-2 text-3xl font-bold">{{ $record->employee?->full_name }}</h1>
        <p class="mt-1 text-slate-600">TIN: {{ $record->employee?->tin }}</p>
    </div>

    <div class="mt-6 grid md:grid-cols-2 gap-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5"><p class="text-sm text-slate-500">Tax Form</p><p class="mt-2 text-xl font-semibold">{{ $record->tax_form }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5"><p class="text-sm text-slate-500">Pay Period</p><p class="mt-2 text-xl font-semibold">{{ $record->pay_period }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5"><p class="text-sm text-slate-500">Gross Compensation</p><p class="mt-2 text-xl font-semibold">₱{{ number_format($record->gross_compensation, 2) }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5"><p class="text-sm text-slate-500">Tax Withheld</p><p class="mt-2 text-xl font-semibold">₱{{ number_format($record->tax_withheld, 2) }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5"><p class="text-sm text-slate-500">Taxable Compensation</p><p class="mt-2 text-xl font-semibold">₱{{ number_format($record->taxable_compensation, 2) }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5"><p class="text-sm text-slate-500">ATC Code</p><p class="mt-2 text-xl font-semibold">{{ $record->atc_code }}</p></div>
    </div>

    @if ($record->tax_form === 'SAWT')
        <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="text-xl font-semibold mb-4">SAWT Details</h2>
            <div class="grid md:grid-cols-2 gap-4 text-sm">
                <div><span class="text-slate-500">Amended Return:</span> {{ $record->amended_return ?? 'N' }}</div>
                <div><span class="text-slate-500">No. of Sheets Attached:</span> {{ $record->no_of_sheets_attached ?? 0 }}</div>
                <div><span class="text-slate-500">Tax Relief / Special Law:</span> {{ $record->tax_relief_special_law ?? 'N' }}</div>
                <div><span class="text-slate-500">Quarter:</span> {{ $record->quarter ?? '1Q' }}</div>
            </div>
        </div>
    @endif
</div>
</body>
</html>
