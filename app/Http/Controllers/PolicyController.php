<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PolicyController extends Controller
{
    public function index()
    {
        return view('policies.policies');
    }

    public function store(Request $request)
    {
        // Your store logic
        return back()->with('success', 'Policy saved successfully!');
    }

    public function previewPdf(Request $request)
    {
        $data = $request->only([
            'policy',
            'version',
            'effectivity_date',
            'prepared_by',
            'reviewed_by',
            'approved_by',
            'classification',
            'description'
        ]);

        $pdf = Pdf::loadView('policies.pdf_preview', compact('data'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true, // Enables support for Quill's nested tags
                'isRemoteEnabled' => true,      // Allows for external images/css
                'defaultFont' => 'sans-serif',
                'debugLayout' => false,
            ]);

        return $pdf->stream('policy_live_preview.pdf');
    }
}
