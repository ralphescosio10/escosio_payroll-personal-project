<?php

namespace App\Http\Controllers;

use App\Models\CompensationRecord;
use App\TaxFormCatalog;
use Illuminate\Http\Request;

class TaxSelectionController extends Controller
{
    public function choose()
    {
        $forms = array_keys(TaxFormCatalog::labels());
        $totalsByForm = [];

        foreach ($forms as $form) {
            $records = CompensationRecord::query()->where('tax_form', $form);
            $totalsByForm[$form] = [
                'count' => $records->count(),
                'gross' => (float) $records->sum('gross_compensation'),
                'taxable' => (float) $records->sum('taxable_compensation'),
                'withheld' => (float) $records->sum('tax_withheld'),
            ];
        }

        return view('forms.choose', [
            'totalsByForm' => $totalsByForm,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tax_form' => ['required', 'in:MAP,QAP,SAWT,Annual'],
        ]);

        $request->session()->put('selected_tax_form', $validated['tax_form']);

        return redirect()->route('dashboard')->with('success', 'Tax form selected: '.TaxFormCatalog::label($validated['tax_form']));
    }

    public function selectedForm()
    {
        return session('selected_tax_form', 'MAP');
    }
}
