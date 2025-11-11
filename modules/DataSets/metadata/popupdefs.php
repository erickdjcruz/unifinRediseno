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

$popupMeta = ['moduleMain' => 'DataSet',
    'varName' => 'DATASET',
    'className' => 'DataSet',
    'orderBy' => 'name',
    'whereClauses' => ['name' => 'data_sets.name'],
    'listviewdefs' => [
        'NAME' => [
            'width' => '35',
            'label' => 'LBL_NAME',
            'link' => true,
            'default' => true],
        'DESCRIPTION' => [
            'width' => '65',
            'label' => 'LBL_DESCRIPTION',
            'link' => false,
            'default' => true],
    ],
    'searchdefs' => [
        'name',
    ],
];
