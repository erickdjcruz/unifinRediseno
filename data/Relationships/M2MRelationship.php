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
 * Represents a many to many relationship that is table based.
 * @api
 */
class M2MRelationship extends SugarRelationship
{
    public $type = 'many-to-many';

    /**#@+
     * @var array|false
     */
    protected $lhsLinkDef;
    protected $rhsLinkDef;

    /**#@-*/

    public function __construct(array $def)
    {
        $this->def = $def;
        $this->name = $def['name'];

        $lhsModule = $def['lhs_module'];
        $this->lhsLinkDef = $this->getLinkedDefForModuleByRelationship($lhsModule);

        if (isset($this->lhsLinkDef['name'])) {
            $this->lhsLink = $this->lhsLinkDef['name'];
        }

        $rhsModule = $def['rhs_module'];
        $this->rhsLinkDef = $this->getLinkedDefForModuleByRelationship($rhsModule);

        if (isset($this->rhsLinkDef['name'])) {
            $this->rhsLink = $this->rhsLinkDef['name'];
        }

        $this->self_referencing = $lhsModule == $rhsModule;
    }

    /**
     * Find the link entry for a particular relationship and module.
     *
     * @param $module
     * @return array|bool
     */
    public function getLinkedDefForModuleByRelationship($module)
    {
        $results = VardefManager::getLinkFieldForRelationship($module, BeanFactory::getObjectName($module), $this->name);
        //Only a single link was found
        if (isset($results['name'])) {
            return $results;
        } //Multiple links with same relationship name
        elseif (is_array($results)) {
            return $this->getMostAppropriateLinkedDefinition($results);
        } else {
            return false;
        }
    }

    /**
     * Find the most 'appropriate' link entry for a relationship/module in which there are multiple link entries with the
     * same relationship name.
     *
     * @param $links
     * @return bool
     */
    protected function getMostAppropriateLinkedDefinition($links)
    {
        //First priority is to find a link name that matches the relationship name
        foreach ($links as $link) {
            if (isset($link['name']) && $link['name'] == $this->name) {
                return $link;
            }
        }
        //Next would be a relationship that has a side defined
        foreach ($links as $link) {
            if (isset($link['id_name'])) {
                return $link;
            }
        }
        //Unable to find an appropriate link, guess and use the first one
        $GLOBALS['log']->info("Unable to determine best appropriate link for relationship {$this->name}");
        return $links[0];
    }

    /**
     * @param  $lhs SugarBean left side bean to add to the relationship.
     * @param  $rhs SugarBean right side bean to add to the relationship.
     * @param array $additionalFields key=>value pairs of fields to save on the relationship
     * @return boolean true if successful
     * @internal param $ {array} $additionalFields defaults to empty array
     */
    public function add($lhs, $rhs, $additionalFields = [])
    {
        $lhsLinkName = $this->lhsLink;
        $rhsLinkName = $this->rhsLink;

        if (empty($lhsLinkName) || empty($rhsLinkName)) {
            // no need to continue as we missed one of the link names
            return false;
        }

        if (empty($lhs->$lhsLinkName) && !$lhs->load_relationship($lhsLinkName)) {
            $lhsClass = get_class($lhs);
            $GLOBALS['log']->fatal("could not load LHS $lhsLinkName in $lhsClass");
            return false;
        }
        if (empty($rhs->$rhsLinkName) && !$rhs->load_relationship($rhsLinkName)) {
            $rhsClass = get_class($rhs);
            $GLOBALS['log']->fatal("could not load RHS $rhsLinkName in $rhsClass");
            return false;
        }

        // Test to see if the relationship already exists before attempting to
        // add it again.
        $isUpdate = false;
        $currentRow = $this->relationship_exists($lhs, $rhs, true);
        if (!$currentRow) {
            if (empty($_SESSION['disable_workflow']) || $_SESSION['disable_workflow'] != 'Yes') {
                $this->callBeforeAdd($lhs, $rhs, $lhsLinkName);
                $this->callBeforeAdd($rhs, $lhs, $rhsLinkName);
            }
        } else {
            $this->callBeforeUpdate($lhs, $rhs, $lhsLinkName);
            $this->callBeforeUpdate($rhs, $lhs, $rhsLinkName);
            $isUpdate = true;
        }

        //Many to many has no additional logic, so just add a new row to the table and notify the beans.
        $dataToInsert = $this->getRowToInsert($lhs, $rhs, $additionalFields);
        /**
         * We need to do a complete check against all fields in the relationship to ensure that
         * an update occurs for any additional relationship fields
         * */
        if (!$currentRow || !$this->compareRow($currentRow, $dataToInsert)) {
            $this->addRow($dataToInsert);

            if ($this->self_referencing) {
                $this->addSelfReferencing($lhs, $rhs, $additionalFields);
            }
            if (!$isUpdate && (empty($_SESSION['disable_workflow']) || $_SESSION['disable_workflow'] != 'Yes')) {
                $lhs->$lhsLinkName->resetLoaded();
                $rhs->$rhsLinkName->resetLoaded();

                $this->callAfterAdd($lhs, $rhs, $lhsLinkName);
                $this->callAfterAdd($rhs, $lhs, $rhsLinkName);
            }
        }

        if ($isUpdate) {
            $this->callAfterUpdate($lhs, $rhs, $lhsLinkName);
            $this->callAfterUpdate($rhs, $lhs, $rhsLinkName);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function addRow(array $row)
    {
        //Need to manage the primary flag if we are going to be updating/inserting data.
        if (!empty($this->def['primary_flag_column']) && $row[$this->def['primary_flag_column']]) {
            $rhsKey = $this->def['join_key_rhs'];
            $lhsKey = $this->def['join_key_lhs'];
            // The primary flag is true, that means we need to block
            // out the old primary flag
            if ($this->def['primary_flag_side'] == 'rhs') {
                $where = [$rhsKey => $row[$rhsKey]];
            } else {
                $where = [$lhsKey => $row[$lhsKey]];
            }
            DBManagerFactory::getConnection()->update(
                $this->getRelationshipTable(),
                [$this->def['primary_flag_column'] => 0],
                $where
            );
        }
        return parent::addRow($row);
    }

    /**
     * Used to check for duplicate rows for this relationship. Compares all fields rather than just id's.
     *
     * @param       $currentRow existing row
     * @param       $dataToInsert new row to be inserted
     * @param array $ignoreFields fields to ignore for duplicate comparison
     *
     * @return bool true if rows are identical.
     */
    protected function compareRow($currentRow, $dataToInsert, $ignoreFields = ['id', 'date_modified'])
    {
        foreach ($dataToInsert as $field => $value) {
            if (!safeInArray($field, $ignoreFields) && (!isset($currentRow[$field]) || $currentRow[$field] != $value)) {
                return false;
            }
        }

        return true;
    }

    protected function getRowToInsert($lhs, $rhs, $additionalFields = [])
    {
        $row = array_merge($additionalFields, [
            'id' => create_guid(),
            $this->def['join_key_lhs'] => $lhs->id,
            $this->def['join_key_rhs'] => $rhs->id,
            'date_modified' => TimeDate::getInstance()->nowDb(),
            'deleted' => 0,
        ]);

        if (!$this->ignore_role_filter) {
            foreach ($this->getRelationshipRoleColumns() as $column => $value) {
                $row[$column] = $value;
            }
        }

        if (!empty($this->def['fields'])) {
            foreach ($this->def['fields'] as $fieldDef) {
                if (!empty($fieldDef['name']) && !isset($row[$fieldDef['name']]) && !empty($fieldDef['default'])) {
                    $row[$fieldDef['name']] = $fieldDef['default'];
                }
            }
        }
        if (!empty($this->def['primary_flag_column'])) {
            if (!isset($additionalFields[$this->def['primary_flag_column']])) {
                if (!empty($this->def['primary_flag_default'])) {
                    $row[$this->def['primary_flag_column']] = true;
                } else {
                    $row[$this->def['primary_flag_column']] = false;
                }
            } else {
                $row[$this->def['primary_flag_column']] = $additionalFields[$this->def['primary_flag_column']];
            }
        }

        return $row;
    }

    /**
     * Adds the reversed version of this relationship to the table so that it can be accessed from either side equally
     * @param $lhs
     * @param $rhs
     * @param array $additionalFields
     * @return boolean
     */
    protected function addSelfReferencing($lhs, $rhs, $additionalFields = [])
    {
        if ($rhs->id != $lhs->id) {
            $dataToInsert = $this->getRowToInsert($rhs, $lhs, $additionalFields);
            $this->addRow($dataToInsert);
        }
        return true;
    }

    public function remove($lhs, $rhs)
    {
        if (!($lhs instanceof SugarBean) || !($rhs instanceof SugarBean)) {
            $GLOBALS['log']->fatal('LHS and RHS must be beans');
            return false;
        }
        $lhsLinkName = $this->lhsLink;
        $rhsLinkName = $this->rhsLink;

        if (empty($lhsLinkName) || empty($rhsLinkName)) {
            // no need to continue as we missed one of the link names
            return false;
        }

        if (!($lhs instanceof SugarBean)) {
            $GLOBALS['log']->fatal('LHS is not a SugarBean object');
            return false;
        }
        if (!($rhs instanceof SugarBean)) {
            $GLOBALS['log']->fatal('RHS is not a SugarBean object');
            return false;
        }
        if (empty($lhs->$lhsLinkName) && !$lhs->load_relationship($lhsLinkName)) {
            $GLOBALS['log']->fatal("could not load LHS $lhsLinkName");
            return false;
        }
        if (empty($rhs->$rhsLinkName) && !$rhs->load_relationship($rhsLinkName)) {
            $GLOBALS['log']->fatal("could not load RHS $rhsLinkName");
            return false;
        }

        if (empty($_SESSION['disable_workflow']) || $_SESSION['disable_workflow'] != 'Yes') {
            if ($lhs->$lhsLinkName instanceof Link2) {
                $this->callBeforeDelete($lhs, $rhs, $lhsLinkName);
            }

            if ($rhs->$rhsLinkName instanceof Link2) {
                $this->callBeforeDelete($rhs, $lhs, $rhsLinkName);
            }
        }

        $dataToRemove = [
            $this->def['join_key_lhs'] => $lhs->id,
            $this->def['join_key_rhs'] => $rhs->id,
        ];

        $this->removeRow($dataToRemove);

        if ($this->self_referencing) {
            $this->removeSelfReferencing($lhs, $rhs);
        }

        if (empty($_SESSION['disable_workflow']) || $_SESSION['disable_workflow'] != 'Yes') {
            if ($lhs->$lhsLinkName instanceof Link2) {
                $this->callAfterDelete($lhs, $rhs, $lhsLinkName);
            }

            if ($rhs->$rhsLinkName instanceof Link2) {
                $this->callAfterDelete($rhs, $lhs, $rhsLinkName);
            }
        }

        return $this->setNewPrimary($dataToRemove);
    }

    /**
     * Removes the reversed version of this relationship
     * @param $lhs
     * @param $rhs
     * @param array $additionalFields
     * @return boolean
     */
    protected function removeSelfReferencing($lhs, $rhs, $additionalFields = [])
    {
        if ($rhs->id != $lhs->id) {
            $dataToRemove = [
                $this->def['join_key_lhs'] => $rhs->id,
                $this->def['join_key_rhs'] => $lhs->id,
            ];
            $this->removeRow($dataToRemove);
        }
        return true;
    }

    /**
     * Sets a new primary record for this relationship, if necessary
     * @param array $rowData
     * @return boolean
     */
    protected function setNewPrimary($rowData)
    {
        if (empty($this->def['primary_flag_column'])
            || empty($this->def['primary_flag_default'])) {
            // No primary flag, don't need to worry about a new primary record
            return true;
        }

        $conn = DBManagerFactory::getInstance()->getConnection();
        $query = $conn->createQueryBuilder();
        $expr = $query->expr();
        $query->select('id', $this->def['primary_flag_column'])
            ->from($this->getRelationshipTable())
            ->where($expr->eq($this->def['primary_flag_column'], 1));
        foreach ($rowData as $field => $value) {
            $query->andWhere($expr->eq($field, $query->createPositionalParameter($value)));
        }
        $oldRow = $query->execute()->fetchAssociative();

        if (!empty($oldRow)) {
            $joinColumnName = $this->def['join_key_' . $this->def['primary_flag_side']];
            // Deleted the primary record, and we need a default primary
            $conn->update(
                $this->getRelationshipTable(),
                [$this->def['primary_flag_column'] => 0],
                [$joinColumnName => $rowData[$joinColumnName]]
            );
            $id = $conn->executeQuery(
                sprintf(
                    'SELECT id FROM %s WHERE %s = ? AND deleted = 0',
                    $this->getRelationshipTable(),
                    $joinColumnName
                ),
                [$rowData[$joinColumnName]]
            )->fetchOne();

            if ($id) {
                $conn->update($this->getRelationshipTable(), [$this->def['primary_flag_column'] => 1], ['id' => $id]);
            }
        }
        return true;
    }

    /**
     * @param  $link Link2 loads the relationship for this link.
     * @return array
     */
    public function load($link, $params = [])
    {
        $result = $this->getSugarQuery($link, $params)->execute();
        $idField = $this->linkIsLHS($link) ? $this->def['join_key_rhs'] : $this->def['join_key_lhs'];
        $rows = [];
        foreach ($result as $row) {
            if (empty($row['id']) && empty($row[$idField])) {
                continue;
            }
            $id = empty($row['id']) ? $row[$idField] : $row['id'];
            $rows[$id] = $row;
        }
        return ['rows' => $rows];
    }

    protected function linkIsLHS($link)
    {
        return $link->getSide() == REL_LHS;
    }

    /**
     * @inheritdoc
     */
    public function getQuery($link, $params = [])
    {
        if ($this->linkIsLHS($link)) {
            $knownKey = $this->def['join_key_lhs'];
            $targetKey = $this->def['join_key_rhs'];
            $relatedSeed = BeanFactory::getDefinition($this->getRHSModule());
            $relatedSeedKey = $this->def['rhs_key'];
            $whereTable = '';
            if (empty($params['right_join_table_alias'])) {
                if (!empty($relatedSeed)) {
                    $whereTable = $relatedSeed->table_name;
                }
            } else {
                $whereTable = $params['right_join_table_alias'];
            }
        } else {
            $knownKey = $this->def['join_key_rhs'];
            $targetKey = $this->def['join_key_lhs'];
            $relatedSeed = BeanFactory::getDefinition($this->getLHSModule());
            $relatedSeedKey = $this->def['lhs_key'];
            $whereTable = '';
            if (empty($params['left_join_table_alias'])) {
                if (!empty($relatedSeed)) {
                    $whereTable = $relatedSeed->table_name;
                }
            } else {
                $whereTable = $params['left_join_table_alias'];
            }
        }
        $rel_table = $this->getJoinTable($link, $params);
        $relTableAlias = $this->getRelationshipTable();
        $db = $link->getFocus()->db;
        $where = sprintf(
            '%s.%s = %s %s',
            $relTableAlias,
            $knownKey,
            $db->quoted($link->getFocus()->id),
            $this->getRoleWhere()
        );

        //Add any optional where clause
        if (!empty($params['where']) && !empty($whereTable)) {
            // Build up the where clause
            $add_where = is_string($params['where']) ?
                $params['where'] :
                $this->getOptionalWhereClause($params['where'], $whereTable, $relatedSeed);

            if (!empty($add_where)) {
                $where .= " AND $relTableAlias.$targetKey=$whereTable.id AND $add_where";
            }
        }

        $deleted = !empty($params['deleted']) ? 1 : 0;
        $from = $rel_table !== $relTableAlias ? "$rel_table $relTableAlias " : "$relTableAlias ";
        if (!empty($params['enforce_teams']) && $relatedSeed !== false) {
            if ($relTableAlias != $relatedSeed->table_name) {
                $from .= "JOIN {$relatedSeed->table_name} ON {$relTableAlias}.{$targetKey} = {$relatedSeed->table_name}.{$relatedSeedKey} ";
            }
            $relatedSeed->add_team_security_where_clause($from);
        }
        if ((!empty($params['where']) || !empty($params['orderby'])) && !empty($whereTable)) {
            $from .= " LEFT JOIN $whereTable on $relTableAlias.$targetKey=$whereTable.id";
            if (isset($relatedSeed->custom_fields)) {
                $customJoin = $relatedSeed->custom_fields->getJOIN();
                $from .= $customJoin ? $customJoin['join'] : '';
            }
        }

        $select = "$targetKey id";
        if (!($params['skipAdditionalFields'] ?? false)) {
            foreach ($this->getAdditionalFields() as $field => $def) {
                $select .= ", $relTableAlias.$field";
            }
        }

        if (empty($params['return_as_array'])) {
            $orderby = (!empty($params['orderby']) && !empty($whereTable)) ? " ORDER BY $whereTable.{$params['orderby']}" : '';
            $query = "SELECT $select FROM $from WHERE $where AND $relTableAlias.deleted=$deleted $orderby";
            //Limit is not compatible with return_as_array
            if (!empty($params['limit']) && $params['limit'] > 0) {
                $offset = $params['offset'] ?? 0;
                $query = $db->limitQuery($query, $offset, $params['limit'], false, '', false);
            }
            return $query;
        } else {
            return [
                'select' => "SELECT $select",
                'from' => "FROM $from",
                'where' => "WHERE $where AND $relTableAlias.deleted=$deleted",
            ];
        }
    }

    /**
     * Get SugarQuery for loading relationship records
     *
     * @param Link2 $link
     * @param array $params
     *
     * @return SugarQuery
     * @throws \Exception
     */
    protected function getSugarQuery(Link2 $link, array $params = [])
    {
        if ($this->linkIsLHS($link)) {
            $knownKey = $this->def['join_key_lhs'];
            $targetKey = $this->def['join_key_rhs'];
            $relatedSeed = BeanFactory::getDefinition($this->getRHSModule());
            if (empty($params['right_join_table_alias'])) {
                if (!empty($relatedSeed)) {
                    $whereTable = $relatedSeed->table_name;
                }
            } else {
                $whereTable = $params['right_join_table_alias'];
            }
        } else {
            $knownKey = $this->def['join_key_rhs'];
            $targetKey = $this->def['join_key_lhs'];
            $relatedSeed = BeanFactory::getDefinition($this->getLHSModule());
            if (empty($params['left_join_table_alias'])) {
                if (!empty($relatedSeed)) {
                    $whereTable = $relatedSeed->table_name;
                }
            } else {
                $whereTable = $params['left_join_table_alias'];
            }
        }
        if (empty($whereTable)) {
            $message = 'Related table is undefined for ' . $this->name . ' relationship';
            $GLOBALS['log']->fatal($message);
            throw new \Exception($message);
        }
        $rel_table = $this->getJoinTable($link, $params);
        $relTableAlias = $this->getRelationshipTable();
        $query = new SugarQuery();
        $query->from($relatedSeed, ['team_security' => !empty($params['enforce_teams']), 'add_deleted' => false]);
        $query->joinTable($rel_table, ['alias' => $relTableAlias])
            ->on()->equalsField("$relTableAlias.$targetKey", "$whereTable.id");

        $query->select()->selectReset();
        $query->select([["$relTableAlias.$targetKey", 'id']]); // id is an alias here
        foreach ($this->getAdditionalFields() as $field => $def) {
            $query->select()->addField("$relTableAlias.$field");
        }

        $ownerField = $relatedSeed->getOwnerField();
        if ($ownerField) {
            $query->select([[$ownerField, 'related_owner_id']]);
        }

        $query->where()->equals("$relTableAlias.$knownKey", $link->getFocus()->id);
        $this->buildSugarQueryRoleWhere($query, $relTableAlias);

        //Add any optional where clause
        if (!empty($params['where']) && !empty($whereTable)) {
            if (is_string($params['where'])) {
                $query->where()->addRaw($params['where']);
            } else {
                // Build up the where clause
                $optionalWhereAdded = $this->buildOptionalQueryWhere(
                    $query,
                    $params['where'],
                    $whereTable,
                    $relatedSeed
                );

                if ($optionalWhereAdded) {
                    $query->where()->equalsField("$relTableAlias.$targetKey", "$whereTable.id");
                }
            }
        }

        $deleted = empty($params['deleted']) ? 0 : 1;
        $query->where()->equals("$relTableAlias.deleted", $deleted);

        if (!empty($params['orderby']) && !empty($whereTable)) {
            $orderByFields = $this->getOrderByFields($params['orderby']);
            foreach ($orderByFields as $field => $direction) {
                $query->orderBy("$whereTable.$field", $direction);
            }
        }
        if (!empty($params['limit']) && ($params['limit'] > 0)) {
            $query->limit($params['limit']);
        }
        if (isset($params['offset'])) {
            $query->offset($params['offset']);
        }

        return $query;
    }

    public function getJoin($link, $params = [], $return_array = false)
    {
        $linkIsLHS = $link->getSide() == REL_LHS;
        if ($linkIsLHS) {
            $startingTable = (empty($params['left_join_table_alias']) ? $link->getFocus()->table_name : $params['left_join_table_alias']);
        } else {
            $startingTable = (empty($params['right_join_table_alias']) ? $link->getFocus()->table_name : $params['right_join_table_alias']);
        }

        $self_join = ($this->def['lhs_module'] == $this->def['rhs_module']) ? true : false;

        $startingKey = $linkIsLHS ? $this->def['lhs_key'] : $this->def['rhs_key'];
        // Adding a check for badly defined relationships for self referencing relationship
        if ($self_join && !empty($this->lhsLinkDef['id_name']) && $this->lhsLinkDef['id_name'] != $this->def['join_key_lhs']) {
            $startingJoinKey = $linkIsLHS ? $this->lhsLinkDef['id_name'] : $this->rhsLinkDef['id_name'];
        } else {
            $startingJoinKey = $linkIsLHS ? $this->def['join_key_lhs'] : $this->def['join_key_rhs'];
        }
        $joinTable = $this->getJoinTable($link, $params);
        $joinTableWithAlias = $joinTable;
        // Adding a check for badly defined relationships for self referencing relationship
        if ($self_join && !empty($this->lhsLinkDef['id_name']) && $this->lhsLinkDef['id_name'] != $this->def['join_key_lhs']) {
            $joinKey = $linkIsLHS ? $this->rhsLinkDef['id_name'] : $this->lhsLinkDef['id_name'];
        } else {
            $joinKey = $linkIsLHS ? $this->def['join_key_rhs'] : $this->def['join_key_lhs'];
        }
        $targetTable = $linkIsLHS ? $this->def['rhs_table'] : $this->def['lhs_table'];
        $targetTableWithAlias = $targetTable;
        $targetKey = $linkIsLHS ? $this->def['rhs_key'] : $this->def['lhs_key'];
        $join_type = $params['join_type'] ?? ' INNER JOIN ';

        $join = '';

        //Set up any table aliases required
        if (!empty($params['join_table_link_alias'])) {
            $joinTableWithAlias = $joinTable . ' ' . $params['join_table_link_alias'];
            $joinTable = $params['join_table_link_alias'];
        }
        if (!empty($params['join_table_alias'])) {
            $targetTableWithAlias = $targetTable . ' ' . $params['join_table_alias'];
            $targetTable = $params['join_table_alias'];
        }

        $join1 = "$startingTable.$startingKey=$joinTable.$startingJoinKey";
        $join2 = "$targetTable.$targetKey=$joinTable.$joinKey";


        //First join the relationship table
        $join .= "$join_type $joinTableWithAlias ON $join1 AND $joinTable.deleted=0\n";
        if (empty($params['ignore_role'])) {
            //Next add any role filters
            $join .= $this->getRoleWhere($joinTable) . "\n";
        }
        //Then finally join the related module's table
        $join .= "$join_type $targetTableWithAlias ON $join2 AND $targetTable.deleted=0\n";

        if ($return_array) {
            return [
                'join' => $join,
                'type' => $this->type,
                'rel_key' => $joinKey,
                'join_tables' => [$joinTable, $targetTable],
                'where' => '',
                'select' => "$targetTable.id",
            ];
        }
        return $join;
    }

    /**
     * Build a Join using an existing SugarQuery Object
     * @param Link2 $link
     * @param SugarQuery $sugar_query
     * @param array $options array of additional paramters. Possible parameters include
     *  - 'myAlias' String name of starting table alias
     *  - 'joinTableAlias' String alias to use for the related table in the final result
     *  - 'reverse' Boolean true if this join should be built in reverse for subpanel style queries where the select is
     *              on the related table
     *  - 'ignoreRole' Boolean true if the role column of the relationship should be ignored for this join .
     * @return SugarQuery_Builder_Join[]
     */
    public function buildJoinSugarQuery(Link2 $link, $sugar_query, $options)
    {
        $linkIsLHS = $link->getSide() == REL_LHS;
        if (!empty($options['reverse'])) {
            $linkIsLHS = !$linkIsLHS;
        }

        $startingTable = $linkIsLHS ? $this->def['lhs_table'] : $this->def['rhs_table'];
        if (!empty($options['myAlias'])) {
            $startingTable = $options['myAlias'];
        }

        $startingKey = $linkIsLHS ? $this->def['lhs_key'] : $this->def['rhs_key'];

        $startingJoinKey = $linkIsLHS ? $this->def['join_key_lhs'] : $this->def['join_key_rhs'];

        $joinTable = $this->getJoinTable($link, $options);

        $joinKey = $linkIsLHS ? $this->def['join_key_rhs'] : $this->def['join_key_lhs'];

        $targetTable = $linkIsLHS ? $this->def['rhs_table'] : $this->def['lhs_table'];
        $targetKey = $linkIsLHS ? $this->def['rhs_key'] : $this->def['lhs_key'];
        $targetModule = $linkIsLHS ? $this->def['rhs_module'] : $this->def['lhs_module'];
        $join_type = $options['joinType'] ?? 'INNER';

        $joinTable_alias = DBManagerFactory::getInstance()
            ->getValidDBName($this->def['name'], true, 'alias');
        $targetTable_alias = !empty($options['joinTableAlias']) ? $options['joinTableAlias'] : $targetTable;

        $relTableJoin = $sugar_query->joinTable($joinTable, ['alias' => $joinTable_alias, 'joinType' => $join_type, 'linkingTable' => true,])
            ->on()->equalsField("{$startingTable}.{$startingKey}", "{$joinTable_alias}.{$startingJoinKey}")
            ->equals("{$joinTable_alias}.deleted", '0');

        $targetTableJoin = $sugar_query->joinTable($targetTable, [
            'alias' => $targetTable_alias,
            'joinType' => $join_type,
            'bean' => BeanFactory::getDefinition($targetModule),
        ])
            ->on()->equalsField("{$targetTable_alias}.{$targetKey}", "{$joinTable_alias}.{$joinKey}")
            ->equals("{$targetTable_alias}.deleted", '0');

        $sugar_query->join[$targetTable_alias]->relationshipTableAlias = $joinTable_alias;

        if (empty($options['ignoreRole'])) {
            $this->buildSugarQueryRoleWhere($sugar_query, $joinTable_alias);
        }

        $this->addCustomToSugarQuery($sugar_query, $options, $linkIsLHS, $targetTable_alias);

        return [$joinTable_alias => $relTableJoin, $targetTable_alias => $targetTableJoin];
    }

    protected function addCustomToSugarQuery($sugar_query, $options, $linkIsLHS, $targetTable_alias)
    {
        if (!empty($options['includeCustom'])) {
            $bean = BeanFactory::getDefinition(
                $linkIsLHS ? $this->def['rhs_module'] : $this->def['lhs_module']
            );
            if (!empty($bean) && $bean->hasCustomFields()) {
                $table_cstm = $bean->get_custom_table_name();
                $alias_cstm = "{$targetTable_alias}_cstm";
                $alias_cstm = $bean->db->getValidDBName($alias_cstm, false, 'alias');
                $sugar_query->joinTable($table_cstm, ['alias' => $alias_cstm, 'joinType' => 'LEFT', 'linkingTable' => true])
                    ->on()->equalsField("$alias_cstm.id_c", "{$targetTable_alias}.id");
            }
        }
    }


    /**
     * Similar to getQuery or Get join, except this time we are starting from the related table and
     * searching for items with id's matching the $link->focus->id
     * @param  $link
     * @param array $params
     * @param bool $return_array
     * @return String|Array
     */
    public function getSubpanelQuery($link, $params = [], $return_array = false)
    {
        $targetIsLHS = $link->getSide() == REL_RHS;
        $startingTable = $targetIsLHS ? $this->def['lhs_table'] : $this->def['rhs_table'];
        ;
        $startingKey = $targetIsLHS ? $this->def['lhs_key'] : $this->def['rhs_key'];
        $startingJoinKey = $targetIsLHS ? $this->def['join_key_lhs'] : $this->def['join_key_rhs'];
        $joinTable = $this->getJoinTable($link, $params);
        $joinTableWithAlias = $this->getRelationshipTable();
        $joinKey = $targetIsLHS ? $this->def['join_key_rhs'] : $this->def['join_key_lhs'];
        $targetKey = $targetIsLHS ? $this->def['rhs_key'] : $this->def['lhs_key'];
        $join_type = $params['join_type'] ?? ' INNER JOIN ';

        $query = '';

        //Set up any table aliases required
        if (!empty($params['join_table_link_alias'])) {
            $joinTableWithAlias = $joinTable . ' ' . $params['join_table_link_alias'];
            $joinTable = $params['join_table_link_alias'];
        }

        $where = "$startingTable.$startingKey=$joinTable.$startingJoinKey AND $joinTable.$joinKey='{$link->getFocus()->$targetKey}'";

        //Check if we should ignore the role filter.
        $ignoreRole = !empty($params['ignore_role']);

        //First join the relationship table
        $query .= "$join_type $joinTableWithAlias ON $where AND $joinTable.deleted=0\n"
            //Next add any role filters
            . $this->getRoleWhere($joinTable, $ignoreRole) . "\n";

        if (!empty($params['return_as_array'])) {
            $return_array = true;
        }
        if ($return_array) {
            return [
                'join' => $query,
                'type' => $this->type,
                'rel_key' => $joinKey,
                'join_tables' => [$joinTable],
                'where' => '',
                'select' => ' ',
            ];
        }
        return $query;
    }

    protected function getRoleFilterForJoin()
    {
        $ret = '';
        if (!$this->ignore_role_filter) {
            $db = DBManagerFactory::getInstance();
            foreach ($this->getRelationshipRoleColumns() as $column => $value) {
                $ret .= " AND {$this->getRelationshipTable()}.$column";
                //role column value.
                if (empty($value)) {
                    $ret .= ' IS NULL';
                } else {
                    $ret .= '=' . $db->quoted($value);
                }
            }
            $ret .= "\n";
        }
        if (!empty($this->def['primary_flag_column'])
            && !empty($this->primaryOnly)) {
            $field = $this->getRelationshipTable() . '.' . $this->def['primary_flag_column'];

            $ret .= " AND {$field} = 1 ";
        }

        return $ret;
    }

    /**
     * @param  $lhs
     * @param  $rhs
     * @param bool $returnRow
     * @return array|bool|string
     */
    public function relationship_exists($lhs, $rhs, $returnRow = false)
    {
        $query = DBManagerFactory::getConnection()->createQueryBuilder();
        $expr = $query->expr();

        $query
            ->from($this->getRelationshipTable())
            ->where($expr->eq($this->join_key_lhs, $query->createPositionalParameter($lhs->id)))
            ->andWhere($expr->eq($this->join_key_rhs, $query->createPositionalParameter($rhs->id)));

        $this->addRoleWhereToQuery($query);

        $query->andWhere($expr->eq('deleted', 0));

        if ($returnRow) {
            $result = $query->select('*')->execute()->fetchAssociative();
        } else {
            $result = $query->select('id')->execute()->fetchOne();
        }
        return $result;
    }

    /**
     * @return Array - set of fields that uniquely identify an entry in this relationship
     */
    protected function getAlternateKeyFields()
    {
        $fields = [$this->join_key_lhs, $this->join_key_rhs];

        //Roles can allow for multiple links between two records with different roles
        if (!$this->ignore_role_filter) {
            $fields = array_merge($fields, $this->getRelationshipRoleColumns());
        }

        return $fields;
    }

    /**
     * Returns relationship table by default.
     * Overridden by PersonM2MRelationship.
     *
     * @param Link2 $link
     * @param array $options
     * @return string
     */
    protected function getJoinTable(Link2 $link, $options)
    {
        return $this->getRelationshipTable();
    }

    public function getRelationshipTable()
    {
        if (!empty($this->def['table'])) {
            return $this->def['table'];
        } elseif (!empty($this->def['join_table'])) {
            return $this->def['join_table'];
        }

        return false;
    }

    public function getFields()
    {
        global $dictionary;

        if (!empty($this->def['fields'])) {
            return $this->def['fields'];
        }

        // in case if relationship uses another entity's table
        $table = $this->getRelationshipTable();
        if (isset($dictionary[$table])) {
            return $dictionary[$table]['fields'];
        }

        return $this->getStandardFields();
    }

    protected function getStandardFields()
    {
        $fields = [
            'id' => ['name' => 'id'],
            'date_modified' => ['name' => 'date_modified'],
            'modified_user_id' => ['name' => 'modified_user_id'],
            'created_by' => ['name' => 'created_by'],
            $this->def['join_key_lhs'] => ['name' => $this->def['join_key_lhs']],
            $this->def['join_key_rhs'] => ['name' => $this->def['join_key_rhs']],
        ];
        foreach ($this->getRelationshipRoleColumns() as $column => $value) {
            $fields[$column] = ['name' => $column];
        }
        $fields['deleted'] = ['name' => 'deleted'];

        return $fields;
    }

    protected function getAdditionalFields()
    {
        return array_diff_key($this->getFields(), $this->getStandardFields());
    }

    /**
     * {@inheritdoc}
     */
    public function getType($side)
    {
        return REL_TYPE_MANY;
    }
}
