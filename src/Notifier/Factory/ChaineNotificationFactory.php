<?php

namespace App\Notifier\Factory;

use Symfony\Component\Notifier\Notification\Notification;

class ChaineNotificationFactory implements NotificationFactoryInterface
{
    public function __construct(
        private iterable $factories
    ){
        $this->factories = $factories instanceof \Traversable ? iterator_to_array($factories) : $factories;
    }
    
    public function createNotification(string $channel = ''):Notification
    {
        return $this->factories[$channel]->createNotification();
    }
}