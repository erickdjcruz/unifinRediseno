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


class RelateApi extends FilterApi
{
    public function registerApiRest()
    {
        return [
            'filterRelatedRecords' => [
                'reqType' => 'GET',
                'path' => ['<module>', '?', 'link', '?', 'filter'],
                'pathVars' => ['module', 'record', '', 'link_name', ''],
                'jsonParams' => ['filter'],
                'method' => 'filterRelated',
                'shortHelp' => 'Lists related filtered records.',
                'longHelp' => 'include/api/help/module_record_link_link_name_filter_get_help.html',
            ],
            'filterRelatedRecordsCount' => [
                'reqType' => 'GET',
                'path' => ['<module>', '?', 'link', '?', 'filter', 'count'],
                'pathVars' => ['module', 'record', '', 'link_name', '', ''],
                'jsonParams' => ['filter'],
                'method' => 'filterRelatedCount',
                'shortHelp' => 'Lists related filtered records.',
                'longHelp' => 'include/api/help/module_record_link_link_name_filter_get_help.html',

            ],
            'filterRelatedRecordsLeanCount' => [
                'reqType' => 'GET',
                'minVersion' => '11.4',
                'path' => ['<module>', '?', 'link', '?', 'filter', 'leancount'],
                'pathVars' => ['module', 'record', '', 'link_name', '', ''],
                'jsonParams' => ['filter'],
                'method' => 'filterRelatedLeanCount',
                'shortHelp' => 'Gets the "lean" count of filtered related items. ' .
                    'The count should always be in the range: 0..max_num. ' .
                    'The response has a boolean flag "has_more" that defines if there are more rows, ' .
                    'than max_num parameter value.',
                'longHelp' => 'include/api/help/module_record_link_link_name_filter_get_help.html',

            ],
            'listRelatedRecords' => [
                'reqType' => 'GET',
                'path' => ['<module>', '?', 'link', '?'],
                'pathVars' => ['module', 'record', '', 'link_name'],
                'jsonParams' => ['filter'],
                'method' => 'filterRelated',
                'shortHelp' => 'Lists related records.',
                'longHelp' => 'include/api/help/module_record_link_link_name_filter_get_help.html',
            ],
            'listRelatedRecordsCount' => [
                'reqType' => 'GET',
                'path' => ['<module>', '?', 'link', '?', 'count'],
                'pathVars' => ['module', 'record', '', 'link_name', ''],
                'jsonParams' => ['filter'],
                'method' => 'filterRelatedCount',
                'shortHelp' => 'Counts all filtered related records.',
                'longHelp' => 'include/api/help/module_record_link_link_name_filter_get_help.html',
            ],
            'listRelatedRecordsLeanCount' => [
                'reqType' => 'GET',
                'minVersion' => '11.4',
                'path' => ['<module>', '?', 'link', '?', 'leancount'],
                'pathVars' => ['module', 'record', '', 'link_name', ''],
                'jsonParams' => ['filter'],
                'method' => 'filterRelatedLeanCount',
                'shortHelp' => 'Gets the "lean" count of related items.' .
                    'The count should always be in the range: 0..max_num. ' .
                    'The response has a boolean flag "has_more" that defines if there are more rows, ' .
                    'than max_num parameter value.',
                'longHelp' => 'include/api/help/module_record_link_link_name_filter_get_help.html',
            ],
        ];
    }

    /**
     * Gets a new relate bean for a link.
     * @param SugarBean $record
     * @param string $linkName
     * @return SugarBean
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionNotFound
     */
    protected function getLinkBean(SugarBean $record, string $linkName): SugarBean
    {
        if (!$record->load_relationship($linkName)) {
            // The relationship did not load.
            throw new SugarApiExceptionNotFound('Could not find a relationship named: ' . $linkName);
        }
        $linkModuleName = $record->$linkName->getRelatedModuleName();
        $linkSeed = BeanFactory::newBean($linkModuleName);
        if (!$linkSeed->ACLAccess('list')) {
            throw new SugarApiExceptionNotAuthorized('No access to list records for module: ' . $linkModuleName);
        }
        return $linkSeed;
    }

    public function filterRelatedSetup(ServiceBase $api, array $args)
    {
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

        // Some relationships want the role column ignored
        if (!empty($args['ignore_role'])) {
            $ignoreRole = true;
        } else {
            $ignoreRole = false;
        }

        $q->joinSubpanel($record, $linkName, ['joinType' => 'INNER', 'ignoreRole' => $ignoreRole]);

        $q->setJoinOn(['baseBeanId' => $record->id]);

        // return 'is_external_link' for records created by external users
        if ($this->hasExternalRecords($record, $linkName)) {
            $join = $q->getJoinForLink($linkName);
            if ($join && $join->relationshipTableAlias) {
                $alias = $join->relationshipTableAlias;
                $q->select()->fieldRaw("$alias.is_external_link");
            }
        }

        if (!isset($args['filter']) || !is_array($args['filter'])) {
            $args['filter'] = [];
        }
        self::addFilters($args['filter'], $q->where(), $q);

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
     * Checks if a link has external records.
     * @param SugarBean $bean
     * @param string $link
     * @return bool
     */
    protected function hasExternalRecords(SugarBean $bean, string $link): bool
    {
        $relObj = $bean->$link->getRelationshipObject();
        return $relObj && get_class($relObj) === 'PersonM2MRelationship' &&
            $relObj->hasExternalRecords();
    }

    public function filterRelated(ServiceBase $api, array $args)
    {

        $api->action = 'list';

        [$args, $q, $options, $linkSeed] = $this->filterRelatedSetup($api, $args);

        return $this->runQuery($api, $args, $q, $options, $linkSeed);
    }

    public function filterRelatedCount(ServiceBase $api, array $args)
    {
        $api->action = 'list';

        /** @var SugarQuery $q */
        [, $q] = $this->filterRelatedSetup($api, $args);

        $q->select->selectReset()->setCountQuery();
        $q->limit = null;
        $q->orderByReset();

        $stmt = $q->compile()->execute();
        $count = (int)$stmt->fetchOne();

        return [
            'record_count' => $count,
        ];
    }

    /**
     * Checks if the count of related records is lower than the max_num
     * The number should be in range [0..{max_num+1}]
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function filterRelatedLeanCount(ServiceBase $api, array $args)
    {
        if (isset($args['max_num'])) {
            $args['max_num'] = (int)$args['max_num'];
        }
        if (!isset($args['max_num'])
            || $args['max_num'] <= 0) {
            throw new SugarApiExceptionMissingParameter('max_num parameter is missing or invalid');
        }
        $api->action = 'list';
        $args['fields'] = 'id';
        $args['view'] = '';

        /** @var SugarQuery $q */
        [, $q] = $this->filterRelatedSetup($api, $args);
        $q->orderByReset();
        $stmt = $q->compile()->execute();
        $count = safeCount($stmt->fetchFirstColumn());

        return [
            'record_count' => $count > $args['max_num'] ? $args['max_num'] : $count,
            'has_more' => $count > $args['max_num'],
        ];
    }
}
