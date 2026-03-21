<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UploadedFileController extends Controller
{
    public function show(Request $request, string $path): BinaryFileResponse
    {
        abort_unless(Storage::disk('public')->exists($path), 404);

        $fullPath = Storage::disk('public')->path($path);
        $filename = basename($path);
        $mimeType = Storage::disk('public')->mimeType($path) ?: 'application/octet-stream';

        if ($request->boolean('download')) {
            return Storage::disk('public')->download($path, $filename, [
                'Content-Type' => $mimeType,
            ]);
        }

        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
}
