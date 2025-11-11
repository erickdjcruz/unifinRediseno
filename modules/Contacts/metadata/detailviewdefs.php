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
$viewdefs['Contacts']['DetailView'] = [
    'templateMeta' => ['form' => ['buttons' => ['EDIT', 'DUPLICATE', 'DELETE', 'FIND_DUPLICATES',
        ['customCode' => '<input type="submit" class="button" title="{$APP.LBL_MANAGE_SUBSCRIPTIONS}" onclick="this.form.return_module.value=\'Contacts\'; this.form.return_action.value=\'DetailView\'; this.form.return_id.value=\'{$fields.id.value}\'; this.form.action.value=\'Subscriptions\'; this.form.module.value=\'Campaigns\'; this.form.module_tab.value=\'Contacts\';" name="Manage Subscriptions" value="{$APP.LBL_MANAGE_SUBSCRIPTIONS}"/>',
            //Bug#51778: The custom code will be replaced with sugar_html. customCode will be deplicated.
            'sugar_html' => [
                'type' => 'submit',
                'value' => '{$APP.LBL_MANAGE_SUBSCRIPTIONS}',
                'htmlOptions' => [
                    'class' => 'button',
                    'id' => 'manage_subscriptions_button',
                    'title' => '{$APP.LBL_MANAGE_SUBSCRIPTIONS}',
                    'onclick' => 'this.form.return_module.value=\'Contacts\'; this.form.return_action.value=\'DetailView\'; this.form.return_id.value=\'{$fields.id.value}\'; this.form.action.value=\'Subscriptions\'; this.form.module.value=\'Campaigns\'; this.form.module_tab.value=\'Contacts\';',
                    'name' => 'Manage Subscriptions',
                ],
            ],

        ],
    ],
    ],
        'maxColumns' => '2',

        'useTabs' => true,
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],

        'includes' => [
            ['file' => 'modules/Leads/Lead.js'],
        ],
    ],


    'panels' => [
        'lbl_contact_information' => [

            [

                [
                    'name' => 'full_name',
                    'label' => 'LBL_NAME',

                    'displayParams' => [
                        'enableConnectors' => true,
                        'module' => 'Contacts',
                        'connectors' => [
                            0 => 'ext_rest_twitter',
                        ],
                    ],
                ],

                [
                    'name' => 'picture',
                    'label' => 'LBL_PICTURE_FILE',
                ],
            ],

            [

                [
                    'name' => 'title',
                    'comment' => 'The title of the contact',
                    'label' => 'LBL_TITLE',
                ],
                [
                    'name' => 'phone_work',
                    'label' => 'LBL_OFFICE_PHONE',
                ],
            ],

            [
                [
                    'name' => 'department',
                    'label' => 'LBL_DEPARTMENT',
                ],
                [
                    'name' => 'phone_mobile',
                    'label' => 'LBL_MOBILE_PHONE',
                ],
            ],

            [
                [
                    'name' => 'account_name',
                    'label' => 'LBL_ACCOUNT_NAME',
                    'displayParams' => [
                        'enableConnectors' => true,
                        'module' => 'Contacts',
                        'connectors' => [
                        ],
                    ],
                ],
                [
                    'name' => 'phone_fax',
                    'label' => 'LBL_FAX_PHONE',
                ],
            ],

            [

                [
                    'name' => 'primary_address_street',
                    'label' => 'LBL_PRIMARY_ADDRESS',
                    'type' => 'address',
                    'displayParams' => [
                        'key' => 'primary',
                    ],
                ],

                [
                    'name' => 'alt_address_street',
                    'label' => 'LBL_ALTERNATE_ADDRESS',
                    'type' => 'address',
                    'displayParams' => [
                        'key' => 'alt',
                    ],
                ],
            ],

            [

                [
                    'name' => 'email',
                    'studio' => 'false',
                    'label' => 'LBL_EMAIL_ADDRESS',
                ],
                'business_center_name',
            ],

            [

                [
                    'name' => 'description',
                    'comment' => 'Full text of the note',
                    'label' => 'LBL_DESCRIPTION',
                ],
            ],
        ],

        'LBL_PANEL_ADVANCED' => [

            [

                [
                    'name' => 'report_to_name',
                    'label' => 'LBL_REPORTS_TO',
                ],

                [
                    'name' => 'sync_contact',
                    'comment' => 'Synch to outlook?  (Meta-Data only)',
                    'label' => 'LBL_SYNC_CONTACT',
                ],
            ],

            [

                [
                    'name' => 'lead_source',
                    'comment' => 'How did the contact come about',
                    'label' => 'LBL_LEAD_SOURCE',
                ],

                [
                    'name' => 'do_not_call',
                    'comment' => 'An indicator of whether contact can be called',
                    'label' => 'LBL_DO_NOT_CALL',
                ],
            ],

            [

                [
                    'name' => 'campaign_name',
                    'label' => 'LBL_CAMPAIGN',
                ],

            ],

            [
                ['name' => 'portal_name',
                    'label' => 'LBL_PORTAL_NAME',
                    'hideIf' => 'empty($PORTAL_ENABLED)',
                ],
                ['name' => 'portal_active',
                    'label' => 'LBL_PORTAL_ACTIVE',
                    'hideIf' => 'empty($PORTAL_ENABLED)',
                ],
            ],
            [
                ['name' => 'preferred_language',
                    'label' => 'LBL_PREFERRED_LANGUAGE',
                ],
            ],

        ],
        'LBL_PANEL_ASSIGNMENT' => [

            [

                [
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_ASSIGNED_TO_NAME',
                ],

                [
                    'name' => 'date_modified',
                    'customCode' => '{$fields.date_modified.value} {$APP.LBL_BY} {$fields.modified_by_name.value}',
                    'label' => 'LBL_DATE_MODIFIED',
                ],
            ],

            [

                'team_name',

                [
                    'name' => 'date_entered',
                    'customCode' => '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value}',
                    'label' => 'LBL_DATE_ENTERED',
                ],

            ],
        ],
    ],
];
