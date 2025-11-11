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
function display_conflict_between_objects($object_1, $object_2, $field_defs, $module_dir, $display_name)
{
    $mod_strings = return_module_language($GLOBALS['current_language'], 'OptimisticLock');
    $title = '<tr><td >&nbsp;</td>';
    $object1_row = '<tr class="oddListRowS1"><td><b>' . htmlspecialchars($mod_strings['LBL_YOURS'], ENT_COMPAT) . '</b></td>';
    $object2_row = '<tr class="evenListRowS1"><td><b>' . htmlspecialchars($mod_strings['LBL_IN_DATABASE'], ENT_COMPAT) . '</b></td>';
    $exists = false;

    foreach ($field_defs as $name => $ignore) {
        $value = $object_1[$name];
        // FIXME: Replace the comparison here with a function from SugarWidgets
        if (!is_scalar($value) || $name === 'team_name') {
            continue;
        }
        if ($value != $object_2->$name && !($object_2->$name instanceof Link2)) {
            $title .= '<td ><b>&nbsp;' .
                htmlspecialchars(translate($field_defs[$name]['vname'], $module_dir), ENT_COMPAT) .
                '</b></td>';
            $object1_row .= '<td>&nbsp;' . htmlspecialchars($value, ENT_COMPAT) . '</td>';
            $object2_row .= '<td>&nbsp;' . htmlspecialchars($object_2->$name, ENT_COMPAT) . '</td>';
            $exists = true;
        }
    }

    if ($exists) {
        $detailViewUrl = 'index.php?' . http_build_query([
                'module' => $module_dir,
                'action' => 'DetailView',
                'record' => $object_1['id'],
            ]);
        $object1ResolveUrl = 'index.php?' . http_build_query([
                'module' => 'OptimisticLock',
                'action' => 'LockResolve',
                'save' => true,
            ]);
        $object2ResolveUrl = 'index.php?' . http_build_query([
                'module' => $object_2->module_dir,
                'action' => 'DetailView',
                'record' => $object_2->id,
            ]);
        $lblConflictExists = htmlspecialchars($mod_strings['LBL_CONFLICT_EXISTS'], ENT_COMPAT);
        $display_name = htmlspecialchars($display_name, ENT_COMPAT);
        $lblAcceptYours = htmlspecialchars($mod_strings['LBL_ACCEPT_YOURS'], ENT_COMPAT);
        $lblAcceptDatabase = htmlspecialchars($mod_strings['LBL_ACCEPT_DATABASE'], ENT_COMPAT);

        $html = <<<HTML
                    <b>{$lblConflictExists}
                        <a href="$detailViewUrl"  target='_blank'>$display_name</a>
                    </b>
                    <br>
                    <table class='list view' border='0' cellspacing='0' cellpadding='2'>
                        $title
                        <td>&nbsp;</td>
                    </tr>
                    $object1_row
                    <td>
                        <a href="$object1ResolveUrl">{$lblAcceptYours}</a>
                    </td>
                </tr>
                $object2_row
                <td>
                    <a href="$object2ResolveUrl">{$lblAcceptDatabase}</a>
                </td>
            </tr>
        </table>
        <br>
        HTML;

        echo $html;
    } else {
        echo "<b>{$mod_strings['LBL_RECORDS_MATCH']}</b><br>";
    }
}

if (isset($_SESSION['o_lock_object'])) {
    global $beanFiles, $moduleList;
    $object = $_SESSION['o_lock_object'];
    $current_state = BeanFactory::getBean($_SESSION['o_lock_module'], $object['id']);

    if (isset($_REQUEST['save'])) {
        $_SESSION['o_lock_fs'] = true;
        echo $_SESSION['o_lock_save'];
        die();
    } else {
        display_conflict_between_objects($object, $current_state, $current_state->field_defs, $current_state->module_dir, $_SESSION['o_lock_class']);
    }
} else {
    echo $mod_strings['LBL_NO_LOCKED_OBJECTS'];
}
