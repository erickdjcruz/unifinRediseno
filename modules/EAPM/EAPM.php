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

use Sugarcrm\Sugarcrm\Util\Arrays\ArrayFunctions\ArrayFunctions;

class EAPM extends Basic
{
    public $new_schema = true;
    public $module_dir = 'EAPM';
    public $object_name = 'EAPM';
    public $table_name = 'eapm';
    public $importable = false;
    public $id;
    public $type;
    public $name;
    public $date_entered;
    public $date_modified;
    public $modified_user_id;
    public $modified_by_name;
    public $created_by;
    public $created_by_name;
    public $description;
    public $deleted;
    public $created_by_link;
    public $modified_user_link;
    public $assigned_user_id;
    public $assigned_user_name;
    public $assigned_user_link;
    public $password;
    public $url;
    public $validated = false;
    public $oauth_token;
    public $oauth_secret;
    public $application;
    public $consumer_key;
    public $consumer_secret;
    public $disable_row_level_security = true;
    public static $passwordPlaceholder = '::PASSWORD::';

    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }

    public static function getLoginInfo($application, $includeInactive = false)
    {
        global $current_user;

        $eapmBean = BeanFactory::newBean('EAPM');

        if (isset($_SESSION['EAPM'][$application]) && !$includeInactive) {
            if (ArrayFunctions::is_array_access($_SESSION['EAPM'][$application])) {
                $eapmBean->fromArray($_SESSION['EAPM'][$application]);
            } else {
                return null;
            }
        } else {
            $queryArray = ['assigned_user_id' => $current_user->id, 'application' => $application, 'deleted' => 0];
            if (!$includeInactive) {
                $queryArray['validated'] = 1;
            }
            $eapmBean = $eapmBean->retrieve_by_string_fields($queryArray, false);

            // Don't cache the include inactive results
            if (!$includeInactive) {
                if ($eapmBean != null) {
                    $_SESSION['EAPM'][$application] = $eapmBean->toArray();
                } else {
                    $_SESSION['EAPM'][$application] = '';
                    return null;
                }
            }
        }

        if (isset($eapmBean->password)) {
            $eapmBean->password = $eapmBean->decrypt_after_retrieve($eapmBean->password);
        }

        return $eapmBean;
    }

    public function create_new_list_query(
        $order_by,
        $where,
        $filter = [],
        $params = [],
        $show_deleted = 0,
        $join_type = '',
        $return_array = false,
        $parentbean = null,
        $singleSelect = false,
        $ifListForExport = false
    ) {

        global $current_user;

        if (!is_admin($GLOBALS['current_user'])) {
            // Restrict this so only admins can see other people's records
            $owner_where = $this->getOwnerWhere($current_user->id);

            if (empty($where)) {
                $where = $owner_where;
            } else {
                $where .= ' AND ' . $owner_where;
            }
        }

        return parent::create_new_list_query(
            $order_by,
            $where,
            $filter,
            $params,
            $show_deleted,
            $join_type,
            $return_array,
            $parentbean,
            $singleSelect,
            $ifListForExport
        );
    }

    public function save($check_notify = false)
    {
        $this->fillInName();
        if (empty($this->skipReassignment) && !is_admin($GLOBALS['current_user'])) {
            $this->assigned_user_id = $GLOBALS['current_user']->id;
        }

        if (!empty($this->password) && $this->password == static::$passwordPlaceholder) {
            $this->password = empty($this->fetched_row['password']) ? '' : $this->fetched_row['password'];
        }

        $parentRet = parent::save($check_notify);

        // Nuke the EAPM cache for this record
        if (isset($_SESSION['EAPM'][$this->application])) {
            unset($_SESSION['EAPM'][$this->application]);
        }

        // Nuke the Meetings type dropdown cache
        sugar_cache_clear('meetings_type_drop_down');

        return $parentRet;
    }

    public function mark_deleted($id)
    {
        // Nuke the EAPM cache for this record
        if (isset($_SESSION['EAPM'][$this->application])) {
            unset($_SESSION['EAPM'][$this->application]);
        }

        return parent::mark_deleted($id);
    }

    public function validated()
    {
        if (empty($this->id)) {
            return false;
        }
        // Don't use save, it will attempt to revalidate
        $db = DBManagerFactory::getInstance();
        $sql = sprintf(
            'UPDATE eapm SET validated=1, api_data=%s WHERE id=%s AND deleted=0',
            $db->quoted($this->api_data),
            $db->quoted($this->id)
        );
        $db->query($sql);
        if (!$this->deleted && !empty($this->application)) {
            // deactivate other EAPMs with same app
            $sql = sprintf(
                'UPDATE eapm SET deleted=1 WHERE application=%s AND id != %s AND deleted=0 AND assigned_user_id=%s',
                $db->quoted($this->application),
                $db->quoted($this->id),
                $db->quoted($this->assigned_user_id)
            );
            $db->query($sql, true);
        }

        // Nuke the EAPM cache for this record
        if (isset($_SESSION['EAPM'][$this->application])) {
            unset($_SESSION['EAPM'][$this->application]);
        }
    }

    protected function fillInName()
    {
        if (!empty($this->application)) {
            $apiList = ExternalAPIFactory::loadFullAPIList(false, true);
        }
        if (!empty($apiList) && isset($apiList[$this->application]) && $apiList[$this->application]['authMethod'] == 'oauth') {
            $this->name = sprintf(translate('LBL_OAUTH_NAME', $this->module_dir), $this->application);
        }
    }

    public function fill_in_additional_detail_fields()
    {
        $this->fillInName();
        parent::fill_in_additional_detail_fields();
    }

    public function fill_in_additional_list_fields()
    {
        $this->fillInName();
        parent::fill_in_additional_list_fields();
    }

    public function save_cleanup()
    {
        $this->oauth_token = '';
        $this->oauth_secret = '';
        $this->api_data = '';
    }

    /**
     * Given a user remove their associated accounts. This is called when a user is deleted from the system.
     * @param  $user_id
     * @return void
     */
    public function delete_user_accounts($user_id)
    {
        $db = DBManagerFactory::getInstance();
        $sql = sprintf(
            'DELETE FROM %s WHERE assigned_user_id = %s',
            $this->table_name,
            $db->quoted($user_id)
        );
        $db->query($sql, true);
    }
}

// External API integration, for the dropdown list of what external API's are available
function getEAPMExternalApiDropDown()
{
    $apiList = ExternalAPIFactory::getModuleDropDown('', true, true);

    // Reject Email Oauth connections
    $reject = ['GoogleEmail' => 0, 'MicrosoftEmail' => 1];
    $apiList = array_diff_key($apiList, $reject);
    return $apiList;
}
