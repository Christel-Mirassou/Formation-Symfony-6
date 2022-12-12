<?php

namespace App\Provider;

use App\Entity\Movie;
use App\Consumer\OmdbApiConsumer;
use App\Repository\MovieRepository;
use App\Transformer\OmdbMovieTransformer;

class MovieProvider
{
    // on injecte le MovieRepository pour vérifier en BDD si le film existe déjà, puis on injecte le Consumer pour aller chercher les infos sur l'API et enfin on injecte le Transformer pour transformer les données de l'API en objet Movie
    public  function __construct(
        private MovieRepository $repository,
        private OmdbApiConsumer $consumer,
        private OmdbMovieTransformer $transformer
    ){}

    // on crée une méthode getMovie qui prend en paramètre le type de recherche (par ID ou par titre) et la valeur de recherche (l'ID ou le titre) et qui retourne un objet Movie
    public function getMovie(string $type, string $value): Movie
    {
        //1) On recherche en premier les données de l'API
       
        // On vérifie que le type de recherche est bien un type de recherche par titre ou ID
        // if(!\in_array($type, [OmdbApiConsumer::MODE_ID, OmdbApiConsumer::MODE_TITLE]))

        //Si la recherche est différente du mode titre, on lance une exception
        if($type !== OmdbApiConsumer::MODE_TITLE)
        {
            throw new \InvalidArgumentException();
        }
        
        //2)On récupère les données de l'API
        $data = $this->consumer->consume($type, $value);

        //on vérifie si le film existe déjà en BDD en fonction du titre
        if ($movie = $this->repository->findOneBy(['title' => $data['Title']])) {
            return $movie;
        }

        //3) On transforme les données de l'API en objet Movie et on l'envoie en BDD
        $movie = $this->transformer->transform($data);
        $this->repository->save($movie, true);

        return $movie;
    }
}