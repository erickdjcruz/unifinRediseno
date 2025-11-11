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
$viewdefs['base']['view']['dashletselect'] = [
    'template' => 'filtered-list',
    'panels' => [
        [
            'fields' => [
                [
                    'label' => 'LBL_DASHLET_CONFIGURE_TITLE',
                    'name' => 'title',
                    'type' => 'text',
                    'link' => true,
                    'events' => [
                        'click a' => 'dashletlist:select-and-edit',
                    ],
                    'filter' => 'startsWith',
                    'sortable' => true,
                ],
                [
                    'label' => 'LBL_DESCRIPTION',
                    'name' => 'description',
                    'type' => 'text',
                    'filter' => 'contains',
                    'sortable' => true,
                ],
                [
                    'type' => 'rowaction',
                    'tooltip' => 'LBL_PREVIEW',
                    'event' => 'dashletlist:preview:fire',
                    'css_class' => 'btn !border-0 bg-none no-inset',
                    'icon' => 'sicon-preview',
                    'width' => '2.75rem',
                    'widthClass' => 'cell-xsmall',
                    'sortable' => false,
                ],
            ],
        ],
    ],
];
