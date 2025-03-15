<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioService
{
    protected $twilio;

    public function __construct()
    {
        $this->twilio = new Client(
            env('TWILIO_SID'),
            env('TWILIO_AUTH_TOKEN')
        );
    }

    // Send verification code
    public function sendVerificationCode($phoneNumber)
    {
        $verification = $this->twilio->verify->v2->services(env('TWILIO_VERIFY_SID'))
            ->verifications
            ->create("+2".$phoneNumber, "sms");

        return $verification->sid;
    }

    // Check verification code
    public function checkVerificationCode($phoneNumber, $code)
    {
        $verificationCheck = $this->twilio->verify->v2->services(env('TWILIO_VERIFY_SID'))
            ->verificationChecks
            ->create([
                'to' => "+2".$phoneNumber,
                'code' => $code
            ]);

        return $verificationCheck->status === 'approved';
    }
}
