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
function getModuleMultiSelectOptions()
{
    global $beanList, $beanFiles, $dictionary, $app_list_strings;

    $exclude_modules = [
        'ImportMap',
        'UsersLastImport',
        'SavedSearch',
        'UserPreference',
        'SugarFavorites',
        'OAuthKey',
        'OAuthToken',
    ];

    if (!isset($_SESSION['reassignRecords']['assignedModuleListCache'])) {
        $beanListDup = $beanList;

        unset($beanListDup['ForecastManagerWorksheets']);

        foreach ($beanListDup as $m => $p) {
            if (empty($beanFiles[$p])) {
                unset($beanListDup[$m]);
            } else {
                $obj = BeanFactory::newBean($m);
                if (!isset($obj->field_defs['assigned_user_id']) || (
                    isset($obj->field_defs['assigned_user_id']['source']) &&
                        $obj->field_defs['assigned_user_id']['source'] === 'non-db'
                ) || (
                    isset($dictionary[$obj->object_name]['reassignable']) &&
                    !isTruthy($dictionary[$obj->object_name]['reassignable'])
                )
                ) {
                    unset($beanListDup[$m]);
                }
            }
        }

        //Get the list of beans without the excluded modules
        $beanListDup = array_diff($beanListDup, $exclude_modules);

        //Leon bug 20739
        $beanListDupDisp = [];
        foreach ($beanListDup as $m => $p) {
            $beanListDupDisp[$m] = $app_list_strings['moduleList'][$m] ?? $p;
        }

        asort($beanListDupDisp, SORT_STRING);

        $_SESSION['reassignRecords']['assignedModuleListCache'] = $beanListDup;
        $_SESSION['reassignRecords']['assignedModuleListCacheDisp'] = $beanListDupDisp;
    }

    $selected = [];

    if (!empty($_SESSION['reassignRecords']['modules'])) {
        foreach ($_SESSION['reassignRecords']['modules'] as $key => $mod) {
            $selected[] = $key;
        }
    }

    return get_select_options_with_id_separate_key(
        $_SESSION['reassignRecords']['assignedModuleListCacheDisp'],
        $_SESSION['reassignRecords']['assignedModuleListCacheDisp'],
        $selected
    );
}

function processConditions(SugarBean $bean, string $fromuser, array $moduleFilters, string $module, array $data): array
{
    $db = $bean->db;
    $table = $bean->table_name;
    $q_tables = " {$table} ";
    $q_where = "WHERE {$table}.deleted=0 AND {$table}.assigned_user_id = " . $db->quoted($fromuser);

    // Process conditions based on metadata
    if (isset($moduleFilters[$module]['fields']) && is_array($moduleFilters[$module]['fields'])) {
        $custom_added = false;
        foreach ($moduleFilters[$module]['fields'] as $meta) {
            $metaName = $meta['name'];

            if (!empty($data[$metaName])) {
                $_SESSION['reassignRecords']['filters'][$metaName] = $data[$metaName];
            }

            $is_custom = !empty($meta['custom_table']);

            if ($is_custom && !$custom_added) {
                $q_tables .= "INNER JOIN {$table}_cstm ON {$table}.id = {$table}_cstm.id_c ";
                $custom_added = true;
            }

            $addcstm = $is_custom ? '_cstm' : '';

            switch ($meta['type']) {
                case 'text':
                case 'select':
                    $q_where .= sprintf(
                        ' and %s.%s = %s ',
                        $table . $addcstm,
                        $meta['dbname'],
                        $db->quoted($data[$metaName])
                    );

                    break;
                case 'multiselect':
                    if (empty($data[$metaName])) {
                        continue 2;
                    }

                    // Also check condition where default selected was the
                    // only thing and set to none. However, we need to
                    // exclude '0', since '0' is considered by php
                    // to be empty, but in our logic, it is a valid value.
                    if (safeCount($data[$metaName]) == 1 &&
                        empty($data[$metaName][0]) &&
                        $data[$metaName][0] !== '0') {
                        continue 2;
                    }

                    $empty_check = '';
                    foreach ($data[$metaName] as $onevalue) {
                        if (empty($onevalue)) {
                            $empty_check .= " OR {$table}{$addcstm}.{$meta['dbname']} IS NULL ";
                        }
                    }

                    $in_string = implode(',', array_map(function ($value) use ($db): string {
                        return $db->quoted($value);
                    }, (array)$data[$metaName]));

                    $q_where .= " AND ({$table}{$addcstm}.{$meta['dbname']} IN ($in_string) $empty_check)";
                    break;
                default:
                    continue 2;
                    break;
            }
        }
    }
    return [$q_tables, $q_where];
}
