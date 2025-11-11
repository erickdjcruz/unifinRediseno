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

class Escalation extends SugarBean
{
    public $object_name = 'Escalation';
    public $table_name = 'escalations';
    public $module_dir = 'Escalations';
    public $module_name = 'Escalations';
    public $importable = true;

    public $escalation_number;
    public $name;
    public $description;
    public $status;
    public $reason;
    public $source;
    public $parent_type;
    public $parent_name;
    public $parent_id;
    public $team_id;
    public $team_name;
    public $assigned_user_id;
    public $assigned_user_name;
    public $modified_user_id;
    public $modified_user_name;
    public $created_by;
    public $created_by_name;

    public $call_id;
    public $email_id;
    public $message_id;
    public $meeting_id;
    public $note_id;
    public $task_id;
    public $account_id;
    public $document_id;

    public $relationship_fields = [
        'call_id' => 'calls',
        'email_id' => 'emails',
        'contact_id' => 'contacts',
        'message_id' => 'messages',
        'meeting_id' => 'meetings',
        'note_id' => 'notes',
        'task_id' => 'tasks',
        'document_id' => 'documents',
        'accounts_id' => 'accounts',
    ];

    /**
     * @inheritDoc
     */
    public function bean_implements($interface): bool
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function save($check_notify = false)
    {
        $id = parent::save($check_notify);

        $this->handleParentEscalation();

        $parentBean = BeanFactory::retrieveBean($this->parent_type, $this->parent_id);
        $parentAccountId = '';
        // check if the parent record has an account related to it
        if (!empty($parentBean->account_id)) {
            $parentAccountId = $parentBean->account_id;
        } elseif ($this->parent_type == 'Accounts' && !empty($parentBean->parent_id)) {
            // For Accounts module parent id represents its parent accountId
            $parentAccountId = $parentBean->parent_id;
        }

        if (!empty($parentAccountId)) {
            $this->linkToAccountOfParent($parentAccountId, 'accounts');
        }

        return $id;
    }

    /**
     * {@inheritdoc}
     */
    public function mark_deleted($id)
    {
        parent::mark_deleted($id);

        $this->handleParentEscalation();
    }

    /**
     * Links the escalation record to its parent's account
     *
     * @param string $accountId id of the account to be related
     * @param string $linkName link name
     * @throws SugarApiExceptionNotFound
     */
    private function linkToAccountOfParent($accountId, $linkName)
    {
        // load the relationship, this is required to perform any operation on related data
        $this->load_relationship($linkName);

        if (isset($this->$linkName)) {
            // get array of all account Ids related to this escalation
            $accountIds = $this->$linkName->get();

            // if account is already related, don't do anything
            if (safeInArray($accountId, $accountIds)) {
                return;
            }

            // get bean to be related
            $relatedBean = BeanFactory::retrieveBean('Accounts', $accountId);

            if (!$relatedBean || $relatedBean->deleted) {
                throw new SugarApiExceptionNotFound('Could not find the related bean');
            }

            // link account to the escalation record
            $this->$linkName->add([$relatedBean]);

            //Clean up any hanging related records.
            SugarRelationship::resaveRelatedBeans();
        }
    }

    /**
     * Handle escalation change behavior for related parent
     *
     * @throws SugarQueryException
     */
    public function handleParentEscalation()
    {
        $parentTypeChanged = is_array($this->stateChanges) && array_key_exists('parent_type', $this->stateChanges);
        $parentIdChanged = is_array($this->stateChanges) && array_key_exists('parent_id', $this->stateChanges);

        if ($parentIdChanged) {
            if ($parentTypeChanged) {
                $parentType = $this->stateChanges['parent_type'];
            } else {
                $parentType = [
                    'before' => $this->parent_type,
                    'after' => $this->parent_type,
                ];
            }

            $parentId = $this->stateChanges['parent_id'];
            $this->setParentEscalation($parentType['before'], $parentId['before']);
            $this->setParentEscalation($parentType['after'], $parentId['after']);
        } else {
            $this->setParentEscalation($this->parent_type, $this->parent_id);
        }
    }

    /**
     * Set escalation properties on specified parent
     *
     * Requires the parent module to be using escalatable SugarObject
     * 'uses' => [
     *      'escalatable'
     * ]
     *
     * @param $parentType
     * @param $parentId
     * @throws SugarQueryException
     */
    public function setParentEscalation($parentType, $parentId)
    {
        if (empty($parentType) || empty($parentId)) {
            return;
        }

        $bean = BeanFactory::retrieveBean($parentType, $parentId);

        // the parent module must have is_escalated field available
        if (!$bean || !isset($bean->is_escalated)) {
            return;
        }

        $isEscalated = $this->isParentEscalated($parentId);

        // do nothing if no change in escalation
        if ($bean->is_escalated === $isEscalated) {
            return;
        }

        $bean->is_escalated = $isEscalated;
        $bean->save();
    }

    /**
     * Get non closed escalations for specified parent
     *
     * @param $parentId
     * @return array
     * @throws SugarQueryException
     */
    public function getNonClosedEscalationsForParent($parentId): array
    {
        $q = new SugarQuery();
        $q->from($this);
        $q->select(['id']);
        $q->where()->queryAnd()
            ->equals('parent_id', $parentId)
            ->notEquals('status', 'Closed');

        return $q->execute();
    }

    /**
     * Determine if parent is escalated
     *
     * @param $parentId
     * @return bool
     * @throws SugarQueryException
     */
    public function isParentEscalated($parentId): bool
    {
        $escalations = $this->getNonClosedEscalationsForParent($parentId);
        return safeCount($escalations) !== 0;
    }
}
