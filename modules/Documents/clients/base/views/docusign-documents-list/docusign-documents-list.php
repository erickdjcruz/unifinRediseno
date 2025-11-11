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
$module_name = 'Documents';
$viewdefs[$module_name]['base']['view']['docusign-documents-list'] = [
    'favorite' => false,
    'following' => false,
    'sticky_resizable_columns' => true,
    'selection' => [],
    'rowactions' => [
        'actions' => [
            [
                'type' => 'rowaction',
                'css_class' => 'btn',
                'tooltip' => 'Remove',
                'event' => 'list:document:remove',
                'icon' => 'sicon sicon-remove',
                'acl_action' => 'view',
            ],
        ],
    ],
    'panels' => [
        [
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'document_name',
                    'label' => 'LBL_LIST_DOCUMENT_NAME',
                    'enabled' => true,
                    'default' => true,
                    'link' => true,
                    'type' => 'name',
                ],
                [
                    'name' => 'filename',
                    'label' => 'LBL_LIST_FILENAME',
                    'type' => 'file',
                    'enabled' => true,
                    'default' => true,
                ],
            ],
        ],
    ],
    'last_state' => [
        'id' => 'docusign-documents-list',
    ],
];
