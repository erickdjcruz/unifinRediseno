<?php

if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class cambiosRazonSocialExterno extends SugarApi
{
    public function registerApiRest()
    {
        return array(
            'verificar_razonsocial_ext' => array(
                'reqType' => 'GET',
                'noLoginRequired' => false,
                'path' => array('verificar_razonsocialExterno', '?', '?'),
                'pathVars' => array('metodo', 'rfc', 'id'),
                'method' => 'getVerificarRazonSocial',
                'shortHelp' => 'Obtiene estructura del campo edit para obtener cambios en dirección fiscal de la cuenta relacionada',
                'longHelp' => '',
            ),
            'GestionarCambiosRazonSocialFiscalExt' => array(
                'reqType' => 'POST',
                'noLoginRequired' => false,
                'path' => array('ValidarCambiosRazonSocialFiscalExterno'),
                'pathVars' => array('metodo'),
                'method' => 'gestionarCambiosRazonSocialExt',
                'shortHelp' => 'Aprueba o rechaza cambios de razón social y muestra el valor actualizado de la cuenta',
                'longHelp' => '',
            ),
        );
    }

    /*
    * Obtiene dirección fiscal de cuenta que tiene valor en campo json_audit_c
    */
    public function getVerificarRazonSocial($api, $args)
    {
        $rfc = isset($args['rfc']) ? trim($args['rfc']) : '';
        $id_cuenta = isset($args['id']) ? trim($args['id']) : '';
        $array_json_audit = array();

        if (empty($rfc) || empty($id_cuenta)) {
            throw new SugarApiExceptionInvalidParameter(
                'Los parámetros de RFC y ID Cuenta son obligatorios para esta petición.'
            );
        }

        // Consulta simplificada para obtener el JSON de auditoría
        $queryAudit = "SELECT rfc_c, json_audit_c FROM accounts_cstm 
                      WHERE rfc_c = '{$rfc}' AND id_c = '{$id_cuenta}' 
                      AND json_audit_c IS NOT NULL AND json_audit_c != ''";
        
        $GLOBALS['log']->fatal("Consulta GetVerificarRazonSocial: " . $queryAudit);

        $results = $GLOBALS['db']->query($queryAudit);
        if ($results->num_rows > 0) {
            while ($row = $GLOBALS['db']->fetchByAssoc($results)) {
                $json_audit = $row['json_audit_c'];
                break; // Solo necesitamos el primer resultado
            }
        }

        // Decodificar JSON para validar estructura
        $datos_cambio = array();
        if (!empty($json_audit)) {
            $datos_cambio = json_decode($json_audit, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new SugarApiExceptionInvalidParameter('El formato del JSON de auditoría es inválido.');
            }
        }

        /*return array(
            //'json_audit' => $json_audit,
            'datos_cambio' => $datos_cambio
        );*/
        return $datos_cambio;
    }

    public function gestionarCambiosRazonSocialExt($api, $args)
    {
        global $current_user, $app_list_strings;;
        $response = array();
        $date = TimeDate::getInstance()->nowDb();
        
        // Validar parámetros requeridos
        if (empty($args['accion']) || empty($args['idCuenta']) ) {
            throw new SugarApiExceptionInvalidParameter(
                'Los parámetros "accion" (aprobado/rechazado), "idCuenta"  son obligatorios.'
            );
        }
        
        $accion = $args['accion']; // 'aprobado' o 'rechazado'
        $id_cuenta = $args['idCuenta'];
        $rfc = $args['rfc'];
        
        if ($accion === 'rechazado') {
            if (empty($args['razon_rechazo'])) {
                throw new SugarApiExceptionInvalidParameter(
                    'El parámetro "razon_rechazo" es obligatorio cuando la acción es "rechazado".'
                );
            }
        
            // Validar que el motivo de rechazo no esté vacío o solo contenga espacios
            $razon_rechazo = trim($args['razon_rechazo']);
            if (empty($razon_rechazo)) {
                throw new SugarApiExceptionInvalidParameter(
                    'El motivo de rechazo no puede estar vacío o contener solo espacios.'
                );
            }
        }

        $beanCuenta = BeanFactory::getBean('Accounts', $id_cuenta, array('disable_row_level_security' => true));
        
        if (empty($beanCuenta->id)) {
            throw new SugarApiExceptionInvalidParameter('La cuenta especificada no existe.');
        }

        // Obtener información actual de la cuenta para la respuesta
        $cuentaInfo = array(
            'id' => $beanCuenta->id,
            'nombre_actual' => $beanCuenta->name,
            'rfc_actual' => $beanCuenta->rfc_c,
            'tipo_persona_actual' => $beanCuenta->tipo_registro_c,
            'razon_social_actual' => $beanCuenta->razonsocial_c,
            'nombre_comercial_actual' => $beanCuenta->nombre_comercial_c,
            'denominacion_actual' => $beanCuenta->denominacion_c,
            'regimen_capital_actual' => $beanCuenta->regimen_capital_c,
            'primer_nombre_actual' => $beanCuenta->primernombre_c,
            'apellido_paterno_actual' => $beanCuenta->apellidopaterno_c,
            'apellido_materno_actual' => $beanCuenta->apellidomaterno_c
        );

        // Obtener el JSON de cambios pendientes desde la base de datos
        $json_cambios = $this->obtenerJsonCambiosPendientes($id_cuenta, $rfc);
        $datos_cambio = json_decode($json_cambios, true);
        
        if (empty($datos_cambio)) {
            throw new SugarApiExceptionInvalidParameter('No se encontraron cambios pendientes para esta cuenta.');
        }

        $GLOBALS['log']->fatal("Datos de cambio obtenidos: " . print_r($datos_cambio, true));
        
        if ($accion === 'aprobado') {
            $GLOBALS['log']->fatal("aprobado");
            // Lógica para aprobar cambios usando los datos del JSON
            if ($datos_cambio['tipo'] !== 'Persona Moral') {
                // Actualizar datos para persona física
                $this->actualizarCuentaPorQuery($id_cuenta, 
                array('name' => $datos_cambio['nombre_por_actualizar']),
                array(
                    'primernombre_c' => $datos_cambio['primer_nombre_por_actualizar'],
                    'apellidopaterno_c' => $datos_cambio['paterno_por_actualizar'],
                    'apellidomaterno_c' => $datos_cambio['materno_por_actualizar']
                ));
            } else {
                // Actualizar datos para persona moral
                $this->actualizarCuentaPorQuery($id_cuenta, 
                array('name' => $datos_cambio['nombre_por_actualizar']),
                array(
                    'razonsocial_c' => $datos_cambio['razon_social_por_actualizar'],
                    'nombre_comercial_c' => $datos_cambio['razon_social_por_actualizar'],
                    'denominacion_c' => $datos_cambio['denominacion_por_actualizar'],
                    'regimen_capital_c' => $datos_cambio['regimen_capital_por_actualizar']
                ));
            }
            
            // Actualizar nombre en solicitudes relacionadas
            if (!empty($datos_cambio['nombre_actual']) && !empty($datos_cambio['nombre_por_actualizar'])) {
                $this->setNombreCuentaEnSolicitudes($beanCuenta, $datos_cambio['nombre_actual'], $datos_cambio['nombre_por_actualizar']);
            }
            
            $response['mensaje'] = "Cambios de cuenta aprobados correctamente";
            
            // Resetear banderas después de aprobar
            $this->reestableceBanderasCuentaAprobado($id_cuenta, $current_user->id, $date);
            $this->insertAuditAccion($id_cuenta,'aprobado');

            $response['estado'] = 'aprobado';

            $this->actualizarDatosEnSistemaExterno($beanCuenta, $datos_cambio);
            
        } elseif ($accion === 'rechazado') {
            $GLOBALS['log']->fatal("rechazado");
            // Lógica para rechazar cambios
            //$razon_rechazo = isset($args['razon_rechazo']) ? $args['razon_rechazo'] : 'Sin razón especificada';
            $razon_rechazo = trim($args['razon_rechazo']);

            // Revertir cambios a los valores anteriores
            if ($datos_cambio['tipo'] === 'Persona Moral') {
                $this->actualizarCuentaPorQuery($id_cuenta, 
                array('name' => $datos_cambio['nombre_actual']),
                array(
                    'razonsocial_c' => $datos_cambio['razon_social_actual'],
                    'nombre_comercial_c' => $datos_cambio['razon_social_actual'],
                    'denominacion_c' => $datos_cambio['denominacion_actual'],
                    'regimen_capital_c' => $datos_cambio['regimen_capital_actual']
                ));
            } else {
                $this->actualizarCuentaPorQuery($id_cuenta, 
                array('name' => $datos_cambio['nombre_actual']),
                array(
                    'primernombre_c' => $datos_cambio['primer_nombre_actual'],
                    'apellidopaterno_c' => $datos_cambio['paterno_actual'],
                    'apellidomaterno_c' => $datos_cambio['materno_actual']
                ));
            }

            // Resetear banderas en BD
            $this->reestableceBanderasCuentaRechazado($id_cuenta, $current_user->id, $date);
            $this->insertAuditAccion($id_cuenta,'rechazado');
            
            // Guardar razón de rechazo
            $this->saveRazonRechazo($id_cuenta, $razon_rechazo);
            
            // Enviar correo de notificación
            $bodyCorreo = $this->buildBodyCorreoRechazo($beanCuenta->name, $razon_rechazo);
            //$emailsDestinatarios = $this->getUsuariosDestinatariosRechazo($beanCuenta->user_id_c);
            $emailsDestinatarios = $app_list_strings['emais_juridico_aprobacion_rs_list'];
            $this->sendEmailRechazo($beanCuenta->name, $emailsDestinatarios, $bodyCorreo);
            
            $response['mensaje'] = "Cambios de cuenta rechazados";
            $response['razon_rechazo'] = $razon_rechazo;
            $response['estado'] = 'rechazado';
        } else {
            throw new SugarApiExceptionInvalidParameter('La acción debe ser "aprobado" o "rechazado".');
        }
        
        // Obtener información actualizada de la cuenta para la respuesta
        $beanCuentaActualizada = BeanFactory::getBean('Accounts', $id_cuenta, array('disable_row_level_security' => true));
        $beanCuentaActualizada->save();
        $cuentaInfo['nombre_actualizado'] = $beanCuentaActualizada->name;
        $cuentaInfo['razon_social_actualizado'] = $beanCuentaActualizada->razonsocial_c;
        $cuentaInfo['nombre_comercial_actualizado'] = $beanCuentaActualizada->nombre_comercial_c;
        $cuentaInfo['denominacion_actualizado'] = $beanCuentaActualizada->denominacion_c;
        $cuentaInfo['regimen_capital_actualizado'] = $beanCuentaActualizada->regimen_capital_c;
        $cuentaInfo['primer_nombre_actualizado'] = $beanCuentaActualizada->primernombre_c;
        $cuentaInfo['apellido_paterno_actualizado'] = $beanCuentaActualizada->apellidopaterno_c;
        $cuentaInfo['apellido_materno_actualizado'] = $beanCuentaActualizada->apellidomaterno_c;
        
        // Incluir los datos del cambio en la respuesta
        //$response['datos_cambio'] = $datos_cambio;
        $response['cuenta_actualizada'] = $cuentaInfo;
        $response['fecha_procesamiento'] = $date;
        $response['usuario_procesamiento'] = $current_user->user_name;
        
        return $response;
    }
    
    /**
     * Actualiza campos de la cuenta directamente en base de datos
     */
    private function actualizarCuentaPorQuery($id_cuenta, $campos, $camposcstm)
    {
        $updates = array();
        foreach ($campos as $campo => $valor) {
            $valor_escaped = $GLOBALS['db']->quote($valor);
            $updates[] = "{$campo} = '{$valor_escaped}'";
        }

        foreach ($camposcstm as $campocs => $valorc) {
            $valor_escaped = $GLOBALS['db']->quote($valorc);
            $updatescstm[] = "{$campocs} = '{$valor_escaped}'";
        }
        
        $query = "UPDATE accounts SET " . implode(', ', $updates) . " WHERE id = '{$id_cuenta}'";
        $GLOBALS['log']->fatal("Actualización directa cuenta: " . $query);
        $GLOBALS['db']->query($query);
        
        $query_cstm = "UPDATE accounts_cstm SET " . implode(', ', $updatescstm) . " WHERE id_c = '{$id_cuenta}'";
        $GLOBALS['log']->fatal("Actualización directa accounts_cstm: " . $query_cstm);
        $GLOBALS['db']->query($query_cstm);
        
    }

    /**
     * Reestablece banderas cuando se aprueban cambios
     */
    private function reestableceBanderasCuentaAprobado($id_cuenta, $id_usuario, $fecha)
    {
        $queryUpdateBanderasAccount = "UPDATE accounts_cstm SET 
            valid_cambio_razon_social_c = '0', 
            cambio_nombre_c = '0', 
            cambio_dirfiscal_c = '0', 
            json_audit_c = '', 
            user_id9_c = '{$id_usuario}', 
            fecha_aprueba_rechaza_c = '{$fecha}', 
            json_direccion_audit_c = '', 
            omitir_guardado_direcciones_c = '0', 
            accion_cambio_fiscal_c = 'Aprobó', 
            direccion_actualizada_api_c = '0' 
            WHERE id_c = '{$id_cuenta}'";
        
        $GLOBALS['log']->fatal("UPDATE BANDERAS DE CUENTA APROBADO: " . $queryUpdateBanderasAccount);
        $GLOBALS['db']->query($queryUpdateBanderasAccount);
    }

    /**
     * Reestablece banderas cuando se rechazan cambios
     */
    private function reestableceBanderasCuentaRechazado($id_cuenta, $id_usuario, $fecha)
    {
        $queryUpdateBanderasAccount = "UPDATE accounts_cstm SET 
            valid_cambio_razon_social_c = '0', 
            cambio_nombre_c = '0', 
            cambio_dirfiscal_c = '0', 
            json_audit_c = '', 
            user_id9_c = '{$id_usuario}', 
            fecha_aprueba_rechaza_c = '{$fecha}', 
            json_direccion_audit_c = '', 
            omitir_guardado_direcciones_c = '0', 
            accion_cambio_fiscal_c = 'Rechazó', 
            direccion_actualizada_api_c = '0' 
            WHERE id_c = '{$id_cuenta}'";
        
        $GLOBALS['log']->fatal("UPDATE BANDERAS DE CUENTA RECHAZADO: " . $queryUpdateBanderasAccount);
        $GLOBALS['db']->query($queryUpdateBanderasAccount);
    }

     /**
     * Actualiza los datos en el sistema externo via API
     */
    private function actualizarDatosEnSistemaExterno($beanCuenta, $datos_cambio)
    {
        global $sugar_config;
        
        try {
            $token = $sugar_config['tokenOnboarding'];
            $rfc = $beanCuenta->rfc_c;
            $url = $sugar_config['onboarding_url'] . 'update-client/' . $rfc . '/';
            
            // Preparar body según tipo de persona
            if ($beanCuenta->tipodepersona_c == 'Persona Moral') {
                $business_name = $beanCuenta->denominacion_c;
                $business_entity = $beanCuenta->regimen_capital_c;
                $body = json_encode([
                    "business_name" => $business_name,
                    "business_entity" => $business_entity
                ]);
            } else {
                $name = $beanCuenta->primernombre_c;
                $last_name = $beanCuenta->apellidopaterno_c;
                $mother_last_name = $beanCuenta->apellidomaterno_c;
                $body = json_encode([
                    "name" => $name,
                    "last_name" => $last_name,
                    "mother_last_name" => $mother_last_name
                ]);
            }
            
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Token ' . $token,
                    'Content-Type: application/json'
                ),
            ));
            
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            
            $GLOBALS['log']->fatal("Actualización sistema externo - HTTP Code: " . $httpCode);
            $GLOBALS['log']->fatal("Actualización sistema externo - Response: " . $response);
            
            if ($httpCode >= 200 && $httpCode < 300) {
                $GLOBALS['log']->fatal("Datos actualizados exitosamente en sistema externo para RFC: " . $rfc);
            } else {
                $GLOBALS['log']->fatal("Error al actualizar datos en sistema externo para RFC: " . $rfc . " - Código: " . $httpCode);
            }
            
        } catch (Exception $e) {
            $GLOBALS['log']->fatal("Excepción al actualizar sistema externo: " . $e->getMessage());
        }
    }

    /**
     * Obtiene el JSON de cambios pendientes desde la base de datos
     */
    private function obtenerJsonCambiosPendientes($id_cuenta, $rfc)
    {
        $query = "SELECT json_audit_c FROM accounts_cstm 
                 WHERE id_c = '{$id_cuenta}' AND rfc_c = '{$rfc}'
                 AND json_audit_c IS NOT NULL AND json_audit_c != ''";
        
        $GLOBALS['log']->fatal("Consulta obtenerJsonCambiosPendientes: " . $query);
        
        $result = $GLOBALS['db']->query($query);
        if ($result->num_rows > 0) {
            while ($row = $GLOBALS['db']->fetchByAssoc($result)) {
                return $row['json_audit_c'];
            }
        }
        
        return '';
    }

    public function aprobarCambiosRazonSocial($api, $args)
    {
        global $current_user;
        $response = array();
        $date = TimeDate::getInstance()->nowDb();
        $id_cuenta = $args['idCuenta'];
        $beanCuenta = BeanFactory::getBean('Accounts', $id_cuenta, array('disable_row_level_security' => true));

        if (!empty($args['cuenta'])) {

            if (!empty($beanCuenta)) {

                if ($args['cuenta']['tipo'] !== 'Persona Moral') {
                    //Se establecen valores para Primer Nombre, Paterno y Materno
                    $beanCuenta->primernombre_c = $args['cuenta']['primer_nombre_por_actualizar'];
                    $beanCuenta->apellidopaterno_c = $args['cuenta']['paterno_por_actualizar'];
                    $beanCuenta->apellidomaterno_c = $args['cuenta']['materno_por_actualizar'];
                } else { //Al ser Moral, se establecen nuevos valores en Razón Social y Nombre Comercial
                    $beanCuenta->razonsocial_c = $args['cuenta']['razon_social_por_actualizar'];
                    $beanCuenta->nombre_comercial_c = $args['cuenta']['razon_social_por_actualizar'];
                }

                //$beanCuenta->save();
                //Una vez aprobados los cambios para nombre, se establece el nuevo nombre para solicitudes relacionada
                $nombre_actual = $args['cuenta']['nombre_actual'];
                $nombre_actualizar = $args['cuenta']['nombre_por_actualizar'];
                $this->setNombreCuentaEnSolicitudes($beanCuenta, $nombre_actual, $nombre_actualizar);

                array_push($response, "Cuenta actualizada correctamente");
            }
        }


        // if( !empty($args['direccion']) ){
        //     $id_direccion = $args['direccion']['id_direccion'];
        //     if( $id_cuenta == "" ){
        //         $id_cuenta = $this->getIdCuenta($id_direccion);
        //     }

        //     //En caso de tener 5 caracteres en el string, quiere decir que es el CP y hay que obtener el id del Código Postal
        //     if( strlen($args['direccion']['cp_por_actualizar']) == 5 ){
        //         $id_codigo_postal = $this->getIdCodigoPostal( $args['direccion']['cp_por_actualizar'] );
        //     }else{
        //         $id_codigo_postal =  $args['direccion']['cp_por_actualizar'];
        //     }

        //     $beanDireccion = BeanFactory::getBean('dire_Direccion', $id_direccion , array('disable_row_level_security' => true));
        //     if( isset($args['direccion']['indicador']) && $args['direccion']['indicador'] !== "" ){
        //         $beanDireccion->indicador = $args['direccion']['indicador'];
        //     }
        //     $beanDireccion->dire_direccion_dire_codigopostaldire_codigopostal_ida = $id_codigo_postal;
        //     $beanDireccion->dire_direccion_dire_paisdire_pais_ida = $args['direccion']['pais_por_actualizar'];
        //     $beanDireccion->dire_direccion_dire_estadodire_estado_ida = $args['direccion']['estado_por_actualizar'];
        //     $beanDireccion->dire_direccion_dire_municipiodire_municipio_ida = $args['direccion']['municipio_por_actualizar'];
        //     $beanDireccion->dire_direccion_dire_ciudaddire_ciudad_ida = $args['direccion']['ciudad_por_actualizar'];
        //     $beanDireccion->dire_direccion_dire_coloniadire_colonia_ida = $args['direccion']['colonia_por_actualizar'];
        //     $beanDireccion->calle =$args['direccion']['calle_por_actualizar'];
        //     $beanDireccion->numext =$args['direccion']['numext_por_actualizar'];
        //     $beanDireccion->numint =$args['direccion']['numint_por_actualizar'];
        //     $direccion_completa = $args['direccion']['calle_por_actualizar'] . " " . $args['direccion']['numext_por_actualizar'] . " " . ($args['direccion']['numint_por_actualizar'] != "" ? "Int: " . $args['direccion']['numint_por_actualizar'] : "") . ", Colonia " . $beanDireccion->dire_direccion_dire_colonia_name . ", Municipio " . $beanDireccion->dire_direccion_dire_municipio_name;

        //     $beanDireccion->name = $direccion_completa;
        //     $beanDireccion->cambio_direccion_c = 0;
        //     $beanDireccion->json_audit_c = '';
        //     $beanDireccion->valid_cambio_razon_social_c = 0;

        //     $beanDireccion->save();

        //     array_push($response,"Direccion " .$beanDireccion->id. " actualizada correctamente");

        // }

        // if( !empty($args['direcciones']) && $beanCuenta->cambio_dirfiscal_c == 1 ){

        //     $direcciones = $args['direcciones']['json_dire_actualizar'];

        //     if( count($direcciones) > 0 ){
        //         for ($i=0; $i < count($direcciones); $i++) {
        //             $id_direccion = $direcciones[$i]['id'];

        //             if( $id_direccion != "" ){
        //                 $bean_direccion = BeanFactory::getBean('dire_Direccion', $id_direccion , array('disable_row_level_security' => true));

        //             }else{
        //                 $bean_direccion = BeanFactory::newBean('dire_Direccion');
        //                 $bean_direccion->accounts_dire_direccion_1accounts_ida = $id_cuenta;
        //             }

        //             $bean_direccion->indicador = $direcciones[$i]['indicador'];
        //             $tipo_string = "";
        //             if ( !empty($direcciones[$i]['tipodedireccion'] !== "") ) {
        //                 $tipo_string .= '^' . $direcciones[$i]['tipodedireccion'][0] . '^';
        //             }
        //             $bean_direccion->tipodedireccion = $tipo_string;

        //             $bean_direccion->dire_direccion_dire_codigopostaldire_codigopostal_ida = $direcciones[$i]['postal'];
        //             $bean_direccion->dire_direccion_dire_paisdire_pais_ida = $direcciones[$i]['pais'];
        //             $bean_direccion->dire_direccion_dire_estadodire_estado_ida = $direcciones[$i]['estado'];
        //             $bean_direccion->dire_direccion_dire_municipiodire_municipio_ida = $direcciones[$i]['municipio'];
        //             $bean_direccion->dire_direccion_dire_ciudaddire_ciudad_ida = $direcciones[$i]['ciudad'];
        //             $bean_direccion->dire_direccion_dire_coloniadire_colonia_ida = $direcciones[$i]['colonia'];
        //             $bean_direccion->calle = $direcciones[$i]['calle'];
        //             $bean_direccion->numext = $direcciones[$i]['numext'];
        //             $bean_direccion->numint = $direcciones[$i]['numint'];

        //             $bean_direccion->save();

        //             array_push($response,"Direccion " .$bean_direccion->id. " actualizada correctamente");
        //         }
        //     }

        // }

        if ($beanCuenta == "" || empty($beanCuenta)) {
            $beanCuenta = BeanFactory::getBean('Accounts', $id_cuenta, array('disable_row_level_security' => true));
        }

        $beanCuenta->valid_cambio_razon_social_c = 0;
        $beanCuenta->cambio_nombre_c = 0;
        $beanCuenta->cambio_dirfiscal_c = 0;
        $beanCuenta->json_audit_c = '';
        $beanCuenta->json_direccion_audit_c = '';
        $beanCuenta->omitir_guardado_direcciones_c = 0;
        $beanCuenta->direccion_actualizada_api_c = 0;

        //Establece valor sobre el campo del usuario que aprobo/rechazó el cambio
        $beanCuenta->user_id9_c = $current_user->id;
        $beanCuenta->usr_aprueba_rechaza_c = $current_user->full_name;
        $beanCuenta->fecha_aprueba_rechaza_c = $date;
        $beanCuenta->accion_cambio_fiscal_c = "Aprobó";

        $beanCuenta->save();

        return $response;
    }

    public function rechazarCambiosRazonSocial($api, $args)
    {
        global $current_user;
        global $app_list_strings;
        $response = array();
        $id_cuenta = "";
        $date = TimeDate::getInstance()->nowDb();

        if (!empty($args['cuenta'])) {
            $id_cuenta = $args['cuenta']['id_cuenta'];
            $razon_rechazo = $args['cuenta']['razon_rechazo'];

            $beanCuenta = BeanFactory::getBean('Accounts', $id_cuenta, array('disable_row_level_security' => true));
            $nombreCuenta = $beanCuenta->name;
            $idUsuarioLeasing = $beanCuenta->user_id_c;

            //Al ser rechazados los cambios, las banderas únicamente se actualizan desde bd para evitar pasar por todos los LH
            $this->reestableceBanderasCuenta($id_cuenta);
            $this->insertAuditAccion($id_cuenta);

            //Guardar razón rechazo
            $this->saveRazonRechazo($id_cuenta, $razon_rechazo);

            //Envía correo
            $bodyCorreo = $this->buildBodyCorreoRechazo($nombreCuenta, $razon_rechazo);
            $emailsDestinatarios = $this->getUsuariosDestinatariosRechazo($idUsuarioLeasing);

            $GLOBALS['log']->fatal(print_r($emailsDestinatarios, true));

            $this->sendEmailRechazo($nombreCuenta, $emailsDestinatarios, $bodyCorreo);

            array_push($response, "Cambios de Cuenta rechazados");
        }

        if (!empty($args['direccion'])) {
            $id_direccion = $args['direccion']['id_direccion'];
            if ($id_cuenta == "") {
                $id_cuenta = $this->getIdCuenta($id_direccion);
                //Resetea banderas de Cuentas
                $this->reestableceBanderasCuenta($id_cuenta);
            }

            $this->reestableceBanderasDireccion($id_direccion);

            array_push($response, "Cambios de Dirección rechazados");
        }

        if (!empty($args['direcciones'])) {
            $id_cuenta = $args['idCuenta'];

            $this->insertAuditAccion($id_cuenta);
            $this->insertAuditJSONDirecciones($id_cuenta);
            $this->reestableceBanderasCuenta($id_cuenta);

            array_push($response, "Cambios de Direcciones rechazados");
        }

        return $response;
    }

    public function getIdCodigoPostal($cp)
    {

        $queryCP = "SELECT id FROM dire_codigopostal WHERE name = '{$cp}'";

        $resultCP = $GLOBALS['db']->query($queryCP);
        $id_cp = "";

        if ($resultCP->num_rows > 0) {
            while ($row = $GLOBALS['db']->fetchByAssoc($resultCP)) {
                $id_cp = $row['id'];
            }
        }

        return $id_cp;
    }

    public function getIdCuenta($id_direccion)
    {
        $id_cuenta = "";
        //Si no se tiene id de cuenta, se obtiene el id de la cuenta relacionada a la dirección
        $queryGetCuenta = "SELECT accounts_dire_direccion_1accounts_ida FROM accounts_dire_direccion_1_c WHERE accounts_dire_direccion_1dire_direccion_idb='{$id_direccion}'";
        $resultCuenta = $GLOBALS['db']->query($queryGetCuenta);
        if ($resultCuenta->num_rows > 0) {
            while ($row = $GLOBALS['db']->fetchByAssoc($resultCuenta)) {
                $id_cuenta = $row['accounts_dire_direccion_1accounts_ida'];
            }
        }

        return $id_cuenta;
    }

    public function reestableceBanderasCuenta($id_cuenta)
    {
        global $current_user;
        $date = TimeDate::getInstance()->nowDb();

        $queryUpdateBanderasAccount = "UPDATE accounts_cstm SET valid_cambio_razon_social_c = '0', cambio_nombre_c = '0', cambio_dirfiscal_c = '0', json_audit_c = '', user_id9_c = '{$current_user->id}', fecha_aprueba_rechaza_c ='{$date}', json_direccion_audit_c = '', omitir_guardado_direcciones_c = '0', accion_cambio_fiscal_c = 'Rechazó', direccion_actualizada_api_c = '0' WHERE id_c = '{$id_cuenta}'";
        $GLOBALS['log']->fatal("UPDATE BANDERAS DE CUENTA");
        $GLOBALS['log']->fatal($queryUpdateBanderasAccount);

        $GLOBALS['db']->query($queryUpdateBanderasAccount);
    }

    public function insertAuditAccion($id_cuenta, $accion)
    {

        global $current_user;
        $id_user = $current_user->id;
        $parent_id = $id_cuenta;
        $id_audit = create_guid();
        $date = TimeDate::getInstance()->nowDb();

        $insertQueryAudit = "INSERT INTO `accounts_audit` (`id`,`parent_id`,`date_created`,`created_by`,`field_name`,`data_type`,`before_value_string`,`after_value_string`,`before_value_text`,`after_value_text`,`event_id`,`date_updated`) VALUES ('{$id_audit}','{$parent_id}','{$date}','{$id_user}','accion_cambio_fiscal_c','varchar','','{$accion}',NULL,NULL,'',NULL)";

        $GLOBALS['db']->query($insertQueryAudit);
    }

    public function saveRazonRechazo($id_cuenta, $razon_rechazo)
    {
        $beanResumen = BeanFactory::getBean('tct02_Resumen', $id_cuenta, array('disable_row_level_security' => true));

        $beanResumen->razon_rechazo_regimen_c = $razon_rechazo;

        $beanResumen->save();
    }

    public function buildBodyCorreoRechazo($nombreCuenta, $razonRechazo)
    {

        $mailHTML = '<p align="justify"><font face="verdana" color="#635f5f">
            Se rechaza la actualización de la razón social de <b>' . $nombreCuenta . '</b>.<br>
            <br>Descripción de rechazo: ' . $razonRechazo . '<br>
            <br>Atentamente Unifin</font></p>
            <br><br><img border="0" id="bannerUnifin" src="https://www.unifin.com.mx/ri/front/img/logo.png">
            <br><span style="font-size:8.5pt;color:#757b80">____________________________________________</span>
            <p class="MsoNormal" style="text-align: justify;">
              <span style="font-size: 7.5pt; font-family: \'Arial\',sans-serif; color: #212121;">
                Este correo electrónico y sus anexos pueden contener información CONFIDENCIAL para uso exclusivo de su destinatario. Si ha recibido este correo por error, por favor, notifíquelo al remitente y bórrelo de su sistema.
                Las opiniones expresadas en este correo son las de su autor y no son necesariamente compartidas o apoyadas por UNIFIN, quien no asume aquí obligaciones ni se responsabiliza del contenido de este correo, a menos que dicha información sea confirmada por escrito por un representante legal autorizado.
                No se garantiza que la transmisión de este correo sea segura o libre de errores, podría haber sido viciada, perdida, destruida, haber llegado tarde, de forma incompleta o contener VIRUS.
                Asimismo, los datos personales, que en su caso UNIFIN pudiera recibir a través de este medio, mantendrán la seguridad y privacidad en los términos de la Ley Federal de Protección de Datos Personales; para más información consulte nuestro <a href="https://www.unifin.com.mx/aviso-de-privacidad" target="_blank">Aviso de Privacidad</a>  publicado en <a href="http://www.unifin.com.mx/" target="_blank">www.unifin.com.mx</a>
              </span>
            </p>';

        return $mailHTML;
    }

    public function getUsuariosDestinatariosRechazo($idUsuarioLeasing)
    {
        global $app_list_strings;
        $emailsList = array();

        if (!empty($idUsuarioLeasing)) {

            $beanUserLeasing = BeanFactory::getBean('Users', $idUsuarioLeasing, array('disable_row_level_security' => true));
            $estado = $beanUserLeasing->status;
            $es_grupo = $beanUserLeasing->is_group;
            $emailLeasing = "";

            if ($es_grupo) {
                $listRechazoLeasing = $app_list_strings['robina_rechazo_leasing_list'];
                $emailEncargadoLeasing = "";
                for ($i = 0; $i < count($listRechazoLeasing); $i++) {
                    $emailEncargadoLeasing = $listRechazoLeasing[$i];
                }

                if ($emailEncargadoLeasing !== "") {
                    array_push($emailsList, $emailEncargadoLeasing);
                }
            } else {

                $notificaJefe = ($estado == 'Inactive') ? true : false;

                if ($notificaJefe) {
                    $idJefe = $beanUserLeasing->reports_to_id;
                    $beanJefe = BeanFactory::getBean('Users', $idJefe, array('disable_row_level_security' => true));
                    $emailLeasing = $beanJefe->email1;
                } else {

                    $emailLeasing = $beanUserLeasing->email1;
                }

                if ($emailLeasing !== "") {
                    array_push($emailsList, $emailLeasing);
                }
            }
        } else {
            //Si la cuenta no tiene usuario leasing, se notifica a Juan Carlos Vera
            $listRechazoLeasing = $app_list_strings['robina_rechazo_leasing_list'];
            $emailEncargadoLeasing = "";
            for ($i = 0; $i < count($listRechazoLeasing); $i++) {
                $emailEncargadoLeasing = $listRechazoLeasing[$i];
            }

            if ($emailEncargadoLeasing !== "") {
                array_push($emailsList, $emailEncargadoLeasing);
            }
        }

        $listEmailsEncargados = $app_list_strings['robina_rechazo_list'];

        for ($i = 0; $i < count($listEmailsEncargados); $i++) {
            array_push($emailsList, $listEmailsEncargados[$i]);
        }

        return $emailsList;
    }

    public function sendEmailRechazo($nombreCuenta, $emailsList, $bodyCorreo)
    {
        try {
            global $app_list_strings;
            $mailer = MailerFactory::getSystemDefaultMailer();
            $mailTransmissionProtocol = $mailer->getMailTransmissionProtocol();
            $mailer->setSubject('Se rechaza la actualización de la razón Social de ' . $nombreCuenta);
            $body = trim($bodyCorreo);
            $mailer->setHtmlBody($body);
            $mailer->clearRecipients();
            //for ($i = 0; $i < count($emailsList); $i++) {
            //    $GLOBALS['log']->fatal("AGREGANDO CORREOS DESTINATARIOS: " . $emailsList[$i]);
            //    $mailer->addRecipientsTo(new EmailIdentity($emailsList[$i], $emailsList[$i]));
            //}
            foreach ($emailsList as $key1 => $email1) {
                $GLOBALS['log']->fatal("AGREGANDO CORREO DESTINATARIOS: " . $email1);
                $mailer->addRecipientsTo(new EmailIdentity($email1));
            }
            $result = $mailer->send();
        } catch (Exception $e) {
            $GLOBALS['log']->fatal("Exception: No se ha podido enviar correo al email ");
            $GLOBALS['log']->fatal(print_r($e, true));
        }
    }

    public function insertAuditJSONDirecciones($id_cuenta)
    {

        global $current_user;
        $id_user = $current_user->id;
        $parent_id = $id_cuenta;
        $id_audit = create_guid();
        $date = TimeDate::getInstance()->nowDb();

        $beanCuenta = BeanFactory::getBean('Accounts', $id_cuenta, array('disable_row_level_security' => true));

        $json_direcciones = $beanCuenta->json_direccion_audit_c;

        $insertQueryAudit = "INSERT INTO `accounts_audit` (`id`,`parent_id`,`date_created`,`created_by`,`field_name`,`data_type`,`before_value_string`,`after_value_string`,`before_value_text`,`after_value_text`,`event_id`,`date_updated`) VALUES ('{$id_audit}','{$parent_id}','{$date}','{$id_user}','json_direccion_audit_c','text',NULL,NULL,'{$json_direcciones}','','',NULL)";

        $GLOBALS['db']->query($insertQueryAudit);
    }

    public function reestableceBanderasDireccion($id_direccion)
    {

        $queryResetDireccion = "UPDATE dire_direccion_cstm SET json_audit_c = '', cambio_direccion_c = '0', valid_cambio_razon_social_c = '0' WHERE id_c = '{$id_direccion}'";
        $GLOBALS['log']->fatal("UPDATE BANDERAS DE DIRECCION");
        $GLOBALS['log']->fatal($queryResetDireccion);
        $GLOBALS['db']->query($queryResetDireccion);
    }

    public function setNombreCuentaEnSolicitudes($beanCuenta, $nombre_actual, $nombre_actualizar)
    {

        if ($beanCuenta->load_relationship('opportunities')) {

            $relatedOpps = $beanCuenta->opportunities->getBeans();

            if (!empty($relatedOpps)) {
                foreach ($relatedOpps as $opp) {
                    /*
                    tct_etapa_ddw_c - R (RECHAZADO)

                    estatus_c - N (AUTORIZADA)
                            - R (RECHAZADA CREDITO)
                            - CM (RECHAZADA COMITE)
                            - K (CANCELADA)
                    */
                    if ($opp->tct_etapa_ddw_c != 'R' && $opp->estatus_c != 'N' && $opp->estatus_c != 'R' && $opp->estatus_c != 'CM' && $opp->estatus_c != 'K') {
                        //Obtiene el nombre de la solicitud para reemplazar el nuevo nombre de la cuenta
                        $nombre_opp = $opp->name;

                        if (strpos($nombre_opp, $nombre_actual) !== false) {
                            $nuevoNombreOpp = str_replace($nombre_actual, $nombre_actualizar, $nombre_opp);
                            $opp->name = $nuevoNombreOpp;

                            $opp->save();
                        }
                    }
                }
            }
        }
    }
}
