<?php

namespace App\Mail;

use App\Models\FinanceRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupplierCompletionMail extends Mailable
{
    use Queueable, SerializesModels;

    public FinanceRecord $record;
    public string $completionUrl;

    public function __construct(FinanceRecord $record, string $completionUrl)
    {
        $this->record = $record;
        $this->completionUrl = $completionUrl;
    }

    public function build()
    {
        return $this->subject('Complete Supplier Information - ' . ($this->record->record_title ?: 'Supplier Record'))
            ->view('emails.finance-supplier-completion');
    }
}
