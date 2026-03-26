<?php

namespace App\Http\Controllers;

use App\Models\Legal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class LegalController extends Controller
{
    public function index(Request $request)
    {
        $query = Legal::query();

        if ($request->filled('type') && $request->type !== 'All Types') {
            $query->where('legal_type', $request->type);
        }

        if ($request->filled('status') && $request->status !== 'All') {
            if ($request->status === 'Completed') {
                $query->whereNotNull('document_path');
            }

            if ($request->status === 'Pending') {
                $query->whereNull('document_path');
            }
        }

        $data = $query
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'legal_type' => $item->legal_type,
                    'client' => $item->client,
                    'tin' => $item->tin,
                    'date' => $item->date?->format('Y-m-d'),
                    'document_type' => $item->document_type,
                    'document_name' => $item->document_name,
                    'document_path' => $item->document_path,
                    'user' => $item->user,
                    'status' => $item->status,
                    'document_url' => $item->document_path ? asset($item->document_path) : null,
                ];
            });

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'legal_type' => 'required|string|max:255',
            'client' => 'required|string|max:255',
            'tin' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'document_type' => 'nullable|string|max:255',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $relativePath = null;
        $documentName = null;

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $documentName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());

            $destinationPath = public_path('documents/legal');
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $documentName);
            $relativePath = 'documents/legal/' . $documentName;
        }

        $legal = Legal::create([
            'legal_type' => $validated['legal_type'],
            'client' => $validated['client'],
            'tin' => $validated['tin'] ?? null,
            'date' => $validated['date'] ?? now()->toDateString(),
            'document_type' => $validated['document_type'] ?? null,
            'document_name' => $documentName,
            'document_path' => $relativePath,
            'user' => Auth::check() ? Auth::user()->name : 'System',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Legal document saved successfully.',
            'data' => [
                'id' => $legal->id,
                'legal_type' => $legal->legal_type,
                'client' => $legal->client,
                'tin' => $legal->tin,
                'date' => $legal->date?->format('Y-m-d'),
                'document_type' => $legal->document_type,
                'document_name' => $legal->document_name,
                'document_path' => $legal->document_path,
                'user' => $legal->user,
                'status' => $legal->status,
                'document_url' => $legal->document_path ? asset($legal->document_path) : null,
            ]
        ], 201);
    }
}