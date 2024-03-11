<?php

namespace App\Security\Voter;

use App\Entity\MicroPost;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends Voter<string, MicroPost>
 */
class MicroPostVoter extends Voter
{
    public const string EDIT = 'POST_EDIT';
    public const string VIEW = 'POST_VIEW';

    public function __construct(protected Security $security)
    {

    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW], strict: true)
            && $subject instanceof MicroPost;
    }


    /**
     * @param string $attribute
     * @param MicroPost $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) return false;
        if ($this->security->isGranted('ROLE_ADMIN')) return true;

        /** @var User $userEntity */
        $userEntity = $user;

        // ... (check conditions and return true to grant permission) ...
        return match ($attribute) {
            self::EDIT => $subject->getAuthor()?->getId() === $userEntity->getId() &&
                $this->security->isGranted('ROLE_EDITOR'),
            self::VIEW => true,
            default    => false,
        };
    }
}
