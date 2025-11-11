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
$viewdefs['base']['view']['dri-customer-journey-momentum-dashlet'] = [
    'dashlets' => [
        [
            'label' => 'LBL_DEFAULT_DRI_CUSTOMER_JOURNEY_MOMENTUM_DASHLET_TITLE',
            'description' => 'LBL_DEFAULT_DRI_CUSTOMER_JOURNEY_MOMENTUM_DASHLET_DESC',
            'config' => [],
            'preview' => [],
            'filter' => [
                'module' => explode(',', $GLOBALS['sugar_config']['customer_journey']['enabled_modules']),
                'view' => 'record',
            ],
        ],
    ],
];
