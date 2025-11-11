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
        'Administration' => 'Administrácia',
        'Product' => 'Produkt',
        'User' => 'Používateľ',
    ],
    $object_name . '_status_dom' => [
        'New' => 'Nové',
        'Assigned' => 'Priradené',
        'Closed' => 'Uzatvorené',
        'Pending Input' => 'Čaká sa na vstup',
        'Rejected' => 'Zamietnuté',
        'Duplicate' => 'Duplikát',
    ],
    $object_name . '_priority_dom' => [
        'P1' => 'Vysoký',
        'P2' => 'Stredný',
        'P3' => 'Nízky',
    ],
    $object_name . '_resolution_dom' => [
        '' => '',
        'Accepted' => 'Prijaté',
        'Duplicate' => 'Duplikát',
        'Closed' => 'Uzatvorené',
        'Out of Date' => 'Zastarané',
        'Invalid' => 'Neplatné',
    ],
];
