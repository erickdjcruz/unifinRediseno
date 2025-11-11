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


class DocumentsApiHelper extends SugarBeanApiHelper
{
    /**
     * Formats the bean so it is ready to be handed back to the API's client. Certian fields will get extra processing
     * to make them easier to work with from the client end.
     *
     * @param $bean SugarBean The bean you want formatted
     * @param $fieldList array Which fields do you want formatted and returned (leave blank for all fields)
     * @param $options array Currently no options are supported
     * @return array The bean in array format, ready for passing out the API to clients.
     */
    public function formatForApi(SugarBean $bean, array $fieldList = [], array $options = [])
    {
        // Set the revision id so that additional fields like filename get picked up.
        if (!empty($fieldList)) {
            $document_revision_id = $this->getDocumentRevisionId($bean->table_name, $bean->id);
            if (false !== $document_revision_id) {
                // Set the revision and setup everything else for a document that
                // depends on a revision id, like filename, revision, etc
                $bean->document_revision_id = $document_revision_id;
                $bean->fill_in_additional_detail_fields();
            }
        }

        return parent::formatForApi($bean, $fieldList, $options);
    }

    /**
     * @param string $tableName
     * @param string $id
     * @return false|mixed
     * @throws \Doctrine\DBAL\Exception
     */
    protected function getDocumentRevisionId(string $tableName, string $id)
    {
        $db = DBManagerFactory::getInstance();
        $connection = $db->getConnection();
        $tableNameQuoted = $db->getValidDBName($tableName, false, 'table');
        $sql = "SELECT document_revision_id FROM {$tableNameQuoted} WHERE id = ?";
        return $connection->fetchOne($sql, [$id]);
    }
}
