<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait HandlesUploads
{
    protected function handleUpload(Request $request, string $field, ?string $existingPath = null): ?string
    {
        if (!$request->hasFile($field)) {
            return $existingPath;
        }

        if ($existingPath) {
            Storage::delete($existingPath);
        }

        return $request->file($field)->store('uploads');
    }
}
