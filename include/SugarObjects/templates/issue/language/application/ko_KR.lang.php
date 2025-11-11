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
        'Administration' => '관리',
        'Product' => '제품',
        'User' => '사용자',
    ],
    $object_name . '_status_dom' => [
        'New' => '신규',
        'Assigned' => '할당됨',
        'Closed' => '완료됨',
        'Pending Input' => '입력대기',
        'Rejected' => '거부됨',
        'Duplicate' => '복사',
    ],
    $object_name . '_priority_dom' => [
        'P1' => '높음',
        'P2' => '보통',
        'P3' => '낮음',
    ],
    $object_name . '_resolution_dom' => [
        '' => '',
        'Accepted' => '수락됨',
        'Duplicate' => '복사',
        'Closed' => '완료됨',
        'Out of Date' => '기간만료',
        'Invalid' => '무효',
    ],
];
