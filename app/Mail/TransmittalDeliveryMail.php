<?php

namespace App\Mail;

use App\Models\Transmittal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransmittalDeliveryMail extends Mailable
{
    use Queueable, SerializesModels;

    public Transmittal $transmittal;

    public function __construct(Transmittal $transmittal)
    {
        $this->transmittal = $transmittal->loadMissing(['items', 'receipt']);
    }

    public function build()
    {
        $mail = $this->subject('Transmittal ' . ($this->transmittal->transmittal_no ?? ''))
            ->view('emails.transmittal-delivery')
            ->with([
                'transmittal' => $this->transmittal,
            ]);

        $transmittalPdf = Pdf::loadView('transmittal.preview-pdf', [
            'transmittal' => $this->transmittal,
        ])->setPaper('a4', 'portrait')->output();

        $mail->attachData(
            $transmittalPdf,
            ($this->transmittal->transmittal_no ?? 'transmittal') . '.pdf',
            ['mime' => 'application/pdf']
        );

        if ($this->transmittal->receipt) {
            $receiptPdf = Pdf::loadView('transmittal.receipt-pdf', [
                'transmittal' => $this->transmittal,
            ])->setPaper([0, 0, 300, 200])->output();

            $mail->attachData(
                $receiptPdf,
                ($this->transmittal->receipt->receipt_no ?? 'receipt') . '.pdf',
                ['mime' => 'application/pdf']
            );
        }

        foreach ($this->transmittal->items as $item) {
            if (!empty($item->attachment_path)) {
                $fullPath = storage_path('app/public/' . $item->attachment_path);

                if (file_exists($fullPath)) {
                    $mail->attach($fullPath, [
                        'as' => basename($item->attachment_path),
                    ]);
                }
            }
        }

        return $mail;
    }
}