<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main_index')]
    public function index(MovieRepository $movieRepository): Response
    {
        return $this->render('main/index.html.twig', [
            'movies' => $movieRepository->findBy([], ['id' => 'DESC'], limit: 6),
        ]);
    }

    #[Route('/contact', name: 'app_main_contact')]
    public function contact(Request $request): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dump($form->getData());

            return $this->redirectToRoute('app_main_contact');
        }
        return $this->renderForm('main/contact.html.twig', [
            'form' => $form,
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
