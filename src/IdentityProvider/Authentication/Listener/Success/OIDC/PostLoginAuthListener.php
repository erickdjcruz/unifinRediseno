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

use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Listener\Success\PostLoginAuthListener as
BasePostLoginAuthListener;

use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PostLoginAuthListener extends BasePostLoginAuthListener
{
    /** @var SessionInterface $session */
    protected $session;

    /**
     * UpdateUserLastLoginListener constructor.
     *
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * Delete OIDC login-action marker at the end of the listeners chain
     *
     * @inheritdoc
     */
    public function execute(AuthenticationEvent $event): void
    {
        parent::execute($event);
        if ($this->session->get('oidc_login_action')) {
            $this->session->remove('oidc_login_action');
            $currentUser = $event->getAuthenticationToken()->getUser()->getSugarUser();
            $currentUser->call_custom_logic('after_login');
        }
    }
}
