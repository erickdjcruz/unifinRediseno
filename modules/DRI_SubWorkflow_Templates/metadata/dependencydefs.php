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
 * This dependency sets the sort_order field to read only after record creation.
 */
$dependencies['DRI_SubWorkflow_Templates']['sort_order_dep'] = [
    'hooks' => ['edit'],
    // Trigger formula for the dependency. Defaults to 'true'.
    'trigger' => 'true',
    'triggerFields' => ['trigger'],
    'onload' => true,
    // Actions is a list of actions to fire when the trigger is true
    'actions' => [
        [
            'name' => 'ReadOnly', // Action type
            // The parameters passed in depend on the action type
            'params' => [
                'target' => 'sort_order',
                'label' => 'sort_order_label', // normally <field>_label
                'value' => 'not(equal($id, ""))',
            ],
        ],
    ],
];
