<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
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

    protected function appendUploadedFiles(
        Request $request,
        string $singleField,
        string $multiField,
        array $existingHistory = [],
        ?string $existingPath = null,
        string $directory = 'uploads'
    ): array {
        $history = collect($existingHistory)
            ->filter(fn ($entry) => is_array($entry) && !empty($entry['path']))
            ->values()
            ->all();

        if ($existingPath && !collect($history)->contains(fn ($entry) => ($entry['path'] ?? null) === $existingPath)) {
            $history[] = [
                'path' => $existingPath,
                'name' => basename($existingPath),
                'uploaded_at' => null,
            ];
        }

        $files = collect();

        if ($request->hasFile($singleField)) {
            $files->push($request->file($singleField));
        }

        if ($request->hasFile($multiField)) {
            $files = $files->merge($request->file($multiField));
        }

        $timestamp = Carbon::now('Asia/Manila')->toDateTimeString();

        $files
            ->filter(fn ($file) => $file instanceof UploadedFile)
            ->each(function (UploadedFile $file) use (&$history, $directory, $timestamp) {
                $path = $this->storeUploadedFile($file, $directory);

                $history[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'uploaded_at' => $timestamp,
                ];
            });

        $latestPath = collect($history)->last()['path'] ?? $existingPath;

        return [$latestPath, array_values($history)];
    }

    protected function sanitizedOriginalFilename(string $originalName): string
    {
        $filename = trim(str_replace(['\\', '/', ':', '*', '?', '"', '<', '>', '|'], '_', basename($originalName)));

        return $filename !== '' ? $filename : 'upload-' . now()->format('YmdHis');
    }
}
