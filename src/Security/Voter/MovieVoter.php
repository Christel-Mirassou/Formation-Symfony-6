<?php

namespace App\Security\Voter;

use App\Entity\Movie;
use App\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

// Définition d'une stratégie pour ce voter en attribut le nombre définit son degré de priorité
class MovieVoter extends Voter
{
    public const VIEW = 'Movie.view';
    public const EDIT = 'Movie.edit';

    public function __construct(
        private AuthorizationCheckerInterface $checker
    ){}

    // C'est dans cette methode que l'on doit mettre les conditions dans lesquelles le voter va entrer en jeu
    protected function supports(string $attribute, mixed $subject): bool
    {
        //le voter vote si l'attribut est VIEW ou EDIT et si le sujet est une instance de Movie
        return \in_array($attribute, [self::VIEW, self::EDIT])
            && $subject instanceof Movie;
    }

    //C'est dans cette méthode que l'on définit les actions pour lesquelles le voter vote oui ou non
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        //ici on vérifie que le user est un admin qui a tous les droits
        if ($this->checker->isGranted('ROLE_ADMIN')) {
            return true;
        }

        //On vérifie que le user est dans la BDD
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        //on regarde quelle action est appelée pour le voter Edit ou View
        //Match remplace en PHP 8 le switch et if/elseif
        return match ($attribute){
            self::VIEW => $this->checkView($subject, $user),
            self::EDIT => $this->checkEdit($subject, $user),
            default => false,
        };
    }

    //Méthode qui définit si le user a le droit de regarder un film
    public function checkView(Movie $movie, User $user): bool
    {
        //SI le rating est G ou Not Rated on laisse le droit de voir
        if (\in_array($movie->getRated(), ['G', 'Not Rated'])) {
            return true;
        }
        
        //SI le user n'a pas donner sa date de naissance il n'a pas le droit de voir à part les G ou Not Rated
        if (!$user->getBirthday()){
            return false;
        }
        
        //On fait un différentiel sur la date de naissance pour avoir l'age
        $age = $user->getBirthday()->diff(new \DateTime())->y;
        
        //Grace à l'age on sait suivant les rating si les flms sont visibles ou non
        return match($movie->getRated()){
            'PG', 'PG-13' => $age >= 13,
            'NC-17', 'R' => $age >=17,
            default => false,
        };
    }

    //Méthode qui définit si le user a le droit d'éditer un film ou s'il est un Admin
    public function checkEdit(Movie $movie, User $user): bool
    {
        return $this->checker->isGranted('ROLE_ADMIN')||
            ($this->checkView($movie, $user) && $movie->getAddedBy() === $user);
    }
}