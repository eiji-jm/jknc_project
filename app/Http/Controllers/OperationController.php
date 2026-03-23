<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperationController extends Controller
{
    public function index()
    {
        $data = Operation::orderByDesc('date_uploaded')
            ->orderByDesc('created_at')
            ->get();

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'client' => 'required|string',
            'tin' => 'nullable|string',
            'operation_type' => 'required|string',
            'document_type' => 'required|string',
            'date_uploaded' => 'required|date',
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $file = $request->file('document');
        $path = $file->store('operation_documents', 'public');

        $entry = Operation::create([
            'date_uploaded' => $request->date_uploaded,
            'user' => Auth::user()->name ?? 'Unknown User',
            'client' => $request->client,
            'tin' => $request->tin,
            'operation_type' => $request->operation_type,
            'document_type' => $request->document_type,
            'status' => 'Open',
            'document_name' => $file->getClientOriginalName(),
            'document_path' => 'storage/' . $path,
        ]);

        return response()->json([
            'message' => 'Operation entry saved successfully.',
            'data' => $entry,
        ], 201);
    }
}