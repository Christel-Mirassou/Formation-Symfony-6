<?php

namespace App\Notifier\Notification;

use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\SmsRecipientInterface;
use Symfony\Component\Notifier\Notification\SmsNotificationInterface;

class FirebaseNotification extends Notification implements SmsNotificationInterface
{
    public function asSmsMessage(SmsRecipientInterface $recipient, string $transport = null): ?SmsMessage
    {
        return new SmsMessage('0000','foo');
    }
}