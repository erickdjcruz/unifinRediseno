<?php
/**
 * Created by PhpStorm.
 * User: Jorge
 * Date: 6/15/2015
 * Time: 12:18 PM
 */
/*
$hook_array['after_save'][] = Array(
    2,
    'WS Inserta dirección en UNICS',
    'custom/modules/dire_Direccion/Dir_Direcciones_Hooks.php',
    'Dir_Direcciones_Hooks',
    'insertarDireccionenUNICS'
);
*/

$hook_array['before_save'][] = Array(
    1,
    'detecta si estamos insertando o actualizando un servicio',
    'custom/modules/dire_Direccion/Dir_Direcciones_Hooks.php',
    'Dir_Direcciones_Hooks',
    'detectaEstado'
);

$hook_array['before_save'][] = Array(
    2,
    'change all text fields to UpperCase',
    'custom/modules/dire_Direccion/Dir_Direcciones_Hooks.php',
    'Dir_Direcciones_Hooks',
    'textToUppperCase'
);

$hook_array['before_save'][] = Array(
    3,
    'get the last sequencia number (MAX) related to the person, and adds 1',
    'custom/modules/dire_Direccion/Dir_Direcciones_Hooks.php',
    'Dir_Direcciones_Hooks',
    'setSequencia'
);

$hook_array['before_save'][] = Array(
    4,
    'Establece campo name con la concatenación de Calle, Exterior, Interior, Colonia y Municipio',
    'custom/modules/dire_Direccion/Dir_Direcciones_Hooks.php',
    'Dir_Direcciones_Hooks',
    'setConcatName'
);

$hook_array['before_delete'][] = Array(
    1,
    'WS Elimina Direccion en UNICS',
    'custom/modules/dire_Direccion/Dir_Direcciones_Hooks.php',
    'Dir_Direcciones_Hooks',
    'eliminaDireccionenUNICS'
);


$hook_array['before_save'][] = Array(
    6,
    'Establece campo json para generar estructura de valores que se quieren actualizar y además establece banderas para mandar notificación de cambio',
    'custom/modules/dire_Direccion/Dir_Direcciones_Hooks.php',
    'Dir_Direcciones_Hooks',
    'setValoresPorActualizar'
);

$hook_array['before_save'][] = Array(
    7,
    'Establece información en campo description con identificadores de sepomex para mostrar direcciones correctamente en módulo de cuentas',
    'custom/modules/dire_Direccion/Dir_Direcciones_Hooks.php',
    'Dir_Direcciones_Hooks',
    'setDescriptionSepomex'
);


/*
$hook_array['after_save'][] = Array(
    1,
    'Revierte valores en caso de detectar cambios en una dirección fiscal',
    'custom/modules/dire_Direccion/Dir_Direcciones_Hooks.php',
    'Dir_Direcciones_Hooks',
    'revierteCambiosFiscal'
);
*/
