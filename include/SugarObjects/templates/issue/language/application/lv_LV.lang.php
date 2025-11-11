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
        'Administration' => 'Administrēšana',
        'Product' => 'Produkts',
        'User' => 'Lietotājs',
    ],
    $object_name . '_status_dom' => [
        'New' => 'Jauns',
        'Assigned' => 'Piešķirts',
        'Closed' => 'Slēgts',
        'Pending Input' => 'Gaida ievadi',
        'Rejected' => 'Noraidīts',
        'Duplicate' => 'Dublikāts',
    ],
    $object_name . '_priority_dom' => [
        'P1' => 'Augsts',
        'P2' => 'Vidējs',
        'P3' => 'Zems',
    ],
    $object_name . '_resolution_dom' => [
        '' => '',
        'Accepted' => 'Pieņemts',
        'Duplicate' => 'Dublikāts',
        'Closed' => 'Slēgts',
        'Out of Date' => 'Novecojis',
        'Invalid' => 'Nederīgs',
    ],
];
