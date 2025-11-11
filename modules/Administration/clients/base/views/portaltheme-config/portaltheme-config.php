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
$viewdefs['Administration']['base']['view']['portaltheme-config'] = [
    'template' => 'record',
    'panels' => [
        [
            'name' => 'panel_body',
            'columns' => 1,
            'labelsOnTop' => true,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'portaltheme_configure_login_header',
                    'type' => 'sub-title',
                    'text' => 'LBL_PORTALTHEME_CONFIGURE_LOGIN_HEADER',
                ],
                [
                    'name' => 'portaltheme_login_page_image_url',
                    'type' => 'image-url',
                    'label' => 'LBL_PORTALTHEME_LOGIN_PAGE_IMAGE',
                    'dbType' => 'varchar',
                    'default' => 'themes/default/images/company_logo.png',
                    'len' => 255,
                ],
                [
                    'name' => 'portaltheme_configure_homepage_header',
                    'type' => 'sub-title',
                    'text' => 'LBL_PORTALTHEME_CONFIGURE_HOMEPAGE_HEADER',
                ],
                [
                    'name' => 'portaltheme_navigation_bar_logo_image_url',
                    'type' => 'image-url',
                    'label' => 'LBL_PORTALTHEME_NAVIGATION_BAR_LOGO_IMAGE',
                    'dbType' => 'varchar',
                    'len' => 255,
                    'preview_components' => [
                        [
                            'layout' => 'portaltheme-megamenu.portaltheme-module-list',
                            'view' => 'module-menu',
                            'properties' => [
                                'logoImage',
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'portaltheme_banner_background_style',
                    'type' => 'enum',
                    'label' => 'LBL_PORTALTHEME_BANNER_BACKGROUND_STYLE',
                    'options' => 'portaltheme_banner_background_style_dom',
                    'default' => 'default',
                    'preview_components' => [
                        [
                            'layout' => 'dashboard',
                            'view' => 'contentsearchdashlet',
                            'properties' => [
                                'bannerBackgroundStyle',
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'portaltheme_banner_background_color',
                    'type' => 'colorpicker',
                    'label' => 'LBL_PORTALTHEME_BANNER_BACKGROUND_COLOR',
                    'default' => '#ffffff',
                    'preview_components' => [
                        [
                            'layout' => 'dashboard',
                            'view' => 'contentsearchdashlet',
                            'properties' => [
                                'bannerBackgroundColor',
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'portaltheme_banner_background_image',
                    'type' => 'image-url',
                    'label' => 'LBL_PORTALTHEME_BANNER_BACKGROUND_IMAGE',
                    'dbType' => 'varchar',
                    'len' => 255,
                    'preview_components' => [
                        [
                            'layout' => 'dashboard',
                            'view' => 'contentsearchdashlet',
                            'properties' => [
                                'bannerBackgroundImage',
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'portaltheme_new_case_button',
                    'type' => 'bool',
                    'label' => 'LBL_PORTALTHEME_NEW_CASE_BUTTON',
                    'default' => 1,
                    'preview_components' => [
                        [
                            'layout' => 'portaltheme-megamenu',
                            'view' => 'header-help',
                            'properties' => [
                                'enabled',
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'portaltheme_new_case_message',
                    'type' => 'text',
                    'label' => 'LBL_PORTALTHEME_NEW_CASE_MESSAGE',
                    'default' => 'LBL_PORTALTHEME_NEW_CASE_MESSAGE_DEFAULT',
                    'preview_components' => [
                        [
                            'layout' => 'portaltheme-megamenu',
                            'view' => 'header-help',
                            'properties' => [
                                'label',
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'portaltheme_new_case_button_text',
                    'type' => 'text',
                    'label' => 'LBL_PORTALTHEME_NEW_CASE_BUTTON_TEXT',
                    'default' => 'LBL_PORTALTHEME_NEW_CASE_BUTTON_TEXT_DEFAULT',
                    'preview_components' => [
                        [
                            'layout' => 'portaltheme-megamenu',
                            'view' => 'header-help',
                            'fields' => [
                                'help_button',
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'portaltheme_search_bar',
                    'type' => 'bool',
                    'label' => 'LBL_PORTALTHEME_SEARCH_BAR',
                    'default' => 1,
                    'preview_components' => [
                        [
                            'layout' => 'dashboard',
                            'view' => 'contentsearchdashlet',
                            'properties' => [
                                'enabled',
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'portaltheme_search_bar_text',
                    'type' => 'text',
                    'label' => 'LBL_PORTALTHEME_SEARCH_BAR_TEXT',
                    'default' => 'LBL_PORTALTHEME_SEARCH_BAR_TEXT_DEFAULT',
                    'preview_components' => [
                        [
                            'layout' => 'dashboard',
                            'view' => 'contentsearchdashlet',
                            'properties' => [
                                'greeting',
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'portaltheme_button_color',
                    'type' => 'colorpicker',
                    'label' => 'LBL_PORTALTHEME_BUTTON_COLOR',
                    'default' => '#0679C8',
                    'preview_components' => [
                        [
                            'layout' => 'portaltheme-megamenu',
                            'view' => 'header-help',
                            'properties' => [
                                'buttonColor',
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'portaltheme_text_link_color',
                    'type' => 'colorpicker',
                    'label' => 'LBL_PORTALTHEME_TEXT_LINK_COLOR',
                    'default' => '#0679C8',
                ],
            ],
            'linkLabels' => [
                [
                    'name' => 'portaltheme_restore_defaults_link',
                    'link' => [
                        'text' => 'LBL_PORTALTHEME_RESTORE_DEFAULTS_LINK',
                        'css_class' => 'restore-defaults-btn',
                        'href' => 'javascript:void(0)',
                    ],
                    'text' => 'LBL_PORTALTHEME_RESTORE_DEFAULTS',
                ],
                [
                    'name' => 'portaltheme_open_aws_settings_link',
                    'css_class' => 'hide',
                    'link' => [
                        'text' => 'LBL_PORTALTHEME_OPEN_AWS_SETTINGS_LINK',
                        'css_class' => 'open-aws-settings-btn',
                        'href' => '#Administration/config/aws',
                        'target' => '_blank',
                    ],
                    'text' => 'LBL_PORTALTHEME_OPEN_AWS_SETTINGS',
                ],
            ],
            'helpLabels' => [
                [
                    'name' => 'LBL_PORTALTHEME_SAVE_CHANGES',
                    'text' => 'LBL_PORTALTHEME_SAVE_CHANGES_HELP_TEXT',
                ],
            ],
        ],
    ],
];
