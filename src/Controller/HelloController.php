<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class HelloController extends AbstractController
{
    #[Route('/hello/{name<[a-zA-Z- ]+>?World}', name: 'app_hello')]   //ici on ajoute le paramètre name qui ne peut comporter que des lettres avec un tiret ou un espace, le + signifie qu'il en faut au moins 1, pour que ce paramètre soit optionnel on lui donne une valeur par défaut World précédé de ?
    
    //#[Route('/hello/{name}', name: 'app_hello', requirements: ['name' => '[a-zA-Z- ]+'], defaults: ['name' => 'World'])]  //version détaillée de la ligne précédente
    public function index(string $name, ValidatorInterface $validator, string $sfVersion): Response
    {
        //dump de la version de Symfony qui est cherchée avec une variable d'environnement .env.local et binder dans services.yaml
        dump($sfVersion);
        
        return $this->render('hello/index.html.twig', [
            'controller_name' => $name,
        ]);
    }
}
