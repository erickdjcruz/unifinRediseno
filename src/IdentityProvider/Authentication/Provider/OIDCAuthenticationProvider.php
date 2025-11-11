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

namespace Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;

use Sugarcrm\IdentityProvider\Authentication\UserMapping\MappingInterface;
use Sugarcrm\IdentityProvider\STS\EndpointInterface;
use Sugarcrm\IdentityProvider\Srn\Converter;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Exception\IdmNonrecoverableException;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Exception\InvalidTokenException;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Exception\ServiceAccountAuthenticationException;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\OAuth2\Client\Provider\IdmProvider;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\ServiceAccount\Checker;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\ServiceAccount\ServiceAccount;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Token\OIDC\CodeToken;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Token\OIDC\IntrospectToken;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Token\OIDC\JWTBearerToken;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Token\OIDC\RefreshToken;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Token\OIDC\RevokeToken;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\User;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\User\SugarOIDCUserChecker;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\UserProvider\SugarOIDCUserProvider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @internal
 * Class OIDCAuthenticationProvider
 * Provides all authentication operations on OIDC server.
 */
class OIDCAuthenticationProvider implements AuthenticationProviderInterface
{
    /**
     * @var IdmProvider
     */
    protected $oAuthProvider = null;

    /**
     * @var SugarOIDCUserProvider
     */
    protected $userProvider;

    /**
     * @var SugarOIDCUserChecker
     */
    protected $userChecker;

    /**
     * @var MappingInterface
     */
    protected $userMapping;

    /**
     * @var Sugarcrm\Sugarcrm\IdentityProvider\Authentication\ServiceAccount\Checker
     */
    protected $SAChecker;

    /**
     * @var \LoggerManager
     */
    protected $logger;

    /**
     * List of handlers that can be used to handle tokens.
     * Actually, they correspond to steps of SAML authentication flow.
     *
     * @var array
     */
    protected $handlers = [
        IntrospectToken::class => 'introspectToken',
        JWTBearerToken::class => 'jwtBearerGrantTypeAuth',
        RefreshToken::class => 'refreshTokenGrantTypeAuth',
        CodeToken::class => 'authCodeGrantTypeAuth',
        RevokeToken::class => 'revokeToken',
    ];

    /**
     * OIDCAuthenticationProvider constructor.
     * @param AbstractProvider $oAuthProvider
     * @param UserProviderInterface $userProvider
     * @param UserCheckerInterface $userChecker
     * @param MappingInterface $userMapping
     */
    public function __construct(
        AbstractProvider      $oAuthProvider,
        UserProviderInterface $userProvider,
        SugarOIDCUserChecker  $userChecker,
        MappingInterface      $userMapping,
        Checker               $SAChecker
    ) {

        $this->oAuthProvider = $oAuthProvider;
        $this->userProvider = $userProvider;
        $this->userChecker = $userChecker;
        $this->userMapping = $userMapping;
        $this->SAChecker = $SAChecker;
    }

    /**
     * to get logger
     * @return \LoggerManager
     */
    protected function getLogger(): \LoggerManager
    {
        if (empty($this->logger)) {
            $this->logger = \LoggerManager::getLogger();
        }
        return $this->logger;
    }

    /**
     * @inheritdoc
     */
    public function authenticate(TokenInterface $token)
    {
        $handlerMethod = null;
        foreach ($this->handlers as $tokenClass => $handler) {
            if ($token instanceof $tokenClass) {
                $handlerMethod = $handler;
                break;
            }
        }
        if (!$handlerMethod) {
            throw new AuthenticationServiceException('There is no authentication handler for ' . get_class($token));
        }

        try {
            return $this->{$handlerMethod}($token);
        } catch (AuthenticationException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new AuthenticationException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Processes exchange oauth2 code to access token.
     *
     * @param TokenInterface $token
     * @return TokenInterface
     *
     * @throws AuthenticationException
     */
    protected function authCodeGrantTypeAuth(TokenInterface $token): TokenInterface
    {
        try {
            $accessToken = $this->oAuthProvider->getAccessToken(
                'authorization_code',
                ['code' => $token->getCredentials(), 'scope' => explode(' ', $token->getScope())]
            );

            $resultToken = new CodeToken($token->getCredentials(), $token->getScope());
            $this->populateAuthenticatedTokenByAccessToken($accessToken, $resultToken);

            return $resultToken;
        } catch (\Exception $e) {
            throw new AuthenticationException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param TokenInterface $token
     * @return TokenInterface
     *
     * @throws AuthenticationException
     */
    protected function refreshTokenGrantTypeAuth(TokenInterface $token): TokenInterface
    {
        try {
            $accessToken = $this->oAuthProvider->getAccessToken(
                'refresh_token',
                ['refresh_token' => $token->getCredentials()]
            );

            $resultToken = new RefreshToken($accessToken->getRefreshToken());
            $this->populateAuthenticatedTokenByAccessToken($accessToken, $resultToken);

            return $resultToken;
        } catch (\Exception $e) {
            throw new AuthenticationException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Token introspection on OIDC server.
     *
     * @param TokenInterface $token
     * @return IntrospectToken
     */
    protected function introspectToken(TokenInterface $token)
    {
        $user = null;
        $resultToken = null;
        $accessToken = new AccessToken(['access_token' => $token->getCredentials()]);
        $result = $this->oAuthProvider->introspectToken($accessToken);

        if (empty($result) || !is_array($result)) {
            $this->getLogger()->fatal('Wrong response from STS');
            throw new AuthenticationException('Wrong response from STS');
        }

        if (empty($result['active'])) {
            $this->getLogger()->warn('IDM access token is expired: ' . json_encode($result));
            throw new InvalidTokenException('IDM access token is expired');
        }

        if (empty($result['sub'])) {
            throw new AuthenticationException('Empty subject in OIDC token');
        }

        $resultScopes = explode($this->oAuthProvider->getScopeSeparator(), $result['scope'] ?? '');
        if (!in_array($token->getCrmOAuthScope(), $resultScopes)) {
            throw new IdmNonrecoverableException(
                sprintf('Access token should contain %s scope', $token->getCrmOAuthScope())
            );
        }

        $resultToken = new IntrospectToken($token->getCredentials(), $token->getTenant(), $token->getCrmOAuthScope());
        $resultToken->setAttributes($result);
        $resultToken->setAttribute('platform', $token->getAttribute('platform'));

        /** @var User|ServiceAccount $user */
        $user = $this->userProvider->loadUserBySrn($result['sub']);

        if ($user->isServiceAccount()) {
            if (!$this->SAChecker->isAllowed($token->getCredentials(), $result)) {
                throw new ServiceAccountAuthenticationException(
                    sprintf('Service account %s is not allowed for this sugarcrm instance', $result['sub'])
                );
            }
            if (!empty($result['ext']['dataSourceSRN'])) {
                $user->setDataSourceSRN($result['ext']['dataSourceSRN']);
            }

            if (!empty($result['ext']['dataSourceName'])) {
                $user->setDataSourceName($result['ext']['dataSourceName']);
            }

            $resultToken->setUser($user);
            $resultToken->setAuthenticated(true);
            return $resultToken;
        }

        if (isset($result['ext']['tid']) && $token->getTenant() != $result['ext']['tid']) {
            throw new IdmNonrecoverableException(
                sprintf('Access token does not belong to tenant %s', $token->getTenant())
            );
        }

        if (isset($result['ext']['sudoer'])) {
            $this->userChecker->setAllowInactive(true);
        }

        $userSRN = Converter::fromString($result['sub'] ?? '');
        $tenantSRN = Converter::fromString($token->getTenant());
        if ($userSRN->getTenantId() != $tenantSRN->getTenantId()) {
            throw new IdmNonrecoverableException(
                sprintf('Access token claims should belong to tenant %s', $token->getTenant())
            );
        }

        $userInfo = $this->oAuthProvider->getUserInfo($accessToken);
        if (!empty($userInfo)) {
            $this->getLogger()->debug('user info: ' . is_array($userInfo) ? json_encode($userInfo) : $userInfo);
        } else {
            $this->getLogger()->fatal('Can not retrieve user info from STS for subject: ' . $result['sub'] ?? 'empty subject');
        }
        $user->setAttribute('oidc_data', $this->userMapping->map($userInfo));
        if (isset($userInfo['updated_at'])) {
            $user->setAttribute('updated_at', $userInfo['updated_at']);
        }
        $user->setAttribute('oidc_identify', $this->userMapping->mapIdentity($result));

        foreach ($result as $key => $value) {
            $user->setAttribute($key, $value);
        }
        try {
            $this->userChecker->checkPostAuth($user);
        } catch (\Exception $e) {
            throw new IdmNonrecoverableException($e->getMessage());
        }

        $resultToken->setUser($user);
        $resultToken->setAuthenticated(true);

        return $resultToken;
    }

    /**
     * Token revocation on OIDC server.
     *
     * @param TokenInterface $token
     * @return RevokeToken
     */
    protected function revokeToken(TokenInterface $token)
    {
        $accessToken = new AccessToken(['access_token' => $token->getCredentials()]);

        $this->oAuthProvider->revokeToken($accessToken);

        return new RevokeToken($token->getCredentials());
    }

    /**
     * Provides JWT Bearer oauth2 flow
     *
     * @param TokenInterface $token
     * @return TokenInterface
     */
    protected function jwtBearerGrantTypeAuth(TokenInterface $token)
    {
        $userSrn = Converter::fromString($token->getIdentity());
        $userResource = $userSrn->getResource();
        $this->userProvider->setAllowInactive($token->hasAttribute('sudoer'));
        $user = $this->userProvider->loadUserByField($userResource[1], 'id');
        $token->setUser($user);

        $keySetInfo = $this->oAuthProvider->getKeySet();
        $privateKey = array_filter($keySetInfo['keys'], function ($value) {
            return $value['kid'] == EndpointInterface::PRIVATE_KEY;
        });

        $token->setAttribute('privateKey', array_shift($privateKey));
        $token->setAttribute('aud', $this->oAuthProvider->getBaseAccessTokenUrl([]));
        $token->setAttribute('iss', $keySetInfo['clientId']);
        $token->setAttribute('kid', $keySetInfo['keySetId']);
        $token->setAttribute('iat', time());

        $accessToken = $this->oAuthProvider->getJwtBearerAccessToken((string)$token);
        $resultToken = clone $token;
        $this->populateAuthenticatedTokenByAccessToken($accessToken, $resultToken);
        return $resultToken;
    }

    /**
     * Populates Authenticated Token by data stored in Access Token
     * @param AccessToken $source
     * @param TokenInterface $destination
     */
    protected function populateAuthenticatedTokenByAccessToken(AccessToken $source, TokenInterface $destination): void
    {
        $extraValues = $source->getValues();
        $destination->setAttribute('token', $source->getToken());
        $destination->setAttribute('exp', $source->getExpires());
        $destination->setAttribute('expires_in', $source->getExpires() - time());
        $destination->setAttribute('token_type', $extraValues['token_type'] ?? 'bearer');
        $destination->setAttribute('scope', $extraValues['scope'] ?? null);
        if ($source->getRefreshToken()) {
            $destination->setAttribute('refresh_token', $source->getRefreshToken());
        }
        $destination->setAuthenticated(true);
    }

    /**
     * @inheritdoc
     */
    public function supports(TokenInterface $token)
    {
        return array_key_exists(get_class($token), $this->handlers);
    }
}
