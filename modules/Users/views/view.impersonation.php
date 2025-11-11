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

use Sugarcrm\IdentityProvider\Srn;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\AuthProviderBasicManagerBuilder;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Config;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Token\OIDC\IntrospectToken;
use Sugarcrm\Sugarcrm\inc\Entitlements\Exception\SubscriptionException;

class UsersViewImpersonation extends SidecarView
{
    use IdmModeAuthTrait;

    /**
     * @var User
     */
    protected $issuer = null;

    protected const TOKEN_TIME_WINDOW = 30;

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct();
        $this->options['show_header'] = false;
        $this->options['show_javascript'] = false;
    }

    public function preDisplay($params = [])
    {
        if (!$this->getIdpConfig()->isIDMModeEnabled()) {
            $this->redirect();
        }

        $platform = $this->request->getValidInputGet('platform');
        if (!empty($platform)) {
            $this->platform = $platform;
        }

        try {
            $this->ensureIssuer();

            if (!$this->isImpersonationAllowed()) {
                $this->redirect();
            }

            $this->setupUser();
        } catch (SubscriptionException $e) {
            SugarApplication::redirect('./#licenseSeats');
        } catch (\Exception $e) {
            $this->redirect();
        }

        parent::preDisplay($params);
    }

    protected function setupUser(): void
    {
        $accessToken = $this->request->getValidInputPost('access_token');
        $refreshToken = $this->request->getValidInputPost('refresh_token');

        if (empty($accessToken)) {
            $this->redirect();
        }
        /** @var SugarOAuth2ServerOIDC $oAuthServer */
        $oAuthServer = $this->getOAuth2Server();

        $GLOBALS['logic_hook']->call_custom_logic('Users', 'before_login');

        $tokenInfo = $oAuthServer->verifyAccessToken($accessToken);
        if (!$tokenInfo) {
            $this->redirect();
        }

        /** @var User $user */
        $user = BeanFactory::getBean('Users', $tokenInfo['user_id']);
        if (!$user) {
            $this->redirect();
        }
        $user->call_custom_logic('after_login');

        $this->ensureLoginStatus($user);

        $expires_in = intval($tokenInfo['expires'] - time());
        $this->setupDownloadToken($accessToken, $expires_in);

        $this->setupBWCImpersonationSession();

        $this->authorization = [
            'access_token' => $accessToken,
            'expires_in' => $expires_in,
            'refresh_token' => $refreshToken,
            'impersonation_for' => $this->issuer->id,
        ];
    }

    protected function isImpersonationAllowed(): bool
    {
        $accessToken = $this->request->getValidInputPost('access_token');
        if (empty($accessToken)) {
            return false;
        }

        $accessTokenInfo = $this->introspectAccessToken($accessToken);

        if (!$accessTokenInfo) {
            return false;
        }

        $iat = intval($accessTokenInfo->getAttribute('iat'), 10);

        $tn = time();
        if ($iat + static::TOKEN_TIME_WINDOW < $tn || $iat - static::TOKEN_TIME_WINDOW > $tn) {
            return false;
        }

        $user = $accessTokenInfo->getUser();

        if (!$user || !$user->hasAttribute('sudoer')) {
            return false;
        }

        // Forbid to impersonate yourself
        if (!$user->getSugarUser() || $user->getSugarUser()->id == $this->issuer->id) {
            return false;
        }

        $sudoer = $user->getAttribute('sudoer');

        if (!$sudoer) {
            return false;
        }

        // Forbid impersonate user in case the sudoer is not the issuer
        try {
            $sudoerSrn = Srn\Converter::fromString($sudoer);
            if (!Srn\Manager::isUser($sudoerSrn) || $sudoerSrn->getResource()[1] != $this->issuer->id) {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    protected function ensureIssuer(): void
    {
        $token = $this->grabIssuerToken();
        if (empty($token)) {
            $this->redirect();
        }

        $issuerToken = $this->introspectAccessToken($token);

        if (!$issuerToken) {
            $this->redirect();
        }

        if (!$issuerToken->isAuthenticated()) {
            $this->redirect();
        }

        if (!$issuerToken->getUser()) {
            $this->redirect();
        }

        $issuer = $issuerToken->getUser()->getSugarUser();
        if (!$issuer) {
            $this->redirect();
        }

        if (!$issuer->isAdmin()) {
            $this->redirect();
        }
        $this->issuer = $issuer;
    }


    /**
     * @param string $token
     * @return TokenInterface|null
     */
    protected function introspectAccessToken(string $token): ?TokenInterface
    {
        $idmModeConfig = $this->getIdpConfig()->getIDMModeConfig();
        $authManager = (new AuthProviderBasicManagerBuilder($this->getIdpConfig()))->buildAuthProviders();

        $introspectToken = new IntrospectToken($token, $idmModeConfig['tid'], $idmModeConfig['crmOAuthScope']);
        $introspectToken->setAttribute('platform', $this->platform);

        return $authManager->authenticate($introspectToken);
    }

    protected function grabIssuerToken(): ?string
    {
        if (array_key_exists('HTTP_OAUTH_TOKEN', $_SERVER)) {
            return $_SERVER['HTTP_OAUTH_TOKEN'];
        }
        return $this->request->getValidInputPost('issuer');
    }

    protected function getOAuth2Server(): SugarOAuth2Server
    {
        return \SugarOAuth2Server::getOAuth2Server($this->platform);
    }

    /**
     * Redirects to the main page.
     */
    protected function redirect(): void
    {
        $GLOBALS['logic_hook']->call_custom_logic('Users', 'login_failed');
        SugarApplication::redirect('./#stsAuthError');
    }

    protected function getIdpConfig(): Config
    {
        return new Config(\SugarConfig::getInstance());
    }
}
