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

class AuthApi extends SugarApi
{
    public const CONNECTOR_LABELS = [
        'GoogleEmail' => 'LBL_SMTPTYPE_GOOGLE_OAUTH2',
        'MicrosoftEmail' => 'LBL_SMTPTYPE_MICROSOFT',
    ];

    public function registerApiRest()
    {
        return [
            'getAuthInfo' => [
                'reqType' => 'GET',
                'path' => ['EAPM', 'auth'],
                'pathVars' => ['module', ''],
                'method' => 'getAuthInfo',
                'shortHelp' => 'Get auth info for an application',
                'longHelp' => 'include/api/help/module_get_help.html',
            ],
            'getFullAPIList' => [
                'reqType' => 'GET',
                'path' => ['EAPM', 'list'],
                'pathVars' => ['module', ''],
                'method' => 'getFullAPIList',
                'minVersion' => '11.23',
                'shortHelp' => 'Get full API list for the application',
                'longHelp' => 'include/api/help/eapm_list.html',
            ],
        ];
    }

    /**
     * Gets auth url for an application.
     *
     * @param ServiceBase $api The API class of the request
     * @param array $args The arguments array passed in from the API
     * @return array Auth URL
     * @throws SugarApiExceptionMissingParameter
     */
    public function getAuthInfo(ServiceBase $api, array $args): array
    {
        $this->requireArgs($args, ['application']);
        $authWarning = $this->getAuthWarning($args['application']);
        $data = ['auth_warning' => $authWarning];
        $extApi = $this->getExternalApi($args['application']);
        if ($extApi) {
            $client = $extApi->getClient();
            $data['auth_url'] = $client->createAuthUrl();
        }
        return $data;
    }

    /**
     * Gets warning message for oauth2 connector.
     *
     * @param string $application
     * @return string
     */
    public function getAuthWarning(string $application): string
    {
        global $current_user;

        $docUrl = 'https://www.sugarcrm.com/crm/product_doc.php?edition=' . $GLOBALS['sugar_flavor'] . '&version=' .
            $GLOBALS['sugar_version'] . '&lang=' . $GLOBALS['current_language'] . '&module=Email&route=Outgoing';
        $productCodes = $current_user->getProductCodes();
        $productCodes = urlencode(implode(',', $productCodes));
        $docUrl .= '&products=' . $productCodes;
        $docLink = '<a href="' . $docUrl . '" target="_blank" rel="nofollow noopener noreferrer">' . translate('LBL_EMAILS') . '</a>';
        $connectorName = translate(self::CONNECTOR_LABELS[$application] ?? '');
        return string_format(translate('LBL_EMAIL_AUTH_WARNING'), [$connectorName, $docLink]);
    }

    /**
     * Gets external api object for an application.
     *
     * @param string $application
     * @return ExternalAPIBase|bool
     */
    public function getExternalApi(string $application)
    {
        return ExternalAPIFactory::loadAPI($application, true);
    }

    /**
     * Load the full exteranl api list
     */
    public function getFullAPIList()
    {
        return ExternalAPIFactory::loadFullAPIList(false, true);
    }
}
