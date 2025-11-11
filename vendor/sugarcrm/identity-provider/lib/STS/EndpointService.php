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

namespace Sugarcrm\IdentityProvider\STS;

class EndpointService implements EndpointInterface
{
    /**
     * Allowed oAuth2 commands
     * @var array
     */
    protected $oAuth2Endpoints =
        [
            EndpointInterface::AUTH_ENDPOINT,
            EndpointInterface::TOKEN_ENDPOINT,
            EndpointInterface::INTROSPECT_ENDPOINT,
            EndpointInterface::REVOCATION_ENDPOINT,
        ];

    /**
     * Allowed keys endpoints
     * @var array
     */
    protected $oAuth2KeysEndpoints =
        [
            EndpointInterface::CONSENT_CHALLENGE_KEYS,
            EndpointInterface::HTTPS_TLS_KEYS,
            EndpointInterface::CONSENT_RESPONSE_KEYS,
            EndpointInterface::OPENID_KEYS,
        ];

    /**
     * Allowed keys type
     * @var array
     */
    protected $aAuth2KeysType =
        [
            EndpointInterface::PRIVATE_KEY,
            EndpointInterface::PUBLIC_KEY,
        ];

    /**
     * @var string
     */
    protected $stsHost;

    /**
     * EndpointsService constructor.
     * @param array $oAuth2Config
     */
    public function __construct(array $oAuth2Config)
    {
        if (!isset($oAuth2Config['host'])) {
            throw new \InvalidArgumentException('STS host must be set');
        }

        $this->stsHost = rtrim($oAuth2Config['host'], '/');
    }

    /**
     * @inheritdoc
     */
    public function getOAuth2Endpoint($endpoint)
    {
        if (!in_array($endpoint, $this->oAuth2Endpoints)) {
            throw new \InvalidArgumentException('Endpoint ' . $endpoint . ' is not allowed');
        }

        return $this->stsHost . '/oauth2/' . $endpoint;
    }

    /**
     * @inheritdoc
     */
    public function getKeysEndpoint($endpoint, $keyType = null)
    {
        if (!in_array($endpoint, $this->oAuth2KeysEndpoints)) {
            throw new \InvalidArgumentException('Endpoint ' . $endpoint . ' is not allowed');
        }

        if (is_null($keyType)) {
            return $this->stsHost . '/keys/' . $endpoint;
        }

        if (!in_array($keyType, $this->aAuth2KeysType)) {
            throw new \InvalidArgumentException('Key ' . $keyType . ' is not allowed');
        }

        return $this->stsHost . '/keys/' . $endpoint . '/' . $keyType;
    }

    /**
     * @inheritdoc
     */
    public function getWellKnownConfigurationEndpoint()
    {
        return $this->stsHost . '/.well-known/openid-configuration';
    }

    /**
     * return consent data url
     * @param $requestId
     * @return string
     */
    public function getConsentDataRequestEndpoint($requestId)
    {
        return sprintf(
            '%s/%s/%s?consent_challenge=%s',
            $this->stsHost,
            EndpointInterface::OAUTH2_ENDPOINT,
            EndpointInterface::CONSENT_ENDPOINT,
            $requestId
        );
    }

    /**
     * return consent accept url
     * @param $requestId
     * @return string
     */
    public function getConsentAcceptRequestEndpoint($requestId)
    {
        return sprintf(
            '%s/%s/%s/%s?consent_challenge=%s',
            $this->stsHost,
            EndpointInterface::OAUTH2_ENDPOINT,
            EndpointInterface::CONSENT_ENDPOINT,
            EndpointInterface::ACCEPT_ENDPOINT,
            $requestId
        );
    }

    /**
     * return consent reject url
     * @param $requestId
     * @return string
     */
    public function getConsentRejectRequestEndpoint($requestId)
    {
        return sprintf(
            '%s/%s/%s/%s?consent_challenge=%s',
            $this->stsHost,
            EndpointInterface::OAUTH2_ENDPOINT,
            EndpointInterface::CONSENT_ENDPOINT,
            EndpointInterface::REJECT_ENDPOINT,
            $requestId
        );
    }

    /**
     * return get user info endpoint
     * @return string
     */
    public function getUserInfoEndpoint()
    {
        return sprintf('%s/%s', $this->stsHost, EndpointInterface::USER_INFO_ENDPOINT);
    }

    /**
     * Get Login Request endpoint (Hydra specific)
     *
     * @param string $loginRequestId
     * @return string
     */
    public function getLoginRequestEndpoint(string $loginRequestId): string
    {
        return sprintf(
            '%s/%s/%s/%s?login_challenge=%s',
            $this->stsHost,
            EndpointInterface::OAUTH2_ENDPOINT,
            EndpointInterface::AUTH_ENDPOINT,
            EndpointInterface::LOGIN_REQUESTS_ENDPOINT,
            $loginRequestId
        );
    }

    /**
     * Get Login Request Accept endpoint (Hydra specific)
     *
     * @param string $loginRequestId
     * @return string
     */
    public function getLoginRequestAcceptEndpoint(string $loginRequestId): string
    {
        return sprintf(
            '%s/%s/%s/%s/%s?login_challenge=%s',
            $this->stsHost,
            EndpointInterface::OAUTH2_ENDPOINT,
            EndpointInterface::AUTH_ENDPOINT,
            EndpointInterface::LOGIN_REQUESTS_ENDPOINT,
            EndpointInterface::ACCEPT_ENDPOINT,
            $loginRequestId
        );
    }

    /**
     * Get Login Request Reject endpoint (Hydra specific)
     *
     * @param string $loginRequestId
     * @return string
     */
    public function getLoginRequestRejectEndpoint(string $loginRequestId): string
    {
        return sprintf(
            '%s/%s/%s/%s/%s?login_challenge=%s',
            $this->stsHost,
            EndpointInterface::OAUTH2_ENDPOINT,
            EndpointInterface::AUTH_ENDPOINT,
            EndpointInterface::LOGIN_REQUESTS_ENDPOINT,
            EndpointInterface::REJECT_ENDPOINT,
            $loginRequestId
        );
    }
}
