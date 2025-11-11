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

global $sugar_config;

$systemConfig = Administration::getSettings(false, true);
$idpConfig = new Config(\SugarConfig::getInstance());
$isIDMModeEnabled = $idpConfig->isIDMModeEnabled();
// is LDAP Authentication enabled
$isLDAP = !empty($systemConfig->settings['system_ldap_enabled']);
// is SAML Authentication enabled
$isSAML = !empty($sugar_config['authenticationClass']);

$labelExternalAuthOnly = $isLDAP ? 'LBL_LDAP_ONLY' : ($isSAML ? 'LBL_SAML_ONLY' : false);

$externalAuthOnly = [
    'name' => 'external_auth_only',
    'label' => $labelExternalAuthOnly,
];

$viewdefs['Users']['base']['view']['record-extra-meta'] = [
    'panels' => [
        [
            'name' => 'advanced_tab',
            'label' => 'LBL_ADVANCED',
            'columns' => 2,
            'newTab' => true,
            'placeholders' => true,
            'panelDefault' => 'expanded',
            'fields' => [[], []],
        ],
        [
            'name' => 'advanced_tab_user_settings_panel',
            'label' => 'LBL_USER_SETTINGS',
            'css_class' => 'panel-border-none',
            'columns' => 2,
            'placeholders' => true,
            'panelDefault' => 'expanded',
            'fields' => [
                'receive_notifications',
                'export_delimiter',
                'send_email_on_mention',
                'default_export_charset',
                'use_real_names',
                'reminder_time',
                [
                    'name' => 'team_name',
                    'label' => 'LBL_DEFAULT_TEAM',
                ],
                'appearance',
                'email_reminder_time',
                [
                    'name' => 'site_url',
                    'span' => 12,
                    'add_links' => false,
                    'css_class' => 'break-words',
                ],
            ],
        ],
        [
            'name' => 'advanced_tab_layout_options_panel',
            'label' => 'LBL_LAYOUT_OPTIONS',
            'columns' => 2,
            'placeholders' => true,
            'panelDefault' => 'expanded',
            'fields' => [
                [
                    'name' => 'user_tabs',
                    'type' => 'available-modules',
                    'span' => 12,
                ],
                'number_pinned_modules',
                'field_name_placement',
            ],
        ],
        [
            'name' => 'advanced_tab_locale_settings_panel',
            'label' => 'LBL_USER_LOCALE',
            'columns' => 2,
            'placeholders' => true,
            'panelDefault' => 'expanded',
            'fields' => [
                'datef',
                'default_currency_significant_digits',
                'timef',
                'num_grp_sep',
                'timezone',
                'dec_sep',
                'currency',
                'default_locale_name_format',
                'currency_show_preferred',
                [],
                'currency_create_in_preferred',
            ],
        ],
        [
            'name' => 'advanced_tab_pdf_settings_panel',
            'label' => 'LBL_PDF_SETTINGS',
            'columns' => 2,
            'placeholders' => true,
            'panelDefault' => 'expanded',
            'fields' => [
                [
                    'name' => 'sugarpdf_pdf_font_name_main',
                    'span' => 12,
                ],
                [
                    'name' => 'sugarpdf_pdf_font_size_main',
                    'span' => 12,
                ],
                [
                    'name' => 'sugarpdf_pdf_font_name_data',
                    'span' => 12,
                ],
                [
                    'name' => 'sugarpdf_pdf_font_size_data',
                    'span' => 12,
                ],
            ],
        ],
        [
            'name' => 'advanced_tab_calendar_options_panel',
            'label' => 'LBL_CALENDAR_OPTIONS',
            'columns' => 2,
            'placeholders' => true,
            'panelDefault' => 'expanded',
            'fields' => [
                [
                    'name' => 'calendar_publish_key',
                    'span' => 12,
                    'css_class' => 'break-words',
                ],
                [
                    'name' => 'calendar_publish_location',
                    'span' => 12,
                    'add_links' => false,
                    'css_class' => 'break-words',
                ],
                [
                    'name' => 'calendar_search_location',
                    'span' => 12,
                    'add_links' => false,
                    'css_class' => 'break-words',
                ],
                [
                    'name' => 'calendar_ical_subscription_url',
                    'span' => 12,
                    'add_links' => false,
                    'css_class' => 'break-words',
                ],
                [
                    'name' => 'fdow',
                    'span' => 12,
                ],
            ],
        ],
        [
            'name' => 'access_tab',
            'label' => 'LBL_USER_ACCESS',
            'columns' => 2,
            'newTab' => true,
            'placeholders' => true,
            'panelDefault' => 'expanded',
            'invisibility' => 'create',
            'fields' => [[], []],
        ],
        [
            'name' => 'access_tab_user_role_panel',
            'label' => 'LBL_MY_ACCESS',
            'css_class' => 'panel-border-none',
            'columns' => 2,
            'placeholders' => true,
            'panelDefault' => 'expanded',
            'fields' => [
                [
                    'name' => 'user_role_access',
                    'type' => 'role_access',
                    'span' => 12,
                    'dismiss_label' => true,
                    'readonly' => true,
                ],
            ],
        ],
        [
            'name' => 'downloads_tab',
            'label' => 'LBL_DOWNLOADS',
            'columns' => 2,
            'newTab' => true,
            'placeholders' => true,
            'panelDefault' => 'expanded',
            'visibility' => 'self',
            'fields' => [[], []],
        ],
        [
            'name' => 'downloads_tab_panel',
            'label' => 'LBL_DOWNLOADS',
            'css_class' => 'panel-border-none',
            'columns' => 2,
            'placeholders' => true,
            'panelDefault' => 'expanded',
            'visibility' => 'self',
            'fields' => [
                [
                    'name' => 'downloads',
                    'type' => 'downloads',
                    'dismiss_label' => true,
                    'span' => 12,
                    'readonly' => true,
                ],
            ],
        ],
    ],
];

// adding the "Enable LDAP Authentication" or "Enable SAML Authentication" checkbox
// in a non-IDM mode
if (!$isIDMModeEnabled && ($isLDAP || $isSAML)) {
    $viewdefsIDM = &$viewdefs['Users']['base']['view']['record-extra-meta']['panels'][1]['fields'];
    array_splice($viewdefsIDM, 9, 0, [$externalAuthOnly]);
}
