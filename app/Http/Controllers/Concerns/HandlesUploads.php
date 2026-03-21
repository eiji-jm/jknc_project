<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        return $request->file($field)->store('uploads', 'public');
    }
}
