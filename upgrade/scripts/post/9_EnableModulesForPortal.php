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
 * Enable Messages and Emails for portal
 */
class SugarUpgradeEnableModulesForPortal extends UpgradeScript
{
    public $order = 9200;
    public $type = self::UPGRADE_DB;

    public function run()
    {
        if (version_compare($this->from_version, '12.2.0', '>=')) {
            return;
        }

        $allowedModules = ['Messages', 'Emails'];
        $allowedActions = ['edit', 'admin', 'access', 'list', 'view'];

        $role = BeanFactory::newBean('ACLRoles');
        $role->retrieve_by_string_fields(['name' => 'Customer Self-Service Portal Role']);

        if (!empty($role->id)) { // 'role id not empty' means portal has been enabled before
            $roleActions = $role->getRoleActions($role->id);

            // update role
            foreach ($roleActions as $moduleName => $actions) {
                if (!in_array($moduleName, $allowedModules)) {
                    continue;
                }
                if (isset($actions['module']['access']['id'])) {
                    $role->setAction($role->id, $actions['module']['access']['id'], ACL_ALLOW_ENABLED);
                }
                foreach ($actions['module'] as $actionName => $action) {
                    if (in_array($actionName, $allowedActions)) {
                        $aclAllow = ACL_ALLOW_ALL;
                    } else {
                        $aclAllow = ACL_ALLOW_NONE;
                    }
                    if ($actionName != 'access') {
                        $role->setAction($role->id, $action['id'], $aclAllow);
                    }
                }
            }

            // save
            $user = $this->getPortalUser();
            if ($user && !($user->check_role_membership($role->name))) {
                $user->load_relationship('aclroles');
                $user->aclroles->add($role);
                $user->save();
            }
        }
    }

    /**
     * @return SugarBean|null
     * @throws SugarApiExceptionNotFound
     */
    protected function getPortalUser()
    {
        $portalUserName = 'SugarCustomerSupportPortalUser';
        $id = BeanFactory::newBean('Users')->retrieve_user_id($portalUserName);
        if ($id) {
            $resultUser = BeanFactory::getBean('Users', $id);
            if ($resultUser) {
                return $resultUser;
            }
        }
        $this->log('Failed to get portal user');
        return null;
    }
}
