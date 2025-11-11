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
        'Administration' => 'Administración',
        'Product' => 'Producto',
        'User' => 'Usuario',
    ],
    $object_name . '_status_dom' => [
        'New' => 'Nuevo',
        'Assigned' => 'Asignado',
        'Closed' => 'Cerrado',
        'Pending Input' => 'Entrada pendiente',
        'Rejected' => 'Rechazado',
        'Duplicate' => 'Duplicado',
    ],
    $object_name . '_priority_dom' => [
        'P1' => 'Alto',
        'P2' => 'Medio',
        'P3' => 'Bajo',
    ],
    $object_name . '_resolution_dom' => [
        '' => '',
        'Accepted' => 'Aceptado',
        'Duplicate' => 'Duplicado',
        'Closed' => 'Cerrado',
        'Out of Date' => 'Caducado',
        'Invalid' => 'No válido',
    ],
];
