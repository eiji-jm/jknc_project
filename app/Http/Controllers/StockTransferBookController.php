<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StockTransferBookIndex;

class StockTransferBookController extends Controller
{
    public function index()
    {
        $indexes = StockTransferBookIndex::latest()->get();

        return view('corporate.stock-transfer-book.index', compact('indexes'));
    }

    public function storeIndex(Request $request)
    {
        $request->validate([
            'family_name'     => 'required|string|max:255',
            'first_name'      => 'nullable|string|max:255',
            'middle_name'     => 'nullable|string|max:255',
            'nationality'     => 'nullable|string|max:255',
            'current_address' => 'nullable|string|max:1000',
            'tin'             => 'nullable|string|max:255',
        ]);

        StockTransferBookIndex::create([
            'family_name'     => $request->family_name,
            'first_name'      => $request->first_name,
            'middle_name'     => $request->middle_name,
            'nationality'     => $request->nationality,
            'current_address' => $request->current_address,
            'tin'             => $request->tin,
            'created_by'      => Auth::id(),
        ]);

        return redirect()->route('stock-transfer-book.index')
            ->with('success', 'Index entry saved successfully.');
    }

    public function lookupIndex(Request $request)
    {
        $query = trim((string) $request->get('q', ''));

        if ($query === '') {
            return response()->json([]);
        }

        $matches = StockTransferBookIndex::query()
            ->where('family_name', 'like', "%{$query}%")
            ->orWhere('first_name', 'like', "%{$query}%")
            ->orWhere('middle_name', 'like', "%{$query}%")
            ->limit(8)
            ->get([
                'id',
                'family_name',
                'first_name',
                'middle_name',
                'nationality',
                'current_address',
                'tin',
            ]);

        return response()->json($matches);
    }

    public function journal()
    {
        $journals = collect();
        return view('corporate.stock-transfer-book.journal', compact('journals'));
    }

    public function ledger()
    {
        $ledgers = collect();
        return view('corporate.stock-transfer-book.ledger', compact('ledgers'));
    }

    public function installment()
    {
        $installments = collect();
        return view('corporate.stock-transfer-book.installment', compact('installments'));
    }

    public function certificates()
    {
        $certificates = collect();
        return view('corporate.stock-transfer-book.certificates', compact('certificates'));
    }
}