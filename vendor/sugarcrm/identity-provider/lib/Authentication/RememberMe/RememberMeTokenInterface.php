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

namespace Sugarcrm\IdentityProvider\Authentication\RememberMe;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

interface RememberMeTokenInterface extends TokenInterface
{
    public const LOGGED_IN = 1;

    public const ACTIVE = 2;

    public const LOGGED_OUT = 4;

    public const TOKEN_STATUS = 'token_status';

    /**
     * Set LoggedIn flag to token
     *
     * @return $this
     */
    public function setLoggedIn(): self;

    /**
     * Set Active flag to token
     *
     * @return $this
     */
    public function setLoggedActive(): self;

    /**
     * Set Active flag to token
     *
     * @return $this
     */
    public function setLoggedInactive(): self;

    /**
     * Set Logged out flag to token
     * @return $this
     */
    public function setLoggedOut(): self;

    /**
     * Is user active?
     * @return bool
     */
    public function isActive(): bool;

    /**
     * Is user logged in?
     * @return bool
     */
    public function isLoggedIn(): bool;

    /**
     * Is user logger out?
     * @return bool
     */
    public function isLoggedOut(): bool;

    /**
     * Get user SRN from token
     * @return string
     */
    public function getSRN(): string;

    /**
     * Get remembered user token
     * @return TokenInterface
     */
    public function getSource(): TokenInterface;

    /**
     * Get tenant id from remembered user token
     * @return string
     */
    public function getTenantId(): string;
}
