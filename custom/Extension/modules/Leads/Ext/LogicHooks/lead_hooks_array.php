<?php
/**
 * Created by PhpStorm.
 * User: Jorge
 * Date: 6/16/2015
 * Time: 4:24 PM
 */
$hook_array['after_save'][] = Array(
    1,
    'crear Persona prospecto',
    'custom/modules/Leads/Lead_Hooks.php',
    'Lead_Hooks',
    'crearProspecto'
);


$hook_array['after_save'][] = Array(
    3,
    'crear URL de Originación',
    'custom/modules/Leads/Lead_Hooks.php',
    'Lead_Hooks',
    'crearURLOriginacion'
);

$hook_array['after_save'][] = Array(
    4,
    'Convierte a Cuenta',
    'custom/modules/Leads/Lead_Hooks.php',
    'Lead_Hooks',
    're_asign_meetings'
);

$hook_array['after_save'][] = Array(
    5,
    'Direcciones_Leads_Hooks',//Just a quick comment about the logic of it
    'custom/modules/Leads/lead_direcciones_class.php', //path to the logic hook
    'lead_direcciones_class', // name of the class
    'lead_direcciones_function' // name of the function.
);

$hook_array['after_save'][] = Array(
    6,
    'Enviar notificaciones a Vendors',//Just a quick comment about the logic of it
    'custom/modules/Leads/NotificacionVendor.php', //path to the logic hook
    'NotificacionVendor', // name of the class
    'notificaVendors' // name of the function.
);

$hook_array['before_save'][] = Array(
   1,
   'Evita guardado de registro en caso de que se relacione una cuenta bloqueada',
   //Hsace referencia a archivo dentro de Opportunities para no generar uno nuevo ya que se reutiliza la funcionalidad para Leads
   'custom/modules/Opportunities/Check_Bloqueo_Cuenta_Opp.php',
   'Check_Bloqueo_Cuenta_Opp',
   'verifica_cuenta_bloqueada_opp'
);

$hook_array['before_save'][] = Array(
    14,
    'Comprueba longitud y formato de campos de teléfono',
    'custom/modules/Prospects/clean_fields.php',
    'clean_fields_class',
    'checkFormatPhones'
 );

