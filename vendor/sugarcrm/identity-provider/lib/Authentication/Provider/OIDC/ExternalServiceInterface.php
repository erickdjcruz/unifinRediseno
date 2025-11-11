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

namespace Sugarcrm\IdentityProvider\Authentication\Provider\OIDC;

use League\OAuth2\Client\Token\AccessToken;

interface ExternalServiceInterface
{
    /**
     * @return string
     */
    public function getAuthorizationUrl(): string;

    /**
     * @param $accessToken
     * @return array
     */
    public function getUserInfo($accessToken): array;

    /**
     * @param array $options
     * @return AccessToken
     */
    public function getAccessToken(array $options = []): AccessToken;

    /**
     * @param $state
     * @return bool
     */
    public function checkState($state): bool;
}
