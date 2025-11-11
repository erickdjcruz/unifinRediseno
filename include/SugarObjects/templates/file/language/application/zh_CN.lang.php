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
/*********************************************************************************
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

$app_list_strings = [
    strtolower($object_name) . '_category_dom' => [
        '' => '',
        'Marketing' => '营销',
        'Knowledge Base' => '知识库',
        'Sales' => '销售',
    ],

    strtolower($object_name) . '_subcategory_dom' => [
        '' => '',
        'Marketing Collateral' => '营销宣传资料',
        'Product Brochures' => '产品手册',
        'FAQ' => '常见问题',
    ],

    strtolower($object_name) . '_status_dom' => [
        'Active' => '活动的',
        'Draft' => '草稿',
        'FAQ' => '常见问题',
        'Expired' => '过期',
        'Under Review' => '审核中',
        'Pending' => '待定',
    ],
];
