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
$object_name = strtolower($object_name);
$app_list_strings = [

    $object_name . '_type_dom' => [
        'Administration' => 'Administratorius',
        'Product' => 'Produktas',
        'User' => 'Vartotojas',
    ],
    $object_name . '_status_dom' => [
        'New' => 'Naujas',
        'Assigned' => 'Priskirtas',
        'Closed' => 'Uždaryta',
        'Pending Input' => 'Laukianti įvestis',
        'Rejected' => 'Atmesta',
        'Duplicate' => 'Dublikatas',
    ],
    $object_name . '_priority_dom' => [
        'P1' => 'Didelis',
        'P2' => 'Vidutinis',
        'P3' => 'Žemas',
    ],
    $object_name . '_resolution_dom' => [
        '' => '',
        'Accepted' => 'Patvirtinta',
        'Duplicate' => 'Dublikatas',
        'Closed' => 'Uždaryta',
        'Out of Date' => 'Pasenęs',
        'Invalid' => 'Netinkamas',
    ],
];
