<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(config('firebase.credentials'));
        $this->messaging = $factory->createMessaging();
    }

    public function sendPushNotification($token, $title, $body)
    {
        $message = CloudMessage::withTarget('token', $token)
            ->withNotification(Notification::create($title, $body));

        return $this->messaging->send($message);
    }
}
