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
$fields = [
    'category_name',
    'list_price',
    'cost_price',
    'tax_class',
    'mft_part_num',
    'weight',
];

$serviceFieldDefaults = [
    'service_start_date' => 'now()',
    'service_duration_value' => '1',
    'service_duration_unit' => '"year"',
];

$dependencies['Products']['read_only_fields'] = [
    'hooks' => ['edit'],
    //Trigger formula for the dependency. Defaults to 'true'.
    'trigger' => 'true',
    'triggerFields' => ['product_template_id'],
    'onload' => true,
    //Actions is a list of actions to fire when the trigger is true
    'actions' => [],
];

foreach ($fields as $field) {
    $dependencies['Products']['read_only_fields']['actions'][] = [
        'name' => 'ReadOnly', //Action type
        //The parameters passed in depend on the action type
        'params' => [
            'target' => $field,
            'label' => $field . '_label', //normally <field>_label
            'value' => 'not(equal($product_template_id,""))', //Formula
        ],
    ];
}

// Handle dependencies related to service fields
$dependencies['Products']['service_fields_required'] = [
    'hooks' => ['edit'],
    'trigger' => 'true',
    'triggerFields' => ['service'],
    'onload' => true,
    'actions' => [
        [
            'name' => 'SetRequired',
            'params' => [
                'target' => 'service_start_date',
                'value' => 'equal($service,1)',
            ],
        ],
        [
            'name' => 'SetRequired',
            'params' => [
                'target' => 'service_duration_value',
                'value' => 'equal($service,1)',
            ],
        ],
        [
            'name' => 'SetRequired',
            'params' => [
                'target' => 'service_duration_unit',
                'value' => 'equal($service,1)',
            ],
        ],
    ],
];

$dependencies['Products']['service_fields_values'] = [
    'hooks' => ['edit'],
    'trigger' => 'true',
    'triggerFields' => ['service'],
    'onload' => true,
    'actions' => [
        [
            'name' => 'SetValue',
            'params' => [
                'target' => 'service_start_date',
                'value' => 'ifElse(
                    equal($service,1),
                    ifElse(
                        equal($service_start_date,""),
                        ' . $serviceFieldDefaults['service_start_date'] . ',
                        $service_start_date
                    ),
                    "")',
            ],
        ],
        [
            'name' => 'SetValue',
            'params' => [
                'target' => 'service_duration_value',
                'value' => 'ifElse(
                    equal($service,1),
                    ifElse(
                        equal($service_duration_value,""),
                        ' . $serviceFieldDefaults['service_duration_value'] . ',
                        $service_duration_value
                    ),
                    "")',
            ],
        ],
        [
            'name' => 'SetValue',
            'params' => [
                'target' => 'service_duration_unit',
                'value' => 'ifElse(
                    equal($service,1),
                    ifElse(
                        equal($service_duration_unit,""),
                        ' . $serviceFieldDefaults['service_duration_unit'] . ',
                        $service_duration_unit
                    ),
                    "")',
            ],
        ], [
            'name' => 'SetValue',
            'params' => [
                'target' => 'renewable',
                'value' => 'ifElse(
                    equal($service, "1"),
                    $renewable,
                    0)',
            ],
        ], [
            'name' => 'SetValue',
            'params' => [
                'target' => 'add_on_to_name',
                'value' => 'ifElse(
                    equal($service, "1"),
                    $add_on_to_name,
                    "")',
            ],
        ], [
            'name' => 'SetValue',
            'params' => [
                'target' => 'add_on_to_id',
                'value' => 'ifElse(
                    equal($service, "1"),
                    $add_on_to_id,
                    "")',
            ],
        ],
        [
            'name' => 'SetValue',
            'params' => [
                'target' => 'service_end_date',
                'value' => 'ifElse(
                    equal($service, "1"),
                    $service_end_date,
                    "")',
            ],
        ],
    ],
];

$dependencies['Products']['service_fields_read_only'] = [
    'hooks' => ['edit', 'view'],
    'trigger' => 'true',
    'triggerFields' => ['service', 'product_template_id', 'lock_duration'],
    'onload' => true,
    'actions' => [
        [
            'name' => 'ReadOnly',
            'params' => [
                'target' => 'service',
                'value' => 'not(equal($product_template_id,""))',
            ],
        ], [
            'name' => 'ReadOnly',
            'params' => [
                'target' => 'renewable',
                'value' => 'equal($service,0)',
            ],
        ], [
            'name' => 'ReadOnly',
            'params' => [
                'target' => 'service_start_date',
                'value' => 'equal($service,0)',
            ],
        ], [
            'name' => 'ReadOnly',
            'params' => [
                'target' => 'service_duration_value',
                'value' => 'or(equal($service,0),and(not(equal($product_template_id,"")),equal($lock_duration,1)), not(equal($add_on_to_id,"")))',
            ],
        ], [
            'name' => 'ReadOnly',
            'params' => [
                'target' => 'service_duration_unit',
                'value' => 'or(equal($service,0),and(not(equal($product_template_id,"")),equal($lock_duration,1)), not(equal($add_on_to_id,"")))',
            ],
        ], [
            'name' => 'ReadOnly',
            'params' => [
                'target' => 'add_on_to_name',
                'value' => 'equal($service, 0)',
            ],
        ],
    ],
];
