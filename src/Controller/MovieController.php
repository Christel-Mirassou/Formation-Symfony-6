<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/movie')]
class MovieController extends AbstractController
{
    #[Route('', name: 'app_movie_index')]
    public function index(MovieRepository $movieRepository): Response
    {
        return $this->render('movie/index.html.twig', [
            'movies' => $movieRepository->findAll(),
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_movie_details')]
    public function details(int $id, MovieRepository $movieRepository): Response
    {
        $movie = $movieRepository->find($id);
        
        return $this->render('movie/details.html.twig', [
            'movie' => $movie,
        ]);
    }
    //OU
    //MAIS problème si la BDD est vide une grosse erreur s'affiche 
    // public function details(Movie $movie): Response
    // {
    //     return $this->render('movie/details.html.twig', [
    //         'movie' => $movie,
    //     ]);
    // }
}
