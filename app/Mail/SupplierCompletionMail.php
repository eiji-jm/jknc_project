<?php

namespace App\Mail;

use App\Models\FinanceRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

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
        $mail = $this->subject('Complete Supplier Information - ' . ($this->record->record_title ?: 'Supplier Record'))
            ->view('emails.finance-supplier-completion');

        foreach ((array) ($this->record->attachments ?? []) as $attachment) {
            $path = (string) data_get($attachment, 'path', '');
            $storedPath = ltrim(str_replace('storage/', '', $path), '/\\');

            if ($storedPath && Storage::disk('public')->exists($storedPath)) {
                $options = [];
                if ($mime = data_get($attachment, 'mime')) {
                    $options['mime'] = $mime;
                }

                $mail->attachFromStorageDisk('public', $storedPath, data_get($attachment, 'name'), $options);
            }
        }

        return $mail;
    }
}
