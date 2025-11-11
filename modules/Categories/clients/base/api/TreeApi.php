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


/**
 * Api to work with data in tree-like format.
 * Class TreeApi
 */
class TreeApi extends FilterApi
{
    /**
     * Depth of the tree by default.
     *
     * @var integer
     */
    public $defaultTreeDepth = 5;

    public function registerApiRest()
    {
        return [
            'filterModuleSubTree' => [
                'reqType' => 'GET',
                'path' => ['<module>', '?', 'tree', '?'],
                'pathVars' => ['module', 'record', '', 'link_name'],
                'method' => 'filterSubTree',
                'exception' => [
                    'SugarApiExceptionInvalidParameter',
                    'SugarApiExceptionNotAuthorized',
                    'SugarApiExceptionNotFound',
                ],
            ],
            'filterModuleTree' => [
                'reqType' => 'GET',
                'path' => ['<module>', 'tree', '?'],
                'pathVars' => ['module', '', 'link_name'],
                'method' => 'filterTree',
                'exception' => [
                    'SugarApiExceptionInvalidParameter',
                    'SugarApiExceptionNotAuthorized',
                    'SugarApiExceptionNotFound',
                ],
            ],
            'tree' => [
                'reqType' => 'GET',
                'path' => ['<module>', '?', 'tree'],
                'pathVars' => ['module', 'root', ''],
                'method' => 'tree',
                'noEtag' => true,
                'shortHelp' => 'This method returns formatted tree for selected root',
                'longHelp' => 'modules/Categories/clients/base/api/help/tree_get_tree_help.html',
            ],
            'children' => [
                'reqType' => 'GET',
                'path' => ['<module>', '?', 'children'],
                'pathVars' => ['module', 'record', ''],
                'method' => 'children',
                'shortHelp' => 'This method returns children categories for selected record',
                'longHelp' => 'modules/Categories/clients/base/api/help/tree_get_children_help.html',
            ],
            'next' => [
                'reqType' => 'GET',
                'path' => ['<module>', '?', 'next'],
                'pathVars' => ['module', 'record', ''],
                'method' => 'next',
                'shortHelp' => 'This method returns next sibling of selected record',
                'longHelp' => 'modules/Categories/clients/base/api/help/tree_get_next_help.html',
            ],
            'prev' => [
                'reqType' => 'GET',
                'path' => ['<module>', '?', 'prev'],
                'pathVars' => ['module', 'record', ''],
                'method' => 'prev',
                'shortHelp' => 'This method returns previous sibling of selected record',
                'longHelp' => 'modules/Categories/clients/base/api/help/tree_get_prev_help.html',
            ],
            'parent' => [
                'reqType' => 'GET',
                'path' => ['<module>', '?', 'parent'],
                'pathVars' => ['module', 'record', ''],
                'method' => 'getParent',
                'shortHelp' => 'This method returns parent node of selected record',
                'longHelp' => 'modules/Categories/clients/base/api/help/tree_get_parent_help.html',
            ],
            'path' => [
                'reqType' => 'GET',
                'path' => ['<module>', '?', 'path'],
                'pathVars' => ['module', 'record', ''],
                'method' => 'path',
                'shortHelp' => 'This method returns full path of selected record',
                'longHelp' => 'modules/Categories/clients/base/api/help/tree_get_path_help.html',
            ],
            'movebefore' => [
                'reqType' => 'PUT',
                'path' => ['<module>', '?', 'movebefore', '?'],
                'pathVars' => ['module', 'record', 'movebefore', 'target'],
                'method' => 'moveBefore',
                'shortHelp' => 'This method record as previous sibling of target',
                'longHelp' => 'modules/Categories/clients/base/api/help/tree_put_movebefore_help.html',
            ],
            'moveafter' => [
                'reqType' => 'PUT',
                'path' => ['<module>', '?', 'moveafter', '?'],
                'pathVars' => ['module', 'record', 'moveafter', 'target'],
                'method' => 'moveAfter',
                'shortHelp' => 'This method record as next sibling of target',
                'longHelp' => 'modules/Categories/clients/base/api/help/tree_put_moveafter_help.html',
            ],
            'movefirst' => [
                'reqType' => 'PUT',
                'path' => ['<module>', '?', 'movefirst', '?'],
                'pathVars' => ['module', 'record', 'movefirst', 'target'],
                'method' => 'moveFirst',
                'shortHelp' => 'This method record as first child of target',
                'longHelp' => 'modules/Categories/clients/base/api/help/tree_put_movefirst_help.html',
            ],
            'movelast' => [
                'reqType' => 'PUT',
                'path' => ['<module>', '?', 'movelast', '?'],
                'pathVars' => ['module', 'record', 'movelast', 'target'],
                'method' => 'moveLast',
                'shortHelp' => 'This method record as last child of target',
                'longHelp' => 'modules/Categories/clients/base/api/help/tree_put_movelast_help.html',
            ],
            'append' => [
                'reqType' => 'POST',
                'path' => ['<module>', 'append', '?'],
                'pathVars' => ['module', 'append', 'target'],
                'method' => 'append',
                'shortHelp' => 'This method append record to target as last child',
                'longHelp' => 'modules/Categories/clients/base/api/help/tree_post_append_help.html',
                'exceptions' => [
                    'SugarApiExceptionNotFound',
                    'SugarApiExceptionInvalidParameter',
                ],
            ],
            'prepend' => [
                'reqType' => 'POST',
                'path' => ['<module>', 'prepend', '?'],
                'pathVars' => ['module', 'prepend', 'target'],
                'method' => 'prepend',
                'shortHelp' => 'This method prepend record to target as first child',
                'longHelp' => 'modules/Categories/clients/base/api/help/tree_post_prepend_help.html',
                'exceptions' => [
                    'SugarApiExceptionNotFound',
                    'SugarApiExceptionInvalidParameter',
                ],
            ],
            'insertbefore' => [
                'reqType' => 'POST',
                'path' => ['<module>', 'insertbefore', '?'],
                'pathVars' => ['module', 'insertbefore', 'target'],
                'method' => 'insertBefore',
                'shortHelp' => 'This method insert record as previous sibling of target',
                'longHelp' => 'modules/Categories/clients/base/api/help/tree_post_insertbefore_help.html',
                'exceptions' => [
                    'SugarApiExceptionNotFound',
                    'SugarApiExceptionInvalidParameter',
                ],
            ],
            'insertafter' => [
                'reqType' => 'POST',
                'path' => ['<module>', 'insertafter', '?'],
                'pathVars' => ['module', 'insertafter', 'target'],
                'method' => 'insertAfter',
                'shortHelp' => 'This method insert record as next sibling of target',
                'longHelp' => 'modules/Categories/clients/base/api/help/tree_post_insertafter_help.html',
                'exceptions' => [
                    'SugarApiExceptionNotFound',
                    'SugarApiExceptionInvalidParameter',
                ],
            ],
        ];
    }

    /**
     * This method loads and returns bean.
     * @param string $module Module to retrieve bean.
     * @param string $id Bean id.
     * @return SugarBean
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarApiExceptionNotFound
     */
    protected function retrieveBean($module, $id = null)
    {
        $bean = BeanFactory::retrieveBean($module, $id);

        if (null === $bean) {
            throw new SugarApiExceptionNotFound('Could not find record in module: ' . $module);
        }
        if (false === ($bean instanceof NestedBeanInterface)) {
            throw new SugarApiExceptionInvalidParameter(
                'Requested module "' . $module . '" should be instance of NestedBeanInterface'
            );
        }

        return $bean;
    }

    /**
     * This method loads and returns beans that should be bound in the tree.
     * @param string $module Module to retrieve bean.
     * @param string $subject Subject bean id.
     * @param string $target Target bean id.
     * @return array SugarBean An array of SugarBeans that can bound in tree.
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarApiExceptionNotFound
     * @throws SugarApiException Subject and target beans are not suitable for the operation.
     */
    protected function loadBoundBeans($module, $subject, $target)
    {
        $bean = $this->retrieveBean($module, $subject);
        if (null === $bean) {
            throw new SugarApiExceptionNotFound('Could not find record: ' . $subject . ' in module: ' . $module);
        }

        $target = $this->retrieveBean($module, $target);
        if (null === $target) {
            throw new SugarApiExceptionNotFound('Could not find record: ' . $target . ' in module: ' . $module);
        }

        if ($bean->id === $target->id) {
            throw new SugarApiException('The target node should not be self.');
        }

        if ($target->isDescendantOf($bean)) {
            throw new SugarApiException('The target node should not be descendant.');
        }

        return [$bean, $target];
    }

    /**
     * This method creates and populate from API new bean.
     * @param ServiceBase $api Api object.
     * @param array $args The arguments array passed in from the API.
     * @return SugarBean Created bean.
     * @throws SugarApiExceptionInvalidParameter Provided module doesn't implement `NestedBeanInterface`.
     * @throws SugarApiExceptionEditConflict Server and client timestamps are different.
     */
    protected function createNewBean(ServiceBase $api, array $args)
    {
        $bean = BeanFactory::newBean($args['module']);

        if (false === ($bean instanceof NestedBeanInterface)) {
            throw new SugarApiExceptionInvalidParameter(
                'Requested module "' . $args['module'] . '" should be ' .
                'instance of NestedBeanInterface'
            );
        }

        try {
            $errors = ApiHelper::getHelper($api, $bean)->populateFromApi($bean, $args);
        } catch (SugarApiExceptionEditConflict $conflict) {
            $api->action = 'view';
            $data = $this->formatBean($api, $args, $bean);
            // put current state of the record on the exception
            $conflict->setExtraData('record', $data);
            throw $conflict;
        }
        $bean->id = null;
        return $bean;
    }

    /**
     * This method prepends record to target as first child.
     * @param ServiceBase $api Api object.
     * @param array $args The arguments array passed in from the API.
     * @return array An array version of the SugarBean with only the requested fields (also filtered by ACL).
     * @throws SugarApiExceptionNotFound
     * @throws SugarApiExceptionInvalidParameter
     */
    public function prepend(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['module', 'target']);
        $bean = $this->createNewBean($api, $args);
        $api->action = 'save';
        $target = $this->retrieveBean($args['module'], $args['target']);
        $target->prepend($bean);
        $this->updateBean($bean, $api, $args);
        return $this->formatBean($api, $args, $bean);
    }

    /**
     * This method appends record to target as last child.
     * @param ServiceBase $api Api object.
     * @param array $args The arguments array passed in from the API.
     * @return array An array version of the SugarBean with only the requested fields (also filtered by ACL).
     * @throws SugarApiExceptionNotFound
     * @throws SugarApiExceptionInvalidParameter
     */
    public function append(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['module', 'target']);
        $bean = $this->createNewBean($api, $args);
        $api->action = 'save';
        $target = $this->retrieveBean($args['module'], $args['target']);
        $target->append($bean);
        $this->updateBean($bean, $api, $args);
        return $this->formatBean($api, $args, $bean);
    }

    /**
     * This method inserts record as previous sibling of target.
     * @param ServiceBase $api Api object.
     * @param array $args The arguments array passed in from the API.
     * @return array An array version of the SugarBean with only the requested fields (also filtered by ACL).
     * @throws SugarApiExceptionNotFound
     * @throws SugarApiExceptionInvalidParameter
     */
    public function insertBefore(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['module', 'target']);
        $bean = $this->createNewBean($api, $args);
        $api->action = 'save';
        $target = $this->retrieveBean($args['module'], $args['target']);
        $bean->insertBefore($target);
        $this->updateBean($bean, $api, $args);
        return $this->formatBean($api, $args, $bean);
    }

    /**
     * This method inserts record as next sibling of target.
     * @param ServiceBase $api Api object.
     * @param array $args The arguments array passed in from the API.
     * @return array An array version of the SugarBean with only the requested fields (also filtered by ACL).
     * @throws SugarApiExceptionNotFound
     * @throws SugarApiExceptionInvalidParameter
     */
    public function insertAfter(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['module', 'target']);
        $bean = $this->createNewBean($api, $args);
        $api->action = 'save';
        $target = $this->retrieveBean($args['module'], $args['target']);
        $bean->insertAfter($target);
        $this->updateBean($bean, $api, $args);
        return $this->formatBean($api, $args, $bean);
    }

    /**
     * This method moves record as previous sibling of target.
     * @param ServiceBase $api Api object.
     * @param array $args The arguments array passed in from the API.
     * @return array An array version of the SugarBean with only the requested fields (also filtered by ACL).
     */
    public function moveBefore(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['module', 'record', 'target']);
        [$bean, $target] = $this->loadBoundBeans($args['module'], $args['record'], $args['target']);
        $bean->moveBefore($target);
        return $this->formatBean($api, $args, $bean);
    }

    /**
     * This method moves record as next sibling of target.
     * @param ServiceBase $api Api object.
     * @param array $args The arguments array passed in from the API.
     * @return array An array version of the SugarBean with only the requested fields (also filtered by ACL).
     */
    public function moveAfter(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['module', 'record', 'target']);
        [$bean, $target] = $this->loadBoundBeans($args['module'], $args['record'], $args['target']);
        $bean->moveAfter($target);
        return $this->formatBean($api, $args, $bean);
    }

    /**
     * This method moves record as first child of target.
     * @param ServiceBase $api Api object.
     * @param array $args The arguments array passed in from the API.
     * @return array An array version of the SugarBean with only the requested fields (also filtered by ACL).
     */
    public function moveFirst(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['module', 'record', 'target']);
        [$bean, $target] = $this->loadBoundBeans($args['module'], $args['record'], $args['target']);
        $bean->moveAsFirst($target);
        return $this->formatBean($api, $args, $bean);
    }

    /**
     * This method moves record as last child of target.
     * @param ServiceBase $api Api object.
     * @param array $args The arguments array passed in from the API.
     * @return array An array version of the SugarBean.
     */
    public function moveLast(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['module', 'record', 'target']);
        [$bean, $target] = $this->loadBoundBeans($args['module'], $args['record'], $args['target']);
        $bean->moveAsLast($target);
        return $this->formatBean($api, $args, $bean);
    }

    /**
     * This method returns formatted tree for selected root.
     * @param ServiceBase $api Api object.
     * @param array $args The arguments array passed in from the API.
     * @return array Formatted collection.
     * @uses TreeApi::formatTree to format output results.
     */
    public function tree(ServiceBase $api, array $args)
    {
        $api->action = 'list';
        $this->requireArgs($args, ['module', 'root']);
        // Load up a seed bean
        $seed = BeanFactory::newBean($args['module']);
        if (!$seed->ACLAccess($api->action)) {
            throw new SugarApiExceptionNotAuthorized('No access to view records for module: ' . $args['module']);
        }
        $seed = $this->retrieveBean($args['module'], $args['root']);
        return $this->formatTree($api, $args, $seed->getTree());
    }

    /**
     * This method formats tree data to sugar api collection object.
     * @param ServiceBase $api Api object.
     * @param array $args The arguments array passed in from the API.
     * @param array $tree Hierarchy of nodes.
     * @return array Formatted collection.
     */
    public function formatTree(ServiceBase $api, array $args, array $tree)
    {
        $this->requireArgs($args, ['module']);
        $data = $emptySet = [
            'next_offset' => -1,
            'records' => [],
        ];

        foreach ($tree as $node) {
            $nodeBean = BeanFactory::getBean($args['module'], $node['id']);
            $nodeBean = $this->formatBean($api, $args, $nodeBean);
            if (!empty($node['children'])) {
                $nodeBean['children'] = $this->formatTree($api, $args, $node['children']);
            } else {
                $nodeBean['children'] = $emptySet;
            }

            $data['records'][] = $nodeBean;
        }

        return $data;
    }

    /**
     * This method returns children nodes for selected record.
     * @param ServiceBase $api Api object.
     * @param array $args The arguments array passed in from the API.
     * @return array List of children nodes.
     * @uses TreeApi::formatTree to format output results.
     */
    public function children(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['module', 'record']);
        $record = $this->retrieveBean($args['module'], $args['record']);
        return $this->formatTree($api, $args, $record->getChildren(1));
    }

    /**
     * This method returns next sibling of selected record.
     * @param ServiceBase $api Api object.
     * @param array $args The arguments array passed in from the API.
     * @return array Node data.
     */
    public function next(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['module', 'record']);
        $record = $this->retrieveBean($args['module'], $args['record']);
        return $record->getNextSibling();
    }

    /**
     * This method returns previous sibling of selected record.
     * @param ServiceBase $api Api object.
     * @param array $args The arguments array passed in from the API.
     * @return array Node data.
     */
    public function prev(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['module', 'record']);
        $record = $this->retrieveBean($args['module'], $args['record']);
        return $record->getPrevSibling();
    }

    /**
     * This method returns parent node of selected record.
     * @param ServiceBase $api Api object.
     * @param array $args The arguments array passed in from the API.
     * @return array Node data.
     */
    public function getParent(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['module', 'record']);
        $bean = $this->retrieveBean($args['module'], $args['record']);
        return $bean->getParent();
    }

    /**
     * This method returns full path of selected record.
     * @param ServiceBase $api Api object.
     * @param array $args The arguments array passed in from the API.
     * @return array List of parent nodes.
     */
    public function path(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['module', 'record']);
        $record = $this->retrieveBean($args['module'], $args['record']);
        return $record->getParents();
    }

    /**
     * {@inheritDoc}
     */
    protected function parseArguments(ServiceBase $api, array $args, SugarBean $seed = null)
    {
        $options = parent::parseArguments($api, $args, $seed);
        // Set up the defaults
        $options['depth'] = $this->defaultTreeDepth;

        if (!empty($args['depth'])) {
            $options['depth'] = (int)$args['depth'];
        }
        return $options;
    }

    /**
     * {@inheritDoc}
     */
    protected function runQuery(ServiceBase $api, array $args, SugarQuery $q, array $options, SugarBean $seed = null)
    {
        $data = parent::runQuery($api, $args, $q, $options, $seed);

        if ($options['depth'] > 0) {
            $options['depth']--;
            foreach ($data['records'] as $i => $row) {
                $record = $seed->getCleanCopy();
                $record->loadFromRow($row, true);

                $q = self::getQueryObject($seed, $options);
                $q->joinSubpanel(
                    $record,
                    $args['link_name'],
                    [
                        'joinType' => 'INNER',
                        'ignoreRole' => !empty($args['ignore_role']),
                    ]
                );
                self::addFilters($args['filter'], $q->where(), $q);

                $data['records'][$i][$args['link_name']] = $this->runQuery($api, $args, $q, $options, $seed);
            }
        }
        return $data;
    }

    /**
     * Filters tree for provided record.
     * @param ServiceBase $api Api object.
     * @param array $args The arguments array passed in from the API.
     * @return array
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionNotFound
     */
    public function filterSubTree(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['module', 'record', 'link_name']);
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
        if (!$record->ACLAccess('view')) {
            throw new SugarApiExceptionNotAuthorized('No access to view records for module: ' . $args['module']);
        }
        // Load the relationship.
        $linkName = $args['link_name'];
        if (!$record->load_relationship($linkName)) {
            // The relationship did not load.
            throw new SugarApiExceptionNotFound('Could not find a relationship named: ' . $args['link_name']);
        }
        $linkModuleName = $record->$linkName->getRelatedModuleName();
        if ($linkModuleName != $record->module_name) {
            throw new SugarApiExceptionNotFound('Could not find self referencing in relationship named: ' . $linkName);
        }
        $linkSeed = BeanFactory::newBean($linkModuleName);
        if (!$linkSeed->ACLAccess('list')) {
            throw new SugarApiExceptionNotAuthorized('No access to list records for module: ' . $linkModuleName);
        }
        $options = $this->parseArguments($api, $args, $linkSeed);

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

        if (!isset($args['filter']) || !is_array($args['filter'])) {
            $args['filter'] = [];
        }

        $q = self::getQueryObject($linkSeed, $options);
        $q->joinSubpanel(
            $record,
            $linkName,
            [
                'joinType' => 'INNER',
                'ignoreRole' => !empty($args['ignore_role']),
            ]
        );
        self::addFilters($args['filter'], $q->where(), $q);

        return $this->runQuery($api, $args, $q, $options, $linkSeed);
    }

    /**
     * Filters tree for provided module.
     * @param ServiceBase $api Api object.
     * @param array $args The arguments array passed in from the API.
     * @return array
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionNotFound
     */
    public function filterTree(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['module', 'link_name']);
        // Load up a seed bean
        $seed = BeanFactory::newBean($args['module']);
        if (!$seed->ACLAccess('list')) {
            throw new SugarApiExceptionNotAuthorized('No access to view records for module: ' . $args['module']);
        }
        // Load the relationship.
        $linkName = $args['link_name'];
        if (!$seed->load_relationship($linkName)) {
            // The relationship did not load.
            throw new SugarApiExceptionNotFound('Could not find a relationship named: ' . $linkName);
        }
        $linkModuleName = $seed->$linkName->getRelatedModuleName();
        if ($linkModuleName != $seed->module_name) {
            throw new SugarApiExceptionNotFound('Could not find self referencing in relationship named: ' . $linkName);
        }
        $options = $this->parseArguments($api, $args, $seed);

        // If they don't have fields selected we need to include any link fields
        // for this relationship
        if (empty($args['fields']) && is_array($seed->field_defs)) {
            $relatedLinkName = $seed->$linkName->getRelatedModuleLinkName();
            $options['linkDataFields'] = [];

            foreach ($seed->field_defs as $field => $def) {
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
        if (!isset($args['filter']) || !is_array($args['filter'])) {
            $args['filter'] = [];
        }

        $q = self::getQueryObject($seed, $options);

        if ($seed->$linkName->getSide() == REL_LHS) {
            $q->where()->isNull($seed->$linkName->getRelationshipObject()->def['rhs_key'], $seed);
        } else {
            $q->where()->isNull($seed->$linkName->getRelationshipObject()->def['lhs_key'], $seed);
        }

        self::addFilters($args['filter'], $q->where(), $q);

        return $this->runQuery($api, $args, $q, $options, $seed);
    }
}
