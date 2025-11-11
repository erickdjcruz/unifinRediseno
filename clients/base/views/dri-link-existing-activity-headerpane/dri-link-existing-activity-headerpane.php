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

$viewdefs['base']['view']['dri-link-existing-activity-headerpane'] = [
    'template' => 'headerpane',
    'fields' => [
        [
            'name' => 'title',
            'type' => 'label',
            'default_value' => 'TPL_SEARCH_AND_ADD',
        ],
        [
            'name' => 'collection-count',
            'type' => 'collection-count',
        ],
    ],
    'buttons' => [
        [
            'name' => 'close',
            'type' => 'button',
            'label' => 'LBL_CANCEL_BUTTON_LABEL',
            'events' => [
                'click' => 'selection:closedrawer:fire',
            ],
            'css_class' => 'btn-invisible btn-link',
        ],
        [
            'name' => 'add',
            'type' => 'button',
            'label' => 'LBL_ADD_BUTTON',
            'events' => [
                'click' => 'selection:add:fire',
            ],
            'css_class' => 'btn-primary',
        ],
        [
            'name' => 'sidebar_toggle',
            'type' => 'sidebartoggle',
        ],
    ],
];
