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
    //Removed button because this layout def is a component of
    //the activities sub-panel.

    'top_buttons' => [
        [
            'widget_class' => 'SubPanelTopCreateButton',
        ],
        [
            'widget_class' => 'SubPanelTopSelectButton', 'popup_module' => 'Tasks',
        ],
    ],


    'list_fields' => [
        'object_image' => [
            'vname' => 'LBL_OBJECT_IMAGE',
            'widget_class' => 'SubPanelIcon',
            'width' => '2%',
        ],
        'name' => [
            'vname' => 'LBL_LIST_SUBJECT',
            'widget_class' => 'SubPanelDetailViewLink',
            'width' => '30%',
        ],
        'status' => [
            'widget_class' => 'SubPanelActivitiesStatusField',
            'vname' => 'LBL_LIST_STATUS',
            'width' => '15%',
        ],
        'contact_name' => [
            'widget_class' => 'SubPanelDetailViewLink',
            'target_record_key' => 'contact_id',
            'target_module' => 'Contacts',
            'module' => 'Contacts',
            'vname' => 'LBL_LIST_CONTACT',
            'width' => '11%',
        ],

        'parent_name' => [
            'vname' => 'LBL_LIST_RELATED_TO',
            'width' => '22%',
            'target_record_key' => 'parent_id',
            'target_module_key' => 'parent_type',
            'widget_class' => 'SubPanelDetailViewLink',
            'sortable' => false,
        ],
        'date_modified' => [
            'vname' => 'LBL_LIST_DATE_MODIFIED',
            'width' => '10%',
        ],
        'edit_button' => [
            'vname' => 'LBL_EDIT_BUTTON',
            'widget_class' => 'SubPanelEditButton',
            'width' => '2%',
        ],
        'remove_button' => [
            'vname' => 'LBL_REMOVE',
            'widget_class' => 'SubPanelRemoveButton',
            'width' => '2%',
        ],
        'parent_id' => [
            'usage' => 'query_only',
        ],
        'parent_type' => [
            'usage' => 'query_only',
        ],
        'filename' => [
            'usage' => 'query_only',
            'force_exists' => true,
        ],


    ],
];
