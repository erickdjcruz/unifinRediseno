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
$viewdefs ['Meetings'] =
    [
        'DetailView' => [
            'templateMeta' => [
                'form' => [
                    'buttons' => [
                        'EDIT',
                        'SHARE',
                        'DUPLICATE',
                        'DELETE',
                        [
                            'customCode' => '{if $fields.status.value != "Held" && $bean->aclAccess("edit")} <input type="hidden" name="isSaveAndNew" value="false">  <input type="hidden" name="status" value="">  <input type="hidden" name="isSaveFromDetailView" value="true">  <input title="{$APP.LBL_CLOSE_AND_CREATE_BUTTON_TITLE}"   class="button"  onclick="this.form.status.value=\'Held\'; this.form.action.value=\'Save\';this.form.return_module.value=\'Meetings\';this.form.isDuplicate.value=true;this.form.isSaveAndNew.value=true;this.form.return_action.value=\'EditView\'; this.form.isDuplicate.value=true;this.form.return_id.value=\'{$fields.id.value}\';" id="close_create_button" name="button"  value="{$APP.LBL_CLOSE_AND_CREATE_BUTTON_TITLE}"  type="submit">{/if}',
                            //Bug#51778: The custom code will be replaced with sugar_html. customCode will be deplicated.
                            'sugar_html' => [
                                'type' => 'submit',
                                'value' => '{$APP.LBL_CLOSE_AND_CREATE_BUTTON_TITLE}',
                                'htmlOptions' => [
                                    'title' => '{$APP.LBL_CLOSE_AND_CREATE_BUTTON_TITLE}',
                                    'name' => '{$APP.LBL_CLOSE_AND_CREATE_BUTTON_TITLE}',
                                    'class' => 'button',
                                    'id' => 'close_create_button',
                                    'onclick' => 'this.form.isSaveFromDetailView.value=true; this.form.status.value=\'Held\'; this.form.action.value=\'Save\';this.form.return_module.value=\'Meetings\';this.form.isDuplicate.value=true;this.form.isSaveAndNew.value=true;this.form.return_action.value=\'EditView\'; this.form.isDuplicate.value=true;this.form.return_id.value=\'{$fields.id.value}\';',

                                ],
                                'template' => '{if $fields.status.value != "Held" && $bean->aclAccess("edit")}[CONTENT]{/if}',
                            ],
                        ],
                        [
                            'customCode' => '{if $fields.status.value != "Held" && $bean->aclAccess("edit")} <input type="hidden" name="isSave" value="false">  <input title="{$APP.LBL_CLOSE_BUTTON_TITLE}"  accesskey="{$APP.LBL_CLOSE_BUTTON_KEY}"  class="button"  onclick="this.form.status.value=\'Held\'; this.form.action.value=\'Save\';this.form.return_module.value=\'Meetings\';this.form.isSave.value=true;this.form.return_action.value=\'DetailView\'; this.form.return_id.value=\'{$fields.id.value}\'"  id="close_button" name="button1"  value="{$APP.LBL_CLOSE_BUTTON_TITLE}"  type="submit">{/if}',
                            //Bug#51778: The custom code will be replaced with sugar_html. customCode will be deplicated.
                            'sugar_html' => [
                                'type' => 'submit',
                                'value' => '{$APP.LBL_CLOSE_BUTTON_TITLE}',
                                'htmlOptions' => [
                                    'title' => '{$APP.LBL_CLOSE_BUTTON_TITLE}',
                                    'accesskey' => '{$APP.LBL_CLOSE_BUTTON_KEY}',
                                    'class' => 'button',
                                    'onclick' => 'this.form.status.value=\'Held\'; this.form.action.value=\'Save\';this.form.return_module.value=\'Meetings\';this.form.isSave.value=true;this.form.return_action.value=\'DetailView\'; this.form.return_id.value=\'{$fields.id.value}\';',
                                    'name' => '{$APP.LBL_CLOSE_BUTTON_TITLE}',
                                    'id' => 'close_button',
                                ],
                                'template' => '{if $fields.status.value != "Held" && $bean->aclAccess("edit")}[CONTENT]{/if}',
                            ],
                        ],
                    ],
                    'hidden' => [
                        '<input type="hidden" name="isSaveAndNew">',
                        '<input type="hidden" name="status">',
                        '<input type="hidden" name="isSaveFromDetailView">',
                        '<input type="hidden" name="isSave">',
                    ],
                    'headerTpl' => 'modules/Meetings/tpls/detailHeader.tpl',
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
                'useTabs' => false,
            ],
            'panels' => [
                'lbl_meeting_information' => [
                    [
                        [
                            'name' => 'name',
                            'label' => 'LBL_SUBJECT',
                        ],
                        'status',
                    ],
                    [
                        'type',

                        [
                            'name' => 'displayed_url',
                        ],
                    ],
                    [
                        [
                            'name' => 'date_start',
                            'label' => 'LBL_DATE_TIME',
                        ],
                        [
                            'name' => 'password',
                        ],
                    ],
                    [
                        [
                            'name' => 'duration',
                            'customCode' => '{$fields.duration_hours.value}{$MOD.LBL_HOURS_ABBREV} {$fields.duration_minutes.value}{$MOD.LBL_MINSS_ABBREV} ',
                            'label' => 'LBL_DURATION',
                        ],
                        [
                            'name' => 'parent_name',
                            'customLabel' => '{sugar_translate label=\'LBL_MODULE_NAME\' module=$fields.parent_type.value}',
                        ],
                    ],
                    [
                        [
                            'name' => 'reminder_time',
                            'customCode' => '{include file="modules/Meetings/tpls/reminders.tpl"}',
                            'label' => 'LBL_REMINDER',
                        ],
                        'location',
                    ],
                    [
                        'description',
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
        ],
    ];
