<?php
/**
 * Created by PhpStorm.
 * User: Tactos
 * Date: 2/05/2022
 * Time: 4:24 PM
 */

 $hook_array['before_save'][] = Array(
     2,
     'Change all text fields to UpperCase',
     'custom/modules/Prospects/clean_fields.php',
     'clean_fields_class',
     'textToUppperCase'
 );
 $hook_array['before_save'][] = Array(
     3,
     'Se eliminan los espacios superiores a uno en los campos principales de la Cuenta',
     'custom/modules/Prospects/clean_fields.php',
     'clean_fields_class',
     'setCleanName'
 );

 $hook_array['before_save'][] = Array(
     4,
     'Validacion de duplicados RFC',
     'custom/modules/Prospects/Validate_Email.php',
     'Validate_Email',
     'existsEmail'
 );

//  $hook_array['before_save'][] = Array(
//      5,
//      'Se llena Macro Sector',
//      'custom/modules/Prospects/clasifica_sectorial.php',
//      'clasifica_sectorial_class',
//      'clasifica_sectorial_function'
//  );
 $hook_array['before_save'][] = Array(
     6,
     'Validacion de duplicados RFC',
     'custom/modules/Prospects/validate_rfc.php',
     'class_validate_rfc',
     'func_validate_rfc'
 );
 $hook_array['before_save'][] = Array(
     7,
     'Validacion REUS email y telefonos',
     'custom/modules/Prospects/valida_reus.php',
     'class_po_reus',
     'func_po_reus'
 );
 $hook_array['before_save'][] = Array(
     8,
     'Consulta Servicio de C4 para Teléfonos',
     'custom/modules/Prospects/telefonos_c4.php',
     'telefonos_c4_class',
     'telefonos_c4_function'
 );

$hook_array['after_save'][] = Array(
    1,
    'Guarda direcciones en Publico Objetivo',//Just a quick comment about the logic of it
    'custom/modules/Prospects/po_direcciones_class.php', //path to the logic hook
    'po_direcciones_class', // name of the class
    'po_direcciones_function' // name of the function.
);


$hook_array['before_save'][] = Array(
    9,
    'Actualiza a los usuario a quien les reporta',
    'custom/modules/Prospects/clients/filterPO.php',
    'filterPO',
    'AssignFilterAccounts_ByUsr'
);

$hook_array['before_save'][] = Array(
    10,
    'Establece bandera Solo Lectura cuando la petición viene de Onboarding',
    'custom/modules/Prospects/Prospects_Onboarding.php',
    'Prospects_Onboarding',
    'setFlagReadOnly'
);

$hook_array['before_save'][] = array(
    13,
    'Inegración de PO con Onboarding en actualización',
    'custom/modules/Prospects/Prospects_Onboarding.php',
    'Prospects_Onboarding',
    'updateApiOnboarding'
);

$hook_array['before_save'][] = array(
    14,
    'Actualización de email a Lead y Cuenta en caso de convertido',
    'custom/modules/Prospects/Validate_Email.php',
    'Validate_Email',
    'checkUpdateEmailPO'
);

$hook_array['before_save'][] = array(
    15,
    'Comprueba longitud y formato de campos de teléfono',
    'custom/modules/Prospects/clean_fields.php',
    'clean_fields_class',
    'checkFormatPhones'
);