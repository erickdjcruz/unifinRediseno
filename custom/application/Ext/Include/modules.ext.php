<?php
// WARNING: The contents of this file are auto-generated.
?>
<?php
// Merged from custom/Extension/application/Ext/Include/rli_unhide.ext.php

$moduleList[] = 'RevenueLineItems';
if (isset($modInvisList) && is_array($modInvisList)) {
    foreach ($modInvisList as $key => $mod) {
        if ($mod === 'RevenueLineItems') {
            unset($modInvisList[$key]);
        }
    }
}
?>
