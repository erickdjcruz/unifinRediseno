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
        'Administration' => 'Administration',
        'Product' => 'Produit',
        'User' => 'Utilisateur',
    ],
    $object_name . '_status_dom' => [
        'New' => 'Nouveau',
        'Assigned' => 'Assigné',
        'Closed' => 'Fermé',
        'Pending Input' => 'En attente de saisie',
        'Rejected' => 'Rejeté',
        'Duplicate' => 'Doublon',
    ],
    $object_name . '_priority_dom' => [
        'P1' => 'Haut',
        'P2' => 'Moyen',
        'P3' => 'Bas',
    ],
    $object_name . '_resolution_dom' => [
        '' => '',
        'Accepted' => 'Accepté',
        'Duplicate' => 'Doublon',
        'Closed' => 'Fermé',
        'Out of Date' => 'Obsolète',
        'Invalid' => 'Non valide',
    ],
];
