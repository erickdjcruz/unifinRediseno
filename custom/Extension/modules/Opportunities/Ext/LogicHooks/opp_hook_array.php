<?php
 /**
 * @file   opp_hook_array.php
 * @author trobinson@levementum.com
 * @date   6/3/2015 1:06 PM
 * @brief  opportunity logic hook array
 */

 $hook_array['before_save'][] = Array(
    1,
    'busca presolicitudes abiertas para un mismo cliente',
    'custom/modules/Opportunities/opp_logic_hooks.php',
    'OpportunityLogic',
    'buscaDuplicados'
);
/*
 $hook_array['before_save'][] = Array(
    2,
    'evey time a new team is added to the account record, All related opportunities get the new team',
    'custom/modules/Opportunities/opp_logic_hooks.php',
    'OpportunityLogic', // name of the class
    'setTeams'
);*/

 $hook_array['before_save'][] = Array(
    3,
    'evey time a new team is added to the account record, All related opportunities get the new team',
    'custom/modules/Opportunities/opp_logic_hooks.php',
    'OpportunityLogic', // name of the class
    'crearFolioSolicitud'
);


 $hook_array['before_save'][] = Array(
    4,
    'evey time a new team is added to the account record, All related opportunities get the new team',
    'custom/modules/Opportunities/opp_logic_hooks.php',
    'OpportunityLogic', // name of the class
    'setFechadeCierre'
);

$hook_array['before_save'][] = Array(
    6,
    'Envia los datos de ratifiacion e incremento',
    'custom/modules/Opportunities/opp_logic_hooks.php',
    'OpportunityLogic', // name of the class
    'AsignaCondicionesFinancieras'
);

$hook_array['before_save'][] = Array(
    5,
    'Envia los datos de ratifiacion e incremento',
    'custom/modules/Opportunities/opp_logic_hooks.php',
    'OpportunityLogic', // name of the class
    'creaRatificacion'
);

$hook_array['after_save'][] = Array(
    9,
    'Establece campo tct_estapa_subetapa_txf_c con la concatenación entre campos tct_etapa_ddw_c y estatus_c',
    'custom/modules/Opportunities/opp_logic_hooks.php',
    'OpportunityLogic', // name of the class
    'setEtapaSubetapa'
);

$hook_array['after_save'][] = Array(
    4,
    'evey time a new team is added to the account record, All related opportunities get the new team',
    'custom/modules/Opportunities/opp_logic_hooks.php',
    'OpportunityLogic', // name of the class
    'creaSolicitud'
);

$hook_array['after_save'][] = Array(
    5,
    'Bitacora de cambios',
    'custom/modules/Opportunities/opp_logic_hooks.php',
    'OpportunityLogic', // name of the class
    'bitacora_estatus'
);

$hook_array['before_save'][] = Array(
    11,
    'Actualizar estado de los campos de tipo de producto en la cuenta al avanzar en las etapas de solicitud',
    'custom/modules/Opportunities/opp_logic_hooks.php',
    'OpportunityLogic', // name of the class
    'actualizatipoprod'
);

$hook_array['before_save'][] = Array(
    12,
    'Cancela Crédito SOS cuando Línea Leasing es cancelada',
    'custom/modules/Opportunities/opp_logic_hooks.php',
    'OpportunityLogic', // name of the class
    'cancelaSOS'
);

$hook_array['after_save'][] = Array(
    13,
    'Lanza petición a Unics cuando el producto es Unilease',
    'custom/modules/Opportunities/opp_unilease.php',
    'ProductUnilease', // name of the class
    'setUnicsUnilease'
);

/*
$hook_array['after_save'][] = Array(
    10,
    'Crea linea de credito para producto Uniclick solamente',
    'custom/modules/Opportunities/opp_mambu.php',
    'MambuLogic', // name of the class
    'create_LC'
);
*/

$hook_array['after_save'][] = Array(
    11,
    'Envía notificación a director de la solicitud',
    'custom/modules/Opportunities/opp_notificacion_director.php',
    'NotificacionDirector', // name of the class
    'notificaDirector'
);

$hook_array['after_save'][] = Array(
    12,
    'Envía notificación a asesor asignado cuando se cancela o autoriza solicitud',
    'custom/modules/Opportunities/opp_notificacion_director.php',
    'NotificacionDirector', // name of the class
    'notificaEstatusAsesor'
);

$hook_array['after_save'][] = Array(
    14,
    'Envía integracion Quantico de Leasing, Factoraje y CA',
    'custom/modules/Opportunities/opp_quantico.php',
    'IntegracionQuantico', // name of the class
    'QuanticoIntegracion'
);
/*
$hook_array['after_save'][] = Array(
    15,
    'Actualizacion Quantico de Leasing, Factoraje y CA',
    'custom/modules/Opportunities/opp_quantico.php',
    'IntegracionQuantico', // name of the class
    'QuanticoUpdate'
);
*/
$hook_array['after_save'][] = Array(
    16,
    'Actualizacion de valores para condiciones financieras de quantico',
    'custom/modules/Opportunities/opp_quantico.php',
    'IntegracionQuantico', // name of the class
    'CFQuanticoUpdate'
);

$hook_array['before_save'][] = Array(
    17,
    'Funcion para guardar informacion del usuario logueado asi como asignado a la opp CREADA.',
    'custom/modules/Opportunities/opp_logic_hooks.php',
    'OpportunityLogic', // name of the class
    'InfoMeet'
);

/*$hook_array['after_save'][] = Array(
    18,
    'POST UNION SERVICE',
    'custom/modules/Opportunities/opp_union.php',
    'oppUnionService', // name of the class
    'idResponseUnion'
);*/

$hook_array['before_save'][] = Array(
    24,
    'default check soc',
    'custom/modules/Opportunities/opp_logic_hooks.php',
    'OpportunityLogic', // name of the class
    'SolicitudSOC'
);

$hook_array['before_save'][] = Array(
    25,
    'Establece la misma estructura que el Origen de la cuenta relacionada',
    'custom/modules/Opportunities/opp_logic_hooks.php',
    'OpportunityLogic', // name of the class
    'estableceOrigenDeCuenta');

$hook_array['after_save'][] = Array(
    19,
    'Enviar notificaciones a Vendors',//Just a quick comment about the logic of it
    'custom/modules/Opportunities/opp_notificacion_vendor.php', //path to the logic hook
    'NotificacionVendor', // name of the class
    'notificaVendors' // name of the function.
);

$hook_array['before_save'][] = Array(
   27,
   'Evita guardado de registro en caso de que se relacione una cuenta bloqueada',
   'custom/modules/Opportunities/Check_Bloqueo_Cuenta_Opp.php',
   'Check_Bloqueo_Cuenta_Opp',
   'verifica_cuenta_bloqueada_opp'
);

$hook_array['after_save'][] = Array(
    20,
    'Envía petición para cancelación en Quanticp',
    'custom/modules/Opportunities/opp_logic_hooks.php',
    'OpportunityLogic', // name of the class
    'rechazoQuantico'
 );
