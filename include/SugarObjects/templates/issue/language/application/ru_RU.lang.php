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
        'Administration' => 'Администрирование',
        'Product' => 'Продукт',
        'User' => 'Пользователь',
    ],
    $object_name . '_status_dom' => [
        'New' => 'Новый',
        'Assigned' => 'Назначенный',
        'Closed' => 'Закрытый',
        'Pending Input' => 'Ожидание ввода',
        'Rejected' => 'Отклоненный',
        'Duplicate' => 'Дубликат',
    ],
    $object_name . '_priority_dom' => [
        'P1' => 'Высокий',
        'P2' => 'Средний',
        'P3' => 'Низкий',
    ],
    $object_name . '_resolution_dom' => [
        '' => '',
        'Accepted' => 'Принят',
        'Duplicate' => 'Дубликат',
        'Closed' => 'Закрыт',
        'Out of Date' => 'Устарел',
        'Invalid' => 'Недействительный',
    ],
];
