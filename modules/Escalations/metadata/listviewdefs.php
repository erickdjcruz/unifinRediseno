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
$listViewDefs ['Escalations'] = [
    'ESCALATION_NUMBER' => [
        'width' => '5',
        'label' => 'LBL_LIST_NUMBER',
        'default' => true,
    ],
    'NAME' => [
        'width' => '40',
        'label' => 'LBL_LIST_SUBJECT',
        'link' => true,
        'default' => true,
    ],
    'PARENT_NAME' => [
        'width' => '20',
        'label' => 'LBL_LIST_RELATED_TO',
        'dynamic_module' => 'PARENT_TYPE',
        'id' => 'PARENT_ID',
        'link' => true,
        'default' => true,
        'sortable' => false,
        'ACLTag' => 'PARENT',
        'related_fields' => [
            'parent_id',
            'parent_type',
        ],
    ],
];
