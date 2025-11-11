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

class Generic extends Portal
{
    public function addVisibilityQuery(\SugarQuery $query, array $options = [])
    {
        $accountsOfContact = PortalFactory::getInstance('Session')->getAccountIds();

        if (!empty($accountsOfContact) && !empty($this->context->getAccountsRelationshipLink())) {
            // Contact with Accounts
            $this->addVisibilityQueryForContactWithAccounts($query, $options);
        } elseif (!empty($this->context->getContactsRelationshipLink())) {
            // Contact without Accounts
            $this->addVisibilityQueryForContactWithoutAccounts($query, $options);
        } else {
            // prevent visibility
            $query->where()->addRaw('1 = 0');
        }
    }

    protected function addVisibilityQueryForContactWithAccounts(\SugarQuery $query, array $options = [])
    {
        $query->join($this->context->getAccountsRelationshipLink(), ['alias' => 'gen_acct_portal'])->on()->in('gen_acct_portal.id', PortalFactory::getInstance('Session')->getAccountIds());
    }

    protected function addVisibilityQueryForContactWithoutAccounts(\SugarQuery $query, array $options = [])
    {
        $query->join($this->context->getContactsRelationshipLink(), ['alias' => 'gen_cont_portal'])->on()->equals('gen_cont_portal.id', PortalFactory::getInstance('Session')->getContactId());
    }
}
