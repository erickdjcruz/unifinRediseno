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

$popupMeta = ['moduleMain' => 'CustomQuery',
    'varName' => 'CUSTOMQUERY',
    'className' => 'CustomQuery',
    'orderBy' => 'name',
    'whereClauses' => ['name' => 'custom_queries.name',
        'description' => 'custom_queries.description'],
    'listviewdefs' => [
        'NAME' => [
            'width' => '20',
            'label' => 'LBL_LIST_NAME',
            'link' => true,
            'default' => true],
        'DESCRIPTION' => [
            'width' => '20',
            'label' => 'LBL_LIST_DESCRIPTION',
            'link' => false,
            'default' => true],
    ],
    'searchdefs' => [
        'name',
        'description',
    ],
];
