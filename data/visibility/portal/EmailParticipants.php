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
use SugarQuery;
use Sugarcrm\Sugarcrm\Visibility\Portal\Emails;

/**
 * Class EmailParticipants
 * Applies visibility restrictions to EmailParticipants retrieved via portal.
 *
 * @package Sugarcrm\Sugarcrm\data\visibility\portal
 */
class EmailParticipants extends Portal
{
    protected $visibilityQueryOr;

    public function addVisibilityQuery(\SugarQuery $query, array $options = [])
    {
        $query->where()->equals($options['table_alias'] . '.portal_flag', 1);

        $this->visibilityQueryOr = $query->where()->queryOr();

        $this->addVisibilityOrQueryEmails($options);
    }

    /**
     * This is for the email participants.
     * @param array $options
     * @throws \SugarQueryException
     */
    protected function addVisibilityOrQueryEmails(array $options)
    {
        $emailBean = BeanFactory::newBean('Emails');
        $subQuery = new SugarQuery();
        $subQuery->select('id');
        $subQuery->from($emailBean);

        // apply the email visibility
        $emailVisibility = new Emails($this->context);
        $emailVisibility->addVisibilityQuery($subQuery);

        // convert the SugarQuery to a Doctrine DBAL query
        $subQueryBuilder = $subQuery->compile();

        // if the participant's email_id belongs to an email that is visible, then this participant
        // should be visible as well
        $this->visibilityQueryOr->in($options['table_alias'] . '.email_id', $subQueryBuilder);
    }
}
