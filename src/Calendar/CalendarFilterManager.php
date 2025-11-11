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

namespace Sugarcrm\Sugarcrm\Calendar;

class CalendarFilterManager
{
    /**
     * Returns default Sugar filters from specific module
     *
     * @param string $module
     * @return array
     */
    public function getDefaultFilters($module): array
    {
        $defaultFilters = [];
        $mm = new \MetaDataManager();

        $filtersMeta = $mm->getModuleFilters($module);

        foreach ($filtersMeta['basic']['meta']['filters'] as $filter) {
            $defaultFilters[$module][$filter['id']] = $filter['id'];
        }

        return $defaultFilters[$module];
    }

    /**
     * Retrieves filter definition based on its ID and corresponding module.
     *
     * @param string $module
     * @param string $filterId
     *
     * @return array
     */
    public function getFilterDefinition(string $module, string $filterId): array
    {
        $sq = new \SugarQuery();

        $from = \BeanFactory::newBean('Filters');

        $sq->select('filter_definition');
        $sq->from($from);
        $sq->where()->equals('id', $filterId);
        $sq->where()->equals('module_name', $module);

        $result = $sq->execute();

        if (empty($result) === true) {
            return [];
        }

        $filterDef = $result[0]['filter_definition'];
        $filterDef = json_decode($filterDef, true);

        if (is_null($filterDef)) {
            $filterDef = [];
        }

        return $filterDef;
    }
}
