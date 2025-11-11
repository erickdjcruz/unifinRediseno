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
/**
 * This dependency sets visibility of body and body_html fields based on text_only
 * field
 */
$dependencies['EmailTemplates']['set_visibility_fields'] = [
    'hooks' => ['edit', 'view',],
    'trigger' => 'true',
    'triggerFields' => ['text_only'],
    'onload' => true,
    'actions' => [
        [
            'name' => 'SetVisibility',
            'params' => [
                'target' => 'body_set',
                'label' => 'body_set_label', //normally <field>_label
                'value' => 'not(equal($text_only, "1"))',
            ],
        ],
    ],
];
