<?php
/**
 * Created by JG.
 * User: tactos
 * Date: 21/12/20
 * Time: 12:47 PM
 */

class IntegracionQuantico
{
    public function QuanticoIntegracion($bean = null, $event = null, $args = null)
    {
        //Declaración de variables gloables
        global $sugar_config, $db, $app_list_strings, $current_user;
        $user = $sugar_config['quantico_usr'];
        $pwd = $sugar_config['quantico_psw'];
        $auth_encode = base64_encode($user . ':' . $pwd);
        $arrayBo = explode(',', $bean->usuario_bo_c);
        $iniciaPUni2 = ($app_list_strings['switch_inicia_proceso_list']['ejecuta'] == 1) ? true: false;   //Control para swith que indica si debe ejecutar o no inicia-proceso a uni2

        //Criterios para envío de solicitud a Quantico
        require_once("custom/clients/base/api/excluir_productos.php");
        $args_uni_producto = [];
        $args_uni_producto['idCuenta'] = $bean->account_id;
        $args_uni_producto['Producto'] = $bean->tipo_producto_c;
		$args_uni_producto['Negocio'] = $bean->negocio_c;
		$args_uni_producto['Financiero'] = $bean->producto_financiero_c;
        $EjecutaApi = new excluir_productos();
        $response_exluye = $EjecutaApi->Excluyeprecalif(null, $args_uni_producto);
        //consulta no viable
        $query = "select ap.accounts_uni_productos_1accounts_ida account_id, up.id producto_id, up.tipo_producto, up.no_viable
        from accounts_uni_productos_1_c ap
        join uni_productos up on ap.accounts_uni_productos_1uni_productos_idb = up.id
        where accounts_uni_productos_1accounts_ida = '{$bean->account_id}'
        and up.tipo_producto = '{$bean->tipo_producto_c}'";
        $queryResult = $db->query($query);
        $row = $bean->db->fetchByAssoc($queryResult);
        //condiciones
        $generaSolicitud = false;
        $generaSolicitud = ($args['isUpdate'] == 1 && $bean->tct_etapa_ddw_c == 'SI' && $bean->tipo_producto_c != '6' && ($bean->tipo_producto_c != '1' || ($bean->tipo_producto_c=='2' && ($bean->negocio_c=='2' || $bean->negocio_c=='10'))) && $bean->estatus_c!='1' ) ? true : $generaSolicitud;
        $generaSolicitud = ($args['isUpdate'] == 1 && $bean->tct_etapa_ddw_c == 'SI' && ($bean->tipo_producto_c == '1' || ($bean->tipo_producto_c=='2' && ($bean->negocio_c!='2' || $bean->negocio_c!='10'))) && $bean->vobo_dir_c == true) ? true : $generaSolicitud;
		    //$generaSolicitud = ($bean->tipo_producto_c == '3' || $bean->producto_financiero_c == '40' || ($bean->tipo_de_operacion_c == 'RATIFICACION_INCREMENTO' && ($bean->tipo_producto_c != '1' || ($bean->tipo_producto_c=='2' && ($bean->negocio_c=='2' || $bean->negocio_c=='10'))))) ? true : $generaSolicitud;
        $generaSolicitud = ($bean->tipo_producto_c == '3' || $bean->producto_financiero_c == '40') ? true : $generaSolicitud;
        $generaSolicitud = ($bean->tipo_de_operacion_c == 'RATIFICACION_INCREMENTO' && ($bean->tipo_producto_c == '1' || ($bean->tipo_producto_c=='2' && ($bean->negocio_c!='2' || $bean->negocio_c!='10')))&& $response_exluye == 1) ? true : $generaSolicitud;
        $generaSolicitud = ($args['isUpdate'] == 1 && $bean->tct_etapa_ddw_c == 'SI' && ($bean->tipo_producto_c == '1' || ($bean->tipo_producto_c=='2' && ($bean->negocio_c!='2' || $bean->negocio_c!='10')))&& $response_exluye == 1) ? true : $generaSolicitud;
        $generaSolicitud = ($args['isUpdate'] == 1 && $bean->tct_etapa_ddw_c == 'SI' && $bean->producto_financiero_c!="0" &&$bean->producto_financiero_c!="") ? true : $generaSolicitud;
        $generaSolicitud = ($args['isUpdate'] == 1 && $bean->tct_etapa_ddw_c == 'SI' && $bean->tipo_producto_c == '1' && $bean->negocio_c == '3') ? true : $generaSolicitud;
		    $generaSolicitud = ($args['isUpdate'] == 1 && $bean->admin_cartera_c) ? true : $generaSolicitud;
        //$generaSolicitud = ($args['isUpdate'] == 1 && $row['no_viable'] == '1') ? false: $generaSolicitud;
        $generaSolicitud = $bean->tipo_producto_c == '14' ? false: $generaSolicitud;

        if ( ( ($bean->id_process_c != "" && $iniciaPUni2) || (!$iniciaPUni2) ) && $bean->idsolicitud_c != "" && $bean->quantico_id_c == "" && $generaSolicitud && !$bean->tct_oportunidad_perdida_chk_c) {
            $GLOBALS['log']->fatal("Inicia Petición de integración con Quantico");
            $beanCuenta = BeanFactory::retrieveBean('Accounts', $bean->account_id, array('disable_row_level_security' => true));

            if ($beanCuenta->tipodepersona_c == 'Persona Fisica') {
                $tipoPersona = "1";
            }
            if ($beanCuenta->tipodepersona_c == 'Persona Fisica con Actividad Empresarial') {
                $tipoPersona = "2";
            }
            if ($beanCuenta->tipodepersona_c == 'Persona Moral') {
                $tipoPersona = "3";
            }

            //Se define URL
            $host = $sugar_config['quantico_url_base'] . '/CreditRequestIntegration/rest/CreditRequestApi/PostRegister';
            $beanUser = BeanFactory::retrieveBean('Users', $bean->assigned_user_id, array('disable_row_level_security' => true));
            $idActiveDirectory = $beanUser->id_active_directory_c;
            $RequestAmount = $bean->tipo_de_operacion_c=="RATIFICACION_INCREMENTO"?0:$bean->monto_c;
            $IncreaseAmount = $bean->tipo_de_operacion_c=="RATIFICACION_INCREMENTO"?$bean->monto_ratificacion_increment_c:0;
            $CreditLineId = $bean->tipo_de_operacion_c=="RATIFICACION_INCREMENTO"?$bean->id_linea_credito_c:0;

            //Recupera el equipo del assigned_user de la opp
            $asignado=$bean->assigned_user_id;
            $valorEquipo="";
            //$GLOBALS['log']->fatal("Realiza consulta para obtener el equipo del assigned_user_id");
            $queryasesor="SELECT equipo_c,id_active_directory_c,nombre_completo_c from users_cstm where id_c='$asignado' limit 1";
            //$GLOBALS['log']->fatal("Ejecuta consulta" .print_r($queryasesor, true));
            $queryResult = $db->query($queryasesor);
            while ($row = $db->fetchByAssoc($queryResult)) {
                $valorEquipo = $row['equipo_c'];
                $idActiveDirectory = $row['id_active_directory_c'];
                $nombreUsuario = $row['nombre_completo_c'];
            }

            $OnboardingContact = $this->getOnBoardingContact($bean);
            //Define estructura de nueva solicitud
            $body = array(
                "RequestId" => $bean->idsolicitud_c,
                //"ProcessId" => $iniciaPUni2 ? $bean->id_process_c: '1',
                "ProcessId" =>  $bean->idsolicitud_c,
                "OpportunitiesId" => $bean->id,
                "ClientId" => $bean->account_id,
                "ClientName" => $bean->account_name,
                "ProductId" => $bean->producto_financiero_c,
                "RequestTypeId" => $bean->tipo_de_operacion_c,
                "PersonTypeId" => $tipoPersona,
                "ProductTypeId" => $bean->tipo_producto_c,
                "CurrencyTypeId" => (!empty($bean->ce_moneda_c) && $bean->ce_moneda_c!='') ? $bean->ce_moneda_c :  "1",
                "RequestAmount" => $RequestAmount,
                "IncreaseAmount" => $IncreaseAmount,
                "AdviserId" => $idActiveDirectory,
                "AdviserName" => $nombreUsuario,
                "SinglePaymentPercentage"=>$bean->porciento_ri_c,
                //"SinglePaymentPercentage" => $CreditLineId,
                "CreditLineId" => $CreditLineId,
                "BackOfficeId" => str_replace("^", "", $arrayBo[0]),
                "BackOfficeName" => $app_list_strings['usuario_bo_0'][str_replace("^", "", $arrayBo[0])],
                "BusinessGroupId"=>$bean->negocio_c,
                "TeamId"=>$valorEquipo,
                "OnboardingContact"=>$OnboardingContact
            );
            //Obtiene campo de condiciones financieras de quantico
            $strCFQuantico=$bean->cf_quantico_c;
            $jsonCFQuantico="";
            if($strCFQuantico != ""){
                $jsonCFQuantico=json_decode($strCFQuantico);
                //Declaracion de Body
                $body["FinancialTermGroupResponse"] = $jsonCFQuantico->FinancialTermGroupResponseList;

            }
            //Para Crédito Corto plazo
            if ($bean->producto_financiero_c=='78') {
                $body["BusinessAmount"] = $bean->monto_gpo_emp_c;
            }

      			//Valida si se tiene Administración de Cartera y se añaden campos extras al body
      			if($bean->admin_cartera_c==1) {
                $body["ProductOriginPortfolioId"] = $bean->producto_origen_vencido_c;
                $body["RequestTypePortfolioId"] = $bean->tipo_sol_admin_cartera_c;
                $body["BusinessAmount"] = $bean->monto_gpo_emp_c;
                $body["NumberDaysoverduePortfolio"] = $bean->cartera_dias_vencido_c;
      			}
            //$GLOBALS['log']->fatal('Body Quantico integracion ' . json_encode($body));
            $callApi = new UnifinAPI();
            $resultado = $callApi->postQuantico($host, $body, $auth_encode);
            $GLOBALS['log']->fatal('Resultado: Peticion Quantico integracion ' . json_encode($resultado));
            if ($resultado['Success']) {
                $GLOBALS['log']->fatal('Ha realizado correctamente');
                $query = "UPDATE opportunities_cstm
                              SET quantico_id_c ='{$resultado['ErrorMessage']}'
                              WHERE id_c = '{$bean->id}'";
                $queryResult = $db->query($query);
                $bean->quantico_id_c = $resultado['ErrorMessage'];
            } else {
                $GLOBALS['log']->fatal("Error al procesar la solicitud a Quantico, verifique información");
                $GLOBALS['log']->fatal("Enviando notificación para bitácora de errores Quantico");
                require_once("custom/clients/base/api/ErrorLogApi.php");
                $apiErrorLog = new ErrorLogApi();
                $args = array(
                    "integration"=> "Quantico: CreditRequestApi/PostRegister",
                    "system"=> "Quantico",
                    "parent_type"=> "Opportunities",
                    "parent_id"=> $bean->id,
                    "endpoint"=> $host,
                    "request"=> json_encode($body),
                    "response"=> json_encode($resultado)
                );
                $responseErrorLog = $apiErrorLog->setDataErrorLog(null, $args);
            }
        }
        $GLOBALS['log']->fatal("Termina QuanticoIntegracion");
    }

    public function QuanticoUpdate($bean = null, $event = null, $args = null)
    {
        if ($bean->idsolicitud_c != "" && $bean->quantico_id_c != "" && $bean->cancelado_quantico_c == "" && $bean->tct_oportunidad_perdida_chk_c) {
            $GLOBALS['log']->fatal('Inicia Cancelación de Solicitud Quantico');
            global $sugar_config, $db, $app_list_strings, $current_user;
            $user = $sugar_config['quantico_usr'];
            $pwd = $sugar_config['quantico_psw'];
            $auth_encode = base64_encode($user . ':' . $pwd);
            //Se genera mensaje mediante las opciones de cancelacion
            $mensaje = "";
            if ($bean->tct_razon_op_perdida_ddw_c != "") {
                switch ($bean->tct_razon_op_perdida_ddw_c) {
                    case 'P':
                        $mensaje = "El motivo de cancelación es el precio.";
                        break;
                    case 'C':
                        $mensaje = "El motivo de cancelación se debe a que la empresa" . $bean->tct_competencia_quien_txf_c . " " . $bean->tct_competencia_porque_txf_c;
                        break;
                    case 'NPR':
                        $mensaje = "No se cuenta con el producto " . $bean->tct_sin_prod_financiero_ddw_c;
                        break;
                    case 'PRP':
                        $mensaje = "Se pagó con recursos propios.";
                        break;
                    case 'ANF':
                        $mensaje = "Debido a activos no financiables.";
                        break;
                    case 'CDN':
                        $mensaje = "El cliente decide no comprar el activo.";
                        break;
                    case 'CNC':
                        $mensaje = "El cliente decide no ccompletó el expediente.";
                        break;
                    case 'TR':
                        $mensaje = "Debido al tiempo de respuesta.";
                        break;
                    case 'DI':
                        $mensaje = "Debido datos incorrectos/duplicidad.";
                        break;
                    case '10':
                        $mensaje = "Debido que no cumple con el scoring comercial.";
                        break;
                    default:
                        $mensaje;
                }
            }

            //Se define URL
            $host = $sugar_config['quantico_url_base'] . '/CreditRequestIntegration/rest/CreditRequestApi/CancelCreditRequest';

            $body = array(
                "RequestNumber" => $bean->idsolicitud_c,
                "UserComercialId" => $current_user->id_active_directory_c,
                "Comment" => $mensaje
            );
            $callApi = new UnifinAPI();
            $resultado = $callApi->postQuantico($host, $body, $auth_encode);
            $GLOBALS['log']->fatal('Resultado: Actualizacion Quantico ' . json_encode($resultado));

            if ($resultado['Success'] && $resultado['ErrorMessage'] == "") {
                //$GLOBALS['log']->fatal('Actualización Correcta');
                $query = "UPDATE opportunities_cstm
                              SET cancelado_quantico_c ='Actualización Correcta de Quantico.'
                              WHERE id_c = '{$bean->id}'";
                $queryResult = $db->query($query);
                $bean->cancelado_quantico_c = $resultado['ErrorMessage'];
            } else {
                $GLOBALS['log']->fatal("Error al actualizar a Quantico");
                $GLOBALS['log']->fatal("Enviando notificación para bitácora de errores Quantico");
                require_once("custom/clients/base/api/ErrorLogApi.php");
                $apiErrorLog = new ErrorLogApi();
                $args = array(
                    "integration"=> "Quantico: CreditRequestApi/CancelCreditRequest",
                    "system"=> "Quantico",
                    "parent_type"=> "Opportunities",
                    "parent_id"=> $bean->id,
                    "endpoint"=> $host,
                    "request"=> json_encode($body),
                    "response"=> json_encode($resultado)
                );
                $responseErrorLog = $apiErrorLog->setDataErrorLog(null, $args);
            }
        }
        $GLOBALS['log']->fatal('Finaliza QuanticoUpdate');
    }

    public function CFQuanticoUpdate($bean = null, $event = null, $args = null){
        //Control para swith que indica si debe ejecutar o proceso para Quantico
        $switchUni2 = $app_list_strings['switch_inicia_proceso_list']['ejecuta'];

        //Mandar actualización de condiciones financieras en caso de que se detecte una actualización al campo que guarda el json de la petición
        if ($bean->fetched_row['cf_quantico_c'] != $bean->cf_quantico_c && !empty($bean->quantico_id_c) && $switchUni2=="0"){
            $GLOBALS['log']->fatal('Inicia Actualización - Quantico Condiciones financieras');
            global $sugar_config, $db, $app_list_strings, $current_user;
            $user = $sugar_config['quantico_usr'];
            $pwd = $sugar_config['quantico_psw'];
            $auth_encode = base64_encode($user . ':' . $pwd);

            //Se define URL
            $host = $sugar_config['quantico_url_base'] . '/CreditRequestIntegration/rest/CreditRequestApi/ModifyFinantialCondition';
            $body=json_decode($bean->cf_quantico_c);

            $callApi = new UnifinAPI();
            $resultado = $callApi->postQuantico($host, $body, $auth_encode);
            $GLOBALS['log']->fatal('Resultado: Actualizacion Quantico ' . json_encode($resultado));

            if ($resultado['Success'] && $resultado['ErrorMessage'] == "") {
                $GLOBALS['log']->fatal('Resultado: Actualizacion Quantico ' . json_encode($resultado));
            }else{

                $GLOBALS['log']->fatal("Enviando notificación para bitácora de errores Quantico");
                require_once("custom/clients/base/api/ErrorLogApi.php");
                $apiErrorLog = new ErrorLogApi();
                $args = array(
                    "integration"=> "Quantico: CreditRequestApi/ModifyFinantialCondition",
                    "system"=> "Quantico",
                    "parent_type"=> "Opportunities",
                    "parent_id"=> $bean->id,
                    "endpoint"=> $host,
                    "request"=> json_encode($body),
                    "response"=> json_encode($resultado)
                );
                $responseErrorLog = $apiErrorLog->setDataErrorLog(null, $args);

            }

        }

    }

    /*Obtiene relaciones tipo Contacto - Promocion y se envía a la petición de quantico la Cuenta Relacionada tipo Persona
    * solo si ha sido creada a partir de un Público Objetivo, el cual se obtiene únicamente si el campo relacionado account_id2_c
    * de prospects_cstm contiene valor
    */
    public function getOnBoardingContact($bean){
        global $db;
        
        $OnboardingContact = "";
        $queryGetCuentasRelaciones = "SELECT 
    r.id,rc.account_id1_c,r.relaciones_activas, r.tipodecontacto, pc.id_c idPO
FROM rel_relaciones_accounts_1_c ra
    INNER JOIN rel_relaciones r on ra.rel_relaciones_accounts_1rel_relaciones_idb = r.id
    INNER JOIN rel_relaciones_cstm rc ON r.id = rc.id_c
    INNER JOIN prospects_cstm pc on pc.account_id2_c = rc.account_id1_c
WHERE rel_relaciones_accounts_1accounts_ida = '{$bean->account_id}'
AND r.relaciones_activas LIKE '^Contacto^'
AND r.tipodecontacto = 'Promocion'
AND ra.deleted=0
AND r.deleted =0
ORDER BY ra.date_modified desc
limit 1
;";

        $resultCuentasRelaciones = $db->query($queryGetCuentasRelaciones);

        $count_relaciones_contacto = $resultCuentasRelaciones->num_rows;

        if( $count_relaciones_contacto > 0 ){
            
            while ($row = $db->fetchByAssoc($resultCuentasRelaciones)) {
                $OnboardingContact = $row['account_id1_c'];
            }
        }

        return $OnboardingContact;

    }

}
