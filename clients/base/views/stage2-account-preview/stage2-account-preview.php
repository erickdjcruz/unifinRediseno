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
$viewdefs['base']['view']['stage2-account-preview'] = [
    'panels' => [
        [
            'name' => 'company_header',
            'labels' => false,
            'fields' => [
                [
                    'name' => 'hint_account_logo',
                    'type' => 'stage2_image',
                    'label' => 'LBL_HINT_COMPANY_LOGO',
                    'dismiss_label' => true,
                    'fields' => [
                        'hint_account_logo',
                    ],
                ],
                [
                    'name' => 'name',
                    'type' => 'text',
                    'dismiss_label' => true,
                    'fields' => [
                        'name',
                    ],
                ],
                [
                    'name' => 'website',
                    'type' => 'stage2_url',
                    'dismiss_label' => true,
                    'fields' => [
                        'website',
                    ],
                ],
            ],
        ],
        [
            'name' => 'company_info',
            'columns' => 1,
            'labels' => true,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'hint_account_size',
                    'label' => 'LBL_HINT_COMPANY_SIZE',
                ],
                [
                    'name' => 'hint_account_industry',
                    'label' => 'LBL_HINT_COMPANY_INDUSTRY',
                ],
                [
                    'name' => 'hint_account_location',
                    'label' => 'LBL_HINT_COMPANY_LOCATION',
                ],
                [
                    'name' => 'annual_revenue',
                    'label' => 'LBL_HINT_COMPANY_ANNUAL_REVENUE',
                ],
                [
                    'name' => 'description',
                    'label' => 'LBL_HINT_COMPANY_DESCRIPTION',
                    'person_name' => 'hint_account_description',
                    'person_label' => 'LBL_HINT_COMPANY_DESCRIPTION',
                ],
                'lead_source',
            ],
        ],
        [
            'name' => 'company_extended',
            'hide' => true,
            'fields' => [
                [
                    'name' => 'hint_account_naics_code_lbl',
                    'label' => 'LBL_HINT_COMPANY_NAICS_CODE_LABEL',
                ],
                [
                    'name' => 'sic_code',
                    'label' => 'LBL_HINT_COMPANY_SIC_CODE_LABEL',
                    'person_name' => 'hint_account_sic_code_label',
                    'person_label' => 'LBL_HINT_COMPANY_SIC_CODE_LABEL',
                ],
                [
                    'name' => 'hint_account_fiscal_year_end',
                    'label' => 'LBL_HINT_COMPANY_FISCAL_YEAR_END',
                ],
                [
                    'name' => 'hint_account_founded_year',
                    'label' => 'LBL_HINT_COMPANY_FOUNDED_YEAR',
                ],
                [
                    'name' => 'hint_account_facebook_handle',
                    'type' => 'stage2_url',
                    'label' => 'LBL_HINT_COMPANY_FACEBOOK',
                ],
                [
                    'name' => 'twitter',
                    'type' => 'stage2_url',
                    'label' => 'LBL_HINT_COMPANY_TWITTER',
                    'person_name' => 'hint_account_twitter_handle',
                    'person_label' => 'LBL_HINT_COMPANY_TWITTER',
                ],
                [
                    'name' => 'hint_account_industry_tags',
                    'label' => 'LBL_HINT_COMPANY_INDUSTRY_TAGS',
                    'person_name' => 'hint_industry_tags',
                    'person_label' => 'LBL_HINT_COMPANY_INDUSTRY_TAGS',
                ],
            ],
        ],
    ],
];
