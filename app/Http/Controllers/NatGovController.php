<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesUploads;
use App\Models\NatGov;
use Illuminate\Http\Request;

class NatGovController extends Controller
{
    use HandlesUploads;

    public function index()
    {
        $natgovs = NatGov::latest()->get();

        return view('corporate.natgov.index', [
            'natgovs' => $natgovs,
            'companyDefaults' => $this->companyDefaults(),
        ]);
    }

    public function create()
    {
        return view('corporate.common.form', [
            'title' => 'Add NatGov',
            'action' => route('natgov.store'),
            'method' => 'POST',
            'cancelRoute' => route('natgov'),
            'fields' => $this->fields(),
            'item' => new NatGov(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['document_path'] = $this->handleUpload($request, 'document_path');
        $data['approved_document_path'] = $this->handleUpload($request, 'approved_document_path');

        NatGov::create($data);

        return redirect()->route('natgov')->with('success', 'NatGov entry created.');
    }

    public function show(NatGov $natgov)
    {
        return view('corporate.natgov.preview', [
            'natgov' => $natgov,
            'backRoute' => route('natgov'),
            'editRoute' => route('natgov.edit', $natgov),
            'deleteRoute' => route('natgov.destroy', $natgov),
            'updateRoute' => route('natgov.update', $natgov),
        ]);
    }

    public function edit(NatGov $natgov)
    {
        return view('corporate.common.form', [
            'title' => 'Edit NatGov',
            'action' => route('natgov.update', $natgov),
            'method' => 'PUT',
            'cancelRoute' => route('natgov'),
            'fields' => $this->fields(),
            'item' => $natgov,
        ]);
    }

    public function update(Request $request, NatGov $natgov)
    {
        $data = $this->validateData($request);
        $data['document_path'] = $this->handleUpload($request, 'document_path', $natgov->document_path);
        $data['approved_document_path'] = $this->handleUpload($request, 'approved_document_path', $natgov->approved_document_path);

        $natgov->update($data);

        return redirect()->route('natgov')->with('success', 'NatGov entry updated.');
    }

    public function destroy(NatGov $natgov)
    {
        $natgov->delete();

        return redirect()->route('natgov')->with('success', 'NatGov entry deleted.');
    }

    private function fields(): array
    {
        return [
            ['name' => 'client', 'label' => 'Client', 'type' => 'text'],
            ['name' => 'tin', 'label' => 'TIN', 'type' => 'text'],
            ['name' => 'agency', 'label' => 'Government Body/Agency', 'type' => 'text'],
            ['name' => 'registration_status', 'label' => 'Registration Status', 'type' => 'text'],
            ['name' => 'registration_date', 'label' => 'Registration Date', 'type' => 'date'],
            ['name' => 'registration_no', 'label' => 'Registration No.', 'type' => 'text'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'text'],
            ['name' => 'uploaded_by', 'label' => 'Uploaded By', 'type' => 'text'],
            ['name' => 'date_uploaded', 'label' => 'Date Uploaded', 'type' => 'date'],
            ['name' => 'document_path', 'label' => 'Upload Draft NatGov Document (PDF)', 'type' => 'file'],
            ['name' => 'approved_document_path', 'label' => 'Upload Approved NatGov Document (PDF)', 'type' => 'file'],
        ];
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'client' => ['nullable', 'string', 'max:255'],
            'tin' => ['nullable', 'string', 'max:255'],
            'agency' => ['nullable', 'string', 'max:255'],
            'registration_status' => ['nullable', 'string', 'max:255'],
            'registration_date' => ['nullable', 'date'],
            'registration_no' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:255'],
            'uploaded_by' => ['nullable', 'string', 'max:255'],
            'date_uploaded' => ['nullable', 'date'],
            'document_path' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'approved_document_path' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);
    }

    private function companyDefaults(): array
    {
        return [
            'client' => 'JK&C Group of Companies',
            'tin' => '000-000-000-000',
        ];
    }
}
