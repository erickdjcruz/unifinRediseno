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

namespace Sugarcrm\Sugarcrm\Visibility\Portal;

use Sugarcrm\Sugarcrm\Portal\Factory as PortalFactory;

class Contacts extends Portal
{
    public function addVisibilityQuery(\SugarQuery $query, array $options = [])
    {
        $accountsOfContact = PortalFactory::getInstance('Session')->getAccountIds();
        if (!empty($accountsOfContact)) {
            // Contact with Accounts
            $this->addVisibilityQueryForContactWithAccounts($query, $options);
        } else {
            // Contact without Accounts
            $this->addVisibilityQueryForContactWithoutAccounts($query, $options);
        }
    }

    protected function addVisibilityQueryForContactWithAccounts(\SugarQuery $query, array $options = [])
    {
        $query->join($this->context->getAccountsRelationshipLink(), ['alias' => 'cont_acct_portal'])->on()->in('cont_acct_portal.id', PortalFactory::getInstance('Session')->getAccountIds());
    }

    protected function addVisibilityQueryForContactWithoutAccounts(\SugarQuery $query, array $options = [])
    {
        // Special case, if there are no accounts in the list, at least allow them access to their own contact
        $query->where()->equals($options['table_alias'] . '.id', PortalFactory::getInstance('Session')->getContactId());
    }
}
