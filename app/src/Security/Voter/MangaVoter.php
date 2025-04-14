<?php

namespace App\Security\Voter;

use App\Entity\Manga;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Bundle\SecurityBundle\Security;

class MangaVoter extends Voter
{
    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    public function __construct(
        private Security $security
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])
            && $subject instanceof Manga;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        // Si l'utilisateur n'est pas authentifié, refuser l'accès
        if (!$user instanceof User) {
            return false;
        }

        // L'administrateur a tous les droits
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        /** @var Manga $manga */
        $manga = $subject;

        // Vérifier si l'utilisateur est le propriétaire du manga
        return match($attribute) {
            self::VIEW, self::EDIT, self::DELETE => $manga->getOwner() === $user,
            default => false,
        };
    }
}