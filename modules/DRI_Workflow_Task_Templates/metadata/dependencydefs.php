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
/**
 * This dependency sets the task_due_date_type field required if the activity_type is Meetings or Calls.
 */
$dependencies['DRI_Workflow_Task_Templates']['days_type_dep'] = [
    'hooks' => ['all'],
    // Trigger formula for the dependency. Defaults to 'true'.
    'trigger' => 'true',
    'triggerFields' => ['activity_type'],
    'onload' => true,
    // Actions is a list of actions to fire when the trigger is true
    'actions' => [
        [
            'name' => 'SetRequired', // Action type
            // The parameters passed in depend on the action type
            'params' => [
                'target' => 'task_due_date_type',
                'label' => 'task_due_date_type_label', // normally <field>_label
                'value' => 'or(equal($activity_type, "Meetings"), equal($activity_type, "Calls"))',
            ],
        ],
    ],
];
