<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CorporateStatusNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $employeeName;
    public string $moduleName;
    public string $corporationName;
    public string $companyRegNo;
    public string $decision;
    public ?string $reviewNote;

    public function __construct(
        string $employeeName,
        string $moduleName,
        string $corporationName,
        string $companyRegNo,
        string $decision,
        ?string $reviewNote = null
    ) {
        $this->employeeName = $employeeName;
        $this->moduleName = $moduleName;
        $this->corporationName = $corporationName;
        $this->companyRegNo = $companyRegNo;
        $this->decision = $decision;
        $this->reviewNote = $reviewNote;
    }

    public function build()
    {
        return $this->subject('Corporate Submission Update - ' . $this->decision)
            ->view('emails.corporate-status-notification');
    }
}