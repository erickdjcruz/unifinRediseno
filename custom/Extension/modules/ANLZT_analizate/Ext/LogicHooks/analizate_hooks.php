<?php
/**
 * Created by PhpStorm.
 * User: Adrian Arauz
 * Date: 24/02/20
 * Time: 11:48 AM
 */

 /*
$hook_array['after_save'][] = Array(
    1,
    'Envía y reenvia Correo',
    'custom/modules/ANLZT_analizate/analizate_hooks.php',
    'analizate_hooks',
    'EnvioMail'
);
*/
$hook_array['after_save'][] = Array(
    2,
    'Actualiza información en cuenta a partir de JSON ROBINA',
    'custom/modules/ANLZT_analizate/analizate_hooks.php',
    'analizate_hooks',
    'ActualizaRobina'
);
