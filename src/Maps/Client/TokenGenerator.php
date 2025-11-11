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

namespace Sugarcrm\Sugarcrm\Maps\Client;

use LoggerManager;
use Sugarcrm\Sugarcrm\Maps\Logger;
use SugarOAuth2Server;

class TokenGenerator
{
    /**
     * The client id for the sudo token
     *
     * @var string
     */
    public const CLIENT_ID = 'sugar';

    /**
     * The platform for the sudo token
     *
     * @var string
     */
    public const PLATFORM = 'gcs';

    /**
     * Build and return a token
     *
     * @return string|void
     */
    public function createToken()
    {
        global $current_user;

        try {
            // we need the current session_id to revert back to
            $sessionId = session_id();

            // Auth uses the REMOTE_ADDR later in the toke generate process.
            // If it is not set, we will assign it to '' to prevent an error.
            if (!isset($_SERVER['REMOTE_ADDR'])) {
                $_SERVER['REMOTE_ADDR'] = '';
            }

            /**
             * Get a sudo token to be used on the DocumentMerge server
             * This changes the current session
             */
            $outhServer = SugarOAuth2Server::getOAuth2Server();
            $sudoToken = $outhServer->getSudoToken($current_user->user_name, self::CLIENT_ID, self::PLATFORM);

            // close the current sudo token
            session_write_close();

            // revert to the old session
            session_id($sessionId);
            session_start();

            if (is_array($sudoToken)) {
                return $sudoToken;
            }

            $logger = new Logger(LoggerManager::getLogger());
            $logger->alert("Unable to create a Sugar token for Maps process, sessionID: {$sessionId}");
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            $logger = new Logger(LoggerManager::getLogger());
            $logger->alert("Unable to create a Sugar token for Maps process, {$errorMessage}");
        }
    }
}
