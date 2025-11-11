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
$viewdefs['base']['view']['stage2-preview'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'labels' => false,
            'fields' => [
                [
                    'name' => 'hint_photo',
                    'type' => 'stage2_image',
                    'size' => 'large',
                    'dismiss_label' => true,
                    'readonly' => true,
                ],
                [
                    'name' => 'full_name',
                    'type' => 'fullname',
                    'label' => 'LBL_NAME',
                    'dismiss_label' => true,
                    'fields' => [
                        'first_name',
                        'last_name',
                    ],
                ],
                [
                    'name' => 'title',
                    'type' => 'text',
                    'dismiss_label' => true,
                    'fields' => [
                        'title',
                    ],
                ],
            ],
        ],
        [
            'name' => 'contacts_basic',
            'columns' => 1,
            'labels' => true,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'phone_work',
                    'label' => 'LBL_LIST_PHONE',
                ],
                [
                    'name' => 'phone_mobile',
                    'label' => 'LBL_MOBILE_PHONE',
                ],
                [
                    'name' => 'phone_other',
                    'label' => 'LBL_OTHER_PHONE',
                ],
                [
                    'name' => 'email',
                    'label' => 'LBL_LIST_EMAIL_ADDRESS',
                ],
            ],
        ],
        [
            'name' => 'contacts_extended',
            'hide' => true,
            'fields' => [
                [
                    'name' => 'hint_education',
                    'label' => 'LBL_HINT_EDUCATION',
                ],
                [
                    'name' => 'hint_education_2',
                    'type' => 'text',
                    'label' => 'LBL_HINT_EDUCATION_2',
                    'dismiss_label' => true,
                    'fields' => [
                        'hint_education_2',
                    ],
                ],
                [
                    'name' => 'hint_job_2',
                    'label' => 'LBL_HINT_JOB_2',
                ],

                [
                    'name' => 'hint_facebook',
                    'type' => 'stage2_url',
                    'label' => 'LBL_HINT_FACEBOOK',
                ],
                [
                    'name' => 'hint_twitter',
                    'type' => 'stage2_url',
                    'label' => 'LBL_HINT_TWITTER',
                ],
            ],
        ],

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
                    'name' => 'account_name',
                    'type' => 'text',
                    'dismiss_label' => true,
                    'fields' => [
                        'account_name',
                    ],
                ],
                [
                    'name' => 'hint_account_website',
                    'type' => 'stage2_url',
                    'dismiss_label' => true,
                    'fields' => [
                        'hint_account_website',
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
                    'name' => 'hint_account_annual_revenue',
                    'label' => 'LBL_HINT_COMPANY_ANNUAL_REVENUE',
                ],
                [
                    'name' => 'hint_account_description',
                    'label' => 'LBL_HINT_COMPANY_DESCRIPTION',
                ],
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
                    'name' => 'hint_account_sic_code_label',
                    'label' => 'LBL_HINT_COMPANY_SIC_CODE_LABEL',
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
                    'name' => 'hint_account_twitter_handle',
                    'type' => 'stage2_url',
                    'label' => 'LBL_HINT_COMPANY_TWITTER',
                ],
                [
                    'name' => 'hint_industry_tags',
                    'label' => 'LBL_HINT_COMPANY_INDUSTRY_TAGS',
                ],
            ],
        ],
    ],
];
