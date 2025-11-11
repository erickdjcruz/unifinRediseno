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
 * This dependency sets the service duration, service start date and quantity as required
 */
$dependencies['PurchasedLineItems']['set_required_fields'] = [
    'hooks' => ['edit'],
    //Trigger formula for the dependency. Defaults to 'true'.
    'trigger' => 'true',
    'triggerFields' => ['trigger_event'],
    'onload' => true,
    //Actions is a list of actions to fire when the trigger is true
    'actions' => [
        [
            'name' => 'SetRequired', //Action type
            //The parameters passed in depend on the action type
            'params' => [
                'target' => 'quantity',
                'label' => 'quantity_label', //normally <field>_label
                'value' => 'true', //Formula
            ],
        ],
        [
            'name' => 'SetRequired', //Action type
            //The parameters passed in depend on the action type
            'params' => [
                'target' => 'service_start_date',
                'label' => 'service_start_date_label', //normally <field>_label
                'value' => 'true', //Formula
            ],
        ],
        [
            'name' => 'SetRequired', //Action type
            //The parameters passed in depend on the action type
            'params' => [
                'target' => 'service_duration_value',
                'label' => 'service_duration_value_label', //normally <field>_label
                'value' => 'true', //Formula
            ],
        ],
        [
            'name' => 'SetRequired', //Action type
            //The parameters passed in depend on the action type
            'params' => [
                'target' => 'service_duration_unit',
                'label' => 'service_duration_unit_label', //normally <field>_label
                'value' => 'true', //Formula
            ],
        ],
    ],
];

/**
 * This dependency sets the service_duration to be visible when the PLI is a service i.e., service = '1'
 */
$dependencies['PurchasedLineItems']['set_visibility_fields'] = [
    'hooks' => ['edit', 'view'],
    //Trigger formula for the dependency. Defaults to 'true'.
    'trigger' => 'true',
    'triggerFields' => ['service'],
    'onload' => true,
    //Actions is a list of actions to fire when the trigger is true
    'actions' => [
        [
            'name' => 'SetVisibility', //Action type
            //The parameters passed in depend on the action type
            'params' => [
                'target' => 'service_duration_unit',
                'label' => 'service_duration_unit_label', //normally <field>_label
                'value' => 'equal($service, "1")', //Formula
            ],
        ],
        [
            'name' => 'SetVisibility', //Action type
            //The parameters passed in depend on the action type
            'params' => [
                'target' => 'service_duration_value',
                'label' => 'service_duration_value_label', //normally <field>_label
                'value' => 'equal($service, "1")', //Formula
            ],
        ],
        [
            'name' => 'SetVisibility', //Action type
            //The parameters passed in depend on the action type
            'params' => [
                'target' => 'service_duration',
                'label' => 'service_duration_label', //normally <field>_label
                'value' => 'equal($service, "1")', //Formula
            ],
        ],
        [
            'name' => 'SetVisibility', //Action type
            //The parameters passed in depend on the action type
            'params' => [
                'target' => 'renewable',
                'label' => 'renewable_label', //normally <field>_label
                'value' => 'equal($service, "1")', //Formula
            ],
        ],
    ],
];

$dependencies['PurchasedLineItems']['set_readonly_fields'] = [
    'hooks' => ['edit', 'view'],
    //Trigger formula for the dependency. Defaults to 'true'.
    'trigger' => 'true',
    'triggerFields' => ['trigger_event'],
    'onload' => true,
    //Actions is a list of actions to fire when the trigger is true
    'actions' => [
        [
            'name' => 'ReadOnly', //Action type
            //The parameters passed in depend on the action type
            'params' => [
                'target' => 'service',
                'label' => 'service_label', //normally <field>_label
                'value' => 'true', //Formula
            ],
        ],
    ],
];
