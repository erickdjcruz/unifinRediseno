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

final class DataArchiverFilterApi extends FilterApi
{
    /**
     * Formats and returns a valid where clause object.
     * @param $filterDefs filter defs json object
     * @param $module
     * @return SugarQuery_Builder_Andwhere|SugarQuery_Builder_Where|null
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarQueryException
     */
    public function convertFiltersToWhere($filterDefs, $module)
    {
        $seed = BeanFactory::newBean($module);
        $query = parent::getQueryObject($seed, ['limit' => 0, 'offset' => -1]);
        static::addFilters($filterDefs, $query->where(), $query);
        return $query->where();
    }
}
