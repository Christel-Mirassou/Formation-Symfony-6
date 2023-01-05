<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class KernelRequestSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Environment $twig,
        private bool $isMaintenance
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if ($this->isMaintenance) {
            $event->setResponse(new Response($this->twig->render('maintenance.html.twig')));
        }
        //Ici le simple fait de faire une setResponse directe avec dans la méthode suivante un priority à 9999
        //tout ça passe en priorité
        // $event->setResponse(new Response('Site en maintenance'));
    }

    // Méthode static qui retourne un tableau de tous les events que l'on écoute
    // et toutes les méthodes qui doivent être exécuter quand l'event est déclenché
    public static function getSubscribedEvents(): array
    {
        return [
            //le tableau correspond au nom de l'event et en face le nom de la méthode
            // KernelEvents::REQUEST => 'onKernelRequest',

            //le nom de la méthode peut être aussi un tableau avec le nom de la méthode est sa priorité
            KernelEvents::REQUEST => ['onKernelRequest', 9999],

            //le nom de la méthode peut être aussi un tableau de tableau avec plusieurs méthodes
            // qui peuvent être les même avant ou après un event ou tout autre logique
            // KernelEvents::REQUEST => [
            //         ['onKernelRequest', 10],
            //         ['onKernelRequest', -10]    
            //     ]    
        ];
    }
}
