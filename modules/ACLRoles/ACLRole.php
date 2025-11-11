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

use Doctrine\DBAL\Connection;
use Sugarcrm\Sugarcrm\AccessControl\AccessControlManager;

class ACLRole extends SugarBean
{
    public $module_dir = 'ACLRoles';
    public $object_name = 'ACLRole';
    public $table_name = 'acl_roles';
    public $new_schema = true;
    public $disable_row_level_security = true;
    public $disable_custom_fields = true;
    public $relationship_fields = [
        'user_id' => 'users',
    ];

    public $created_by;


    public function __construct()
    {
        parent::__construct();
        $this->disable_row_level_security = true;
    }

    // bug 16790 - missing get_summary_text method led Tracker to display SugarBean's "base implementation"
    public function get_summary_text()
    {
        return "$this->name";
    }

    public function clearCaches()
    {
        //Need to invalidate the caches for rolesets when roles change (availible modules may change)
        if ($this->load_relationship('acl_role_sets')) {
            $rolesets = $this->acl_role_sets->getBeans();
            $mm = MetaDataManager::getManager();
            foreach ($rolesets as $roleset) {
                $context = new MetaDataContextRoleSet($roleset);
                $mm->invalidateCache($mm->getPlatformsWithCaches(), $context);
            }
        }
        sugar_cache_clear('ACL');
        Report::clearCaches();
    }

    /**
     * Sets the relationship between a role and an action and sets the access level of that relationship
     *
     * @param string $role_id - the role id
     * @param string $action_id - the ACL Action id
     * @param int $access - the access level ACL_ALLOW_ALL ACL_ALLOW_NONE ACL_ALLOW_OWNER...
     */
    public function setAction($role_id, $action_id, $access)
    {
        $action = BeanFactory::retrieveBean('ACLActions', $action_id);
        if (!$action) {
            return;
        }

        if ($action->acltype == 'module'
            && $action->category == 'Users'
            && $action->name != 'admin') {
            return;
        }

        $relationship_data = ['role_id' => $role_id, 'action_id' => $action_id,];
        $additional_data = ['access_override' => $access];
        $this->set_relationship('acl_roles_actions', $relationship_data, true, true, $additional_data);
    }


    /**
     * static  getUserRoles($user_id)
     * returns a list of ACLRoles for a given user id
     *
     * @param GUID $user_id
     * @return a list of ACLRole objects
     */
    public static function getUserRoles($user_id, $getAsNameArray = true)
    {
        //if we don't have it loaded then lets check against the db
        $query = 'SELECT acl_roles.* ' .
            'FROM acl_roles ' .
            'INNER JOIN acl_roles_users ON acl_roles_users.user_id = ? ' .
            'AND acl_roles_users.role_id = acl_roles.id AND acl_roles_users.deleted = 0 ' .
            'WHERE acl_roles.deleted=0 ';

        $stmt = DBManagerFactory::getConnection()->executeQuery($query, [$user_id]);

        $user_roles = [];

        while ($row = $stmt->fetchAssociative()) {
            $role = BeanFactory::newBean('ACLRoles');
            $role->populateFromRow($row);
            if ($getAsNameArray) {
                $user_roles[] = $role->name;
            } else {
                $user_roles[] = $role;
            }
        }

        return $user_roles;
    }

    /**
     * Returns a list of Role names for a given user id
     *
     * @param string $user_id
     * @return array List of ACLRole Names
     */
    public static function getUserRoleNames($user_id)
    {
        $user_roles = sugar_cache_retrieve('RoleMembershipNames_' . $user_id);

        if (!$user_roles) {
            //if we don't have it loaded then lets check against the db
            $query = 'SELECT acl_roles.* ' .
                'FROM acl_roles ' .
                'INNER JOIN acl_roles_users ON acl_roles_users.user_id = ? ' .
                'AND acl_roles_users.role_id = acl_roles.id AND acl_roles_users.deleted = 0 ' .
                'WHERE acl_roles.deleted=0 ';

            $stmt = DBManagerFactory::getConnection()->executeQuery($query, [$user_id]);

            $user_roles = [];

            while ($row = $stmt->fetchAssociative()) {
                $user_roles[] = $row['name'];
            }

            sugar_cache_put('RoleMembershipNames_' . $user_id, $user_roles);
        }

        return $user_roles;
    }


    /**
     * static getAllRoles($returnAsArray = false)
     *
     * @param boolean $returnAsArray - should it return the results as an array of arrays or as an array of ACLRoles
     * @return either an array of array representations of acl roles or an array of ACLRoles
     */
    public static function getAllRoles($returnAsArray = false)
    {
        $query = 'SELECT acl_roles.* FROM acl_roles
                    WHERE acl_roles.deleted=0 ORDER BY name';

        $stmt = DBManagerFactory::getConnection()->executeQuery($query);

        $roles = [];

        while ($row = $stmt->fetchAssociative()) {
            $role = BeanFactory::newBean('ACLRoles');
            $role->populateFromRow($row);
            if ($returnAsArray) {
                $roles[] = $role->toArray();
            } else {
                $roles[] = $role;
            }
        }
        return $roles;
    }

    /**
     * static getRoleActions($role_id)
     *
     * gets the actions of a given role
     *
     * @param GUID $role_id
     * @return array of actions
     */
    public static function getRoleActions($role_id, $type = 'module')
    {
        global $beanList;
        //if we don't have it loaded then lets check against the db

        $builder = DBManagerFactory::getConnection()->createQueryBuilder();

        //only if we have a role id do we need to join the table otherwise lets use the ones defined in acl_actions as the defaults
        $selectFields = ['acl_actions.*'];
        if (!empty($role_id)) {
            $selectFields[] = 'ara.access_override';
        }

        $builder->select($selectFields)
            ->from('acl_actions');

        if (!empty($role_id)) {
            $builder->leftJoin(
                'acl_actions',
                'acl_roles_actions',
                'ara',
                'ara.role_id = :role_id AND ara.action_id = acl_actions.id AND ara.deleted = 0'
            )
                ->setParameter('role_id', $role_id);
        }

        $builder->where('acl_actions.deleted = 0')
            ->addOrderBy('acl_actions.category')
            ->addOrderBy('acl_actions.name');

        $stmt = $builder->execute();

        $role_actions = [];

        while ($row = $stmt->fetchAssociative()) {
            $action = BeanFactory::newBean('ACLActions');
            $action->populateFromRow($row);
            if (!empty($row['access_override'])) {
                $action->aclaccess = $row['access_override'];
            } else {
                $action->aclaccess = ACL_ALLOW_DEFAULT;
            }
            //#27877 . If  there is no this module in beanlist , we will not show them in UI, no matter this module was deleted or not in ACL_ACTIONS table.
            if (empty($beanList[$action->category])) {
                continue;
            }
            //end
            if (!AccessControlManager::instance()->allowModuleAccess($action->category)) {
                continue;
            }

            if (!isset($role_actions[$action->category])) {
                $role_actions[$action->category] = [];
            }

            $role_actions[$action->category][$action->acltype][$action->name] = $action->toArray();
        }

        // Sort by translated categories
        uksort($role_actions, 'ACLRole::langCompare');
        return $role_actions;
    }

    private static function langCompare($a, $b)
    {
        global $app_list_strings;
        // Fallback to array key if translation is empty
        $a = empty($app_list_strings['moduleList'][$a]) ? $a : $app_list_strings['moduleList'][$a];
        $b = empty($app_list_strings['moduleList'][$b]) ? $b : $app_list_strings['moduleList'][$b];
        return $a <=> $b;
    }

    /**
     * Clears ACL caches after Role deletion so that Users will pick up the
     * latest ACL data
     *
     * @inheritdoc
     */
    public function mark_deleted($id)
    {
        $this->clearCaches();
        $this->updateUsersACLInfo();

        parent::mark_deleted($id);
    }

    /**
     * function mark_relationships_deleted($id)
     *
     * special case to delete acl_roles_actions relationship
     *
     * @param ACLRole GUID $id
     */
    public function mark_relationships_deleted($id)
    {
        //we need to delete the actions relationship by hand (special case)
        $date_modified = TimeDate::getInstance()->nowDb();
        $query = 'UPDATE acl_roles_actions SET deleted=1 , date_modified=? WHERE role_id = ? AND deleted=0';

        $conn = $this->db->getConnection();
        $conn->executeStatement($query, [$date_modified, $id]);

        parent::mark_relationships_deleted($id);
    }

    /**
     * Updates users date_modified to make sure clients use latest version of ACLs
     */
    public function updateUsersACLInfo()
    {
        $query = 'UPDATE users SET date_modified = ? WHERE id IN ('
            . 'SELECT user_id FROM acl_roles_users WHERE role_id = ? AND deleted = 0'
            . ') AND deleted = 0';

        $this->db->getConnection()->executeUpdate($query, [
            TimeDate::getInstance()->nowDb(),
            $this->id,
        ]);
    }
}
