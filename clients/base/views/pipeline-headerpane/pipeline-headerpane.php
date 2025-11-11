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

$viewdefs['base']['view']['pipeline-headerpane'] = [
    'buttons' => [
        [
            'name' => 'pipeline_create_button',
            'type' => 'button',
            'label' => 'LBL_CREATE_BUTTON_LABEL',
            'css_class' => 'btn btn-primary',
            'acl_action' => 'create',
            'events' => [
                'click' => 'button:pipeline_create_button:click',
            ],
        ],
    ],
    'fields' => [
        [
            'name' => 'pipeline_type',
            'label' => 'LBL_PIPELINE_TYPE',
            'type' => 'pipeline-type',
        ],
    ],
];
