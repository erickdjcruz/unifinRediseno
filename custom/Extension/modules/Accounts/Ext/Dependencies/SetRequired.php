<?php //cambio de la formula para que el campo sea requerido por persona física y en LEAD.
$dependencies['Accounts']['PrimerNombre_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('tipodepersona_c', 'primernombre_c', 'tipo_registro_cuenta_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired', //Action type
            'params' => array(
                'target' => 'primernombre_c',
                'label' => 'primernombre_c_label',
                'value' => 'not(equal($tipodepersona_c,"Persona Moral"))', //Formula
            ),
        ),
    ),
);
//Actualizacion dependencia apellidos para cuenta Proveedor y Lead.
$dependencies['Accounts']['ApellidoPaterno_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('tipodepersona_c', 'apellidopaterno_c', 'tipo_registro_cuenta_c', 'subtipo_registro_cuenta_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired',
            'params' => array(
                'target' => 'apellidopaterno_c',
                'label' => 'apellidopaterno_c_label',
                'value' => 'not(or(equal($tipodepersona_c,"Persona Moral")))',
            ),
        ),
    ),
);
//Cambio de dependencia para excepciones en algunas reglas de negocio. Adrian Arauz 17/07/18
$dependencies['Accounts']['RazonSocial_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('tipodepersona_c', 'razonsocial_c', 'subtipo_registro_cuenta_c', 'tipo_registro_cuenta_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired', //Action type
            'params' => array(
                'target' => 'razonsocial_c',
                'label' => 'razonsocial_c_label',
                //'value' => 'and(equal($tipodepersona_c,"Persona Moral"),equal($subtipo_cuenta_c,"Interesado"))', //Formula
                //'value' => 'not(or(equal($tipodepersona_c,"Persona Fisica"),equal($tipo_registro_c,"Lead"),equal($subtipo_cuenta_c,"Contactado")))',
                //Cambio de condicion, requerido para todos salvo persona fisica
                'value' => 'equal($tipodepersona_c,"Persona Moral")',
            ),
        ),
    ),
);
//Esta dependencia entra en conflicto con el campo requerido (el RFC) para Cliente y Prospecto con I de Expediente y En Credito
$dependencies['Accounts']['RFC_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    //'triggerFields' => array('tipo_registro_c','subtipo_cuenta_c','rfc_c'),
    'triggerFields' => array('subtipo_registro_cuenta_c', 'rfc_c', 'tipo_registro_cuenta_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired',
            'params' => array(
                'target' => 'rfc_c',
                'label' => 'rfc_c_label',
                //'value' => 'and(not(equal($tipo_registro_c,"Persona" )),not(equal($tipo_registro_c,"Prospecto")))',
                'value' => 'or(equal($tipo_registro_cuenta_c,"3"),equal($subtipo_registro_cuenta_c,"8"),equal($subtipo_registro_cuenta_c,"9"),equal($tipo_registro_cuenta_c,"5"))',
            ),
        ),
    ),
);

/*$dependencies['Accounts']['Profesion_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('tipodepersona_c', 'estatus_c', 'tipo_registro_cuenta_c', 'profesion_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired',
            'params' => array(
                'target' => 'profesion_c',
                'label' => 'profesion_c_label',
                'value' => 'not(or(equal($tipodepersona_c,"Persona Moral"),equal($estatus_c,"Interesado"),equal($tipo_registro_cuenta_c,"1"),equal($subtipo_registro_cuenta_c,"2"),equal($subtipo_registro_cuenta_c,"7")))',
            ),
        ),
    ),
);*/
/*
$dependencies['Accounts']['IVA_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('tipodepersona_c', 'estatus_c', 'esproveedor_c', 'iva_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired',
            'params' => array(
                'target' => 'iva_c',
                'label' => 'iva_c_label',
                'value' => 'or(equal($tipo_registro_cuenta_c,"5"), equal($esproveedor_c,true))',
            ),
        ),
    ),
);
*/
//Se añade la dependencia para subtipo de cuenta Integracion de Expediente. Se añade $subtipo_cuenta_c solamente. Actualizacion Proveedor.
/*	$dependencies['Accounts']['estadocivil_c_required'] = array(
            'hooks' => array("all"),
            'trigger' => 'true',
            'triggerFields' => array('tipodepersona_c', 'estatus_c','subtipo_cuenta_c'),
            'onload' => true,
            'actions' => array(
                    array(
                            'name' => 'SetRequired',
                            'params' => array(
                                    'target' => 'estadocivil_c',
                                    'label' => 'estadocivil_c_label',
                                    //'value' => 'and(not(equal($tipodepersona_c,"Persona Moral")), or(equal($tipo_registro_c,"Cliente"), equal($estatus_c,"Interesado")),equal($subtipo_cuenta_c,"Integracion de Expediente"),equal($tipo_registro_c,"Persona"),equal($tipo_registro_c,"Proveedor"))',
                                'value' => 'not(or(equal($tipodepersona_c,"Persona Moral"),equal($tipo_registro_c,"Lead"),equal($subtipo_cuenta_c,"Contactado"),equal($subtipo_cuenta_c,"Interesado")))',
                            ),
                    ),
            ),
    );*/
//Cambio de dependencia para integracion de expediente, se añade el subtipo de cuenta, cliente y crédito.
$dependencies['Accounts']['Genero_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('tipodepersona_c', 'genero_c', 'subtipo_registro_cuenta_c', 'tipo_registro_cuenta_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired',
            'params' => array(
                'target' => 'genero_c',
                'label' => 'genero_c_label',
                //'value' => 'and(not(equal($tipodepersona_c,"Persona Moral")), or(equal($tipo_registro_c,"Cliente"),equal($tipo_registro_c,"Cliente"),equal($estatus_c,"Interesado"), and(or(equal($subtipo_cuenta_c,"Integracion de Expediente"),equal($subtipo_cuenta_c,"Credito")))))',
                'value' => 'and(or(equal($tipo_registro_cuenta_c,"3"),equal($subtipo_registro_cuenta_c,"8")),or(equal($tipodepersona_c,"Persona Fisica"),equal($tipodepersona_c,"Persona Fisica con Actividad Empresarial")))',

            ),
        ),
    ),
);
//Se modifica la formula para excepciones y con ello no tener una demasiado larga para su requerimiento. Adrian Arauz 17/07/18
$dependencies['Accounts']['fechaNacimiento_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('tipodepersona_c', 'estatus_c', 'tipo_registro_cuenta_c', 'fechadenacimiento_c', 'subtipo_registro_cuenta_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired',
            'params' => array(
                'target' => 'fechadenacimiento_c',
                'label' => 'fechadenacimiento_c_label',
                //'value' => 'and(equal($tipodepersona_c,"Persona Moral"),and(not(equal($tipo_registro_c,"Persona")),or(equal($subtipo_cuenta_c,"Integracion de Expediente"),equal($tipo_registro_c,"Cliente"))))',
                'value' => 'and(or(equal($tipo_registro_cuenta_c,"3"),equal($subtipo_registro_cuenta_c,"8")),or(equal($tipodepersona_c,"Persona Fisica"),equal($tipodepersona_c,"Persona Fisica con Actividad Empresarial")))',
            ),
        ),
    ),
);
//Modificacion 17/07/18 Para exceptuar cuentas de registro.
//Modificacion 02/04/25 Para cambio dynamics.
$dependencies['Accounts']['fechaconstitutiva_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('tipodepersona_c', 'estatus_c', 'tipo_registro_cuenta_c', 'fechaconstitutiva_c', 'subtipo_registro_cuenta_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired',
            'params' => array(
                'target' => 'fechaconstitutiva_c',
                'label' => 'fechaconstitutiva_c_label',
                //'value' => 'and(equal($tipodepersona_c,"Persona Moral"),or(equal($tipo_registro_c,"Cliente"),equal($subtipo_cuenta_c,"Integracion de Expediente")))',
                //'value' => 'not(or(equal($tipodepersona_c,"Persona Fisica"),equal($tipodepersona_c,"Persona Fisica con Actividad Empresarial"),equal($tipo_registro_c,"Lead"),equal($subtipo_cuenta_c,"Contactado"),equal($subtipo_cuenta_c,"Interesado"),equal($tipo_registro_c,"Persona")))',
                'value' => 'and(or(equal($tipo_registro_cuenta_c,"3"),equal($subtipo_registro_cuenta_c,"8")),equal($tipodepersona_c,"Persona Moral"),not(equal($esproveedor_c,"1")))',
            ),
        ),
    ),
);
//Queda a prueba ya que una condicion de visibilidad en Studio.Añadir prospecto e interesado solamente. 16/07/18 actualizado. 17/07/18 Cambio de formula para excepciones.
/*
$dependencies['Accounts']['Pais_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('pais_nacimiento_c', 'estatus_c', 'tipo_registro_cuenta_c', 'subtipo_registro_cuenta_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired',
            'params' => array(
                'target' => 'pais_nacimiento_c',
                'label' => 'pais_nacimiento_c_label',
                //'value' => 'or(equal($tipo_registro_c,"Cliente"),  equal($estatus_c,"Interesado"), equal($tipo_registro_c,"Proveedor"),equal($subtipo_cuenta_c,"Integracion de Expediente"))',
                'value' => 'not(or(equal($tipo_registro_cuenta_c,"1"),equal($subtipo_registro_cuenta_c,"2"),equal($subtipo_registro_cuenta_c,"7"),equal($tipo_registro_cuenta_c,"4")))',
            ),
        ),
    ),
);
*
//Modificacion para  exceptuar, en este caso se eliminará el regimen fiscal. 17/07/18
/*
$dependencies['Accounts']['EstadoNacimiento_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('pais_nacimiento_c', 'estado_nacimiento_c', 'tipodepersona_c', 'estatus_c', 'tipo_registro_cuenta_c', 'subtipo_registro_cuenta_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired',
            'params' => array(
                'target' => 'estado_nacimiento_c',
                'label' => 'estado_nacimiento_c_label',
                //'value' => 'or(equal($tipo_registro_c,"Cliente"),  equal($estatus_c,"Interesado"), equal($tipo_registro_c,"Proveedor"),equal($subtipo_cuenta_c,"Integracion de Expediente"))',
                'value' => 'and(not(equal($pais_nacimiento_c,"")),not(or(equal($tipo_registro_cuenta_c,"1"),equal($subtipo_registro_cuenta_c,"2"),equal($subtipo_registro_cuenta_c,"7"))))',
            ),
        ),
    ),
);
*/
//Actualizacion para la condicion del regimen fiscal para el prospecto/interesado. Adrian Arauz 13/07/18. Se añade cuenta cliente. 17/07/18 se añade excepcion para proveedor.

    // $dependencies['Accounts']['SectorEconomico_required'] = array(
    //         'hooks' => array("all"),
    //         'trigger' => 'true',
    //         'triggerFields' => array('tipo_registro_cuenta_c','sectoreconomico_c','subtipo_registro_cuenta_c'),
    //         'onload' => true,
    //         'actions' => array(
    //                 array(
    //                         'name' => 'SetRequired',
    //                         'params' => array(
    //                                 'target' => 'sectoreconomico_c',
    //                                 'label' => 'sectoreconomico_c_label',
    //                                 'value' => 'or(and(not(equal($tipo_registro_cuenta_c,"1")),not(equal("$subtipo_registro_cuenta_c,"2"),equal($tipo_registro_cuenta_c,"3"))))',
    //                         ),
    //                 ),
    //         ),
    // );

$dependencies['Accounts']['TipoMotivo_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('estatus_c', 'tipodemotivo_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired', //Action type
            'params' => array(
                'target' => 'tipodemotivo_c',
                'label' => 'tipodemotivo_c_label',
                'value' => 'equal($estatus_c,"No Interesado")', //Formula
            ),
        ),
    ),
);

$dependencies['Accounts']['origendelprospecto_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('origendelprospecto_c', 'estatus_c', 'tipo_registro_cuenta_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired',
            'params' => array(
                'target' => 'origendelprospecto_c',
                'label' => 'origendelprospecto_c_label',
                'value' => 'or(equal($tipo_registro_cuenta_c,"1"),equal($tipo_registro_cuenta_c,"2"),equal($tipo_registro_cuenta_c,"3"))',
            ),
        ),
    ),
);


//* CVV Cuestionario de PLD
/*
$dependencies['Accounts']['ctpldorigenrecursocliente_required'] = array(
        'hooks' => array("all"),
        'trigger' => 'true',
        'triggerFields' => array('tipo_registro_c', 'estatus_c'),
        'onload' => true,
        'actions' => array(
                array(
                        'name' => 'SetRequired',
                        'params' => array(
                                'target' => 'ctpldorigenrecursocliente_c',
                                'label' => 'ctpldorigenrecursocliente_c_label',
                                'value' => 'or(equal($tipo_registro_c,"Cliente"), equal($estatus_c,"Interesado"))',
                       ),
                ),
        ),
);
*/

//Ajuste a personas morales
$dependencies['Accounts']['ctpldidproveedorrecursosclie_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('tipo_registro_cuenta_c', 'estatus_c', 'ctpldidproveedorrecursosclie_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired',
            'params' => array(
                'target' => 'ctpldidproveedorrecursosclie_c',
                'label' => 'ctpldidproveedorrecursosclie_c_label',
                'value' => 'or(and(not(equal($tipodepersona_c,"Persona Moral")),equal($tipo_registro_cuenta_c,"3")),and(not(equal($tipodepersona_c,"Persona Moral")),equal($estatus_c,"Interesado")))',
                //'value' => 'or(equal($tipo_registro_c,"Cliente"), equal($estatus_c,"Interesado"))',
            ),
        ),
    ),
);

$dependencies['Accounts']['ctpldidproveedorrecursosson_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('tipo_registro_cuenta_c', 'estatus_c', 'ctpldidproveedorrecursosson_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired',
            'params' => array(
                'target' => 'ctpldidproveedorrecursosson_c',
                'label' => 'ctpldidproveedorrecursosson_c_label',
                'value' => 'or(and(not(equal($tipodepersona_c,"Persona Moral")),equal($tipo_registro_cuenta_c,"3")),and(not(equal($tipodepersona_c,"Persona Moral")),equal($estatus_c,"Interesado")))',
                //'value' => 'or(equal($tipo_registro_c,"Cliente"), equal($estatus_c,"Interesado"))',
            ),
        ),
    ),
);

////Se convierten a requeridos cuando se selecciona el check de alguna pregunta:
/*Desempena o ha desempenado funciones Publicas destacadas en Mexico o en el extranjero,
altos puestos ejecutivos en Empresas Estatales o funciones importantes en Partidos Politicos?*/
/*
$dependencies['Accounts']['ctpldfuncionespublicascargo_c_required'] = array(
        'hooks' => array("all"),
        'trigger' => 'true',
        'triggerFields' => array('ctpldfuncionespublicas_c'),
        'onload' => true,
        'actions' => array(
                array(
                        'name' => 'SetRequired',
                        'params' => array(
                                'target' => 'ctpldfuncionespublicascargo_c',
                                'label' => 'ctpldfuncionespublicascargo_c_label',
                                'value' => 'equal($ctpldfuncionespublicas_c,"1")',
                        ),
                ),
        ),
);
*/

/*Su conyuge o alguno de sus padres, abuelos, hijos, nietos, hermanos, suegros, hijos politicos o cunados,
desempenan o han desempenado Funciones Publicas destacadas en Mexico o en el extranjero,
altos puestos ejecutivos en Empresas Estatales o funciones importantes en partidos politicos*/
$dependencies['Accounts']['ctpldconyugecargo_c_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('ctpldconyuge_c', 'ctpldconyugecargo_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired',
            'params' => array(
                'target' => 'ctpldconyugecargo_c',
                'label' => 'ctpldconyugecargo_c_label',
                'value' => 'equal($ctpldconyuge_c,"1")',
            ),
        ),
    ),
);

/*Alguno de sus Socios o Accionistas desempena o ha desempenado funciones Publicas destacadas en Mexico o en el extranjero,
altos puestos ejecutivos en Empresas Estatales o funciones importantes en Partidos Politicos*/
$dependencies['Accounts']['ctpldaccionistascargo_c_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('ctpldaccionistas_c', 'ctpldaccionistascargo_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired',
            'params' => array(
                'target' => 'ctpldaccionistascargo_c',
                'label' => 'ctpldaccionistascargo_c_label',
                'value' => 'equal($ctpldaccionistas_c,"1")',
            ),
        ),
    ),
);

/*Su conyuge o alguno de los padres, abuelos, hijos, nietos, hermanos, suegros, hijos politicos o cunados de los Socios o Accionistas,
desempena o ha desempenado funciones publicas destacadas en Mexico o en el extranjero, altos puestos ejecutivos en Empresa*/
$dependencies['Accounts']['ctpldaccionistasconyugecargo_c_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('ctpldaccionistasconyuge_c', 'ctpldaccionistasconyugecargo_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired',
            'params' => array(
                'target' => 'ctpldaccionistasconyugecargo_c',
                'label' => 'ctpldaccionistasconyugecargo_c_label',
                'value' => 'equal($ctpldaccionistasconyuge_c,"1")',
            ),
        ),
    ),
);

/*Realizara pagos utilizando otro instrumento monetario*/
$dependencies['Accounts']['imotrodesc_c_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('imotro_c', 'imotrodesc_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired',
            'params' => array(
                'target' => 'imotrodesc_c',
                'label' => 'imotrodesc_c_label',
                'value' => 'equal($imotro_c,"1")',
            ),
        ),
    ),
);

$dependencies['Accounts']['referenciador_c_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('origen_cuenta_c', 'referenciador_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired',
            'params' => array(
                'target' => 'referenciador_c',
                'label' => 'referenciador_c_label',
                'value' => 'equal($origen_cuenta_c,"6")', //Formula
            ),
        ),
    ),
);

$dependencies['Accounts']['referido_cliente_prov_c_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('origen_cuenta_c', 'referido_cliente_prov_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired',
            'params' => array(
                'target' => 'referido_cliente_prov_c',
                'label' => 'referido_cliente_prov_c_label',
                'value' => 'or(equal($origen_cuenta_c,"4"), equal($origen_cuenta_c,"5"),equal($origen_cuenta_c,"8"))',
            ),
        ),
    ),
);

$dependencies['Accounts']['referenciado_agencia_c_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('origendelprospecto_c', 'referenciado_agencia_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired',
            'params' => array(
                'target' => 'referenciado_agencia_c',
                'label' => 'referenciado_agencia_c_label',
                'value' => 'equal($origendelprospecto_c,"Agencia Distribuidor")',
            ),
        ),
    ),
);

$dependencies['Accounts']['evento_marketing_c_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('origendelprospecto_c', 'evento_marketing_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired',
            'params' => array(
                'target' => 'evento_marketing_c',
                'label' => 'evento_marketing_c_label',
                'value' => 'or(equal($origendelprospecto_c,"Eventos Mercadotecnia"),equal($origendelprospecto_c,"Mercadotecnia"))', //Formula
            ),
        ),
    ),
);


//////////***************************   BEGIN: READ ONLY   ******************************////////////////////////////

$dependencies['Accounts']['pep_c_lista_negra_c_readonly'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'pep_c',
                'value' => 'true',
            ),
        ),
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'lista_negra_c',
                'value' => 'true',
            ),
        ),
    ),
);

/*
    $dependencies['Accounts']['idprospecto_readonly'] = array(
            'hooks' => array("all"),
            'trigger' => 'true',
            'triggerFields' => array('estatus_c'),
            'onload' => true,
            'actions' => array(
                    array(
                            'name' => 'ReadOnly',
                            'params' => array(
                                    'target' => 'idprospecto_c',
                                    'label' => 'idprospecto_c_label',
                                    'value' => 'not(equal($estatus_c,"Make it Read Only"))',
                            ),
                    ),
            ),
    );
*/

$dependencies['Accounts']['idcliente_c_readonly'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('idcliente_c', 'tipo_registro_cuenta_c', 'estatus_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'idcliente_c',
                'label' => 'idcliente_c_label',
                'value' => 'true',
            ),
        ),
    ),
);

$dependencies['Accounts']['reus_readonly'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'reus_c',
                'label' => 'LBL_REUS',
                'value' => 'true',
            ),
        ),
    ),
);
$dependencies['Accounts']['promotorleasing_c_readonly'] = array(
    'hooks' => array("edit"),
    'trigger' => 'true',
    'triggerFields' => array('idcliente_c', 'promotorleasing_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'promotorleasing_c',
                //'label' => 'LBL_promotorleasing_c',
                'value' => 'not(equal($idcliente_c,""))',
            ),
        ),
    ),
);

$dependencies['Accounts']['promotorfactoraje_c_readonly'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('idcliente_c', 'promotorfactoraje_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'promotorfactoraje_c',
                //'label' => 'LBL_promotorfactoraje_c',
                'value' => 'not(equal($idcliente_c,""))',
            ),
        ),
    ),
);

$dependencies['Accounts']['promotorcredit_c_readonly'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('idcliente_c', 'promotorcredit_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'promotorcredit_c',
                'label' => 'LBL_promotorcredit_c',
                'value' => 'not(equal($idcliente_c,""))',
            ),
        ),
    ),
);

$dependencies['Accounts']['promotoruniclick_c_readonly'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('idcliente_c', 'promotorcredit_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'promotoruniclick_c',
                'label' => 'LBL_promotoruniclick_c',
                'value' => 'not(equal($idcliente_c,""))',
            ),
        ),
    ),
);

$dependencies['Accounts']['promotorrm_c_readonly'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('idcliente_c', 'promotorrm_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'promotorrm_c',
                'label' => 'LBL_PROMOTORRM_C',
                'value' => 'not(equal($idcliente_c,""))',
            ),
        ),
    ),
);


$dependencies['Accounts']['referencia_bancaria_c_readonly'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('idcliente_c', 'tipo_registro_cuenta_c', 'estatus_c', 'referencia_bancaria_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'referencia_bancaria_c',
                'label' => 'referencia_bancaria_c_label',
                'value' => 'true',
            ),
        ),
    ),
);

//////////***************************   END: READ ONLY   ******************************////////////////////////////


/** Carlos Zaragoza: Al cambiar tipo de registro a cliente bloquear estatus seleccionando integraci�n de expediente*/
/*
$dependencies['Accounts']['estatus_c_readonly'] = array(
    'hooks' => array("edit"),
    'trigger' => 'true',
    'triggerFields' => array('estatus_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'estatus_c',
                'label' => 'estatus_c_label',
                'value' => 'equal($tipo_registro_c,"Cliente")',
            ),
        ),
    ),
);
*/

$dependencies['Accounts']['alta_proveedor_c_readonly'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('idcliente_c', 'tipo_registro_cuenta_c', 'estatus_c', 'alta_proveedor_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'ReadOnly',
            'params' => array(
                'target' => 'alta_proveedor_c',
                'label' => 'alta_proveedor_c_label',
                'value' => 'true',
            ),
        ),
    ),
);
//Dependencia para el apellido materno en el tipo de cuenta Prospecto e Interesado. Nuevo cambio en la formula para desabilitar el requerido en Lead y Contactado. OK
// $dependencies['Accounts']['ApellidoMaterno_required'] = array(
//     'hooks' => array("all"),
//     'trigger' => 'true',
//     'triggerFields' => array('tipodepersona_c', 'tipo_registro_cuenta_c', 'subtipo_registro_cuenta_c', 'apellidomaterno_c'),
//     'onload' => true,
//     'actions' => array(
//         array(
//             'name' => 'SetRequired',
//             'params' => array(
//                 'target' => 'apellidomaterno_c',
//                 'label' => 'apellidomaterno_c_label',
//                 //'value' => 'and(equal($tipodepersona_c,"Persona Fisica"), equal($tipo_registro_c,"Prospecto"), equal($subtipo_cuenta_c,"Interesado"))',
//                 'value' => 'and(or(equal($tipo_registro_cuenta_c,"3"),equal($subtipo_registro_cuenta_c,"8"),equal($subtipo_registro_cuenta_c,"7")),or(equal($tipodepersona_c,"Persona Fisica"),equal($tipodepersona_c,"Persona Fisica con Actividad Empresarial")))',
//             ),
//         ),
//     ),
// );

//Dependencia para el sector economico para el tipo de persona fisica en registro prospecto e interesado
//Adrian Arauz 13/07/18

// $dependencies['Accounts']['SectorEconomico_required'] = array(
//     'hooks' => array("all"),
//     'trigger' => 'true',
//     'triggerFields' => array('tipo_registro_cuenta_c', 'sectoreconomico_c', 'subtipo_registro_cuenta_c'),
//     'onload' => true,
//     'actions' => array(
//         array(
//             'name' => 'SetRequired',
//             'params' => array(
//                 'target' => 'sectoreconomico_c',
//                 'label' => 'sectoreconomico_c_label',
//                 'value' => 'not(or(equal($tipo_registro_cuenta_c,"1"),equal($subtipo_registro_cuenta_c,"2"),equal($tipo_registro_cuenta_c,"5"),equal($tipo_registro_cuenta_c,"4")))',
//             ),
//         ),
//     ),
// );
//Dependencia para ventas anuales para Prospecto con Integracion de expediente. Actualizacion 17/07/18 Añadiendo excepciones para mejorar la fórmula.
$dependencies['Accounts']['Ventas_anuales_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('tipo_registro_cuenta_c', 'ventas_anuales_c', 'subtipo_registro_cuenta_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired',
            'params' => array(
                'target' => 'ventas_anuales_c',
                'label' => 'ventas_anuales_c_label',
                //'value' => 'or(equal($subtipo_cuenta_c,"Integracion de Expediente"),equal($tipo_registro_c,"Cliente"))',
                'value' => 'not(or(equal($tipo_registro_cuenta_c,"1"),equal($subtipo_registro_cuenta_c,"2"),equal($subtipo_registro_cuenta_c,"7"),equal($tipo_registro_cuenta_c,"5"),equal($tipo_registro_cuenta_c,"4")))',
            ),
        ),
    ),
);
//Dependencia para Activo Fijo en Prospecto con Integracion de Expediente. modificacion de la formula para añadir excepciones solamente. 17/07/18
/*$dependencies['Accounts']['activo_fijo_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('tipo_registro_cuenta_c', 'activo_fijo_c', 'subtipo_registro_cuenta_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired',
            'params' => array(
                'target' => 'activo_fijo_c',
                'label' => 'activo_fijo_c_label',
                //'value' => 'or(equal($subtipo_cuenta_c,"Integracion de Expediente"),equal($tipo_registro_c,"Cliente"))',
                'value' => 'not(or(equal($tipo_registro_cuenta_c,"1"),equal($subtipo_registro_cuenta_c,"2"),equal($subtipo_registro_cuenta_c,"7"),equal($tipo_registro_cuenta_c,"5"),equal($tipo_registro_cuenta_c,"4")))',
            ),
        ),
    ),
);*/
//Dependencia para potencial de la cuenta en Cliente e integración de expediente. Actualizacion 17/07/18 para añadir excepciones.
$dependencies['Accounts']['Potencial_cuenta_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('tipo_registro_cuenta_c', 'activo_fijo_c', 'subtipo_registro_cuenta_c', 'potencial_cuenta_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired',
            'params' => array(
                'target' => 'potencial_cuenta_c',
                'label' => 'potencial_cuenta_c_label',
                //'value' => 'or(equal($subtipo_cuenta_c,"Integracion de Expediente"),equal($tipo_registro_c,"Cliente"))',
                'value' => 'not(or(equal($tipo_registro_cuenta_c,"1"),equal($subtipo_registro_cuenta_c,"2"),equal($subtipo_registro_cuenta_c,"7"),equal($tipo_registro_cuenta_c,"5"),equal($tipo_registro_cuenta_c,"4")))',
            ),
        ),
    ),
);
//Dependencia de ZonaGeografica para prospecto e integracion de expediente. Actualizacion 17/07/18 para excepciones
//Dependencia de ZonaGeografica dynamics 02/04/2025
$dependencies['Accounts']['Zona_Geografica_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('tipo_registro_cuenta_c', 'zonageografica_c', 'subtipo_registro_cuenta_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired',
            'params' => array(
                'target' => 'zonageografica_c',
                'label' => 'zonageografica_c_label',
                //'value' => 'and(equal($subtipo_cuenta_c,"Integracion de Expediente"),equal($tipo_registro_c,"Cliente"))',
                'value' => 'not(or(equal($esproveedor_c,"1"),equal($tipo_registro_cuenta_c,"4"),equal($tipo_registro_cuenta_c,"5"),equal($subtipo_registro_cuenta_c,"2"),equal($subtipo_registro_cuenta_c,"7"),equal($tipo_registro_cuenta_c,"Lead")))',
            ),
        ),
    ),
);
//Dependencia para IFE/Pasaporte con persona fisica, integración de expediente. Adrian Arauz/ 16/07/18. Actrualizacion debido a choque
/*$dependencies['Accounts']['Pasaporte_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('tipo_registro_cuenta_c', 'ifepasaporte_c', 'subtipo_registro_cuenta_c', 'tipodepersona_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired',
            'params' => array(
                'target' => 'ifepasaporte_c',
                'label' => 'ifepasaporte_c_label',
                //'value' => 'and(or(equal($subtipo_cuenta_c,"Integracion de Expediente"),equal($tipo_registro_c,"Cliente"),equal($subtipo_cuenta_c,"Credito")),equal($tipodepersona_c,"Persona Fisica"))',
                'value' => 'and(or(equal($tipo_registro_cuenta_c,"3"),equal($subtipo_registro_cuenta_c,"8")),or(equal($tipodepersona_c,"Persona Fisica"),equal($tipodepersona_c,"Persona Fisica con Actividad Empresarial")))',
            ),
        ),
    ),
);*/
//Dependencia para Curp Requerido en Persona fisica e integracion de expediente. Actualizacion de excepciones.
$dependencies['Accounts']['Curp_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('tipo_registro_cuenta_c', 'curp_c', 'subtipo_registro_cuenta_c', 'tipodepersona_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired',
            'params' => array(
                'target' => 'curp_c',
                'label' => 'curp_c_label',
                'value' => 'and(or(equal($tipo_registro_cuenta_c,"3"),equal($subtipo_registro_cuenta_c,"8")),or(equal($tipodepersona_c,"Persona Fisica"),equal($tipodepersona_c,"Persona Fisica con Actividad Empresarial")))',
            ),
        ),
    ),
);
//Dependencia para el estado civil. Adrian Arauz 16/07/18
$dependencies['Accounts']['Estado_Civil_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('tipo_registro_cuenta_c', 'estadocivil_c', 'subtipo_registro_cuenta_c', 'tipodepersona_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired',
            'params' => array(
                'target' => 'estadocivil_c',
                'label' => 'estadocivil_c_label',
                'value' => 'and(or(equal($tipo_registro_cuenta_c,"3"),equal($subtipo_registro_cuenta_c,"8")),or(equal($tipodepersona_c,"Persona Fisica"),equal($tipodepersona_c,"Persona Fisica con Actividad Empresarial")))',
            ),
        ),
    ),
);
//Dependencia para regimen patrimonial en Cliente, Prospecto con integracion a expediente. Pendiente despues de actualización.
$dependencies['Accounts']['Regimen_Patrimonial_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('tipo_registro_cuenta_c', 'regimenpatrimonial_c', 'subtipo_registro_cuenta_c', 'tipodepersona_c', 'estadocivil_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired',
            'params' => array(
                'target' => 'regimenpatrimonial_c',
                'label' => 'regimenpatrimonial_c_label',
                'value' => 'equal($estadocivil_c,"Casado")',
            ),
        ),
    ),
);
//Profesion. Actualizacion de formula para excepciones en algunos registros. 17/07/18
/*$dependencies['Accounts']['Profesion_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('tipo_registro_cuenta_c', 'profesion_c', 'subtipo_registro_cuenta_c', 'tipodepersona_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired',
            'params' => array(
                'target' => 'profesion_c',
                'label' => 'profesion_c_label',
                'value' => 'and(or(equal($tipo_registro_cuenta_c,"3"),equal($subtipo_registro_cuenta_c,"8")),or(equal($tipodepersona_c,"Persona Fisica"),equal($tipodepersona_c,"Persona Fisica con Actividad Empresarial")))',
            ),
        ),
    ),
);*/
//Dependencia para numero de empleados para prospecto/interesado. A la espera de cambios en la vista registro para visualizar si funciona*
$dependencies['Accounts']['Numero_Empleados_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('tipo_registro_cuenta_c', 'empleados_c', 'subtipo_registro_cuenta_c', 'tipodepersona_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired',
            'params' => array(
                'target' => 'empleados_c',
                'label' => 'empleados_c_label',
                'value' => 'or(equal($subtipo_registro_cuenta_c,"7"),equal($tipo_registro_cuenta_c,"2"))',
            ),
        ),
    ),
);

// $dependencies['Accounts']['MacroSector'] = array(
//     'hooks' => array("all"),
//     'trigger' => 'true',
//     'triggerFields' => array('tipo_registro_cuenta_c','tct_macro_sector_ddw_c','subtipo_registro_cuenta_c'),
//     'onload' => true,
//     'actions' => array(
//         array(
//             'name' => 'SetRequired',
//             'params' => array(
//                 'target' => 'tct_macro_sector_ddw_c',
//                 'label' => 'LBL_TCT_MACRO_SECTOR_DDW',
//                 'value' => 'or(equal($tipo_registro_cuenta_c,"3"),equal($subtipo_registro_cuenta_c,"7"),equal($subtipo_registro_cuenta_c,"8"),equal($subtipo_registro_cuenta_c,"9"),equal($tipo_registro_cuenta_c,"5"))',
//             ),
//         ),
//     ),
// );

$dependencies['Accounts']['NoregistroCNBV_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('tipodepersona_c', 'tct_cpld_pregunta_u2_txf_c', 'tct_cpld_pregunta_u1_ddw_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired',
            'params' => array(
                'target' => 'tct_cpld_pregunta_u2_txf_c',
                'label' => 'tct_cpld_pregunta_u2_txf_c_label',
                'value' => 'and(equal($tipodepersona_c,"Persona Moral"),equal($tct_cpld_pregunta_u1_ddw_c,"Si"))',
            ),
        ),
    ),
);

$dependencies['Accounts']['ClavePizarra_required'] = array(
    'hooks' => array("all"),
    'trigger' => 'true',
    'triggerFields' => array('tipodepersona_c', 'tct_cpld_pregunta_u4_txf_c', 'tct_cpld_pregunta_u3_ddw_c'),
    'onload' => true,
    'actions' => array(
        array(
            'name' => 'SetRequired',
            'params' => array(
                'target' => 'tct_cpld_pregunta_u4_txf_c',
                'label' => 'tct_cpld_pregunta_u4_txf_c_label',
                'value' => 'and(equal($tipodepersona_c,"Persona Moral"),equal($tct_cpld_pregunta_u3_ddw_c,"Si"))',
            ),
        ),
    ),
);
