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

$viewdefs['base']['layout']['sidebar-help'] = [
    'components' => [
        [
            'view' => 'sidebar-help-header',
        ],
        [
            'layout' => [
                'type' => 'base',
                'css_class' => 'helplet-list-container py-0 px-4 w-96',
                'components' => [
                    [
                        'view' => [
                            'name' => 'about-version',
                            'css_class' => 'mt-3',
                        ],
                        'context' => [
                            'module' => 'Home',
                        ],
                    ],
                    [
                        'view' => 'helplet',
                    ],
                    [
                        'view' => [
                            'type' => 'helplet',
                            'resources' => [
                                'sugar_university' => [
                                    'url' => 'https://sugarclub.sugarcrm.com/learn',
                                    'link' => 'LBL_LEARNING_RESOURCES_SUGAR_UNIVERSITY_LINK',
                                    'type' => 'sugar-university',
                                    'icon' => 'sicon-sugar-u',
                                ],
                                'community' => [
                                    'url' => 'https://sugarclub.sugarcrm.com/',
                                    'link' => 'LBL_LEARNING_RESOURCES_COMMUNITY_LINK',
                                    'type' => 'community',
                                    'icon' => 'sicon-sugar-club',
                                ],
                                'support' => [
                                    'url' => 'https://support.sugarcrm.com/',
                                    'link' => 'LBL_LEARNING_RESOURCES_SUPPORT_LINK',
                                    'type' => 'support',
                                    'icon' => 'sicon-document-lg',
                                ],
                                'marketplace' => [
                                    'url' => 'https://www.sugaroutfitters.com/',
                                    'link' => 'LBL_LEARNING_RESOURCES_SUGAR_OUTFITTERS_LINK',
                                    'type' => 'marketplace',
                                    'icon' => 'sicon-marketplace-lg',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
