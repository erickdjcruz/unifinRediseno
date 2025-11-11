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
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Exception\IdmNonrecoverableException;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Exception\ServiceAccountAuthenticationException;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Exception\InvalidTokenException;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\AuthProviderOIDCManagerBuilder;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\AuthProviderApiLoginManagerBuilder;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\AuthProviderBasicManagerBuilder;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Token\OIDC\IntrospectToken;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Token\OIDC\JWTBearerToken;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Token\OIDC\RefreshToken;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Token\OIDC\RevokeToken;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Token\OIDC\CodeToken;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\User;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Sugarcrm\IdentityProvider\Srn;
use Sugarcrm\Sugarcrm\Util\Uuid;
use Sugarcrm\Sugarcrm\Logger\Factory as LoggerFactory;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Sugar OAuth2.0 server that connects Sugar and OpenID Connect server (e.g. STS authentication).
 * @api
 */
class SugarOAuth2ServerOIDC extends SugarOAuth2Server implements LoggerAwareInterface
{
    use LoggerAwareTrait;
    /**
     * @var string
     */
    protected $sudoFor = '';

    public const PORTAL_PLATFORM = 'portal';

    protected const IDM_NONRECOVERABLE_ERROR = 'idm_nonrecoverable_error';

    /**
     * @var string
     */
    protected $platform;

    /**
     * SugarOAuth2ServerOIDC constructor.
     *
     * @param IOAuth2Storage $storage
     * @param array $config
     */
    public function __construct(IOAuth2Storage $storage, array $config)
    {
        parent::__construct($storage, $config);
        $this->setLogger(LoggerFactory::getLogger('authentication'));
    }

    /**
     * Sets the platform
     *
     * @param string $platform
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;
        if ($platform && $platform !== 'base') {
            parent::setPlatform($platform);
        }
    }

    /**
     * @inheritdoc
     * @throws OAuth2ServerException
     * @throws \InvalidArgumentException
     */
    public function grantAccessToken(array $inputData = null, array $authHeaders = null): array
    {
        $oidcGrantType = [self::GRANT_TYPE_REFRESH_TOKEN, self::GRANT_TYPE_AUTH_CODE];
        $sourceToken = null;

        if (empty($inputData['grant_type'])) {
            throw new OAuth2ServerException(
                self::HTTP_BAD_REQUEST,
                self::ERROR_INVALID_REQUEST,
                'Invalid grant_type parameter or parameter missing'
            );
        }

        if (!in_array($inputData['grant_type'], $oidcGrantType, true)) {
            return parent::grantAccessToken($inputData, $authHeaders);
        }

        switch ($inputData['grant_type']) {
            case self::GRANT_TYPE_REFRESH_TOKEN:
                if (!$this->storage instanceof IOAuth2RefreshTokens) {
                    throw new OAuth2ServerException(self::HTTP_BAD_REQUEST, self::ERROR_UNSUPPORTED_GRANT_TYPE);
                }

                if (empty($inputData['refresh_token'])) {
                    throw new OAuth2ServerException(
                        self::HTTP_BAD_REQUEST,
                        self::ERROR_INVALID_REQUEST,
                        'No "refresh_token" parameter found'
                    );
                }

                if (Uuid::isValid($inputData['refresh_token'])) {
                    return parent::grantAccessToken($inputData, $authHeaders);
                }
                $sourceToken = new RefreshToken($inputData['refresh_token']);
                break;
            case self::GRANT_TYPE_AUTH_CODE:
                if (empty($inputData['code']) || empty($inputData['scope'])) {
                    throw new OAuth2ServerException(
                        self::HTTP_BAD_REQUEST,
                        self::ERROR_INVALID_REQUEST,
                        'Missing parameters'
                    );
                }

                if (Uuid::isValid($inputData['code'])) {
                    return parent::grantAccessToken($inputData, $authHeaders);
                }

                $sourceToken = new CodeToken($inputData['code'], $inputData['scope']);
                break;
        }

        if (!$sourceToken) {
            throw new OAuth2ServerException(self::HTTP_BAD_REQUEST, self::ERROR_INVALID_REQUEST);
        }

        try {
            $idpConfig = $this->getIdpConfig();
            $authManager = $this->getAuthProviderBasicBuilder($idpConfig)->buildAuthProviders();
            /** @var TokenInterface $resultToken */
            $resultToken = $authManager->authenticate($sourceToken);
            return [
                'access_token' => $resultToken->getAttribute('token'),
                'expires_in' => $resultToken->getAttribute('expires_in'),
                'token_type' => $resultToken->getAttribute('token_type'),
                'scope' => $resultToken->getAttribute('scope'),
                'refresh_token' => $resultToken->getAttribute('refresh_token'),
                'download_token' => $resultToken->getAttribute('token'),
                'refresh_expires_in' => time() + $this->getVariable(self::CONFIG_REFRESH_LIFETIME),
            ];
        } catch (AuthenticationException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            throw new OAuth2ServerException(self::HTTP_BAD_REQUEST, self::ERROR_INVALID_REQUEST, $e->getMessage());
        }
    }

    /**
     * Verifies openID connect token delegating it to OIDC server.
     * Loads PHP session bound to the token.
     *
     * @param string $token OIDC Access Token
     * @param string|null $scope
     *
     * @return array
     * @throws OAuth2AuthenticateException
     */
    public function verifyAccessToken($token, $scope = null)
    {
        /*
         * Parent token verification should be applied if access token is a uuid generated by SugarCRM
         * For example portal platform should not pass through new oauth2
         */
        if (Uuid::isValid($token)) {
            return parent::verifyAccessToken($token, $scope);
        }

        $userToken = null;
        try {
            $config = $this->getIdpConfig();
            $idmModeConfig = $this->getIdmModeConfig();
            $authManager = $this->getAuthProviderBuilder($config)->buildAuthProviders();
            $introspectToken = new IntrospectToken(
                $token,
                $idmModeConfig['tid'],
                $idmModeConfig['crmOAuthScope']
            );
            $introspectToken->setAttribute('platform', $this->platform);
            /** @var IntrospectToken $userToken */
            $userToken = $authManager->authenticate($introspectToken);
        } catch (AuthenticationException $e) {
            $message = $e->getMessage();
            if ($e instanceof IdmNonrecoverableException) {
                $error = static::IDM_NONRECOVERABLE_ERROR;
                $this->logger->warning('IDM mode introspect nonrecoverable exception: ' . $e->getMessage());
            } elseif ($e instanceof ServiceAccountAuthenticationException) {
                $error = self::ERROR_INVALID_GRANT;
                $this->logger->warning('IDM mode introspect exception: ' . $e->getMessage());
            } elseif ($e instanceof InvalidTokenException) {
                $error = self::ERROR_INVALID_GRANT;
                $this->logger->warning('Invalid token: ' . $e->getMessage());
            } else {
                $error = self::ERROR_INVALID_GRANT;
                $message = 'The access token provided has expired.';
                $this->logger->warning('IDM mode introspect exception: ' . $e->getMessage());
            }
            throw new OAuth2AuthenticateException(
                self::HTTP_UNAUTHORIZED,
                $this->getVariable(self::CONFIG_TOKEN_TYPE),
                $this->getVariable(self::CONFIG_WWW_REALM),
                $error,
                $message,
                $scope
            );
        }

        if (!$userToken->isAuthenticated()) {
            return [];
        }

        /** @var User $user */
        $user = $userToken->getUser();

        $tokenInfo = [
            'client_id' => $userToken->getAttribute('client_id'),
            'user_id' => $user->getSugarUser()->id,
            'expires' => $userToken->getAttribute('exp'),
        ];

        if ($user->isServiceAccount()) {
            $tokenInfo['serviceAccount'] = $user;
        }

        return $tokenInfo;
    }

    /**
     * get IDM oonfig object
     * @return Config
     */
    protected function getIdpConfig()
    {
        return new Config(\SugarConfig::getInstance());
    }

    /**
     * get Idm mode Config
     * @return array
     */
    protected function getIdmModeConfig()
    {
        return $this->getIdpConfig()->getIDMModeConfig();
    }

    /**
     * @inheritdoc
     */
    protected function createAccessToken($client_id, $user_id, $scope = null)
    {
        if ($this->storage->hasPortalStore($client_id)) {
            return parent::createAccessToken($client_id, $user_id, $scope);
        }

        $idmModeConfig = $this->getIdmModeConfig();

        $tenantSrn = Srn\Converter::fromString($idmModeConfig['tid']);
        $srnManagerConfig = [
            'partition' => $tenantSrn->getPartition(),
            'region' => $tenantSrn->getRegion(),
        ];
        $srnManager = new Srn\Manager($srnManagerConfig);
        $userSrn = $srnManager->createUserSrn($tenantSrn->getTenantId(), $user_id);

        $idpConfig = $this->getIdpConfig();
        try {
            $authManager = $this->getAuthProviderApiLoginBuilder($idpConfig)->buildAuthProviders();
            $jwtBearerToken = new JWTBearerToken(Srn\Converter::toString($userSrn), $idmModeConfig['tid']);
            if (!empty($this->sudoFor)) {
                $jwtBearerToken->setAttribute(
                    'sudoer',
                    Srn\Converter::toString($srnManager->createUserSrn($tenantSrn->getTenantId(), $this->sudoFor))
                );
            }
            $jwtBearerToken->setAttribute('platform', $this->platform);
            $accessToken = $authManager->authenticate($jwtBearerToken);

            $token = [
                'access_token' => $accessToken->getAttribute('token'),
                'expires_in' => $accessToken->getAttribute('expires_in'),
                'token_type' => $accessToken->getAttribute('token_type'),
                'scope' => $accessToken->getAttribute('scope'),
            ];

            if ($this->storage instanceof IOAuth2RefreshTokens && $accessToken->hasAttribute('refresh_token')) {
                $token['refresh_token'] = $accessToken->getAttribute('refresh_token');
                $token['refresh_expires_in'] = $this->getVariable(self::CONFIG_REFRESH_LIFETIME);
                if (is_null($this->storage->refreshToken)) {
                    $this->storage->refreshToken = BeanFactory::newBean('OAuthTokens');
                }
                $this->storage->refreshToken->expire_ts = time() + $token['refresh_expires_in'];
                $this->storage->refreshToken->download_token = $token['access_token'];
            }

            return $token;
        } catch (AuthenticationException $e) {
            throw new \SugarApiExceptionNeedLogin($e->getMessage());
        }
    }

    /**
     * @param Config $config
     * @return AuthProviderApiLoginManagerBuilder
     */
    protected function getAuthProviderApiLoginBuilder(Config $config): AuthProviderApiLoginManagerBuilder
    {
        return new AuthProviderApiLoginManagerBuilder($config);
    }

    /**
     * @param Config $config
     * @return AuthProviderOIDCManagerBuilder
     */
    protected function getAuthProviderBuilder(Config $config)
    {
        return new AuthProviderOIDCManagerBuilder($config);
    }

    /**
     * @param Config $config
     *
     * @return AuthProviderBasicManagerBuilder
     */
    protected function getAuthProviderBasicBuilder(Config $config)
    {
        return new AuthProviderBasicManagerBuilder($config);
    }

    /**
     * Set old refresh token
     * This is required for unit tests
     * @param $refreshToken
     * @return SugarOAuth2ServerOIDC
     */
    public function setOldRefreshToken($refreshToken)
    {
        $this->oldRefreshToken = $refreshToken;
        return $this;
    }

    /**
     * @param string $userName
     * @param string $clientId
     * @param string $platform
     * @param bool $allowInactive
     * @param bool $needRefresh
     * @return string
     * @throws SugarApiExceptionNotFound
     */
    public function getSudoToken(
        $userName,
        $clientId,
        $platform,
        bool $allowInactive = false,
        bool $needRefresh = false
    ) {

        if ($allowInactive) {
            $this->sudoFor = $GLOBALS['current_user']->id;
        }
        return parent::getSudoToken($userName, $clientId, $platform, $allowInactive, $needRefresh);
    }

    protected function revokeToken(string $token)
    {
        $idpConfig = $this->getIdpConfig();
        try {
            $authManager = $this->getAuthProviderBasicBuilder($idpConfig)->buildAuthProviders();
            $revokeToken = new RevokeToken($token);
            $authManager->authenticate($revokeToken);
        } catch (AuthenticationException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
        }
    }

    /**
     * @inheritdoc
     * @param string $token
     */
    public function unsetRefreshToken($token)
    {
        $this->revokeToken($token);
    }

    /**
     * Revoke access token
     * @param string $token
     */
    public function unsetAccessToken($token)
    {
        $this->revokeToken($token);
    }
}
