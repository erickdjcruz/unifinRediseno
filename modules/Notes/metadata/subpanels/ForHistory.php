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

    'where' => '',


    'list_fields' => [
        'object_image' => [
            'vname' => 'LBL_OBJECT_IMAGE',
            'widget_class' => 'SubPanelIcon',
            'width' => '2%',
            'image2' => 'attachment',
            'image2_url_field' => [
                'id_field' => 'id',
                'filename_field' => 'filename',
            ],
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
            'force_exists' => true, //this will create a fake field in the case a field is not defined

        ],
        'reply_to_status' => [
            'usage' => 'query_only',
            'force_exists' => true,
            'force_default' => 0,
        ],
        'contact_name' => [
            'widget_class' => 'SubPanelDetailViewLink',
            'target_record_key' => 'contact_id',
            'target_module' => 'Contacts',
            'module' => 'Contacts',
            'vname' => 'LBL_LIST_CONTACT',
            'width' => '11%',
            'sortable' => false,
        ],
        'parent_id' => [
            'usage' => 'query_only',
            'force_exists' => true,
        ],
        'parent_type' => [
            'usage' => 'query_only',
            'force_exists' => true,
        ],

        'date_modified' => [
            'vname' => 'LBL_LIST_DATE_MODIFIED',
            'width' => '10%',
        ],
        'date_entered' => [
            'vname' => 'LBL_LIST_DATE_ENTERED',
            'width' => '10%',
        ],
        'assigned_user_name' => [
            'name' => 'assigned_user_name',
            'vname' => 'LBL_LIST_ASSIGNED_TO_NAME',
            'widget_class' => 'SubPanelDetailViewLink',
            'target_record_key' => 'assigned_user_id',
            'target_module' => 'Employees',
            'width' => '10%',
        ],
        'assigned_user_owner' => [
            'force_exists' => true, //this will create a fake field since this field is not defined
            'usage' => 'query_only',
        ],
        'assigned_user_mod' => [
            'force_exists' => true, //this will create a fake field since this field is not defined
            'usage' => 'query_only',
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
        'file_url' => [
            'usage' => 'query_only',
        ],
        'filename' => [
            'usage' => 'query_only',
        ],

    ],
];
