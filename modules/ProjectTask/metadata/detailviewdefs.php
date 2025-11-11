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
$viewdefs['ProjectTask']['DetailView'] = [
    'templateMeta' => ['maxColumns' => '2',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
        'includes' => [
            ['file' => 'modules/ProjectTask/ProjectTask.js'],
        ],
        'form' => [
            'buttons' => ['EDIT',

                ['customCode' => '{if $bean->aclAccess("edit")}<input type="submit" name="EditTaskInGrid" value=" {$MOD.LBL_EDIT_TASK_IN_GRID_TITLE} " ' .
                    'title="{$MOD.LBL_EDIT_TASK_IN_GRID_TITLE}"  ' .
                    'class="button" onclick="this.form.record.value=\'{$fields.project_id.value}\';prep_edit_task_in_grid(this.form);" />{/if}',
                    //Bug#51778: The custom code will be replaced with sugar_html. customCode will be deplicated.
                    'sugar_html' => [
                        'type' => 'submit',
                        'value' => ' {$MOD.LBL_EDIT_TASK_IN_GRID_TITLE} ',
                        'htmlOptions' => [
                            'title' => '{$MOD.LBL_EDIT_TASK_IN_GRID_TITLE}',
                            'class' => 'button',
                            'name' => 'EditTaskInGrid',
                            'onclick' => 'this.form.record.value=\'{$fields.project_id.value}\';prep_edit_task_in_grid(this.form);',
                        ],
                        'template' => '{if $bean->aclAccess("edit")}[CONTENT]{/if}',
                    ],

                ],
            ],
            'hideAudit' => true,
        ],

    ],
    'panels' => [
        'default' => [

            [
                'name',

                [
                    'name' => 'project_task_id',
                    'label' => 'LBL_TASK_ID',
                ],
            ],

            [
                'date_start',
                'date_finish',
            ],
            [
                [
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_ASSIGNED_USER_ID',
                ],
                [

                    'name' => 'team_name',
                ],
            ],


            [
                [
                    'name' => 'duration',
                    'customCode' => '{$fields.duration.value}&nbsp;{$fields.duration_unit.value}',
                    'label' => 'LBL_DURATION',
                ],
            ],

            [
                'status',
                'priority',
            ],

            [
                'percent_complete',
                [
                    'name' => 'milestone_flag',
                    'label' => 'LBL_MILESTONE_FLAG',
                ],
            ],


            [
                [
                    'name' => 'resource_id',
                    'customCode' => '{$resource}',
                    'label' => 'LBL_RESOURCE',
                ],
            ],

            [

                [
                    'name' => 'project_name',
                    'customCode' => '<a href="index.php?module=Project&action=DetailView&record={$fields.project_id.value}">{$fields.project_name.value}&nbsp;</a>',
                    'label' => 'LBL_PARENT_ID',
                ],

                [
                    'name' => 'actual_duration',
                    'customCode' => '{$fields.actual_duration.value}&nbsp;{$fields.duration_unit.value}',
                    'label' => 'LBL_ACTUAL_DURATION',
                ],
            ],

            [

                'task_number',
                'order_number',
            ],

            [
                'estimated_effort',
                'utilization',
            ],

            [

                [
                    'name' => 'description',
                ],
            ],

        ],
    ],


];
