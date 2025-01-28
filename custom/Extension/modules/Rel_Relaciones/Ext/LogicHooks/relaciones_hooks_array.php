<?php
/**
 * Created by PhpStorm.
 * User: Jorge
 * Date: 6/12/2015
 * Time: 9:40 PM
 */
$hook_array['before_save'][] = Array(
    1,
    'Set the name of the record',
    'custom/modules/Rel_Relaciones/Rel_Relaciones_Hooks.php',
    'Rel_Relaciones_Hooks',
    'SetName'
);
$hook_array['after_save'][] = Array(
    1,
    'Insertar Relacion en UNICS',
    'custom/modules/Rel_Relaciones/Rel_Relaciones_Hooks.php',
    'Rel_Relaciones_Hooks',
    'insertarRelacionenUNICS'
);
$hook_array['after_save'][] = Array(
    2,
    'Inserta los datos a la otra tabla',
    'custom/modules/Rel_Relaciones/Rel_Relaciones_Hooks.php',
    'Rel_Relaciones_Hooks',
    'setAccount'
);
$hook_array['after_delete'][] = Array(
    1,
    'Actualiza Relacion en UNICS',
    'custom/modules/Rel_Relaciones/Rel_Relaciones_Hooks.php',
    'Rel_Relaciones_Hooks',
    'insertarRelacionenUNICS'
);
$hook_array['before_save'][] = Array(
    2,
    'Establece registro en tabla de auditoría dependiendo de la plataforma proveniento de los servicios Relaciones',
    'custom/modules/Rel_Relaciones/Rel_Relaciones_platform.php',
    'Relacion_platform_user',
    'set_audit_user_platform'
);
$hook_array['before_save'][] = Array(
    3,
    'Establece valor en el campo que relaciona el registro de la Relación con su Cuenta padre del subpanel Relaciones con otros Clientes',
    'custom/modules/Rel_Relaciones/Rel_Relaciones_Hooks.php',
    'Rel_Relaciones_Hooks',
    'SetRelacionesOtrosClientes'
);

$hook_array['before_save'][] = Array(
   4,
   'Evita guardado de registro en caso de que se relacione una cuenta bloqueada',
   'custom/modules/Rel_Relaciones/Rel_Relaciones_Hooks.php',
   'Rel_Relaciones_Hooks',
   'verifica_cuenta_bloqueada'
);

$hook_array['before_save'][] = Array(
   5,
   'Recupera historial comercial de PO y liga a cuenta principal',
   'custom/modules/Rel_Relaciones/Rel_Relaciones_Hooks.php',
   'Rel_Relaciones_Hooks',
   'obtenHistorialComercial'
);
