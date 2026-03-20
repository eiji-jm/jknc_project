<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::latest()->get();

        return view('corporate.contacts-list', compact('contacts'));
    }

    public function create()
    {
        return view('corporate.common.form', [
            'title' => 'Add Contact',
            'action' => route('contacts.store'),
            'method' => 'POST',
            'cancelRoute' => route('contacts'),
            'fields' => $this->fields(),
            'item' => new Contact(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        Contact::create($data);

        return redirect()->route('contacts')->with('success', 'Contact created.');
    }

    public function show(Contact $contact)
    {
        return view('corporate.common.show', [
            'title' => 'Contact Details',
            'item' => $contact,
            'fields' => $this->fields(),
            'backRoute' => route('contacts'),
            'editRoute' => route('contacts.edit', $contact),
        ]);
    }

    public function edit(Contact $contact)
    {
        return view('corporate.common.form', [
            'title' => 'Edit Contact',
            'action' => route('contacts.update', $contact),
            'method' => 'PUT',
            'cancelRoute' => route('contacts'),
            'fields' => $this->fields(),
            'item' => $contact,
        ]);
    }

    public function update(Request $request, Contact $contact)
    {
        $data = $this->validateData($request);
        $contact->update($data);

        return redirect()->route('contacts')->with('success', 'Contact updated.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()->route('contacts')->with('success', 'Contact deleted.');
    }

    private function fields(): array
    {
        return [
            ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true],
            ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
            ['name' => 'nationality', 'label' => 'Nationality', 'type' => 'text'],
            ['name' => 'address', 'label' => 'Address', 'type' => 'text'],
            ['name' => 'tax_id', 'label' => 'Tax ID', 'type' => 'text'],
        ];
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'nationality' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'tax_id' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
