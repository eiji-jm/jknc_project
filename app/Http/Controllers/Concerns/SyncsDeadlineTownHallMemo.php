<?php

namespace App\Http\Controllers\Concerns;

use App\Models\TownHallCommunication;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait SyncsDeadlineTownHallMemo
{
    protected function syncDeadlineTownHallMemo(
        Model $record,
        ?string $deadlineDate,
        string $moduleLabel,
        string $recordLabel,
        string $previewRouteName
    ): void {
        if (!$deadlineDate) {
            $this->deleteDeadlineTownHallMemo($record);

            return;
        }

        $deadline = Carbon::parse($deadlineDate)->startOfDay();
        $today = now()->startOfDay();
        $daysUntilDeadline = $today->diffInDays($deadline, false);

        $status = $daysUntilDeadline < 0 ? 'Overdue' : 'Open';
        $timingLabel = $daysUntilDeadline < 0 ? 'Overdue' : 'Incoming';
        $deadlineText = $deadline->format('F d, Y');
        $fromName = Auth::user()?->name ?: 'Corporate Compliance System';

        $message = collect([
            "This is an automated Town Hall memo for an {$moduleLabel} deadline.",
            "Record: {$recordLabel}",
            "Deadline: {$deadlineText}",
            $daysUntilDeadline < 0
                ? 'Status: The deadline has already passed and needs immediate attention.'
                : "Status: The deadline is approaching in {$daysUntilDeadline} day(s).",
            'Preview: ' . route($previewRouteName, $record),
        ])->implode("\n");

        $communication = TownHallCommunication::query()->firstOrNew([
            'source_type' => $record::class,
            'source_id' => $record->getKey(),
        ]);

        $communication->fill([
            'communication_date' => now()->toDateString(),
            'from_name' => $fromName,
            'department_stakeholder' => $moduleLabel,
            'to_for' => 'Town Hall',
            'status' => $status,
            'approval_status' => 'Approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'approval_notes' => 'System-generated deadline memo.',
            'subject' => "{$moduleLabel} Deadline {$timingLabel}: {$recordLabel}",
            'message' => $message,
            'additional' => 'Deadline tracking memo',
            'created_by' => Auth::id(),
            'deadline_date' => $deadline->toDateString(),
        ]);
        $communication->save();

        if (!$communication->ref_no) {
            $communication->ref_no = 'TH-' . str_pad((string) $communication->id, 5, '0', STR_PAD_LEFT);
            $communication->save();
        }
    }

    protected function deleteDeadlineTownHallMemo(Model $record): void
    {
        TownHallCommunication::query()
            ->where('source_type', $record::class)
            ->where('source_id', $record->getKey())
            ->delete();
    }
}
