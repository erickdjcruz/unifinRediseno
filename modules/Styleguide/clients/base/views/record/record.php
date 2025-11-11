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
$viewdefs['Styleguide']['base']['view']['record'] = [
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
                [
                    'name' => 'title',
                    'label' => 'Base',
                    'type' => 'text',
                ],
                [
                    'name' => 'do_not_call',
                    'label' => 'Boolean',
                    'type' => 'bool',
                    'text' => 'Do not call',
                ],
                [
                    'name' => 'parent_name',
                    'label' => 'Parent',
                    'type' => 'parent',
                ],
                [
                    'name' => 'assigned_user_name',
                    'label' => 'Relate',
                    'type' => 'relate',
                    'id' => 'ASSIGNED_USER_ID',
                    'default' => true,
                    'sortable' => false,
                    'help' => 'This is the user that will be responsible for this record.',
                ],
                [
                    'name' => 'user_email',
                    'label' => 'Email',
                    'type' => 'email',
                ],
                [
                    'name' => 'team_name',
                    'label' => 'Teamset',
                    'type' => 'teamset',
                    'module' => 'Teams',
                    'help' => 'Teamset fields provide a way for records to be assigned to a group of users.',
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
                    'name' => 'birthdate',
                    'label' => 'Date',
                    'type' => 'date',
                ],
                [
                    'name' => 'date_start',
                    'label' => 'Datetimecombo',
                    'type' => 'datetimecombo',
                ],
                [
                    'name' => 'filename',
                    'label' => 'File',
                    'type' => 'file',
                ],
                [
                    'name' => 'list_price',
                    'label' => 'Currency',
                    'type' => 'currency',
                ],
                [
                    'name' => 'website',
                    'label' => 'URL',
                    'type' => 'url',
                ],
                [
                    'name' => 'phone_home',
                    'label' => 'Phone',
                    'type' => 'phone',
                ],
                [
                    'name' => 'description',
                    'label' => 'Textarea',
                    'type' => 'textarea',
                ],
                [
                    'name' => 'radio_button_group',
                    'type' => 'radioenum',
                    'label' => 'Radioenum',
                    'view' => 'edit',
                    'options' => [
                        'option_one' => 'Option One',
                        'option_two' => 'Option Two',
                    ],
                    'default' => false,
                    'enabled' => true,
                ],
                [
                    'name' => 'secret_password',
                    'label' => 'Password',
                    'type' => 'password',
                ],
                [
                    'name' => 'empty_text',
                    'label' => 'Label',
                    'type' => 'label',
                    'default_value' => 'Static text string.',
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
    ],
];
