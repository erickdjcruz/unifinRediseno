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
 * Class Messages
 * Applies visibility restrictions to Messages retrieved via portal.
 *
 * @package Sugarcrm\Sugarcrm\data\visibility\portal
 */
class Messages extends Portal
{
    /**
     * Apply portal visibility rules to Messages
     * @param SugarQuery $query
     * @param array $options
     */
    public function addVisibilityQuery(SugarQuery $query, array $options = [])
    {
        $this->addCaseVisibilityQuery($query);
        $messageVisibility = PortalFactory::getInstance('Settings')->getModuleVisibility('message');
        if ($messageVisibility === 'related_contacts') {
            $this->addContactVisibilityQuery($query);
        }
    }

    /**
     * Only display Messages if they are related to the Current Portal Contact
     * @param SugarQuery $query
     */
    private function addContactVisibilityQuery(SugarQuery $query)
    {
        $options = [
            'alias' => 'messages_cont_portal',
            // $query->join calls the joined class's addVisibilityQuery when
            // team_security is true. For related beans Visibility Queries, query->join
            // doesn't use subqueries, but instead applies the join->on() params
            // to the main query. Contacts Visibility class has a join on Accounts, and
            // the resulting SQL attempted to match the Message's parent_id to the
            // account instead of the Contact's. We already limit visibility based
            // on contacts/accounts in addCaseVisibilityQuery, so we can set
            // team_security false for this one.
            'team_security' => false,
        ];
        $query->join($this->context->getContactsRelationshipLink(), $options)
            ->on()->queryAnd()
            ->equals('messages_cont_portal.id', PortalFactory::getInstance('Session')->getContactId());
    }

    /**
     * Create a subquery to apply Case visibility rules, and join on the table
     * created via the subquery to ensure Messages are only visible if their
     * parent case is also portal visible.
     * @param SugarQuery $query
     */
    private function addCaseVisibilityQuery(SugarQuery $query)
    {
        // Create a subquery for cases. Cases Portal Visibility restrictions
        // will be applied here
        $caseBean = BeanFactory::newBean('Cases');
        $subQuery = new SugarQuery();
        $subQuery->select('id');
        $subQuery->from($caseBean);

        // We need to convert the SugarQuery to a Doctrine DBAL query in order
        // to join on it.
        $subQueryBuilder = $subQuery->compile();
        $query->joinTable($subQueryBuilder, ['alias' => 'msg_case_portal'])->on()
            ->queryAnd()
            ->equals('messages.parent_type', 'Cases')
            ->equalsField('messages.parent_id', 'msg_case_portal.id');
    }
}
