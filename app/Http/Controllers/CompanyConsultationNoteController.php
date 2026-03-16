<?php

namespace App\Http\Controllers;

use App\Models\CompanyConsultationNote;
use App\Support\CompanyHistoryLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CompanyConsultationNoteController extends Controller
{
    public function store(Request $request, int $company): JsonResponse
    {
        abort_unless(Schema::hasTable('company_consultation_notes'), 404);

        $validated = $this->validatePayload($request);
        $note = CompanyConsultationNote::query()->create([
            ...$validated,
            'company_id' => $company,
        ]);

        CompanyHistoryLogger::log($company, [
            'type' => 'notes',
            'title' => 'Note added to company',
            'description' => $validated['summary'] ?: 'Consultation note created',
            'extra_label' => 'Note',
            'extra_value' => $validated['title'],
            'user_name' => $validated['author'] ?: 'System',
            'user_initials' => $this->initials($validated['author'] ?: 'System'),
        ]);

        return response()->json($this->transform($note), 201);
    }

    public function update(Request $request, int $company, CompanyConsultationNote $note): JsonResponse
    {
        abort_unless($note->company_id === $company, 404);

        $validated = $this->validatePayload($request);
        $note->update($validated);

        CompanyHistoryLogger::log($company, [
            'type' => 'notes',
            'title' => 'Consultation note updated',
            'description' => $validated['summary'] ?: 'Consultation note updated',
            'extra_label' => 'Note',
            'extra_value' => $validated['title'],
            'user_name' => $validated['author'] ?: 'System',
            'user_initials' => $this->initials($validated['author'] ?: 'System'),
        ]);

        return response()->json($this->transform($note->fresh()));
    }

    public function destroy(int $company, CompanyConsultationNote $note): JsonResponse
    {
        abort_unless($note->company_id === $company, 404);

        CompanyHistoryLogger::log($company, [
            'type' => 'notes',
            'title' => 'Consultation note deleted',
            'description' => 'Consultation note removed from company profile',
            'extra_label' => 'Note',
            'extra_value' => $note->title,
            'user_name' => $note->author ?: 'System',
            'user_initials' => $this->initials($note->author ?: 'System'),
        ]);

        $note->delete();

        return response()->json(['ok' => true]);
    }

    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'consultation_date' => ['required', 'date'],
            'author' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'details' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:255'],
            'linked_deal' => ['nullable', 'string', 'max:255'],
            'linked_activity' => ['nullable', 'string', 'max:255'],
            'attachments' => ['nullable', 'array'],
        ]);
    }

    private function transform(CompanyConsultationNote $note): array
    {
        return [
            'id' => $note->id,
            'title' => $note->title,
            'consultationDate' => optional($note->consultation_date)->format('Y-m-d'),
            'author' => $note->author,
            'summary' => $note->summary,
            'details' => $note->details,
            'category' => $note->category,
            'linkedDeal' => $note->linked_deal,
            'linkedActivity' => $note->linked_activity,
            'attachments' => $note->attachments ?? [],
            'createdAt' => optional($note->created_at)->toIso8601String(),
            'updatedAt' => optional($note->updated_at)->toIso8601String(),
        ];
    }

    private function initials(string $name): string
    {
        return collect(explode(' ', trim($name)))
            ->filter()
            ->take(2)
            ->map(fn (string $part) => strtoupper(substr($part, 0, 1)))
            ->implode('') ?: 'NA';
    }
}
