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

$viewdefs['KBContents']['base']['view']['activity-card-content'] = [
    'panels' => [
        [
            'css_class' => 'panel-group flex',
            'fields' => [
                'language' => [
                    'name' => 'language',
                    'type' => 'enum-config',
                    'key' => 'languages',
                    'readonly' => true,
                ],
                'status',
                [
                    'name' => 'revision',
                    'type' => 'fieldset',
                    'css_class' => 'flex',
                    'fields' => [
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_ACTIVE_REV',
                            'css_class' => 'activity-label',
                        ],
                        'revision',
                    ],
                ],
            ],
        ],
    ],
];
