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
    'discount_price',
    'tax_class',
    'mft_part_num',
    'weight',
];

$serviceFieldDefaults = [
    'service_start_date' => 'now()',
    'service_duration_value' => '1',
    'service_duration_unit' => '"year"',
];

$dependencies['RevenueLineItems']['read_only_fields'] = [
    'hooks' => ['edit'],
    //Trigger formula for the dependency. Defaults to 'true'.
    'trigger' => 'true',
    'triggerFields' => ['product_template_name'],
    'onload' => true,
    //Actions is a list of actions to fire when the trigger is true
    'actions' => [],
];

foreach ($fields as $field) {
    $dependencies['RevenueLineItems']['read_only_fields']['actions'][] = [
        'name' => 'ReadOnly', //Action type
        //The parameters passed in depend on the action type
        'params' => [
            'target' => $field,
            'label' => $field . '_label', //normally <field>_label
            'value' => 'not(equal($product_template_name,""))', //Formula
        ],
    ];
}

/**
 * This dependency set the commit_stage to the correct value and to read only when the sales stage
 * is Closed Won (include) or Closed Lost (exclude)
 */
$dependencies['RevenueLineItems']['commit_stage_readonly_set_value'] = [
    'hooks' => ['edit'],
    //Trigger formula for the dependency. Defaults to 'true'.
    'trigger' => 'true',
    'triggerFields' => ['sales_stage'],
    'onload' => true,
    //Actions is a list of actions to fire when the trigger is true
    'actions' => [
        [
            'name' => 'ReadOnly', //Action type
            //The parameters passed in depend on the action type
            'params' => [
                'target' => 'commit_stage',
                'label' => 'commit_stage_label', //normally <field>_label
                'value' => 'isForecastClosed($sales_stage)', //Formula
            ],
        ],
        [
            'name' => 'SetValue', //Action type
            //The parameters passed in depend on the action type
            'params' => [
                'target' => 'commit_stage',
                'label' => 'commit_stage_label', //normally <field>_label
                'value' => 'ifElse(isForecastClosedWon($sales_stage), "include",
                    ifElse(isForecastClosedLost($sales_stage), "exclude", $commit_stage))', //Formula
            ],
        ],
    ],
];

$dependencies['RevenueLineItems']['set_base_rate'] = [
    'hooks' => ['edit'],
    //Trigger formula for the dependency. Defaults to 'true'.
    'trigger' => 'true',
    'triggerFields' => ['sales_stage'],
    'onload' => true,
    //Actions is a list of actions to fire when the trigger is true
    'actions' => [
        [
            'name' => 'SetValue', //Action type
            //The parameters passed in depend on the action type
            'params' => [
                'target' => 'base_rate',
                'label' => 'base_rate_lable', //normally <field>_label
                'value' => 'ifElse(isForecastClosed($sales_stage), $base_rate, currencyRate($currency_id))', //Formula
            ],
        ],
    ],
];

/**
 * This dependency set the best and worst values to equal likely when the sales stage is
 * set to closed won.
 */
$dependencies['RevenueLineItems']['best_worst_sales_stage_read_only'] = [
    'hooks' => ['edit'],
    //Trigger formula for the dependency. Defaults to 'true'.
    'trigger' => 'true',
    'triggerFields' => ['sales_stage'],
    'onload' => true,
    //Actions is a list of actions to fire when the trigger is true
    'actions' => [
        [
            'name' => 'ReadOnly', //Action type
            //The parameters passed in depend on the action type
            'params' => [
                'target' => 'best_case',
                'label' => 'best_case_label', //normally <field>_label
                'value' => 'isForecastClosed($sales_stage)', //Formula
            ],
        ],
        [
            'name' => 'ReadOnly', //Action type
            //The parameters passed in depend on the action type
            'params' => [
                'target' => 'worst_case',
                'label' => 'worst_case_label', //normally <field>_label
                'value' => 'isForecastClosed($sales_stage)', //Formula
            ],
        ],
        [
            'name' => 'SetValue', //Action type
            //The parameters passed in depend on the action type
            'params' => [
                'target' => 'best_case',
                'label' => 'best_case_label',
                'value' => 'string(ifElse(isForecastClosed($sales_stage), $likely_case, $best_case))',
            ],
        ],
        [
            'name' => 'SetValue', //Action type
            //The parameters passed in depend on the action type
            'params' => [
                'target' => 'worst_case',
                'label' => 'worst_case_label',
                'value' => 'string(ifElse(isForecastClosed($sales_stage), $likely_case, $worst_case))',
            ],
        ],
    ],
];

$dependencies['RevenueLineItems']['likely_case_copy_when_closed'] = [
    'hooks' => ['edit'],
    //Trigger formula for the dependency. Defaults to 'true'.
    'trigger' => 'true',
    'triggerFields' => ['likely_case'],
    'onload' => true,
    //Actions is a list of actions to fire when the trigger is true
    'actions' => [
        [
            'name' => 'SetValue', //Action type
            //The parameters passed in depend on the action type
            'params' => [
                'target' => 'best_case',
                'label' => 'best_case_label',
                'value' => 'string(ifElse(isForecastClosed($sales_stage), $likely_case, $best_case))',
            ],
        ],
        [
            'name' => 'SetValue', //Action type
            //The parameters passed in depend on the action type
            'params' => [
                'target' => 'worst_case',
                'label' => 'worst_case_label',
                'value' => 'string(ifElse(isForecastClosed($sales_stage), $likely_case, $worst_case))',
            ],
        ],
    ],
];

// Handle dependencies related to service fields
$dependencies['RevenueLineItems']['service_fields_required'] = [
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

$dependencies['RevenueLineItems']['service_fields_values'] = [
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

$dependencies['RevenueLineItems']['service_fields_read_only'] = [
    'hooks' => ['edit', 'view'],
    'trigger' => 'true',
    'triggerFields' => ['service', 'product_template_id', 'lock_duration', 'add_on_to_id'],
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
