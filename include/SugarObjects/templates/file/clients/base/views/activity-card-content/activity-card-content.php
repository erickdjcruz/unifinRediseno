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

$viewdefs['<module_name>']['base']['view']['activity-card-content'] = [
    'panels' => [
        [
            'name' => 'panel_body_1',
            'label' => 'LBL_PANEL_1',
            'css_class' => 'panel-group flex',
            'fields' => [
                'category_id',
                'subcategory_id',
            ],
        ],
        [
            'name' => 'panel_body_2',
            'label' => 'LBL_PANEL_2',
            'css_class' => 'panel-group flex',
            'fields' => [
                [
                    'name' => 'published_by',
                    'type' => 'fieldset',
                    'css_class' => 'flex',
                    'fields' => [
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_LIST_LAST_REV_CREATOR',
                            'css_class' => 'activity-label',
                        ],
                        [
                            'name' => 'created_by_name',
                            'type' => 'relate',
                        ],
                    ],
                ],
            ],
        ],
        [
            'name' => 'panel_body_3',
            'label' => 'LBL_PANEL_3',
            'css_class' => 'panel-group flex',
            'fields' => [
                [
                    'name' => 'publish_date',
                    'type' => 'fieldset',
                    'css_class' => 'flex',
                    'fields' => [
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_ACTIVE_DATE',
                            'css_class' => 'activity-label',
                        ],
                        [
                            'name' => 'active_date',
                        ],
                    ],
                ],
                [
                    'name' => 'exp_date',
                    'type' => 'fieldset',
                    'css_class' => 'flex',
                    'fields' => [
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_EXPIRATION_DATE',
                            'css_class' => 'activity-label',
                        ],
                        [
                            'name' => 'exp_date',
                        ],
                    ],
                ],
            ],
        ],
    ],
];

