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

use Sugarcrm\Sugarcrm\Util\Uuid;

/**
 * Migrates data in DocuSign package, previously in WSYS_docusign_status and WSYS_docusign_notes
 */
class SugarUpgradeMigrateDocuSignMLPData extends UpgradeScript
{
    public $order = 9100;
    public $type = self::UPGRADE_DB;

    protected $envelopeFieldsMapping = [
        'email_subject' => 'name',
        'envelope_id' => 'envelope_id',
        'docusign_envelope_status' => 'status',
        'sugar_document_id' => 'document_id',
        'date_entered' => 'date_entered',
        'date_modified' => 'date_modified',
        'created_by' => 'created_by',
        'modified_user_id' => 'modified_user_id',
        'parent_id' => 'parent_id',
        'parent_type' => 'parent_type',
        'last_audit' => 'last_audit',
        'team_id' => 'team_id',
        'team_set_id' => 'team_set_id',
        'acl_team_set_id' => 'acl_team_set_id',
        'assigned_user_id' => 'assigned_user_id',
    ];

    protected $commentLogFieldsMapping = [
        'description' => 'entry',
        'date_entered' => 'date_entered',
        'date_modified' => 'date_modified',
        'modified_user_id' => 'modified_user_id',
        'created_by' => 'created_by',
    ];

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        if ($this->shouldRun()) {
            $this->log('Migrating DocuSign MLP data');

            $fieldsMapping = $this->updateAvailableFields($this->envelopeFieldsMapping, 'wsys_docusign_status');
            $this->executeMigration($fieldsMapping);

            $this->log('Done migrating DocuSign');
        } else {
            $this->log('Skipping migrating DocuSign');
        }
    }

    /**
     * Determines if this upgrader should run
     *
     * @return bool true if the upgrader should run
     */
    protected function shouldRun()
    {
        global $db;

        if (version_compare($this->from_version, '12.0.0', '>=')) {
            return false;
        }

        return $db->tableExists('wsys_docusign_status');
    }

    /**
     * Update available fields
     *
     * In case of old package being installed, make sure to skip newer fields
     *
     * @param array $fields
     * @param String $table
     * @return array
     */
    protected function updateAvailableFields($fields, $table): array
    {
        global $db;

        $cols = $db->get_columns($table);
        $colsNames = array_keys($cols);

        $newFields = [];
        foreach ($fields as $oldFieldName => $newFieldName) {
            if (in_array($oldFieldName, $colsNames)) {
                $newFields[$oldFieldName] = $newFieldName;
            }
        }
        return $newFields;
    }

    /**
     * Migrates data from old DocuSign modules
     *
     * @param array $mapping
     * @throws Doctrine\DBAL\Exception
     */
    protected function executeMigration($mapping)
    {
        global $db;

        // Get envelopes
        $qb = $db->getConnection()->createQueryBuilder();

        $selectFields = array_keys($mapping);
        array_unshift($selectFields, 'id');

        $qb->select($selectFields);
        $qb->from('wsys_docusign_status');
        $qb->where($qb->expr()->eq('deleted', 0));
        $result = $qb->execute();

        // Copy the values
        while ($row = $result->fetchAssociative()) {
            $qb = $db->getConnection()->createQueryBuilder();

            $qb->insert('docusign_envelopes');

            $values = [];
            foreach ($mapping as $oldFieldName => $newFieldName) {
                if (!is_null($row[$oldFieldName])) {
                    $values[$newFieldName] = $db->quoted($row[$oldFieldName]);
                }
            }
            if (!empty($values)) {
                $values['id'] = $db->quoted(Uuid::uuid4());
                $qb->values($values);
                $qb->execute();

                $this->migrateComments($row['id'], $values['id']);
            }
        }
    }

    /**
     * Migrates data from old WSYS_docusign_notes to Comments
     *
     * @param String $oldSugarEnvelopeId
     * @param String $newSugarEnvelopeId
     * @throws Doctrine\DBAL\Exception
     */
    protected function migrateComments($oldSugarEnvelopeId, $newSugarEnvelopeId)
    {
        global $db;

        // Get comments
        $qb = $db->getConnection()->createQueryBuilder();

        $selectFields = ['n.description', 'n.date_entered', 'n.date_modified', 'n.modified_user_id', 'created_by',
            'team_id', 'team_set_id', 'assigned_user_id'];

        $qb->select($selectFields);
        $qb->from('docusign_status_notes_c', 'sn');
        $qb->join('sn', 'wsys_docusign_notes', 'n', 'sn.docusign_status_notesnotes_idb = n.id');

        $andWhere = $qb->expr()->and(
            $qb->expr()->eq('sn.docusign_status_notes_docusign_status_ida', $db->quoted($oldSugarEnvelopeId)),
            $qb->expr()->eq('sn.deleted', 0),
            $qb->expr()->eq('n.deleted', 0),
        );
        $qb->where($andWhere);
        $result = $qb->execute();

        // Copy the values
        while ($row = $result->fetchAssociative()) {
            $qb = $db->getConnection()->createQueryBuilder();

            $qb->insert('commentlog');

            $values = [];
            foreach ($this->commentLogFieldsMapping as $noteFieldName => $commentFieldName) {
                if (!is_null($row[$noteFieldName])) {
                    $values[$commentFieldName] = $db->quoted($row[$noteFieldName]);
                }
            }
            if (!empty($values)) {
                $values['id'] = $db->quoted(Uuid::uuid4());
                $qb->values($values);
                $qb->execute();

                $this->linkCommentToEnvelope($values['id'], $newSugarEnvelopeId);
            }
        }
    }

    /**
     * Create relation between commentlog and envelope record
     *
     * @param String $commentLogId
     * @param String $newSugarEnvelopeId
     * @throws Doctrine\DBAL\Exception
     */
    protected function linkCommentToEnvelope($commentLogId, $newSugarEnvelopeId)
    {
        global $db;

        $qb = $db->getConnection()->createQueryBuilder();

        $qb->insert('commentlog_rel');

        $values = [];
        $values['id'] = $db->quoted(Uuid::uuid4());
        $values['record_id'] = $newSugarEnvelopeId;
        $values['commentlog_id'] = $commentLogId;
        $values['module'] = $db->quoted('DocuSignEnvelopes');
        $qb->values($values);

        $qb->execute();
    }
}
