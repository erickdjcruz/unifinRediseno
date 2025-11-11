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

$viewdefs['Contacts']['base']['view']['record'] = [
    'buttons' => [
        [
            'type' => 'button',
            'name' => 'cancel_button',
            'label' => 'LBL_CANCEL_BUTTON_LABEL',
            'css_class' => 'btn-invisible btn-link',
            'showOn' => 'edit',
            'events' => [
                'click' => 'button:cancel_button:click',
            ],
        ],
        [
            'type' => 'rowaction',
            'event' => 'button:save_button:click',
            'name' => 'save_button',
            'label' => 'LBL_SAVE_BUTTON_LABEL',
            'css_class' => 'btn btn-primary',
            'showOn' => 'edit',
            'acl_action' => 'edit',
        ],
        [
            'type' => 'actiondropdown',
            'name' => 'main_dropdown',
            'primary' => true,
            'showOn' => 'view',
            'buttons' => [
                [
                    'type' => 'rowaction',
                    'event' => 'button:edit_button:click',
                    'name' => 'edit_button',
                    'label' => 'LBL_EDIT_BUTTON_LABEL',
                    'acl_action' => 'edit',
                ],
                [
                    'type' => 'shareaction',
                    'name' => 'share',
                    'label' => 'LBL_RECORD_SHARE_BUTTON',
                    'acl_action' => 'view',
                ],
                [
                    'type' => 'pdfaction',
                    'name' => 'download-pdf',
                    'label' => 'LBL_PDF_VIEW',
                    'action' => 'download',
                    'acl_action' => 'view',
                ],
                [
                    'type' => 'pdfaction',
                    'name' => 'email-pdf',
                    'label' => 'LBL_PDF_EMAIL',
                    'action' => 'email',
                    'acl_action' => 'view',
                ],
                [
                    'type' => 'divider',
                ],
                [
                    'type' => 'manage-subscription',
                    'name' => 'manage_subscription_button',
                    'label' => 'LBL_MANAGE_SUBSCRIPTIONS',
                    'showOn' => 'view',
                    'value' => 'edit',
                ],
                [
                    'type' => 'vcard',
                    'name' => 'vcard_button',
                    'label' => 'LBL_VCARD_DOWNLOAD',
                    'acl_action' => 'edit',
                ],
                [
                    'type' => 'divider',
                ],
                [
                    'type' => 'rowaction',
                    'event' => 'button:find_duplicates_button:click',
                    'name' => 'find_duplicates',
                    'label' => 'LBL_DUP_MERGE',
                    'acl_action' => 'edit',
                ],
                [
                    'type' => 'rowaction',
                    'event' => 'button:duplicate_button:click',
                    'name' => 'duplicate_button',
                    'label' => 'LBL_DUPLICATE_BUTTON_LABEL',
                    'acl_module' => 'Contacts',
                    'acl_action' => 'create',
                ],
                [
                    'type' => 'rowaction',
                    'event' => 'button:historical_summary_button:click',
                    'name' => 'historical_summary_button',
                    'label' => 'LBL_HISTORICAL_SUMMARY',
                    'acl_action' => 'view',
                ],
                [
                    'type' => 'rowaction',
                    'event' => 'button:audit_button:click',
                    'name' => 'audit_button',
                    'label' => 'LNK_VIEW_CHANGE_LOG',
                    'acl_action' => 'view',
                ],
                [
                    'type' => 'divider',
                ],
                [
                    'type' => 'rowaction',
                    'event' => 'button:delete_button:click',
                    'name' => 'delete_button',
                    'label' => 'LBL_DELETE_BUTTON_LABEL',
                    'acl_action' => 'delete',
                ],
            ],
        ],
        [
            'name' => 'sidebar_toggle',
            'type' => 'sidebartoggle',
        ],
    ],
    'panels' => [
        [
            'name' => 'panel_header',
            'header' => true,
            'fields' => [
                [
                    'name' => 'picture',
                    'type' => 'hint-contacts-photo',
                    'size' => 'large',
                    'dismiss_label' => true,
                    'white_list' => true,
                    'related_fields' => ['hint_contact_pic'],
                ],
                [
                    'name' => 'name',
                    'label' => 'LBL_NAME',
                    'dismiss_label' => true,
                    'type' => 'fullname',
                    'fields' => ['salutation', 'first_name', 'last_name'],
                ],
                [
                    'name' => 'favorite',
                    'label' => 'LBL_FAVORITE',
                    'type' => 'favorite',
                    'dismiss_label' => true,
                ],
                [
                    'name' => 'follow',
                    'label' => 'LBL_FOLLOW',
                    'type' => 'follow',
                    'readonly' => true,
                    'dismiss_label' => true,
                ],
            ],
        ],
        [
            'name' => 'panel_body',
            'label' => 'LBL_RECORD_BODY',
            'columns' => 2,
            'placeholders' => true,
            'fields' => [
                'title',
                'phone_mobile',
                'department',
                'do_not_call',
                'account_name',
                'business_center_name',
                'market_score',
                [
                    'name' => 'email',
                    'licenseDependency' => [
                        'HINT' => [
                            'type' => 'hint-email',
                        ],
                    ],
                ],
                [
                    'name' => 'tag',
                    'span' => 12,
                ],
            ],
        ],
        [
            'columns' => 2,
            'name' => 'panel_hidden',
            'label' => 'LBL_RECORD_SHOWMORE',
            'hide' => true,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'primary_address',
                    'type' => 'fieldset',
                    'css_class' => 'address',
                    'label' => 'LBL_PRIMARY_ADDRESS',
                    'fields' => [
                        [
                            'name' => 'primary_address_street',
                            'css_class' => 'address_street',
                            'placeholder' => 'LBL_PRIMARY_ADDRESS_STREET',
                        ],
                        [
                            'name' => 'primary_address_city',
                            'css_class' => 'address_city',
                            'placeholder' => 'LBL_PRIMARY_ADDRESS_CITY',
                        ],
                        [
                            'name' => 'primary_address_state',
                            'css_class' => 'address_state',
                            'placeholder' => 'LBL_PRIMARY_ADDRESS_STATE',
                        ],
                        [
                            'name' => 'primary_address_postalcode',
                            'css_class' => 'address_zip',
                            'placeholder' => 'LBL_PRIMARY_ADDRESS_POSTALCODE',
                        ],
                        [
                            'name' => 'primary_address_country',
                            'css_class' => 'address_country',
                            'placeholder' => 'LBL_PRIMARY_ADDRESS_COUNTRY',
                        ],
                    ],
                ],
                [
                    'name' => 'alt_address',
                    'type' => 'fieldset',
                    'css_class' => 'address',
                    'label' => 'LBL_ALT_ADDRESS',
                    'fields' => [
                        [
                            'name' => 'alt_address_street',
                            'css_class' => 'address_street',
                            'placeholder' => 'LBL_ALT_ADDRESS_STREET',
                        ],
                        [
                            'name' => 'alt_address_city',
                            'css_class' => 'address_city',
                            'placeholder' => 'LBL_ALT_ADDRESS_CITY',
                        ],
                        [
                            'name' => 'alt_address_state',
                            'css_class' => 'address_state',
                            'placeholder' => 'LBL_ALT_ADDRESS_STATE',
                        ],
                        [
                            'name' => 'alt_address_postalcode',
                            'css_class' => 'address_zip',
                            'placeholder' => 'LBL_ALT_ADDRESS_POSTALCODE',
                        ],
                        [
                            'name' => 'alt_address_country',
                            'css_class' => 'address_country',
                            'placeholder' => 'LBL_ALT_ADDRESS_COUNTRY',
                        ],
                        [
                            'name' => 'copy',
                            'label' => 'NTC_COPY_PRIMARY_ADDRESS',
                            'type' => 'copy',
                            'mapping' => [
                                'primary_address_street' => 'alt_address_street',
                                'primary_address_city' => 'alt_address_city',
                                'primary_address_state' => 'alt_address_state',
                                'primary_address_postalcode' => 'alt_address_postalcode',
                                'primary_address_country' => 'alt_address_country',
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'geocode_status',
                    'licenseFilter' => ['MAPS'],
                ],
                'phone_fax',
                'phone_work',
                'twitter',
                [
                    'name' => 'dnb_principal_id',
                    'readonly' => true,
                ],
                [
                    'name' => 'description',
                    'span' => 12,
                ],
                'report_to_name',
                'sync_contact',
                'lead_source',
                'assigned_user_name',
                [
                    'name' => 'campaign_name',
                    'span' => 12,
                ],
                [
                    'name' => 'preferred_language',
                    'type' => 'language',
                ],
                'portal_user_company_name',
                'portal_name',
                'portal_active',
                [
                    'name' => 'portal_password',
                    'type' => 'change-password',
                ],
                [
                    'name' => 'date_modified_by',
                    'readonly' => true,
                    'inline' => true,
                    'type' => 'fieldset',
                    'label' => 'LBL_DATE_MODIFIED',
                    'fields' => [
                        [
                            'name' => 'date_modified',
                        ],
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_BY',
                        ],
                        [
                            'name' => 'modified_by_name',
                        ],
                    ],
                ],
                'team_name',
                [
                    'name' => 'date_entered_by',
                    'readonly' => true,
                    'inline' => true,
                    'type' => 'fieldset',
                    'label' => 'LBL_DATE_ENTERED',
                    'fields' => [
                        [
                            'name' => 'date_entered',
                        ],
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_BY',
                        ],
                        [
                            'name' => 'created_by_name',
                        ],
                    ],
                ],
            ],
        ],
        [
            'name' => 'panel_hint',
            'label' => 'LBL_HINT_PANEL',
            'columns' => 2,
            'labels' => true,
            'labelsOnTop' => true,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'hint_account_website',
                    'label' => 'LBL_HINT_COMPANY_WEBSITE',
                    'type' => 'stage2_url',
                    'white_list' => true,
                    'fields' => ['hint_photo', 'hint_account_logo'],
                ],
                'hint_education',
                [
                    'name' => 'hint_education_2',
                    'parent_key' => 'hint_education',
                ],
                'hint_job_2',
                'hint_account_size',
                'hint_account_industry',
                'hint_account_location',
                [
                    'name' => 'hint_account_description',
                    'account_key' => 'description',
                ],
                'hint_account_founded_year',
                [
                    'name' => 'hint_industry_tags',
                    'account_key' => 'hint_account_industry_tags',
                ],
                'hint_account_naics_code_lbl',
                [
                    'name' => 'hint_account_sic_code_label',
                    'account_key' => 'sic_code',
                ],
                'hint_account_fiscal_year_end',
                [
                    'name' => 'hint_account_annual_revenue',
                    'account_key' => 'annual_revenue',
                ],
                [
                    'name' => 'hint_facebook',
                    'type' => 'stage2_url',
                ],
                [
                    'name' => 'hint_twitter',
                    'type' => 'stage2_url',
                ],
                [
                    'name' => 'hint_account_facebook_handle',
                    'type' => 'stage2_url',
                ],
                [
                    'name' => 'hint_account_twitter_handle',
                    'type' => 'stage2_url',
                    'account_key' => 'twitter',
                ],
                [
                    'name' => 'phone_other',
                    'type' => 'phone',
                ],
            ],
            'licenseFilter' => ['HINT'],
        ],
    ],
];
