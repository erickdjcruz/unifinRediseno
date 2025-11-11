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

use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Config;

$mainDropdownButtons = [
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
        'type' => 'rowaction',
        'event' => 'button:reset_preferences:click',
        'name' => 'reset_preferences',
        'label' => 'LBL_RESET_PREFERENCES',
        'acl_action' => 'edit',
    ],
    [
        'type' => 'divider',
    ],
    [
        'type' => 'rowaction',
        'event' => 'button:historical_summary_button:click',
        'name' => 'historical_summary_button',
        'label' => 'LBL_HISTORICAL_SUMMARY',
        'acl_action' => 'view',
    ],
];

$idpConfig = new Config(\SugarConfig::getInstance());
$isIDMModeEnabled = $idpConfig->isIDMModeEnabled();
if (!$isIDMModeEnabled) {
    array_splice(
        $mainDropdownButtons,
        8,
        0,
        [[
            'type' => 'rowaction',
            'event' => 'button:reset_password:click',
            'name' => 'reset_password',
            'label' => 'LBL_PASSWORD_USER_RESET',
            'acl_module' => 'Users',
            'acl_action' => 'admin',
        ]]
    );

    $mainDropdownButtons = array_merge($mainDropdownButtons, [
        [
            'type' => 'rowaction',
            'event' => 'button:duplicate_button:click',
            'name' => 'duplicate_button',
            'label' => 'LBL_DUPLICATE_BUTTON_LABEL',
            'acl_module' => 'Users',
            'acl_action' => 'create',
        ],
        [
            'type' => 'divider',
        ],
        [
            'type' => 'rowaction',
            'event' => 'button:delete_button:click',
            'name' => 'delete_button',
            'label' => 'LBL_DELETE_BUTTON_LABEL',
            'acl_module' => 'Users',
            'acl_action' => 'delete',
        ],
    ]);
}

$viewdefs['Users']['base']['view']['record'] = [
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
        ],
        [
            'type' => 'actiondropdown',
            'name' => 'main_dropdown',
            'primary' => true,
            'showOn' => 'view',
            'buttons' => $mainDropdownButtons,
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
                    'type' => 'avatar',
                    'size' => 'large',
                    'dismiss_label' => true,
                ],
                [
                    'name' => 'full_name',
                    'type' => 'fullname',
                    'fields' => ['first_name', 'last_name'],
                    'dismiss_label' => true,
                ],
            ],
        ],
        [
            'name' => 'panel_body',
            'label' => 'LBL_USER_INFORMATION',
            'columns' => 2,
            'newTab' => true,
            'placeholders' => true,
            'fields' => [
                'status',
                [
                    'name' => 'license_type',
                    'type' => 'enum',
                    'required' => true,
                ],
                [
                    'name' => 'is_admin',
                    'label' => 'LBL_USER_TYPE',
                    'type' => 'user-type',
                    'options' => 'user_type_bool_dom',
                    'optionInfo' => [
                        false => 'LBL_REGULAR_DESC',
                        true => 'LBL_ADMIN_DESC',
                    ],
                ],
                'email',
                'user_name',
                'email_link_type',
                'title',
                [
                    'name' => 'mail_credentials',
                    'type' => 'email-credentials',
                ],
                'department',
                [
                    'name' => 'address',
                    'type' => 'fieldset',
                    'css_class' => 'address',
                    'label' => 'LBL_ADDRESS',
                    'idm_mode_disabled' => true,
                    'fields' => [
                        [
                            'name' => 'address_street',
                            'css_class' => 'address_street',
                            'placeholder' => 'LBL_ADDRESS_STREET',
                        ],
                        [
                            'name' => 'address_city',
                            'css_class' => 'address_city',
                            'placeholder' => 'LBL_ADDRESS_CITY',
                        ],
                        [
                            'name' => 'address_state',
                            'css_class' => 'address_state',
                            'placeholder' => 'LBL_ADDRESS_STATE',
                        ],
                        [
                            'name' => 'address_postalcode',
                            'css_class' => 'address_zip',
                            'placeholder' => 'LBL_ADDRESS_POSTALCODE',
                        ],
                        [
                            'name' => 'address_country',
                            'css_class' => 'address_country',
                            'placeholder' => 'LBL_ADDRESS_COUNTRY',
                        ],
                    ],
                ],
                'phone_work',
                [],
            ],
        ],
        [
            'columns' => 2,
            'name' => 'panel_body_2',
            'label' => 'LBL_EMPLOYEE_INFORMATION',
            'placeholders' => true,
            'fields' => [
                'employee_status',
                'show_on_employees',
                'reports_to_name',
                'phone_mobile',
                'business_center_name',
                'phone_other',
                'description',
                'phone_fax',
                [],
                'phone_home',
            ],
        ],
    ],
];

if ($isIDMModeEnabled) {
    global $current_user;
    $viewdefs['Users']['base']['view']['record']['cloudConsoleEditUserLink'] = $idpConfig->buildCloudConsoleUrl('/', ['users', '{{record}}'], $current_user->id);
}
