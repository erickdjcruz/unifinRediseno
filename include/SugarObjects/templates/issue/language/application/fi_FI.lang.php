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
        'Administration' => 'Hallinta',
        'Product' => 'Tuote',
        'User' => 'Käyttäjä',
    ],
    $object_name . '_status_dom' => [
        'New' => 'Uusi',
        'Assigned' => 'Määritetty',
        'Closed' => 'Suljettu',
        'Pending Input' => 'Odottaa lisätietoja',
        'Rejected' => 'Hylätty',
        'Duplicate' => 'Kopio',
    ],
    $object_name . '_priority_dom' => [
        'P1' => 'Suuri',
        'P2' => 'Keskisuuri',
        'P3' => 'Matala',
    ],
    $object_name . '_resolution_dom' => [
        '' => '',
        'Accepted' => 'Hyväksytty',
        'Duplicate' => 'Kopio',
        'Closed' => 'Suljettu',
        'Out of Date' => 'Vanhentunut',
        'Invalid' => 'Virheellinen',
    ],
];
