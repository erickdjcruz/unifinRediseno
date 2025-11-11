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
use BeanFactory;
use SugarQuery;
use Sugarcrm\Sugarcrm\Visibility\Portal\Emails;

/**
 * IMPORTANT NOTE: Notes is a related module that needs to be filtered based on the visibility of the parent objects
 * IMPORTANT NOTE: If any of the visibility logic on the parent objects (Bugs, Cases and Knowledgebases) changes,
 *  it will need to be updated also below and most likely vice-versa
 */
class Notes extends Portal
{
    protected $visibilityQueryOr;
    protected $query;

    public function addVisibilityQuery(\SugarQuery $query, array $options = [])
    {
        $query->where()->equals($options['table_alias'] . '.portal_flag', 1);

        // add the three subqueries as separate in conditions, if the relevant modules are accessible by the user
        $this->visibilityQueryOr = $query->where()->queryOr();
        $this->query = $query;

        $this->addVisibilityOrQueryCases($options);
        $this->addVisibilityOrQueryBugs($options);
        $this->addVisibilityOrQueryKBContents($options);
        $this->addVisibilityOrQueryAttachments($options);
        $this->addVisibilityOrQueryEmails($options);
    }

    /**
     * This is for the email attachment.
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

        // if the note's email_id belongs to an email that is visible, then this note
        // should be visible as well because it's an attachment of that email
        $this->visibilityQueryOr->in($options['table_alias'] . '.email_id', $subQueryBuilder);
    }

    protected function addVisibilityOrQueryCases(array $options)
    {
        if ($this->isModuleAccessible('Cases') && $this->validateOrQuery($this->visibilityQueryOr)) {
            $accountsOfContact = PortalFactory::getInstance('Session')->getAccountIds();
            if (!empty($accountsOfContact)) {
                // Contact with Accounts
                $caseVisibility = PortalFactory::getInstance('Settings')->getCaseVisibility();
                if ($caseVisibility === 'related_contacts') {
                    $casesQb = $this->addVisibilityQueryForRelatedContacts($this->query);
                } else {
                    $casesQb = $this->addVisibilityQueryForContactWithAccounts($this->query);
                }
            } else {
                // Contact without Accounts
                $casesQb = $this->addVisibilityQueryForContactWithoutAccounts($this->query);
            }

            $this->visibilityQueryOr->in($options['table_alias'] . '.id', $casesQb);

            // for attachments
            $this->visibilityQueryOr->in($options['table_alias'] . '.note_parent_id', $casesQb);
        }
    }

    protected function addVisibilityQueryForRelatedContacts(\SugarQuery $query)
    {
        // get note ids attached to the Cases which are visible to this contact
        $contactId = PortalFactory::getInstance('Session')->getContactId();
        $casesQb = $this->db->getConnection()->createQueryBuilder();
        $casesQb->select(['n.id']);
        $casesQb->from('notes', 'n');
        $casesQb->innerJoin(
            'n',
            'cases',
            'c',
            'c.id = n.parent_id and n.parent_type = ' . $casesQb->createPositionalParameter('Cases') .
            ' and n.deleted = ' . $casesQb->createPositionalParameter(0) .
            ' and c.deleted = ' . $casesQb->createPositionalParameter(0) .
            ' and c.portal_viewable = ' . $casesQb->createPositionalParameter(1)
        )->innerJoin(
            'c',
            'contacts_cases',
            'cc',
            'c.id = cc.case_id and cc.contact_id = ' . $casesQb->createPositionalParameter($contactId) .
            ' and cc.deleted = ' . $casesQb->createPositionalParameter(0)
        );

        return $casesQb;
    }

    protected function addVisibilityQueryForContactWithAccounts(\SugarQuery $query)
    {
        // get note ids attached to the Accounts' Cases, that are visible
        $casesQb = $this->db->getConnection()->createQueryBuilder();
        $casesQb->select(['n.id']);
        $casesQb->from('notes', 'n');
        $casesQb->innerJoin(
            'n',
            'cases',
            'c',
            'c.id = n.parent_id and n.parent_type = ' . $casesQb->createPositionalParameter('Cases') .
            ' and n.deleted = ' . $casesQb->createPositionalParameter(0) .
            ' and c.deleted = ' . $casesQb->createPositionalParameter(0) .
            ' and c.portal_viewable = ' . $casesQb->createPositionalParameter(1)
        );

        $accountsOfContact = PortalFactory::getInstance('Session')->getAccountIds();
        if (safeCount($accountsOfContact) > 1) {
            $casesQb->where(
                $casesQb->expr()->in(
                    'c.account_id',
                    $casesQb->createPositionalParameter((array)$accountsOfContact, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
                )
            );
        } else {
            $casesQb->where('c.account_id = ' . $casesQb->createPositionalParameter($accountsOfContact['0']));
        }

        return $casesQb;
    }

    protected function addVisibilityQueryForContactWithoutAccounts(\SugarQuery $query)
    {
        // retrieve only notes directly related to the contact
        $casesQb = $this->db->getConnection()->createQueryBuilder();
        $casesQb->select(['n.id']);
        $casesQb->from('notes', 'n');
        $casesQb->where('n.contact_id = ' . $casesQb->createPositionalParameter(PortalFactory::getInstance('Session')->getContactId()));

        return $casesQb;
    }

    protected function addVisibilityOrQueryBugs(array $options)
    {
        if ($this->isModuleAccessible('Bugs') && $this->validateOrQuery($this->visibilityQueryOr)) {
            // common logic between Contacts with and without Accounts

            // get note ids attached to Bugs that are visible
            $bugsQb = $this->db->getConnection()->createQueryBuilder();
            $bugsQb->select(['n.id']);
            $bugsQb->from('notes', 'n');
            $bugsQb->innerJoin(
                'n',
                'bugs',
                'b',
                'b.id = n.parent_id and n.parent_type = ' . $bugsQb->createPositionalParameter('Bugs') .
                ' and n.deleted = ' . $bugsQb->createPositionalParameter(0) .
                ' and b.deleted = ' . $bugsQb->createPositionalParameter(0) .
                ' and b.portal_viewable = ' . $bugsQb->createPositionalParameter(1)
            );

            $this->visibilityQueryOr->in($options['table_alias'] . '.id', $bugsQb);

            // for attachments
            $this->visibilityQueryOr->in($options['table_alias'] . '.note_parent_id', $bugsQb);
        }
    }

    protected function addVisibilityOrQueryKBContents(array $options)
    {
        if ($this->isModuleAccessible('KBContents') && $this->validateOrQuery($this->visibilityQueryOr)) {
            // get note ids attached to kb
            // this might be a little too loose, as it does not check for published kbs, but it checks that the note is marked as visible
            $kbQb = $this->db->getConnection()->createQueryBuilder();
            $kbQb->select(['n.id']);
            $kbQb->from('notes', 'n');
            $kbQb->where(
                $kbQb->expr()->or(
                    'n.parent_type = ' . $kbQb->createPositionalParameter('KBContents'),
                    'n.parent_type = ' . $kbQb->createPositionalParameter('KBContentsAttachments')
                )
            );

            $this->visibilityQueryOr->in($options['table_alias'] . '.id', $kbQb);
        }
    }

    protected function addVisibilityOrQueryAttachments($options)
    {
        $attachmentsQb = $this->db->getConnection()->createQueryBuilder();
        $attachmentsQb->select(['n.id']);
        $attachmentsQb->from('notes', 'n');
        $attachmentsQb->where(
            'n.contact_id = ' . $attachmentsQb->createPositionalParameter(
                PortalFactory::getInstance('Session')->getContactId()
            )
        );
        $this->visibilityQueryOr->in($options['table_alias'] . '.id', $attachmentsQb);
    }

    protected function validateOrQuery($queryOr)
    {
        return ($queryOr instanceof \SugarQuery_Builder_Where);
    }

    protected function isModuleAccessible($module)
    {
        return \ACLAction::getUserAccessLevel($GLOBALS['current_user']->id, $module, 'access') >= 0;
    }
}
