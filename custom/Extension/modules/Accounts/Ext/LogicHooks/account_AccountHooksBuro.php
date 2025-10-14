<?php

$hook_array['after_save'][] = Array(
    24,
    'Actualiza bandera para procesar y crear direccion buro de credito',//Just a quick comment about the logic of it
    'custom/modules/Accounts/AccountHooksBuro.php', //path to the logic hook
    'AccountHooksBuro', // name of the class
    'setBanderaBuroCredito' // name of the function.
);