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
$module = 'DRI_Workflow_Task_Templates';
$panel = [
    'view' => 'dri-license-errors',
];

if (isset($viewdefs[$module]['base']['layout']['extra-info'])) {
    $viewdefs[$module]['base']['layout']['extra-info']['components'][] = $panel;
} else {
    $viewdefs[$module]['base']['layout']['extra-info'] = [
        'components' => [
            $panel,
        ],
        'type' => 'simple',
        'span' => 12,
    ];
}
