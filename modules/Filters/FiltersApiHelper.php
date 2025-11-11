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

use Sugarcrm\Sugarcrm\Filters\Filter;

class FiltersApiHelper extends SugarBeanApiHelper
{
    /**
     * {@inheritDoc}
     */
    public function formatForApi(\SugarBean $bean, array $fieldList = [], array $options = [])
    {
        $data = parent::formatForApi($bean, $fieldList, $options);

        // The value of the module_name column. Not the Filters class property.
        $moduleName = $data['module_name'] ?? $bean->module_name;

        if (isset($data['filter_definition'])) {
            $filter = new Filter($moduleName, $data['filter_definition']);
            $data['filter_definition'] = $filter->apiSerialize($this->api);
        }

        if (isset($data['filter_template'])) {
            $filter = new Filter($moduleName, $data['filter_template']);
            $data['filter_template'] = $filter->apiSerialize($this->api);
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function populateFromApi(SugarBean $bean, array $submittedData, array $options = [])
    {
        // The value of the module_name column. Not the Filters class property.
        $moduleName = $submittedData['module_name'] ?? $bean->module_name;

        if (isset($submittedData['filter_definition'])) {
            $filter = new Filter($moduleName, $submittedData['filter_definition']);
            $submittedData['filter_definition'] = $filter->apiUnserialize(
                $this->api
            );
        }

        if (isset($submittedData['filter_template'])) {
            $filter = new Filter($moduleName, $submittedData['filter_template']);
            $submittedData['filter_template'] = $filter->apiUnserialize($this->api);
        }

        return parent::populateFromApi($bean, $submittedData, $options);
    }
}
