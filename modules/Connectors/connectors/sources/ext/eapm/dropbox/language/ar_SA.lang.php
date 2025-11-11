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

$url = 'https://www.sugarcrm.com/crm/product_doc.php?edition=' . $GLOBALS['sugar_flavor'] . '&الإصدار=' .
    $GLOBALS['sugar_version'] . '&اللغة=' . $GLOBALS['current_language'] . '&الوحدة=Connectors&route=Google&products=' .
    $productCodes;

$connector_strings = [
    'LBL_LICENSING_INFO' => '<table border="0" cellspacing="1"><tr><td valign="top" width="35%" class="dataLabel">' .
        'سَجِل مثيل Sugar مع Dropbox لتمكين تكوين حسابات Dropbox للاستخدام داخل ' .
        'Sugar. </td></tr></table>',
    'client_id' => 'معرف العميل',
    'client_secret' => 'سر العميل',
];
