<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main_index')]
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'index',
        ]);
    }

    #[Route('/contact', name: 'app_main_contact')]
    public function contact(): Response
    {
        return $this->render('main/contact.html.twig', [
            'controller_name' => 'Contact',
        ]);
    }

    public function decades()
    {
        $decades = [
            '1970s',
            '1980s',
            '1990s',
            '2000s',
            '2010s',
        ];

        return $this->render('fragments/_decades.html.twig', [
            'decades' => $decades
        ]);
    }
}
