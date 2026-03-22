<?php

namespace App\Http\Controllers;

use App\Models\SpecimenSignature;
use Barryvdh\DomPDF\Facade\Pdf;

class SpecimenSignatureController extends Controller
{
    public function download($id)
    {
        $data = SpecimenSignature::query()->find($id)
            ?? SpecimenSignature::query()->where('contact_id', $id)->firstOrFail();

        $pdf = Pdf::loadView('contacts.specimen-signature-print', compact('data'))
            ->setPaper('A4', 'portrait');

        return $pdf->download('specimen-signature.pdf');
    }
}
