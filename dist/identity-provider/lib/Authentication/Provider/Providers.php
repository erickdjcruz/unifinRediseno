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

namespace Sugarcrm\IdentityProvider\Authentication\Provider;

/**
 * Class for Providers codes references.
 */
class Providers
{
    /**
     * Local provider code
     */
    const LOCAL = 'local';

    /**
     * LDAP provider code
     */
    const LDAP = 'ldap';

    /**
     * SAML provider code
     */
    const SAML = 'saml';

    const OIDC = 'oidc';

    /**
     * Constants to determinate AuthenticationProvider classes
     */
    public const PROVIDER_KEY_LOCAL = 'PROVIDER_KEY_LOCAL';

    public const PROVIDER_KEY_LDAP = 'PROVIDER_KEY_LDAP';

    public const PROVIDER_KEY_SAML = 'PROVIDER_KEY_SAML';

    public const PROVIDER_KEY_OIDC = 'PROVIDER_KEY_OIDC';

    public const PROVIDER_KEY_MIXED = 'PROVIDER_KEY_MIXED';
}
