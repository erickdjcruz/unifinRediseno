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

global $alert_meta_user_array;
$alert_meta_user_array = [
    'user1' => [
        'user_display_type' => 'user1'
        , 'array_type' => 'future'
        , 'field_value' => 'created_by'
        , 'href_text' => 'LBL_BLANK'
        , 'href_text2' => 'LBL_USER'         //href
        , 'href_text3' => 'LBL_USER1'
        , 'base_type' => 'all',
    ]
    , 'user2' => [
        'user_display_type' => 'user2'
        , 'array_type' => 'past'
        , 'field_value' => 'modified_user_id'
        , 'href_text' => 'LBL_BLANK'
        , 'href_text2' => 'LBL_USER'             //href
        , 'href_text3' => 'LBL_USER2'
        , 'base_type' => 'all',
    ]
    /*
,'user3' => Array(
        'user_display_type' => 'user3'
        ,'array_type' => 'future'
        ,'field_value' => 'modified_user_id'
        ,'href_text' => 'LBL_USER3'
        ,'href_text2' => 'LBL_USER'				//href
        ,'href_text3' => 'LBL_USER3b'
        ,'base_type' => 'normal'
    )
*/
    , 'user4' => [
        'user_display_type' => 'user4'
        , 'array_type' => 'future'
        , 'field_value' => 'assigned_user_id'
        , 'href_text' => 'LBL_BLANK'
        , 'href_text2' => 'LBL_USER'                 //href
        , 'href_text3' => 'LBL_USER4'
        , 'base_type' => 'all',
    ]
    , 'user5' => [
        'user_display_type' => 'user5'
        , 'array_type' => 'past'
        , 'field_value' => 'assigned_user_id'
        , 'href_text' => 'LBL_BLANK'
        , 'href_text2' => 'LBL_USER'                 //href
        , 'href_text3' => 'LBL_USER5'
        , 'base_type' => 'all',
    ],
];

$process_dictionary['AlertsCreateStep1'] = ['action' => 'CreateStep1',
    'elements' => [

        'current_user' => [
            'trigger_type' => 'all',
            'top' => [
                'type' => 'radio',
                'name' => 'user_type',
                'value' => 'current_user',
                'options' => [
                    '1' => ['vname' => 'LBL_ALERT_CURRENT_USER_TITLE'],
                    //end top options
                ],
                //end top
            ],
            'bottom' => [
                'type' => 'text',
                'value' => 'current_user',
                'show_address_type' => false,
                'related' => '0',
                'options' => [
                    '1' => ['vname' => 'LBL_ALERT_CURRENT_USER_TITLE', 'text_type' => 'static'],
                    //end bottom options
                ],
                //end bottom
            ],
//end current user array
        ],
        'rel_user' => [
            'trigger_type' => 'all',
            'top' => [
                'type' => 'radio',
                'name' => 'user_type',
                'value' => 'rel_user',
                'options' => [
                    '1' => ['vname' => 'LBL_ALERT_REL_USER_TITLE'],
                    //end top options
                ],
                //end top
            ],
            'bottom' => [
                'type' => 'text',
                'value' => 'rel_user',
                'show_address_type' => false,
                'related' => ['count' => '2', 'rel1_field' => 'rel_module1', 'rel2_field' => 'rel_module2'],
                'options' => [
                    '1' => ['vname' => 'LBL_ALERT_REL_USER', 'text_type' => 'static'],
                    '2' => [
                        'vname' => 'LBL_RECORD',
                        'default' => 'on',
                        'text_type' => 'dynamic',
                        'type' => 'href',
                        'value' => 'rel_module1',
                        'value2' => 'rel_module2',
                        'value_type' => 'relrel_module',
                        'jscript_function' => 'get_selector',
                        'jscript_content' => ['self'],
                    ],
                    //end bottom options
                ],
                //end bottom
            ],
//end rel_user array
        ],
        'rel_user_custom' => [
            'trigger_type' => 'all',
            'top' => [
                'type' => 'radio',
                'name' => 'user_type',
                'value' => 'rel_user_custom',
                'options' => [
                    '1' => ['vname' => 'LBL_ALERT_REL_USER_CUSTOM_TITLE'],
                    //end top options
                ],
                //end top
            ],
            'bottom' => [
                'type' => 'text',
                'value' => 'rel_user_custom',
                'show_address_type' => false,
                'related' => ['count' => '2', 'rel1_field' => 'rel_module1', 'rel2_field' => 'rel_module2'],
                'options' => [
                    '1' => ['vname' => 'LBL_ALERT_REL_USER_CUSTOM', 'text_type' => 'static'],
                    '2' => [
                        'vname' => 'LBL_RECORD',
                        'default' => 'on',
                        'text_type' => 'dynamic',
                        'type' => 'href',
                        'value' => 'rel_module1',
                        'value2' => 'rel_module2',
                        'value_type' => 'relrel_module',
                        'jscript_function' => 'get_selector',
                        'jscript_content' => ['self'],
                    ],
                    //end bottom options
                ],
                //end bottom
            ],
//end rel_user_custom array
        ],
        'trig_user_custom' => [
            'trigger_type' => 'all',
            'top' => [
                'type' => 'radio',
                'name' => 'user_type',
                'value' => 'trig_user_custom',
                'options' => [
                    '1' => ['vname' => 'LBL_ALERT_TRIG_USER_CUSTOM_TITLE'],
                    //end top options
                ],
                //end top
            ],
            'bottom' => [
                'type' => 'text',
                'value' => 'trig_user_custom',
                'show_address_type' => false,
                'related' => '0',
                'options' => [
                    '1' => ['vname' => 'LBL_ALERT_TRIG_USER_CUSTOM', 'text_type' => 'static'],
                    //end bottom options
                ],
                //end bottom
            ],
//end trig_user_custom array
        ],
        'specific_user' => [
            'trigger_type' => 'all',
            'top' => [
                'type' => 'radio',
                'name' => 'user_type',
                'value' => 'specific_user',
                'options' => [
                    '1' => ['vname' => 'LBL_ALERT_SPECIFIC_USER_TITLE'],
                    //end top options
                ],
                //end top
            ],
            'bottom' => [
                'type' => 'text',
                'value' => 'specific_user',
                'show_address_type' => true,
                'related' => '0',
                'options' => [
                    '1' => ['vname' => 'LBL_ALERT_SPECIFIC_USER_TITLE', 'text_type' => 'static'],
                    '2' => [
                        'vname' => 'LBL_USER',
                        'default' => 'on',
                        'text_type' => 'dynamic',
                        'type' => 'href',
                        'value' => 'field_value',
                        'value_type' => 'special_exp',
                        'value_exp_type' => 'assigned_user_id',
                        'jscript_function' => 'get_single_selector',
                        'jscript_content' => ['self', 'assigned_user_id'],
                    ],
                    //end bottom options
                ],
                //end bottom
            ],
//end specific_user array
        ],

        'specific_team' => [
            'trigger_type' => 'all',
            'top' => [
                'type' => 'radio',
                'name' => 'user_type',
                'value' => 'specific_team',
                'options' => [
                    '1' => ['vname' => 'LBL_ALERT_SPECIFIC_TEAM_TITLE'],
                    //end top options
                ],
                //end top
            ],
            'bottom' => [
                'type' => 'text',
                'value' => 'specific_team',
                'show_address_type' => true,
                'related' => '0',
                'options' => [
                    '1' => ['vname' => 'LBL_ALERT_SPECIFIC_TEAM_TITLE', 'text_type' => 'static'],
                    '2' => [
                        'vname' => 'LBL_TEAM',
                        'default' => 'on',
                        'text_type' => 'dynamic',
                        'type' => 'href',
                        'value' => 'field_value',
                        'value_type' => 'special_exp',
                        'value_exp_type' => 'team_list',
                        'jscript_function' => 'get_single_selector',
                        'jscript_content' => ['self', 'team_list'],
                    ],
                    //end bottom options
                ],
                //end bottom
            ],
//end specific_team array
        ],
        'specific_role' => [
            'trigger_type' => 'all',
            'top' => [
                'type' => 'radio',
                'name' => 'user_type',
                'value' => 'specific_role',
                'options' => [
                    '1' => ['vname' => 'LBL_ALERT_SPECIFIC_ROLE_TITLE'],
                    //end top options
                ],
                //end top
            ],
            'bottom' => [
                'type' => 'text',
                'value' => 'specific_role',
                'show_address_type' => true,
                'related' => '0',
                'options' => [
                    '1' => ['vname' => 'LBL_ALERT_SPECIFIC_ROLE_TITLE', 'text_type' => 'static'],
                    '2' => [
                        'vname' => 'LBL_ROLE',
                        'default' => 'on',
                        'text_type' => 'dynamic',
                        'type' => 'href',
                        'value' => 'field_value',
                        'value_type' => 'special_exp',
                        'value_exp_type' => 'role',
                        'jscript_function' => 'get_single_selector',
                        'jscript_content' => ['self', 'role'],
                    ],
                    //end bottom options
                ],
                //end bottom
            ],
//end specific_role array
        ],
        'login_user' => [
            'trigger_type' => 'non_time',
            'top' => [
                'type' => 'radio',
                'name' => 'user_type',
                'value' => 'login_user',
                'related' => '0',
                'options' => [
                    '1' => ['vname' => 'LBL_ALERT_LOGIN_USER_TITLE'],
                    //end top options
                ],
                //end top
            ],
            'bottom' => [
                'type' => 'text',
                'value' => 'login_user',
                'show_address_type' => true,
                'options' => [
                    '1' => ['vname' => 'LBL_ALERT_LOGIN_USER_TITLE', 'text_type' => 'static'],
                    //end bottom options
                ],
                //end bottom
            ],
//end login_user array
        ],


//Begin Assigned Team Target
        'assigned_team_target' => [
            'trigger_type' => 'all',
            'top' => [
                'type' => 'radio',
                'name' => 'user_type',
                'value' => 'assigned_team_target',
                'options' => [
                    '1' => ['vname' => 'LBL_ALERT_SPECIFIC_TEAM_TARGET_TITLE'],
                    //end top options
                ],
                //end top
            ],
            'bottom' => [
                'type' => 'text',
                'value' => 'assigned_team_target',
                'show_address_type' => true,
                'related' => '0',
                'options' => [
                    '1' => ['vname' => 'LBL_ALERT_SPECIFIC_TEAM_TARGET', 'text_type' => 'static'],
                    //end bottom options
                ],
                //end bottom
            ],
        ],
//End Specific Team Target

    ], //End All Elements

    'hide_others' => [
        'target_field' => 'user_type',
        'target_element' => [
            'specific_user' => ['specific_user', 'specific_team', 'specific_role', 'login_user'],
            'specific_team' => ['specific_user', 'specific_team', 'specific_role', 'login_user'],
            'specific_role' => ['specific_user', 'specific_team', 'specific_role', 'login_user'],
            'login_user' => ['specific_user', 'specific_team', 'specific_role', 'login_user'],
        ],

    ],
//End process dictionary AlertsCreateStep1
];
