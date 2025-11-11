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

use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Config;
use Sugarcrm\IdentityProvider\Srn;

$idpConfig = new Config(\SugarConfig::getInstance());
$isIDMModeEnabled = $idpConfig->isIDMModeEnabled();

if ($isIDMModeEnabled) {
    global $current_user;

    $config = $idpConfig->getIDMModeConfig();
    $tenantSrn = Srn\Converter::fromString($config['tid']);
    $srnManager = new Srn\Manager([
        'partition' => $tenantSrn->getPartition(),
        'region' => $tenantSrn->getRegion(),
    ]);
    $userSrn = Srn\Converter::toString($srnManager->createUserSrn($tenantSrn->getTenantId(), $current_user->id));
    $idmUrl = $idpConfig->buildCloudConsoleUrl('userCreate') . "&user_hint=$userSrn";

    $createButton = [
        'name' => 'create_button',
        'type' => 'button',
        'label' => 'LBL_CREATE_BUTTON_LABEL',
        'css_class' => 'btn-primary',
        'acl_action' => 'create',
        'openWindow' => true,
        'externalRoute' => true,
        'alertOnClick' => [
            'id' => 'show_user_idm_alert',
            'level' => 'info',
            'messages' => string_format(translate('ERR_CREATE_USER_FOR_IDM_MODE', 'Users'), [$idmUrl]),
        ],
        'route' => $idmUrl,
    ];
} else {
    $createButton = [
        'name' => 'create_button',
        'type' => 'button',
        'label' => 'LBL_CREATE_BUTTON_LABEL',
        'css_class' => 'btn-primary',
        'acl_action' => 'create',
        'route' => [
            'action' => 'create',
        ],
    ];
}

$viewdefs['Users']['base']['view']['list-headerpane'] = [
    'fields' => [
        [
            'name' => 'title',
            'type' => 'label',
            'default_value' => 'LBL_MODULE_NAME',
        ],
        [
            'name' => 'collection-count',
            'type' => 'collection-count',
        ],
    ],
    'buttons' => [
        $createButton,
        [
            'name' => 'sidebar_toggle',
            'type' => 'sidebartoggle',
        ],
    ],
];
