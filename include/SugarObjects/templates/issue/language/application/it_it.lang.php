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
        'Administration' => 'Amministrazione',
        'Product' => 'Prodotto',
        'User' => 'Utente',
    ],
    $object_name . '_status_dom' => [
        'New' => 'Novità',
        'Assigned' => 'Assegnato',
        'Closed' => 'Chiuso',
        'Pending Input' => 'Input in sospeso',
        'Rejected' => 'Rifiutato',
        'Duplicate' => 'Duplica',
    ],
    $object_name . '_priority_dom' => [
        'P1' => 'Alto',
        'P2' => 'Medio',
        'P3' => 'Basso',
    ],
    $object_name . '_resolution_dom' => [
        '' => '',
        'Accepted' => 'Accettato',
        'Duplicate' => 'Duplica',
        'Closed' => 'Chiuso',
        'Out of Date' => 'Obsoleto',
        'Invalid' => 'Non valido',
    ],
];
