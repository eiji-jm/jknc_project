<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

class BirTax extends Model
{
    protected $fillable = [
        'tin',
        'tax_payer',
        'registering_office',
        'registered_address',
        'tax_types',
        'form_type',
        'filing_frequency',
        'due_date',
        'uploaded_by',
        'date_uploaded',
        'document_path',
        'draft_documents',
        'approved_document_path',
        'approved_documents',
        'notes',
        'notes_visible_to',
    ];

    protected $casts = [
        'due_date' => 'date',
        'date_uploaded' => 'date',
        'draft_documents' => 'array',
        'approved_documents' => 'array',
    ];

    public function authorityNotes(): MorphMany
    {
        return $this->morphMany(AuthorityNote::class, 'noteable')->latest();
    }

    public function getDisplayStatusAttribute(): string
    {
        if (!$this->document_path && !$this->approved_document_path) {
            return 'Draft';
        }

        if ($this->approved_document_path) {
            return 'Approved';
        }

        if (!$this->due_date instanceof Carbon) {
            return 'Pending';
        }

        $today = now()->startOfDay();

        if ($this->due_date->isSameDay($today)) {
            return 'Due Today';
        }

        if ($this->due_date->lt($today)) {
            return 'Overdue';
        }

        return 'Pending';
    }
}
