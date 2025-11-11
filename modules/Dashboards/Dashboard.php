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

use Sugarcrm\Sugarcrm\AccessControl\AccessControlManager;
use Sugarcrm\Sugarcrm\Dashboards\DashletValidatorFactory;

/**
 *  Dashboards is used to store dashboard configuration data.
 */
class Dashboard extends Basic
{
    public $table_name = 'dashboards';
    public $module_name = 'Dashboards';
    public $module_dir = 'Dashboards';
    public $object_name = 'Dashboard';

    /**
     * This overrides the default retrieve function setting the default to encode to false
     */
    public function retrieve($id = '-1', $encode = false, $deleted = true)
    {
        $dashboard = parent::retrieve($id, $encode, $deleted);

        if ($dashboard === null) {
            return null;
        }
        // Expand the metadata for processing.
        $metadata = json_decode($dashboard->metadata);

        // If we don't have a components/dashlets in metadata for whatever reason, we're out, send back unchanged.
        if (!isset($metadata->components) && !isset($metadata->dashlets)) {
            $metadata = $this->processMetadataFilters($metadata);
            $metadata = $this->addRuntimeFilterOperators($metadata);
            $metadata = $this->addUsers($metadata);

            $dashboard->metadata = json_encode($metadata);

            return $dashboard;
        }

        $metadata = $this->processMetadataWithAcl($metadata);
        $metadata = $this->processMetadataFilters($metadata);
        $metadata = $this->addRuntimeFilterOperators($metadata);
        $metadata = $this->addUsers($metadata);

        // Re-encode and save the metadata back to the dashboard object before returning it.
        $dashboard->metadata = json_encode($metadata);

        return $dashboard;
    }

    /**
     * Sanitize metadata dashboard filters
     *
     * @param mixed $metadata
     *
     * @return string
     */
    protected function processMetadataFilters($metadata)
    {
        if (!$metadata) {
            return $metadata;
        }

        $dashlets = [];

        if (property_exists($metadata, 'tabs')) {
            foreach ($metadata->tabs as $tabData) {
                $dashlets = array_merge($dashlets, property_exists($tabData, 'dashlets') ? $tabData->dashlets : []);
            }
        } else {
            $dashlets = property_exists($metadata, 'dashlets') ? $metadata->dashlets : [];
        }

        $metadata = $this->verifyFiltersValidity($metadata, $dashlets);

        return $metadata;
    }

    /**
     * Go through dashlets and check if the filters are still there
     *
     * @param mixed $metadata
     * @param mixed $dashlets
     * @return void
     */
    protected function verifyFiltersValidity($metadata, $dashlets)
    {
        $filterGroups = property_exists($metadata, 'filters') ? $metadata->filters : [];

        foreach (safeIsIterable($dashlets) ? $dashlets : [] as $dashletKey => $dashlet) {
            $isDashletValid = DashletValidatorFactory::getInstance()->validate($dashlet);

            if (!$isDashletValid) {
                $metadata->runtimeFiltersDateModified = $this->getCurrentUTC();
                $dashlets[$dashletKey]->view->filtersDef = [];
            }
        }

        $currentUserRestrictedDashlets = $this->getRestrictedDashletsIds($dashlets);

        if (!empty($currentUserRestrictedDashlets)) {
            $metadata->currentUserRestrictedDashlets = $currentUserRestrictedDashlets;
        }

        $currentUserRestrictedGroups = [];

        foreach ($filterGroups as $filterGroupKey => $filterGroup) {
            for ($filterIdx = safeCount($filterGroup->fields) - 1; $filterIdx >= 0; $filterIdx--) {
                $field = $filterGroup->fields[$filterIdx];
                $isValid = $this->isFieldAvailable($field, $dashlets);

                if (!$isValid) {
                    $metadata->runtimeFiltersDateModified = $this->getCurrentUTC();
                    // remove field out of filter group
                    array_splice($filterGroup->fields, $filterIdx, 1);

                    // remove filter group if empty
                    if (empty($filterGroup->fields)) {
                        unset($metadata->filters->$filterGroupKey);
                    }

                    // remove dashboard fiters if there are no filter groups
                    if (empty((array)$metadata->filters)) {
                        unset($metadata->filters);
                    }
                }

                if (((in_array($field->dashletId, $currentUserRestrictedDashlets) &&
                        property_exists($metadata, 'currentUserRestrictedGroups')) &&
                    (
                        !is_array($metadata->currentUserRestrictedGroups) ||
                        (
                            is_array($metadata->currentUserRestrictedGroups) &&
                            !safeInArray($filterGroupKey, $metadata->currentUserRestrictedGroups)
                        )
                    ))
                ) {
                    $currentUserRestrictedGroups[] = $filterGroupKey;
                }
            }
        }

        if (!empty($currentUserRestrictedGroups)) {
            $metadata->currentUserRestrictedGroups = $currentUserRestrictedGroups;
        }
        return $metadata;
    }

    /**
     * Get current user restricted dashlet ids
     *
     * @param array $dashlets
     * @return array
     */
    protected function getRestrictedDashletsIds($dashlets)
    {
        $currentUserRestrictedDashlets = [];
        foreach (safeIsIterable($dashlets) ? $dashlets : [] as $dashlet) {
            if (!isset($dashlet->view->reportId)) {
                continue;
            }

            $reportId = $dashlet->view->reportId;
            $reportBean = BeanFactory::retrieveBean('Reports', $reportId);
            if (empty($reportBean)) {
                $currentUserRestrictedDashlets[] = $dashlet->id;
            }
        }

        return $currentUserRestrictedDashlets;
    }

    /**
     * Get current UTC taking into account the user timezone
     *
     * @return string
     */
    public function getCurrentUTC()
    {
        global $current_user;

        $userTimezone = $current_user->getPreference('timezone');
        $serverTime = new DateTime('now', new DateTimeZone('UTC'));

        $serverTime->setTimezone(new DateTimeZone($userTimezone));
        $formattedTime = $serverTime->format('Y-m-d H:i:s');

        return $formattedTime;
    }

    /**
     * Check if the field is still a part of a dashlet filters def
     *
     * @param mixed $field
     * @param mixed $dashlets
     *
     * @return boolean
     */
    protected function isFieldAvailable(&$field, $dashlets)
    {
        foreach ($dashlets as $dashlet) {
            $isDashletFieldValid = DashletValidatorFactory::getInstance()->validateField($dashlet, $field);

            if ($dashlet->id === $field->dashletId &&
                $isDashletFieldValid &&
                $this->fieldDefExists($dashlet->view->filtersDef->Filter_1, $field)) {
                $field->dashletLabel = $dashlet->view->label;

                return true;
            }
        }

        return false;
    }

    /**
     * Checks if a field def exists in the filters
     * @param array $filters
     * @param array $field
     * @return boolean
     */
    protected function fieldDefExists($filters, $field)
    {
        $valid = false;

        if (!is_object($filters)) {
            return $valid;
        }

        foreach (safeIsIterable($filters) ? $filters : [] as $filterKey => $subFilters) {
            if ($filterKey === 'operator') {
                continue;
            }

            if (isset($subFilters->operator) && !$valid) {
                $valid = $this->fieldDefExists($subFilters, $field);
            }

            if ($field &&
                $subFilters &&
                property_exists($subFilters, 'runtime') &&
                property_exists($subFilters, 'name') &&
                property_exists($subFilters, 'table_key') &&
                $subFilters->runtime &&
                $subFilters->name === $field->fieldName &&
                $subFilters->table_key === $field->tableKey) {
                return true;
            }
        }

        return $valid;
    }

    /**
     * Append runtime filter operators to the metadata
     * @param $metadata
     *
     * @return array
     */
    protected function addRuntimeFilterOperators($metadata)
    {
        if (!$metadata) {
            return $metadata;
        }

        $fileList = \MetaDataFiles::getClientFiles(['base'], 'filter', 'Reports');
        $results = \MetaDataFiles::getClientFileContents($fileList, 'filter', 'Reports');

        if (!$results || !is_array($results) || !array_key_exists('runtime-operators', $results)
            || !array_key_exists('meta', $results['runtime-operators'])) {
            $metadata->runtimeFilterOperators = [];
        } else {
            $metadata->runtimeFilterOperators = $results['runtime-operators']['meta'];
        }

        return $metadata;
    }

    /**
     * Append users to the metadata
     *
     * @param $metadata
     *
     * @return array
     */
    protected function addUsers($metadata)
    {
        if (!$metadata) {
            return $metadata;
        }

        $userBean = \BeanFactory::newBean('Users');
        $users = $userBean ? $userBean->getUserArray(false) : [];

        $currentUserLabel = translate('LBL_CURRENT_USER');
        $users[$currentUserLabel] = $currentUserLabel;

        $metadata->users = $users;

        return $metadata;
    }

    /**
     * apply ACL and license type restriction to metadata
     * @param $metadata
     * @return string
     */
    protected function processMetadataWithAcl($metadata)
    {
        // If metadata doesn't have a top-level dashlets key, it's a legacy
        // dashboard
        if (!isset($metadata->dashlets)) {
            return $this->processLegacyMetadataWithAcl($metadata);
        }

        $dirty = false;
        foreach ($metadata->dashlets as $key => $dashlet) {
            // This section of code is a portion of the code referred
            // to as Critical Control Software under the End User
            // License Agreement.  Neither the Company nor the Users
            // may modify any portion of the Critical Control Software.
            if (isset($dashlet->context->module) && !SugarACL::checkAccess($dashlet->context->module, 'access')) {
                unset($metadata->dashlets[$key]);
                $dirty = true;
                continue;
            }
            if (!empty($dashlet->view->type)) {
                $allowAccess = $this->allowedToAccessDashlet($dashlet->view->type);
                if (!$allowAccess) {
                    // this is license controled dashlet
                    unset($metadata->dashlets[$key]);
                    $dirty = true;
                }
            }
            //END REQUIRED CODE DO NOT MODIFY
        }

        if ($dirty) {
            $metadata->dashlets = array_values($metadata->dashlets);
        }
        return $metadata;
    }

    /**
     * apply ACL and license type restriction to legacy metadata
     * @param $metadata
     * @return string
     */
    protected function processLegacyMetadataWithAcl($metadata)
    {
        $dirty = false;

        // Loop through the dashboard, drilling down to the dashlet level.
        foreach ($metadata->components as $component_key => $component) {
            if (!isset($component->rows)) {
                continue;
            }
            foreach ($component->rows as $row_key => $row) {
                foreach ($row as $item_key => $item) {
                    // Check if this user has access to the module upon which this dashlet is based.
                    if (isset($item->context->module) && !SugarACL::checkAccess($item->context->module, 'access')) {
                        // The user does not have access, remove the dashlet.
                        unset($metadata->components[$component_key]->rows[$row_key][$item_key]);

                        // Check if this row is now empty.
                        if (safeCount($metadata->components[$component_key]->rows[$row_key]) == 0) {
                            // This row is now empty, remove it and mark the metadata as dirty.
                            unset($metadata->components[$component_key]->rows[$row_key]);
                            $dirty = true;
                        }
                    }
                    // Check if this row is license type controled
                    // This section of code is a portion of the code referred
                    // to as Critical Control Software under the End User
                    // License Agreement.  Neither the Company nor the Users
                    // may modify any portion of the Critical Control Software.
                    if (isset($metadata->components[$component_key]->rows[$row_key]) &&
                        is_array($metadata->components[$component_key]->rows[$row_key]) &&
                        isset($metadata->components[$component_key]->rows[$row_key][$item_key])) {
                        if (!empty($item->view->type)) {
                            // This section of code is a portion of the code referred
                            // to as Critical Control Software under the End User
                            // License Agreement.  Neither the Company nor the Users
                            // may modify any portion of the Critical Control Software.
                            $allowAccess = $this->allowedToAccessDashlet($item->view->type);
                            if (!$allowAccess) {
                                // this is license controled dashlet
                                unset($metadata->components[$component_key]->rows[$row_key][$item_key]);
                                // Check if this row is now empty.
                                if (safeCount($metadata->components[$component_key]->rows[$row_key]) == 0) {
                                    // This row is now empty, remove it and mark the metadata as dirty.
                                    unset($metadata->components[$component_key]->rows[$row_key]);
                                    $dirty = true;
                                }
                            }
                            //END REQUIRED CODE DO NOT MODIFY
                        }
                    }
                }
            }
        }

        // Check if we've modified the metadata.
        if ($dirty) {
            // Loop through the rows re-assigning sequential array keys for dashboard display.
            foreach ($metadata->components as $key => $value) {
                $metadata->components[$key]->rows = array_values($metadata->components[$key]->rows);
            }
        }

        return $metadata;
    }


    /**
     * @param string $dashletLabel
     * @return bool
     */
    protected function allowedToAccessDashlet(string $label): bool
    {
        // This section of code is a portion of the code referred
        // to as Critical Control Software under the End User
        // License Agreement.  Neither the Company nor the Users
        // may modify any portion of the Critical Control Software.
        return AccessControlManager::instance()->allowDashletAccess($label);
        //END REQUIRED CODE DO NOT MODIFY
    }

    /**
     * This function fetches an array of dashboards for the current user
     *
     * 'view' is deprecated because it's reserved db word.
     * Some old API (before 7.2.0) can use 'view'.
     * Because of that API will use 'view' as 'view_name' if 'view_name' isn't present.
     * Returns all the dashboards available for the User given.
     *
     * Optionally you can pass the view in the $options to filter the
     * dashboards of a certain view.
     * For homepage the view is assumed empty.
     *
     * @param User $user The user that we want to get the dashboards from.
     * @param array $options A list of options such as: limit, offset and view.
     *
     * @return array The list of the User's dashboard and next offset.
     */
    public function getDashboardsForUser(User $user, array $options = [])
    {
        $order = !empty($options['order_by']) ? $options['order_by'] : 'date_entered desc';
        $from = "{$this->table_name}.assigned_user_id = '" . $this->db->quote($user->id) . "'
                 AND {$this->table_name}.dashboard_module ='" . $this->db->quote($options['dashboard_module']) . "'";
        if (isset($options['view']) && !isset($options['view_name'])) {
            $options['view_name'] = $options['view'];
        }
        if (!empty($options['view_name'])) {
            $from .= ' and view_name =' . $this->db->quoted($options['view_name']);
        }
        $offset = !empty($options['offset']) ? (int)$options['offset'] : 0;
        $limit = !empty($options['limit']) ? (int)$options['limit'] : -1;
        $result = $this->get_list($order, $from, $offset, $limit, -1, 0);
        $nextOffset = (safeCount($result['list']) > 0 && safeCount($result['list']) == $limit) ? ($offset + $limit) : -1;
        return ['records' => $result['list'], 'next_offset' => $nextOffset];
    }

    /**
     * This overrides the default save function setting assigned_user_id
     * @see SugarBean::save()
     *
     * 'view' is deprecated because it's reserved db word.
     * Some old API (before 7.2.0) can use 'view'.
     * Because of that API will use 'view' as 'view_name' if 'view_name' isn't present.
     */
    public function save($check_notify = false)
    {
        if (empty($this->assigned_user_id)) {
            $this->assigned_user_id = $GLOBALS['current_user']->id;
        }

        if (empty($this->team_id)) {
            $this->team_id = $GLOBALS['current_user']->getPrivateTeamID();
        }

        if (empty($this->team_set_id)) {
            $this->load_relationship('teams');
            $this->teams->add([$this->team_id]);
        }

        if (empty($this->acl_team_set_id)) {
            $this->acl_team_set_id = '';
        }

        if (isset($this->view) && !isset($this->view_name)) {
            $this->view_name = $this->view;
        }

        $this->removeCurrentUserRestrictions();

        return parent::save($check_notify);
    }

    /**
     * Remove current user restrictions
     */
    protected function removeCurrentUserRestrictions()
    {
        if (empty($this->metadata)) {
            return;
        }

        $metadata = json_decode($this->metadata);

        $updateMeta = false;
        if (isset($metadata->currentUserRestrictedDashlets)) {
            unset($metadata->currentUserRestrictedDashlets);
            $updateMeta = true;
        }

        if (isset($metadata->currentUserRestrictedGroups)) {
            unset($metadata->currentUserRestrictedGroups);
            $updateMeta = true;
        }

        if ($updateMeta) {
            $this->metadata = json_encode($metadata);
        }
    }

    /**
     * 'view' is deprecated because it's reserved db word.
     * Some old API (before 7.2.0) can use 'view'.
     * Because of that API will return 'view' with the same value as 'view_name'.
     *
     * @param string $order_by
     * @param string $where
     * @param int $row_offset
     * @param int $limit
     * @param int $max
     * @param int $show_deleted
     * @param bool $singleSelect
     * @param array $select_fields
     *
     * @return array
     */
    public function get_list($order_by = '', $where = '', $row_offset = 0, $limit = -1, $max = -1, $show_deleted = 0, $singleSelect = false, $select_fields = [])
    {
        $result = parent::get_list($order_by, $where, $row_offset, $limit, $max, $show_deleted, $singleSelect, $select_fields);
        if (!empty($result['list'])) {
            foreach ($result['list'] as $dashboard) {
                $dashboard->view = $dashboard->view_name;
            }
        }
        return $result;
    }
}
