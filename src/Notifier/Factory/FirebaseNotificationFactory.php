<?php

namespace App\Notifier\Factory;

use App\Notifier\Notification\DiscordNotification;
use Symfony\Component\Notifier\Notification\Notification;

class DiscordNotificationFactory implements NotificationFactoryInterface, IterableNotificationInterface
{
    public static function getDefaultIndexName(): string
    {
        return 'firebase';
    }

    public function createNotification():Notification
    {
        $notification = new DiscordNotification();
        //...
        return $notification;
    }
}