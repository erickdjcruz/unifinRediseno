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
        'Administration' => 'Διαχείριση',
        'Product' => 'Προϊόν',
        'User' => 'Χρήστης',
    ],
    $object_name . '_status_dom' => [
        'New' => 'Νέο',
        'Assigned' => 'Ανατέθηκε',
        'Closed' => 'Κλειστό',
        'Pending Input' => 'Εισαγωγή Σε Εκκρεμότητα',
        'Rejected' => 'Απορρίφθηκε',
        'Duplicate' => 'Αντίγραφο',
    ],
    $object_name . '_priority_dom' => [
        'P1' => 'Υψηλό',
        'P2' => 'Μέσο',
        'P3' => 'Χαμηλό',
    ],
    $object_name . '_resolution_dom' => [
        '' => '',
        'Accepted' => 'Αποδεκτό',
        'Duplicate' => 'Αντίγραφο',
        'Closed' => 'Κλειστό',
        'Out of Date' => 'Άκυρο',
        'Invalid' => 'Άκυρο',
    ],
];
