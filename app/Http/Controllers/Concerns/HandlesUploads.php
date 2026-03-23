<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HandlesUploads
{
    protected function handleUpload(Request $request, string $field, ?string $existingPath = null): ?string
    {
        if ($request->boolean('remove_' . $field) && $existingPath && !$request->hasFile($field)) {
            Storage::disk('public')->delete($existingPath);

            return null;
        }

        if (!$request->hasFile($field)) {
            return $existingPath;
        }

        if ($existingPath) {
            Storage::disk('public')->delete($existingPath);
        }

        return $this->storeUploadedFile($request->file($field));
    }

    protected function storeUploadedFile($file, string $directory = 'uploads'): string
    {
        $folder = trim($directory, '/') . '/' . Str::uuid();

        return $file->storeAs($folder, $this->sanitizedOriginalFilename($file->getClientOriginalName()), 'public');
    }

    protected function sanitizedOriginalFilename(string $originalName): string
    {
        $filename = trim(str_replace(['\\', '/', ':', '*', '?', '"', '<', '>', '|'], '_', basename($originalName)));

        return $filename !== '' ? $filename : 'upload-' . now()->format('YmdHis');
    }
}
