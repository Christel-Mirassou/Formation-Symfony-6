<?php

namespace App\Transformer;

use App\Entity\Genre;
use App\Entity\Movie;
use App\Repository\GenreRepository;
use RuntimeException;
use Symfony\Component\Form\DataTransformerInterface;

class OmdbMovieTransformer implements DataTransformerInterface
{
    // On injecte le GenreRepository dans le constructeur car on a une entité Genre et qu'on peut en avoir plusieurs
    public function __construct(
        private GenreRepository $genreRepository,
    ){}

    // Méthode qui permet de transformer les données que l'on lui donne en un objet Movie
    public function transform(mixed $value): Movie
    {
        // On récupère les genres séparés par une virgule et on les transforme en tableau
        $genreNames = explode(', ', $value['Genre']);

        // On vérifie si la date de sortie est renseignée, on prend l'année sinon 'N/A' (Non renseignée)
        $date = $value['Released'] === 'N/A' ? $value['Year'] : $value['Released'];
        
        // On crée un objet Movie en récupérant les données de l'API
        $movie = (new Movie())
            ->setTitle($value['Title'])
            ->setPoster($value['Poster'])
            ->setCountry($value['Country'])
            ->setReleasedAt(new \DateTimeImmutable($date))
            ->setRated($value['Rated'])
            ->setImdbId($value['imdbID'])
            ->setPrice(5.0)  //prix par défaut
        ;

        // On parcourt le tableau des genres
        foreach ($genreNames as $name) {
            //on vérifie si le genre existe déjà dans la BDD
            $entity = $this->genreRepository->findOneBy(['name' => $name])
                // Si non on l'ajoute
                ?? (new Genre())->setName($name);
            //on ajoute le genre au film
            $movie->addGenre($entity);
        }

        return $movie;
    }

    public function reverseTransform(mixed $value): array
    {
        throw new RuntimeException("Method not implemented");
    }

}