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

global $current_user, $beanFiles, $mod_strings;

use Sugarcrm\Sugarcrm\Session\SessionStorage;

if (!is_admin($current_user) && !isset($from_sync_client)) {
    sugar_die($GLOBALS['app_strings']['ERR_NOT_ADMIN']);
}

if (ini_get('max_execution_time') > 0 && ini_get('max_execution_time') < 3600) {
    ini_set('max_execution_time', '3600');
}

$db = DBManagerFactory::getInstance();

$sessionStorage = SessionStorage::getInstance();

$statements = [];

$collectors = [
    // by default, collect all statements in an array to display later
    function (?string $statement) use (&$statements): void {
        $statements[] = $statement;
    },
];


// if running as part of the Quick Repair and Rebuild process, wrap the statement observer
if (isset($this->statementObserver)) {
    $collectors[] = function (?string $statement): void {
        ($this->statementObserver)($statement);
    };
}

$collect = function (?string $statement) use (&$collectors): void {
    if (is_null($statement)) {
        return;
    }
    $statement = trim($statement);

    if ($statement === '') {
        return;
    }

    foreach ($collectors as $collector) {
        $collector($statement);
    }
};

$execute = $_REQUEST['execute'] ?? false;
if (isset($_POST['raction'])) {
    if (strtolower($_POST['raction']) === 'execute') {
        $sql = str_replace(
            [
                "\n",
                '&#039;',
            ],
            [
                '',
                "'",
            ],
            preg_replace('#(/\*.+?\*/\n*)#', '', $sessionStorage['repair_sql'])
        );
        foreach (explode(';', $sql) as $stmt) {
            $stmt = trim($stmt);

            if (!empty($stmt)) {
                $db->query($stmt, true, 'Executing repair query: ');
            }
        }

        echo "<h3>{$mod_strings['LBL_REPAIR_DATABASE_SYNCED']}</h3>";
    }
} else {
    if (empty($_REQUEST['repair_silent'])) {
        if (empty($hideModuleMenu)) {
            echo getClassicModuleTitle($mod_strings['LBL_REPAIR_DATABASE'], [$mod_strings['LBL_REPAIR_DATABASE']], true);
        }
        echo <<<HTML
<h1 id="rdloading">
    {$mod_strings['LBL_REPAIR_DATABASE_PROCESSING']}
</h1>
HTML;
        ob_flush();
    }

    VardefManager::clearVardef();
    $repairedTables = [];

    $db->setOption('skip_index_rebuild', true);
    $indices = $db->get_schema_indices();

    foreach ($beanFiles as $bean => $file) {
        if (file_exists($file)) {
            require_once $file;
            unset($GLOBALS['dictionary'][$bean]);
            $focus = BeanFactory::newBeanByName($bean);
            if ($focus instanceof SugarBean) {
                $tableName = $focus->getTableName();
                // Not all Beans are table based, so we need to check if there
                // is a table_name for this bean before proceeding
                // Example Beans are MergeRecord and EmptyBean
                if ($tableName && !isset($repairedTables[$tableName])) {
                    $tableExists = $db->tableExists($tableName);
                    $statement = $db->repairTable($focus, $execute);
                    $collect($statement);

                    // repair table indices only in case if the table previously existed, otherwise the table
                    // has already been created with indices despite skip_index_rebuild
                    if ($tableExists) {
                        $compareIndices = $indices[$tableName] ?? [];
                        $statement = $db->alterTableIndices(
                            $tableName,
                            $focus->getFieldDefinitions(),
                            $focus->getIndices(),
                            $compareIndices,
                            $execute
                        );
                        $collect($statement);
                    }
                    $repairedTables[$focus->table_name] = true;
                }
            }
            //Repair Custom Fields
            if (($focus instanceof SugarBean) && $focus->hasCustomFields() && !isset($repairedTables[$focus->table_name . '_cstm'])) {
                $df = new DynamicField($focus->module_dir);
                $df->bean = $focus;

                $customTableName = $focus->get_custom_table_name();
                $tableExists = $db->tableExists($customTableName);
                $statement = $df->repairCustomFields($execute);
                $collect($statement);

                // repair table indices only in the case if the table previously existed, otherwise the table
                // has already been created with indices despite skip_index_rebuild
                if ($tableExists) {
                    $statement = $df->repairIndices($indices[$customTableName] ?? [], $execute);
                    $collect($statement);
                }

                $repairedTables[$customTableName] = true;
            }
        }
    }

    $olddictionary = $dictionary;

    unset($dictionary);
    include 'modules/TableDictionary.php';

    foreach ($dictionary as $meta) {
        if (empty($meta['table']) || isset($repairedTables[$meta['table']])) {
            continue;
        }

        $tableName = $meta['table'];
        $fielddefs = $meta['fields'];
        $definedIndices = $meta['indices'] ?? [];
        $deployedIndices = $indices[$tableName] ?? [];
        $engine = $meta['engine'] ?? null;
        $tableExists = $db->tableExists($tableName);
        $statement = $db->repairTableParams($tableName, $fielddefs, $definedIndices, $execute, $engine);
        $collect($statement);

        // repair table indices only in the case if the table previously existed, otherwise the table
        // has already been created with indices despite skip_index_rebuild
        if ($tableExists) {
            $statement = $db->alterTableIndices($tableName, $fielddefs, $definedIndices, $deployedIndices, $execute);
            $collect($statement);
        }

        $repairedTables[$tableName] = true;
    }

    $dictionary = $olddictionary;

    $db->setOption('skip_index_rebuild', false);

    $sql = implode("\n", $statements);

    if (empty($_REQUEST['repair_silent'])) {
        echo <<<HTML
 <script type="text/javascript">
 document.getElementById('rdloading').style.display = "none";
 </script>
 HTML;

        if (isset($sql) && !empty($sql)) {
            $qry_str = '';
            foreach (explode("\n", $sql) as $line) {
                if (!empty($line) && substr($line, -2) != '*/') {
                    $line .= ';';
                }

                $qry_str .= $line . "\n";
            }

            $ss = new Sugar_Smarty();
            $ss->assign('MOD', $GLOBALS['mod_strings']);
            $ss->assign('qry_str', $qry_str);
            $sessionStorage['repair_sql'] = $qry_str;
            echo $ss->fetch('modules/Administration/templates/RepairDatabase.tpl');
        } else {
            echo "<h3>{$mod_strings['LBL_REPAIR_DATABASE_SYNCED']}</h3>";
        }
    }
}
