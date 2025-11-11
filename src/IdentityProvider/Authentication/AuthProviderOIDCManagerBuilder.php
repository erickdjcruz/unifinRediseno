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

namespace Sugarcrm\Sugarcrm\IdentityProvider\Authentication;

use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Listener\Success\LoadUserOnSessionListener;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Listener\Success\OIDC\SessionListener;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Listener\Success\OIDC\PostLoginAuthListener;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Listener\Success\OIDC\UpdateUserLanguageListener;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Listener\Success\OIDC\UpdateUserLastLoginListener;
use Sugarcrm\Sugarcrm\IdentityProvider\SessionProxy;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\AuthenticationEvents;

class AuthProviderOIDCManagerBuilder extends AuthProviderManagerBuilder
{
    /**
     * @return EventDispatcherInterface
     */
    protected function getAuthenticationEventDispatcher()
    {
        $dispatcher = new EventDispatcher();
        $session = new SessionProxy();

        $dispatcher->addListener(
            AuthenticationEvents::AUTHENTICATION_SUCCESS,
            [new SessionListener(), 'execute'],
            999
        );

        $dispatcher->addListener(
            AuthenticationEvents::AUTHENTICATION_SUCCESS,
            [new LoadUserOnSessionListener(), 'execute']
        );

        $dispatcher->addListener(
            AuthenticationEvents::AUTHENTICATION_SUCCESS,
            [new UpdateUserLastLoginListener($session), 'execute']
        );

        $dispatcher->addListener(
            AuthenticationEvents::AUTHENTICATION_SUCCESS,
            [new UpdateUserLanguageListener(), 'execute']
        );
        $dispatcher->addListener(
            AuthenticationEvents::AUTHENTICATION_SUCCESS,
            [new PostLoginAuthListener($session), 'execute'],
            -999
        );

        return $dispatcher;
    }
}
