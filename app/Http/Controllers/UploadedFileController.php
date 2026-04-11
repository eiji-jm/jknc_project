<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UploadedFileController extends Controller
{
    public function show(Request $request, string $path): BinaryFileResponse
    {
        $resolvedPath = $this->resolvePath($path);

        abort_unless($resolvedPath && Storage::disk('public')->exists($resolvedPath), 404);

        $fullPath = Storage::disk('public')->path($resolvedPath);
        $filename = basename($resolvedPath);
        $mimeType = Storage::disk('public')->mimeType($resolvedPath) ?: 'application/octet-stream';

        if ($request->boolean('download')) {
            return Storage::disk('public')->download($resolvedPath, $filename, [
                'Content-Type' => $mimeType,
            ]);
        }

        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    private function resolvePath(string $path): ?string
    {
        $candidates = array_values(array_filter(array_unique([
            ltrim($path, '/'),
            preg_replace('#^/?storage/#', '', ltrim($path, '/')),
        ])));

        foreach ($candidates as $candidate) {
            if (Storage::disk('public')->exists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }
}
