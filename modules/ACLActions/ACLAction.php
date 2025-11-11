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

use Sugarcrm\Sugarcrm\DependencyInjection\Container;
use Sugarcrm\Sugarcrm\ACL\Cache;

require_once 'modules/ACLActions/actiondefs.php';

/**
 * ACL actions
 * @api
 */
class ACLAction extends SugarBean
{
    public $module_dir = 'ACLActions';
    public $object_name = 'ACLAction';
    public $table_name = 'acl_actions';
    public $new_schema = true;
    public $disable_custom_fields = true;
    public $disable_row_level_security = true;

    /**
     * Cache of the ACL values
     * @var array
     */
    protected static $acls;

    /**
     * static addActions($category, $type='module')
     * Adds all default actions for a category/type
     *
     * @param STRING $category - the category (e.g module name - Accounts, Contacts)
     * @param STRING $type - the type (e.g. 'module', 'field')
     */
    public static function addActions($category, $type = 'module')
    {
        global $ACLActions;
        $db = DBManagerFactory::getInstance();
        if (isset($ACLActions[$type])) {
            foreach ($ACLActions[$type]['actions'] as $action_name => $action_def) {
                $action = BeanFactory::newBean('ACLActions');
                $id = $db->getConnection()
                    ->executeQuery(
                        "SELECT id FROM {$action->table_name} WHERE name=? AND category=? AND acltype=? and deleted=0",
                        [$action_name, $category, $type]
                    )->fetchOne();
                //only add if an action with that name and category don't exist
                if (false === $id) {
                    $action->name = $action_name;
                    $action->category = $category;
                    $action->aclaccess = $action_def['default'];
                    $action->acltype = $type;
                    $action->modified_user_id = '1';
                    $action->created_by = 1;
                    $action->save();
                }
            }
        } else {
            sugar_die("FAILED TO ADD: $category - TYPE $type NOT DEFINED IN modules/ACLActions/actiondefs.php");
        }
    }

    /**
     * static removeActions($category, $type='module')
     * Removes all default actions for a category/type
     *
     * @param STRING $category - the category (e.g module name - Accounts, Contacts)
     * @param STRING $type - the type (e.g. 'module', 'field')
     */
    public static function removeActions($category, $type = 'module')
    {
        global $ACLActions;
        $db = DBManagerFactory::getInstance();
        if (isset($ACLActions[$type])) {
            foreach ($ACLActions[$type]['actions'] as $action_name => $action_def) {
                $action = BeanFactory::newBean('ACLActions');
                $id = $db->getConnection()
                    ->executeQuery(
                        "SELECT id FROM {$action->table_name} WHERE name=? AND category=? AND acltype=? and deleted=0",
                        [$action_name, $category, $type]
                    )->fetchOne();
                if (false !== $id) {
                    $action->mark_deleted($id);
                }
            }
        } else {
            sugar_die("FAILED TO REMOVE: - CATEGORY : $category - TYPE $type NOT DEFINED IN modules/ACLActions/actiondefs.php");
        }
    }

    /**
     * static AccessColor($access)
     *
     * returns the color associated with an access level
     * these colors exist in the definitions in modules/ACLActions/actiondefs.php
     * @param INT $access - the access level you want the color for
     * @return the color either name or hex representation or false if the level does not exist
     */
    protected static function AccessColor($access)
    {
        global $ACLActionAccessLevels;
        if (isset($ACLActionAccessLevels[$access])) {
            return $ACLActionAccessLevels[$access]['color'];
        }
        return false;
    }

    /**
     * static AccessName($access)
     *
     * returns the translated name  associated with an access level
     * these label definitions  exist in the definitions in modules/ACLActions/actiondefs.php
     * @param INT $access - the access level you want the color for
     * @return the translated access level name or false if the level does not exist
     */
    public static function AccessName($access)
    {
        global $ACLActionAccessLevels;
        if (isset($ACLActionAccessLevels[$access])) {
            return translate($ACLActionAccessLevels[$access]['label'], 'ACLActions');
        }
        return false;
    }

    /**
     * static AccessLabel($access)
     *
     * returns the label  associated with an access level
     * these label definitions  exist in the definitions in modules/ACLActions/actiondefs.php
     * @param INT $access - the access level you want the color for
     * @return the access level label or false if the level does not exist
     */
    protected static function AccessLabel($access)
    {
        global $ACLActionAccessLevels;
        if (isset($ACLActionAccessLevels[$access])) {
            $label = preg_replace('/(LBL_ACCESS_)(.*)/', '$2', $ACLActionAccessLevels[$access]['label']);
            return strtolower($label);
        }
        return false;
    }

    /**
     * static getAccessOptions()
     * this is used for building select boxes
     * @return array containg access levels (ints) as keys and access names as values
     */
    protected static function getAccessOptions($action, $type = 'module')
    {
        global $ACLActions;
        $options = [];

        if (empty($ACLActions[$type]['actions'][$action]['aclaccess'])) {
            return $options;
        }
        foreach ($ACLActions[$type]['actions'][$action]['aclaccess'] as $action) {
            $options[$action] = ACLAction::AccessName($action);
        }
        return $options;
    }

    /**
     * function static getDefaultActions()
     * This function will return a list of acl actions with their default access levels
     *
     *
     */
    public static function getDefaultActions($type = 'module', $action = '')
    {
        $query = 'SELECT * FROM acl_actions WHERE deleted=0 ';
        $params = [];
        if (!empty($type)) {
            $query .= ' AND acltype=?';
            $params[] = $type;
        }
        if (!empty($action)) {
            $query .= 'AND name=?';
            $params[] = $action;
        }
        $query .= ' ORDER BY category';

        $db = DBManagerFactory::getInstance();
        $result = $db->getConnection()
            ->executeQuery($query, $params);
        $default_actions = [];
        foreach ($result->iterateAssociative() as $row) {
            $acl = BeanFactory::newBean('ACLActions');
            $acl->populateFromRow($row);
            $default_actions[] = $acl;
        }
        return $default_actions;
    }

    protected static function loadFromCache($user_id, $type)
    {
        return Container::getInstance()->get(Cache::class)->retrieve($user_id, $type);
    }

    protected static function storeToCache($user_id, $type, $data)
    {
        return Container::getInstance()->get(Cache::class)->store($user_id, $type, $data);
    }

    /**
     * static getUserActions($user_id,$refresh=false, $category='', $action='')
     * returns a list of user actions
     * @param string $user_id
     * @param BOOLEAN $refresh
     * @param string|null $category @deprecated
     * @param string|null $type @deprecated
     * @param string|null $action @deprecated
     * @return ARRAY of ACLActionsArray
     */
    public static function getUserActions(
        ?string $user_id,
        ?bool   $refresh = false,
        ?string $category = null/* @deprecated */,
        ?string $type = null/* @deprecated */,
        ?string $action = null/* @deprecated */
    ): array {


        if (empty($user_id)) {
            return [];
        }
        if (!$refresh) {
            if (empty(self::$acls[$user_id])) {
                self::$acls[$user_id] = self::loadFromCache($user_id, 'acls');
            }
            if (!empty(self::$acls[$user_id])) {
                if ($category === null && $action === null) {
                    return self::$acls[$user_id];
                } elseif ($category !== null && isset(self::$acls[$user_id][$category])) {
                    LoggerManager::getLogger()->warn(
                        self::class . '::' . __METHOD__
                        . ' call with more than 2 parameters is deprecated.'
                        . ' Please get all actions and specify in the caller which part is needed.'
                    );
                    return self::returnLegacyArray($user_id, $category, $type, $action);
                }
            }
        }
        //if we don't have it loaded then lets check against the db
        $overridden_actions = self::getOverriddenActions($user_id);
        $selected_actions = self::getAllActionsWithOverride($overridden_actions);
        if (!isset(self::$acls)) {
            self::$acls = [];
        }
        self::$acls[$user_id] = $selected_actions;
        self::fillEmptyLevels($selected_actions, $user_id, $category, $type, $action);

        self::storeToCache($user_id, 'acls', self::$acls[$user_id]);
        // Sort by translated categories
        uksort(self::$acls[$user_id], 'ACLAction::langCompare');
        return self::$acls[$user_id];
    }

    /**
     * @param string $user_id
     * @param string $category
     * @param string|null $type
     * @param string|null $action
     * @return array
     */
    private static function returnLegacyArray(string $user_id, string $category, ?string $type, ?string $action): array
    {
        if (!isset($action, $type)) {
            return self::$acls[$user_id][$category];
        }
        if (!isset($action)) {
            return self::$acls[$user_id][$category][$type] ?? [];
        }
        if (isset($type)) {
            return self::$acls[$user_id][$category][$type][$action] ?? [];
        }
        return [];
    }

    /**
     * @param array $selected_actions
     * @param string $user_id
     * @param string|null $category
     * @param string|null $type
     * @param STRING|null $action
     */
    private static function fillEmptyLevels(array $selected_actions, string $user_id, ?string $category, ?string $type, ?string $action): void
    {
        if ($category === null) {
            return;
        } else {
            if (!isset($selected_actions[$category])) {
                self::$acls[$user_id][$category] = [];
            }
            if ($type !== null) {
                if (!isset($selected_actions[$category][$type])) {
                    self::$acls[$user_id][$category][$type] = [];
                }
                if ($action !== null) {
                    if (!isset($selected_actions[$category][$action])) {
                        self::$acls[$user_id][$category][$type][$action] = [];
                    }
                }
            }
        }
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
     * Checks if a user has access to this acl if the user is an owner it will check if owners have access
     *
     * @param bool $is_owner
     * @param int $access
     * @return bool
     */
    public static function hasAccess(bool $is_owner = false, int $access = 0): bool
    {
        $tbaConfigurator = new TeamBasedACLConfigurator();
        if ($tbaConfigurator->isEnabledGlobally() && $tbaConfigurator->isValidAccess($access)) {
            // Handled by SugarACLTeamBased.
            return true;
        }

        return (($access !== 0 && $access === ACL_ALLOW_ALL) || ($is_owner && $access === ACL_ALLOW_OWNER));
    }

    /**
     * static function userHasAccess($user_id, $category, $action, $is_owner = false)
     *
     * @param GUID $user_id the user id who you want to check access for
     * @param STRING $category the category you would like to check access for
     * @param STRING $action the action of that category you would like to check access for
     * @param BOOLEAN OPTIONAL $is_owner if the object is owned by the user you are checking access for
     */
    public static function userHasAccess($user_id, $category, $action, $type = 'module', $is_owner = false)
    {
        global $current_user;
        //check if we don't have it set in the cache if not lets reload the cache
        if (ACLAction::getUserAccessLevel($user_id, $category, 'access', $type) < ACL_ALLOW_ENABLED) {
            return false;
        }
        if (empty(self::$acls[$user_id][$category][$type][$action])) {
            ACLAction::getUserActions($user_id, false);
        }
        if (!empty(self::$acls[$user_id][$category][$type][$action])) {
            if ($action == 'access' && self::$acls[$user_id][$category][$type][$action]['aclaccess'] == ACL_ALLOW_ENABLED) {
                return true;
            }
            return ACLAction::hasAccess($is_owner, self::$acls[$user_id][$category][$type][$action]['aclaccess']);
        }
        return false;
    }

    /**
     * function getUserAccessLevel($user_id, $category, $action,$type='module')
     * returns the access level for a given category and action
     *
     * @param GUID $user_id
     * @param STRING $category
     * @param STRING $action
     * @param STRING $type
     * @return INT (ACCESS LEVEL)
     */
    public static function getUserAccessLevel($user_id, $category, $action, $type = 'module')
    {
        if (empty(self::$acls[$user_id][$category][$type][$action])) {
            ACLAction::getUserActions($user_id, false);
        }
        if (!empty(self::$acls[$user_id][$category][$type][$action])) {
            $actionAccess = self::$acls[$user_id][$category][$type][$action]['aclaccess'];

            if (!empty(self::$acls[$user_id][$category][$type]['admin']) && self::$acls[$user_id][$category][$type]['admin']['aclaccess'] >= ACL_ALLOW_ADMIN) {
                $tbaConfigurator = new TeamBasedACLConfigurator();
                if ($tbaConfigurator->isValidAccess($actionAccess)) {
                    // The TBA is not suppressed by admin access.
                    return $actionAccess;
                }
                // If you have admin access for a module, all ACL's are allowed
                return self::$acls[$user_id][$category][$type]['admin']['aclaccess'];
            }
            return $actionAccess;
        }
    }

    /**
     * STATIC function userNeedsOwnership($user_id, $category, $action,$type='module')
     * checks if a user should have ownership to do an action
     *
     * @param GUID $user_id
     * @param STRING $category
     * @param STRING $action
     * @param STRING $type
     * @return boolean
     */
    public static function userNeedsOwnership($user_id, $category, $action, $type = 'module')
    {
        //check if we don't have it set in the cache if not lets reload the cache

        if (empty(self::$acls[$user_id][$category][$type][$action])) {
            ACLAction::getUserActions($user_id, false);
        }


        if (!empty(self::$acls[$user_id][$category][$type][$action])) {
            return self::$acls[$user_id][$category][$type][$action]['aclaccess'] == ACL_ALLOW_OWNER;
        }
        return false;
    }

    /**
     *
     * static pass by ref setupCategoriesMatrix(&$categories)
     * takes in an array of categories and modifes them adding display information
     *
     * @param unknown_type $categories
     */
    public static function setupCategoriesMatrix(&$categories)
    {
        global $ACLActions, $current_user;
        $names = [];
        $disabled = [];
        $tbaConfigurator = new TeamBasedACLConfigurator();
        foreach ($categories as $cat_name => $category) {
            foreach ($category as $type_name => $type) {
                foreach ($type as $act_name => $action) {
                    $names[$act_name] = isset($ACLActions[$type_name]['actions'][$act_name])
                        ? translate($ACLActions[$type_name]['actions'][$act_name]['label'], 'ACLActions')
                        : $act_name;
                    $categories[$cat_name][$type_name][$act_name]['accessColor'] = ACLAction::AccessColor($action['aclaccess']);
                    if ($type_name == 'module') {
                        if ($act_name != 'aclaccess' && $categories[$cat_name]['module']['access']['aclaccess'] == ACL_ALLOW_DISABLED) {
                            $categories[$cat_name][$type_name][$act_name]['accessColor'] = 'darkgray';
                            $disabled[] = $cat_name;
                        }
                    }
                    $categories[$cat_name][$type_name][$act_name]['accessName'] = ACLAction::AccessName($action['aclaccess']);
                    $categories[$cat_name][$type_name][$act_name]['accessLabel'] = ACLAction::AccessLabel($action['aclaccess']);

                    if ($cat_name == 'Users' && $act_name == 'admin') {
                        $categories[$cat_name][$type_name][$act_name]['accessOptions'][ACL_ALLOW_DEFAULT] = ACLAction::AccessName(ACL_ALLOW_DEFAULT);
                        ;
                        $categories[$cat_name][$type_name][$act_name]['accessOptions'][ACL_ALLOW_DEV] = ACLAction::AccessName(ACL_ALLOW_DEV);
                        ;
                        $categories[$cat_name][$type_name][$act_name]['accessOptions'][ACL_ALLOW_ADMIN_DEV] = ACLAction::AccessName(ACL_ALLOW_ADMIN_DEV);
                    } else {
                        $categories[$cat_name][$type_name][$act_name]['accessOptions'] = ACLAction::getAccessOptions($act_name, $type_name);
                        if (!$tbaConfigurator->isAccessibleForModule($cat_name)) {
                            $tbaModuleKeys = array_values($tbaConfigurator->getModuleOptions());
                            foreach ($categories[$cat_name][$type_name][$act_name]['accessOptions'] as $key => $label) {
                                if (in_array($key, $tbaModuleKeys)) {
                                    unset($categories[$cat_name][$type_name][$act_name]['accessOptions'][$key]);
                                }
                            }
                        }
                    }
                }
            }
        }

        if (!is_admin($current_user)) {
            foreach ($disabled as $cat_name) {
                unset($categories[$cat_name]);
            }
        }
        return $names;
    }

    /**
     * Extension of setupCategoriesMatrix function returning only accessName and accessLabel
     *
     * @param array $categories
     * @return array[]
     */
    public static function setupCategories(array &$categories)
    {
        global $ACLActions, $current_user;
        $names = [];
        $disabled = [];
        $access = [];
        $nameActions =[];
        foreach ($categories as $cat_name => $category) {
            foreach ($category as $type_name => $type) {
                foreach ($type as $act_name => $action) {
                    $names[$act_name] = isset($ACLActions[$type_name]['actions'][$act_name])
                        ? translate($ACLActions[$type_name]['actions'][$act_name]['label'], 'ACLActions')
                        : $act_name;
                    $access[$cat_name][$act_name]['accessName'] = ACLAction::AccessName($action['aclaccess']);
                    $access[$cat_name][$act_name]['accessLabel'] = ACLAction::AccessLabel($action['aclaccess']);
                }
            }
        }

        if (!is_admin($current_user)) {
            foreach ($disabled as $cat_name) {
                unset($categories[$cat_name]);
            }
        }

        foreach ($names as $key => $value) {
            $name = [];
            $name['key'] = $key;
            $name['value'] = $value;
            $nameActions[$key] = $name;
        }

        return [
            'categories' => $access,
            'names' => $nameActions,
        ];
    }

    /**
     * Getting list of overridden
     *
     * @param string $user_id User's id to get the list of overridden actions
     * @return array
     */
    private static function getOverriddenActions(string $user_id): array
    {
        $query = <<<SQL
SELECT
    acl_roles_users.user_id,
    acl_roles_actions.action_id,
    acl_roles_actions.access_override
FROM
    acl_roles_users
LEFT JOIN acl_roles_actions acl_roles_actions ON
    acl_roles_actions.role_id = acl_roles_users.role_id
    AND acl_roles_actions.deleted = 0
WHERE
    acl_roles_users.user_id = ?
    AND acl_roles_users.deleted = ?
SQL;
        $conn = DBManagerFactory::getInstance()->getConnection();
        $result = $conn->executeQuery(
            $query,
            [$user_id, 0]
        );
        $actions = [];
        foreach ($result->iterateAssociative() as $row) {
            $actions[] = $row;
        }
        return self::keepMostRestrictiveActions($actions);
    }

    private static function getAllActionsWithOverride(array $overridden_actions): array
    {
        $conn = DBManagerFactory::getInstance()->getConnection();
        $qb = $conn->createQueryBuilder();
        $qb->select(
            'acl_actions.id',
            'acl_actions.name',
            'acl_actions.category',
            'acl_actions.acltype',
            'acl_actions.aclaccess'
        )
            ->from('acl_actions')
            ->andWhere($qb->expr()->eq('acl_actions.deleted', 0));

        $stmt = $qb->execute();

        $selected_actions = [];
        while ($row = $stmt->fetchAssociative()) {
            $isOverride = !empty($overridden_actions[$row['id']]['access_override']);
            if ($isOverride) {
                $row['aclaccess'] = $overridden_actions[$row['id']]['access_override'];
            }
            $selected_actions = self::applyOverride($selected_actions, $row, $isOverride);
        }
        return $selected_actions;
    }

    private static function applyOverride(array $selected_actions, array $row, bool $isOverride): array
    {
        $category = $row['category'];
        $acltype = $row['acltype'];
        $name = $row['name'];
        $aclaccess = $row['aclaccess'];
        $id = $row['id'];
        $overriddenAction = [
            'id' => $id,
            'aclaccess' => $aclaccess,
            'isDefault' => !$isOverride,
        ];
        if (!isset($selected_actions[$category][$acltype][$name])) {
            $selected_actions[$category][$acltype][$name] = $overriddenAction;
        } else {
            $current_action = $selected_actions[$category][$acltype][$name];
            $isCurrentAccessHigher = $current_action['aclaccess'] > $aclaccess;
            if ($isOverride && ($isCurrentAccessHigher || $current_action['isDefault'])) {
                $selected_actions[$category][$acltype][$name] = $overriddenAction;
            }
        }
        return $selected_actions;
    }

    private static function keepMostRestrictiveActions(array $actions): array
    {
        $overridden_actions = [];
        foreach ($actions as $row) {
            if (!empty($row['access_override'])) {
                if (!isset($overridden_actions[$row['action_id']])) {
                    $overridden_actions[$row['action_id']] = $row;
                } else {
                    if ($overridden_actions[$row['action_id']]['access_override'] > $row['access_override']) {
                        $overridden_actions[$row['action_id']] = $row;
                    }
                }
            }
        }
        return $overridden_actions;
    }


    /**
     * @deprecated
     */
    public function clearSessionCache()
    {
        self::clearACLCache();
    }

    /**
     * function clearSessionCache()
     * clears the session variable storing the cache information for acls
     *
     */
    public static function clearACLCache()
    {
        self::$acls = [];
        Container::getInstance()->get(Cache::class)->clearAll();
    }

    public function save($check_notify = false)
    {
        self::clearACLCache();
        parent::save($check_notify);
    }

    public function mark_deleted($id)
    {
        self::clearACLCache();
        parent::mark_deleted($id);
    }

    /**
     * Check if there are any ACLs defined in this module for this user
     * @param string $user_id
     * @param string $module
     * @return boolean
     */
    public static function hasACLs($user_id, $module)
    {
        if ($user_id === null) {
            return false;
        }
        if (empty(self::$acls[$user_id])) {
            self::$acls[$user_id] = self::loadFromCache($user_id, 'acls');
        }
        return !empty(self::$acls[$user_id][$module]);
    }

    /**
     * Directly set ACL data. Useful mostly for unit tests.
     * @param string $user_id
     * @param string $module
     * @param array $data
     */
    public static function setACLData($user_id, $module, $data)
    {
        self::$acls[$user_id][$module] = $data;
    }
}
