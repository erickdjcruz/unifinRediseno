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

use Sugarcrm\Sugarcrm\Util\Uuid;

trait IdmModeAuthTrait
{
    /**
     * current platform
     * @var string
     */
    protected $platform = 'base';

    protected function ensureLoginStatus(User $user): void
    {
        $loginStatus = apiCheckLoginStatus();
        if (true !== $loginStatus && !$user->isAdmin()) {
            if ($loginStatus['level'] == 'maintenance') {
                SugarApplication::redirect('./#maintenance');
            } elseif ($loginStatus['message'] === 'ERROR_LICENSE_SEATS_MAXED') {
                SugarApplication::redirect('./#licenseSeats');
            }
        }
    }

    protected function setupDownloadToken($downloadToken, $expiresIn): void
    {
        // Adding the setcookie() here instead of calling $api->setHeader() because
        // manually adding a cookie header will break 3rd party apps that use cookies
        setcookie(
            RestService::DOWNLOAD_COOKIE . '_' . $this->platform,
            $downloadToken,
            [
                'expires' => time() + $expiresIn,
                'path' => ini_get('session.cookie_path'),
                'domain' => ini_get('session.cookie_domain'),
                'secure' => ini_get('session.cookie_secure'),
                'httponly' => ini_get('session.cookie_httponly'),
                'samesite' => ini_get('session.cookie_samesite'),
            ],
        );
    }

    // Setup new BWC session for user. Needed to replace old admin BWC session
    protected function setupBWCImpersonationSession(): void
    {
        $sessionName = session_name();
        $sessionId = Uuid::uuid4();
        $sessionData = $_SESSION;
        session_write_close();

        ini_set('session.use_cookies', false);
        session_id($sessionId);
        session_start();
        $_SESSION = $sessionData;
        session_write_close();

        setcookie(
            $sessionName,
            $sessionId,
            [
                'path' => ini_get('session.cookie_path'),
                'domain' => ini_get('session.cookie_domain'),
                'secure' => ini_get('session.cookie_secure'),
                'httponly' => ini_get('session.cookie_httponly'),
                'samesite' => ini_get('session.cookie_samesite'),
            ]
        );
    }
}
