<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/book')]
class BookController extends AbstractController
{
    #[Route('', name: 'app_book_index')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    // Routing avec requirements
    // #[Route('/book/{id}', name: 'app_book_details', requirements: ['id' => '\d+'])]
    // #[Route('/book/{id<\d+>}', name: 'app_book_details')]
    // public function details($id): Response
    // {
    //     return $this->render('book/index.html.twig', [
    //         'controller_name' => $id,
    //     ]);
    // }

    // Routing avec valeur par défaut
    // #[Route('/book/id/{id<\d+>}', name: 'app_book_details', defaults: ['id' => 1])]   //valeur par defaut dans la route
    // #[Route('/book/id/{id<\d+>?1}', name: 'app_book_details')]   //valeur par defaut dans la route sans l'attribut defaults mais en gardant le requirements (qui est optionnel)
    // public function details($id = 1): Response   //valeur par défaut dans la fonction php
    // #[Route('/book/id/{id<\d+>?1}', name: 'app_book_details', methods: ['POST'])]   //cette route ne pourra plus être appelée que par la méthode POST ce qui cause une erreur 405 (not allowed)
    #[Route('/{id<\d+>?1}', name: 'app_book_details', methods: ['GET', 'POST'])]
    public function details(int $id): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => $id,
        ]);
    }
    
    #[Route('/new', name: 'app_book_new')]
    public function new(Request $request, ValidatorInterface $validator): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        
        $errrors = $validator->validate($book, null, ['Book']);

        if($this->isGranted('ROLE_ADMIN')){
            //
        }

        // return $this->render('book/new.html.twig', [
        //     'form' => $form->createView(),
        // ]);

        //Nouvelle version de Symfony pour la gestion des formulaires
        return $this->renderForm('book/new.html.twig', [
            'form' => $form,
        ]);
    }


}
