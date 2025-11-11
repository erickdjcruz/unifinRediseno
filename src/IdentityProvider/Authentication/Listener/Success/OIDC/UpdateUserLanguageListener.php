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

namespace Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Listener\Success\OIDC;

use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\User;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;

/**
 * Class UpdateUserLanguageListener
 * @package Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Listener\Success\OIDC
 */
class UpdateUserLanguageListener
{
    /**
     * @param AuthenticationEvent $event
     */
    public function execute(AuthenticationEvent $event): void
    {
        /** @var User $idmUser */
        $idmUser = $event->getAuthenticationToken()->getUser();
        $sugarUser = $idmUser->getSugarUser();
        $userAttributes = $idmUser->getAttribute('oidc_data');

        if (!empty($userAttributes['preferred_language']) &&
            $userAttributes['preferred_language'] !== $sugarUser->preferred_language) {
            $sugarUser->preferred_language = $userAttributes['preferred_language'];
            $sugarUser->save();
        }
    }
}
