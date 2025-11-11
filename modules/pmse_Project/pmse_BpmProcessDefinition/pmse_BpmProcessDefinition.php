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

use Doctrine\DBAL\Connection;

/**
 * THIS CLASS IS FOR DEVELOPERS TO MAKE CUSTOMIZATIONS IN
 */
class pmse_BpmProcessDefinition extends pmse_BpmProcessDefinition_sugar
{
    /**
     * @inheritDoc
     */
    public function ACLAccess($view, $context = null)
    {
        switch ($view) {
            case 'list':
                if (is_array($context) && isset($context['source']) && $context['source'] === 'filter_api') {
                    return false;
                }
                break;

            case 'edit':
            case 'view':
                if (is_array($context) && isset($context['source']) && $context['source'] === 'module_api') {
                    return false;
                }
                break;
        }

        return parent::ACLAccess($view, $context);
    }

    /**
     * Gets all the locked fields for every record id given
     *
     * @param SugarBean $focus
     * @param array|string $ids array of record ids
     * @return array
     */
    public function getRelatedModuleRecords(SugarBean $focus, array $ids)
    {
        // No ids means nothing to do
        if (empty($ids)) {
            return [];
        }

        if (!PMSEEngineUtils::doesModuleHaveLockedFields($focus->getModuleName())) {
            return [];
        }

        $alias = $this->db->getValidDBName($focus->table_name . '_id', false, 'alias');

        $q = new SugarQuery($this->db);
        $q->from($this, ['alias' => 'pd']);
        $q->joinTable('locked_field_bean_rel', ['alias' => 'lfbr'])->on()
            ->equalsField('pd.id', 'lfbr.pd_id');
        $q->where()
            ->equals('lfbr.bean_module', $focus->module_name)
            ->in('lfbr.id', $ids)
            ->equals('lfbr.deleted', 0);
        $q->select([['pd.pro_locked_variables', 'def'], ['lfbr.bean_id', $alias]]);

        $rows = [];
        foreach ($q->execute() as $row) {
            // In the case of empty locked field defs we need to make an array
            // since the json_decode will result in null
            $def = json_decode($row['def']);
            if ($def === null) {
                $def = [];
            }

            // If there is an existing ID to merge, merge that
            $merge = $rows[$row[$alias]] ?? [];

            // Merge the mergeable and current row defs, and unique them
            $rows[$row[$alias]] = array_unique(array_merge($merge, $def));
        }

        return $rows;
    }
}
