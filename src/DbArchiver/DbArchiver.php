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

namespace Sugarcrm\Sugarcrm\DbArchiver;

use BeanFactory;
use DataArchiver;
use Doctrine\DBAL\Connection;
use Email;
use Document;
use RuntimeException;
use Sugarcrm\Sugarcrm\Elasticsearch\Container;
use Sugarcrm\Sugarcrm\SearchEngine\Engine\Elastic;
use Sugarcrm\Sugarcrm\SearchEngine\SearchEngine;

/**
 * Class DbArchiver
 */
class DbArchiver
{
    /**
     * Archive Limit value
     */
    private const ARCHIVE_LIMIT = 10000;

    /**
     * Size of chunks for internal operations, like attached files or related records removal
     */
    private const ID_CHUNK_SIZE = 100;

    /**
     * @var string
     */
    private $module;

    /**
     * @var SugarBean
     */
    private $bean;

    /**
     * @var string
     */
    private $cstmArchiveTableName;

    /**
     * DbArchiver constructor.
     * @param $module
     */

    /**
     * Array of ids that completed the archival process
     * @var array
     */
    private $rowsArchived = [];

    /**
     * Array of ids that completed the archival process for custom tables
     * @var array
     */
    private $cstmRowsArchived = [];

    /**
     * @var Connection
     */
    private $conn;

    public function __construct(string $module)
    {
        // set the active module for this instance of DbArchiver
        $this->module = $module;
    }

    /**
     * Returns the active table SugarBean
     * @return \SugarBean
     * @throws RuntimeException
     */
    public function getBean(): ?\SugarBean
    {
        if (is_null($this->bean)) {
            $bean = \BeanFactory::newBean($this->module);
            if (is_null($bean)) {
                throw new RuntimeException('Could not load bean from module: ' . $this->module);
            }
            $this->bean = $bean;
        }
        return $this->bean;
    }

    /**
     * Returns the module name
     * @return string
     */
    public function getModule(): ?string
    {
        return $this->module;
    }

    /**
     * Returns whether the module has a custom table associated with it or not
     * @param $bean
     * @return bool
     * @throws RuntimeException
     */
    private function hasCustomTable($bean = null)
    {
        if (is_null($bean)) {
            $bean = $this->getBean();
        }
        return $bean->hasCustomFields();
    }

    /**
     * Returns whether there module has an audit table associated with it or not
     * @param $bean
     * @return bool
     * @throws RuntimeException
     */
    private function hasAuditTable($bean = null)
    {
        if (is_null($bean)) {
            $bean = $this->getBean();
        }
        return $bean->is_AuditEnabled() && $bean->db->tableExists($bean->get_audit_table_name());
    }

    /**
     * Creates the archive table based on the active table
     * @param $bean
     * @return bool
     * @throws RuntimeException
     */
    public function createArchiveTable($bean = null): bool
    {
        $indices = [];
        if (is_null($bean)) {
            $bean = $this->getBean();
        }

        $archiveTable = $bean->getArchiveTableName();

        $archiveBean = clone $bean;

        // Create new archive table with only the id index. Remove all auto-increment fields
        $fieldDefs = $bean->getFieldDefinitions();
        foreach ($fieldDefs as $key => $fieldDef) {
            if (isset($fieldDefs[$key]['auto_increment'])) {
                $fieldDefs[$key]['auto_increment'] = false;
            }
        }

        $indices['id'] = $bean->getIndices()['id'];
        $indices['id']['name'] = $indices['id']['name'] . '_archive';

        // If the table has not yet been created, create it
        if (!$bean->db->tableExists($archiveTable)) {
            // Create the archive table
            $archiveBean->db->createTableParams($archiveTable, $fieldDefs, $indices);
        } else {
            $archiveBean->db->repairTableParams($archiveTable, $fieldDefs, $indices);
        }

        // Additional logic to deal with the possibility of a cstm table having been created
        if ($this->hasCustomTable($bean)) {
            $bean2 = clone $bean;
            // By changing the object name, we no longer create indices through checking globals in bean->getIndices
            $bean2->object_name = $bean2->getObjectName() . '_archive';
            $bean2->table_name = $bean2->get_custom_table_name();
            $this->cstmArchiveTableName = $bean2->getArchiveTableName();

            // Default cstmFieldDef for all custom tables
            $cstmFieldDefs = [
                'id_c' => [
                    'name' => 'id_c',
                    'type' => 'id',
                    'required' => 1,
                ],
            ];

            // Add each fieldDef to the cstmFieldDef array
            $cstmFieldsOnBean = $bean2->getFieldDefinitions('source', ['custom_fields']);
            foreach ($cstmFieldsOnBean as $field => $def) {
                unset($def['source']);
                $cstmFieldDefs[$field] = $def;
            }

            // Default indices array
            $indices = [
                [
                    'name' => $this->cstmArchiveTableName . '_pk',
                    'type' => 'primary',
                    'fields' => ['id_c'],
                ],
            ];

            // If the table has not yet been created, create it
            if (!$bean2->db->tableExists($this->cstmArchiveTableName)) {
                // Create the new custom archive table
                $bean2->db->createTableParams($this->cstmArchiveTableName, $cstmFieldDefs, $indices);
            } else {
                $bean2->db->repairTableParams($this->cstmArchiveTableName, $cstmFieldDefs, $indices);
            }
        }
        return true;
    }

    /**
     * Performs the given data manipulation process (Archive and Delete or Only Delete)
     * Also handles the special case where we attempt an archive of the pmse_bpmInbox table. This table requires a
     * special cascading process that is unique to it.
     * @param Where $where
     * @param string $type Either archive  or delete
     * @return array array of ids that were processed
     * @throws RuntimeException
     * @throws \SugarQueryException
     */
    public function performProcess($where, $type = DataArchiver::PROCESS_TYPE_ARCHIVE)
    {
        // Return the results of a query to the database using the given where clause object
        $resultsArray = $this->getTableResults($where);
        $results = $resultsArray[0];
        $cstmResults = $resultsArray[1];

        // create an array of ids
        $ids = array_column($results, 'id');
        $cstmIds = array_column($cstmResults, 'id_c');

        // Custom logic needed when dealing with pmse_bpmInbox table
        $casIds = array_column($results, 'cas_id');
        if (safeCount($casIds) === 0) {
            unset($casIds);
        }

        if (empty($ids)) {
            return [];
        }

        // Get connection for DB in order to instantiate QueryBuilders
        $this->conn = \DBManagerFactory::getConnection();

        // Call this method in case the archive table hasnt been created yet
        if ($type === DataArchiver::PROCESS_TYPE_ARCHIVE) {
            $this->createArchiveTable();
            $this->archive($results, $cstmResults);
            if (isset($casIds) && safeCount($casIds) > 0) {
                $this->cascadeBpmProcess($casIds, $type);
            }
        }

        if ($this->getBean()->getModuleName() == 'Notes') {
            foreach ($this->getNotesAttachments($ids) as $row) {
                $ids[] = $row['id'];
            }
            //Delete files before DB rows, otherwise some file names will be unavailable
            $this->deleteFiles($ids);
        }

        // Emails require some extra cleanup
        if ($this->getBean() instanceof Email && $type === DataArchiver::PROCESS_TYPE_DELETE) {
            $this->cleanupEmails($ids);
        }

        if ($this->getBean() instanceof Document && $type === DataArchiver::PROCESS_TYPE_DELETE) {
            $this->cleanupDocuments($ids);
        }

        // Deletion always occurs
        $this->delete($ids);

        // Do cascading bpm deletion if this is from the bpm inbox table
        if (isset($casIds) && safeCount($casIds) > 0) {
            $this->cascadeBpmProcess($casIds, DataArchiver::PROCESS_TYPE_DELETE);
        }

        // Delete from custom table if there is one
        if ($this->hasCustomTable()) {
            $this->delete($cstmIds, $this->getBean()->get_custom_table_name(), 'id_c');
        }

        // Delete relationships if hard delete, otherwise, leave them alone
        // Only delete relationships if we are not working with the bpm inbox table
        if ($type === DataArchiver::PROCESS_TYPE_DELETE && !isset($casIds)) {
            $this->deleteRelationships($ids);

            // Delete audit table entries if hard delete, otherwise, leave them alone
            if ($this->hasAuditTable()) {
                $this->delete($ids, $this->getBean()->get_audit_table_name(), 'parent_id');
            }
            // Hard delete process with the bpm inbox table
        } elseif ($type === DataArchiver::PROCESS_TYPE_DELETE && isset($casIds) && safeCount($casIds) > 0) {
            $this->cascadeBpmProcess($casIds, $type);
        }

        $engine = SearchEngine::getInstance()->getEngine();
        if ($engine instanceof Elastic) {
            $indexer = $engine->getContainer()->indexer;
            $indexer->finishBatch();
            if (!empty($ids)) {
                $index = $engine->getContainer()->indexPool->getWriteIndex($this->getBean()->getModuleName());
                foreach ($ids as $id) {
                    if (!empty($id)) {
                        $index->deleteById($id);
                    }
                }
            }
        }

        return $ids;
    }

    /**
     * Runs the archiving process
     * @param $rows
     * @param $cstmRows
     * @param $bean
     * @throws RuntimeException
     */
    private function archive($rows, $cstmRows, $bean = null)
    {
        if (is_null($bean)) {
            $bean = $this->getBean();
        }
        // NOTE: This function can be potentially optimized in the future to use 1 SQL statement. This would require
        // changing functionality in QueryBuilder. Specifically, it would require allowing multiple values arrays
        // to be added.

        // Creating the builder objects each iteration because there is no way to reset the parameters that are on
        // each object without the original library being altered.
        // Instantiate QueryBuilder for the insertion into archive table
        $builder = $this->conn->createQueryBuilder();
        $qbArchive = $builder
            ->insert($bean->getArchiveTableName());

        $builder2 = null;
        $qbArchiveCstm = null;
        if ($this->hasCustomTable($bean)) {
            $builder2 = $this->conn->createQueryBuilder();
            $qbArchiveCstm = $builder2
                ->insert($this->cstmArchiveTableName);
        }

        for ($i = 0, $m = safeCount($rows), $cm = safeCount($cstmRows); $i < $m; $i++) {
            $qbArchive
                ->values(
                    array_map(function ($value) use ($builder) {
                        return $builder->createPositionalParameter($value);
                    }, $rows[$i])
                );

            // If the active table has a custom table associated with it, querybuilders need to be set up in the same
            // manner as above
            if ($this->hasCustomTable($bean) && $i < $cm) {
                $qbArchiveCstm
                    ->values(
                        array_map(function ($value) use ($builder2) {
                            return $builder2->createPositionalParameter($value);
                        }, $cstmRows[$i])
                    );
            }

            // Execute archiving SQL statement
            $qbArchive->execute();

            // Store what we have archived so we can undo it if there is an error
            if (!key_exists($bean->getArchiveTableName(), $this->rowsArchived)) {
                $this->rowsArchived[$bean->getArchiveTableName()] = [];
            }
            array_push($this->rowsArchived[$bean->getArchiveTableName()], $rows[$i]['id']);

            // Clear parameters for next iteration
            $qbArchive->setParameters([]);

            // Execute archiving and deletion SQL statements for potential custom table
            if ($this->hasCustomTable($bean) && $i < $cm) {
                $qbArchiveCstm->execute();
                if (!key_exists($bean->get_custom_table_name(), $this->cstmRowsArchived)) {
                    $this->cstmRowsArchived[$bean->get_custom_table_name()] = [];
                }
                array_push($this->cstmRowsArchived[$bean->get_custom_table_name()], $rows[$i]['id']);
                $qbArchiveCstm->setParameters([]);
            }
        }
    }

    /**
     * Runs the deletion process
     * @param array $ids list of ids to delete
     * @param null|string $table The table to delete from
     * @param string $id_name column id name (i.e. 'id', or 'id_c', or 'contact_id'
     * @param bool $delFromCustom Whether or not this table should look for a custom table and delete from it also
     * @throws RuntimeException
     * @throws Doctrine\DBAL\Exception
     */
    private function delete(array $allIds, string $table = null, string $id_name = 'id')
    {
        // Grab table name to use in queries
        if (is_null($table)) {
            $table = $this->getBean()->getTableName();
        }

        // Single query to delete all ids passed
        $builder = $this->conn->createQueryBuilder();

        foreach (array_chunk($allIds, static::ID_CHUNK_SIZE) as $ids) {
            $builder->delete($table)
                ->where($builder->expr()->in($id_name, ':ids'))
                ->setParameter('ids', $ids, Connection::PARAM_STR_ARRAY);

            // Execute query builder
            $builder->execute();
        }

        $this->getBean()->db->optimizeTable($table);
    }

    /**
     * Deletes all relationships associated with a specific hard deleted row from active table
     * @param $ids
     * @throws RuntimeException
     */
    private function deleteRelationships($ids)
    {
        $curTable = $this->getBean()->getTableName();
        // Grab the linked fields from the bean
        $bean = $this->getBean();
        $linked_fields = $bean->get_linked_fields();

        // Loop through each field, determine if there is an associated table and remove the row from that table
        foreach ($linked_fields as $name => $value) {
            if ($bean->load_relationship($name)) {
                // Its possible no relationship data exists, therefore it will never need to be worried about for this
                // process
                if ($bean->$name->getRelationshipObject() === null) {
                    continue;
                }

                // Grab the relationship table associated with the linked_field
                $rel_table = $bean->$name->getRelationshipObject()->getRelationshipTable();

                // We only care about relationships that are M2M and create active relationship tables in the db
                // This ensures that only relationship tables that make sense to delete are deleted.
                // For instance, we do not want to delete the row in cases table where an account may be references
                // because deleting an account should not mean that we lose all data ever associated with it.
                // We also dont care about relationships in the active table being hard deleted from since we are
                // removing the entire row anyway.
                // We only want to remove from the primary relationship tables that have the naming convention of
                // accounts_contacts, etc.
                if (!$bean->$name->getRelationshipObject() instanceof \M2MRelationship ||
                    !$this->getBean()->db->tableExists($rel_table) || $rel_table == $curTable) {
                    continue;
                }

                // Grab the 'side' of the relationship table that the table being hard deleted from is associed with
                $side = $bean->$name->getSide();

                // Grab the id label name associated with the list of ids we are working with as it corresponds to the
                // relationship table
                $id_name = $side === 'LHS' ? $bean->$name->relationship->def['join_key_lhs'] ?? null :
                    $bean->$name->getRelationshipObject()->def['join_key_rhs'] ?? null;

                // For certain relationships this will not exist, and thus we dont want to attempt to delete, as it will
                // throw an error
                if ($id_name === null) {
                    continue;
                }

                // Delete from the relationship table where the specific ids are present
                $this->delete($ids, $rel_table, $id_name);
            }
        }
    }

    /**
     * Helper function when Hard Deleting from the pmse_Inbox table. This allows for cascading deletion to occur for
     * tables that are affect by pmse_Inbox
     * @param array $casIds
     * @param string $type
     */
    private function cascadeBpmProcess(array $casIds, string $type): void
    {
        $flowModule = 'pmse_BpmFlow';
        $flowTable = 'pmse_bpm_flow';
        $threadModule = 'pmse_BpmThread';
        $threadTable = 'pmse_bpm_thread';
        if ($type === DataArchiver::PROCESS_TYPE_DELETE) {
            // Get pmse_bpmFlow table and delete rows corresponding to casID
            $this->delete($casIds, $flowTable, 'cas_id');

            // Get pmse_bpmThread table and delete rows corresponding to casID
            $this->delete($casIds, $threadTable, 'cas_id');
        } elseif ($type === DataArchiver::PROCESS_TYPE_ARCHIVE) {
            $flowBean = \BeanFactory::newBean($flowModule);
            $threadBean = \BeanFactory::newBean($threadModule);
            $this->createArchiveTable($flowBean);
            $this->createArchiveTable($threadBean);

            // Create special filter that can be converted to a where clause for the archival process
            $filterApi = new \DataArchiverFilterApi();
            $cas_filter = array_map(function ($id) {
                return [
                    'cas_id' => [
                        '$equals' => $id,
                    ],
                ];
            }, $casIds);
            $cas_filter_where_flow = $filterApi->convertFiltersToWhere($cas_filter, $flowBean->getModuleName());
            $cas_filter_where_thread = $filterApi->convertFiltersToWhere($cas_filter, $threadBean->getModuleName());

            // Need to ensure we get table results using an OR Where clause instead of the default AND
            $cascadeRowsFlow = $this->getTableResults($cas_filter_where_flow, $flowBean, true);
            $cascadeRowsThread = $this->getTableResults($cas_filter_where_thread, $threadBean, true);

            $this->archive($cascadeRowsFlow[0], $cascadeRowsFlow[1], $flowBean);
            $this->archive($cascadeRowsThread[0], $cascadeRowsThread[1], $threadBean);
        }
    }

    /**
     * Removes the given rows from the archive table. Psuedo transaction engine
     * @throws RuntimeException
     */
    public function removeArchivedRows()
    {
        $archivedTables = $this->getRowsArchived();
        $archivedCustomTables = $this->getCstmRowsArchived();

        foreach ($archivedTables as $archiveTable => $ids) {
            if (safeCount($ids) > 0) {
                $this->delete($ids, $archiveTable, 'id');
            }
        }

        foreach ($archivedCustomTables as $archiveCustomTable => $ids) {
            if (safeCount($ids) > 0) {
                $this->delete($ids, $archiveCustomTable, 'id_c');
            }
        }
    }

    /**
     * Returns the ids of the rows that were successfully archived
     * @return array
     */
    private function getRowsArchived()
    {
        return $this->rowsArchived;
    }

    /**
     * Returns the ids of the rows that were successfully archived from custom table
     * @return array
     */
    private function getCstmRowsArchived()
    {
        return $this->cstmRowsArchived;
    }

    /**
     * Returns the Database rows that need to be archived for the active table
     * @param $where the where clause that defines the filter definitons
     * @param $bean
     * @param bool $or
     * @return array an array of rows from the database table
     * @throws \SugarQueryException|RuntimeException
     */
    private function getTableResults($where, $bean = null, $or = false)
    {
        if (is_null($bean)) {
            $bean = $this->getBean();
        }
        $allFieldDefs = $bean->getFieldDefinitions();
        $cstmFieldDefs = $bean->getFieldDefinitions('source', ['custom_fields']);
        $dbFieldDefs = array_filter($allFieldDefs, function ($field) use ($cstmFieldDefs) {
            return !key_exists('source', $field) && !in_array($field, $cstmFieldDefs);
        });

        $dbFields = array_keys($dbFieldDefs);

        $sq = new \SugarQuery();
        $sq->select($dbFields);
        $sq->from($bean, ['add_deleted' => false]);
        foreach ($where->conditions as $condition) {
            if ($or) {
                $sq->orWhere($condition);
            } else {
                $sq->where($condition);
            }
        }
        $sq->limit(self::ARCHIVE_LIMIT);

        $filter = array_flip($dbFields);

        $results = array_map(function ($row) use ($filter) {
            return array_intersect_key($row, $filter);
        }, $sq->execute());

        // If this table has a custom table associated with it, grab the rows from that custom table as well
        $cstmResults = [];
        if ($this->hasCustomTable($bean)) {
            $cstmResults = $this->getCstmTableResults($results, $bean);
        }

        // Return a results array used to create queries
        return [$results, $cstmResults];
    }

    /**
     * Returns the Database fields needed to be archived for the custom table
     * @param $rows
     * @param $bean
     * @return array
     * @throws RuntimeException
     */
    private function getCstmTableResults($rows, $bean = null)
    {
        if (is_null($bean)) {
            $bean = $this->getBean();
        }

        $ids = array_map(function ($row) {
            return $row['id'];
        }, $rows);
        $fields = ['id_c'];
        $customFields = array_keys($bean->getFieldDefinitions('source', ['custom_fields']));
        $fields = array_merge($fields, $customFields);
        $table = $bean->get_custom_table_name();

        // Get connection for DB in order to instantiate QueryBuilders
        $conn = \DBManagerFactory::getConnection();

        // Custom table query
        $builder = $conn->createQueryBuilder();
        $builder
            ->select($fields)
            ->from($table)
            ->where($builder->expr()->in('id_c', ':ids'))
            ->setParameter('ids', $ids, Connection::PARAM_STR_ARRAY);

        return $builder->execute()->fetchAllAssociative();
    }

    /**
     * Used to archive an individual bean
     * @throws \SugarQueryException|RuntimeException
     */
    public function archiveBean($id)
    {
        // Generate where clause and pass to archive functionality
        $q = new \SugarQuery();
        $w = $q->where()->equals('id', $id);
        $this->performProcess($w);
    }

    private function getNotesAttachments($ids)
    {
        $attachmentsQuery = new \SugarQuery();
        $attachmentsQuery->select(['id']);
        $attachmentsQuery->from($this->getBean());
        $attachmentsQuery->where()->in('note_parent_id', $ids);
        return $attachmentsQuery->execute();
    }

    private function deleteFiles($ids)
    {
        foreach ($ids as $file) {
            \UploadFile::unlink_file($file);
        }
    }

    private function cleanupEmails(array $allEmailIds): void
    {
        foreach (array_chunk($allEmailIds, static::ID_CHUNK_SIZE) as $emailIds) {
            // Remove linked Notes and Notes files
            $this->cleanupEmailNotes($emailIds);

            // Remove all related embedded_filed
            $this->cleanupEmailTextEmbeddedFiles($emailIds);

            // Remove attached email_text records
            $this->cleanupEmailText($emailIds);
        }
    }

    private function cleanupEmailNotes(array $emailIds): void
    {
        $notesTable = BeanFactory::getBean('Notes')->getTableName();
        $builder = $this->conn->createQueryBuilder();
        $builder->select('id')
            ->from($notesTable)
            ->where($builder->expr()->in('email_id', ':ids'))
            ->setParameter('ids', $emailIds, Connection::PARAM_STR_ARRAY);
        $fileIds = $builder->executeQuery()->fetchFirstColumn();

        $notesTable = BeanFactory::getBean('Notes')->getTableName();
        $builder = $this->conn->createQueryBuilder();
        $builder->delete($notesTable)
            ->where($builder->expr()->in('email_id', ':ids'))
            ->setParameter('ids', $emailIds, Connection::PARAM_STR_ARRAY);
        $builder->executeStatement();
        $this->getBean()->db->optimizeTable($notesTable);

        $this->deleteFiles($fileIds);
    }

    private function cleanupEmailTextEmbeddedFiles(array $emailIds): void
    {
        /** @var Email $emailBean */
        $emailBean = \BeanFactory::newBean('Emails');
        $emailTextTable = BeanFactory::getBean('EmailText')->getTableName();
        $embeddedFilesTable = BeanFactory::getBean('EmbeddedFiles')->getTableName();
        $builder = $this->conn->createQueryBuilder();
        $builder->select('description_html')
            ->from($emailTextTable)
            ->where($builder->expr()->in('email_id', ':ids'))
            ->setParameter('ids', $emailIds, Connection::PARAM_STR_ARRAY);
        $results = $builder->executeQuery()->fetchFirstColumn();

        $embeddedFileIds = [];
        $pattern = '~EmbeddedFiles/([a-zA-Z0-9-]+)/file/description_html_file~';
        foreach ($results as $emailText) {
            if (!is_string($emailText)) {
                continue;
            }
            if ($emailBean->supportsGzip()) {
                $emailText = $emailBean->ungzipContent($emailText);
            }
            if (!preg_match($pattern, $emailText, $matches)) {
                continue;
            }
            $embeddedFileIds[] = $matches[1];
        }

        $this->deleteFiles($embeddedFileIds);

        $builder = $this->conn->createQueryBuilder();
        $builder->delete($embeddedFilesTable)
            ->where($builder->expr()->in('id', ':ids'))
            ->setParameter('ids', $embeddedFileIds, Connection::PARAM_STR_ARRAY);
        $builder->executeStatement();
        $this->getBean()->db->optimizeTable($embeddedFilesTable);
    }

    private function cleanupEmailText(array $emailIds): void
    {
        $emailTextTable = BeanFactory::getBean('EmailText')->getTableName();
        $builder = $this->conn->createQueryBuilder();
        $builder->delete($emailTextTable)
            ->where($builder->expr()->in('email_id', ':ids'))
            ->setParameter('ids', $emailIds, Connection::PARAM_STR_ARRAY);
        $builder->executeStatement();
        $this->getBean()->db->optimizeTable($emailTextTable);
    }

    /**
     * @param array $documentIds
     * @return void
     */
    protected function cleanupDocuments(array $documentIds): void
    {
        foreach (array_chunk($documentIds, static::ID_CHUNK_SIZE) as $idsChunk) {
            $table = BeanFactory::getBean('DocumentRevisions')->getTableName();
            $qb = $this->conn->createQueryBuilder();
            $qb->select('id')
                ->from($table)
                ->where($qb->expr()->in('document_id', ':ids'))
                ->setParameter('ids', $idsChunk, Connection::PARAM_STR_ARRAY);
            $revisionIds = $qb->fetchFirstColumn();

            $qb = $this->conn->createQueryBuilder();
            $qb->delete($table)
                ->where($qb->expr()->in('document_id', ':ids'))
                ->setParameter('ids', $idsChunk, Connection::PARAM_STR_ARRAY)
                ->executeStatement();

            $this->deleteFiles($revisionIds);
        }
    }
}
