<?php

namespace App\Security\Voter;

use App\Entity\Book;
use Attribute;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

// Définition d'une stratégie pour ce voter en attribut le nombre définit son degré de priorité
// #[AutoconfigureTag('security.voter', attributes: ['priority' => 2])]
class BookVoter extends Voter
{
    public const VIEW = 'book.view';
    public const EDIT = 'book.edit';

    public function __construct(
        private AuthorizationCheckerInterface $checker
    ){}

    // C'est dans cette methode que l'on doit mettre les conditions dans lesquelles le voter va entrer en jeu
    protected function supports(string $attribute, mixed $subject): bool
    {
        //le voter vote si l'attribut est VIEW ou EDIT et si le sujet est une instance de Book
        return \in_array($attribute, [self::VIEW, self::EDIT])
            && $subject instanceof Book;
    }

    //C'est dans cette méthode que l'on définit les actions pour lesquelles le voter vote oui ou non
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        //ici on vérifie que le user appartient à nos users
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        //ici on détermine si le user à le droit d'editer un livre en utilisant une méthode définit en-dessous
        if($attribute === self::EDIT){
            return $this->checkEdit($subject, $user);
        }
        return false;
    }

    //Méthode qui définit si le user a le droit d'éditer un livre ou s'il est un Admin
    public function checkEdit(Book $book, UserInterface $user): bool
    {
        return $book->getAddedBy() === $user || $this->checker->isGranted('ROLE_ADMIN'); 
    }
}