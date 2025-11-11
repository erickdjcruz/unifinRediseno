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

namespace Sugarcrm\IdentityProvider\Authentication\Token\OIDC;

use Sugarcrm\IdentityProvider\Authentication\Provider\Providers;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * Token to store OIDC auth result
 */
class ResultToken extends AbstractToken
{
    /**
     * @var string
     */
    protected $credentials = null;

    /**
     * @param string $credentials
     * @param array $attributes
     */
    public function __construct($credentials, $attributes)
    {
        $this->credentials = $credentials;
        $this->setAttributes($attributes);
        parent::__construct([]);
    }

    /**
     * @return string
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * return provider key
     * @return string
     */
    public function getProviderKey()
    {
        return Providers::PROVIDER_KEY_OIDC;
    }
}
