<?php

namespace App\Controller;

use App\Consumer\OmdbApiConsumer;
use App\Entity\Movie;
use App\Provider\MovieProvider;
use App\Repository\MovieRepository;
use App\Transformer\OmdbMovieTransformer;
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

    // ROUTE qui permet d'utiiser l'API OMDB et aller chercher des films

    // 1) Après la création du service Consumer
    //
    // #[Route('/omdb/{title}', name: 'app_movie_omdb')]
    // public function omdb(string $title, OmdbApiConsumer $consumer)
    // {
    //     // dd($consumer->consume('t', $title));
    //     return $this->render('movie/details.html.twig', [
    //         'movie' => new Movie(),
    //     ]);
    // }
    
    // 2) Après la création du service Transformer
    //
    // #[Route('/omdb/{title}', name: 'app_movie_omdb')]
    // public function omdb(string $title, OmdbApiConsumer $consumer, OmdbMovieTransformer $transformer)
    // {
    //     $data = $consumer->consume('t', $title);
    //     $movie = $transformer->transform($data);

    //     return $this->render('movie/details.html.twig', [
    //         'movie' => $movie,
    //     ]);
    // }

    // 3) Après la création du Provider qui va centraliser tout ce qui se passe avant et d'envoyer tout en BDD
    #[Route('/omdb/{title}', name: 'app_movie_omdb')]
    public function omdb(string $title, MovieProvider $provider)
    {
        return $this->render('movie/details.html.twig', [
            'movie' => $provider->getMovie(OmdbApiConsumer::MODE_TITLE, $title),
        ]);
    }

}
