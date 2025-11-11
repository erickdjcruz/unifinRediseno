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

/**
 * Class PMSEFilterOutboundEmailsApi
 * Filters OutboundEmails to ensure we get active and non-portal user accounts
 */
class PMSEFilterOutboundEmailsApi extends FilterApi
{
    /**
     * @inheritdoc
     *
     * Force a where clause to only get OutboundEmail accounts for real existing users
     */
    public function filterListSetup(ServiceBase $api, array $args, $acl = 'list')
    {
        [$args, $q, $options, $seed] = parent::filterListSetup($api, $args, $acl);
        $usersAlias = $q->getJoinTableAlias('users', false, false);
        $q->joinTable('users', ['alias' => $usersAlias])
            ->on()
            ->equalsField('user_id', $usersAlias . '.id');
        $q->where()->equals($usersAlias . '.status', 'Active')->queryAnd()->notEquals($usersAlias . '.portal_only', 1);
        return [$args, $q, $options, $seed];
    }
}
