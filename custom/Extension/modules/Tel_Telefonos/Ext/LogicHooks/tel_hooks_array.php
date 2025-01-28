<?php
/**
 * Created by PhpStorm.
 * User: Jorge
 * Date: 6/4/2015
 * Time: 1:31 PM
 */
$hook_array['before_save'][] = Array(
    2,

    'get the last sequencia number (MAX) related to the person, and adds 1',//Just a quick comment about the logic of it

    'custom/modules/Tel_Telefonos/Tel_Hooks.php', //path to the logic hook

    'Tel_Hooks', // name of the class

    'setSequencia' // name of the function.

);

// bdekoning@levementum.com 6/9/15
$hook_array['before_save'][] = array(
    3,
    'Sanitize phone number',
    'custom/modules/Tel_Telefonos/Tel_Hooks.php',
    'Tel_Hooks',
    'sanitizeTelefono'
);

$hook_array['before_save'][] = array(
    4,
    'detecta si estamos insertando o actualizando un servicio',
    'custom/modules/Tel_Telefonos/Tel_Hooks.php',
    'Tel_Hooks',
    'detectaEstado'
);

$hook_array['after_save'][] = array(
    1,
    'Inserta comunicación en UNICS WS',
    'custom/modules/Tel_Telefonos/Tel_Hooks.php',
    'Tel_Hooks',
    'insertaComunicaciónUNICS'
);

$hook_array['before_save'][] = array(
    1,
    'Valida Telefonos duplicado y marcados como principal ',
    'custom/modules/Tel_Telefonos/TelDuplicados_Hooks.php',
    'TelDuplicados_Hooks',
    'validaTelDuplicados'
);

$hook_array['before_save'][] = array(
    7,
    'Valida Telefonos duplicado y marcados como principal ',
    'custom/modules/Tel_Telefonos/Tel_Hooks.php',
    'Tel_Hooks',
    'checkFormatPhones'
);