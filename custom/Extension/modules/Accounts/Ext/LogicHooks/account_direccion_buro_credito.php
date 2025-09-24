<?php
 /**
 * @author salvador.lopez@tactos.com.mx
 */
$hook_array['after_save'][] = Array(
    2,
    'Crea o Actualiza direccion de Buró de Crédito',//Just a quick comment about the logic of it
    'custom/modules/Accounts/Account_Hooks.php', //path to the logic hook
    'Account_Hooks', // name of the class
    'account_direccion_buro_credito' // name of the function.
);