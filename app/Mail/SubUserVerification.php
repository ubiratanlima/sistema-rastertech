<?php

namespace App\Mail;

use App\Models\CustomerSubUser;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubUserVerification extends Mailable
{
    use Queueable, SerializesModels;

    public $subUser;

    public function __construct(CustomerSubUser $subUser)
    {
        $this->subUser = $subUser;
    }

    public function build()
    {
        return $this->subject('Ative seu Acesso - Rastertech')
                    ->markdown('emails.sub-user-verification');
    }
}
