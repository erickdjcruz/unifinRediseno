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

use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Listener\Success\UpdateUserLastLoginListener as
BaseUpdateUserLastLoginListener;

use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class UpdateUserLastLoginListener.
 *
 * Adds specific logic to BaseUpdateUserLastLoginListener for OIDC logged-in users
 */
class UpdateUserLastLoginListener extends BaseUpdateUserLastLoginListener
{
    /** @var SessionInterface $session */
    protected $session;

    /**
     * UpdateUserLastLoginListener constructor
     *
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * Do not update last-login field if it's not a fresh login, but rather just an auth-token check
     * and introspection.
     *
     * @inheritdoc
     */
    public function execute(AuthenticationEvent $event): void
    {
        if ($this->session->get('oidc_login_action')) {
            parent::execute($event);
        }
    }
}
