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
 * This dependency sets the status field to read only if the purchase has been converted.
 */
$dependencies['Purchases']['read_only_fields'] = [
    'hooks' => ['edit'],
    'trigger' => 'true',
    'triggerFields' => ['product_template_name'],
    'onload' => true,
    'actions' => [
        [
            'name' => 'ReadOnly',
            'params' => [
                'target' => 'category_name',
                'label' => 'category_name_label',
                'value' => 'not(equal($product_template_name,""))',
            ],
        ],
        [
            'name' => 'ReadOnly',
            'params' => [
                'target' => 'type_name',
                'label' => 'type_name_label',
                'value' => 'not(equal($product_template_name,""))',
            ],
        ],
        [
            'name' => 'ReadOnly',
            'params' => [
                'target' => 'service',
                'label' => 'service_label',
                'value' => 'not(equal($product_template_name,""))',
            ],
        ],
    ],
];
