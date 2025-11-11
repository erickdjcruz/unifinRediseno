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

/**
 * XOAUTHEncoder is a class for generating XOAUTH2 authentication credentials.
 * SASL XOAUTH2 is an authentication format allowed by some SMTP/IMAP servers
 * that accommodates user authentication via Oauth2 access tokens.
 * It has the following format:
 *
 * base64("user=" {User} "^Aauth=Bearer " {Access Token} "^A^A")
 *
 * PHPMailer provides built-in support for authenticating with SMTP servers
 * using this format
 */
class XOAUTHEncoder
{
    /**
     * @var string $username stores the username for the account to connect to
     */
    private $username;

    /**
     * @var string $accessToken stores a valid access token that is authenticated
     * for the account associated with the given $username
     */
    private $accessToken;

    /**
     * @param string $username
     * @param string $accessToken
     */
    public function __construct(
        $username,
        $accessToken
    ) {

        $this->username = $username;
        $this->accessToken = $accessToken;
    }

    /**
     * Encodes the username and access token into the SASL XOAUTH2 format
     *
     * @return string
     */
    public function getOauth64()
    {
        return base64_encode('user=' . $this->username . "\001auth=Bearer " . $this->accessToken . "\001\001");
    }
}
