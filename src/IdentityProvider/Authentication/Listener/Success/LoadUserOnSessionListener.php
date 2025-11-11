<?php
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

namespace Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Listener\Success;

use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\User;

use Symfony\Component\Security\Core\Event\AuthenticationEvent;

class LoadUserOnSessionListener
{
    /**
     * set user in globals and session
     * @param AuthenticationEvent $event
     */
    public function execute(AuthenticationEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        $this->setGlobalUser($user);
        $this->setSessionUserId($user);
        $this->setSessionIdmUserManager($user);
    }

    /**
     * set current user into global variables
     * @param User $user
     */
    protected function setGlobalUser(User $user)
    {
        global $current_user;
        $current_user = $user->getSugarUser();
    }

    /**
     * set user id into $_SESSION
     * @param User $user
     */
    protected function setSessionUserId(User $user)
    {
        $_SESSION['authenticated_user_id'] = $user->getSugarUser()->id;
    }

    /**
     * set idm user manager flag into $_SESSION
     * This is needed for BWC modules. Because it doesn't use token introspection and load user directly from session.
     * @param User $user
     */
    protected function setSessionIdmUserManager(User $user)
    {
        $_SESSION['is_idm_user_manager'] = $user->getSugarUser()->isIdmUserManager;
    }
}
