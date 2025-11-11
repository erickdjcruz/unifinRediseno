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
    $GLOBALS['sugar_version'] . '&lang=' . $GLOBALS['current_language'] . '&module=Connectors&route=Microsoft' .
    '&produkty=' . $productCodes;

$connector_strings = [
    'LBL_LICENSING_INFO' => '<table border="0" cellspacing="1"><tr><td valign="top" width="35%" class="dataLabel">' .
        'Zaregistrujte si svoju inštanciu Sugar v Microsoft Azure, aby bolo možné povoliť konfiguráciu účtov Microsoft pre použitie v aplikácii Sugar. ' .
        'Prečítajte si <a href="https://www.sugarcrm.com/crm/product_doc.php?edition={$flavor}&version={$version}&lang={$lang}&module=Connectors&route=Microsoft" target=\'_blank\'>Dokumentácia konektorov</a>' .
        '\' target=\'_blank\'>Dokumentácia konektorov</a>, kde nájdete ďalšie informácie.</td></tr></table>',
    'oauth2_client_id' => 'ID klienta',
    'oauth2_client_secret' => 'Utajenie klienta',
    'oauth2_single_tenant_enabled' => 'Pripojiť k aplikácii s jedným nájomníkom',
    'oauth2_single_tenant_id' => 'ID nájomníka',
];
