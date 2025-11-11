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

namespace Sugarcrm\Sugarcrm\IdentityProvider\Authentication\User;

use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Exception\InactiveUserException;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\User;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\UserProvider\SugarLocalUserProvider;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserChecker;
use Symfony\Component\Security\Core\User\UserInterface;

class SugarOIDCUserChecker extends UserChecker
{
    /**
     * Predefined user attributes.
     * @var array
     */
    protected $fixedUserAttributes = [
        'employee_status' => User::USER_EMPLOYEE_STATUS_ACTIVE,
        'status' => User::USER_STATUS_ACTIVE,
        'is_admin' => 0,
        'external_auth_only' => 1,
        'system_generated_password' => 0,
    ];

    /**
     * @var bool
     */
    protected $allowInactive = false;

    /**
     * @var SugarLocalUserProvider
     */
    protected $localUserProvider;

    /**
     * @param SugarLocalUserProvider $localUserProvider
     */
    public function __construct(SugarLocalUserProvider $localUserProvider)
    {
        $this->localUserProvider = $localUserProvider;
    }

    public function setAllowInactive(bool $allowInactive)
    {
        $this->allowInactive = $allowInactive;
        $this->localUserProvider->setAllowInactive($allowInactive);
    }

    public function checkPostAuth(UserInterface $user)
    {
        $this->loadSugarUser($user);
    }

    /**
     * Find or create Sugar User.
     *
     * @param UserInterface $user
     * @throws \Exception
     */
    protected function loadSugarUser(UserInterface $user)
    {
        $userAttributes = $user->getAttribute('oidc_data');
        $identify = $user->getAttribute('oidc_identify');

        try {
            $sugarUser = $this->localUserProvider
                ->loadUserByField($identify['value'], $identify['field'])
                ->getSugarUser();
            $this->setUserData($sugarUser, $user, $userAttributes);
        } catch (UsernameNotFoundException $e) {
            $userAttributes = array_merge(
                [$identify['field'] => $identify['value']],
                $this->fixedUserAttributes,
                $userAttributes
            );
            $sugarUser = $this->localUserProvider->createUser($userAttributes['user_name'], $userAttributes);
        } catch (InactiveUserException $e) {
            $sugarUser = $e->getSugarUser();
            if (!$sugarUser || (($userAttributes['status'] !== User::USER_STATUS_ACTIVE) && !$this->allowInactive)) {
                throw $e;
            }

            $this->setUserData($sugarUser, $user, $userAttributes);
        }
        $user->setSugarUser($sugarUser);
    }

    /**
     * Compare user data and set changes
     * PopulateFromRow couldn't be used because all non-oidc fields are set to empty
     * @param \User $sugarUser
     * @param array $attributes
     */
    protected function setUserData(\User $sugarUser, UserInterface $user, array $attributes)
    {
        $attribute = $user->getAttributes();
        if (isset($attribute['ext']['sudoer'])) {
            $sugarUser->sudoer = $attribute['ext']['sudoer'];
        }

        if (isset($attribute['ext']['is_user_manager'])) {
            $sugarUser->isIdmUserManager = $attribute['ext']['is_user_manager'];
        }

        $timeDate = \TimeDate::getInstance();
        $lastLogin = $timeDate->fromDb($sugarUser->last_login);

        if ($lastLogin && $user->hasAttribute('updated_at') &&
            $user->getAttribute('updated_at') < $lastLogin->getTimestamp()) {
            return;
        }
        $isDataChanged = false;
        $email = null;
        if (isset($attributes['email'])) {
            $primaryEmail = $sugarUser->emailAddress->getPrimaryAddress($sugarUser);
            if (strcasecmp((string)$primaryEmail, $attributes['email']) !== 0) {
                $email = $attributes['email'];
            }
            unset($attributes['email']);
        }

        if (array_key_exists('user_name', $attributes)) {
            unset($attributes['user_name']);
        }

        //do not need to update licenses_type if the user already exist
        if (array_key_exists('license_type', $attributes)) {
            unset($attributes['license_type']);
        }

        $attributes = $this->getDbMassagedAttributes($attributes, $sugarUser);
        foreach ($attributes as $name => $value) {
            if (isset($sugarUser->$name) && strcasecmp($sugarUser->$name, $value) !== 0) {
                $sugarUser->$name = $value;
                $isDataChanged = true;
            }
        }
        if ($isDataChanged) {
            $sugarUser->save();
        }
        if ($email) {
            $sugarUser->emailAddress->addAddress($email, true);
            $sugarUser->emailAddress->save($sugarUser->id, $sugarUser->module_dir);
        }
    }

    /**
     * Get Db massaged attributes for comparison
     *
     * @param array $attributes
     * @param \User $sugarUser
     * @return array
     */
    private function getDbMassagedAttributes(array $attributes, \User $sugarUser): array
    {
        $db = $sugarUser->db;
        $fieldDefs = $sugarUser->getFieldDefinitions('name', array_keys($attributes));
        array_walk($attributes, function (&$value, $key) use ($db, $fieldDefs) {
            $value = $db->massageValue($value, $fieldDefs[$key], true);
        });

        return $attributes;
    }
}
