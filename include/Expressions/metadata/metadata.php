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
$dd_meta = [

    'triggers' => [

        /* Dropdown to Dropdown */
        'trigger1' => [
            'fields' => [
                'country_select',
            ],
            'condition' => 'contains($country_select, "United States")',
            'dependencies' => [
                'loadUSStates' => [
                    'key' => 'loadUSStates',
                ],
            ],
            'triggeronload' => true,
        ],

        'trigger2' => [
            'fields' => [
                'country_select',
            ],
            'condition' => 'contains($country_select, "Canada")',
            'dependencies' => [
                'loadCNStates' => [
                    'key' => 'loadCNStates',
                ],
            ],
            'triggeronload' => true,
        ],


        /* Dropdown to CF */
        'trigger3' => [
            'fields' => [
                'feet_select', 'inches_select', 'weight_select',
            ],
            'dependencies' => [
                'calculateBMI' => ['key' => 'calculateBMI'],
            ],
            'triggeronload' => true,
        ],

        /* CF to Dropdown */
        'trigger4' => [
            'fields' => [
                'income_input',
            ],
            'condition' => 'greaterThan($income_input, 100000)',
            'dependencies' => [
                'populateRich' => ['key' => 'populateRich'],
            ],
            'triggeronload' => true,
        ],
        'trigger5' => [
            'fields' => [
                'income_input',
            ],
            'condition' => 'greaterThan(100000, $income_input)',
            'dependencies' => [
                'populateMedi' => ['key' => 'populateMedi'],
            ],
            'triggeronload' => true,
        ],
        'trigger6' => [
            'fields' => [
                'salary_input', 'tax_input',
            ],
            'dependencies' => [
                'calculateIncome' => ['key' => 'calculateIncome'],
            ],
            'triggeronload' => true,
        ],


        /* Field to Style */
        'trigger7' => [
            'fields' => [
                'temperature_input',
            ],
            'condition' => 'greaterThan($temperature_input, 100)',
            'dependencies' => [
                'makeHot' => ['key' => 'makeHot'],
            ],
            'triggeronload' => true,
        ],
        'trigger8' => [
            'fields' => [
                'temperature_input',
            ],
            'condition' => 'greaterThan(100, $temperature_input)',
            'dependencies' => [
                'makeCold' => ['key' => 'makeCold'],
            ],
            'triggeronload' => true,
        ],

        /* Dropdown to Style */
        'trigger9' => [
            'fields' => [
                'color_input',
            ],
            'dependencies' => [
                'changeStyle' => ['key' => 'changeStyle'],
            ],
            'triggeronload' => true,
        ],

    ],

    'dependencies' => [

        /* Example 1 */
        'loadUSStates' => [
            'field' => 'state_select',
            'expression' => 'enum("New York", "Pennsylvania", "California", "Florida")',
        ],
        'loadCNStates' => [
            'field' => 'state_select',
            'expression' => 'enum("Ontario", "Quebec", "British Columbia", "Manitoba")',
        ],


        /* Example 2 */
        'calculateBMI' => [
            'field' => 'bmi_output',
            'expression' => 'multiply(divide($weight_select,pow(add(multiply($feet_select, 12),$inches_select),2)),703)',
        ],


        /* Example 3 */
        'calculateIncome' => [
            'field' => 'income_input',
            'expression' => 'subtract($salary_input, multiply($salary_input, divide($tax_input, 100)))',
        ],
        'populateRich' => [
            'field' => 'interest_select',
            'expression' => 'enum("Equestrian", "Sailboating", "Golf", "Pool", "Billiards", "Curling")',
        ],
        'populateMedi' => [
            'field' => 'interest_select',
            'expression' => 'enum("Basketball", "Football", "Tennis", "Lacrosse", "Waterpolo", "Swimming")',
        ],


        /* Example 4 */
        'makeHot' => [
            'type' => 'style',
            'field' => 'heat_index',
            'expression' => '{ backgroundColor: "#FF0022" }',
        ],
        'makeCold' => [
            'type' => 'style',
            'field' => 'heat_index',
            'expression' => '{ backgroundColor: "blue" }',
        ],


        /* Example 5 */
        'changeStyle' => [
            'type' => 'style',
            'field' => 'color_index',
            'expression' => '{ backgroundColor: { evaluate: \'concat($color_input, "")\' } }',
        ],
    ],
];


$dep_meta = [
    'triggers' => [

        /* Dropdown to Dropdown */
        'trigger1' => [
            'fields' => [
                'lastname',
            ],
            'condition' => 'contains($lastname, "Smith")',
            'dependencies' => [
                'met' => [
                    'makeReq' => [
                        'key' => 'makeReq',
                    ],
                ],
                'unmet' => [
                    'makeNReq' => [
                        'key' => 'makeNReq',
                    ],
                ],
            ],
            'triggeronload' => true,
        ],
    ],

    'dependencies' => [

        /* Example 1 */
        'makeReq' => [
            'type' => 'require',
            'field' => 'number',
            'require' => true,
            'label_id' => 'number_lbl',
        ],


        /* Example 1 */
        'makeNReq' => [
            'type' => 'require',
            'field' => 'number',
            'require' => false,
            'label_id' => 'number_lbl',
        ],
    ],
];

$val_meta = [
    'myform' => [
        'firstname' => [
            'required' => true,
            'conditions' => [
                'alpha' => [
                ],
                'binarydep' => [
                    'sibling' => 'number',
                ],
            ],
        ],
        'lastname' => [
            'required' => true,
            'conditions' => [
                'alpha' => [
                ],
                'binarydep' => [
                    'sibling' => 'number',
                ],
            ],
        ],
        'email' => [
            'required' => true,
            'conditions' => [
                'email' => [
                    'message' => 'default',
                ],
            ],
        ],
        'phone' => [
            'required' => true,
            'conditions' => [
                'phone' => [

                ],
            ],
        ],
        'date' => [
            'conditions' => [
                'date' => [
                ],
            ],
        ],
        'number' => [
            'required' => true,
            'conditions' => [
                'number' => [
                ],
            ],
        ],
    ],
];


require_once 'include/Expressions/metadata/metatojs.php';

echo getJSFromDDMeta($dep_meta);
echo getJSFromValidationMeta($val_meta);
