<?php

namespace App\Notifier\Factory;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'app.notification')]
// Cet attribut dit que toutes les classes qui implémentent cette interface seront automatiquement taggées avec le tag app.notification
interface IterableNotificationInterface
{
    public static function getDefaultIndexName(): string;
}