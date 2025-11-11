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

$viewdefs['Schedulers']['DetailView'] = [
    'templateMeta' => [
        'maxColumns' => '2',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
        'includes' => [
            ['file' => 'modules/Schedulers/Schedulers.js'],
        ],
    ],

    'panels' => [
        'default' => [
            ['name', 'status'],
            ['date_time_start',
                [
                    'name' => 'time_from',
                    'customCode' => '{$fields.time_from.value|default:$MOD.LBL_ALWAYS}']],
            ['date_time_end',
                [
                    'name' => 'time_to',
                    'customCode' => '{$fields.time_to.value|default:$MOD.LBL_ALWAYS}']],
            [
                [
                    'name' => 'last_run',
                    'customCode' => '{$fields.last_run.value|default:$MOD.LBL_NEVER}'],
                [
                    'name' => 'job_interval',
                    'customCode' => '{$JOB_INTERVAL}'],
            ],
            ['catch_up', 'job'],
            [
                [
                    'name' => 'date_entered',
                    'customCode' => '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value|escape:"html":"UTF-8"}&nbsp;'],
                [
                    'name' => 'date_modified',
                    'customCode' => '{$fields.date_modified.value} {$APP.LBL_BY} {$fields.modified_by_name.value|escape:"html":"UTF-8"}&nbsp;'],
            ]],
    ],

];
