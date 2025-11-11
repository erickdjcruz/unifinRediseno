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

$vardefs = [
    'fields' => [
        'aws_lens_data' => [
            'name' => 'aws_lens_data',
            'vname' => 'LBL_AWS_LENS_DATA',
            'type' => 'text',
            'reportable' => false,
            'studio' => false,
            'comment' => 'Raw data from the aws lens service',
        ],
        'sentiment_score_agent' => [
            'name' => 'sentiment_score_agent',
            'vname' => 'LBL_SENTIMENT_SCORE_AGENT',
            'type' => 'decimal',
            'len' => '10',
            'precision' => '2',
            'comment' => 'The sentiment score for the agent ranging from -5 to 5',
            'reportable' => false,
            'displayParams' => [
                'type' => 'sentiment',
                'readonly' => true,
                'icon' => [
                    'type' => 'sicon-sugar-predict',
                    'tooltip' => 'LBL_PREDICT_TOOLTIP',
                ],
            ],
        ],
        'sentiment_score_agent_string' => [
            'name' => 'sentiment_score_agent_string',
            'vname' => 'LBL_SENTIMENT_SCORE_AGENT',
            'type' => 'enum',
            'options' => 'sentiment_score_dom',
            'readonly' => true,
            'calculated' => true,
            'enforced' => true,
            'formula' => 'sentimentScoreToStr($sentiment_score_agent)',
            'studio' => false,
            'processes' => [
                'types' => [
                    'BRR' => true,
                    'PD' => true,
                ],
            ],
        ],
        'sentiment_score_customer' => [
            'name' => 'sentiment_score_customer',
            'vname' => 'LBL_SENTIMENT_SCORE_CUSTOMER',
            'type' => 'decimal',
            'len' => '10',
            'precision' => '2',
            'comment' => 'The sentiment score for the customer ranging from -5 to 5',
            'reportable' => false,
            'displayParams' => [
                'type' => 'sentiment',
                'readonly' => true,
                'icon' => [
                    'type' => 'sicon-sugar-predict',
                    'tooltip' => 'LBL_PREDICT_TOOLTIP',
                ],
            ],
        ],
        'sentiment_score_customer_string' => [
            'name' => 'sentiment_score_customer_string',
            'vname' => 'LBL_SENTIMENT_SCORE_CUSTOMER',
            'type' => 'enum',
            'options' => 'sentiment_score_dom',
            'readonly' => true,
            'calculated' => true,
            'enforced' => true,
            'formula' => 'sentimentScoreToStr($sentiment_score_customer)',
            'studio' => false,
            'processes' => [
                'types' => [
                    'BRR' => true,
                    'PD' => true,
                ],
            ],
        ],
        'sentiment_score_agent_first_quarter' => [
            'name' => 'sentiment_score_agent_first_quarter',
            'vname' => 'LBL_SENTIMENT_SCORE_AGENT_FIRST_QUARTER',
            'type' => 'decimal',
            'len' => '10',
            'precision' => '2',
            'studio' => false,
            'comment' => 'The sentiment score for the agent during the first quarter ranging from -5 to 5',
        ],
        'sentiment_score_agent_second_quarter' => [
            'name' => 'sentiment_score_agent_second_quarter',
            'vname' => 'LBL_SENTIMENT_SCORE_AGENT_SECOND_QUARTER',
            'type' => 'decimal',
            'len' => '10',
            'precision' => '2',
            'studio' => false,
            'comment' => 'The sentiment score for the agent during the second quarter ranging from -5 to 5',
        ],
        'sentiment_score_agent_third_quarter' => [
            'name' => 'sentiment_score_agent_third_quarter',
            'vname' => 'LBL_SENTIMENT_SCORE_AGENT_THIRD_QUARTER',
            'type' => 'decimal',
            'len' => '10',
            'precision' => '2',
            'studio' => false,
            'comment' => 'The sentiment score for the agent during the third quarter ranging from -5 to 5',
        ],
        'sentiment_score_agent_fourth_quarter' => [
            'name' => 'sentiment_score_agent_fourth_quarter',
            'vname' => 'LBL_SENTIMENT_SCORE_AGENT_FOURTH_QUARTER',
            'type' => 'decimal',
            'len' => '10',
            'precision' => '2',
            'studio' => false,
            'comment' => 'The sentiment score for the agent during the fourth quarter ranging from -5 to 5',
        ],
        'sentiment_score_customer_first_quarter' => [
            'name' => 'sentiment_score_customer_first_quarter',
            'vname' => 'LBL_SENTIMENT_SCORE_CUSTOMER_FIRST_QUARTER',
            'type' => 'decimal',
            'len' => '10',
            'precision' => '2',
            'studio' => false,
            'comment' => 'The sentiment score for the customer during the first quarter ranging from -5 to 5',
        ],
        'sentiment_score_customer_second_quarter' => [
            'name' => 'sentiment_score_customer_second_quarter',
            'vname' => 'LBL_SENTIMENT_SCORE_CUSTOMER_SECOND_QUARTER',
            'type' => 'decimal',
            'len' => '10',
            'precision' => '2',
            'studio' => false,
            'comment' => 'The sentiment score for the customer during the second quarter ranging from -5 to 5',
        ],
        'sentiment_score_customer_third_quarter' => [
            'name' => 'sentiment_score_customer_third_quarter',
            'vname' => 'LBL_SENTIMENT_SCORE_CUSTOMER_THIRD_QUARTER',
            'type' => 'decimal',
            'len' => '10',
            'precision' => '2',
            'studio' => false,
            'comment' => 'The sentiment score for the customer during the third quarter ranging from -5 to 5',
        ],
        'sentiment_score_customer_fourth_quarter' => [
            'name' => 'sentiment_score_customer_fourth_quarter',
            'vname' => 'LBL_SENTIMENT_SCORE_CUSTOMER_FOURTH_QUARTER',
            'type' => 'decimal',
            'len' => '10',
            'precision' => '2',
            'studio' => false,
            'comment' => 'The sentiment score for the customer during the fourth quarter ranging from -5 to 5',
        ],
    ],
];
