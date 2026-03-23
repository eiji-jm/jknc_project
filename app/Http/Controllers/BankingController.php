<?php

namespace App\Http\Controllers;

use App\Models\Banking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankingController extends Controller
{
    public function index()
    {
        $data = Banking::orderByDesc('date_uploaded')
            ->orderByDesc('created_at')
            ->get();

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'client' => 'required|string',
            'tin' => 'nullable|string',
            'bank' => 'required|string',
            'bank_doc' => 'required|string',
            'date_uploaded' => 'required|date',
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $file = $request->file('document');
        $path = $file->store('banking_documents', 'public');

        $entry = Banking::create([
            'date_uploaded' => $request->date_uploaded,
            'user' => Auth::user()->name ?? 'Unknown User',
            'client' => $request->client,
            'tin' => $request->tin,
            'bank' => $request->bank,
            'bank_doc' => $request->bank_doc,
            'status' => 'Open',
            'document_name' => $file->getClientOriginalName(),
            'document_path' => 'storage/' . $path,
        ]);

        return response()->json([
            'message' => 'Banking entry saved successfully.',
            'data' => $entry,
        ], 201);
    }
}