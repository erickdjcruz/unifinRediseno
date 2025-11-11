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
        'Administration' => 'Администрация',
        'Product' => 'Продукт',
        'User' => 'Потребител',
    ],
    $object_name . '_status_dom' => [
        'New' => 'Ново',
        'Assigned' => 'Зададено',
        'Closed' => 'Затворено',
        'Pending Input' => 'Предстоящо въвеждане',
        'Rejected' => 'Отхвърлено',
        'Duplicate' => 'Дублирано',
    ],
    $object_name . '_priority_dom' => [
        'P1' => 'Високо',
        'P2' => 'Средно',
        'P3' => 'Ниско',
    ],
    $object_name . '_resolution_dom' => [
        '' => '',
        'Accepted' => 'Прието',
        'Duplicate' => 'Дублирано',
        'Closed' => 'Затворено',
        'Out of Date' => 'С изтекъл срок',
        'Invalid' => 'Невалидно',
    ],
];
