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
$viewdefs ['Tasks'] =
    [
        'EditView' => [
            'templateMeta' => [
                'form' => [
                    'hidden' => [
                        '<input type="hidden" name="isSaveAndNew" value="false">',
                    ],
                    'buttons' => [
                        'SAVE',
                        'CANCEL',

                        [
                            // FIXME review these SUGAR.ajaxUI.* methods
                            'customCode' => '{if $fields.status.value != "Completed"}<input title="{$APP.LBL_CLOSE_AND_CREATE_BUTTON_TITLE}" class="button" onclick="document.getElementById(\'status\').value=\'Completed\'; this.form.action.value=\'Save\'; this.form.return_module.value=\'Tasks\'; this.form.isDuplicate.value=true; this.form.isSaveAndNew.value=true; this.form.return_action.value=\'EditView\'; this.form.return_id.value=\'{$fields.id.value}\'; if(check_form(\'EditView\'))SUGAR.ajaxUI.submitForm(this.form);" type="button" name="button" value="{$APP.LBL_CLOSE_AND_CREATE_BUTTON_LABEL}">{/if}',
                        ],
                    ],
                ],
                'maxColumns' => '2',
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
                'useTabs' => false,
            ],
            'panels' => [
                'lbl_task_information' => [

                    [

                        [
                            'name' => 'name',
                            'displayParams' => [
                                'required' => true,
                            ],
                        ],

                        [
                            'name' => 'status',
                            'displayParams' => [
                                'required' => true,
                            ],
                        ],
                    ],

                    [

                        [
                            'name' => 'date_start',
                            'type' => 'datetimecombo',
                            'displayParams' => [
                                'showNoneCheckbox' => true,
                                'showFormats' => true,
                            ],
                        ],

                        [
                            'name' => 'parent_name',
                            'label' => 'LBL_LIST_RELATED_TO',
                        ],
                    ],

                    [

                        [
                            'name' => 'date_due',
                            'type' => 'datetimecombo',
                            'displayParams' => [
                                'showNoneCheckbox' => true,
                                'showFormats' => true,
                            ],
                        ],

                        [
                            'name' => 'contact_name',
                            'label' => 'LBL_CONTACT_NAME',
                        ],
                    ],

                    [

                        [
                            'name' => 'priority',
                            'displayParams' => [
                                'required' => true,
                            ],
                        ],

                    ],

                    [

                        [
                            'name' => 'description',
                        ],
                    ],
                ],

                'LBL_PANEL_ASSIGNMENT' => [
                    [
                        'assigned_user_name',
                        ['name' => 'team_name'],
                    ],
                ],

            ],
        ],
    ];
