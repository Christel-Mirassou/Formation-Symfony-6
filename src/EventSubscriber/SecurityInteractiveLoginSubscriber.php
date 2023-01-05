<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class SecurityInteractiveLoginSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UserRepository $repository
    ){}
    
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();
        if(!$user instanceof User){
            return;
        }
        $user->setLastConnectedAt(new \DateTimeImmutable());
        $this->repository->save($user, true);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
        ];
    }
}
