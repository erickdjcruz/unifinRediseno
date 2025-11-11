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

class ExternalUsersRelateApi extends RelateApi
{
    /**
     * {@inheritDoc}
     * @see RelateApi::registerApiRest()
     */
    public function registerApiRest()
    {
        return [
            'listRelatedRecords' => [
                'reqType' => 'GET',
                'path' => ['ExternalUsers', '?', 'link', '?'],
                'pathVars' => ['module', 'record', '', 'link_name'],
                'jsonParams' => ['filter'],
                'method' => 'filterRelated',
                'shortHelp' => 'Lists related records.',
                'longHelp' => 'include/api/help/module_record_link_link_name_filter_get_help.html',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     * @see RelateApi::filterRelatedSetup()
     */
    public function filterRelatedSetup(ServiceBase $api, array $args)
    {
        // Return records related to this external user as normal
        if (empty($args['include_external_items']) ||
            $args['include_external_items'] !== 'true') {
            return parent::filterRelatedSetup($api, $args);
        }

        // Load the parent bean.
        $record = BeanFactory::retrieveBean($args['module'], $args['record']);

        if (empty($record)) {
            throw new SugarApiExceptionNotFound(
                sprintf(
                    'Could not find parent record %s in module: %s',
                    $args['record'],
                    $args['module']
                )
            );
        }

        // Load the relationship.
        $linkName = $args['link_name'];
        $linkSeed = $this->getLinkBean($record, $linkName);

        $options = $this->parseArguments($api, $args, $linkSeed);

        // don't include any attachments when retrieving related notes
        if ($linkSeed->getModuleName() === 'Notes' && $linkName !== 'attachments') {
            $args['filter'] = $args['filter'] ?? [];
            $args['filter'][] = [
                'attachment_flag' => [
                    '$equals' => 0,
                ],
            ];
        }

        // If they don't have fields selected we need to include any link fields
        // for this relationship
        if (empty($args['fields']) && is_array($linkSeed->field_defs)) {
            $relatedLinkName = $record->$linkName->getRelatedModuleLinkName();
            $options['linkDataFields'] = [];
            foreach ($linkSeed->field_defs as $field => $def) {
                if (empty($def['rname_link']) || empty($def['link'])) {
                    continue;
                }
                if ($def['link'] != $relatedLinkName) {
                    continue;
                }
                // It's a match
                $options['linkDataFields'][] = $field;
                $options['select'][] = $field;
            }
        }

        // In case the view parameter is set, reflect those fields in the
        // fields argument as well so formatBean only takes those fields
        // into account instead of every bean property.
        if (!empty($args['view'])) {
            $args['fields'] = $options['select'];
        } elseif (!empty($args['fields'])) {
            $args['fields'] = $this->normalizeFields($args['fields'], $options['displayParams']);
        }

        $q = self::getQueryObject($linkSeed, $options);

        if (!isset($args['filter']) || !is_array($args['filter'])) {
            $args['filter'] = [];
        }

        $ids = $this->getRecordIds($record, $linkSeed, $linkName, $args['filter']);
        $q->where()->in('id', $ids);

        if (!sizeof($q->order_by)) {
            self::addOrderBy($q, $this->defaultOrderBy);
        }

        if (isset($options['relate_collections'])) {
            $options = $this->removeRelateCollectionsFromSelect($options);
        }

        // fixing duplicates in the query is not needed since even if it selects many-to-many related records,
        // they are still filtered by one primary record, so the subset is at most one-to-many
        $options['skipFixQuery'] = true;
        return [$args, $q, $options, $linkSeed];
    }

    /**
     * Gets a list of related record ids.
     * @param SugarBean $record
     * @param SugarBean $linkSeed
     * @param string $linkName
     * @param array $filter
     * @return array
     */
    protected function getRecordIds(SugarBean $record, SugarBean $linkSeed, string $linkName, array $filter)
    {
        // Get ids of records related to this user
        $qRelatedIds = new SugarQuery();
        $qRelatedIds->from($linkSeed);
        $qRelatedIds->select(['id']);
        $qRelatedIds->joinSubpanel($record, $linkName, ['joinType' => 'INNER']);
        $qRelatedIds->setJoinOn(['baseBeanId' => $record->id]);
        self::addFilters($filter, $qRelatedIds->where(), $qRelatedIds);

        $qExternalRelatedIds = [];
        // Get ids of records related to contact/lead/prostect...
        if (!empty($record->parent_type) && !empty($record->parent_id)) {
            $externalBean = BeanFactory::getBean($record->parent_type, $record->parent_id);
            if ($externalBean && !empty($externalBean->id)) {
                foreach ($externalBean->getFieldDefinitions() as $def) {
                    if (isset($def['type']) && $def['type'] === 'link' &&
                        $externalBean->load_relationship($def['name'])) {
                        $externalLinkName = $def['name'];
                        if ($externalBean->$externalLinkName->getRelatedModuleName() === $linkSeed->getModuleName() &&
                            $externalBean->$externalLinkName->getType() === $record->$linkName->getType()) {
                            $qExternalRelatedIdsQuery = new SugarQuery();
                            $qExternalRelatedIdsQuery->from($linkSeed);
                            $qExternalRelatedIdsQuery->select(['id']);
                            $qExternalRelatedIdsQuery->joinSubpanel($externalBean, $externalLinkName, ['joinType' => 'INNER']);
                            $qExternalRelatedIdsQuery->setJoinOn(['baseBeanId' => $externalBean->id]);
                            self::addFilters($filter, $qExternalRelatedIdsQuery->where(), $qExternalRelatedIdsQuery);
                            $qExternalRelatedIds[] = $qExternalRelatedIdsQuery;
                        }
                    }
                }
            }
        }
        // Get ids of records created by this user
        $qExternalIds = new SugarQuery();
        $qExternalIds->from($linkSeed);
        $qExternalIds->select(['id']);
        $qExternalIds->where()
            ->equals('source_id', $record->external_id);

        self::addFilters($filter, $qExternalIds->where(), $qExternalIds);

        $qIds = new SugarQuery();
        $qIds->union($qRelatedIds);
        $qIds->union($qExternalIds);
        foreach ($qExternalRelatedIds as $query) {
            $qIds->union($query);
        }

        $ids = $qIds
            ->compile()
            ->execute()
            ->fetchFirstColumn();

        return array_unique($ids);
    }
}
