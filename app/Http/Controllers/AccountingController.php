<?php

namespace App\Http\Controllers;

use App\Models\Accounting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountingController extends Controller
{
    public function index(Request $request)
    {
        $statementType = $request->get('statement_type');

        $query = Accounting::query();

        if ($statementType && $statementType !== 'All Statement Types') {
            $query->where('statement_type', $statementType);
        }

        $data = $query->orderByDesc('date')
            ->orderByDesc('created_at')
            ->get();

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'statement_type' => 'required|in:PNL,Balance Sheet,Cash Flow,Income Statement,AFS',
            'client' => 'required|string',
            'tin' => 'nullable|string',
            'date' => 'required|date',
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $file = $request->file('document');
        $path = $file->store('accounting_documents', 'public');

        $entry = Accounting::create([
            'statement_type' => $request->statement_type,
            'client' => $request->client,
            'tin' => $request->tin,
            'date' => $request->date,
            'user' => Auth::check() ? Auth::user()->name : 'Unknown User',
            'status' => 'Open',
            'document_name' => $file->getClientOriginalName(),
            'document_path' => 'storage/' . $path,
        ]);

        return response()->json([
            'message' => 'Accounting entry saved successfully.',
            'data' => $entry
        ], 201);
    }
}