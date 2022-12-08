<?php

namespace App\Notifier;

use App\Notifier\Factory\ChaineNotificationFactory;
use Symfony\Component\Notifier\NotifierInterface;

class MovieNotifier
{
    public function __construct(
        private NotifierInterface $notifier,
        private ChaineNotificationFactory $factory
    ){}

    public function sendNotification()
    {
        $user = new class {
            public function getPreferredChannel()
            {
                return 'discord';
            }
        };
        $notification = $this->factory->createNotification($user->getPreferredChannel());
        
        $this->notifier->send($notification);
    }
}