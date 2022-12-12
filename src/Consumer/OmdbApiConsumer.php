<?php

namespace App\Consumer;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OmdbApiConsumer
{
    // constanntes pour un mode de recherche par id ou par titre
    public const MODE_ID = 'i';
    public const MODE_TITLE = 't';

    //cette classe a besoin d'un client http pour communiquer avec l'api
    public function __construct(
        private HttpClientInterface $omdbClient
    ) {}

    // Méthode qui permet d'aller chercher les films grâce à l'api OMDB
    public function consume(string $type, string $value) : array
    {
        //on vérifie que le mode de recherche est valide c-a-d soit par titte soit par id
        if (!\in_array($type, [self::MODE_ID, self::MODE_TITLE])) {
            throw new \InvalidArgumentException(sprintf("Invalid mode provided for consumer : %s, or %s allowed, %s given",
                self::MODE_ID, self::MODE_TITLE, $type));
        }

        //on fait la requête http avec une méthode GET sans URL avec comme queryParameter le type de recherche ('t' ou 'i') et la valeur recherchée (le titre ou l'id)
        $data = $this->omdbClient
            ->request(Request::METHOD_GET, '', ['query' => [$type => $value]])
            //on récupère les données sous forme de tableau
            ->toArray();

        //si la réponse de l'api contient une clé 'Response' et que sa valeur est 'False' alors on lance une exception NotFoundHttpException (réponse 404)
        //car cette api n'est pas du tout restfull et renvoie une réponse 200 même si la recherche n'a rien donné avec response = false et error = 'Movie not found!'  
        if (array_key_exists('Response', $data) && $data['Response'] === 'False') {
            throw new NotFoundHttpException();
        }

        return $data;
    }
}