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

namespace Sugarcrm\Sugarcrm\IdentityProvider\Authentication\ServiceAccount;

use Exception;
use Sugarcrm\IdentityProvider\Srn\Converter;
use Sugarcrm\IdentityProvider\Srn\Manager as SrnManager;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\User;

class ServiceAccount extends User
{
    /**
     * @return \User
     */
    public function getSugarUser(): \User
    {
        if ($this->sugarUser === null) {
            $this->sugarUser = $this->getDataSourceUser();
            if ($this->sugarUser === null) {
                /** @var \User $userBean */
                $this->sugarUser = $this->getUserBean()->getSystemUser();
            }
        }
        return $this->sugarUser;
    }

    /**
     * @return \User
     */
    protected function getUserBean(): \User
    {
        return \BeanFactory::getBean('Users');
    }

    /**
     * @param string $srn
     */
    public function setDataSourceSRN(string $srn): void
    {
        $this->setAttribute('dataSourceSRN', $srn);
    }

    /**
     * @return string|null
     */
    public function getDataSourceSRN(): ?string
    {
        return $this->getAttribute('dataSourceSRN');
    }

    /**
     * @param string $name
     */
    public function setDataSourceName(string $name): void
    {
        $this->setAttribute('dataSourceName', $name);
    }

    /**
     * @return string|null
     */
    public function getDataSourceName(): ?string
    {
        return $this->getAttribute('dataSourceName');
    }

    /**
     * try to get user from dataSourceSRN
     * @return \User|null
     */
    protected function getDataSourceUser(): ?\User
    {
        $dataSourceSRN = $this->getDataSourceSRN();
        if (empty($dataSourceSRN)) {
            return null;
        }
        try {
            $userSRN = Converter::fromString($dataSourceSRN);
        } catch (Exception $e) {
            return null;
        }
        if (empty($userSRN->getResource()[0]) || !SrnManager::isUser($userSRN)) {
            return null;
        }
        if (empty($userSRN->getResource()[1])) {
            return null;
        }
        $userId = $userSRN->getResource()[1];
        $user = $this->getUserBean();
        $user->retrieve($userId, true, false);
        if (!$user->id || !$user->isDeveloperForAnyModule()) {
            return null;
        }
        return $user;
    }
}
