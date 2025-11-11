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
        'Administration' => '管理',
        'Product' => '产品',
        'User' => '用户',
    ],
    $object_name . '_status_dom' => [
        'New' => '新增功能',
        'Assigned' => '分配',
        'Closed' => '已关闭',
        'Pending Input' => '待输入',
        'Rejected' => '已拒绝',
        'Duplicate' => '重复',
    ],
    $object_name . '_priority_dom' => [
        'P1' => '高',
        'P2' => '中',
        'P3' => '低',
    ],
    $object_name . '_resolution_dom' => [
        '' => '',
        'Accepted' => '已接受',
        'Duplicate' => '重复',
        'Closed' => '已关闭',
        'Out of Date' => '已过时',
        'Invalid' => '无效',
    ],
];
