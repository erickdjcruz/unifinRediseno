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
$viewdefs['Leads']['DetailView'] = [
    'templateMeta' => [
        'form' => [
            'buttons' => [
                'EDIT',
                'DUPLICATE',
                'DELETE',
                [
                    'customCode' => '{if $bean->aclAccess("edit") && !$DISABLE_CONVERT_ACTION}<input title="{$MOD.LBL_CONVERTLEAD_TITLE}" accessKey="{$MOD.LBL_CONVERTLEAD_BUTTON_KEY}" type="button" class="button" onClick="document.location=\'index.php?module=Leads&action=ConvertLead&record={$fields.id.value}\'" name="convert" value="{$MOD.LBL_CONVERTLEAD}">{/if}',
                    //Bug#51778: The custom code will be replaced with sugar_html. customCode will be deplicated.
                    'sugar_html' => [
                        'type' => 'button',
                        'value' => '{$MOD.LBL_CONVERTLEAD}',
                        'htmlOptions' => [
                            'title' => '{$MOD.LBL_CONVERTLEAD_TITLE}',
                            'accessKey' => '{$MOD.LBL_CONVERTLEAD_BUTTON_KEY}',
                            'class' => 'button',
                            'onClick' => 'document.location=\'index.php?module=Leads&action=ConvertLead&record={$fields.id.value}\'',
                            'name' => 'convert',
                            'id' => 'convert_lead_button',
                        ],
                        'template' => '{if $bean->aclAccess("edit") && !$DISABLE_CONVERT_ACTION}[CONTENT]{/if}',
                    ],
                ],
                'FIND_DUPLICATES',
                [
                    'customCode' => '<input title="{$APP.LBL_MANAGE_SUBSCRIPTIONS}" class="button" onclick="this.form.return_module.value=\'Leads\'; this.form.return_action.value=\'DetailView\';this.form.return_id.value=\'{$fields.id.value}\'; this.form.action.value=\'Subscriptions\'; this.form.module.value=\'Campaigns\'; this.form.module_tab.value=\'Leads\';" type="submit" name="Manage Subscriptions" value="{$APP.LBL_MANAGE_SUBSCRIPTIONS}">',
                    //Bug#51778: The custom code will be replaced with sugar_html. customCode will be deplicated.
                    'sugar_html' => [
                        'type' => 'submit',
                        'value' => '{$APP.LBL_MANAGE_SUBSCRIPTIONS}',
                        'htmlOptions' => [
                            'title' => '{$APP.LBL_MANAGE_SUBSCRIPTIONS}',
                            'class' => 'button',
                            'id' => 'manage_subscriptions_button',
                            'onclick' => 'this.form.return_module.value=\'Leads\'; this.form.return_action.value=\'DetailView\';this.form.return_id.value=\'{$fields.id.value}\'; this.form.action.value=\'Subscriptions\'; this.form.module.value=\'Campaigns\'; this.form.module_tab.value=\'Leads\';',
                            'name' => '{$APP.LBL_MANAGE_SUBSCRIPTIONS}',
                        ],
                    ],
                ],

            ],
            'headerTpl' => 'modules/Leads/tpls/DetailViewHeader.tpl',
        ],
        'maxColumns' => '2',

        'useTabs' => true,
        'widths' => [
            [
                'label' => '10',
                'field' => '30',
            ],
            [
                'label' => '10',
                'field' => '30',
            ],
        ],
        'includes' => [
            ['file' => 'modules/Leads/Lead.js'],
        ],
    ],
    'panels' => [

        'LBL_CONTACT_INFORMATION' => [
            [
                [
                    'name' => 'full_name',
                    'label' => 'LBL_NAME',

                    'displayParams' => [
                        'enableConnectors' => true,
                        'module' => 'Leads',
                        'connectors' => [
                            0 => 'ext_rest_twitter',
                        ],
                    ],
                ],
                'phone_work',
            ],

            [
                'title',
                'phone_mobile',
            ],

            [
                'department',
                'phone_fax',
            ],

            [
                [
                    'name' => 'account_name',

                    'displayParams' => [
                        'enableConnectors' => true,
                        'module' => 'Leads',
                        'connectors' => [
                        ],
                    ],
                ],
                'website',
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
                'email',
                'business_center_name',
            ],

            [
                'description',
            ],

        ],

        'LBL_PANEL_ADVANCED' => [

            [
                'status',
                'lead_source',
            ],

            [
                'status_description',
                'lead_source_description',
            ],

            [
                'opportunity_amount',
                'refered_by',
            ],

            [
                [
                    'name' => 'campaign_name',
                    'label' => 'LBL_CAMPAIGN',

                ],
                'do_not_call',
            ],
        ],

        'LBL_PANEL_ASSIGNMENT' => [
            [
                [
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_ASSIGNED_TO',
                ],
                [
                    'name' => 'date_modified',
                    'label' => 'LBL_DATE_MODIFIED',
                    'customCode' => '{$fields.date_modified.value} {$APP.LBL_BY} {$fields.modified_by_name.value}',
                ],
            ],
            [

                'team_name',
                [
                    'name' => 'date_entered',
                    'customCode' => '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value}',
                ],
            ],
        ],
    ],
];
