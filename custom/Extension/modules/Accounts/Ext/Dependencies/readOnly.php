<?php
//Variables globales
global $current_user;
global $app_list_strings;
$admin=$current_user->is_admin;
$cac = $current_user->cac_c;
$id = $app_list_strings['tct_persona_generica_list']['accid'];

//Dependencia Edición
$dependencies['Accounts']['readOnly'] = array
(
            'hooks' => array("edit"),
            'trigger' => 'true',
            'triggerFields' => array('id'),
            'onload' => true,
            'actions' => array
	    (
                array
		(
                    'name' => 'ReadOnly',
                    'params' => array
		    (
                        'target' => 'edit_button',
                        'label' => 'LBL_EDIT_BUTTON_LABEL',
                        'value' => 'and(equal($id,"'.$id.'"),equal('.$admin.',0))',
                    ),
                ),
            ),
            'notActions' => array(),
);

//Dependencia CAC - Nivel de satisfacción Leasing
$dependencies['Accounts']['nivel_satisfaccion_c'] = array
(
            'hooks' => array("edit"),
            'trigger' => 'true',
            'triggerFields' => array('cac','id'),
            'onload' => true,
            'actions' => array(
                array(
                    'name' => 'ReadOnly',
                    'params' => array(
                        'target' => 'nivel_satisfaccion_c',
                        'label' => 'LBL_NIVEL_SATISFACCION',
                        'value' => 'equal('.$cac.',0)',
                    ),
                ),
            ),
            'notActions' => array(),
);

$dependencies['Accounts']['fecha_leasing_c'] = array
(
            'hooks' => array("edit"),
            'trigger' => 'true',
            'triggerFields' => array('cac','id'),
            'onload' => true,
            'actions' => array(
                array(
                    'name' => 'ReadOnly',
                    'params' => array(
                        'target' => 'fecha_leasing_c',
                        'label' => 'LBL_FECHA_LEASING',
                        'value' => 'equal('.$cac.',0)',
                    ),
                ),
            ),
            'notActions' => array(),
);

$dependencies['Accounts']['comenta_leasing_c'] = array
(
            'hooks' => array("edit"),
            'trigger' => 'true',
            'triggerFields' => array('cac','id'),
            'onload' => true,
            'actions' => array(
                array(
                    'name' => 'ReadOnly',
                    'params' => array(
                        'target' => 'comenta_leasing_c',
                        'label' => 'LBL_COMENTA_LEASING',
                        'value' => 'equal('.$cac.',0)',
                    ),
                ),
            ),
            'notActions' => array(),
);

//Dependencia CAC - Nivel de satisfacción Factoraje
$dependencies['Accounts']['nivel_satisfaccion_factoring_c'] = array
(
            'hooks' => array("edit"),
            'trigger' => 'true',
            'triggerFields' => array('cac','id'),
            'onload' => true,
            'actions' => array(
                array(
                    'name' => 'ReadOnly',
                    'params' => array(
                        'target' => 'nivel_satisfaccion_factoring_c',
                        'label' => 'LBL_NIVEL_SATISFACCION_FACTORING',
                        'value' => 'equal('.$cac.',0)',
                    ),
                ),
            ),
            'notActions' => array(),
);

$dependencies['Accounts']['fecha_factoraje_c'] = array
(
            'hooks' => array("edit"),
            'trigger' => 'true',
            'triggerFields' => array('cac','id'),
            'onload' => true,
            'actions' => array(
                array(
                    'name' => 'ReadOnly',
                    'params' => array(
                        'target' => 'fecha_factoraje_c',
                        'label' => 'LBL_FECHA_FACTORAJE',
                        'value' => 'equal('.$cac.',0)',
                    ),
                ),
            ),
            'notActions' => array(),
);

$dependencies['Accounts']['comenta_factoraje_c'] = array
(
            'hooks' => array("edit"),
            'trigger' => 'true',
            'triggerFields' => array('cac','id'),
            'onload' => true,
            'actions' => array(
                array(
                    'name' => 'ReadOnly',
                    'params' => array(
                        'target' => 'comenta_factoraje_c',
                        'label' => 'LBL_COMENTA_FACTORAJE',
                        'value' => 'equal('.$cac.',0)',
                    ),
                ),
            ),
            'notActions' => array(),
);

//Dependencia CAC - Nivel de satisfacción CA
$dependencies['Accounts']['nivel_satisfaccion_ca_c'] = array
(
            'hooks' => array("edit"),
            'trigger' => 'true',
            'triggerFields' => array('cac','id'),
            'onload' => true,
            'actions' => array(
                array(
                    'name' => 'ReadOnly',
                    'params' => array(
                        'target' => 'nivel_satisfaccion_ca_c',
                        'label' => 'LBL_NIVEL_SATISFACCION_CA',
                        'value' => 'equal('.$cac.',0)',
                    ),
                ),
            ),
            'notActions' => array(),
);

$dependencies['Accounts']['fecha_ca_c'] = array
(
            'hooks' => array("edit"),
            'trigger' => 'true',
            'triggerFields' => array('cac','id'),
            'onload' => true,
            'actions' => array(
                array(
                    'name' => 'ReadOnly',
                    'params' => array(
                        'target' => 'fecha_ca_c',
                        'label' => 'LBL_FECHA_CA',
                        'value' => 'equal('.$cac.',0)',
                    ),
                ),
            ),
            'notActions' => array(),
);

$dependencies['Accounts']['comenta_ca_c'] = array
(
            'hooks' => array("edit"),
            'trigger' => 'true',
            'triggerFields' => array('cac','id'),
            'onload' => true,
            'actions' => array(
                array(
                    'name' => 'ReadOnly',
                    'params' => array(
                        'target' => 'comenta_ca_c',
                        'label' => 'LBL_COMENTA_CA',
                        'value' => 'equal('.$cac.',0)',
                    ),
                ),
            ),
            'notActions' => array(),
);

// solo lectura si el campo numero exato de empleados es vacio o null
// $dependencies['Accounts']['empleados_c'] = array
// (
//     'hooks' => array("edit"),
//     'trigger' => 'true',
//     'triggerFields' => array('total_empleados_c'),
//     'onload' => true,
//     'actions' => array(
//         array(
//             'name' => 'ReadOnly',
//             'params' => array(
//                 'target' => 'empleados_c',
//                 'label' => 'LBL_EMPLEADOS',
//                 'value' => 'not(equal($total_empleados_c, ""))',
//             ),
//         ),
//     ),
//     'notActions' => array(),
// );

// solo lectura si el campo Ventas Anuales Uni2 esta en true

$dependencies['Accounts']['tct_ano_ventas_ddw_c'] = array
(
    'hooks' => array("edit"),
    'trigger' => 'true',
    'triggerFields' => array('ventas_anuales_uni2_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'tct_ano_ventas_ddw_c',
                'label' => 'LBL_EMPLEADOS',
                'value' => 'equal($ventas_anuales_uni2_c, true)',
            ),
        ),
    ),
    'notActions' => array(),
);

// solo lectura si el campo Ventas Anuales Uni2 esta en true
$dependencies['Accounts']['ventas_anuales_c'] = array
(
    'hooks' => array("edit"),
    'trigger' => 'true',
    'triggerFields' => array('ventas_anuales_uni2_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'ventas_anuales_c',
                'label' => 'LBL_EMPLEADOS',
                'value' => 'equal($ventas_anuales_uni2_c, true)',
            ),
        ),
    ),
    'notActions' => array(),
);

// solo lectura campo rfc
/*$dependencies['Accounts']['rfc_c'] = array
(
    'hooks' => array("edit"),
    'trigger' => 'true',
    'triggerFields' => array('name','id'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'rfc_c',
                'value' => 'true',
            ),
        ),
    ),
    'notActions' => array(),
);*/


// solo lectura campo detalle de grupo empresarial
$dependencies['Accounts']['situacion_gpo_empresa_txt_c'] = array
(
    'hooks' => array("edit"),
    'trigger' => 'true',
    'triggerFields' => array('name','id'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'situacion_gpo_empresa_txt_c',
                'value' => 'true',
            ),
        ),
    ),
    'notActions' => array(),
);

// solo lectura casilla es proveedor, requerimiento dynamics
$dependencies['Accounts']['esproveedor_c'] = array
(
    'hooks' => array("edit"),
    'trigger' => 'true',
    'triggerFields' => array('name','id'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'esproveedor_c',
                'value' => 'true',
            ),
        ),
    ),
    'notActions' => array(),
);
