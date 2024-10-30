<?php

namespace App\Security\Voter;

use App\Entity\Employee;
use App\Entity\Project;
use App\Entity\Task;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class EmployeeTaskVoter extends Voter
{
    public const EDIT = 'TASK_EDIT';
    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return 
            in_array($attribute, [self::EDIT])
            && $subject instanceof Task;
    }
    
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof Employee) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:

                return $subject->getEmployee()->getId() === $user->getId();;
                break;
        }

        return false;
    }
}
