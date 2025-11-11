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

$viewdefs['base']['view']['dri-workflows-widget-configuration'] = [
    'template' => 'record',
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_HEADER',
            'header' => true,
            'fields' => [
                [
                    'name' => 'title',
                    'type' => 'label',
                    'default_value' => 'LBL_WIDGET_LAYOUT_CONFIGURATION_HEADER',
                    'css_class' => 'widget-configuration-class-title',
                ],
            ],
        ],
        [
            'name' => 'panel_body',
            'label' => 'LBL_RECORD_BODY',
            'columns' => 2,
            'labelsOnTop' => true,
            'placeholders' => true,
            'newTab' => false,
            'panelDefault' => 'expanded',
            'fields' => [
                [
                    'name' => 'cj_active_or_archive_filter',
                    'type' => 'cj-widget-config-toggle',
                    'label_left' => 'LBL_CUSTOMER_JOURNEY_WIDGET_ACTIVE',
                    'label_right' => 'LBL_CUSTOMER_JOURNEY_WIDGET_ARCHIVE',
                    'stateValueMapping' => [
                        'active' => true,
                        'archived' => false,
                    ],
                    'keyName' => 'toggleActiveArchived',
                    'defaultStateValue' => 'active',
                    'css_class' => 'archiveActive',
                ],
                [
                    'name' => 'cj_presentation_mode',
                    'type' => 'cj-widget-config-toggle',
                    'label_left' => 'LBL_CJ_PRESENTATION_MODE_VERTICAL',
                    'label_right' => 'LBL_CJ_PRESENTATION_MODE_HORIZONTAL',
                    'stateValueMapping' => [
                        'V' => true,
                        'H' => false,
                    ],
                    'keyName' => 'togglestate',
                    'defaultStateValue' => 'V',
                    'css_class' => 'horizontal-vertical',
                ],
            ],
        ],
    ],
    'buttons' => [
        [
            'name' => 'widget_cancel_button',
            'type' => 'button',
            'label' => 'LBL_CANCEL_BUTTON_LABEL',
            'css_class' => 'btn-invisible btn-link',
            'events' => [
                'click' => 'button:widget_cancel_button:click',
            ],
        ],
        [
            'name' => 'widget_save_button',
            'type' => 'button',
            'label' => 'LBL_SAVE_BUTTON_LABEL',
            'primary' => true,
            'events' => [
                'click' => 'button:widget_save_button:click',
            ],
        ],
    ],
    'last_state' => [
        'id' => 'dri-workflows-widget-configuration',
        'defaults' => [
            'cj_presentation_mode' => 'V',
            'cj_active_or_archive_filter' => 'active',
        ],
    ],
];
