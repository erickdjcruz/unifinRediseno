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


$subpanel_layout = [
    'top_buttons' => [
    ],

    'where' => '',

    'list_fields' => [
        'id' => [
            'name' => 'id',
            'width' => '10%',
            'vname' => 'LBL_ID',
        ],
        'tstate' => [
            'name' => 'tstate',
            'width' => '10%',
            'vname' => 'LBL_STATUS',
        ],
        'token_ts' => [
            'name' => 'token_ts',
            'width' => '10%',
            'vname' => 'LBL_TS',
            'function' => 'testfunc',
        ],
        'consumer_name' => [
            'name' => 'consumer_name',
            'module' => 'OAuthKeys',
            'target_record_key' => 'consumer',
            'target_module' => 'OAuthKeys',
            'width' => '10%',
            'vname' => 'LBL_CONSUMER',
        ],
        'del_button' => [
            'widget_class' => 'SubPanelDeleteButton',
            'vname' => 'LBL_LIST_DELETE',
            'width' => '6%',
            'sortable' => false,
        ],
    ],
];
