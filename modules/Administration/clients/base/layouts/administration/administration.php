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
$viewdefs['Administration']['base']['layout']['administration'] = [
    'css_class' => 'administration bg-[--secondary-content-background]',
    'components' => [
        [
            'view' => 'administration-headerpane',
        ],
        [
            'view' => 'searchbar',
        ],
        [
            'view' => 'administration-errors',
        ],
        [
            'layout' => [
                'name' => 'content-grid-wrapper',
                'css_class' => 'content-grid-wrapper ml-2 mr-2 pt-8 pb-8',
                'components' => [
                    [
                        'layout' => [
                            'name' => 'content-grid',
                            'css_class' => 'm-auto',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
