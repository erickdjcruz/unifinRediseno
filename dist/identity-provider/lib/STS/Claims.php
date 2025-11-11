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

namespace Sugarcrm\IdentityProvider\STS;

/**
 * Represents OpenID Connect claims used in STS
 *
 * @package Sugarcrm\IdentityProvider\STS
 */
class Claims
{
    /**
     * OpenId Connect standard attributes.
     * On par with multiverse/apis/iam/user/v1alpha/user.proto we use a superset of standard claims
     */
    const OIDC_ATTRIBUTES = [
        'given_name',
        'family_name',
        'middle_name',
        'nickname',
        'email',
        'phone_number',
        'address',
        'department',
        'title',
    ];

    /**
     * OIDC claims by requested scope
     * see https://openid.net/specs/openid-connect-core-1_0.html#ScopeClaims
     */
    const CLAIMS_BY_SCOPE = [
        'profile' => [
            'name',
            'family_name',
            'given_name',
            'middle_name',
            'nickname',
            'preferred_username',
            'profile',
            'picture',
            'website',
            'gender',
            'birthdate',
            'zoneinfo',
            'locale',
            'updated_at',
        ],
        'email' => ['email', 'email_verified'],
        'address' => ['address'],
        'phone' => ['phone_number', 'phone_number_verified'],
    ];
}
