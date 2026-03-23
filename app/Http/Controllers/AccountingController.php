<?php

namespace App\Http\Controllers;

use App\Models\Accounting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountingController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'PNL');

        $query = Accounting::query();

        if ($filter !== 'All Documents') {
            $query->where('type', $filter);
        }

        $data = $query->orderByDesc('date')
            ->orderByDesc('created_at')
            ->get();

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'client' => 'required|string',
            'tin' => 'nullable|string',
            'date' => 'required|date',
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $file = $request->file('document');
        $path = $file->store('accounting_documents', 'public');

        $entry = Accounting::create([
            'type' => $request->type,
            'client' => $request->client,
            'tin' => $request->tin,
            'date' => $request->date,
            'user' => Auth::user()->name ?? 'Unknown User',
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