<?php


class Seguros_dynamics
{
    public function getAccount($bean = null, $event = null, $args = null)
    {
        global $sugar_config;
        // Valida si viene de Uni2
        if($bean->seguro_uni2_c)
        {
          if($bean->id_disposicion_c)
          {
            global $db;
            $id_disposicion = "select id_disposicion_c from s_seguros_cstm where id_c <> '{$bean->id}' and id_disposicion_c = '{$bean->id_disposicion_c}'";
            $resultado = $db->query($id_disposicion);
            $encontrado = $db->fetchByAssoc($resultado);
            if($encontrado) throw new SugarApiExceptionInvalidParameter("No se puede guardar. Ya existe el Id Disposición");
          }
        }
        else
        {
          //Consulta Cuenta
          $cuenta = BeanFactory::getBean('Accounts', $bean->s_seguros_accountsaccounts_ida, array('disable_row_level_security' => true));
          $resumen = BeanFactory::getBean('tct02_Resumen', $bean->s_seguros_accountsaccounts_ida, array('disable_row_level_security' => true));
          $GLOBALS['log']->fatal('Inicia Seguros_Dynamics');
          global $sugar_config, $db, $app_list_strings;
            require_once 'include/api/SugarApiException.php';
          if(!$cuenta->int_id_dynamics_c)
          {
            $token = $this->getToken();
            if($token)
            {
                if(empty($cuenta->int_id_dynamics_c)){
                    $url = $sugar_config['inter_dynamics_url'].'Account/'.$cuenta->rfc_c;
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_HEADER, false);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_HTTPHEADER,
                    array("Authorization: Bearer $token",
                        "Content-type: application/json"));
                    $GLOBALS['log']->fatal('Seguros_Dynamics - Consulta Cuenta: ' . $url);
                    $json_response = curl_exec($curl);
                    curl_close($curl);
                    $response = json_decode($json_response, true);
                    $GLOBALS['log']->fatal($response);
                    $id_dyn = $response['id_CRM'];
                    if($id_dyn!="Null" && $id_dyn!=""){
                        global $db;
                        $update = "update accounts_cstm set
                        int_id_dynamics_c='{$id_dyn}'
                        where id_c = '{$cuenta->id}'";
                        $updateExecute = $db->query($update);
                        $cuenta->int_id_dynamics_c = $id_dyn;
                    }else{
                        //Crear creacion de cuenta en Dynamics
                        $url = $sugar_config['inter_dynamics_url'].'Account';
                        $idUsuario = ($bean->tipo_referenciador == 1) ? $bean->user_id1_c : $bean->user_id2_c;
                        $referenciadorDeLaOportunidadC = $app_list_strings['referenciador_list'][$bean->referenciador_c];
                        $nombreUsuarioQuery = "Select nombre_completo_c from users_cstm where id_c ='{$idUsuario}'";
                        $resultUsuario = $db->query($nombreUsuarioQuery);
                        $referenciador = $db->fetchByAssoc($resultUsuario);
                        if($bean->tipo_referenciador == 1) $referenciadorCuenta = $referenciador['nombre_completo_c']." ".$bean->region;
                        if($bean->tipo_referenciador == 2) $referenciadorCuenta = $referenciador['nombre_completo_c']." ".$bean->departamento_c;
                        if($bean->tipo_referenciador == 3) $referenciadorCuenta = $app_list_strings['ejecutivo_c_list'][$bean->ejecutivo_c];
                        $body_cuenta = array(
                            "int_etapa" => "",
                            "int_duenio" => "",
                            "ownerid" => "",
                            "int_tipo_persona" => ($cuenta->tipodepersona_c == 'Persona Moral') ? 'Persona Moral' : 'Persona Física',
                            "name"=> ($cuenta->tipodepersona_c == 'Persona Moral') ? $cuenta->name : $cuenta->primernombre_c,
                            "int_primer_apellido" => $cuenta->apellidopaterno_c,
                            "int_segundo_apellido" => $cuenta->apellidomaterno_c,
                            "int_sexo" => $cuenta->genero_c,
                            "int_tipo_cuenta" => "Prospecto",
                            "int_tipo_documento_id" => "",
                            "int_cliente_autorizado" => "",
                            "int_fecha_nac_const" => ($cuenta->tipodepersona_c == 'Persona Moral') ? $cuenta->fechaconstitutiva_c : $cuenta->fechadenacimiento_c,
                            "int_rfc" => $cuenta->rfc_c,
                            "int_sector_id" => "",
                            "int_subsector_id" => "",
                            "int_rama_id" => "",
                            "int_subramo_id" => "",
                            "transactioncurrencyid" => "",
                            "int_tipo_cliente_id" => "",
                            "int_referenciador_cuenta" => $referenciadorCuenta,
                            "telephone1" => $cuenta->phone_office,
                            "emailaddress1" =>$cuenta->email1 ,
                            "int_pais_id" => "PAIS-0000000152",
                            "int_codigo_postal_id" => "CODP-0000063899",
                            "int_estado_id" => "ENTFED-0000000041",
                            "int_ciudad_id" => "CIUDAD-0000000262",
                            "int_colonia_id" => "COL-0000016388",
                            "address1_line1" => "MIGUEL DE CEERVANTES",
                            "address1_line2" => "301",
                            "address1_line3" => "1",
                            "statecode" => "",
                            "statuscode" => ""
                        );

                        $content = json_encode($body_cuenta);
                        $GLOBALS['log']->fatal('Seguros_Dynamics - Crea nueva: '. $url);
                        $GLOBALS['log']->fatal($content);
                        $curl = curl_init($url);
                        curl_setopt($curl, CURLOPT_HEADER, false);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curl, CURLOPT_HTTPHEADER,
                        array("Authorization: Bearer $token",
                            "Content-type: application/json"));
                        curl_setopt($curl, CURLOPT_POST, true);
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
                        $json_response = curl_exec($curl);
                        curl_close($curl);
                        $response = json_decode($json_response, true);
                        $GLOBALS['log']->fatal($response);
                        if($response['id_CRM']!='' && $response['id_CRM']!='Null'){
                            $id_dyn = $response['id_CRM'];
                            //Update del id dynamics de la cuenta
                            global $db;
                            $update = "update accounts_cstm set
                            int_id_dynamics_c='{$id_dyn}'
                            where id_c = '{$cuenta->id}'";
                            $updateExecute = $db->query($update);
                            $cuenta->int_id_dynamics_c = $id_dyn;
                        }else{
                            $this->setErrorLogFailRequest( "Account", $bean, $url, $content, json_encode($response) );
                            throw new SugarApiExceptionInvalidParameter("No se puede guardar información en Dynamics. Error al procesar cuenta: ".$response['description']);
                        }
                    }
                }
            }
            else
            {
              $this->setErrorLogFailRequest("TokenGenerator",$bean,$sugar_config['inter_dynamics_token_url'], "NA", json_encode($token) );
              throw new SugarApiExceptionInvalidParameter("Servicio de Dynamics no disponible");

            }
          }
        }
        //Prospecto
        if($bean->etapa == 1 && !$bean->int_id_dynamics_c && !$bean->seguro_uni2_c)
        {
          global $db;
          $token = $this->getToken();
          if($token)
          {
            $tipoSeguro= $app_list_strings['dynamics_tipo_list'][$bean->tipo_venta_c];
            $recordTypeId = $app_list_strings['tipo_registro_sf_list'][$bean->tipo_registro_sf_c];
            $ramoID = $app_list_strings['negocio_dynamics_list'][$bean->tipo];
            $subRamo = $app_list_strings['subramos_id_dyn_list'][$bean->subramos_c];
            $areaID= $app_list_strings['area_dyn_map_list'][$bean->tipo."_".$bean->area];
            $oficinaID= $app_list_strings['oficina_dyn_map_list'][$bean->oficina_c];
            $tipoPolizaID= $app_list_strings['poliza_dynamics_list'][$bean->tipo_poliza_c];
            $divisa=$app_list_strings['monedas_list'][$bean->monedas_c];
            $type = $app_list_strings['tipo_sf_list'][$bean->tipo_sf_c];
            $tipoDeRegistroC = $app_list_strings['area_r_list'][$bean->area];
            $ramoC = $app_list_strings['subramos_list'][$bean->subramos_c];
            $currencyIsoCode = $app_list_strings['monedas_list'][$bean->monedas_c];
            $oportunidadInternacionalC = ($bean->nacional_c) ? $app_list_strings['nacional_dynamics_list'][$bean->nacional_c]:'Nacional';
            $oficinaC = $app_list_strings['oficina_list'][$bean->oficina_c];
            $kamC = $app_list_strings['kam_list'][$bean->kam_c];
            $idUsuario = ($bean->tipo_referenciador == 1) ? $bean->user_id1_c : $bean->user_id2_c;
            $referenciadorDeLaOportunidadC = $app_list_strings['referenciador_list'][$bean->referenciador_c];
            $nombreUsuarioQuery = "Select nombre_completo_c from users_cstm where id_c ='{$idUsuario}'";
            $resultUsuario = $db->query($nombreUsuarioQuery);
            $referenciador = $db->fetchByAssoc($resultUsuario);
            if($bean->tipo_referenciador == 1) $referenciadorCuenta = $referenciador['nombre_completo_c']." ".$bean->region;
            if($bean->tipo_referenciador == 2) $referenciadorCuenta = $referenciador['nombre_completo_c']." ".$bean->departamento_c;
            if($bean->tipo_referenciador == 3) $referenciadorCuenta = $app_list_strings['ejecutivo_c_list'][$bean->ejecutivo_c];

            $ejecutivo_c = $app_list_strings['ejecutivo_list'][$bean->ejecutivo_c];
            $vendedorID= $app_list_strings['int_vendedor_id_list'][1];
            $stageName = $app_list_strings['etapa_seguros_list'][$bean->etapa];
            $closeDate = $bean->fecha_cierre_c;
            $closeDate = date("d/m/Y", strtotime($closeDate));
                $url = $sugar_config['inter_dynamics_url'].'Opportunity';
                $arreglo = array(
                "int_etapa" => "Prospectando",
                "statuscode" => "Contactado",
                "int_tipo_opp" => $recordTypeId,
                "parentaccountid" => $cuenta->int_id_dynamics_c,
                "int_tipo" => $tipoSeguro,
                "int_area_responsable_id" => $areaID,
                "int_ramo_id" => $ramoID,
                "int_subramo_id"=>$subRamo,
                "transactioncurrencyid" => $divisa,
                "int_oportunidad_internacional" => $oportunidadInternacionalC,
                "int_localidad_id" => $oficinaID,
                "int_kam_santander_unifin_id" => $ejecutivo_c,
                "int_vendedor_id" => $vendedorID,  //Unifin-Unifin
                "actualclosedate" => $bean->fecha_cierre_c,
                "int_prima_total_objetivo" => $bean->prima_obj_c,
                "int_ing_objetivo_porcentaje" => "",
                "estimatedvalue" => "",
                "ownerid" => "",
                "int_tipo_poliza" => $tipoPolizaID,
                "int_id_sugar" => $bean->id,
                "name" => $bean->name
            );
            $content = json_encode($arreglo);
            $GLOBALS['log']->fatal('Seguros_Dynamics - Crea oportunidad - Prospecto: '. $url);
            $GLOBALS['log']->fatal($content);
            $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER,
                array("Authorization: Bearer $token",
                    "Content-type: application/json"));
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
                $json_response = curl_exec($curl);
                curl_close($curl);
            $response = json_decode($json_response, true);
            $GLOBALS['log']->fatal('Creacion de Prospecto (Cliente- Dynamics) enviada: ' .$response);
            $GLOBALS['log']->fatal(json_encode(print_r($response,true)));
            if($response['id_CRM']!='' && $response['id_CRM']!='Null'){
                $id_op = $response['id_CRM'];
                $bean->int_id_dynamics_c = $id_op;
            }
            else
            {

              $this->setErrorLogFailRequest( "Opportunity", $bean, $url, $content, json_encode($token) );
              throw new SugarApiExceptionInvalidParameter("No se puede guardar información en Dynamics. ".$response['description']);
            }
          }
          else
          {
            $this->setErrorLogFailRequest("TokenGenerator",$bean,$sugar_config['inter_dynamics_token_url'], "NA", json_encode($token) );
            throw new SugarApiExceptionInvalidParameter("Servicio de Dynamics no disponible");
          }
        }
        //Cotizando
        if($bean->fetched_row['etapa'] != $bean->etapa && $bean->etapa == 2 && !$bean->seguro_uni2_c)
        {
          $token = $this->getToken();
          if($token)
          {
            $serviciosaincluirc = $app_list_strings['servicios_a_incluir_c_list'][$bean->servicios_a_incluir_c];
            $stageName = $app_list_strings['etapa_seguros_list'][$bean->etapa];
            $fechaRequierePropuestaC = $bean->fecha_req;
            $fechaRequierePropuestaC = date("d/m/Y", strtotime($fechaRequierePropuestaC));
            $requiereAyudaDeReaTcnica = ($bean->requiere_ayuda_c == 2) ? 'Sí' : 'No';
                $url = $sugar_config['inter_dynamics_url'].'Opportunity/'.$bean->int_id_dynamics_c;
            //Valida etapa previa
            if($bean->fetched_row['etapa'] == 3)
            {
                //Etapa previa es revisión; sólo manda 3 valores
                $content = json_encode(array(
                  "int_etapa"=>"Cotizando",
                  "statuscode"=>"Contactado",
                  "int_datos_completos" =>"Sí"
                ));
            }else{
                // Flujo normal, envía toda la información
                $content = json_encode(array(
                    "int_etapa" => "Cotizando",
                    "statuscode" => "Contactado",
                    "int_ayuda_area_tecnica" =>  $requiereAyudaDeReaTcnica,
                    "int_fecha_comp_comercial" => $bean->fecha_cierre_c,
                    "int_fecha_req_propuesta" => $bean->fecha_req,
                    "description" => $bean->description,
                    "int_servicio_incluir" => $serviciosaincluirc,
                    "int_ing_objetivo_porcentaje"=>"1"
                ));
            }
            $GLOBALS['log']->fatal('Seguros_Dynamics - Actualiza oportunidad - Cotizando: '. $url);
            $GLOBALS['log']->fatal($content);
            $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER,
                array("Authorization: Bearer $token",
                    "Content-type: application/json"));
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
                $json_response = curl_exec($curl);
                curl_close($curl);
            $GLOBALS['log']->fatal('Informacion de Cotizando enviada: ' .$json_response);
            $response = json_decode($json_response, true);
            if($response['critical'] == 'true'){

              $this->setErrorLogFailRequest( "Opportunity", $bean, $url, $content, json_encode($response) );
              throw new SugarApiExceptionInvalidParameter("No se puede guardar. ".$response);
            } 
              
          }
          else
          {
            $this->setErrorLogFailRequest("TokenGenerator",$bean,$sugar_config['inter_dynamics_token_url'], "NA", json_encode($token) );
            throw new SugarApiExceptionInvalidParameter("Servicio de SalesForce no disponible");
          }
        }
        //Cotizado
        if($bean->fetched_row['etapa'] != $bean->etapa && $bean->etapa == 4 && $bean->tipo_registro_sf_c == 1 && !$bean->seguro_uni2_c && $bean->tipo_registro_sf_c!='1')
        {
          $token = $this->getToken();
          if($token)
          {
            $stageName = $app_list_strings['etapa_seguros_list'][$bean->etapa];
                $url = $sugar_config['inter_dynamics_url'].'Opportunity/'.$bean->int_id_dynamics_c;
                $content = json_encode(array(
              "int_etapa" => "Cotizando",
              "statuscode" => "Cotizado"
            ));
            $GLOBALS['log']->fatal('Seguros_Dynamics - Actualiza oportunidad - Cotizado: '. $url);
            $GLOBALS['log']->fatal($content);
            $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER,
                array("Authorization: Bearer $token",
                    "Content-type: application/json"));
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
                $json_response = curl_exec($curl);
            $GLOBALS['log']->fatal('Informacion de Cotizado enviada: ' .$json_response);
                curl_close($curl);
                $response = json_decode($json_response, true);
            if($response['critical'] == 'true'){
              $this->setErrorLogFailRequest( "Opportunity", $bean, $url, $content, json_encode($response) );
              throw new SugarApiExceptionInvalidParameter("No se puede guardar. ".$response);
            } 
          }
          else
          {
            $this->setErrorLogFailRequest("TokenGenerator",$bean,$sugar_config['inter_dynamics_token_url'], "NA", json_encode($token) );
            throw new SugarApiExceptionInvalidParameter("Servicio de Dynamics no disponible");
          }
        }
        //No Cotizado
        if($bean->fetched_row['etapa'] != $bean->etapa && $bean->etapa == 5 && $bean->tipo_registro_sf_c == 1 && !$bean->seguro_uni2_c)
        {
          $token = $this->getToken();
          if($token)
          {
            $stageName = $app_list_strings['etapa_seguros_list'][$bean->etapa];
            $motivo = $app_list_strings['motivo_no_cotizado_list'][$bean->motivo_no_cotizado_c];
                $url = $sugar_config['inter_dynamics_url'].'Opportunity/'.$bean->int_id_dynamics_c;
                $content = json_encode(array(
              "int_etapa" => "Cotizando",
              "statuscode" => "Contactado",
              "int_motivo_no_cotizada" => $motivo,
              "int_razon_perdida"=>"No se pudo colocar",
              "int_comentarios_razon_perdida"=>"Prueba comentarios razón perdida",
              "int_fecha_inicio_vigencia_op"=>"2022-06-28T00:00:00.000",
              "int_fecha_fin_vigencia_op"=>"2023-06-28T00:00:00.000",
              "int_motivo_no_cotizada"=>"No se pudo colocar"
            ));
            $GLOBALS['log']->fatal('Seguros_Dynamics - Actualiza oportunidad - No Cotizado: '. $url);
            $GLOBALS['log']->fatal($content);
            $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER,
                array("Authorization: Bearer $token",
                    "Content-type: application/json"));
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
                $json_response = curl_exec($curl);
            $GLOBALS['log']->fatal('Informacion de No Cotizado enviada: ' .$json_response);
                curl_close($curl);
                $response = json_decode($json_response, true);
            if($response['critical'] == 'true'){
              $this->setErrorLogFailRequest( "Opportunity/".$bean->int_id_dynamics_c, $bean, $url, $content, json_encode($response) );
              throw new SugarApiExceptionInvalidParameter("No se puede guardar. ".$response);
            } 
          }
          else
          {
            $this->setErrorLogFailRequest("TokenGenerator",$bean,$sugar_config['inter_dynamics_token_url'], "NA", json_encode($token) );
            throw new SugarApiExceptionInvalidParameter("Servicio de Dynamics no disponible");
          }
        }
        //Presentación
        if($bean->fetched_row['etapa'] != $bean->etapa && $bean->etapa == 6 && !$bean->seguro_uni2_c)
        {
          $token = $this->getToken();
          if($token)
          {
            $stageName = $app_list_strings['etapa_seguros_list'][$bean->etapa];
                $url = $sugar_config['inter_dynamics_url'].'Opportunity/'.$bean->int_id_dynamics_c;
            if($bean->tipo_registro_sf_c=='1'){
              $content = json_encode(array(
                "int_etapa" => "Presentando",
                "statuscode" => "Contactado"

              ));
            }else{
              $content = json_encode(array(
                "int_etapa" => "Presentando",
                "statuscode" => "Cotizado"

              ));
            }
            $GLOBALS['log']->fatal('Seguros_Dynamics - Actualiza oportunidad - Presentación: '. $url);
            $GLOBALS['log']->fatal($content);
            $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER,
                array("Authorization: Bearer $token",
                    "Content-type: application/json"));
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
                $json_response = curl_exec($curl);
            $GLOBALS['log']->fatal('Informacion de Presentación enviada: ' .$json_response);
                curl_close($curl);
                $response = json_decode($json_response, true);
            if($response['critical'] == 'true'){
              $this->setErrorLogFailRequest( "Opportunity/".$bean->int_id_dynamics_c, $bean, $url, $content, json_encode($response) );
              throw new SugarApiExceptionInvalidParameter("No se puede guardar. ".$response);
            } 
          }
          else
          {
            $this->setErrorLogFailRequest("TokenGenerator",$bean,$sugar_config['inter_dynamics_token_url'], "NA", json_encode($token) );
            throw new SugarApiExceptionInvalidParameter("Servicio de Dynamics no disponible");
          }
        }
        //Re-negociación
        if($bean->fetched_row['etapa'] != $bean->etapa && $bean->etapa == 7 && !$bean->seguro_uni2_c)
        {
          $token = $this->getToken();
          if($token)
          {
            $stageName = $app_list_strings['etapa_seguros_list'][$bean->etapa];
            $url = $sugar_config['inter_dynamics_url'].'Opportunity/'.$bean->int_id_dynamics_c;
                $content = json_encode(array(
              "int_etapa" => "Cotizando",
              "statuscode" => "Recotizar",
              "qualificationcomments" => $bean->motivos_c
            ));
            $GLOBALS['log']->fatal('Seguros_Dynamics - Actualiza oportunidad - Re-negociación: '. $url);
            $GLOBALS['log']->fatal($content);
            $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER,
                array("Authorization: Bearer $token",
                    "Content-type: application/json"));
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
                $json_response = curl_exec($curl);
            $GLOBALS['log']->fatal('Informacion de Re-negociacon enviada: ' .$json_response);
            curl_close($curl);
            $response = json_decode($json_response, true);
            if($response['critical'] == 'true'){
              $this->setErrorLogFailRequest( "Opportunity/".$bean->int_id_dynamics_c, $bean, $url, $content, json_encode($response) );
              throw new SugarApiExceptionInvalidParameter("No se puede guardar. ".$response);
            } 
          }
          else
          {
            $this->setErrorLogFailRequest("TokenGenerator",$bean,$sugar_config['inter_dynamics_token_url'], "NA", json_encode($token) );
            throw new SugarApiExceptionInvalidParameter("Servicio de Dynamics no disponible");
          }
        }
        //Ganada
        if($bean->fetched_row['etapa'] != $bean->etapa && $bean->etapa == 9 && !$bean->seguro_uni2_c)
        {
            //Validamos que se tenga una Cotizacion como Ganada para continuar el proceso
            $idCotizacion = "";
            if($bean->load_relationship('cot_cotizaciones_s_seguros')){
              $beansCotizaciones = $bean->cot_cotizaciones_s_seguros->getBeans();
              if (!empty($beansCotizaciones)) {
                  foreach($beansCotizaciones as $cotizacion){
                      if($cotizacion->cot_ganada_c==true){
                        $idCotizacion = $cotizacion->int_id_dynamics;
                      }
                  }
              }
            }

            if(empty($idCotizacion)){
              throw new SugarApiExceptionInvalidParameter("Se requiere una cotización marcada como ganada para poder avanzar.");
            }

            $token = $this->getToken();
            if($token)
            {
              $stageName = $app_list_strings['etapa_seguros_list'][$bean->etapa];
              $forma_pago = $app_list_strings['forma_pago_dynamics_list'][$bean->forma_pago];
              $currencyIsoCode = $app_list_strings['monedas_list'][$bean->monedas_c];
              $ejecutivo_c = $app_list_strings['ejecutivo_list'][$bean->ejecutivo_c];
              $ejecutivoDynamics= $app_list_strings['int_ejecutivo_id_list'][1];
              $equipoServicioDynamics= $app_list_strings['int_ejecutivo_id_list'][2];
              $fecha_ini_c = $bean->fecha_ini_c;
              $fecha_ini_c = date("d/m/Y", strtotime($fecha_ini_c));
              $fecha_fin_c = $bean->fecha_fin_c;
              $fecha_fin_c = date("d/m/Y", strtotime($fecha_fin_c));
              $url = $sugar_config['inter_dynamics_url'].'Opportunity/'.$bean->int_id_dynamics_c;
              $content = json_encode(array(
                "int_etapa" => "Cierre",
                "statuscode" => "Cotizado",
                "int_ganada_perdida" => "Sí",
                "int_fee_emitir" => $bean->fee_c,
                "int_fee_emitir_porcentaje" => $bean->fee_p_c,
                "int_forma_pago_emitida" => $forma_pago,
                "int_cotizacion_ganadora_id" => $idCotizacion, //Cotización ganadora
                "int_fecha_inicio_vigencia_op" => $bean->fecha_ini_c,
                "int_fecha_fin_vigencia_op" => $bean->fecha_fin_c,
                "int_ganada_cambio_conducto" => "Sí",
                "int_equipo_servicio_id" => $equipoServicioDynamics,
                "int_ejecutivo_servicio_id" => $ejecutivoDynamics // $ejecutivo_c
              ));
              $GLOBALS['log']->fatal('Seguros_Dynamics - Actualiza oportunidad - Ganada: '. $url);
              $GLOBALS['log']->fatal($content);
              $curl = curl_init($url);
              curl_setopt($curl, CURLOPT_HEADER, false);
              curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($curl, CURLOPT_HTTPHEADER,
              array("Authorization: Bearer $token",
                "Content-type: application/json"));
              curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
              curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
              $json_response = curl_exec($curl);
              $GLOBALS['log']->fatal('Informacion de Ganada enviada: ' .$json_response);
              curl_close($curl);
              $response = json_decode($json_response, true);
              if($response['critical'] == 'true'){
                $this->setErrorLogFailRequest( "Opportunity/".$bean->int_id_dynamics_c, $bean, $url, $content, json_encode($response) );
                throw new SugarApiExceptionInvalidParameter("No se puede guardar. ".$response);
              } 
            }
            else
            {
              $this->setErrorLogFailRequest("TokenGenerator",$bean,$sugar_config['inter_dynamics_token_url'], "NA", json_encode($token) );
              throw new SugarApiExceptionInvalidParameter("Servicio de Dynamics no disponible");
            }
        }
        //No Ganada
        if($bean->fetched_row['etapa'] != $bean->etapa && $bean->etapa == 10 && !$bean->seguro_uni2_c)
        {
          $token = $this->getToken();
          if($token)
          {
            $stageName = $app_list_strings['etapa_seguros_list'][$bean->etapa];
            $razonPerdida = $app_list_strings['razon_perdida_list'][$bean->razon_perdida_c];
            $no_renovable_c = $app_list_strings['no_renovable_list'][$bean->no_renovable_c];
            $url = $sugar_config['inter_dynamics_url'].'Opportunity/'.$bean->int_id_dynamics_c;
                $arreglo = array(
              "int_etapa" => "Cierre",
              "statuscode" => "Cotizado",
              "int_ganada_perdida" => "No",
              "int_razon_perdida" => $razonPerdida,
              "int_comentarios_razon_perdida" => $bean->comentarios_c,
              "int_fecha_inicio_vigencia_op" => $bean->fecha_ini_c,
              "int_fecha_fin_vigencia_op" => $bean->fecha_fin_c
            );
            if($bean->tipo_registro_sf_c == 1) unset($arreglo['ramoNoRenovablec']);
            $content = json_encode($arreglo);
            $GLOBALS['log']->fatal('Seguros_Dynamics - Actualiza oportunidad - No Ganada: '. $url);
            $GLOBALS['log']->fatal($content);
            $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER,
                array("Authorization: Bearer $token",
                    "Content-type: application/json"));
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
                $json_response = curl_exec($curl);
            $GLOBALS['log']->fatal('Informacion de No Ganada enviada: ' .$json_response);
            curl_close($curl);
            $response = json_decode($json_response, true);
            if($response['critical'] == 'true'){
              $this->setErrorLogFailRequest( "Opportunity/".$bean->int_id_dynamics_c, $bean, $url, $content, json_encode($response) );
              throw new SugarApiExceptionInvalidParameter("No se puede guardar. ".$response);
            } 
          }
          else
          {
            $this->setErrorLogFailRequest("TokenGenerator",$bean,$sugar_config['inter_dynamics_token_url'], "NA", json_encode($token) );
            throw new SugarApiExceptionInvalidParameter("Servicio de Dynamics no disponible");
          }
        }
    }

    public function getToken()
    {
      //Obtiene Token
      global $sugar_config;
      $loginurl = $sugar_config['inter_dynamics_token_url'];
      $usr = $sugar_config['inter_dynamics_usr'];
      $psw = $sugar_config['inter_dynamics_psw'];
      $params = json_encode(array(
          'name'=>$usr,
          'password'=>$psw,
          'int_serv_servicio'=>'Valida Token'
      ));
      $GLOBALS['log']->fatal('Petición Token Dynamics INTER: '. $loginurl );
      $curl = curl_init($loginurl);
      curl_setopt($curl, CURLOPT_HEADER, false);
      curl_setopt($curl, CURLOPT_HTTPHEADER,
                array(
                    "Content-type: application/json"));
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
      $response = curl_exec($curl);
      $response = json_decode($response, true);
      $GLOBALS['log']->fatal($response);
        curl_close($curl);
        return $response['Token'];
    }

    public function setErrorLogFailRequest( $endpoint, $bean, $url, $request, $response ){

      $GLOBALS['log']->fatal("Enviando notificación para bitácora de errores S_seguros");
      require_once("custom/clients/base/api/ErrorLogApi.php");
      $apiErrorLog = new ErrorLogApi();
      $args = array(
        "integration"=> "Inter Seguros: ".$endpoint,
        "system"=> "Inter Seguros",
        "parent_type"=> "S_seguros",
        "parent_id"=> $bean->id,
        "endpoint"=> $url,
        "request"=> $request,
        "response"=> $response
      );
      $responseErrorLog = $apiErrorLog->setDataErrorLog(null, $args);

    }
}
