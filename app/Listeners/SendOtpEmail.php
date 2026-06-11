<?php

namespace App\Listeners;

use App\Events\OtpRequested;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;

class SendOtpEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
      
    }

    /**
     * Handle the event.
     */
    public function handle(OtpRequested $event): void
    {
          Mail::to($event->email)
        ->send(new OtpMail($event->otp));
    }
}
