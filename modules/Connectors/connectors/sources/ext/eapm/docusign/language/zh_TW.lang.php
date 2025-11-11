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

$flavor = $GLOBALS['sugar_flavor'] ?? '';
$version = $GLOBALS['sugar_version'] ?? '';
$language = $GLOBALS['current_language'] ?? '';
$action = $GLOBALS['action'] ?? '';
$status = getVersionStatus($version);
$key = $GLOBALS['key'] ?? '';
$module = 'DocuSignAdmin';

$url = "https://www.sugarcrm.com/crm/product_doc.php?edition={$flavor}&version={$version}&lang={$language}&" .
    "products={$productCodes}&help_action={$action}&status={$status}&key={$key}&module={$module}";

$connector_strings = [
    'LBL_LICENSING_INFO' => "使用 DocuSign 連接器的步驟：
        <br> - 生成集成密鑰
        <br> - 為信封啟用 DocuSign 連接
        （即 DocuSign 用於訂閱 Sugar 入口點的網絡鉤子）
        <br> - 在 DocuSign 中設置新的應用程序，確保插入重定向 uri 並生成密鑰。
       重定向 uri 必須為 https://SUGAR_URL/oauth-handler/DocuSignOauth2Redirect
        <br> 如果 Sugar 實例受到 IP 限制，則白名單 DocuSign 的 IP 地址",
    'environment' => '環境',
    'integration_key' => '集成密鑰',
    'client_secret' => '客戶端金鑰',
];
