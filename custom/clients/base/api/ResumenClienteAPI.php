<?php
/**
 * Created by PhpStorm.
 * User: salvadorlopez, AF
 * Date: 29/01/18
 * Time: 15:54
 */
if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

require_once("custom/clients/base/api/DisposicionesDWH.php");
class ResumenClienteAPI extends SugarApi
{
    public function registerApiRest()
    {
        return array(
            //GET
            'GetResumen' => array(
                //request type
                'reqType' => 'GET',
                //set authentication
                'noLoginRequired' => true,
                //endpoint path
                'path' => array('ResumenCliente', '?'),
                //endpoint variables
                'pathVars' => array('name', 'id'),
                //method to call
                'method' => 'getResumenPersona',
                //short help string to be displayed in the help documentation
                'shortHelp' => 'An example of a GET endpoint',
                //long help to be displayed in the help documentation
                'longHelp' => '',
            ),
        );
    }

    /**
     * Method to be used for my MyEndpoint/GetExample endpoint
     */
    public function getResumenPersona($api, $args)
    {
        //$GLOBALS['log']->fatal("ResumenPersona: Genera  petición -- ");
        //Recupera Cliente
        $id_cliente = $args['id'];
        $beanPersona = BeanFactory::getBean("Accounts", $id_cliente);

        //Obtiene variables globales
        global $app_list_strings, $current_user, $db;

        //Define tipo de producto principal
        $producto =  $current_user->tipodeproducto_c;
        /* Valores de $producto
        1 = LEASING
        2 = CREDITO SIMPLE
        3 = CREDITO AUTOMOTRIZ
        4 = FACTORAJE
        5 = LINEA CREDITO SIMPLE
		    6 = UNICLICK
        */

        //Define colores:
        $Azul = "#21618C";
        $Rojo = "#C0392B";
        $Amarillo = "#F1C40F";
        $Blanco = "#ffffff";

        ############################
        ## Genera estructura default
        $arr_principal = array();
        //colores
        $arr_principal['colores'] = array(
            "azul" => $Azul,
            "rojo" => $Rojo,
            "amarillo" => $Amarillo
        );
        /*Victor Martinez Lopez
        *20-Septiembre-2018
        *Nuevos Campos de de noticias
        */
        //Noticias General
        $arr_principal['noticia_general']=array(
            "noticia"=>""
            );
        //Noticias Macro Sector
        $arr_principal['noticia_macro_sector']=array(
            "noticia"=>""
            );
        //Noticias de Región
        $arr_principal['noticia_region']=array(
            "noticia"=>""
            );
        //Datos Clave
        $arr_principal['datos_clave']=array(
            "dato_clave"=>""
            );
        //General
        $arr_principal['general_cliente'] = array(
            "estado_cliente_c" => "No definido",
            "tipo" => "No definido",
            "cliente_desde"=>"",
            "segmento" => "Sin Segmento",
            "cobranza" => "Sin Clasificar",
            "sector_economico" => "Sin Especificar",
            "grupo_empresarial" => "Sin Grupo empresarial",
            "nivel_satisfaccion" => "Sin Clasificar",
            "tiene_condicion" => false,
            "color" => $Azul);

        $arr_principal['contactos'] = array(
            "nombre_negocios" => "",
            "puesto_negocios"=>"",
            "telefono_negocios" => "",
            "correo_negocios" => "",
            "nombre_secundario" => "",
            "puesto_secundario"=>"",
            "telefono_secundario" => "",
            "correo_secundario" => "",
            "tiene_negocio" => false,
            "tiene_secundario" =>false,
            "rel_activa_negocio" => false,
        );
        //Leasing
        $arr_principal['leasing'] = array("linea_autorizada" => "",
            "estatus_atencion"=>"",
            "tipo_cuenta"=>"",
            "subtipo_cuenta"=>"",
            "fecha_vencimiento" => "",//más proxima
            "fecha_completa_vencimiento" => "",
            "linea_disponible" => "",
            "potencial" => "",
            "fecha_pago" => "",//mas lejana
            "anexos_activos" => 0,
            "anexos_historicos" => 0,
            "nivel_satisfaccion" => "Sin Clasificar",
            "promotor" => "",
            "color" => "",
            "promotorId" => "",
            "ultima_cita" => "",
            "ultima_llamada" => "",
            "estatusxproducto" => "",
            "estatus_linea" => "",
            "vencimiento_anexo_final" => "",
            "vencimiento_siguiente_anexo" => "",
            "fecha_proximo_pago" => "",
            "mensualidad_activa" => "",
            "dias_atraso" => 0,
            "muestra_producto" => false,
            "es_prospecto_cliente" => false,
            "tiene_linea_autorizada" => false,
            "tiene_anexo_liberado" => false,
            "tiene_anexos" => false
          );
        //Factoraje
        $arr_principal['factoring'] = array("linea_autorizada" => "",
            "estatus_atencion"=>"",
            "tipo_cuenta"=>"",
            "subtipo_cuenta"=>"",
            "fecha_vencimiento" => "",
            "fecha_completa_vencimiento" => "",
            "linea_disponible" => "",
            "fecha_pago" => "",
            "cesiones_activas" => 0,
            "cesiones_historicas" => 0,
            "nivel_satisfaccion" => "Sin Clasificar",
            "promotor" => "",
            "color" => "",
            "promotorId" => "",
            "ultima_cita" => "",
            "ultima_llamada" => "",
            "estatusxproducto" => "",
            "estatus_linea" => "",
            "vencimiento_anexo_final" => "",
            "vencimiento_siguiente_anexo" => "",
            "fecha_proximo_pago" => "",
            "mensualidad_activa" => "",
            "dias_atraso" => 0,
            "tipo_linea" => 0,
            "muestra_producto" => false,
            "es_prospecto_cliente" => false,
            "tiene_linea_autorizada" => false,
            "tiene_cesiones_liberado" => false,
            "tiene_cesiones" => false
          );
        //Crédito automotriz
        $arr_principal['credito_auto'] = array("linea_autorizada" => "",
            "estatus_atencion"=>"",
            "tipo_cuenta"=>"",
            "subtipo_cuenta"=>"",
            "fecha_vencimiento" => "",
            "fecha_completa_vencimiento" => "",
            "linea_disponible" => "",
            "fecha_pago" => "",
            "contratos_activos" => 0,
            "contratos_historicos" => 0,
            "nivel_satisfaccion" => "Sin Clasificar",
            "promotor" => "",
            "color" => "",
            "promotorId" => "",
            "ultima_cita" => "",
            "ultima_llamada" => "",
            "estatusxproducto" => "",
            "estatus_linea" => "",
            "vencimiento_anexo_final" => "",
            "vencimiento_siguiente_anexo" => "",
            "fecha_proximo_pago" => "",
            "mensualidad_activa" => "",
            "dias_atraso" => 0,
            "muestra_producto" => false,
            "es_prospecto_cliente" => false,
            "tiene_linea_autorizada" => false,
            "tiene_contrato_liberado" => false,
            "tiene_contratos" => false
          );
        //Fleet
        $arr_principal['fleet'] = array("linea_aproximada" => "",
            "estatus_atencion"=>"",
            "estatusxproducto" => "",
            "tipo_cuenta"=>"",
            "subtipo_cuenta"=>"",
            "numero_vehiculos" => "",
            "promotor" => "",
            "linea_aproximada" => "",
            "cobranza" => "",
            "color" => "",
            "muestra_producto" => false
          );
        //Uniclick
        $arr_principal['uniclick'] = array("linea_autorizada" => "",
            "estatus_atencion"=>"",
            "tipo_cuenta"=>"",
            "subtipo_cuenta"=>"",
            "fecha_vencimiento"=>"",
            "linea_disponible" => "",
            "promotor" => "",
            "color" => "",
            "muestra_producto" => false
          );
        //Unifactor
        $arr_principal['unifactor'] = array("linea_autorizada" => "",
            "estatus_atencion"=>"",
            "tipo_cuenta"=>"",
            "subtipo_cuenta"=>"",
            "fecha_vencimiento"=>"",
            "linea_disponible" => "",
            "promotor" => "",
            "color" => "",
            "muestra_producto" => false
          );
        //Seguros
        $arr_principal['seguros'] = array(
            "total" => 0,
            "ganadas" => 0,
            "proceso" => 0,
            "prima" => 0,
            "ingreso" => 0,
            "color" => $Azul,
            "promotorId" => "",
            "ultima_cita" => "",
            "ultima_llamada" => "",
            "op_ganadas" => "",
            "op_presentacion" => "",
            "op_cotizando" => "",
            "op_no_cotizado" => "",
            "op_no_ganada" => "",
            "dias_atraso" => "",
            "cobranza" => "",
            "kam_asignado" => "",
            "muestra_producto" => false,
            "tiene_seguros" => false,
            "tiene_ganada" => false
        );
        //Crédito Simple
        $arr_principal['credito_simple'] = array("linea_autorizada" => "",
            "estatus_atencion"=>"",
            "tipo_cuenta"=>"",
            "subtipo_cuenta"=>"",
            "fecha_vencimiento" => "",//más proxima
            "fecha_completa_vencimiento" => "",
            "linea_disponible" => "",
            "potencial" => "",
            "fecha_pago" => "",//mas lejana
            "anexos_activos" => 0,
            "anexos_historicos" => 0,
            "nivel_satisfaccion" => "Sin Clasificar",
            "promotor" => "",
            "color" => "",
            "promotorId" => "",
            "ultima_cita" => "",
            "ultima_llamada" => "",
            "estatusxproducto" => "",
            "estatus_linea" => "",
            "vencimiento_anexo_final" => "",
            "vencimiento_siguiente_anexo" => "",
            "fecha_proximo_pago" => "",
            "mensualidad_activa" => "",
            "dias_atraso" => 0,
            "muestra_producto" => false,
            "es_prospecto_cliente" => false,
            "tiene_linea_autorizada" => false,
        );
        //Unifactor
        $arr_principal['tarjeta_credito'] = array("linea_autorizada" => "",
            "estatus_atencion"=>"",
            "tipo_cuenta"=>"",
            "subtipo_cuenta"=>"",
            "fecha_vencimiento"=>"",
            "linea_disponible" => "",
            "promotor" => "",
            "color" => "",
            "muestra_producto" => false
        );
        //Historial de contactos
        $arr_principal['historial_contactos'] = array(
            "ultima_cita" => "",
            "ultima_llamada" => "",
            "citas" => 0,
            "llamadas" => 0,
            "emails" => 0,
            "fecha_completa_cita" => "",
            "fecha_completa_llamada" => "",
            "estatus_atencion" => "",
            "color" => $Azul
        );
        //Alertas
        $arr_principal['alertas'] = array(
            "color" => $Azul,
            "total" => 0
            //"alerta" => array()
        );
        //Clasificacion Sectorial INEGI
        $arr_principal['inegi'] = array(
            "inegi_clase" => "",
            "inegi_subrama" => "",
            "inegi_rama" => "",
            "inegi_subsector" => "",
            "inegi_sector" => "",
            "inegi_macro" => "",
            "inegi_acualiza_uni2" => ""
            // "inegi_descripcion" => "",
        );
        //Clasificacion Sectorial PB
        $arr_principal['pb'] = array(
            "pb_division" => "",
            "pb_grupo" => "",
            "pb_clase" => ""
        );
        //ASIGNACION AUTOMATICA
        $arr_principal['asignacion_automatica']=array(
            "asignacion_activa_c" => "",
            "id_director_region_aprobar_c" => "",
            "id_asesor_solicita_c" => ""
        );
        //ACTIVIDAD ECONOMICA SAT
        $arr_principal['actividad_economica_sat']=array(
            "id_actividad_economica_sat_c" => "",
            "actividad_economica_sat_c" => ""
        );
        //String operaciones
        $operaciones_ids = "'".$id_cliente."'";

        ############################
        ## Recupera información de Persona
        ############################
        if($beanPersona){
            //General
            $arr_principal['general_cliente']['tipo'] = $beanPersona->tct_tipo_subtipo_txf_c;  // tipo_general_c
            //Se establece atributo para mostrar campos de Gpo Empresarial solo si el tipo Gral es Prospecto o Cliente
            if($beanPersona->tipo_registro_cuenta_c=='2' || $beanPersona->tipo_registro_cuenta_c=='3'){
                $arr_principal['general_cliente']['muestra_gpo_empresarial']='1';
            }
            //Recupera cliente desde
            $queryClienteDesde = "select date_created
                        from accounts_audit
                        where parent_id='{$id_cliente}'
                        and
                        (
                        	(
                        		field_name='tipo_registro_cuenta_c'
                        		and after_value_string = '3'
                            )
                        	or
                            (
                        		field_name='subtipo_registro_cuenta_c'
                        		and after_value_string = '18'
                            )
                        )
                        order by date_created asc
                        limit 1;";
            $resultCD = $db->query($queryClienteDesde);
            $timedateCD = new TimeDate();
            while ($row = $db->fetchByAssoc($resultCD)) {
                $arr_principal['general_cliente']['cliente_desde'] = date_format($timedateCD->fromDb($row['date_created']), "d/m/Y");
            }
            $arr_principal['general_cliente']['cliente_desde'] = !empty($beanPersona->fecha_cliente_c) ? $beanPersona->fecha_cliente_c : $arr_principal['general_cliente']['cliente_desde'];
            $arr_principal['general_cliente']['cliente_desde'] = empty($arr_principal['general_cliente']['cliente_desde']) ? 'Sin identificar' : $arr_principal['general_cliente']['cliente_desde'];
            $arr_principal['general_cliente']['segmento'] = $beanPersona->segmento_c;
            //$arr_principal['general_cliente']['cobranza'] = $beanPersona->cobranza_c;
            $arr_principal['general_cliente']['sector_economico'] = isset($app_list_strings['sectoreconomico_list'][$beanPersona->sectoreconomico_c]) ? $app_list_strings['sectoreconomico_list'][$beanPersona->sectoreconomico_c] : '';
            $arr_principal['general_cliente']['grupo_empresarial'] = $beanPersona->parent_name;
            $arr_principal['general_cliente']['situacion_grupo_empresarial'] = $beanPersona->situacion_gpo_empresa_txt_c;
            $arr_principal['general_cliente']['nivel_satisfaccion'] = isset($app_list_strings['nivel_satisfaccion_list'][$beanPersona->nivel_satisfaccion_c]) ? $app_list_strings['nivel_satisfaccion_list'][$beanPersona->nivel_satisfaccion_c] : '';
            $arr_principal['general_cliente']['asesorRM'] = $beanPersona->promotorrm_c;
            $arr_principal['general_cliente']['actividad_economica'] = isset($app_list_strings['actividad_list'][$beanPersona->actividadeconomica_c]) ? $app_list_strings['actividad_list'][$beanPersona->actividadeconomica_c] : '';
            $arr_principal['general_cliente']['ventas_anuales'] = $beanPersona->ventas_anuales_c;
            $arr_principal['general_cliente']['anio_ventas_anuales'] = $beanPersona->tct_ano_ventas_ddw_c;
            $arr_principal['general_cliente']['fecha_cons_nac'] = ($beanPersona->tipodepersona_c != "Persona Moral") ? $beanPersona->fechadenacimiento_c:$beanPersona->fechaconstitutiva_c;
            $arr_principal['general_cliente']['num_empleados'] = $beanPersona->total_empleados_c;

            //Promotores
            /*$arr_principal['leasing']['promotor']=$beanPersona->promotorleasing_c;
            $arr_principal['factoring']['promotor']=$beanPersona->promotorfactoraje_c;
            $arr_principal['credito_auto']['promotor']=$beanPersona->promotorcredit_c;
            $arr_principal['fleet']['promotor']=$beanPersona->promotorfleet_c;
            $arr_principal['credito_sos']['promotor']=$beanPersona->promotorleasing_c;
            $arr_principal['uniclick']['promotor']=$beanPersona->promotoruniclick_c;
            $arr_principal['unilease']['promotor']=$beanPersona->promotorleasing_c;
            */
            $arr_principal['rm']['promotor']=$beanPersona->promotorrm_c;

            //Nivel satisfacción
            $arr_principal['leasing']['nivel_satisfaccion']=$beanPersona->nivel_satisfaccion_c;
            $arr_principal['factoring']['nivel_satisfaccion']=$beanPersona->nivel_satisfaccion_factoring_c;
            $arr_principal['credito_auto']['nivel_satisfaccion']=$beanPersona->nivel_satisfaccion_ca_c;
            //Estatus atención
            $arr_principal['historial_contactos']['estatus_atencion']=$beanPersona->tct_status_atencion_ddw_c;
        }

        ############################
        ## Recupera y procesa información de Productos
        ############################
        if ($beanPersona->load_relationship('accounts_uni_productos_1')) {
            //Recupera Productos
            $relateProduct = $beanPersona->accounts_uni_productos_1->getBeans($beanPersona->id,array('disable_row_level_security' => true));
            $cobranzaGeneral = '';
            foreach ($relateProduct as $product) {
                //Recupera valores por producto
                $tipoCuenta = $product->tipo_cuenta;
                $subtipoCuenta = $product->subtipo_cuenta;
                $tipoProducto = $product->tipo_producto;
                $statusProducto = $product->estatus_atencion;
                $cobranza = $product->cobranza_c;
                $cobranzaGeneral = (empty($cobranzaGeneral) && !empty($cobranza)) ? $cobranza : $cobranzaGeneral;
                $asignado = $product->assigned_user_name;
                $asignadoId = $product->assigned_user_id;
                $vencimiento_anexo_final = $product->vencimiento_anexo_final_c;
                $vencimiento_siguiente_anexo = $product->vencimiento_anexo_prox_c;
                $fecha_proximo_pago = $product->proxima_mensualidad_c;
                $mensualidad_activa = $product->mensualidad_activa_c;
                $registros_activos = $product->registros_activos_c;
                $registros_historicos = $product->registros_historicos_c;
                $numero_disposiciones=$product->numero_disposiciones_c;
                $dias_atraso = $product->dias_atraso_c;
                $estatusxproducto = $app_list_strings['status_management_list'][$product->status_management_c] .' / '.$app_list_strings['razon_list'][$product->razon_c];
                if ($statusProducto == '' || $statusProducto == null){
                    $statusProducto = '0'; //0 = vacio
                }

                switch ($tipoProducto) {

                    case '1': //Leasing
                        $arr_principal['leasing']['tipo_cuenta'] = $tipoCuenta;
                        $arr_principal['leasing']['subtipo_cuenta'] = $subtipoCuenta;
                        $arr_principal['leasing']['estatus_atencion'] = $statusProducto;
                        $arr_principal['leasing']['cobranza'] = $cobranza;
                        $arr_principal['leasing']['estatusxproducto'] = $estatusxproducto;
                        $arr_principal['leasing']['promotor']= $asignado;
                        $arr_principal['leasing']['promotorId']= $asignadoId;
                        $arr_principal['leasing']['vencimiento_anexo_final'] = $vencimiento_anexo_final;
                        $arr_principal['leasing']['vencimiento_siguiente_anexo'] = $vencimiento_siguiente_anexo;
                        $arr_principal['leasing']['mensualidad_activa'] = $mensualidad_activa;
                        $arr_principal['leasing']['dias_atraso'] = $dias_atraso;
                        $arr_principal['leasing']['fecha_proximo_pago'] = $fecha_proximo_pago;
                        $arr_principal['leasing']['anexos_activos'] = $registros_activos;
                        $arr_principal['leasing']['anexos_historicos'] = $registros_historicos;
                        $arr_principal['leasing']['muestra_producto'] = ($tipoCuenta == '2' || $tipoCuenta == '3') ? true : false; //Valida que sea usuario valido o tipo de cuenta sea Cliente. Se remplaza validación: ($this->usuarioValido($asignadoId) || $tipoCuenta == '3')
                        //$arr_principal['leasing']['muestra_producto'] = true;
                        $arr_principal['leasing']['es_prospecto_cliente'] = ($tipoCuenta == '2' || $tipoCuenta == '3') ? true : false; //Valida tipo de cuenta sea Prospecto o Cliente
                        $arr_principal['leasing']['tiene_anexo_liberado'] = ($registros_activos > 0 ) ? true : false; //Valida que tenga anexos activos
                        $arr_principal['leasing']['tiene_anexos'] = ($registros_activos > 0 || $registros_historicos > 0 || $numero_disposiciones > 0 ) ? true : false; //Valida que tenga anexos activos o tiene por lo menos una disposición
                        //$arr_principal['leasing']['tiene_anexos'] = true; //Valida que tenga anexos activos

                        $arr_principal['leasing']['numero_disposiciones'] = $numero_disposiciones;
                        $arr_principal['leasing']['disposiciones'] = '';
                        break;
                    case '3': //Credito-Automotriz
                        $arr_principal['credito_auto']['tipo_cuenta'] = $tipoCuenta;
                        $arr_principal['credito_auto']['subtipo_cuenta'] = $subtipoCuenta;
                        $arr_principal['credito_auto']['estatus_atencion'] = $statusProducto;
                        $arr_principal['credito_auto']['cobranza'] = $cobranza;
                        $arr_principal['credito_auto']['estatusxproducto'] = $estatusxproducto;
                        $arr_principal['credito_auto']['promotor']=$asignado;
                        $arr_principal['credito_auto']['promotorId']=$product->assigned_user_id;
                        $arr_principal['credito_auto']['vencimiento_anexo_final'] = $vencimiento_anexo_final;
                        $arr_principal['credito_auto']['vencimiento_siguiente_anexo'] = $vencimiento_siguiente_anexo;
                        $arr_principal['credito_auto']['mensualidad_activa'] = $mensualidad_activa;
                        $arr_principal['credito_auto']['dias_atraso'] = $dias_atraso;
                        $arr_principal['credito_auto']['fecha_proximo_pago'] = $fecha_proximo_pago;
                        $arr_principal['credito_auto']['contratos_activos'] = $registros_activos;
                        $arr_principal['credito_auto']['contratos_historicos'] = $registros_historicos;
                        $arr_principal['credito_auto']['muestra_producto'] = ($tipoCuenta == '2' || $tipoCuenta == '3') ? true : false; //Valida que sea usuario valido o tipo de cuenta sea Cliente
                        $arr_principal['credito_auto']['es_prospecto_cliente'] = ($tipoCuenta == '2' || $tipoCuenta == '3') ? true : false; //Valida tipo de cuenta sea Prospecto o Cliente
                        $arr_principal['credito_auto']['tiene_contrato_liberado'] = ($registros_activos > 0 ) ? true : false; //Valida que tenga anexos activos
                        $arr_principal['credito_auto']['tiene_contratos'] = ($registros_activos > 0 || $registros_historicos > 0 ) ? true : false; //Valida que tenga anexos activos
                        break;
                    case '4': //Factoraje
                        $arr_principal['factoring']['tipo_cuenta'] = $tipoCuenta;
                        $arr_principal['factoring']['subtipo_cuenta'] = $subtipoCuenta;
                        $arr_principal['factoring']['estatus_atencion'] = $statusProducto;
                        $arr_principal['factoring']['cobranza'] = $cobranza;
                        $arr_principal['factoring']['estatusxproducto'] = $estatusxproducto;
                        $arr_principal['factoring']['promotor']=$asignado;
                        $arr_principal['factoring']['promotorId']=$product->assigned_user_id;
                        $arr_principal['factoring']['vencimiento_anexo_final'] = $vencimiento_anexo_final;
                        $arr_principal['factoring']['vencimiento_siguiente_anexo'] = $vencimiento_siguiente_anexo;
                        $arr_principal['factoring']['mensualidad_activa'] = $mensualidad_activa;
                        $arr_principal['factoring']['dias_atraso'] = $dias_atraso;
                        $arr_principal['factoring']['fecha_proximo_pago'] = $fecha_proximo_pago;
                        $arr_principal['factoring']['cesiones_activas'] = $registros_activos;
                        $arr_principal['factoring']['cesiones_historicas'] = $registros_historicos;
                        $arr_principal['factoring']['muestra_producto'] = ($tipoCuenta == '2' || $tipoCuenta == '3') ? true : false; //Valida que sea usuario valido o tipo de cuenta sea Cliente
                        $arr_principal['factoring']['es_prospecto_cliente'] = ($tipoCuenta == '2' || $tipoCuenta == '3') ? true : false; //Valida tipo de cuenta sea Prospecto o Cliente
                        $arr_principal['factoring']['tiene_cesiones_liberado'] = ($registros_activos > 0 ) ? true : false; //Valida que tenga anexos activos
                        $arr_principal['factoring']['tiene_cesiones'] = ($registros_activos > 0 || $registros_historicos > 0 ) ? true : false; //Valida que tenga anexos activos
                        break;
                    case '6': //Fleet
                        $arr_principal['fleet']['tipo_cuenta'] = $tipoCuenta;
                        $arr_principal['fleet']['subtipo_cuenta'] = $subtipoCuenta;
                        $arr_principal['fleet']['estatus_atencion'] = $statusProducto;
                        $arr_principal['fleet']['cobranza'] = $cobranza;
                        $arr_principal['fleet']['estatusxproducto'] = $estatusxproducto;
                        $arr_principal['fleet']['promotor']=$asignado;
                        $arr_principal['fleet']['promotorId']=$product->assigned_user_id;
                        $arr_principal['fleet']['muestra_producto'] = ($tipoCuenta == '2' || $tipoCuenta == '3') ? true : false; //Valida que sea usuario valido o tipo de cuenta sea Cliente
                        break;
                    case '7': //Credito SOS
                        $arr_principal['credito_sos']['estatus_atencion'] = $statusProducto;
                        $arr_principal['credito_sos']['cobranza'] = $cobranza;
                        $arr_principal['credito_sos']['promotor']=$asignado;
                        $arr_principal['credito_sos']['promotorId']=$product->assigned_user_id;
                        $arr_principal['credito_sos']['vencimiento_anexo_final'] = $vencimiento_anexo_final;
                        $arr_principal['credito_sos']['vencimiento_siguiente_anexo'] = $vencimiento_siguiente_anexo;
                        $arr_principal['credito_sos']['mensualidad_activa'] = $mensualidad_activa;
                        $arr_principal['credito_sos']['dias_atraso'] = $dias_atraso;
                        $arr_principal['credito_sos']['fecha_proximo_pago'] = $fecha_proximo_pago;
                        $arr_principal['credito_sos']['muestra_producto'] = ($tipoCuenta == '2' || $tipoCuenta == '3') ? true : false; //Valida que sea usuario valido o tipo de cuenta sea Cliente
                        $arr_principal['credito_sos']['es_prospecto_cliente'] = ($tipoCuenta == '2' || $tipoCuenta == '3') ? true : false; //Valida tipo de cuenta sea Prospecto o Cliente
                        $arr_principal['credito_sos']['tiene_anexo_liberado'] = ($registros_activos > 0) ? true : false; //Valida que tenga anexos activos
                        break;
                    case '8': //Uniclick
                        $arr_principal['uniclick']['tipo_cuenta'] = $tipoCuenta;
                        $arr_principal['uniclick']['subtipo_cuenta'] = $subtipoCuenta;
                        $arr_principal['uniclick']['estatus_atencion'] = $statusProducto;
                        $arr_principal['uniclick']['cobranza'] = $cobranza;
                        $arr_principal['uniclick']['estatusxproducto'] = $estatusxproducto;
                        $arr_principal['uniclick']['promotor']=$asignado;
                        $arr_principal['uniclick']['promotorId']=$product->assigned_user_id;
                        $arr_principal['uniclick']['vencimiento_anexo_final'] = $vencimiento_anexo_final;
                        $arr_principal['uniclick']['vencimiento_siguiente_anexo'] = $vencimiento_siguiente_anexo;
                        $arr_principal['uniclick']['mensualidad_activa'] = $mensualidad_activa;
                        $arr_principal['uniclick']['dias_atraso'] = $dias_atraso;
                        $arr_principal['uniclick']['fecha_proximo_pago'] = $fecha_proximo_pago;
                        $arr_principal['uniclick']['muestra_producto'] = ($tipoCuenta == '2' || $tipoCuenta == '3') ? true : false; //Valida que sea usuario valido o tipo de cuenta sea Cliente
                        $arr_principal['uniclick']['es_prospecto_cliente'] = ($tipoCuenta == '2' || $tipoCuenta == '3') ? true : false; //Valida tipo de cuenta sea Prospecto o Cliente
                        $arr_principal['uniclick']['tiene_anexo_liberado'] = ($registros_activos > 0) ? true : false; //Valida que tenga anexos activos
                        break;
                    case '9': //Uniclick
                        //$arr_principal['unilease']['tipo_cuenta'] = $tipoCuenta;
                        //$arr_principal['unilease']['subtipo_cuenta'] = $subtipoCuenta;
                        //$arr_principal['unilease']['estatus_atencion'] = $statusProducto;
                        $arr_principal['unilease']['cobranza'] = $cobranza;
                        break;
                    case '10': //Seguros
                        $arr_principal['seguros']['cobranza'] = $cobranza;
                        break;
                    case '14': //Tarjeta Crédito
                        $arr_principal['tarjeta_credito']['cobranza'] = $cobranza;
                        $arr_principal['tarjeta_credito']['tipo_cuenta'] = $tipoCuenta;
                        $arr_principal['tarjeta_credito']['subtipo_cuenta'] = $subtipoCuenta;
                        $arr_principal['tarjeta_credito']['estatus_atencion'] = $statusProducto;
                        $arr_principal['tarjeta_credito']['cobranza'] = $cobranza;
                        $arr_principal['tarjeta_credito']['estatusxproducto'] = $estatusxproducto;
                        $arr_principal['tarjeta_credito']['promotor']= $asignado;
                        $arr_principal['tarjeta_credito']['promotorId']= $asignadoId;
                        break;
                    case '2': //Crédito Simple
                        $arr_principal['credito_simple']['tipo_cuenta'] = $tipoCuenta;
                        $arr_principal['credito_simple']['subtipo_cuenta'] = $subtipoCuenta;
                        break;
                    default:
                        break;
                }
            }
            $arr_principal['general_cliente']['cobranza'] = $cobranzaGeneral;
        }

        ############################
        ## Recupera y procesa operaciones asociadas
        ############################
        if ($beanPersona->load_relationship('opportunities')) {
            //Recupera operaciones
            $relatedBeans = $beanPersona->opportunities->getBeans($beanPersona->id,array('disable_row_level_security' => true));

            //Línea autorizada
            $linea_aut_leasing = 0;
            $linea_aut_factoring = 0;
            $linea_aut_credito_aut = 0;
            $linea_aut_sos = 0;

            //Línea disponible
            $linea_disp_leasing = 0;
            $linea_disp_factoring = 0;
            $linea_disp_credito_aut = 0;
            $linea_disp_sos = 0;
            $linea_disp_factor = 0;
            $linea_disp_cs = 0;

            //Linea aproximada fleet
            $linea_aprox_fleet=0;
            $numero_vehiculos_fleet=0;
            $linea_aprox_unilease=0;

            //Fecha de vencimiento
            $vencimiento_leasing = '';//date("Y-m-d");
            $vencimiento_factoring ='';//date("Y-m-d");
            //$vencimiento_cauto = date("Y-m-d");
            $vencimiento_cauto = "";
            $vencimiento_sos = "";
            $vencimiento_sos='';
            $vencimiento_uniclick ='';
            $vencimiento_unilease ='';
            $vencimiento_unifactor = '';
            $vencimiento_cs = '';
            //Estatus línea
            $fecha_actual = date('Y-m-d');
            $estatus_linea_leasing = 'Vencida';
            $estatus_linea_factoring = 'Vencida';
            $estatus_linea_credito_auto = 'Vencida';

            //$str_productos_contratados='';
            $arr_productos_contratados=[];
            //Recorre operaciones
            foreach ($relatedBeans as $opps) {

                //Obtener productos de las solicitudes que se encuentran como Cliente con Linea Autorizada
                if($opps->tct_etapa_ddw_c=='CL' && $opps->estatus_c=='N'){
                    $arr_productos_contratados[]=$app_list_strings['tipo_producto_list'][$opps->tipo_producto_c];
                }

                /**
                 *Filtro para operaciones
                 * Operación = LÍNEA DE CRÉDITO || tipo_operacion_c = 2
                 * Tipo de Solicitud = Línea Nueva || tipo_de_operacion_c = LINEA_NUEVA
                 */
                if ($opps->tipo_operacion_c == 2 && $opps->tipo_de_operacion_c == "LINEA_NUEVA" && $opps->tct_opp_estatus_c == 1) {

                    //Agrega Operaciones asociadas
                    $operaciones_ids .= ",'$opps->id'";
                    //Control para leasing
                  if ($opps->tipo_producto_c == 1 /*&& $opps->negocio_c == 5 && ($opps->producto_financiero_c == 0 || $opps->producto_financiero_c == "")*/) {
                        $arr_principal['leasing']['tiene_linea_autorizada'] = ($opps->tct_etapa_ddw_c=='CL' && $opps->estatus_c=='N') ? true : $arr_principal['leasing']['tiene_linea_autorizada'];
                        $linea_aut_leasing += floatval($opps->monto_c);
                        $linea_disp_leasing += floatval($opps->amount);
                        /* Cambiar por otro cmpo de fecha con valores fecha_estimada_cierre_c*/
                        /*********************************/

                        if (!empty($opps->vigencialinea_c)) {
                            //Establece fecha de vencimiento
                            $dateVL = $opps->vigencialinea_c;
                            $timedateVL = Date($dateVL);

                            //Compara fechas
                            if ($dateVL > $vencimiento_leasing || empty($vencimiento_leasing)) {
                                $vencimiento_leasing = $dateVL;
                            }
                            $estatus_linea_leasing = ($timedateVL>=$fecha_actual) ? 'Vigente' : $estatus_linea_leasing;

                            //Agrega valores en arreglo de resultados
                            $arr_principal['leasing']['linea_autorizada'] = $linea_aut_leasing;
                            $arr_principal['leasing']['fecha_vencimiento'] = $vencimiento_leasing;
                            $arr_principal['leasing']['linea_disponible'] = $linea_disp_leasing;
                            $arr_principal['leasing']['potencial'] = "";
                            $arr_principal['leasing']['fecha_pago'] = "";
                            $arr_principal['leasing']['estatus_linea'] = $estatus_linea_leasing;
                        }
                    }

                    //Control para factoring
                    if ($opps->tipo_producto_c == 4 && $opps->negocio_c == 4 && ($opps->producto_financiero_c == 0 || $opps->producto_financiero_c == "")) {
                        $arr_principal['factoring']['tiene_linea_autorizada'] = ($opps->tct_etapa_ddw_c=='CL' && $opps->estatus_c=='N') ? true : $arr_principal['factoring']['tiene_linea_autorizada'];
                        $arr_principal['factoring']['tipo_linea'] = $opps->f_tipo_linea_c;

                        $linea_aut_factoring += floatval($opps->monto_c);
                        $linea_disp_factoring += floatval($opps->amount);
                        /* Cambiar por otro cmpo de fecha con valores fecha_estimada_cierre_c*/
                        /*********************************/

                        if (!empty($opps->vigencialinea_c)) {
                            //Establece fecha de vencimiento
                            $dateVF = $opps->vigencialinea_c;
                            $timedateVF = Date($dateVF);

                            //Compara fechas
                            if ($dateVF > $vencimiento_factoring || empty($vencimiento_factoring)) {
                                $vencimiento_factoring = $dateVF;
                            }
                            $estatus_linea_factoring = ($timedateVF>=$fecha_actual) ? 'Vigente' : $estatus_linea_factoring;

                            //Agrega valores en arreglo de resultados
                            $arr_principal['factoring']['linea_autorizada'] = $linea_aut_factoring;
                            $arr_principal['factoring']['fecha_vencimiento'] = $vencimiento_factoring;
                            $arr_principal['factoring']['linea_disponible'] = $linea_disp_factoring;
                            $arr_principal['factoring']['fecha_pago'] = "";
                            $arr_principal['factoring']['estatus_linea'] = $estatus_linea_factoring;
                        }
                    }

                    //Control para crédito auto
                    $fecha_val = date("Y-m-d");
                    if ($opps->tipo_producto_c == 3) {
                        $arr_principal['credito_auto']['tiene_linea_autorizada'] = ($opps->tct_etapa_ddw_c=='CL' && $opps->estatus_c=='N') ? true : $arr_principal['credito_auto']['tiene_linea_autorizada'];
                        $linea_aut_credito_aut += floatval($opps->monto_c);
                        $linea_disp_credito_aut += floatval($opps->amount);
                        /* Cambiar por otro cmpo de fecha con valores fecha_estimada_cierre_c*/
                        /*********************************/

                        if (!empty($opps->vigencialinea_c)) {
                            //Establece fecha de vencimiento
                            $dateVC = $opps->vigencialinea_c;
                            $timedateVC = Date($dateVC);
                            if ($vencimiento_cauto == "") {
                                $vencimiento_cauto = $opps->vigencialinea_c;
                            }

                            //Compara fechas
                            if ($dateVC < $vencimiento_cauto || empty($vencimiento_cauto)) {
                                $vencimiento_cauto = $dateVC;
                            }
                            $estatus_linea_credito_auto = ($timedateVC>=$fecha_actual) ? 'Vigente' : $estatus_linea_credito_auto;

                            //Agrega valores en arreglo de resultados
                            $arr_principal['credito_auto']['linea_autorizada'] = $linea_aut_credito_aut;
                            $arr_principal['credito_auto']['fecha_vencimiento'] = $vencimiento_cauto;
                            $arr_principal['credito_auto']['linea_disponible'] = $linea_disp_credito_aut;
                            $arr_principal['credito_auto']['fecha_pago'] = "";
                            $arr_principal['credito_auto']['estatus_linea'] = $estatus_linea_credito_auto;
                        }
                    }

                    // Control para crédito sos
                    if ($opps->producto_financiero_c == 40) {
                        $linea_aut_sos += floatval($opps->monto_c);
                        $linea_disp_sos += floatval($opps->amount);
                        /* Cambiar por otro cmpo de fecha con valores fecha_estimada_cierre_c*/
                        /*********************************/

                        if (!empty($opps->vigencialinea_c)) {
                            //Establece fecha de vencimiento
                            $dateVL = $opps->vigencialinea_c;
                            $timedateVL = Date($dateVL);

                            //Compara fechas
                            if ($dateVL > $vencimiento_sos || empty($vencimiento_sos)) {
                                $vencimiento_sos = $dateVL;
                            }

                            //Agrega valores en arreglo de resultados
                            $arr_principal['credito_sos']['linea_autorizada'] = $linea_aut_sos;
                            $arr_principal['credito_sos']['fecha_vencimiento'] = $vencimiento_sos;
                            $arr_principal['credito_sos']['linea_disponible'] = $linea_disp_sos;
                            $arr_principal['credito_sos']['fecha_pago'] = "";

                        }
                    }

                    //control para Uniclick
                    if ($opps->negocio_c == 10 && $opps->estatus_c != 'K') {
                        $arr_principal['uniclick']['muestra_producto'] = true;
                        $linea_aprox_uniclick += floatval($opps->monto_c);
                        $linea_disp_sos += floatval($opps->amount);
                        /* Cambiar por otro cmpo de fecha con valores fecha_estimada_cierre_c*/
                        /*********************************/

                        if (!empty($opps->vigencialinea_c)) {
                						//Establece fecha de vencimiento
                						$dateVL = $opps->vigencialinea_c;
                						$timedateVL = Date($dateVL);

                						//Compara fechas
                						if ($dateVL > $vencimiento_uniclick || empty($vencimiento_uniclick)) {
                						   $vencimiento_uniclick = $dateVL;
                						}

                						$arr_principal['uniclick']['linea_autorizada'] = $linea_aprox_uniclick;
                						$arr_principal['uniclick']['fecha_vencimiento'] = $vencimiento_uniclick;
                						$arr_principal['uniclick']['linea_disponible'] = $linea_disp_sos;
                        }
                    }

                    //control para Unifactor
                    if ($opps->producto_financiero_c == 50 && $opps->estatus_c != 'K') {
                        $arr_principal['unifactor']['muestra_producto'] = true;
                        $linea_aprox_unifactor += floatval($opps->monto_c);
                        $linea_disp_factor += floatval($opps->amount);
                        /* Cambiar por otro cmpo de fecha con valores fecha_estimada_cierre_c*/
                        /*********************************/
                        if (!empty($opps->vigencialinea_c)) {
                						//Establece fecha de vencimiento
                						$dateVL = $opps->vigencialinea_c;
                						$timedateVL = Date($dateVL);
                						//Compara fechas
                						if ($dateVL > $vencimiento_unifactor || empty($vencimiento_unifactor)) {
                						   $vencimiento_unifactor = $dateVL;
                						}
                						$arr_principal['unifactor']['linea_autorizada'] = $linea_aprox_unifactor;
                						$arr_principal['unifactor']['fecha_vencimiento'] = $vencimiento_unifactor;
                						$arr_principal['unifactor']['linea_disponible'] = $linea_disp_factor;
                        }
                    }

                    //control para Crédito simple | No cuenta solicitudes de negocio Uniclick
                    if ($opps->tipo_producto_c == 2 && $opps->estatus_c != 'K' && $opps->negocio_c != 10 ) {
                        $arr_principal['credito_simple']['tiene_linea_autorizada'] = ($opps->tct_etapa_ddw_c=='CL' && $opps->estatus_c=='N') ? true : $arr_principal['credito_simple']['tiene_linea_autorizada'];
                        $arr_principal['credito_simple']['muestra_producto'] = true;
                        $linea_aprox_cs += floatval($opps->monto_c);
                        $linea_disp_cs += floatval($opps->amount);

                        //Establece fecha de vencimiento
            						$dateVCS = $opps->vigencialinea_c;
            						$timedateVCS = Date($dateVCS);
            						//Compara fechas
            						if ($dateVCS > $vencimiento_cs || empty($vencimiento_cs)) {
            						   $vencimiento_cs = $dateVCS;
            						}
                        $estatus_linea_cs = ($timedateVCS>=$fecha_actual) ? 'Vigente' : $estatus_linea_cs;
            						$arr_principal['credito_simple']['linea_autorizada'] = $linea_aprox_cs;
            						$arr_principal['credito_simple']['fecha_vencimiento'] = $vencimiento_cs;
            						$arr_principal['credito_simple']['linea_disponible'] = $linea_disp_cs;
                        $arr_principal['credito_simple']['estatus_linea'] = $estatus_linea_cs;

                    }

                    //Control para Unilease
                    if ($opps->producto_financiero_c == 41 && $opps->estatus_c != 'K') {
                        $linea_aprox_unilease += floatval($opps->monto_c);
                        $linea_disp_sos += floatval($opps->amount);
                        /* Cambiar por otro cmpo de fecha con valores fecha_estimada_cierre_c*/
                        /*********************************/

                        if (!empty($opps->vigencialinea_c)) {
                            //Establece fecha de vencimiento
                            $dateVL = $opps->vigencialinea_c;
                            $timedateVL = Date($dateVL);

                            //Compara fechas
                            if ($dateVL > $vencimiento_unilease || empty($vencimiento_unilease)) {
                                $vencimiento_unilease = $dateVL;
                            }

                            $arr_principal['unilease']['linea_autorizada'] = $linea_aprox_unilease;
                            $arr_principal['unilease']['fecha_vencimiento'] = $vencimiento_unilease;
                            $arr_principal['unilease']['linea_disponible'] = $linea_disp_sos;
                        }
                    }

                    //control para Tarjeta de Crédito
                    if ($opps->tipo_producto_c == 14) {
                        $arr_principal['tarjeta_credito']['muestra_producto'] = true;
                        $linea_aprox_tc += floatval($opps->monto_c);
                        $linea_disp_tc += floatval($opps->amount);
                        /* Cambiar por otro cmpo de fecha con valores fecha_estimada_cierre_c*/
                        /*********************************/
                        if (!empty($opps->vigencialinea_c)) {
                						//Establece fecha de vencimiento
                						$dateVTC = $opps->vigencialinea_c;
                						$timedateVTC = Date($dateVTC);
                						//Compara fechas
                						if ($dateVTC > $vencimiento_tc || empty($vencimiento_tc)) {
                						   $vencimiento_tc = $dateVTC;
                						}
                            $estatus_linea_tc = ($timedateVTC>=$fecha_actual) ? 'Vigente' : $estatus_linea_tc;
                						$arr_principal['tarjeta_credito']['linea_autorizada'] = $linea_aprox_tc;
                						$arr_principal['tarjeta_credito']['fecha_vencimiento'] = $vencimiento_tc;
                						$arr_principal['tarjeta_credito']['linea_disponible'] = $linea_disp_tc;
                            $arr_principal['tarjeta_credito']['estatus_linea'] = $estatus_linea_tc;
                        }
                    }

                }
                // Control para fleet
                if ($opps->tipo_producto_c == 6 && $opps->estatus_c != 'K') {
                    $linea_aprox_fleet += floatval($opps->monto_c);
                    $numero_vehiculos_fleet += floatval($opps->tct_numero_vehiculos_c);

                    $arr_principal['fleet']['linea_aproximada'] = $linea_aprox_fleet;
                    $arr_principal['fleet']['numero_vehiculos'] = $numero_vehiculos_fleet;

                }

            }
            //Se aplica substring para eliminar la última coma
            $arr_principal['general_cliente']['productos_contratados'] = (count($arr_productos_contratados) > 0) ? implode(", ",array_unique($arr_productos_contratados)): '';
            // substr($str_productos_contratados, 0, -2);
            if($arr_principal['general_cliente']['productos_contratados'] != ''){
                $arr_principal['general_cliente']['show_if_linea_autorizada'] = '1';
            }
        }

        ############################
        ## Recupera y procesa reuniones asociadas
        ############################
        if ($beanPersona->load_relationship('meetings')) {
            $query = "select m.assigned_user_id, max(m.date_start) last_date_start
                      from meetings m
                      where
                        m.parent_id='".$id_cliente."'
                        and m.deleted=0
                        and m.status = 'Held'
                        and m.date_start is not null
                      group by m.assigned_user_id
                      ;";

            $resultQ = $db->query($query);
            $timedateFR = new TimeDate();
            while ($row = $db->fetchByAssoc($resultQ)) {
                //Setea ultima cita por asesor asignado
                $arr_principal['leasing']['ultima_cita'] = ($row['assigned_user_id'] == $arr_principal['leasing']['promotorId']) ? date_format($timedateFR->fromDb($row['last_date_start']), "d/m/Y") : $arr_principal['leasing']['ultima_cita'];
                $arr_principal['factoring']['ultima_cita'] = ($row['assigned_user_id'] == $arr_principal['factoring']['promotorId']) ? date_format($timedateFR->fromDb($row['last_date_start']), "d/m/Y") : $arr_principal['factoring']['ultima_cita'];
                $arr_principal['credito_auto']['ultima_cita'] = ($row['assigned_user_id'] == $arr_principal['credito_auto']['promotorId']) ? date_format($timedateFR->fromDb($row['last_date_start']), "d/m/Y") : $arr_principal['credito_auto']['ultima_cita'];
                $arr_principal['fleet']['ultima_cita'] = ($row['assigned_user_id'] == $arr_principal['fleet']['promotorId']) ? date_format($timedateFR->fromDb($row['last_date_start']), "d/m/Y") : $arr_principal['fleet']['ultima_cita'];
                $arr_principal['credito_sos']['ultima_cita'] = ($row['assigned_user_id'] == $arr_principal['credito_sos']['promotorId']) ? date_format($timedateFR->fromDb($row['last_date_start']), "d/m/Y") : $arr_principal['credito_sos']['ultima_cita'];
                $arr_principal['uniclick']['ultima_cita'] = ($row['assigned_user_id'] == $arr_principal['uniclick']['promotorId']) ? date_format($timedateFR->fromDb($row['last_date_start']), "d/m/Y") : $arr_principal['uniclick']['ultima_cita'];
                $arr_principal['unilease']['ultima_cita'] = ($row['assigned_user_id'] == $arr_principal['unilease']['promotorId']) ? date_format($timedateFR->fromDb($row['last_date_start']), "d/m/Y") : $arr_principal['unilease']['ultima_cita'];
                $arr_principal['seguros']['ultima_cita'] = ($row['assigned_user_id'] == $arr_principal['seguros']['promotorId']) ? date_format($timedateFR->fromDb($row['last_date_start']), "d/m/Y") : $arr_principal['seguros']['ultima_cita'];
                $arr_principal['credito_simple']['ultima_cita'] = ($row['assigned_user_id'] == $arr_principal['credito_simple']['promotorId']) ? date_format($timedateFR->fromDb($row['last_date_start']), "d/m/Y") : $arr_principal['credito_simple']['ultima_cita'];
            }
        }

        ############################
        ## Recupera y procesa llamadas asociadas
        ############################
        if ($beanPersona->load_relationship('calls')) {
            $queryC = "select c.assigned_user_id, max(c.date_start) last_date_start
                      from calls c
                      where
                        c.parent_id='".$id_cliente."'
                        and c.deleted=0
                        and c.status = 'Held'
                        and c.date_start is not null
                      group by c.assigned_user_id
                      ;";

            $resultQC = $db->query($queryC);
            $timedateFR = new TimeDate();
            while ($row = $db->fetchByAssoc($resultQC)) {
                //Setea ultima cita por asesor asignado
                $arr_principal['leasing']['ultima_llamada'] = ($row['assigned_user_id'] == $arr_principal['leasing']['promotorId']) ? date_format($timedateFR->fromDb($row['last_date_start']), "d/m/Y") : $arr_principal['leasing']['ultima_llamada'];
                $arr_principal['factoring']['ultima_llamada'] = ($row['assigned_user_id'] == $arr_principal['factoring']['promotorId']) ? date_format($timedateFR->fromDb($row['last_date_start']), "d/m/Y") : $arr_principal['factoring']['ultima_llamada'];
                $arr_principal['credito_auto']['ultima_llamada'] = ($row['assigned_user_id'] == $arr_principal['credito_auto']['promotorId']) ? date_format($timedateFR->fromDb($row['last_date_start']), "d/m/Y") : $arr_principal['credito_auto']['ultima_llamada'];
                $arr_principal['fleet']['ultima_llamada'] = ($row['assigned_user_id'] == $arr_principal['fleet']['promotorId']) ? date_format($timedateFR->fromDb($row['last_date_start']), "d/m/Y") : $arr_principal['fleet']['ultima_llamada'];
                $arr_principal['credito_sos']['ultima_llamada'] = ($row['assigned_user_id'] == $arr_principal['credito_sos']['promotorId']) ? date_format($timedateFR->fromDb($row['last_date_start']), "d/m/Y") : $arr_principal['credito_sos']['ultima_llamada'];
                $arr_principal['uniclick']['ultima_llamada'] = ($row['assigned_user_id'] == $arr_principal['uniclick']['promotorId']) ? date_format($timedateFR->fromDb($row['last_date_start']), "d/m/Y") : $arr_principal['uniclick']['ultima_llamada'];
                $arr_principal['unilease']['ultima_llamada'] = ($row['assigned_user_id'] == $arr_principal['unilease']['promotorId']) ? date_format($timedateFR->fromDb($row['last_date_start']), "d/m/Y") : $arr_principal['unilease']['ultima_llamada'];
                $arr_principal['seguros']['ultima_llamada'] = ($row['assigned_user_id'] == $arr_principal['seguros']['promotorId']) ? date_format($timedateFR->fromDb($row['last_date_start']), "d/m/Y") : $arr_principal['seguros']['ultima_llamada'];
                $arr_principal['credito_simple']['ultima_llamada'] = ($row['assigned_user_id'] == $arr_principal['credito_simple']['promotorId']) ? date_format($timedateFR->fromDb($row['last_date_start']), "d/m/Y") : $arr_principal['credito_simple']['ultima_llamada'];
            }
        }

        ############################
        ## Recupera y procesa emails asociados
        ############################
        if ($beanPersona->load_relationship('archived_emails')) {
            //Recupera llamadas
            $relatedEmails = $beanPersona->archived_emails->getBeans();

            //Procesa si recupera registros
            if ($relatedEmails) {
                //Obtiene total de llamadas
                $total_emails = count($relatedEmails);

                //Agrega valores al arreglo de respuesta
                $arr_principal['historial_contactos']['emails']= $total_emails;
            }
        }

        ############################
        ## Recupera y procesa información de resumen
        ############################
        if($beanPersona){
            //Recupera información de resumen
            $beanResumen = BeanFactory::getBean('tct02_Resumen', $beanPersona->id);

            //Procesa registro
            if($beanResumen){
                $arr_principal['general_cliente']['estado_cliente_c'] = $app_list_strings['estado_cliente_list'][$beanResumen->estado_cliente_c];
                //Recupera Leasing
                // $arr_principal['leasing']['tipo_cuenta']=$beanResumen->tct_tipo_cuenta_l_c;
                $arr_principal['leasing']['fecha_pago']= $beanResumen->leasing_fecha_pago;

                $arr_principal['general_cliente']['score_credito'] = $beanResumen->score_credito_c;
                //Victor
                //codigo para cargar el contenido del .txt de la noticia actualizada
                $filename = 'custom/pdf/noticiaGeneral.txt';
                $resultado = "";

                //funcion fopen abre el archivo con la ruta del mismo y el mode r (read)
                $file = fopen( $filename,"r");

                //Controla accion del archivo ($resultado contiene la info del archivo abierto)
                while(!feof($file)) {
                    $resultado.= fgets($file);
                }
                fclose($file);
                $arr_principal['noticia_general']['noticia']=$resultado;
                $arr_principal['noticia_macro_sector']['noticia']=$beanResumen->tct_noticia_sector_c;
                $arr_principal['noticia_region']['noticia']=$beanResumen->tct_noticia_region_c;
                $arr_principal['datos_clave']['dato_clave']=$beanResumen->tct_datos_clave_txa_c;

                //Recupera Crédito SOS
                $arr_principal['credito_sos']['fecha_pago']=$beanResumen->sos_fecha_pago_c;

			    //Recupera Uniclick
                // $arr_principal['uniclick']['tipo_cuenta']=$beanResumen->tct_tipo_cuenta_uc_c;
                $arr_principal['uniclick']['fecha_pago']= $beanResumen->cauto_fecha_pago;

                //Recupera Unilease
                // $arr_principal['uniclick']['tipo_cuenta']=$beanResumen->tct_tipo_cuenta_uc_c;
                $arr_principal['unilease']['fecha_pago']= $beanResumen->cauto_fecha_pago;

                //Recupera Clasificación Sectorial INEGI
                $arr_principal['inegi']['inegi_clase'] = $beanResumen->inegi_clase_c;
                $arr_principal['inegi']['inegi_subrama'] = $beanResumen->inegi_subrama_c;
                $arr_principal['inegi']['inegi_rama'] = $beanResumen->inegi_rama_c;
                $arr_principal['inegi']['inegi_subsector'] = $beanResumen->inegi_subsector_c;
                $arr_principal['inegi']['inegi_sector'] = $beanResumen->inegi_sector_c;
                $arr_principal['inegi']['inegi_macro'] = $beanResumen->inegi_macro_c;
                $arr_principal['inegi']['inegi_acualiza_uni2'] = $beanResumen->inegi_acualiza_uni2_c;
                // $arr_principal['inegi']['inegi_descripcion'] = $beanResumen->inegi_descripcion_c;

                //Recupera Clasificación Sectorial PB
                $arr_principal['pb']['pb_division'] = $beanResumen->pb_division_c;
                $arr_principal['pb']['pb_grupo'] = $beanResumen->pb_grupo_c;
                $arr_principal['pb']['pb_clase'] = $beanResumen->pb_clase_c;
        		//Condición del Cliente
        		$arr_principal['general_cliente']['condicion'] = $app_list_strings['condicion_cliente_list'][$beanResumen->condicion_cliente_c];
        		$arr_principal['general_cliente']['condicion2'] = $app_list_strings['condicion_cliente_list'][$beanResumen->condicion2_c];
        		$arr_principal['general_cliente']['condicion3'] = $app_list_strings['condicion_cliente_list'][$beanResumen->condicion3_c];
                $arr_principal['general_cliente']['tiene_condicion'] = (!empty($arr_principal['general_cliente']['condicion']) || !empty($arr_principal['general_cliente']['condicion2']) || !empty($arr_principal['general_cliente']['condicion3'])) ? true: false;

                //ASIGNACION AUTOMATICA
                $arr_principal['asignacion_automatica']['asignacion_activa_c'] = $beanResumen->asignacion_activa_c;
                $arr_principal['asignacion_automatica']['id_director_region_aprobar_c'] = $beanResumen->id_director_region_aprobar_c;
                $arr_principal['asignacion_automatica']['id_asesor_solicita_c'] = $beanResumen->id_asesor_solicita_c;

                //ACTIVIDAD ECONOMICA SAT
                $arr_principal['actividad_economica_sat']['id_actividad_economica_sat_c'] = $beanResumen->id_actividad_economica_sat_c;
                $arr_principal['actividad_economica_sat']['actividad_economica_sat_c'] = $beanResumen->actividad_economica_sat_c;
            }
        }

        ############################
        ## Recupera alertas
        ############################
        //Forma petición
        $query = "select * from notifications
        where assigned_user_id='{$current_user->id}'
        and is_read = 0
        and parent_id in ($operaciones_ids)
        order by date_entered asc;";

        //Logs
        //$GLOBALS['log']->fatal($query);

        //Obtiene resultado
        $resultQ = $GLOBALS['db']->query($query);
        $alert_num = 0;

        //Procesa registros recuperados
        while ($row = $GLOBALS['db']->fetchByAssoc($resultQ)) {
          //Recupera alertas y procesa
          $alert_num ++;
          $alerta = array(
            "mensaje"=>str_replace("<br/>","",$row['description']),
            "prioridad"=>$app_list_strings['notifications_severity_list'][$row['severity']],
            "idNotificacion"=>$row['id'],
            "numero"=>$alert_num
          );
          //Agrega alerta a respuesta de salida
          $arr_principal['alertas']['alerta'][] = $alerta;
        }

        //Agrega total de alertas
        $arr_principal['alertas']['total'] = $alert_num;

        ############################
        ## Establecer color para bloques
        ############################
        //Recupera fecha actual
        $timedate = new TimeDate($current_user);
        $today = $timedate->getNow(true);

        /**
          * Leasing:
          * Si la línea está a dos meses de vencer que se pondrá en amarillo. Si está vencida en rojo
        */
        if( $arr_principal['leasing']['fecha_vencimiento'] != ''){
            //Fecha vencimiento
            $dateFVL = $arr_principal['leasing']['fecha_vencimiento'];
            $dateFVL = str_replace('/', '-', $dateFVL);
            $dateFVL = date('Y-m-d', strtotime($dateFVL));

            //Calcula diferencia
            $current_date = new DateTime($today->format("Y-m-d"));
            $fecha_VL = new DateTime($dateFVL);

            $diferencia = $current_date->diff($fecha_VL);
            $meses = ( $diferencia->y * 12 ) + $diferencia->m;

            //Logs
            /*
            $GLOBALS['log']->fatal('Valida fechas Leasign');
            // $GLOBALS['log']->fatal($current_date);
            // $GLOBALS['log']->fatal($fecha_VL);
            $GLOBALS['log']->fatal($meses);
            */

            //Rojo
            if($fecha_VL < $current_date ) {
                $arr_principal['leasing']['color']=$Rojo;
            }
            //amarillo
            if($fecha_VL > $current_date && $meses <= 2) {
                $arr_principal['leasing']['color']=$Amarillo;
            }
        }

        /**
          * Factoring:
          * Si la línea está a dos meses de vencer que se pondrá en amarillo. Si está vencida en rojo
        */
        if( $arr_principal['factoring']['fecha_vencimiento'] != ''){
            //Fecha vencimiento
            $dateFVF = $arr_principal['factoring']['fecha_vencimiento'];
            $dateFVF = str_replace('/', '-', $dateFVF);
            $dateFVF = date('Y-m-d', strtotime($dateFVF));

            //Calcula diferencia
            $current_date = new DateTime($today->format("Y-m-d"));
            $fecha_VL = new DateTime($dateFVF);

            $diferencia = $current_date->diff($fecha_VL);
            $meses = ( $diferencia->y * 12 ) + $diferencia->m;

            //Logs
            /*
            $GLOBALS['log']->fatal('Valida fechas Factoring');
            // $GLOBALS['log']->fatal($current_date);
            // $GLOBALS['log']->fatal($fecha_VL);
            $GLOBALS['log']->fatal($meses);
            */

            //Rojo
            if($fecha_VL < $current_date ) {
                $arr_principal['factoring']['color']=$Rojo;
            }
            //amarillo
            if($fecha_VL > $current_date && $meses <= 2) {
                $arr_principal['factoring']['color']=$Amarillo;
            }
        }

        /**
          * Crédito Auto:
          * Si la línea está a dos meses de vencer que se pondrá en amarillo. Si está vencida en rojo
        */
        if( $arr_principal['credito_auto']['fecha_vencimiento'] != ''){
            //Fecha vencimiento
            $dateFVC = $arr_principal['credito_auto']['fecha_vencimiento'];
            $dateFVC = str_replace('/', '-', $dateFVC);
            $dateFVC = date('Y-m-d', strtotime($dateFVC));

            //Calcula diferencia
            $current_date = new DateTime($today->format("Y-m-d"));
            $fecha_VL = new DateTime($dateFVC);

            $diferencia = $current_date->diff($fecha_VL);
            $meses = ( $diferencia->y * 12 ) + $diferencia->m;

            //Logs
            /*
            $GLOBALS['log']->fatal('Valida fechas Creduti auto');
            // $GLOBALS['log']->fatal($current_date);
            // $GLOBALS['log']->fatal($fecha_VL);
            $GLOBALS['log']->fatal($meses);
            */

            //Rojo
            if($fecha_VL < $current_date ) {
                $arr_principal['credito_auto']['color']=$Rojo;
            }
            //amarillo
            if($fecha_VL > $current_date && $meses <= 2) {
                $arr_principal['credito_auto']['color']=$Amarillo;

            }

        }

        /**
          * Historial de contacto:
          * Amarillo cuando hayan pasado más de 2 meses sin cita/llamada y rojo más de 3 meses.
        */
        if ($arr_principal['historial_contactos']['llamadas'] >= 1 || $arr_principal['historial_contactos']['citas'] >= 1) {
            //Today
            // $timedate = new TimeDate($current_user);
            // $today = $timedate->getNow(true);

            //Última reunión
            //$dateFR = $arr_principal['historial_contactos']['fecha_completa_cita'];
            //$timedateFR = new TimeDate();
            //$fecha_cita = $timedateFR->fromUser($dateFR, $current_user);

            $dateFR = $arr_principal['historial_contactos']['fecha_completa_cita'];
            $timedateFR = new TimeDate();
            $fecha_cita = $timedateFR->fromDb($dateFR);
            // $GLOBALS['log']->fatal('fecha de cita:');
            // $GLOBALS['log']->fatal($fecha_cita);
            //Última llamada
            // $dateFL = $arr_principal['historial_contactos']['fecha_completa_llamada'];
            // $timedateFL = new TimeDate();
            // $fecha_llamada = $timedateFL->fromUser($dateFL, $current_user);
            $dateFL = $arr_principal['historial_contactos']['fecha_completa_llamada'];
            $timedateFL = new TimeDate();
            $fecha_llamada = $timedateFL->fromDb($dateFL);
            // $GLOBALS['log']->fatal('fecha de llamada:');
            // $GLOBALS['log']->fatal($fecha_llamada);

            //Recupera última fecha de contacto
            $fecha_contacto = "";
            if ($fecha_llamada > $fecha_cita) {
              $fecha_contacto = $fecha_llamada;
            }else {
              $fecha_contacto = $fecha_cita;
            }

            // $GLOBALS['log']->fatal('fecha de contacto:');
            // $GLOBALS['log']->fatal($fecha_contacto);
            //Calcula diferencia
            $current_date = new DateTime($today->format("Y-m-d"));
            $fecha_contacto = new DateTime($fecha_contacto->format("Y-m-d"));

            $diferencia = $current_date->diff($fecha_contacto);
            $meses = ( $diferencia->y * 12 ) + $diferencia->m;

            // $GLOBALS['log']->fatal('diferencia:');
            // $GLOBALS['log']->fatal($meses);

            //Asigna color
            if($current_date > $fecha_contacto){
                if($meses == 2){
                    $arr_principal['historial_contactos']['color'] = $Amarillo;
                }
                if ($meses >= 3) {
                    $arr_principal['historial_contactos']['color'] = $Rojo;
                }
            }

            //Log
            /*
            $GLOBALS['log']->fatal($today->format("Y-m-d"));
            $GLOBALS['log']->fatal($fecha_contacto->format("Y-m-d"));
            $GLOBALS['log']->fatal($meses);
            */

        }else{
            $arr_principal['historial_contactos']['color'] = $Rojo;
        }

        ############################
        ## Recupera y procesa Seguros asociadas
        ############################
        if($beanPersona->load_relationship('s_seguros_accounts')) {
          $relatedSeguros = $beanPersona->s_seguros_accounts->getBeans();
          if($relatedSeguros) {
            $arr_principal['seguros']['total'] = count($relatedSeguros);
            $prima =0;
            $ingreso = 0;
            foreach($relatedSeguros as $seguro) {
              if($seguro->etapa == 9) {
                $ganadas = $ganadas + 1;
                $prima += floatval($seguro->prima_neta_ganada_c);
                $ingreso += floatval($seguro->prima_neta);
                $arr_principal['seguros']['tiene_ganada'] = true;
              }
              else {
                if($seguro->etapa != 10) $proceso = $proceso + 1;
              }
              $arr_principal['seguros']['op_ganadas'] = ($seguro->etapa == '9') ? intval($arr_principal['seguros']['op_ganadas'])+1 : $arr_principal['seguros']['op_ganadas'];
              $arr_principal['seguros']['op_presentacion'] = ($seguro->etapa == '6') ? intval($arr_principal['seguros']['op_presentacion'])+1 : $arr_principal['seguros']['op_presentacion'];
              $arr_principal['seguros']['op_cotizando'] = ($seguro->etapa == '2') ? intval($arr_principal['seguros']['op_cotizando'])+1 : $arr_principal['seguros']['op_cotizando'];
              $arr_principal['seguros']['op_no_cotizado'] = ($seguro->etapa == '5') ? intval($arr_principal['seguros']['op_no_cotizado'])+1 : $arr_principal['seguros']['op_no_cotizado'];
              $arr_principal['seguros']['op_no_ganada'] = ($seguro->etapa == '10') ? intval($arr_principal['seguros']['op_no_ganada'])+1 : $arr_principal['seguros']['op_no_ganada'];
              $arr_principal['seguros']['kam_asignado'] = $seguro->ejecutivo_c;
            }
            $arr_principal['seguros']['ganadas'] = $ganadas;
            $arr_principal['seguros']['proceso'] = $proceso;
            $arr_principal['seguros']['prima'] = $prima;
            $arr_principal['seguros']['ingreso'] = $ingreso;
            $arr_principal['seguros']['muestra_producto'] = true;
            $arr_principal['seguros']['tiene_seguros'] = true;
          }
        }

        ############################
        ## Recupera información de Relaciones para mostrar en sección de Contactos en vista 360
        ############################
        if($beanPersona->load_relationship('rel_relaciones_accounts_1')) {
            $relatedRelaciones = $beanPersona->rel_relaciones_accounts_1->getBeans();
            if($relatedRelaciones) {

                $queryOrdenRelaciones = "SELECT a.name,
                r.relaciones_activas,
                r.id relacion_id,
                ac.tipodepersona_c,
                IF(ac.tipodepersona_c='Persona Moral',
                    CASE
                        WHEN r.relaciones_activas like '%Negocios%' THEN 1
                        WHEN (SELECT puesto_cuenta_c FROM accounts_cstm WHERE id_c=rc.account_id1_c) ='3' THEN 2 -- Cuenta relacionada tiene como puesto Director General
                        WHEN (r.relaciones_activas like '%Directivo%' or (SELECT puesto_cuenta_c FROM accounts_cstm WHERE id_c=rc.account_id1_c) IN ('4','5','6','7')) THEN 3 -- Relación incluye relación activa Directivo o Cuenta Relacionada tiene algún puesto de Director
                        WHEN (r.relaciones_activas like '%Accionista%' or (SELECT puesto_cuenta_c FROM accounts_cstm WHERE id_c=rc.account_id1_c) ='2') THEN 4 -- Relación incluye relación activa Accionista o Cuenta Relacionada tiene puesto Accionistas
                        WHEN r.relaciones_activas like '%Representante%' THEN 5 -- Relación incluye relación activa Representante
                        WHEN (r.relaciones_activas like '%Contacto%' or (SELECT puesto_cuenta_c FROM accounts_cstm WHERE id_c=rc.account_id1_c) ='10') THEN 6 -- Relación incluye relación activa Contacto o Cuenta Relacionada tiene puesto Administrativo
                        WHEN r.relaciones_activas like '%Propietario Real%' THEN 7 -- Relación incluye relación activa Propietario Real
                        ELSE 8
                    END,
                    CASE -- Es PF o PFAE
                        WHEN a.id= rc.account_id1_c THEN 1 -- Cuenta Principal es igual a Cuenta Relacionada
                        WHEN r.relaciones_activas like '%Negocios%' THEN 2 -- Relación incluye relación activa Negocios
                        WHEN (SELECT puesto_cuenta_c FROM accounts_cstm WHERE id_c=rc.account_id1_c) ='3' THEN 3 -- Cuenta relacionada tiene como puesto Director General
                        WHEN (r.relaciones_activas like '%Directivo%' or (SELECT puesto_cuenta_c FROM accounts_cstm WHERE id_c=rc.account_id1_c) IN ('4','5','6','7')) THEN 4 -- Relación incluye relación activa Directivo o Cuenta Relacionada tiene algún puesto de Director
                        WHEN (r.relaciones_activas like '%Accionista%' or (SELECT puesto_cuenta_c FROM accounts_cstm WHERE id_c=rc.account_id1_c) ='2') THEN 5 -- Relación incluye relación activa Accionista o Cuenta Relacionada tiene puesto Accionistas
                        WHEN r.relaciones_activas like '%Representante%' THEN 6 -- Relación incluye relación activa Representante
                        WHEN (r.relaciones_activas like '%Contacto%' or (SELECT puesto_cuenta_c FROM accounts_cstm WHERE id_c=rc.account_id1_c) ='10') THEN 7 -- Relación incluye relación activa Contacto o Cuenta Relacionada tiene puesto Administrativo
                        WHEN r.relaciones_activas like '%Propietario Real%' THEN 8 -- Relación incluye relación activa Propietario Real
                        ELSE 9
                    END
                    ) orden
                FROM rel_relaciones_accounts_1_c ra
                INNER JOIN rel_relaciones r on ra.rel_relaciones_accounts_1rel_relaciones_idb=r.id
                INNER JOIN rel_relaciones_cstm rc on r.id=rc.id_c
                INNER JOIN accounts a on ra.rel_relaciones_accounts_1accounts_ida=a.id
                INNER JOIN accounts_cstm ac on a.id=ac.id_c
                WHERE ra.rel_relaciones_accounts_1accounts_ida='{$beanPersona->id}'
                AND r.deleted=0 AND a.deleted=0
                ORDER by orden asc;";
                $queryResultOrdenRelaciones = $db->query($queryOrdenRelaciones);
                $count=0;
                while ($row = $db->fetchByAssoc($queryResultOrdenRelaciones)) {
                    if($count==0){//El primer registro en el orden corresponde al Contacto de Negocios
                        //$GLOBALS['log']->fatal("COUNT 0 ES EL CONTACTO DE NEGOCIOS");
                        $id_relacion=$row['relacion_id'];
                        $rel = BeanFactory::getBean("Rel_Relaciones", $id_relacion);

                        //Obtengo bean de la cuenta relacionada para recuperar su teléfono y email
                        $beanRelacionNegocios = BeanFactory::getBean("Accounts", $rel->account_id1_c,array('disable_row_level_security' => true));

                        //Obtiene teléfonos
                        $telefono_principal_negocio="";
                        if($beanRelacionNegocios->load_relationship('accounts_tel_telefonos_1')){
                            $relatedTelefonos = $beanRelacionNegocios->accounts_tel_telefonos_1->getBeans();
                            if(count($relatedTelefonos)>0){
                                foreach($relatedTelefonos as $tel) {
                                     //$GLOBALS['log']->fatal(print_r($tel,true));
                                    if($tel->principal==1){
                                        $telefono_principal_negocio=$tel->telefono;
                                    }
                                }
                            }
                        }
                        $arr_principal['contactos']['nombre_negocios'] = $rel->relacion_c;
                        $arr_principal['contactos']['id_nombre_negocios'] = $beanRelacionNegocios->id;
                        $arr_principal['contactos']['puesto_negocios'] = isset($app_list_strings['puestos_list'][$beanRelacionNegocios->puesto_cuenta_c]) ? $app_list_strings['puestos_list'][$beanRelacionNegocios->puesto_cuenta_c] : '';
                        $arr_principal['contactos']['telefono_negocios'] = $telefono_principal_negocio;
                        $arr_principal['contactos']['correo_negocios'] = $beanRelacionNegocios->email1;
                        $arr_principal['contactos']['tiene_negocio'] = true;
                        $arr_principal['contactos']['rel_activa_negocio'] = (strpos($rel->relaciones_activas, 'Negocios') !== false) ? true: false;

                        $count++;

                    }else if($count==1){//El segundo registro en el orden se toma como el Contacto Secundario
                        //$GLOBALS['log']->fatal("COUNT 1 ES EL CONTACTO SECUNDARIO");
                        $id_relacion=$row['relacion_id'];
                        $rel = BeanFactory::getBean("Rel_Relaciones", $id_relacion);

                        //Obtengo bean de la cuenta relacionada para recuperar su teléfono y email
                        $beanRelacionSecundaria = BeanFactory::getBean("Accounts", $rel->account_id1_c,array('disable_row_level_security' => true));

                        //Obtiene teléfonos
                        $telefono_principal_secundario="";
                        if($beanRelacionSecundaria->load_relationship('accounts_tel_telefonos_1')){
                            $relatedTelefonos = $beanRelacionSecundaria->accounts_tel_telefonos_1->getBeans();
                            if(count($relatedTelefonos)>0){

                                foreach($relatedTelefonos as $tel) {
                                    $GLOBALS['log']->fatal("TELEFONO ".$tel->telefono);
                                    if($tel->principal==1){
                                        $telefono_principal_secundario=$tel->telefono;
                                    }
                                }
                            }
                        }

                        $arr_principal['contactos']['nombre_secundario'] = $rel->relacion_c;
                        $arr_principal['contactos']['id_nombre_secundario'] = $beanRelacionSecundaria->id;
                        $arr_principal['contactos']['puesto_secundario'] =isset($app_list_strings['puestos_list'][$beanRelacionSecundaria->puesto_cuenta_c]) ? $app_list_strings['puestos_list'][$beanRelacionSecundaria->puesto_cuenta_c] : '';
                        $arr_principal['contactos']['telefono_secundario'] = $telefono_principal_secundario;
                        $arr_principal['contactos']['correo_secundario'] = $beanRelacionSecundaria->email1;
                        $arr_principal['contactos']['tiene_secundario'] = true;
                        $count++;
                    }
                }

            }
        }

        return $arr_principal;
    }

    //Función para validar si un usuario no es usuario de grupo
    public function usuarioValido($idUsuario)
    {
        $valido = false;
        if(!empty($idUsuario)){
            //Valida usuario
            global $db;
            $queryU = "select count(*) from users where id='{$idUsuario}' and is_group=0 and deleted=0 limit 1;";
            $queryResult = $db->getOne($queryU);
            $valido = ($queryResult > 0) ? true : false;
        }
        //$GLOBALS['log']->fatal('Usuario valido: '.$idUsuario .'-'.$valido);
        return $valido;
    }
}
