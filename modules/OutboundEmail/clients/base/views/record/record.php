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
$viewdefs['OutboundEmail']['base']['view']['record'] = [
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
                    'type' => 'rowaction',
                    'event' => 'button:duplicate_button:click',
                    'name' => 'duplicate_button',
                    'label' => 'LBL_DUPLICATE_BUTTON_LABEL',
                    'acl_module' => 'OutboundEmail',
                    'acl_action' => 'create',
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
                    'type' => 'avatar',
                    'size' => 'large',
                    'dismiss_label' => true,
                    'readonly' => true,
                ],
                [
                    'name' => 'name',
                    'related_fields' => [
                        'type',
                    ],
                ],
                [
                    'name' => 'favorite',
                    'label' => 'LBL_FAVORITE',
                    'type' => 'favorite',
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
                [
                    'name' => 'mail_smtptype',
                    'type' => 'email-provider',
                    'span' => 12,
                ],
                [
                    'name' => 'email_authorize',
                    'type' => 'email-authorize',
                    'label' => 'LBL_EMAIL_AUTHORIZE',
                    'span' => 12,
                ],
                [
                    'name' => 'auth_status',
                    'type' => 'auth-status',
                    'label' => 'LBL_STATUS',
                    'related_fields' => [
                        'eapm_id',
                    ],
                    'readonly' => true,
                ],
                'authorized_account',
                [
                    'name' => 'mail_smtpuser',
                    'required' => true,
                ],
                [
                    'name' => 'mail_smtppass',
                    'type' => 'change-password',
                    'required' => true,
                ],
                'mail_smtpserver',
                'mail_smtpport',
                'mail_smtpauth_req',
                'mail_smtpssl',
                [
                    'name' => 'email_address',
                    'type' => 'email-address',
                    'link' => false,
                ],
                'reply_to_name',
                [
                    'name' => 'reply_to_email_address',
                    'type' => 'email-address',
                    'link' => false,
                ],
            ],
        ],
        [
            'name' => 'panel_hidden',
            'label' => 'LBL_RECORD_SHOWMORE',
            'hide' => true,
            'columns' => 2,
            'placeholders' => true,
            'fields' => [
                'team_name',
                'preferred_sending_account',
            ],
        ],
    ],
    'dependencies' => [
        [
            'hooks' => ['edit'],
            'trigger' => 'true',
            'triggerFields' => ['mail_smtptype'],
            'onload' => false,
            'actions' => [
                [
                    'action' => 'SetValue',
                    'params' => [
                        'target' => 'mail_smtpserver',
                        'value' =>
                            'ifElse(or(equal($mail_smtptype,"google"), equal($mail_smtptype,"google_oauth2")), "smtp.gmail.com",
                                ifElse(equal($mail_smtptype,"exchange"), "",
                                    ifElse(equal($mail_smtptype,"exchange_online"), "smtp.office365.com",
                                        ifElse(equal($mail_smtptype,"outlook"), "smtp-mail.outlook.com",
                                            $mail_smtpserver))))',
                    ],
                ],
                [
                    'action' => 'SetValue',
                    'params' => [
                        'target' => 'mail_smtpport',
                        'value' =>
                            'ifElse(or(equal($mail_smtptype,"google"), equal($mail_smtptype,"google_oauth2")), "587",
                                ifElse(or(equal($mail_smtptype,"exchange"), equal($mail_smtptype,"exchange_online")), "587",
                                    ifElse(equal($mail_smtptype,"outlook"), "587",
                                        $mail_smtpport)))',
                    ],
                ],
                [
                    'action' => 'SetValue',
                    'params' => [
                        'target' => 'mail_smtpssl',
                        'value' =>
                            'ifElse(or(equal($mail_smtptype,"google"), equal($mail_smtptype,"google_oauth2")), "2",
                                ifElse(or(equal($mail_smtptype,"exchange"), equal($mail_smtptype,"exchange_online")), "2",
                                    ifElse(equal($mail_smtptype,"outlook"), "2",
                                        $mail_smtpssl)))',
                    ],
                ],
                [
                    'action' => 'SetValue',
                    'params' => [
                        'target' => 'mail_smtpauth_req',
                        'value' =>
                            'ifElse(or(equal($mail_smtptype,"google"), equal($mail_smtptype,"google_oauth2")), "1",
                                ifElse(or(equal($mail_smtptype,"exchange"), equal($mail_smtptype,"exchange_online")), "1",
                                    ifElse(equal($mail_smtptype,"outlook"), "1",
                                        $mail_smtpauth_req)))',
                    ],
                ],
            ],
        ],
        [
            'hooks' => ['edit'],
            'trigger' => 'true',
            'triggerFields' => ['mail_smtpssl'],
            'onload' => false,
            'actions' => [
                [
                    'action' => 'SetValue',
                    'params' => [
                        'target' => 'mail_smtpport',
                        'value' =>
                            'ifElse(equal($mail_smtpssl,"1"), "465",
                                ifElse(equal($mail_smtpssl,"2"), "587",
                                    "25"))',
                    ],
                ],
            ],
        ],
        [
            'hooks' => ['edit'],
            'trigger' => 'true',
            'triggerFields' => ['mail_smtpauth_req', 'mail_authtype'],
            'onload' => true,
            'actions' => [
                [
                    'action' => 'SetRequired',
                    'params' => [
                        'target' => 'mail_smtpuser',
                        'value' => 'and(equal($mail_smtpauth_req, "1"), not(equal($mail_authtype,"oauth2")))',
                    ],
                ],
                [
                    'action' => 'SetRequired',
                    'params' => [
                        'target' => 'mail_smtppass',
                        'value' => 'and(equal($mail_smtpauth_req, "1"), not(equal($mail_authtype,"oauth2")))',
                    ],
                ],
            ],
        ],
        [
            'hooks' => ['all'],
            'trigger' => 'true',
            'triggerFields' => ['mail_smtpauth_req', 'mail_authtype'],
            'onload' => true,
            'actions' => [
                [
                    'action' => 'SetVisibility',
                    'params' => [
                        'target' => 'mail_smtpuser',
                        'value' => 'and(equal($mail_smtpauth_req, "1"), not(equal($mail_authtype,"oauth2")))',
                    ],
                ],
                [
                    'action' => 'SetVisibility',
                    'params' => [
                        'target' => 'mail_smtppass',
                        'value' => 'and(equal($mail_smtpauth_req, "1"), not(equal($mail_authtype,"oauth2")))',
                    ],
                ],
            ],
        ],
        [
            'hooks' => ['all'],
            'trigger' => 'true',
            'triggerFields' => ['mail_authtype'],
            'onload' => true,
            'actions' => [
                [
                    'action' => 'SetVisibility',
                    'params' => [
                        'target' => 'auth_status',
                        'value' => 'equal($mail_authtype,"oauth2")',
                    ],
                ],
                [
                    'action' => 'SetVisibility',
                    'params' => [
                        'target' => 'authorized_account',
                        'value' => 'equal($mail_authtype,"oauth2")',
                    ],
                ],
                [
                    'action' => 'ReadOnly',
                    'params' => [
                        'target' => 'mail_smtpauth_req',
                        'value' => 'equal($mail_authtype,"oauth2")',
                    ],
                ],
                [
                    'action' => 'ReadOnly',
                    'params' => [
                        'target' => 'authorized_account',
                        'value' => 'equal($mail_authtype,"oauth2")',
                    ],
                ],
            ],
        ],
    ],
];
