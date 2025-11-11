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

class FilterPortalApi extends FilterApi
{
    protected static function addFilter($field, $filter, SugarQuery_Builder_Where $where, SugarQuery $q)
    {
        // Check if Portal user is trying to access an unauthorized filter
        $portalUnauthorizedFilters = [
            '$tracker',
            '$favorite',
        ];
        if (safeInArray($field, $portalUnauthorizedFilters)) {
            throw new SugarApiExceptionNotAuthorized('No access to ' . $field . ' filter');
        }

        parent::addFilter($field, $filter, $where, $q);
    }
}
