<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/book/{id<\d+>}', name: 'app_book_delete', methods: ['DELETE'])]
class DeleteBookController extends AbstractController
{
    public function __invoke(int $id): Response
    {
        return $this->redirectToRoute('app_book_index');
    }
}