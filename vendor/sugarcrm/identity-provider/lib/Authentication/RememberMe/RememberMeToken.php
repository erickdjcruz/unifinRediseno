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

namespace Sugarcrm\IdentityProvider\Authentication\RememberMe;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Sugarcrm\IdentityProvider\Srn\Converter as SRNConverter;

class RememberMeToken implements RememberMeTokenInterface
{
    /**
     * @var TokenInterface
     */
    private $token;

    public function __construct(TokenInterface $token)
    {
        $this->token = $token;
    }

    /**
     * @inheritDoc
     */
    public function getSource(): TokenInterface
    {
        return $this->token;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return $this->token->__toString();
    }

    /**
     * @inheritDoc
     */
    public function getRoleNames(): array
    {
        return $this->token->getRoleNames();
    }

    /**
     * @inheritDoc
     */
    public function getCredentials()
    {
        return $this->token->getCredentials();
    }

    /**
     * @inheritDoc
     */
    public function getUser()
    {
        return $this->token->getUser();
    }

    /**
     * @inheritDoc
     */
    public function setUser($user)
    {
        $this->token->setUser($user);
    }

    /**
     * Only for backward compatibility
     * @inheritDoc
     */
    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    /**
     * @inheritDoc
     */
    public function getUserIdentifier(): string
    {
        return $this->token->getUserIdentifier();
    }

    /**
     * @inheritDoc
     */
    public function isAuthenticated()
    {
        return $this->token->isAuthenticated();
    }

    /**
     * @inheritDoc
     */
    public function setAuthenticated($isAuthenticated)
    {
        $this->token->setAuthenticated($isAuthenticated);
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
        $this->token->eraseCredentials();
    }

    /**
     * @inheritDoc
     */
    public function getAttributes()
    {
        return $this->token->getAttributes();
    }

    /**
     * @inheritDoc
     */
    public function setAttributes(array $attributes)
    {
        $this->token->setAttributes($attributes);
    }

    /**
     * @inheritDoc
     */
    public function hasAttribute($name)
    {
        return $this->token->hasAttribute($name);
    }

    /**
     * @inheritDoc
     */
    public function getAttribute($name)
    {
        return $this->token->getAttribute($name);
    }

    /**
     * @inheritDoc
     */
    public function setAttribute($name, $value)
    {
        $this->token->setAttribute($name, $value);
    }

    /**
     * @return mixed
     */
    public function getProviderKey()
    {
        return $this->token->getProviderKey();
    }


    /**
     * @inheritDoc
     */
    public function setLoggedIn(): RememberMeTokenInterface
    {
        $this->setAttribute(static::TOKEN_STATUS, static::LOGGED_IN);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setLoggedActive(): RememberMeTokenInterface
    {
        $this->setAttribute(static::TOKEN_STATUS, static::LOGGED_IN | static::ACTIVE);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setLoggedInactive(): RememberMeTokenInterface
    {
        $statusValue =
            $this->hasAttribute(static::TOKEN_STATUS) ? $this->getAttribute(static::TOKEN_STATUS) : 0;
        if ($statusValue & static::ACTIVE) {
            $this->setAttribute(static::TOKEN_STATUS, $statusValue ^ static::ACTIVE);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setLoggedOut(): RememberMeTokenInterface
    {
        $this->setAttribute(static::TOKEN_STATUS, static::LOGGED_OUT);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isLoggedIn(): bool
    {
        return $this->hasAttribute(static::TOKEN_STATUS) &&
            $this->getAttribute(static::TOKEN_STATUS) & static::LOGGED_IN;
    }

    /**
     * @inheritDoc
     */
    public function isLoggedOut(): bool
    {
        return $this->hasAttribute(static::TOKEN_STATUS) &&
            $this->getAttribute(static::TOKEN_STATUS) & static::LOGGED_OUT;
    }

    /**
     * @inheritDoc
     */
    public function isActive(): bool
    {
        return $this->hasAttribute(static::TOKEN_STATUS) &&
            $this->getAttribute(static::TOKEN_STATUS) & static::ACTIVE;
    }

    /**
     * @inheritDoc
     */
    public function getSRN(): string
    {
        if ($this->hasAttribute('srn')) {
            return $this->getAttribute('srn');
        }

        return '';
    }

    /**
     * @inheritDoc
     */
    public function serialize(): ?string
    {
        return serialize($this->token);
    }

    /**
     * @inheritDoc
     */
    public function unserialize($data): void
    {
        $this->token = unserialize($data, ['allowed_classes' => false]);
    }

    /**
     * @inheritDoc
     */
    public function getTenantId(): string
    {
        return SRNConverter::fromString($this->getSRN())->getTenantId();
    }

    /**
     * @inheritDoc
     */
    public function __serialize(): array
    {
        return [
            'token' => $this->token,
        ];
    }

    /**
     * @inheritDoc
     */
    public function __unserialize(array $data): void
    {
        $this->token = $data['token'];
    }
}
