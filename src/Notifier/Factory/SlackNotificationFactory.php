<?php

namespace App\Notifier\Factory;

use App\Notifier\Notification\SlackNotification;
use Symfony\Component\Notifier\Notification\Notification;

class SlackNotificationFactory implements NotificationFactoryInterface, IterableNotificationInterface
{
    public static function getDefaultIndexName(): string
    {
        return 'slack';
    }

    public function createNotification():Notification
    {
        $notification = new SlackNotification();
        //...
        return $notification;
    }
}