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

use Sugarcrm\Sugarcrm\Entitlements\SubscriptionManager;

global $current_user;

$productCodes = $current_user->getProductCodes();
$productCodes = urlencode(implode(',', $productCodes));

$url = 'https://www.sugarcrm.com/crm/product_doc.php?edition=' . $GLOBALS['sugar_flavor'] . '&เวอร์ชัน=' .
    $GLOBALS['sugar_version'] . '&ภาษา=' . $GLOBALS['current_language'] . '&โมดูล=ตัวเชื่อมต่อ&เส้นทาง=Google&ผลิตภัณฑ์=' .
    $productCodes;

$connector_strings = [
    'LBL_LICENSING_INFO' => '<table border="0" cellspacing="1"><tr><td valign="top" width="35%" class="dataLabel">' .
        'ลงทะเบียนอินสแตนซ์ Sugar ของคุณด้วย Dropbox เพื่อเปิดใช้งานการกำหนดค่าบัญชี Dropbox สำหรับใช้ภายใน ' .
        'Sugar </td></tr></table>',
    'client_id' => 'ID ไคลเอนต์',
    'client_secret' => 'ข้อมูลลับของไคลเอนต์',
];
