<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\GeneratesCorporateDocumentNumbers;

class CorporateDocumentDefaultsController extends Controller
{
    use GeneratesCorporateDocumentNumbers;

    public function __invoke()
    {
        return response()->json([
            'today' => now()->toDateString(),
            'current_user' => auth()->user()?->name ?? '',
            'notice_number' => $this->nextNoticeNumber(),
            'minutes_ref' => $this->nextMinutesRef(),
            'resolution_no' => $this->nextResolutionNumber(),
            'certificate_no' => $this->nextSecretaryCertificateNumber(),
        ]);
    }
}
