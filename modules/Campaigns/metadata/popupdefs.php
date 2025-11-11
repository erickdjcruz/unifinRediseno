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

$popupMeta = [
    'moduleMain' => 'Campaign',
    'varName' => 'CAMPAIGN',
    'orderBy' => 'name',
    'whereClauses' => ['name' => 'campaigns.name'],
    'searchInputs' => ['name'],
    'listviewdefs' => [
        'NAME' => [
            'width' => '20',
            'label' => 'LBL_LIST_CAMPAIGN_NAME',
            'link' => true,
            'default' => true],
        'CAMPAIGN_TYPE' => [
            'width' => '10',
            'label' => 'LBL_LIST_TYPE',
            'default' => true],
        'STATUS' => [
            'width' => '10',
            'label' => 'LBL_LIST_STATUS',
            'default' => true],
        'START_DATE' => [
            'width' => '10',
            'label' => 'LBL_LIST_START_DATE',
            'default' => true],
        'END_DATE' => [
            'width' => '10',
            'label' => 'LBL_LIST_END_DATE',
            'default' => true],
    ],
    'searchdefs' => [
        'name',
        'campaign_type',
        'status',
        'start_date',
        'end_date',
    ],
];
