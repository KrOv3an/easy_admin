<?php

namespace App\Security\Voter;

use App\Entity\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminUserVoter extends Voter
{
    /**
     * @param Security $security
     */
    public function __construct(private readonly Security $security) {
    }

    /**
     * @param string $attribute
     * @param $subject
     * @return bool
     */
    protected function supports(string $attribute, $subject): bool
    {
        return $attribute === 'ADMIN_USER_EDIT' && $subject instanceof User;
    }

    /**
     * @param string $attribute
     * @param $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if (!$subject instanceof User) {
           throw new LogicException('Subject is not instance of User');
        }

        if ($attribute === 'ADMIN_USER_EDIT') {
            return $user === $subject || $this->security->isGranted('ROLE_SUPER_ADMIN');
        }

        return false;
    }
}
