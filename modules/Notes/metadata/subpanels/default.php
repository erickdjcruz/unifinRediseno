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
        ['widget_class' => 'SubPanelTopCreateButton'],
        ['widget_class' => 'SubPanelTopSelectButton', 'popup_module' => 'Notes'],
    ],

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
            'width' => '50%',
        ],
        'contact_name' => [
            'module' => 'Contacts',
            'vname' => 'LBL_LIST_CONTACT_NAME',
            'width' => '20%',
            'target_record_key' => 'contact_id',
            'target_module' => 'Contacts',
            'widget_class' => 'SubPanelDetailViewLink',
        ],
        'date_modified' => [
            'vname' => 'LBL_LIST_DATE_MODIFIED',
            'width' => '10%',
        ],
        'edit_button' => [
            'vname' => 'LBL_EDIT_BUTTON',
            'widget_class' => 'SubPanelEditButton',
            'module' => 'Notes',
            'width' => '5%',
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
