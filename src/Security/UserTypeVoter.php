<?php
namespace App\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserTypeVoter extends Voter
{
    const IS_CLIENT = 'IS_CLIENT';
    const IS_CHEF_EQUIPE = 'IS_CHEF_EQUIPE';
    const IS_TECHNICIAN = 'IS_TECHNICIAN';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::IS_CLIENT, self::IS_CHEF_EQUIPE, self::IS_TECHNICIAN]);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        // Vérifier le type d'utilisateur et accorder/refuser l'accès
        switch ($attribute) {
            case self::IS_CLIENT:
                return $user instanceof \App\Entity\Client;
            case self::IS_CHEF_EQUIPE:
                return $user instanceof \App\Entity\Chefequipe;
            case self::IS_TECHNICIAN:
                return $user instanceof \App\Entity\Technicien;
        }

        return false;
    }
}