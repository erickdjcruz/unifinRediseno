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
        'Administration' => 'ניהול מערכת',
        'Product' => 'מוצר',
        'User' => 'משתמש',
    ],
    $object_name . '_status_dom' => [
        'New' => 'חדש',
        'Assigned' => 'הוקצה',
        'Closed' => 'נסגר',
        'Pending Input' => 'ממתין לקלט',
        'Rejected' => 'נדחה',
        'Duplicate' => 'כפילות',
    ],
    $object_name . '_priority_dom' => [
        'P1' => 'גבוהה',
        'P2' => 'בינונית',
        'P3' => 'נמוכה',
    ],
    $object_name . '_resolution_dom' => [
        '' => '',
        'Accepted' => 'התקבל',
        'Duplicate' => 'לשכפל',
        'Closed' => 'נסגר',
        'Out of Date' => 'לא עדכני',
        'Invalid' => 'לא תקין',
    ],
];
