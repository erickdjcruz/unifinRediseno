<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

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

use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Config as IdmConfig;

require_once 'service/core/SoapLogTrait.php';

global $disable_date_format;
$disable_date_format = true;

class SoapHelperWebServices
{
    use SoapLogTrait;

    /**
     * @var IdmConfig
     */
    protected $idmConfig = null;

    public function get_field_list($value, $fields, $translate = true)
    {
        $this->getLogger()->info('Begin: SoapHelperWebServices->get_field_list(' . print_r($value, true) . ', ' . print_r($fields, true) . ", $translate");
        $module_fields = [];
        $link_fields = [];
        if (!empty($value->field_defs)) {
            foreach ($value->field_defs as $var) {
                if (!empty($fields) && !safeInArray($var['name'], $fields)) {
                    continue;
                }
                if (isset($var['source']) && ($var['source'] != 'db' && $var['source'] != 'non-db' && $var['source'] != 'custom_fields') && $var['name'] != 'email1' && $var['name'] != 'email2' && (!isset($var['type']) || $var['type'] != 'relate') && !(isset($var['type']) && $var['type'] == 'id' && isset($var['link']))) {
                    continue;
                }
                if (isset($var['source']) && $var['source'] == 'non_db' && (isset($var['type']) && $var['type'] != 'link')) {
                    continue;
                }
                $required = 0;
                $options_dom = [];
                $options_ret = [];
                // Apparently the only purpose of this check is to make sure we only return fields
                //   when we've read a record.  Otherwise this function is identical to get_module_field_list

                if (isset($var['required']) && $var['required'] && $var['required'] !== 'false') {
                    $required = 1;
                }
                if (isset($var['options'])) {
                    $options_dom = translate($var['options'], $value->module_dir);
                    if (!is_array($options_dom)) {
                        $options_dom = [];
                    }
                    foreach ($options_dom as $key => $oneOption) {
                        $options_ret[$key] = $this->get_name_value($key, $oneOption);
                    }
                }

                if (!empty($var['dbType']) && $var['type'] == 'bool') {
                    $options_ret['type'] = $this->get_name_value('type', $var['dbType']);
                }

                $entry = [];
                $entry['name'] = $var['name'];
                $entry['type'] = $var['type'];
                if ($var['type'] == 'link') {
                    $entry['relationship'] = ($var['relationship'] ?? '');
                    $entry['module'] = ($var['module'] ?? '');
                    $entry['bean_name'] = ($var['bean_name'] ?? '');
                    $link_fields[$var['name']] = $entry;
                } else {
                    if ($translate) {
                        $entry['label'] = isset($var['vname']) ? translate($var['vname'], $value->module_dir) : $var['name'];
                    } else {
                        $entry['label'] = $var['vname'] ?? $var['name'];
                    }
                    $entry['required'] = $required;
                    $entry['options'] = $options_ret;
                    if (isset($var['default'])) {
                        $entry['default_value'] = $var['default'];
                    }
                    $module_fields[$var['name']] = $entry;
                } // else
            } //foreach
        } //if

        if ($value->module_dir == 'Bugs') {
            $seedRelease = BeanFactory::newBean('Releases');
            $options = $seedRelease->get_releases(true, 'Active');
            $options_ret = [];
            foreach ($options as $name => $value) {
                $options_ret[] = ['name' => $name, 'value' => $value];
            }
            if (isset($module_fields['fixed_in_release'])) {
                $module_fields['fixed_in_release']['type'] = 'enum';
                $module_fields['fixed_in_release']['options'] = $options_ret;
            }
            if (isset($module_fields['release'])) {
                $module_fields['release']['type'] = 'enum';
                $module_fields['release']['options'] = $options_ret;
            }
            if (isset($module_fields['release_name'])) {
                $module_fields['release_name']['type'] = 'enum';
                $module_fields['release_name']['options'] = $options_ret;
            }
        }

        if (isset($value->assigned_user_name) && isset($module_fields['assigned_user_id'])) {
            $module_fields['assigned_user_name'] = $module_fields['assigned_user_id'];
            $module_fields['assigned_user_name']['name'] = 'assigned_user_name';
        }
        if (isset($value->assigned_name) && isset($module_fields['team_id'])) {
            $module_fields['team_name'] = $module_fields['team_id'];
            $module_fields['team_name']['name'] = 'team_name';
        }
        if (isset($module_fields['modified_user_id'])) {
            $module_fields['modified_by_name'] = $module_fields['modified_user_id'];
            $module_fields['modified_by_name']['name'] = 'modified_by_name';
        }
        if (isset($module_fields['created_by'])) {
            $module_fields['created_by_name'] = $module_fields['created_by'];
            $module_fields['created_by_name']['name'] = 'created_by_name';
        }

        $return = ['module_fields' => $module_fields, 'link_fields' => $link_fields];
        $this->getLogger()->info('End: SoapHelperWebServices->get_field_list ->> ' . print_r($return, true));
        return $return;
    } // fn

    public function setFaultObject($errorObject)
    {
        if ($this->getLogger()->wouldLog('debug')) {
            $this->getLogger()->debug('SoapHelperWebServices->setFaultObject - ' . var_export($errorObject, true));
        }
        global $service_object;
        $service_object->error($errorObject);
    } // fn

    /**
     * Validate the user session based on user name and password hash.
     *
     * @param string $user_name -- The user name to create a session for
     * @param string $password -- The MD5 sum of the user's password
     * @return bool true -- If the session is created
     * @return bool false -- If the session is not created
     */
    public function validate_user($user_name, $password)
    {
        $this->getLogger()->info('Begin: SoapHelperWebServices->validate_user');
        global $server, $current_user;
        $user = BeanFactory::newBean('Users');
        $user->user_name = $user_name;
        $authController = AuthenticationController::getInstance();
        // Check to see if the user name and password are consistent.
        if ($user->authenticate_user($password)) {
            // we also need to set the current_user.
            $user->retrieve($user->id);
            $current_user = $user;
            $this->getLogger()->info('End: SoapHelperWebServices->validate_user - validation passed');
            return true;
        }

        $password = $this->decrypt_string($password);
        if ($authController->login($user_name, $password) && isset($_SESSION['authenticated_user_id'])) {
            $user->retrieve($_SESSION['authenticated_user_id']);
            $current_user = $user;
            $this->getLogger()->info('End: SoapHelperWebServices->validate_user - validation passed');
            return true;
        }

        $this->getLogger()->info("SECURITY: failed attempted login for $user_name using SOAP api");
        $server->setError('Invalid username and/or password');
        return false;
    }

    /**
     * Validate the provided session information is correct and current.  Load the session.
     *
     * @param String $session_id -- The session ID that was returned by a call to login.
     * @return true -- If the session is valid and loaded.
     * @return false -- if the session is not valid.
     */
    public function validate_authenticated($session_id)
    {
        $this->getLogger()->info('Begin: SoapHelperWebServices->validate_authenticated');
        if (!empty($session_id)) {
            // only initialize session once in case this method is called multiple times
            if (!session_id()) {
                session_id($session_id);
                session_start();
            }

            if (!empty($_SESSION['is_valid_session']) && $this->is_valid_ip_address('ip_address') && $_SESSION['type'] == 'user') {
                global $current_user;
                $current_user = BeanFactory::getBean('Users', $_SESSION['user_id']);
                $this->login_success();
                $this->getLogger()->info('Begin: SoapHelperWebServices->validate_authenticated - passed');
                $this->getLogger()->info('End: SoapHelperWebServices->validate_authenticated');
                return true;
            }

            $this->getLogger()->debug('calling destroy');
            session_destroy();
        }
        LogicHook::initialize();
        $GLOBALS['logic_hook']->call_custom_logic('Users', 'login_failed');
        $this->getLogger()->info('End: SoapHelperWebServices->validate_authenticated - validation failed');
        return false;
    }

    /**
     * Use the same logic as in SugarAuthenticate to validate the ip address
     *
     * @param string $session_var
     * @return bool - true if the ip address is valid, false otherwise.
     */
    public function is_valid_ip_address($session_var)
    {
        global $sugar_config;
        // grab client ip address
        $clientIP = query_client_ip();
        $classCheck = 0;
        // check to see if config entry is present, if not, verify client ip
        if (!isset($sugar_config['verify_client_ip']) || $sugar_config['verify_client_ip'] == true) {
            // check to see if we've got a current ip address in $_SESSION
            // and check to see if the session has been hijacked by a foreign ip
            if (isset($_SESSION[$session_var])) {
                $session_parts = explode('.', $_SESSION[$session_var]);
                $client_parts = explode('.', $clientIP);
                if (safeCount($session_parts) < 4) {
                    $classCheck = 0;
                } else {
                    // match class C IP addresses
                    for ($i = 0; $i < 3; $i++) {
                        if ($session_parts[$i] == $client_parts[$i]) {
                            $classCheck = 1;
                            continue;
                        } else {
                            $classCheck = 0;
                            break;
                        }
                    }
                }
                // we have a different IP address
                if ($_SESSION[$session_var] != $clientIP && empty($classCheck)) {
                    $this->getLogger()->fatal("IP Address mismatch: SESSION IP: {$_SESSION[$session_var]} CLIENT IP: {$clientIP}");
                    return false;
                }
            } else {
                return false;
            }
        }
        return true;
    }

    public function checkSessionAndModuleAccess($session, $login_error_key, $module_name, $access_level, $module_access_level_error_key, $errorObject)
    {
        $this->getLogger()->info('Begin: SoapHelperWebServices->checkSessionAndModuleAccess - ' . $module_name);
        if (!$this->validate_authenticated($session)) {
            $this->getLogger()->fatal('SoapHelperWebServices->checkSessionAndModuleAccess - validate_authenticated failed - ' . $module_name);
            $errorObject->set_error('invalid_session');
            $this->setFaultObject($errorObject);
            $this->getLogger()->info('End: SoapHelperWebServices->checkSessionAndModuleAccess -' . $module_name);
            return false;
        } // if

        global $beanList, $beanFiles;
        if (!empty($module_name)) {
            if (empty($beanList[$module_name])) {
                $this->getLogger()->fatal('SoapHelperWebServices->checkSessionAndModuleAccess - module does not exists - ' . $module_name);
                $errorObject->set_error('no_module');
                $this->setFaultObject($errorObject);
                $this->getLogger()->info('End: SoapHelperWebServices->checkSessionAndModuleAccess -' . $module_name);
                return false;
            } // if
            global $current_user;
            if (!$this->check_modules_access($current_user, $module_name, $access_level)) {
                $this->getLogger()->fatal('SoapHelperWebServices->checkSessionAndModuleAccess - no module access - ' . $module_name);
                $errorObject->set_error('no_access');
                $this->setFaultObject($errorObject);
                $this->getLogger()->info('End: SoapHelperWebServices->checkSessionAndModuleAccess - ' . $module_name);
                return false;
            }
        } // if
        $this->getLogger()->info('End: SoapHelperWebServices->checkSessionAndModuleAccess - ' . $module_name);
        return true;
    } // fn

    public function checkACLAccess($bean, $viewType, $errorObject, $error_key)
    {
        $this->getLogger()->info('Begin: SoapHelperWebServices->checkACLAccess');
        if (!$bean->ACLAccess($viewType)) {
            $this->getLogger()->fatal('SoapHelperWebServices->checkACLAccess - no ACLAccess');
            $errorObject->set_error($error_key);
            $this->setFaultObject($errorObject);
            $this->getLogger()->info('End: SoapHelperWebServices->checkACLAccess');
            return false;
        } // if
        $this->getLogger()->info('End: SoapHelperWebServices->checkACLAccess');
        return true;
    } // fn

    public function checkQuery($errorObject, $query, $order_by = '')
    {
        $valid = new SugarSQLValidate();
        if (!$valid->validateQueryClauses($query, $order_by)) {
            $this->getLogger()->fatal("SoapHelperWebServices->checkQuery - bad query: $query $order_by");
            $errorObject->set_error('no_access');
            $this->setFaultObject($errorObject);
            return false;
        }
        return true;
    }

    public function get_name_value($field, $value)
    {
        return ['name' => $field, 'value' => $value];
    }

    public function get_user_module_list($user)
    {
        $this->getLogger()->info('Begin: SoapHelperWebServices->get_user_module_list');
        global $moduleList;
        $modules = array_flip(SugarACL::filterModuleList($moduleList, 'access', true)); // module names end up as keys
        global $modInvisList;

        foreach ($modInvisList as $invis) {
            $modules[$invis] = 'read_only';
        }

        foreach ($modules as $key => $val) {
            if (!SugarACL::checkAccess($key, 'edit', ['owner_override' => true])) {
                // not accessible for write
                $modules[$key] = 'read_only';
            } else {
                // access ok
                if ($modules[$key] != 'read_only') {
                    $modules[$key] = '';
                }
            }
        }
        $this->getLogger()->info('End: SoapHelperWebServices->get_user_module_list');
        return $modules;
    }

    public function check_modules_access($user, $module_name, $action = 'write')
    {
        $this->getLogger()->info('Begin: SoapHelperWebServices->check_modules_access');
        if (!isset($_SESSION['avail_modules'])) {
            $_SESSION['avail_modules'] = $this->get_user_module_list($user);
        }
        if (isset($_SESSION['avail_modules'][$module_name])) {
            if ($action == 'write' && $_SESSION['avail_modules'][$module_name] == 'read_only') {
                if (is_admin($user)) {
                    $this->getLogger()->info('End: SoapHelperWebServices->check_modules_access - SUCCESS: Admin can even write to read_only module');
                    return true;
                } // if
                $this->getLogger()->info('End: SoapHelperWebServices->check_modules_access - FAILED: write action on read_only module only available to admins');
                return false;
            } elseif ($action == 'write' && strcmp(strtolower($module_name), 'users') == 0 && !$user->isAdminForModule($module_name)) {
                //rrs bug: 46000 - If the client is trying to write to the Users module and is not an admin then we need to stop them
                return false;
            }
            $this->getLogger()->info('End: SoapHelperWebServices->check_modules_access - SUCCESS');
            return true;
        }
        $this->getLogger()->info('End: SoapHelperWebServices->check_modules_access - FAILED: Module info not available in $_SESSION');
        return false;
    }


    public function get_name_value_list($value)
    {
        $this->getLogger()->info('Begin: SoapHelperWebServices->get_name_value_list');
        global $app_list_strings;
        $list = [];
        if (!empty($value->field_defs)) {
            if (isset($value->assigned_user_name)) {
                $list['assigned_user_name'] = $this->get_name_value('assigned_user_name', $value->assigned_user_name);
            }
            if (isset($value->assigned_name)) {
                $list['team_name'] = $this->get_name_value('team_name', $value->assigned_name);
            }
            if (isset($value->modified_by_name)) {
                $list['modified_by_name'] = $this->get_name_value('modified_by_name', $value->modified_by_name);
            }
            if (isset($value->created_by_name)) {
                $list['created_by_name'] = $this->get_name_value('created_by_name', $value->created_by_name);
            }
            foreach ($value->field_defs as $var) {
                if (isset($var['source']) && ($var['source'] != 'db' && $var['source'] != 'custom_fields') && $var['name'] != 'email1' && $var['name'] != 'email2' && (!isset($var['type']) || $var['type'] != 'relate')) {
                    continue;
                }

                if (isset($value->{$var['name']})) {
                    $val = $value->{$var['name']};
                    $type = $var['type'];

                    if (strcmp($type, 'date') == 0) {
                        $val = substr($val, 0, 10);
                    } elseif (strcmp($type, 'enum') == 0 && !empty($var['options'])) {
                        //$val = $app_list_strings[$var['options']][$val];
                    }

                    $list[$var['name']] = $this->get_name_value($var['name'], $val);
                }
            }
        }
        $this->getLogger()->info('End: SoapHelperWebServices->get_name_value_list');
        return $list;
    }

    public function filter_fields($value, $fields)
    {
        $this->getLogger()->info('Begin: SoapHelperWebServices->filter_fields');
        global $invalid_contact_fields;
        $filterFields = [];
        foreach ($fields as $field) {
            if (is_array($invalid_contact_fields)) {
                if (safeInArray($field, $invalid_contact_fields)) {
                    continue;
                } // if
            } // if
            if (isset($value->field_defs[$field])) {
                $var = $value->field_defs[$field];
                if (isset($var['source']) && ($var['source'] != 'db' && $var['source'] != 'custom_fields') && $var['name'] != 'email1' && $var['name'] != 'email2' && (!isset($var['type']) || $var['type'] != 'relate') && !(isset($var['type']) && $var['type'] == 'id' && isset($var['link']))) {
                    if ($value->module_dir == 'Emails' && (($var['name'] == 'description') || ($var['name'] == 'description_html') || ($var['name'] == 'from_addr_name') || ($var['name'] == 'reply_to_addr') || ($var['name'] == 'to_addrs_names') || ($var['name'] == 'cc_addrs_names') || ($var['name'] == 'bcc_addrs_names') || ($var['name'] == 'raw_source'))) {
                    } else {
                        continue;
                    }
                }
            } // if
            $filterFields[] = $field;
        } // foreach
        $this->getLogger()->info('End: SoapHelperWebServices->filter_fields');
        return $filterFields;
    } // fn

    public function get_name_value_list_for_fields($value, $fields)
    {
        $this->getLogger()->info('Begin: SoapHelperWebServices->get_name_value_list_for_fields');
        global $app_list_strings;
        global $invalid_contact_fields;

        $list = [];
        if (!empty($value->field_defs)) {
            if (empty($fields)) {
                $fields = array_keys($value->field_defs);
            }
            if (isset($value->assigned_user_name) && safeInArray('assigned_user_name', $fields)) {
                $list['assigned_user_name'] = $this->get_name_value('assigned_user_name', $value->assigned_user_name);
            }
            if (isset($value->assigned_name) && safeInArray('assigned_name', $fields)) {
                $list['team_name'] = $this->get_name_value('team_name', $value->assigned_name);
            }
            if (isset($value->modified_by_name) && safeInArray('modified_by_name', $fields)) {
                $list['modified_by_name'] = $this->get_name_value('modified_by_name', $value->modified_by_name);
            }
            if (isset($value->created_by_name) && safeInArray('created_by_name', $fields)) {
                $list['created_by_name'] = $this->get_name_value('created_by_name', $value->created_by_name);
            }

            $filterFields = $this->filter_fields($value, $fields);

            //now check field level acl's if this bean implements them
            if ($value->bean_implements('ACL') && !empty($GLOBALS['current_user'])) {
                $filterFields = $this->returnFieldsWithAccess($value, $filterFields);
            }

            foreach ($filterFields as $field) {
                $var = $value->field_defs[$field] ?? null;
                if (isset($var['name']) && isset($value->{$var['name']})) {
                    $val = $value->{$var['name']};
                    $type = $var['type'];

                    if (strcmp($type, 'date') == 0) {
                        $val = substr($val, 0, 10);
                    } elseif (strcmp($type, 'enum') == 0 && !empty($var['options'])) {
                        //$val = $app_list_strings[$var['options']][$val];
                    }
                    if ($type == 'encrypt' && !empty($var['write_only'])) {
                        $val = !empty($val);
                    }

                    $list[$var['name']] = $this->get_name_value($var['name'], $val);
                } // if
            } // foreach
        } // if
        $this->getLogger()->info('End: SoapHelperWebServices->get_name_value_list_for_fields');
        if ($this->getLogger()->wouldLog('debug')) {
            $this->getLogger()->debug('SoapHelperWebServices->get_name_value_list_for_fields - return data = ' . var_export($list, true));
        } // if
        return $list;
    } // fn

    public function array_get_name_value_list($array)
    {
        $this->getLogger()->info('Begin: SoapHelperWebServices->array_get_name_value_list');
        $list = [];
        foreach ($array as $name => $value) {
            $list[$name] = $this->get_name_value($name, $value);
        }
        $this->getLogger()->info('End: SoapHelperWebServices->array_get_name_value_list');
        return $list;
    }

    public function array_get_name_value_lists($array)
    {
        $this->getLogger()->info('Begin: SoapHelperWebServices->array_get_name_value_lists');
        $list = [];
        foreach ($array as $name => $value) {
            $tmp_value = $value;
            if (is_array($value)) {
                $tmp_value = [];
                foreach ($value as $k => $v) {
                    $tmp_value[$k] = $this->get_name_value($k, $v);
                }
            }
            $list[$name] = $this->get_name_value($name, $tmp_value);
        }
        $this->getLogger()->info('End: SoapHelperWebServices->array_get_name_value_lists');
        return $list;
    }

    public function name_value_lists_get_array($list)
    {
        $this->getLogger()->info('Begin: SoapHelperWebServices->name_value_lists_get_array');
        $array = [];
        foreach ($list as $key => $value) {
            if (isset($value['value']) && isset($value['name'])) {
                if (is_array($value['value'])) {
                    $array[$value['name']] = [];
                    foreach ($value['value'] as $v) {
                        $array[$value['name']][$v['name']] = $v['value'];
                    }
                } else {
                    $array[$value['name']] = $value['value'];
                }
            }
        }
        $this->getLogger()->info('End: SoapHelperWebServices->name_value_lists_get_array');
        return $array;
    }

    public function array_get_return_value($array, $module)
    {

        $this->getLogger()->info('Begin/End: SoapHelperWebServices->array_get_return_value');
        return ['id' => $array['id'],
            'module_name' => $module,
            'name_value_list' => $this->array_get_name_value_list($array),
        ];
    }

    public function get_return_value_for_fields($value, $module, $fields)
    {
        $this->getLogger()->info('Begin: SoapHelperWebServices->get_return_value_for_fields');
        global $module_name, $current_user;
        $module_name = $module;
        if ($module == 'Users' && $value->id != $current_user->id) {
            $value->user_hash = '';
        }
        $value = clean_sensitive_data($value->field_defs, $value);
        $this->getLogger()->info('End: SoapHelperWebServices->get_return_value_for_fields');
        return ['id' => $value->id,
            'module_name' => $module,
            'name_value_list' => $this->get_name_value_list_for_fields($value, $fields),
        ];
    }

    /**
     * Fetch and array of related records
     *
     * @param String $bean -- Primary record
     * @param String $link_field_name -- The name of the relationship
     * @param Array $link_module_fields -- The keys of the array are the SugarBean attributes, the values of the array are the values the attributes should have.
     * @param String $optional_where -- IGNORED
     * @return Array 'rows/fields_set_on_rows' -- The list of records and what fields were actually set for thos erecords
     */

    public function getRelationshipResults($bean, $link_field_name, $link_module_fields, $optional_where = '')
    {
        $this->getLogger()->info('Begin: SoapHelperWebServices->getRelationshipResults');
        global $current_user, $disable_date_format, $timedate;

        $bean->load_relationship($link_field_name);
        if (isset($bean->$link_field_name)) {
            $params = [];
            if (!empty($optional_where)) {
                $params['where'] = $optional_where;
            }

            //First get all the related beans
            $related_beans = $bean->$link_field_name->getBeans($params);

            if (isset($related_beans[0])) {
                // use first bean to filter fields since all records have same module
                // and  $this->filter_fields doesn't use ACLs
                $filterFields = $this->filter_fields($related_beans[0], $link_module_fields);
            } else {
                $filterFields = $this->filter_fields(null, $link_module_fields);
            }

            $list = [];
            foreach ($related_beans as $id => $bean) {
                $row = [];
                //Create a list of field/value rows based on $link_module_fields

                foreach ($filterFields as $field) {
                    if (isset($bean->$field)) {
                        if (isset($bean->field_defs[$field]['type']) && $bean->field_defs[$field]['type'] == 'date') {
                            $row[$field] = $timedate->to_display_date_time($bean->$field);
                        }
                        $row[$field] = $bean->$field;
                    } else {
                        $row[$field] = '';
                    }
                }
                //Users can't see other user's hashes
                if (is_a($bean, 'User') && $current_user->id != $bean->id && isset($row['user_hash'])) {
                    $row['user_hash'] = '';
                }
                $row = clean_sensitive_data($bean->field_defs, $row);
                $list[] = $row;
            }
            $this->getLogger()->info('End: SoapHelperWebServices->getRelationshipResults');
            return ['rows' => $list, 'fields_set_on_rows' => $filterFields];
        } else {
            $this->getLogger()->info('End: SoapHelperWebServices->getRelationshipResults - ' . $link_field_name . ' relationship does not exists');
            return false;
        } // else
    } // fn

    public function get_return_value_for_link_fields($bean, $module, $link_name_to_value_fields_array)
    {
        $this->getLogger()->info('Begin: SoapHelperWebServices->get_return_value_for_link_fields');
        global $module_name, $current_user;
        $module_name = $module;
        if ($module == 'Users' && $bean->id != $current_user->id) {
            $bean->user_hash = '';
        }
        $bean = clean_sensitive_data($bean->field_defs, $bean);

        if (empty($link_name_to_value_fields_array) || !is_array($link_name_to_value_fields_array)) {
            $this->getLogger()->debug('End: SoapHelperWebServices->get_return_value_for_link_fields - Invalid link information passed ');
            return [];
        }

        if ($this->getLogger()->wouldLog('debug')) {
            $this->getLogger()->debug('SoapHelperWebServices->get_return_value_for_link_fields - link info = ' . var_export($link_name_to_value_fields_array, true));
        } // if
        $link_output = [];
        foreach ($link_name_to_value_fields_array as $link_name_value_fields) {
            if (!is_array($link_name_value_fields) || !isset($link_name_value_fields['name']) || !isset($link_name_value_fields['value'])) {
                continue;
            }
            $link_field_name = $link_name_value_fields['name'];
            $link_module_fields = $link_name_value_fields['value'];
            if (is_array($link_module_fields) && !empty($link_module_fields)) {
                $result = $this->getRelationshipResults($bean, $link_field_name, $link_module_fields);
                if (!$result) {
                    $link_output[] = ['name' => $link_field_name, 'records' => []];
                    continue;
                }
                $list = $result['rows'];
                $filterFields = $result['fields_set_on_rows'];
                if ($list) {
                    $rowArray = [];
                    foreach ($list as $row) {
                        $nameValueArray = [];
                        foreach ($filterFields as $field) {
                            $nameValue = [];
                            if (isset($row[$field])) {
                                $nameValueArray[$field] = $this->get_name_value($field, $row[$field]);
                            } // if
                        } // foreach
                        $rowArray[] = $nameValueArray;
                    } // foreach
                    $link_output[] = ['name' => $link_field_name, 'records' => $rowArray];
                } // if
            } // if
        } // foreach
        $this->getLogger()->debug('End: SoapHelperWebServices->get_return_value_for_link_fields');
        if ($this->getLogger()->wouldLog('debug')) {
            $this->getLogger()->debug('SoapHelperWebServices->get_return_value_for_link_fields - output = ' . var_export($link_output, true));
        } // if
        return $link_output;
    } // fn

    /**
     *
     * @param String $module_name -- The name of the module that the primary record is from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method).
     * @param String $module_id -- The ID of the bean in the specified module
     * @param String $link_field_name - The relationship name for which to create realtionships.
     * @param Array $related_ids -- The array of ids for which we want to create relationships
     * @param Array $name_value_list -- The array of name value pair of additional attributes to be set when adding this relationship
     * @param int delete -- If 0 then add relationship else delete this relationship data
     * @return true on success, false on failure
     */
    public function new_handle_set_relationship($module_name, $module_id, $link_field_name, $related_ids, $name_value_list, $delete)
    {
        $this->getLogger()->info('Begin: SoapHelperWebServices->new_handle_set_relationship');
        global $beanList, $beanFiles;

        if (empty($beanList[$module_name])) {
            $this->getLogger()->debug('SoapHelperWebServices->new_handle_set_relationship - module ' . $module_name . ' does not exists');
            $this->getLogger()->info('End: SoapHelperWebServices->new_handle_set_relationship');
            return false;
        } // if
        $mod = BeanFactory::getBean($module_name, $module_id);
        if (empty($mod->id)) {
            $this->getLogger()->info('End: SoapHelperWebServices->new_handle_set_relationship');
            return false;
        }
        if (!$mod->ACLAccess('DetailView')) {
            $this->getLogger()->info('End: SoapHelperWebServices->new_handle_set_relationship');
            return false;
        }

        if ($mod->load_relationship($link_field_name)) {
            if (!$delete) {
                $name_value_pair = [];
                if (!empty($name_value_list)) {
                    $relFields = $mod->$link_field_name->getRelatedFields();
                    if (!empty($relFields)) {
                        $relFieldsKeys = array_keys($relFields);
                        foreach ($name_value_list as $key => $value) {
                            if (in_array($value['name'], $relFieldsKeys)) {
                                $name_value_pair[$value['name']] = $value['value'];
                            } // if
                        } // foreach
                    } // if
                }
                $mod->$link_field_name->add($related_ids, $name_value_pair);
            } else {
                foreach ($related_ids as $id) {
                    $mod->$link_field_name->delete($module_id, $id);
                } // foreach
            } // else
            $this->getLogger()->info('End: SoapHelperWebServices->new_handle_set_relationship');
            return true;
        } else {
            $this->getLogger()->info('End: SoapHelperWebServices->new_handle_set_relationship');
            return false;
        }
    }

    public function new_handle_set_entries($module_name, $name_value_lists, $select_fields = false)
    {
        $this->getLogger()->info('Begin: SoapHelperWebServices->new_handle_set_entries');
        global $current_user, $app_list_strings;

        $ret_values = [];

        $ids = [];
        $count = 1;
        $total = sizeof($name_value_lists);
        foreach ($name_value_lists as $name_value_list) {
            $seed = BeanFactory::newBean($module_name);
            $seed->update_vcal = false;
            foreach ($name_value_list as $value) {
                if ($value['name'] == 'id') {
                    $seed->retrieve($value['value']);
                    break;
                }
            }

            if ($this->isIDMMode() && $this->isIDMModeModule($module_name) && !$seed->isUpdate()) {
                continue;
            }

            foreach ($name_value_list as $value) {
                $val = $value['value'];
                if (is_array($seed->field_defs) && isset($value['name'])
                    && isset($seed->field_defs[$value['name']])
                    && is_array($seed->field_defs[$value['name']]) &&
                    $seed->field_defs[$value['name']]['type'] == 'enum') {
                    $vardef = $seed->field_defs[$value['name']];
                    if (isset($app_list_strings[$vardef['options']]) && !isset($app_list_strings[$vardef['options']][$value])) {
                        if (safeInArray($val, $app_list_strings[$vardef['options']])) {
                            $val = array_search($val, $app_list_strings[$vardef['options']]);
                        }
                    }
                }
                if ($module_name == 'Users' && !empty($seed->id) && ($seed->id != $current_user->id) && $value['name'] == 'user_hash') {
                    continue;
                }
                if (!empty($seed->field_defs[$value['name']]['sensitive'])) {
                    continue;
                }

                if ($this->isIDMMode()
                    && $this->isIDMModeModule($module_name)
                    && $this->isIDMModeField($value['name'])) {
                    continue;
                }

                $seed->{$value['name']} = $val;
            }

            if ($count == $total) {
                $seed->update_vcal = false;
            }
            $count++;

            //Add the account to a contact
            if ($module_name == 'Contacts') {
                $this->getLogger()->debug('Creating Contact Account');
                $this->add_create_account($seed);
                $duplicate_id = $this->check_for_duplicate_contacts($seed);
                if ($duplicate_id == null) {
                    if ($seed->ACLAccess('Save') && ($seed->deleted != 1 || $seed->ACLAccess('Delete'))) {
                        $seed->save();
                        if ($seed->deleted == 1) {
                            $seed->mark_deleted($seed->id);
                        }
                        $ids[] = $seed->id;
                    }
                } else {
                    //since we found a duplicate we should set the sync flag
                    if ($seed->ACLAccess('Save')) {
                        $seed = $seed->getCleanCopy();
                        $seed->id = $duplicate_id;
                        $seed->contacts_users_id = $current_user->id;
                        $seed->save();
                        $ids[] = $duplicate_id;//we have a conflict
                    }
                }
            } elseif ($module_name == 'Meetings' || $module_name == 'Calls') {
                //we are going to check if we have a meeting in the system
                //with the same outlook_id. If we do find one then we will grab that
                //id and save it
                if ($seed->ACLAccess('Save') && ($seed->deleted != 1 || $seed->ACLAccess('Delete'))) {
                    if (empty($seed->id) && !isset($seed->id)) {
                        if (!empty($seed->outlook_id) && isset($seed->outlook_id)) {
                            //at this point we have an object that does not have
                            //the id set, but does have the outlook_id set
                            //so we need to query the db to find if we already
                            //have an object with this outlook_id, if we do
                            //then we can set the id, otherwise this is a new object
                            $order_by = '';
                            $query = $seed->table_name . ".outlook_id = '" . $GLOBALS['db']->quote($seed->outlook_id) . "'";
                            $response = $seed->get_list($order_by, $query, 0, -1, -1, 0);
                            $list = $response['list'];
                            if (safeCount($list) > 0) {
                                foreach ($list as $value) {
                                    $seed->id = $value->id;
                                    break;
                                }
                            }//fi
                        }//fi
                    }//fi
                    if (empty($seed->reminder_time)) {
                        $seed->reminder_time = -1;
                    }
                    if ($seed->reminder_time == -1) {
                        $defaultRemindrTime = $current_user->getPreference('reminder_time');
                        if ($defaultRemindrTime != -1) {
                            $seed->reminder_checked = '1';
                            $seed->reminder_time = $defaultRemindrTime;
                        }
                    }
                    $seed->save();
                    if ($seed->deleted == 1) {
                        $seed->mark_deleted($seed->id);
                    }
                    $ids[] = $seed->id;
                }//fi
            } else {
                if ($seed->ACLAccess('Save') && ($seed->deleted != 1 || $seed->ACLAccess('Delete'))) {
                    $seed->save();
                    $ids[] = $seed->id;
                }
            }

            // if somebody is calling set_entries_detail() and wants fields returned...
            if ($select_fields !== false) {
                $ret_values[$count] = [];

                foreach ($select_fields as $select_field) {
                    if (isset($seed->$select_field)) {
                        $ret_values[$count][$select_field] = $this->get_name_value($select_field, $seed->$select_field);
                    }
                }
            }
        }

        // handle returns for set_entries_detail() and set_entries()
        if ($select_fields !== false) {
            $this->getLogger()->info('End: SoapHelperWebServices->new_handle_set_entries');
            return [
                'name_value_lists' => $ret_values,
            ];
        } else {
            $this->getLogger()->info('End: SoapHelperWebServices->new_handle_set_entries');
            return [
                'ids' => $ids,
            ];
        }
    }

    public function get_return_value($value, $module)
    {
        $this->getLogger()->info('Begin: SoapHelperWebServices->get_return_value');
        global $module_name, $current_user;
        $module_name = $module;
        if ($module == 'Users' && $value->id != $current_user->id) {
            $value->user_hash = '';
        }
        $value = clean_sensitive_data($value->field_defs, $value);
        $this->getLogger()->info('End: SoapHelperWebServices->new_handle_set_entries');
        return ['id' => $value->id,
            'module_name' => $module,
            'name_value_list' => $this->get_name_value_list($value),
        ];
    }

    /**
     * Return the data from a report
     *
     * @param SavedReport $seed
     * @return array - output_list - the rows of the reports, field_list - the fields in the report.
     */
    public function get_report_value($seed, $select_fields)
    {
        $result = [];
        $this->getLogger()->info('Begin: SoapHelperWebServices->get_report_value');
        $field_list = [];
        $output_list = [];
        $report = new Report(html_entity_decode($seed->content, ENT_COMPAT), '', '');
        $report->enable_paging = false; //set paging = false for report.

        $next_row_fn = 'get_next_row';
        $report->plain_text_output = true;
        if ($report->report_type == 'summary') {
            $report->run_summary_query();
            $header = $report->get_summary_header_row();
            $next_row_fn = 'get_summary_next_row';
        } else {
            $report->run_query();
            $header = $report->get_header_row();
        }

        foreach ($header as $key => $value) {
            $field_list[$key] = ['name' => $value,
                'type' => 'header',
                'label' => $value,
                'required' => '0',
                'options' => [],
            ];
        }

        $index = 0;
        $column_types = 'display_columns';
        if ($report->report_type == 'summary') {
            $column_types = 'summary_columns';
        } // if
        while (($row = $report->$next_row_fn('result', $column_types)) != 0) {
            $row_list = ['id' => $index,
                'module_name' => 'Reports',
                'name_value_list' => [],
            ];

            foreach ($row['cells'] as $key => $value) {
                $this->getLogger()->info('SoapHelperWebServices->get_report_value name = ' . $key . '  value = ' . $value);

                if (!empty($select_fields) && is_array($select_fields) && safeInArray($key, $select_fields)) {
                    if (safeInArray($key, $select_fields)) {
                        $row_list['name_value_list'][$key] = $this->get_name_value($key, $value);
                    } // if
                } else {
                    $row_list['name_value_list'][$key] = $this->get_name_value($key, $value);
                } // else
            } // foreach
            $output_list[$index] = $row_list;
            $index++;
            $output_list[] = $row;
        } // while

        $result['output_list'] = $output_list;
        $result['field_list'] = $field_list;
        $this->getLogger()->info('End: SoapHelperWebServices->get_report_value');
        return $result;
    } // fn

    public function get_return_module_fields($value, $module, $fields, $translate = true)
    {
        $this->getLogger()->info('Begin: SoapHelperWebServices->get_return_module_fields');
        global $module_name;
        $module_name = $module;
        $result = $this->get_field_list($value, $fields, $translate);
        $this->getLogger()->info('End: SoapHelperWebServices->get_return_module_fields');
        return ['module_name' => $module,
            'module_fields' => $result['module_fields'],
            'link_fields' => $result['link_fields'],
        ];
    } // fn

    public function login_success($name_value_list = [])
    {
        $this->getLogger()->info('Begin: SoapHelperWebServices->login_success');
        global $current_language, $sugar_config, $app_strings, $app_list_strings;
        $current_language = $sugar_config['default_language'];
        if (is_array($name_value_list) && !empty($name_value_list)) {
            foreach ($name_value_list as $key => $value) {
                if (isset($value['name']) && ($value['name'] == 'language')) {
                    $language = $value['value'];
                    $supportedLanguages = $sugar_config['languages'];
                    if (array_key_exists($language, $supportedLanguages)) {
                        $current_language = $language;
                    } // if
                } // if
                if (isset($value['name']) && ($value['name'] == 'notifyonsave')) {
                    if ($value['value']) {
                        $_SESSION['notifyonsave'] = true;
                    }
                } // if
            } // foreach
        } else {
            if (isset($_SESSION['user_language'])) {
                $current_language = $_SESSION['user_language'];
            } // if
        }
        $this->getLogger()->info('Users language is = ' . $current_language);
        $app_strings = return_application_language($current_language);
        $app_list_strings = return_app_list_strings_language($current_language);
        $GLOBALS['logic_hook']->call_custom_logic('', 'after_load_user');
        // Reset ACLs in case after_load_user hook changed ACL setups
        SugarACL::resetACLs();
        $this->getLogger()->info('End: SoapHelperWebServices->login_success');
    } // fn


    public function checkSaveOnNotify()
    {
        $notifyonsave = false;
        if (isset($_SESSION['notifyonsave']) && $_SESSION['notifyonsave'] == true) {
            $notifyonsave = true;
        } // if
        return $notifyonsave;
    }

    /*
     *	Given an account_name, either create the account or assign to a contact.
     */
    public function add_create_account($seed)
    {
        $this->getLogger()->info('Begin: SoapHelperWebServices->add_create_account');
        global $current_user;
        $account_name = $seed->account_name;
        $account_id = $seed->account_id;
        $assigned_user_id = $current_user->id;

        // check if it already exists
        $focus = BeanFactory::newBean('Accounts');
        if ($focus->ACLAccess('Save')) {
            if (empty($account_name) && empty($account_id)) {
                return;
            } // if
            $temp = BeanFactory::getBean($seed->module_dir, $seed->id);
            if (!isset($seed->accounts)) {
                $seed->load_relationship('accounts');
            } // if

            if ($seed->account_name == '' && isset($temp->account_id)) {
                $seed->accounts->delete($seed->id, $temp->account_id);
                $this->getLogger()->info('End: SoapHelperWebServices->add_create_account');
                return;
            }
            $arr = [];

            if (!empty($account_id)) {  // bug # 44280
                $query = "select id, deleted from {$focus->table_name} WHERE id='" . $seed->db->quote($account_id) . "'";
            } else {
                $query = "select id, deleted from {$focus->table_name} WHERE name='" . $seed->db->quote($account_name) . "'";
            }
            $result = $seed->db->query($query, true);

            $row = $seed->db->fetchByAssoc($result, false);

            // we found a row with that id
            if (isset($row['id']) && $row['id'] != -1) {
                // if it exists but was deleted, just remove it entirely
                if (isset($row['deleted']) && $row['deleted'] == 1) {
                    $query2 = "delete from {$focus->table_name} WHERE id='" . $seed->db->quote($row['id']) . "'";
                    $result2 = $seed->db->query($query2, true);
                } // else just use this id to link the contact to the account
                else {
                    $focus->id = $row['id'];
                }
            }

            // if we didnt find the account, so create it
            if (!isset($focus->id) || $focus->id == '') {
                $focus->name = $account_name;

                if (isset($assigned_user_id)) {
                    $focus->assigned_user_id = $assigned_user_id;
                    $focus->modified_user_id = $assigned_user_id;
                }
                $focus->save();
            }

            if ($seed->accounts != null && $temp->account_id != null && $temp->account_id != $focus->id) {
                $seed->accounts->delete($seed->id, $temp->account_id);
            }

            if (isset($focus->id) && $focus->id != '') {
                $seed->account_id = $focus->id;
            } // if
            $this->getLogger()->info('End: SoapHelperWebServices->add_create_account');
        } else {
            $this->getLogger()->info('End: SoapHelperWebServices->add_create_account - Insufficient ACLAccess');
        } // else
    } // fn

    public function check_for_duplicate_contacts($seed)
    {
        $this->getLogger()->info('Begin: SoapHelperWebServices->check_for_duplicate_contacts');

        if (isset($seed->id)) {
            $this->getLogger()->info('End: SoapHelperWebServices->check_for_duplicate_contacts - no duplicte found');
            return null;
        }

        $query = '';

        $trimmed_email = trim((string)$seed->email1);
        $trimmed_email2 = trim((string)$seed->email2);
        $trimmed_last = trim((string)$seed->last_name);
        $trimmed_first = trim((string)$seed->first_name);
        if (!empty($trimmed_email) || !empty($trimmed_email2)) {
            //obtain a list of contacts which contain the same email address
            $contacts = $seed->emailAddress->getBeansByEmailAddress($trimmed_email);
            $contacts2 = $seed->emailAddress->getBeansByEmailAddress($trimmed_email2);
            $contacts = array_merge($contacts, $contacts2);
            if (safeCount($contacts) == 0) {
                $this->getLogger()->info('End: SoapHelperWebServices->check_for_duplicate_contacts - no duplicte found');
                return null;
            } else {
                foreach ($contacts as $contact) {
                    if (!empty($trimmed_last) && strcmp($trimmed_last, $contact->last_name) == 0) {
                        if ($this->isContactMailContains($contact, $trimmed_email, $trimmed_email2)) {
                            $contact->load_relationship('accounts');
                            if (empty($seed->account_name) || strcmp($seed->account_name, $contact->account_name) == 0) {
                                $this->getLogger()->info('End: SoapHelperWebServices->check_for_duplicate_contacts - duplicte found ' . $contact->id);
                                return $contact->id;
                            }
                        }
                    }
                }
                $this->getLogger()->info('End: SoapHelperWebServices->check_for_duplicate_contacts - no duplicte found');
                return null;
            }
        } else {
            $this->getLogger()->info('End: SoapHelperWebServices->check_for_duplicate_contacts - no duplicte found');
        }
        return null;
    }

    /**
     * Decrypt a string using the TripleDES algorithm.
     *
     * @param string $string
     * @param string $key
     * @param string $iv
     * @return string
     */
    public static function decrypt_tripledes(string $string, string $key, string $iv): string
    {
        return rtrim(
            openssl_decrypt(hex2bin($string), 'des-ede3-cbc', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv),
            "\0"
        );
    }

    /**
     * decrypt a string use the TripleDES algorithm. This meant to be
     * modified if the end user chooses a different algorithm
     *
     * @param $string - the string to decrypt
     *
     * @return a decrypted string if we can decrypt, the original string otherwise
     */
    public function decrypt_string($string)
    {
        $this->getLogger()->info('Begin: SoapHelperWebServices->decrypt_string');

        $focus = Administration::getSettings();
        $key = '';
        if (!empty($focus->settings['ldap_enc_key'])) {
            $key = $focus->settings['ldap_enc_key'];
        }
        if (empty($key)) {
            $this->getLogger()->info('End: SoapHelperWebServices->decrypt_string - empty key');
            return $string;
        }
        $key = substr(md5($key), 0, 24);
        $iv = 'password';
        $this->getLogger()->info('End: SoapHelperWebServices->decrypt_string');
        return self::decrypt_tripledes($string, $key, $iv);
    }

    /**
     * returnFieldsWithAccess
     *
     * @param object $seed an instance of the bean we are checking acl's on
     * @param array $select_fields array of fields being explicitly checked for access, empty array means check all
     * @return array Array of the fields for this bean that passed the acl filter test
     */
    public function returnFieldsWithAccess($seed, $select_fields = [])
    {
        //can't do anything if there is no bean
        if (empty($seed)) {
            return $select_fields;
        }
        //if the select fields array is empty, then use all the fields for this bean
        if (empty($select_fields)) {
            $fields = $seed->field_defs;
            $select_fields = array_keys($fields);
        }
        //check to see if bean implements acl and this is not an admin so we can remove any restricted fields
        if ($seed->bean_implements('ACL') && !empty($GLOBALS['current_user']) && !$GLOBALS['current_user']->is_admin) {
            //lets load up any acl fields for this uer
            $moduleACL = ACLField::loadUserFields($seed->module_dir, $seed->object_name, $GLOBALS['current_user']->id);

            //iterate through the select fields array and remove any restricted acl fields (less than 0)
            foreach ($select_fields as $fieldnum => $fieldname) {
                if (isset($moduleACL['fields'][$fieldname]) && $moduleACL['fields'][$fieldname] < 0) {
                    //this field has an acl restricting the user from accessing it, unset it
                    unset($select_fields[$fieldnum]);
                }
            }
        }
        return $select_fields;
    }

    /**
     * Is instance working in IDM mode
     *
     * @return boolean
     */
    public function isIDMMode()
    {
        return $this->getIDMConfig()->isIDMModeEnabled();
    }

    /**
     * Is module controlled by IDM mode. Should return `true` for `Users` and `Employees`
     *
     * @param string $module
     * @return boolean
     */
    public function isIDMModeModule($module)
    {
        return in_array($module, $this->getIDMConfig()->getIDMModeDisabledModules());
    }

    /**
     * Checks if this field is managed by IDM
     *
     * @param string $name
     * @return boolean
     */
    public function isIDMModeField($name)
    {
        return array_key_exists($name, $this->getIDMConfig()->getIDMModeDisabledFields());
    }

    /**
     * Returns IDM config
     *
     * @return IdmConfig
     */
    protected function getIDMConfig()
    {
        if (is_null($this->idmConfig)) {
            $this->idmConfig = new IdmConfig(\SugarConfig::getInstance());
        }
        return $this->idmConfig;
    }

    /**
     * @param SugarBean $contact
     * @param string $trimmedEmail
     * @param string $trimmedEmail2
     * @return bool
     */
    protected function isContactMailContains($contact, string $trimmedEmail, string $trimmedEmail2): bool
    {
        $result = false;

        if (!empty($trimmedEmail)) {
            $result = strcmp($trimmedEmail, (string)$contact->email1) === 0 ||
                strcmp($trimmedEmail, (string)$contact->email2) === 0;
        }
        if (!$result && !empty($trimmedEmail2)) {
            $result = strcmp($trimmedEmail2, (string)$contact->email1) === 0 ||
                strcmp($trimmedEmail2, (string)$contact->email2) === 0;
        }

        return $result;
    }
} // clazz
