<?php

namespace App\Security\Voter;

use App\Entity\Employee;
use App\Entity\Project;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class EmployeeProjectVoter extends Voter
{
    public const LIST = 'PROJECT_LIST';
    public const LIST_ALL = 'PROJECT_ALL';
    public const VIEW = 'PROJECT_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return 
            in_array($attribute, [self::LIST_ALL, self::LIST]) ||
            (
                in_array($attribute, [self::VIEW])
                && $subject instanceof Project
            );
    }
    
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof Employee) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                $employeeIds = $subject->getEmployees()->map(fn($employee) => $employee->getId())->toArray();
                $userId = $user->getId();

                return in_array($userId, $employeeIds) || in_array('ROLE_PROJECT_MANAGER', $user->getRoles());
                break;

            case self::LIST:
                // logic to determine if the user can VIEW
                // return true or false
                return true;
                break;

            case self::LIST_ALL:
                return in_array('ROLE_PROJECT_MANAGER', $user->getRoles());
                break;
        }

        return false;
    }
}
