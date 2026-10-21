<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Alphalist Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; margin: 24px; }
        h1 { font-size: 22px; margin-bottom: 12px; }
        .meta { font-size: 11px; color: #475569; margin-bottom: 18px; }
        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px; text-align: left; }
        th { background: #e2e8f0; }
        .totals { margin-top: 14px; font-size: 11px; }
    </style>
</head>
<body>
    <h1>Alphalist Report</h1>
    <div class="meta">Generated: {{ $generatedAt }}</div>

    <table>
        <thead>
            <tr>
                <th>TIN</th>
                <th>Employee</th>
                <th>Pay Period</th>
                <th>Tax Form</th>
                <th>ATC</th>
                <th>Gross</th>
                <th>Taxable</th>
                <th>Tax Withheld</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($records as $record)
                <tr>
                    <td>{{ $record->employee?->tin ?? '-' }}</td>
                    <td>{{ $record->employee?->full_name ?? '-' }}</td>
                    <td>{{ $record->pay_period }}</td>
                    <td>{{ $record->tax_form_label }}</td>
                    <td>{{ $record->atc_code }}</td>
                    <td>₱{{ number_format($record->gross_compensation, 2) }}</td>
                    <td>₱{{ number_format($record->taxable_compensation, 2) }}</td>
                    <td>₱{{ number_format($record->tax_withheld, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">No records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="totals">
        <strong>Total Gross:</strong> ₱{{ number_format($totals['gross'], 2) }}
        &nbsp;|&nbsp;
        <strong>Total Taxable:</strong> ₱{{ number_format($totals['taxable'], 2) }}
        &nbsp;|&nbsp;
        <strong>Total Tax Withheld:</strong> ₱{{ number_format($totals['withheld'], 2) }}
    </div>
</body>
</html>
