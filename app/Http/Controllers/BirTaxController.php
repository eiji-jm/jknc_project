<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesUploads;
use App\Models\BirTax;
use App\Models\GisRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class BirTaxController extends Controller
{
    use HandlesUploads;

    public function index()
    {
        $taxes = BirTax::latest()->get();

        return view('corporate.bir-tax.index', [
            'taxes' => $taxes,
            'companyDefaults' => $this->companyDefaults(),
        ]);
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
        $data['approved_document_path'] = $this->handleUpload($request, 'approved_document_path');

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
            'updateRoute' => route('bir-tax.update', $birTax),
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
        $data['approved_document_path'] = $this->handleUpload($request, 'approved_document_path', $birTax->approved_document_path);

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
            ['name' => 'document_path', 'label' => 'Upload Draft BIR & Tax Document (PDF)', 'type' => 'file'],
            ['name' => 'approved_document_path', 'label' => 'Upload Approved BIR & Tax Document (PDF)', 'type' => 'file'],
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
            'approved_document_path' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);
    }

    private function companyDefaults(): array
    {
        if (Schema::hasTable('gis_records')) {
            $gis = GisRecord::where('approval_status', 'Approved')->latest()->first();

            if ($gis) {
                return [
                    'tax_payer' => $gis->corporation_name ?: 'JK&C Group of Companies',
                    'tin' => $gis->tin ?: '000-000-000-000',
                    'registered_address' => $gis->business_address ?: ($gis->principal_address ?: 'JK&C Corporate Office'),
                ];
            }
        }

        return [
            'tax_payer' => 'JK&C Group of Companies',
            'tin' => '000-000-000-000',
            'registered_address' => 'JK&C Corporate Office',
        ];
    }
}
