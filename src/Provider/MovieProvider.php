<?php

namespace App\Provider;

use App\Entity\User;
use App\Entity\Movie;
use App\Consumer\OmdbApiConsumer;
use App\Repository\MovieRepository;
use App\Transformer\OmdbMovieTransformer;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MovieProvider
{
    //on crée une propriété privée nullable pour le SymfonyStyle qui va aidé à l'affichage dans le terminal
    private ?SymfonyStyle $io = null;

    // on injecte le MovieRepository pour vérifier en BDD si le film existe déjà, puis on injecte le Consumer pour aller chercher les infos sur l'API et enfin on injecte le Transformer pour transformer les données de l'API en objet Movie
    public  function __construct(
        private MovieRepository $repository,
        private OmdbApiConsumer $consumer,
        private OmdbMovieTransformer $transformer,
        private AuthorizationCheckerInterface $checker, //pour vérifier le role d'un user
        //private Security $security,     //ici on injecte le composant Security en entier
        private TokenStorageInterface $storage, //Plutot que d'injecter Security pour le user on peut seulement injecter le Token
    ) {
    }

    // on crée une méthode getMovie qui prend en paramètre le type de recherche (par ID ou par titre) et la valeur de recherche (l'ID ou le titre) et qui retourne un objet Movie
    public function getMovie(string $type, string $value): Movie
    {
        //1) On recherche en premier les données de l'API

        // On autorise la recherche par titre ou par ID
        if (!\in_array($type, [OmdbApiConsumer::MODE_ID, OmdbApiConsumer::MODE_TITLE])) {
            throw new \InvalidArgumentException();
        }

        // On affiche un message dans le terminal disant q'uon appelle l'API
        // $this->sendIo('text', "Calling OMDB API");
        $this->io?->text("Calling OMDB API");

        //Si la recherche est différente du mode titre, on lance une exception
        // if($type !== OmdbApiConsumer::MODE_TITLE)
        // {
        //     throw new \InvalidArgumentException();
        // }

        //2)On récupère les données de l'API
        $data = $this->consumer->consume($type, $value);

        //on vérifie si le film existe déjà en BDD en fonction du titre
        if ($movie = $this->repository->findOneBy(['title' => $data['Title']])) {

            //On affiche un message dans le terminal disant que le film existe déjà en BDD
            // $this->sendIo('note', "Movie found in database");
            $this->io?->note("Movie found in database");

            return $movie;
        }

        // AUTHORIZATION :

        // 1)Utilisation du service d'AuthorizationCheckerInterface
        // if($this->checker->isGranted('ROLE_ADMIN')){
        //     //
        // }

        // 2)Utilisation du composant SECURITY
        // $user = $this->security->getUser();
        // if($this->security->isGranted){
        //     //
        // }



        //On affiche un message dans le terminal disant que le film n'existe pas en BDD et qu'on le sauvegarde en BDD
        // $this->sendIo('section', "Movie not found in database, saving");
        $this->io?->section("Movie not found in database, saving");

        //3) On transforme les données de l'API en objet Movie et on l'envoie en BDD
        $movie = $this->transformer->transform($data);

        // Version avec SECURITY d'injecter
        //on vérifie que le user est bien en BDD
        // if (($user = $this->security->getUser()) instanceof User) {
        //     //on permet aux utilisateurs d'ajouter un film en BDD avec leur propre id
        //     $movie->setAddedBy($user);
        // }

        //Version avec Token d'injecter
        //on vérifie que le user est bien en BDD
        if (($user = $this->storage->getToken()?->getUser()) instanceof User) {
            //on permet aux utilisateurs d'ajouter un film en BDD avec leur propre id
            $movie->setAddedBy($user);
        }

        $this->repository->save($movie, true);

        //On affiche un message dans le terminal disant que le film a été sauvegardé en BDD
        // $this->sendIo('note', "Movie saved!");
        $this->io?->note("Movie saved!");

        return $movie;
    }

    //On pourra ainsi depuis la méthode getMovie, documenter dans la console ce que l'on fait quand il y aura un SymfonyStyle
    public function setSymfonyStyle(SymfonyStyle $io): void
    {
        $this->io = $io;
    }


    //On supprime cette méthode car on utilise le null coalescing operator PHP8

    //On créé une méthode privée pour envoyer les messages à la console
    // private function sendIo(string $type, string $message): void
    // {
    //     // if ($this->io) {
    //     //     $this->io->$type($message);
    //     // }

    //     // On peut simplifier le code en utilisant le null coalescing operator PHP8
    //     $this->io?->$type($message);
    // }
}
