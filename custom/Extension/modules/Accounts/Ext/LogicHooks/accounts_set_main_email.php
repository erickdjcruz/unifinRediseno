<?php

$hook_array['before_save'][] = Array(
    33,
    'Al detectar cambio de email, se mantiene el email actual y el nuevo se va manteniendo como principal',
    'custom/modules/Accounts/Account_Hooks.php',
    'Account_Hooks',
    'setEmailPrincipal'
);