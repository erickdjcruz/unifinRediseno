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
    $GLOBALS['sugar_version'] . '&version=' . $GLOBALS['current_language'] . '&module=Connectors&route=Google&products=' .
    $productCodes;

$connector_strings = [
    'LBL_LICENSING_INFO' => '<td valign="top" width="35%" class="dataLabel">' .
        'Registrieren Sie Ihre Sugar-Instanz bei Dropbox, um die Konfiguration von Dropbox-Konten für die Verwendung' .
        'in Sugar zu aktivieren. </table>',
    'client_id' => 'Client ID',
    'client_secret' => 'Client Secret',
];
