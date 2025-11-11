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

// When the module package is built with specific non-default configuration parameters, they
// appear here. Otherwise the module returns an empty array.
$config = [
    'US' => [
        'service-url' => 'https://hint-data-enrichment.service.sugarcrm.com',
        'hint-iss-service-url' => 'https://hint-interest-subscription.service.sugarcrm.com',
        'hint-notifications-service-url' => 'https://hint-notifications.service.sugarcrm.com',
    ],
    'EU' => [
        'service-url' => 'https://hint-data-enrichment-eu-central-1.service.sugarcrm.com',
        'hint-iss-service-url' => 'https://hint-interest-subscription-eu-central-1.service.sugarcrm.com',
        'hint-notifications-service-url' => 'https://hint-notifications-eu-central-1.service.sugarcrm.com',
    ],
    'APSE' => [
        'service-url' => 'https://hint-data-enrichment-ap-southeast-2.service.sugarcrm.com',
        'hint-iss-service-url' => 'https://hint-interest-subscription-ap-southeast-2.service.sugarcrm.com',
        'hint-notifications-service-url' => 'https://hint-notifications-ap-southeast-2.service.sugarcrm.com',
    ],
    'hint_version' => '5.4.2',
    'package_type' => 'prod',
];
return $config;
