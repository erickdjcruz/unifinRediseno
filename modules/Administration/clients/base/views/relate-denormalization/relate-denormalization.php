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
$viewdefs['Administration']['base']['view']['relate-denormalization'] = [
    'label' => 'LBL_OPPS_CONFIG_VIEW_BY_LABEL',
    'fields-group1' => [
        [
            'name' => 'modules',
            'type' => 'modules',
            'view' => 'edit',
        ],
    ],
    'fields-group2' => [
        [
            'label' => 'LBL_MANAGE_RELATE_DENORMALIZATION_DD_NOTE',
            'name' => 'field-lists',
            'type' => 'linked-lists',
            'view' => 'list',
        ],
    ],
    'fields-group3' => [
        [
            'label' => 'LBL_MANAGE_RELATE_DENORMALIZATION_JOB_LIST',
            'name' => 'job-list',
            'type' => 'job-list',
            'view' => 'default',
        ],
    ],
];
