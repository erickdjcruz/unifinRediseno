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

use Sugarcrm\Sugarcrm\SearchEngine\SearchEngine;
use Sugarcrm\Sugarcrm\SearchEngine\MetaDataHelper;
use Sugarcrm\Sugarcrm\Elasticsearch\Provider\GlobalSearch\SearchFields;
use Sugarcrm\Sugarcrm\Elasticsearch\Provider\GlobalSearch\Handler\Implement\MultiFieldHandler;

class KBContentsApiHelper extends SugarBeanApiHelper
{
    public function formatForApi(SugarBean $bean, array $fieldList = [], array $options = [])
    {
        if ($this->api->action == 'view' && !empty($this->api->getRequest()->args['viewed'])) {
            $bean->viewcount = $bean->viewcount + 1;
            $query = sprintf(
                'UPDATE %s SET viewcount = %d WHERE id = %s',
                $bean->table_name,
                intval($bean->viewcount),
                $bean->db->quoted($bean->id)
            );
            $bean->db->query($query);
        }
        $result = parent::formatForApi($bean, $fieldList, $options);

        $query = new SugarQuery();
        $query->select(['language']);
        $query->distinct(true);
        $fromOptions = ['team_security' => false];
        $query->from(BeanFactory::newBean('KBContents'), $fromOptions);
        $query->where()
            ->equals('kbdocument_id', $bean->kbdocument_id);

        $langs = $query->execute();
        if ($langs) {
            $result['related_languages'] = [];
            foreach ($langs as $lang) {
                $result['related_languages'][] = $lang['language'];
            }
        }

        return $result;
    }

    public function populateFromApi(SugarBean $bean, array $submittedData, array $options = [])
    {
        $result = parent::populateFromApi($bean, $submittedData, $options);

        if (!$this->checkStatus($bean)) {
            throw new SugarApiExceptionInvalidParameter('Invalid status field value');
        }

        return $result;
    }

    /**
     * Get Elastic Search representation of fields.
     * @param array $fields Of field names.
     * @return array ['FieldName' => ['ElasticName', ...], ...]
     */
    public function getElasticSearchFields(array $fields)
    {
        $result = [];
        $engineContainer = SearchEngine::getInstance()->getEngine()->getContainer();
        $metaDataHelper = new MetaDataHelper($engineContainer->logger);
        $ftsFields = $metaDataHelper->getFtsFields('KBContents');

        $fieldHandler = new MultiFieldHandler();
        foreach ($fields as $fieldName) {
            if (!isset($ftsFields[$fieldName])) {
                continue;
            }
            $sfs = new SearchFields();
            $fieldHandler->buildSearchFields($sfs, 'KBContents', $fieldName, $ftsFields[$fieldName]);
            $sfList = [];
            foreach ($sfs as $sf) {
                $sfList[] = $sf->compile();
            }
            $result[$fieldName] = $sfList;
        }
        return $result;
    }

    /**
     * Send notifications for in review, published and for getting back to draft articles.
     * {@inheritdoc}
     */
    public function checkNotify($bean)
    {
        $prevStatus = null;
        $changedData = $bean->db->getDataChanges($bean);
        if (isset($changedData['status'])) {
            $prevStatus = $changedData['status']['before'];
        }
        // New. In-review or published.
        if ($bean->new_with_id &&
            (
                ($bean->status == KBContent::ST_IN_REVIEW && !empty($bean->kbsapprover_id)) ||
                (in_array($bean->status, KBContent::getPublishedStatuses()) && !empty($bean->assigned_user_id))
            )
        ) {
            return true;
        }
        // Update. To in-review or published. From in-review to draft.
        if ($prevStatus &&
            (
                ($bean->status == KBContent::ST_IN_REVIEW && !empty($bean->kbsapprover_id)) ||
                (
                    !empty($bean->assigned_user_id) &&
                    (
                        in_array($bean->status, KBContent::getPublishedStatuses()) ||
                        ($bean->status == KBContent::ST_DRAFT && $prevStatus == KBContent::ST_IN_REVIEW)
                    )
                )
            )
        ) {
            return true;
        }
        return false;
    }

    /**
     * Check if status field has correct value.
     * @param SugarBean $bean Bean to check.
     * @return bool True if value is correct, false otherwise.
     */
    public function checkStatus($bean)
    {
        $field = new SugarFieldEnum('enum');
        $opts = $field->getOptions($bean->getFieldDefinition('status'));
        return isset($opts[$bean->status]);
    }
}
