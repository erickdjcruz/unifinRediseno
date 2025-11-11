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

require_once 'modules/Audit/field_assoc.php';

/**
 * Class ChangeTimersApiHelper
 */
class ChangeTimersApiHelper extends SugarBeanApiHelper
{
    /**
     * @inheritDoc
     */
    public function formatForApi(SugarBean $bean, array $fieldList = [], array $options = [])
    {
        $data = parent::formatForApi($bean, $fieldList, $options);

        // Try to convert id fields to the actual relate field values
        if (!empty($options['args']['module']) && !empty($options['args']['record'])) {
            $parentBean = BeanFactory::getBean($options['args']['module'], $options['args']['record']);
            $mappings = $this->getMappingForRelateFields($parentBean);
            $fieldMapping = $mappings[$data['field_name']] ?? [];
            if (!empty($fieldMapping)) {
                $data['value_string'] = $this->runRelateFieldQuery($fieldMapping, $data['value_string']);
            }
        }
        return $data;
    }

    /**
     * Get mapping of id fields to the relate field
     *
     * @param SugarBean $bean
     * @return array
     */
    private function getMappingForRelateFields(SugarBean $bean)
    {
        global $genericAssocFieldsArray, $moduleAssocFieldsArray;

        // for now, use the audit mappings
        if (!empty($moduleAssocFieldsArray[$bean->object_name])) {
            return array_merge($genericAssocFieldsArray, $moduleAssocFieldsArray[$bean->object_name]);
        }
        return $genericAssocFieldsArray;
    }

    /**
     * Run a query to get the relate field value from a mapping
     * @param array $field_arr
     * @param string $fieldValue
     * @return string
     * @throws Doctrine\DBAL\Exception
     */
    private function runRelateFieldQuery(array $field_arr, string $fieldValue)
    {
        // code taken from Audit.php due to time constraints
        $sql = <<<SQL
SELECT %s FROM {$field_arr['table_name']}
WHERE {$field_arr['select_field_join']} = ?
SQL;

        $db = DBManagerFactory::getInstance();
        $row = $db->getConnection()
            ->executeQuery(
                sprintf(
                    $sql,
                    is_array($field_arr['select_field_name']) ?
                        implode(',', $field_arr['select_field_name']) : $field_arr['select_field_name']
                ),
                [$fieldValue]
            )->fetchAssociative();

        if (is_array($field_arr['select_field_name'])) {
            $returnVal = '';
            foreach ($field_arr['select_field_name'] as $col) {
                $returnVal .= $row[$col] . ' ';
            }

            return $returnVal;
        } else {
            return $row[$field_arr['select_field_name']];
        }
    }
}
