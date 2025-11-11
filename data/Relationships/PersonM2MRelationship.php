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
 * Custom relationship class for Person modules to support External Users.
 */
class PersonM2MRelationship extends M2MRelationship
{
    /**
     * DB
     *
     * @var DBManager
     */
    protected $db;

    /**
     * @inheritdoc
     * @param array $def
     */
    public function __construct(array $def)
    {
        parent::__construct($def);
        $this->db = DBManagerFactory::getInstance();
    }

    /**
     * Checks if a table is empty.
     * @param string $table
     * @param boolean $addDelete
     * @return boolean
     */
    protected function isTableEmpty($table, $addDelete = true)
    {
        $qb = $this->db->getConnection()->createQueryBuilder();
        $where = $addDelete ? 'deleted=0' : '1=1';
        $count = $qb->select('count(id)')->from($table)->where($where)->execute()->fetchOne();
        return $count < 1;
    }

    /**
     * Checks if lhs module is type of Person.
     * @return boolean
     */
    protected function isLHSPerson()
    {
        $lhsBean = BeanFactory::getBean($this->def['lhs_module']);
        return !empty($lhsBean) && is_subclass_of($lhsBean, 'Person');
    }

    /**
     * Returns corresponding relationship def if exists.
     * @return array|NULL
     */
    protected function getExternalUserRelationshipDef()
    {
        $lhsBean = BeanFactory::getBean($this->def['lhs_module']);
        if (empty($lhsBean)) {
            return null;
        }
        $externalUserRelationships = is_subclass_of($lhsBean, 'Person') ?
            SugarRelationshipFactory::getInstance()
                ->getRelationshipsBetweenModules('ExternalUsers', $this->def['rhs_module'], 'many-to-many') :
            SugarRelationshipFactory::getInstance()
                ->getRelationshipsBetweenModules('ExternalUsers', $this->def['lhs_module'], 'many-to-many');
        if (empty($externalUserRelationships)) {
            return null;
        }
        return SugarRelationshipFactory::getInstance()
            ->getRelationshipDef($externalUserRelationships[0]);
    }

    /**
     * {@inheritDoc}
     * @see M2MRelationship::getJoinTable()
     */
    protected function getJoinTable(Link2 $link, $options)
    {
        $table = $this->getRelationshipTable();
        if (!$this->hasExternalRecords()) {
            return $table;
        }
        $externalUserRelationshipDef = $this->getExternalUserRelationshipDef();
        $userKey = $externalUserRelationshipDef['lhs_module'] === 'ExternalUsers' ?
            $externalUserRelationshipDef['join_key_lhs'] : $externalUserRelationshipDef['join_key_rhs'];
        $objectKey = $externalUserRelationshipDef['lhs_module'] === 'ExternalUsers' ?
            $externalUserRelationshipDef['join_key_rhs'] : $externalUserRelationshipDef['join_key_lhs'];
        if ($this->isLHSPerson()) {
            $targetUserKey = $this->def['join_key_lhs'];
            $targetObjectKey = $this->def['join_key_rhs'];
            $personModule = $this->db->quoted($this->def['lhs_module']);
        } else {
            $targetUserKey = $this->def['join_key_rhs'];
            $targetObjectKey = $this->def['join_key_lhs'];
            $personModule = $this->db->quoted($this->def['rhs_module']);
        }
        $additionalFields = array_keys($this->getAdditionalFields());
        if (!empty($this->def['primary_flag_column'])) {
            $additionalFields[] = $this->def['primary_flag_column'];
        }
        $roleColumns = array_keys($this->getRelationshipRoleColumns());
        $additionalFields = array_unique(array_merge($additionalFields, $roleColumns));
        $targetAdditionalSelect = implode(',', $additionalFields);
        if ($targetAdditionalSelect) {
            $targetAdditionalSelect .= ', ';
        }
        $additionalSelect = implode(',', array_map(function ($column) {
            return "NULL $column";
        }, $additionalFields));
        if ($additionalSelect) {
            $additionalSelect .= ', ';
        }
        $where = 'eur.deleted=0';
        $targetWhere = 'deleted=0';
        if (!empty($link->focus->id)) {
            $focusId = $this->db->quoted($link->focus->id);
            if (is_subclass_of($link->focus, 'Person')) {
                $targetWhere .= " AND $targetUserKey=$focusId";
                if (!empty($link->focus->external_user_id)) {
                    $userId = $this->db->quoted($link->focus->external_user_id);
                    $where .= " AND eur.$userKey=$userId";
                }
            } else {
                $targetWhere .= " AND $targetObjectKey=$focusId";
                $where .= " AND eur.$objectKey=$focusId";
            }
        }
        $table = <<<SQL
(
  SELECT id, 0 is_external_link, {$targetObjectKey}, {$targetUserKey}, {$targetAdditionalSelect}date_modified, deleted
  FROM {$this->def['join_table']}
  WHERE {$targetWhere}
  UNION
  SELECT eur.id, 1 is_external_link, eur.{$objectKey} {$targetObjectKey}, eu.parent_id {$targetUserKey}, {$additionalSelect}eur.date_modified, eur.deleted
  FROM {$externalUserRelationshipDef['join_table']} eur
  JOIN external_users eu ON eu.id=eur.{$userKey} AND eu.parent_type=$personModule AND eu.deleted=0
  WHERE {$where}
)
SQL;
        return $table;
    }

    /**
     * Checks if there are records created by external users.
     * @return boolean
     */
    public function hasExternalRecords(): bool
    {
        $externalUserRelationshipDef = $this->getExternalUserRelationshipDef();
        return !empty($externalUserRelationshipDef) &&
            !$this->isTableEmpty($externalUserRelationshipDef['join_table']);
    }
}
