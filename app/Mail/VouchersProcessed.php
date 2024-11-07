<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VouchersProcessed extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $successfulVouchers;
    public $failedVouchers;

    public function __construct($user, $successfulVouchers, $failedVouchers)
    {
        $this->user = $user;
        $this->successfulVouchers = $successfulVouchers;
        $this->failedVouchers = $failedVouchers;
    }

    public function build()
    {
        return $this->subject('Resultados de la Carga de Comprobantes')
                    ->view('emails.vouchers_processed');
    }
}
