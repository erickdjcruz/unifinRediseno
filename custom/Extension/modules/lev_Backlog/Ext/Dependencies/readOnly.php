<?php
/**
 * Created by Levementum.
 * User: jgarcia@levementum.com
 * Date: 3/7/2016
 * Time: 3:09 PM
 */
global $current_user;
$editar_backlog=$current_user->editar_backlog_chk_c;

$dependencies['lev_Backlog']['monto_real_logrado_readonly'] = array(
    'hooks' => array("all"),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'monto_real_logrado',
                'label' => 'monto_real_logrado_label',
                'value' => 'true',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['monto_original_readonly'] = array(
    'hooks' => array("all"),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'monto_original',
                'label' => 'monto_original_label',
                'value' => 'true',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['equipo_readonly'] = array(
    'hooks' => array("all"),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'equipo',
                'label' => 'equipo_label',
                'value' => 'true',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['lev_backlog_opportunities_name_readonly'] = array(
    'hooks' => array("all"),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'lev_backlog_opportunities_name',
                'label' => 'lev_backlog_opportunities_name_label',
                'value' => 'true',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['cliente_readonly'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('editar'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'cliente',
                'label' => 'cliente_label',
                'value' => 'not(equal($numero_de_backlog, ""))', //'not(equal($estatus_de_la_operacion, "Activa"))',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['comentario_readonly'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('editar'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'comentario',
                'label' => 'comentario_label',
                'value' => 'equal($editar, false)',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['description_readonly'] = array(
    'hooks' => array("all"),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'description',
                'label' => 'description_label',
                'value' => 'true',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['estatus_de_la_operacion_readonly'] = array(
    'hooks' => array("all"),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'estatus_operacion_c',
                'label' => 'estatus_de_la_operacion_label',
                'value' => 'true',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['etapa_readonly'] = array(
    'hooks' => array("all"),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'etapa_c',
                'label' => 'etapa_label',
                'value' => 'true',
            ),
        ),
    ),
);

// $dependencies['lev_Backlog']['monto_comprometido_readonly'] = array(
//     'hooks' => array("all"),
//     'trigger' => 'true',
//     'triggerFields' => array('editar'),
//     'onload' => true,
//     'actions' => array(
//         array(
//             'name' => 'ReadOnly',
//             'params' => array(
//                 'target' => 'monto_comprometido',
//                 'label' => 'monto_comprometido_label',
//                 'value' => 'not(equal($numero_de_backlog, ""))', //'not(equal($estatus_de_la_operacion, "Activa"))',
//             ),
//         ),
//     ),
// );

$dependencies['lev_Backlog']['name_readonly'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('editar'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'name',
                'label' => 'name_label',
                'value' => 'true',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['numero_de_backlog_readonly'] = array(
    'hooks' => array("all"),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'numero_de_backlog',
                'label' => 'numero_de_backlog_label',
                'value' => 'true',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['numero_de_solicitud_readonly'] = array(
    'hooks' => array("all"),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'numero_de_solicitud',
                'label' => 'numero_de_solicitud_label',
                'value' => 'true',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['producto_readonly'] = array(
    'hooks' => array("all"),
    'onload' => true,
    'trigger' => 'true',
    'triggerFields' => array('id'),
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'producto_c',
                'label' => 'producto_label',
                'value' => 'and(equal(0,'.$editar_backlog.'),not(equal($id,"")))',
            ),
        ),
    ),
);


$dependencies['lev_Backlog']['renta_inicial_comprometida_readonly'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('editar','monto_comprometido'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'renta_inicial_comprometida',
                'label' => 'renta_inicial_comprometida_label',
                'value' => 'or(not(equal($numero_de_backlog, "")),equal($monto_comprometido, "1"))', //'not(equal($estatus_de_la_operacion, "Activa"))',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['ri_final_comprometida_c_readonly'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('editar','monto_final_comprometido_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'ri_final_comprometida_c',
                'label' => 'ri_final_comprometida_c_label',
                'value' => 'equal($monto_final_comprometido_c, "1")',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['renta_inicial_real_readonly'] = array(
    'hooks' => array("all"),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'renta_inicial_real',
                'label' => 'renta_inicial_real_label',
                'value' => 'true',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['tipo_readonly'] = array(
    'hooks' => array("all"),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'tipo_c',
                'label' => 'tipo_label',
                'value' => 'true',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['tipo_de_operacion_readonly'] = array(
    'hooks' => array("all"),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'tipo_operacion_c',
                'label' => 'tipo_de_operacion_label',
                'value' => 'true',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['assigned_user_name_readonly'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('editar'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'assigned_user_name',
                'label' => 'assigned_user_name_label',
                'value' => 'not(equal($estatus_operacion_c, "2"))',
            ),
        ),
    ),
);


$dependencies['lev_Backlog']['monto_comprometido_cancelado_readonly'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('editar'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'monto_comprometido_cancelado',
                'label' => 'monto_comprometido_cancelado_label',
                'value' => 'true',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['renta_inicialcomp_can_readonly'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('editar'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'renta_inicialcomp_can',
                'label' => 'renta_inicialcomp_can_label',
                'value' => 'true',
            ),
        ),
    ),
);


$dependencies['lev_Backlog']['motivo_de_cancelacion_readonly'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('editar'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'motivo_cancelacion_c',
                'label' => 'motivo_de_cancelacion_label',
                'value' => 'true',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['etapa_preliminar_readonly'] = array(
    'hooks' => array("all"),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'etapa_preliminar_c',
                'label' => 'etapa_preliminar_label',
                'value' => 'true',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['progreso_readonly'] = array(     
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('editar'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'progreso',
                'label' => 'progreso_label',
                'value' => 'true',
            ),
        ),
    ),
);


$dependencies['lev_Backlog']['porciento_ri_readonly'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('editar','monto_comprometido'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'porciento_ri',
                'label' => 'porciento_ri_label',
                'value' => 'or(not(equal($numero_de_backlog, "")),equal($monto_comprometido, "1"))',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['region_readonly'] = array(
    'hooks' => array("all"),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'region',
                'label' => 'region_label',
                'value' => 'true',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['progreso_readonly'] = array(
    'hooks' => array("all"),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'progreso',
                'label' => 'progreso_label',
                'value' => 'true',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['anio_readonly'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('editar'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'anio',
                'label' => 'anio_label',
                'value' => 'not(equal($numero_de_backlog, ""))',  //'not(equal($estatus_de_la_operacion, "Activa"))',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['mes_readonly'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('editar'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'mes',
                'label' => 'mes_label',
                'value' => 'not(equal($numero_de_backlog, ""))', //'not(equal($estatus_de_la_operacion, "Activa"))',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['assigned_user_name_readonly'] = array(
    'hooks' => array("all"),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'assigned_user_name',
                'label' => 'assigned_user_name_label',
                'value' => 'true',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['cliente_readonly'] = array(
    'hooks' => array("all"),
    'trigger' => 'not(equal($numero_de_backlog, ""))',
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'cliente',
                'label' => 'cliente_label',
                'value' => 'not(equal($numero_de_backlog, ""))',
            ),
        ),
    ),
);

//--------------------------- Montos de Multi-etapa
$dependencies['lev_Backlog']['monto_prospecto_c_readonly'] = array(
    'hooks' => array("all"),
    'onload' => true,
    'trigger' => 'true',
    'triggerFields' => array('id','producto_c'),
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'monto_prospecto_c',
                'label' => 'monto_prospecto_c_label',
                'value' => 'not(and(equal($producto_c,"2"),equal(1,'.$editar_backlog.')))',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['monto_credito_c_readonly'] = array(
    'hooks' => array("all"),
    'onload' => true,
    'trigger' => 'true',
    'triggerFields' => array('id','producto_c'),
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'monto_credito_c',
                'label' => 'monto_credito_c_label',
                'value' => 'not(and(equal($producto_c,"2"),equal(1,'.$editar_backlog.')))',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['monto_rechazado_c_readonly'] = array(
    'hooks' => array("all"),
    'onload' => true,
    'trigger' => 'true',
    'triggerFields' => array('id','producto_c'),
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'monto_rechazado_c',
                'label' => 'monto_rechazado_c_label',
                'value' => 'not(and(equal($producto_c,"2"),equal(1,'.$editar_backlog.')))',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['monto_sin_solicitud_c_readonly'] = array(
    'hooks' => array("all"),
    'onload' => true,
    'trigger' => 'true',
    'triggerFields' => array('id','producto_c'),
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'monto_sin_solicitud_c',
                'label' => 'monto_sin_solicitud_c_label',
                'value' => 'not(and(equal($producto_c,"2"),equal(1,'.$editar_backlog.')))',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['monto_con_solicitud_c_readonly'] = array(
    'hooks' => array("all"),
    'onload' => true,
    'trigger' => 'true',
    'triggerFields' => array('id','producto_c'),
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'monto_con_solicitud_c',
                'label' => 'monto_con_solicitud_c_label',
                'value' => 'not(and(equal($producto_c,"2"),equal(1,'.$editar_backlog.')))',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['ri_prospecto_c_readonly'] = array(
    'hooks' => array("all"),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'ri_prospecto_c',
                'label' => 'ri_prospecto_c_label',
                'value' => 'true',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['ri_credito_c_readonly'] = array(
    'hooks' => array("all"),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'ri_credito_c',
                'label' => 'ri_credito_c_label',
                'value' => 'true',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['ri_rechazada_c_readonly'] = array(
    'hooks' => array("all"),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'ri_rechazada_c',
                'label' => 'ri_rechazada_c_label',
                'value' => 'true',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['ri_sin_solicitud_c_readonly'] = array(
    'hooks' => array("all"),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'ri_sin_solicitud_c',
                'label' => 'ri_sin_solicitud_c_label',
                'value' => 'true',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['ri_con_solicitud_c_readonly'] = array(
    'hooks' => array("all"),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'ri_con_solicitud_c',
                'label' => 'ri_con_solicitud_c_label',
                'value' => 'true',
            ),
        ),
    ),
);


$dependencies['lev_Backlog']['tasa_c_required'] = array(
    'hooks' => array("all"),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired', //Action type
            'params' => array(
                'target' => 'tasa_c',
                'label' => 'LBL_TASA_C',
                'value' => 'true',
            ),
        ),
    ),
);

$dependencies['lev_Backlog']['monto_devuelta_c_readonly'] = array(
    'hooks' => array("all"),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'monto_devuelta_c',
                'label' => 'monto_devuelta_c_label',
                'value' => 'true',
            ),
        ),
    ),
);
