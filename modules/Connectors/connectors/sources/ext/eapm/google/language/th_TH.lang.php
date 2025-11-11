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

$url = 'https://www.sugarcrm.com/crm/product_doc.php?edition=' . $GLOBALS['sugar_flavor'] . '&version=' .
    $GLOBALS['sugar_version'] . '&lang=' . $GLOBALS['current_language'] . '&module=Connectors&route=Google' .
    $productCodes;

$connector_strings = [
    'LBL_LICENSING_INFO' => '<table border="0" cellspacing="1"><tr><td valign="top" width="35%" class="dataLabel">' .
        'ลงทะเบียนอินสแตนซ์ Sugar ของคุณด้วย Google เพื่อเปิดใช้งานการกำหนดค่าบัญชี Google สำหรับใช้ภายใน Sugar ' .
        'โปรดศึกษา <a href="https://www.sugarcrm.com/crm/product_doc.php?edition={$flavor}&version={$version}&lang={$lang}&module=Connectors&route=Google" target=\'_blank\'>' .
        '\' target=\'_blank\'>เอกสารเกี่ยวกับเครื่องมือการเชื่อมต่อ</a> เพื่อรับข้อมูลเพิ่มเติม </td></tr></table>',
    'oauth2_client_id' => 'ID ไคลเอนต์',
    'oauth2_client_secret' => 'ข้อมูลลับของไคลเอนต์',
];
