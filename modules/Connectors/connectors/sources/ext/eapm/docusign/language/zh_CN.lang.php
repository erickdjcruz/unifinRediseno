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
    'LBL_LICENSING_INFO' => "使用 DocuSign 连接器的步骤：
        <br> - 生成集成密钥
        <br> - 为信封启用 DocuSign 连接
        （即 DocuSign 用于订阅 Sugar 入口点的网络钩子）
        <br> - 在 DocuSign 中设置新的应用程序，确保插入重定向 uri 并生成密钥。
       重定向 uri 必须为 https://SUGAR_URL/oauth-handler/DocuSignOauth2Redirect
        <br> 如果 Sugar 实例受到 IP 限制，则白名单 DocuSign 的 IP 地址",
    'environment' => '环境',
    'integration_key' => '集成密钥',
    'client_secret' => '客户端密钥',
];
