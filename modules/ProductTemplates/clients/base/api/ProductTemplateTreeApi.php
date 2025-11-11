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

class ProductTemplateTreeApi extends SugarApi
{
    public function registerApiRest()
    {
        return [
            'tree' => [
                'reqType' => 'GET',
                'path' => ['ProductTemplates', 'tree',],
                'pathVars' => ['module', 'type',],
                'method' => 'getTemplateTree',
                'shortHelp' => 'Returns a filterable tree structure of all Product Templates and Product Categories',
                'longHelp' => 'modules/ProductTemplates/clients/base/api/help/tree.html',
            ],
            'filterTree' => [
                'reqType' => 'POST',
                'path' => ['ProductTemplates', 'tree',],
                'pathVars' => ['module', 'type',],
                'method' => 'getTemplateTree',
                'shortHelp' => 'Returns a filterable tree structure of all Product Templates and Product Categories',
                'longHelp' => 'modules/ProductTemplates/clients/base/api/help/tree.html',
            ],
        ];
    }

    /**
     * Gets the full tree data in a jstree structure
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarQueryException
     */
    public function getTemplateTree(ServiceBase $api, array $args)
    {
        $this->checkAccess();

        $data = [];
        $tree = [];
        $records = [];
        $max_num = $this->getSugarConfig()->get('list_max_entries_per_page', 20);
        $offset = -1;
        $total = 0;
        $max_limit = $this->getSugarConfig()->get('max_list_limit');

        //set parameters
        if (array_key_exists('filter', $args)) {
            $data = $this->getTreeDataWithFilter($args['filter']);
        } elseif (array_key_exists('root', $args)) {
            $data = $this->getTreeDataWithRoot($args['root']);
        } else {
            $data = $this->getTreeDataWithRoot(null);
        }

        if (array_key_exists('offset', $args)) {
            $offset = $args['offset'];
        }

        //if the max_num is in-between 1 and $max_limit, set it, otherwise use max_limit
        if (array_key_exists('max_num', $args) && ($args['max_num'] < 1 || $args['max_num'] > $max_limit)) {
            $max_num = $max_limit;
        } elseif (array_key_exists('max_num', $args)) {
            $max_num = $args['max_num'];
        }

        // get total records in this set, calculate start position, slice data to current page
        $total = safeCount($data);

        $offset = ($offset == -1) ? 0 : $offset;

        if ($offset < $total) {
            $data = array_slice($data, $offset, $max_num);

            //build the treedata
            foreach ($data as $node) {
                // do not render a leaf for an empty (product-free) category
                if ($node['type'] === 'product' || $this->checkCategoryContainsProduct($node['id'])) {
                    //create new leaf
                    $records[] = $this->generateNewLeaf($node, $offset);
                }
                $offset++;
            }
        }

        if ($total <= $offset) {
            $offset = -1;
        }

        $tree['records'] = $records;
        $tree['next_offset'] = $offset;

        return $tree;
    }

    /**
     * Checks if the user has access to both ProductCategories and ProductTemplates
     */
    protected function checkAccess()
    {
        $pcBean = BeanFactory::newBean('ProductCategories');
        $ptBean = BeanFactory::newBean('ProductTemplates');
        if (!$pcBean->aclAccess('list')) {
            throw new SugarApiExceptionNotAuthorized('No access to view Product Categories records');
        } elseif (!$ptBean->aclAccess('list')) {
            throw new SugarApiExceptionNotAuthorized('No access to view Product Catalog records');
        }
    }

    /**
     * Returns true if the provided category has at least one product template
     */
    protected function checkCategoryContainsProduct(string $id): bool
    {
        $query = $this->prepareCategoryCheckQuery();
        $query->where()->equals('category.id', $id);

        return $this->continueRecursiveCategoryCheck($query);
    }

    /**
     * Returns true if the children of the provided category list relate to at least one product template
     */
    protected function checkChildCategoriesContainProduct(array $categoryIds): bool
    {
        $query = $this->prepareCategoryCheckQuery();
        $query->where()->in('category.parent_id', $categoryIds);

        return $this->continueRecursiveCategoryCheck($query);
    }

    /**
     * Returns true if provided SugarQuery returned at least one product template,
     * otherwise recursively calls checkChildCategoriesContainProduct
     */
    protected function continueRecursiveCategoryCheck(SugarQuery $query): bool
    {
        $productFreeCategoriesIds = [];
        $stmt = $query->compile()->execute();
        while ($row = $stmt->fetchAssociative()) {
            if ($row['p_id'] !== null) {
                return true;
            }
            $productFreeCategoriesIds[$row['c_id']] = true;
        }

        if (!empty($productFreeCategoriesIds)) {
            return $this->checkChildCategoriesContainProduct(array_keys($productFreeCategoriesIds));
        } else {
            return false;
        }
    }

    /**
     * Returns SugarQuery for fetching categories and related product templates
     */
    protected function prepareCategoryCheckQuery(): SugarQuery
    {
        $bean = BeanFactory::newBean('ProductCategories');
        $query = new SugarQuery(DBManagerFactory::getInstance('listviews'));
        $query->from($bean, ['alias' => 'category']);

        $subQuery = new SugarQuery(DBManagerFactory::getInstance('listviews'));
        $bean = BeanFactory::newBean('ProductTemplates');
        $subQuery->from($bean, ['alias' => 'product_template']);
        $subQuery->select->selectReset();
        $subQuery->select(['product_template.id', 'product_template.category_id']);
        $subQuery->where()->equals('product_template.active_status', 'Active');

        $query->joinTable($subQuery, ['alias' => 'product_template', 'joinType' => 'LEFT'])
            ->on()
            ->equalsField('product_template.category_id', 'category.id');
        $query->select->selectReset();
        $query->select([['category.id', 'c_id'], ['product_template.id', 'p_id']]);
        $query->orderBy('product_template.id', 'ASC');

        return $query;
    }

    /**
     * Create input array with given filter
     *
     * @param string $filter
     * @return array
     * @throws SugarQueryException
     */
    protected function getTreeDataWithFilter(string $filter)
    {
        $array = [
            'ProductCategories' => $filter,
            'ProductTemplates' => $filter,
        ];

        return $this->getTreeDataWithArray($array);
    }

    /**
     * Create input array with given root 'id'
     *
     * @param string $root
     * @return array
     * @throws SugarQueryException
     */
    protected function getTreeDataWithRoot(string $root = null)
    {
        $array = [
            'ProductCategories' => [
                'parent_id' => $root,
            ],
            'ProductTemplates' => [
                'category_id' => $root,
            ],
        ];

        return $this->getTreeDataWithArray($array);
    }

    /**
     * Get data using SugarQuery with the given input array
     *
     * @param array $input
     * @return array
     * @throws SugarQueryException
     */
    protected function getTreeDataWithArray(array $input = [])
    {
        $q = new SugarQuery();

        // Create a separate query for each input, and union them
        // all together using $q
        foreach ($input as $table => $value) {
            $bean = BeanFactory::newBean($table);
            if (!is_null($bean)) {
                $db = DBManagerFactory::getInstance();
                if ($table === 'ProductCategories') {
                    $type = $db->quoted('category');
                    $listOrder = 'list_order';
                    $listOrderNullsLast = '(CASE WHEN list_order IS NULL THEN 1 ELSE 0 END)';
                } elseif ($table === 'ProductTemplates') {
                    $type = $db->quoted('product');
                    $listOrder = 0;
                    $listOrderNullsLast = 0;
                }
                $query = new SugarQuery();
                $query->from($bean);
                $query->select(['id', 'name']);
                $query->select()->fieldRaw($type, 'type');
                $query->select()->fieldRaw($listOrderNullsLast, 'list_order_nulls_last');
                $query->select()->fieldRaw($listOrder, 'list_order');
                if ($table === 'ProductTemplates') {
                    $query->where()->equals('active_status', 'Active');
                }
                // Set up the filter for the query
                if (is_array($value)) {
                    foreach ($value as $key => $colValue) {
                        if (is_null($colValue)) {
                            $query->where()->isNull($key);
                        } else {
                            $query->where()->equals($key, $colValue);
                        }
                    }
                } else {
                    $query->where()->contains('name', $value);
                }

                $q->union($query);
            }
        }

        // Set the correct order of the results. Types should be grouped
        // together first, then by the list order within those types. Ties
        // should be broken by alphabetical order
        $q->orderBy('type', 'ASC');
        $q->orderBy('list_order_nulls_last', 'ASC');
        $q->orderBy('list_order', 'ASC');
        $q->orderBy('name', 'ASC');

        return $q->execute();
    }

    /**
     * gets an instance of sugarconfig
     *
     * @return SugarConfig
     */
    protected function getSugarConfig()
    {
        return SugarConfig::getInstance();
    }

    /**
     * generates new leaf node
     * @param $node
     * @param $index
     * @return stdClass
     */
    protected function generateNewLeaf($node, $index)
    {
        $returnObj = new \stdClass();
        $returnObj->id = $node['id'];
        $returnObj->type = $node['type'];
        $returnObj->data = $node['name'];
        $returnObj->state = ($node['type'] == 'product') ? '' : 'closed';
        $returnObj->index = $index;

        return $returnObj;
    }

    /**
     * @param $filter
     * @return mixed[][]
     * @deprecated
     */
    protected function getFilteredTreeData($filter)
    {
        $filter = "%$filter%";
        $unionFilter = 'and name like ? ';

        return $this->getTreeData($unionFilter, $unionFilter, [$filter, $filter]);
    }

    /**
     * @param $root
     * @return mixed[][]
     * @deprecated
     */
    protected function getRootedTreeData($root)
    {
        $union1Root = '';
        $union2Root = '';

        if ($root == null) {
            $union1Root = 'and parent_id is null ';
            $union2Root = 'and category_id is null ';
            $params = [];
        } else {
            $union1Root = 'and parent_id = ? ';
            $union2Root = 'and category_id = ? ';
            $params = [$root, $root];
        }

        return $this->getTreeData($union1Root, $union2Root, $params);
    }

    /**
     * Gets the tree data
     *
     * @param string $union1Filter
     * @param string $union2Filter
     * @param array $params Query parameters
     *
     * @return mixed[][]
     * @deprecated
     */
    protected function getTreeData($union1Filter, $union2Filter, array $params)
    {
        $q = "select id, name, 'category' as type from product_categories " .
            'where deleted = 0 ' .
            $union1Filter .
            'union all ' .
            "select id, name, 'product' as type from product_templates " .
            'where deleted = 0 ' .
            $union2Filter .
            'order by type, name';

        $conn = $this->getDBConnection();
        $result = $conn->executeQuery($q, $params);
        return $result->fetchAllAssociative();
    }

    /**
     * Gets the DB connection for the query
     * @return \Sugarcrm\Sugarcrm\Dbal\Connection
     */
    public function getDBConnection()
    {
        $db = DBManagerFactory::getInstance();
        return $db->getConnection();
    }
}
