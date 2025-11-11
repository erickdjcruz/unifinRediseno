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
    '&products=' . $productCodes;

$connector_strings = [
    'LBL_LICENSING_INFO' => '<table border="0" cellspacing="1"><tr><td valign="top" width="35%" class="dataLabel">' .
        'Registrirajte svoju instancu Sugar putem usluge Microsoft Azure kako biste omogućili konfiguraciju Microsoftovih računa za uporabu u okviru Sugara. ' .
        'Pogledajte <a href="https://www.sugarcrm.com/crm/product_doc.php?edition={$flavor}&version={$version}&lang={$lang}&module=Connectors&route=Microsoft" target=\'_blank\'>dokumentaciju za poveznike</a>' .
        '\' target=\'_blank\'>Dokumentaciju za Poveznike</a> za više informacija.</td></tr></table>',
    'oauth2_client_id' => 'ID klijenta',
    'oauth2_client_secret' => 'Klijentski tajni ključ',
    'oauth2_single_tenant_enabled' => 'Poveži se s jednom prijavom stanara',
    'oauth2_single_tenant_id' => 'ID stanara',
];
