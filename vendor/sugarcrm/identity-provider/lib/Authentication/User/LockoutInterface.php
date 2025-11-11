<?php


namespace Sugarcrm\IdentityProvider\Authentication\User;

use Sugarcrm\IdentityProvider\Authentication\Exception\PermanentLockedUserException;
use Sugarcrm\IdentityProvider\Authentication\Exception\TemporaryLockedUserException;
use Sugarcrm\IdentityProvider\Authentication\User;

interface LockoutInterface
{
    /**
     * Is lockout enabled.
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * Is user locked?
     * @param User $user
     * @return bool
     */
    public function isUserLocked(User $user): bool;

    /**
     * @param User $user
     * @throws TemporaryLockedUserException | PermanentLockedUserException
     */
    public function throwLockoutException(User $user): void;
}
