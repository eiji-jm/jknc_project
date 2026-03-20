<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesUploads;
use App\Models\BirTax;
use Illuminate\Http\Request;

class BirTaxController extends Controller
{
    use HandlesUploads;

    public function index()
    {
        $taxes = BirTax::latest()->get();

        return view('corporate.bir-tax.index', compact('taxes'));
    }

    public function create()
    {
        return view('corporate.common.form', [
            'title' => 'Add BIR & Tax',
            'action' => route('bir-tax.store'),
            'method' => 'POST',
            'cancelRoute' => route('bir-tax'),
            'fields' => $this->fields(),
            'item' => new BirTax(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['document_path'] = $this->handleUpload($request, 'document_path');

        BirTax::create($data);

        return redirect()->route('bir-tax')->with('success', 'BIR & Tax entry created.');
    }

    public function show(BirTax $birTax)
    {
        return view('corporate.bir-tax.preview', [
            'tax' => $birTax,
            'backRoute' => route('bir-tax'),
            'editRoute' => route('bir-tax.edit', $birTax),
            'deleteRoute' => route('bir-tax.destroy', $birTax),
        ]);
    }

    public function edit(BirTax $birTax)
    {
        return view('corporate.common.form', [
            'title' => 'Edit BIR & Tax',
            'action' => route('bir-tax.update', $birTax),
            'method' => 'PUT',
            'cancelRoute' => route('bir-tax'),
            'fields' => $this->fields(),
            'item' => $birTax,
        ]);
    }

    public function update(Request $request, BirTax $birTax)
    {
        $data = $this->validateData($request);
        $data['document_path'] = $this->handleUpload($request, 'document_path', $birTax->document_path);

        $birTax->update($data);

        return redirect()->route('bir-tax')->with('success', 'BIR & Tax entry updated.');
    }

    public function destroy(BirTax $birTax)
    {
        $birTax->delete();

        return redirect()->route('bir-tax')->with('success', 'BIR & Tax entry deleted.');
    }

    private function fields(): array
    {
        return [
            ['name' => 'tin', 'label' => 'TIN', 'type' => 'text'],
            ['name' => 'tax_payer', 'label' => 'Tax Payer', 'type' => 'text'],
            ['name' => 'registering_office', 'label' => 'Registering Office', 'type' => 'text'],
            ['name' => 'registered_address', 'label' => 'Registered Address', 'type' => 'text'],
            ['name' => 'tax_types', 'label' => 'Tax Types', 'type' => 'text'],
            ['name' => 'form_type', 'label' => 'Form Type', 'type' => 'text'],
            ['name' => 'filing_frequency', 'label' => 'Filing Frequency', 'type' => 'text'],
            ['name' => 'due_date', 'label' => 'Due Date', 'type' => 'date'],
            ['name' => 'uploaded_by', 'label' => 'Uploaded By', 'type' => 'text'],
            ['name' => 'date_uploaded', 'label' => 'Date Uploaded', 'type' => 'date'],
            ['name' => 'document_path', 'label' => 'Upload BIR & Tax Document (PDF)', 'type' => 'file'],
        ];
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'tin' => ['nullable', 'string', 'max:255'],
            'tax_payer' => ['nullable', 'string', 'max:255'],
            'registering_office' => ['nullable', 'string', 'max:255'],
            'registered_address' => ['nullable', 'string', 'max:255'],
            'tax_types' => ['nullable', 'string', 'max:255'],
            'form_type' => ['nullable', 'string', 'max:255'],
            'filing_frequency' => ['nullable', 'string', 'max:255'],
            'due_date' => ['nullable', 'date'],
            'uploaded_by' => ['nullable', 'string', 'max:255'],
            'date_uploaded' => ['nullable', 'date'],
            'document_path' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);
    }
}
