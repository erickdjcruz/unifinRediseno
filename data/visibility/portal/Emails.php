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

use BeanFactory;
use Sugarcrm\Sugarcrm\Portal\Factory as PortalFactory;
use SugarQuery;

/**
 * Class Emails
 * Applies visibility restrictions to Emails retrieved via portal.
 *
 * @package Sugarcrm\Sugarcrm\data\visibility\portal
 */
class Emails extends Portal
{
    /**
     * Add query to restrict record visibility for portal users.
     *
     * @param SugarQuery $query
     * @param array $options
     */
    public function addVisibilityQuery(SugarQuery $query, array $options = [])
    {
        $this->addCaseVisibilityQuery($query);
        $emailVisibility = PortalFactory::getInstance('Settings')->getModuleVisibility('email');
        if ($emailVisibility === 'related_contacts') {
            $this->addContactVisibilityQuery($query);
        }
    }

    /**
     * Restrict Email visibility based on whether or not an email has an email address
     * relationship with the current portal contact.
     *
     * @param SugarQuery $query
     */
    private function addContactVisibilityQuery(SugarQuery $query)
    {
        // Restrict visibility to emails with a Contact Email Address, if that contact
        // is the current portal contact. Emails don't have a direct relationship with
        // contacts in the to, cc, or bcc fields, so we go through the emails_email_addr_rel
        // table where those values are actually stored.
        $query->joinTable('emails_email_addr_rel', ['alias' => 'email_addr_rel'])->on()->queryAnd()
            ->equals('email_addr_rel.parent_type', 'Contacts')
            ->equals('email_addr_rel.parent_id', PortalFactory::getInstance('Session')->getContactId());
        $query->where()->queryAnd()->addRaw('email_addr_rel.email_id=emails.id');
    }

    /**
     * Create a subquery to apply Case visibility rules, and join on the table
     * created via the subquery to ensure Emails are only visible if their
     * parent case is also portal visible.
     * @param SugarQuery $query
     */
    private function addCaseVisibilityQuery(SugarQuery $query)
    {
        // Emails have a m2m relationship with Cases via the multi-purpose emails_beans
        // table, so we have to join on that first.
        $query->joinTable('emails_beans', ['alias' => 'emails_beans_portal'])->on()->queryAnd()
            ->equalsField('emails.id', 'emails_beans_portal.email_id')
            ->equals('emails_beans_portal.bean_module', 'Cases');

        // Create a subquery for cases. Cases Portal Visibility restrictions
        // will be applied here
        $caseBean = BeanFactory::newBean('Cases');
        $subQuery = new SugarQuery();
        $subQuery->select('id');
        $subQuery->from($caseBean);

        // We need to convert the SugarQuery to a Doctrine DBAL query in order
        // to join on it.
        $subQueryBuilder = $subQuery->compile();

        // Finally, we join on our subquery of visible cases where the email is
        // related to a visible case either via the emails_beans table or the
        // via the emails parent_id field
        $query->joinTable($subQueryBuilder, ['alias' => 'email_case_portal'])->on()->queryOr()
            ->equalsField('emails_beans_portal.id', 'email_case_portal.id')
            ->equalsField('emails.parent_id', 'email_case_portal.id');
    }
}
