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
$module_name = 'DocuSignEnvelopes';
$viewdefs[$module_name]['base']['view']['docusign-drafts-list'] = [
    'favorite' => false,
    'following' => false,
    'sticky_resizable_columns' => true,
    'selection' => [],
    'rowactions' => [
        'actions' => [
            [
                'type' => 'rowaction',
                'event' => 'click:draft:open',
                'acl_action' => 'view',
                'tooltip' => 'Open draft',
                'icon' => 'sicon sicon-folder-open',
                'css_class' => 'btn',
            ],
        ],
    ],
    'panels' => [
        [
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'name',
                    'label' => 'Name',
                    'enabled' => true,
                    'default' => true,
                    'link' => true,
                ],
                [
                    'name' => 'date_entered',
                    'label' => 'LBL_DATE_ENTERED',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'created_by_name',
                    'label' => 'LBL_CREATED',
                    'enabled' => true,
                    'readonly' => true,
                    'module' => 'Users',
                    'type' => 'relate',
                    'id_name' => 'created_by',
                    'default' => true,
                ],
                [
                    'name' => 'status',
                    'label' => 'LBL_STATUS',
                    'enabled' => true,
                    'default' => true,
                    'link' => true,
                ],
            ],
        ],
    ],
    'last_state' => [
        'id' => 'docusign-drafts-list',
    ],
];
