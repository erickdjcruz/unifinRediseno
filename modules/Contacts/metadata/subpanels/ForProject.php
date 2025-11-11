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
        ['widget_class' => 'SubPanelTopSelectButton', 'title' => 'LBL_SELECT_USER_RESOURCE', 'popup_module' => 'Users',],
    ],

    'where' => '',

    'list_fields' => [
        'object_image' => [
            'widget_class' => 'SubPanelIcon',
            'width' => '2%',
        ],
        'name' => [
            'name' => 'name',
            'vname' => 'LBL_RESOURCE_NAME',
            'width' => '93%',
        ],
        /*		'remove_button'=>array(
                    'vname' => 'LBL_REMOVE',
                     'widget_class' => 'SubPanelRemoveButtonProjects',
                     'width' => '5%',
                ),
                */
        'remove_button' => [
            'vname' => 'LBL_REMOVE',
            'widget_class' => 'SubPanelRemoveButton',
            'module' => 'Contacts',
            'width' => '5%',
        ],
        'first_name' => [
            'usage' => 'query_only',
        ],
        'last_name' => [
            'usage' => 'query_only',
        ],
        'salutation' => [
            'name' => 'salutation',
            'usage' => 'query_only',
        ],
    ],
];
