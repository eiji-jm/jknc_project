<?php

namespace App\Http\Controllers;

use App\Models\TransmittalReceipt;

class TransmittalReceiptController extends Controller
{
    public function show($id)
    {
        $receipt = TransmittalReceipt::with('transmittal')->findOrFail($id);

        return view('transmittal.receipt', compact('receipt'));
    }
}