<?php

namespace App\Http\Controllers;

use App\Models\CompanyActivity;
use App\Support\CompanyHistoryLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CompanyActivityController extends Controller
{
    public function store(Request $request, int $company): JsonResponse
    {
        abort_unless(Schema::hasTable('company_activities'), 404);

        $validated = $this->validatePayload($request);
        $activity = CompanyActivity::query()->create([
            ...$validated,
            'company_id' => $company,
            'completed_at' => ($validated['status'] ?? '') === 'Completed' ? now() : null,
        ]);

        CompanyHistoryLogger::log($company, [
            'type' => 'profile',
            'title' => $validated['type'] . ' created',
            'description' => $validated['description'],
            'extra_label' => 'Status',
            'extra_value' => $validated['status'],
            'user_name' => $validated['assigned_user'],
            'user_initials' => $this->initials($validated['assigned_user']),
        ]);

        return response()->json($this->transform($activity), 201);
    }

    public function update(Request $request, int $company, CompanyActivity $activity): JsonResponse
    {
        abort_unless($activity->company_id === $company, 404);

        $validated = $this->validatePayload($request);
        $activity->update([
            ...$validated,
            'completed_at' => ($validated['status'] ?? '') === 'Completed' ? ($activity->completed_at ?? now()) : null,
        ]);

        CompanyHistoryLogger::log($company, [
            'type' => 'profile',
            'title' => $validated['type'] . ' updated',
            'description' => $validated['description'],
            'extra_label' => 'Status',
            'extra_value' => $validated['status'],
            'user_name' => $validated['assigned_user'],
            'user_initials' => $this->initials($validated['assigned_user']),
        ]);

        return response()->json($this->transform($activity->fresh()));
    }

    public function complete(int $company, CompanyActivity $activity): JsonResponse
    {
        abort_unless($activity->company_id === $company, 404);

        $activity->update([
            'status' => 'Completed',
            'completed_at' => now(),
        ]);

        CompanyHistoryLogger::log($company, [
            'type' => 'profile',
            'title' => $activity->type . ' completed',
            'description' => $activity->description,
            'extra_label' => 'Status',
            'extra_value' => 'Completed',
            'user_name' => $activity->assigned_user,
            'user_initials' => $this->initials($activity->assigned_user),
        ]);

        return response()->json($this->transform($activity->fresh()));
    }

    public function destroy(int $company, CompanyActivity $activity): JsonResponse
    {
        abort_unless($activity->company_id === $company, 404);

        CompanyHistoryLogger::log($company, [
            'type' => 'profile',
            'title' => $activity->type . ' deleted',
            'description' => $activity->description,
            'extra_label' => 'Status',
            'extra_value' => $activity->status,
            'user_name' => $activity->assigned_user,
            'user_initials' => $this->initials($activity->assigned_user),
        ]);

        $activity->delete();

        return response()->json(['ok' => true]);
    }

    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'type' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'assigned_user' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:255'],
            'due_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function transform(CompanyActivity $activity): array
    {
        return [
            'id' => $activity->id,
            'type' => $activity->type,
            'icon' => $this->iconForType($activity->type),
            'description' => $activity->description,
            'when' => optional($activity->due_at)->format('M d, Y h:i A') ?: '-',
            'owner' => $activity->assigned_user,
            'status' => $activity->status,
            'notes' => $activity->notes,
            'dueAt' => optional($activity->due_at)->format('Y-m-d\TH:i'),
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

    private function iconForType(string $type): string
    {
        return match (strtolower($type)) {
            'call' => 'fa-phone',
            'meeting' => 'fa-video',
            'email' => 'fa-envelope',
            default => 'fa-square-check',
        };
    }
}
