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


$viewdefs['PdfManager'] =
    [
        'EditView' => [
            'templateMeta' => [
                'form' => [
                    'footerTpl' => 'modules/PdfManager/tpls/EditViewFooter.tpl',
                    'enctype' => 'multipart/form-data',
                    'hidden' => [
                        '<input type="hidden" name="base_module_history" id="base_module_history" value="{$fields.base_module.value}">',
                    ],
                ],
                'maxColumns' => '2',
                'widths' => [
                    0 =>
                        [
                            'label' => '10',
                            'field' => '30',
                        ],
                    1 =>
                        [
                            'label' => '10',
                            'field' => '30',
                        ],
                ],
                'includes' => [
                    [
                        'file' => 'modules/PdfManager/javascript/PdfManager.js',
                    ],
                ],
                'useTabs' => false,
                'syncDetailEditViews' => false,
            ],
            'panels' => [
                'default' => [
                    0 =>
                        [
                            0 => 'name',
                            1 =>
                                [
                                    'name' => 'team_name',
                                    'displayParams' => [
                                        'display' => true,
                                    ],
                                ],
                        ],
                    1 =>
                        [
                            0 => ['name' => 'description',
                                'displayParams' => ['rows' => 1],
                            ],
                        ],
                    2 =>
                        [
                            0 =>
                                [
                                    'name' => 'base_module',
                                    'label' => 'LBL_BASE_MODULE',
                                    'popupHelp' => 'LBL_BASE_MODULE_POPUP_HELP',
                                    'displayParams' => [
                                        'field' => [
                                            'onChange' => 'SUGAR.PdfManager.loadFields(this.value, \'\');',
                                        ],
                                    ],
                                ],
                            1 =>
                                [
                                    'name' => 'published',
                                    'label' => 'LBL_PUBLISHED',
                                    'popupHelp' => 'LBL_PUBLISHED_POPUP_HELP',
                                ],
                        ],
                    3 =>
                        [
                            0 =>
                                [
                                    'name' => 'field',
                                    'label' => 'LBL_FIELD',
                                    'customCode' => '{include file="modules/PdfManager/tpls/getFields.tpl"}',
                                    'popupHelp' => 'LBL_FIELD_POPUP_HELP',
                                ],
                        ],
                    4 =>
                        [
                            0 =>
                                [
                                    'name' => 'body_html',
                                    'label' => 'LBL_BODY_HTML',
                                    'popupHelp' => 'LBL_BODY_HTML_POPUP_HELP',
                                ],
                        ],
                    5 =>
                        [
                            0 => [
                                'name' => 'header_title',
                            ],
                            1 => [
                                'name' => 'header_text',
                            ],
                        ],
                    6 =>
                        [
                            0 => [
                                'name' => 'header_logo',
                                'popupHelp' => 'LBL_HEADER_LOGO_POPUP_HELP',
                            ],
                            1 => [
                                'name' => 'footer_text',
                            ],
                        ],
                ],
                'lbl_editview_panel1' => [
                    0 =>
                        [
                            0 =>
                                [
                                    'name' => 'author',
                                    'label' => 'LBL_AUTHOR',
                                ],
                            1 =>
                                [
                                    'name' => 'title',
                                    'label' => 'LBL_TITLE',
                                ],
                        ],
                    1 =>
                        [
                            0 =>
                                [
                                    'name' => 'subject',
                                    'label' => 'LBL_SUBJECT',
                                ],
                            1 =>
                                [
                                    'name' => 'keywords',
                                    'label' => 'LBL_KEYWORDS',
                                    'popupHelp' => 'LBL_KEYWORDS_POPUP_HELP',
                                ],
                        ],
                ],
            ],
        ],
    ];
