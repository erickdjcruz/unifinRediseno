<?php
error_reporting(E_ALL);

array_push($job_strings, 'job_crea_direccion_buro_credito');

function job_crea_direccion_buro_credito()
{
    global $db, $app_list_strings;
    
    $GLOBALS['log']->fatal("*** INICIANDO JOB: CrearDireccionBuroJob - Buscando cuentas con bandera activa ***");
    
    try {
        // Consultar cuentas que tienen la bandera activa
        $GLOBALS['log']->fatal("*** INICIANDO JOB: CrearDireccionBuroJob - Buscando cuentas con bandera activa ***");
        $query = "SELECT a.id from accounts a, accounts_cstm b, accounts_dire_direccion_1_c c, dire_direccion d, dire_direccion_cstm e, tct02_resumen_cstm f
        where a.id = b.id_c and a.id = c.accounts_dire_direccion_1accounts_ida and d.id = e.id_c and a.id = f.id_c and a.deleted = 0 and 
        d.id = c.accounts_dire_direccion_1dire_direccion_idb and c.deleted = 0 and d.deleted = 0 and b.tipo_registro_cuenta_c IN (2,3,4) and 
        d.inactivo = 0 and d.indicador &2 = 2 and f.seguimiento_bc_c = 0 and a.date_entered >= '2024-10-01'"; 
                
        //$GLOBALS['log']->fatal("consulta");
        $GLOBALS['log']->fatal($query);

        $result = $db->query($query);
        $cuentasProcesadas = 0;
        $cuentasConError = 0;
        $cuentasEncontradas = 0;
        $cuentasSinLocalidad = 0;
        $GLOBALS['log']->fatal("Registros encontrados en consulta: " . $result->num_rows);
        
        while ($row = $db->fetchByAssoc($result)) {
            $cuentasEncontradas++;
            $accountId = $row['account_id'];
            //$GLOBALS['log']->fatal("Procesando cuenta con bandera Buró: " . $accountId);

            try {
                $account = BeanFactory::getBean('Accounts', $accountId);
                
                if (empty($account->id)) {
                    $cuentasConError++;
                    $GLOBALS['log']->fatal("Error: No se pudo cargar la cuenta: " . $accountId);
                    continue;
                }
                
                //$GLOBALS['log']->fatal("Cuenta encontrada: " . $accountId);
                $cuentasProcesadas++;
                
                // Verificar si ya tiene dirección Buró
                $direccion_buro = false;
                $direccion_buro_id = null;
                
                if ($account->load_relationship('accounts_dire_direccion_1')) {
                    $relatedDirecciones = $account->accounts_dire_direccion_1->getBeans();
                    
                    if (!empty($relatedDirecciones)) {
                        foreach ($relatedDirecciones as $direccion) {
                            if ($direccion->indicador == '64' && !$direccion->inactivo) {
                                //$GLOBALS['log']->fatal("La dirección Buró de Crédito encontrada es: " . $direccion->id);
                                $direccion_buro = true;
                                $direccion_buro_id = $direccion->id;
                                break;
                            }
                        }
                    }
                }

                if ($direccion_buro) {
                    //$GLOBALS['log']->fatal("La cuenta {$accountId} ya tiene dirección Buró ({$direccion_buro_id}), desactivando bandera");
                    
                    // Desactivar bandera de creación
                    $query = "UPDATE tct02_resumen_cstm SET crear_direccion_buro_c = 0 WHERE id_c = '{$accountId}'";
                    $resultUpdate = $db->query($query);
                    //$GLOBALS['log']->fatal("Bandera crear_direccion_buro_c desactivada para cuenta: '{$accountId}'");
                    
                } else {
                    //$GLOBALS['log']->fatal("La cuenta {$accountId} NO tiene dirección Buró, procediendo a crear una");
                    
                    // Buscar dirección fiscal activa
                    $indicador_direcciones_fiscales = array(2,3,6,7,10,11,14,15,18,19,22,23,26,27,30,31,34,35,38,39,42,43,46,47,50,51,54,55,58,59,62,63);
                    $auxDireccion = null;
                    
                    if ($account->load_relationship('accounts_dire_direccion_1')) {
                        $relatedDirecciones = $account->accounts_dire_direccion_1->getBeans();
                        
                        if (!empty($relatedDirecciones) && $relatedDirecciones != null) {
                            foreach ($relatedDirecciones as $direccion) {
                                if (in_array($direccion->indicador, $indicador_direcciones_fiscales) && !$direccion->inactivo) {
                                    $auxDireccion = $direccion;
                                    //$GLOBALS['log']->fatal("Dirección fiscal encontrada: " . $direccion->id);
                                    break;
                                }
                            }
                        }
                    }

                    $sincolonia = [" ","", ".", "-", "_", "Sin Colonia", "SIN COLONIA","OTRA NO ESPECIFICADA EN EL CATALOGO"];
                    $sinciudad = [" ","", ".", "-", "_" , "Sin Ciudad", "SIN CIUDAD","OTRA NO ESPECIFICADA EN EL CATALOGO"];
                       
                   if (empty($auxDireccion)) {
                    $cuentasConError++;
                    }elseif (in_array($auxDireccion->colonia_c, $sincolonia) && ($auxDireccion->localidad_c != '' && $auxDireccion->localidad_c != null ) ) {
                        $cuentasSinLocalidad++;
                    }else {
                        $direccion_completa = '';
                        $desc_aux = '';
                        // Crear dirección Buró como copia de la fiscal
                        $direccionBuro = BeanFactory::newBean('dire_Direccion');
                        $direccionBuro->id = create_guid();
                        $direccionBuro->new_with_id = true;
                        
                        // Copiar datos de la dirección fiscal                       
                        $direccionBuro->tipodedireccion = $auxDireccion->tipodedireccion;
                        $direccionBuro->calle = $auxDireccion->calle;
                        $direccionBuro->numext = $auxDireccion->numext;
                        $direccionBuro->numint = $auxDireccion->numint;
                        $direccionBuro->principal = false;
                        $direccionBuro->inactivo = false;
                        $direccionBuro->indicador = '64';                        
                        /*******************************************************/
                        $localidad = $auxDireccion->localidad_c;
                        /*****************************************/
                        // Related Sepomex
                        $new_sepomex = [];
                        $nuevaDireccion = false;
                        $idSepomex = $auxDireccion->dir_sepomex_dire_direcciondir_sepomex_ida;
                        
                        $sqlQuery = "SELECT id, codigo_postal, colonia, municipio, estado, ciudad, id_pais, id_estado, id_ciudad, id_municipio, id_colonia 
                        FROM dir_sepomex WHERE id = '{$idSepomex}';";
                        $resultSepomex = $db->query($sqlQuery);
                        
                        $idPais = $idEstado = $idCiudad = $idMunicipio = $idColonia = "";
 
                        //$GLOBALS['log']->fatal("localidad envio: " . $localidad); 
                        $colonia = $ciudad = "";
                        $auxRow = null;

                        while($row = $db->fetchByAssoc($resultSepomex)) {
                            //$GLOBALS['log']->fatal("Datos Sepomex: " );
                            //$GLOBALS['log']->fatal( print_r($row,true) );
                            $colonia = $row['colonia'];
                            $ciudad = $row['ciudad'];  
                            if(in_array($row['ciudad'], $sinciudad) || in_array($row['colonia'], $sincolonia)) {
                                $GLOBALS['log']->fatal("SIN CIUDAD Y/O SIN COLONIA DETECTADO"); 
                                if(in_array($row['colonia'], $sincolonia)) { 
                                    $colonia = $localidad;
                                    $GLOBALS['log']->fatal("SIN COLONIA, usando localidad: " . $localidad);  
                                }
                                if(in_array($row['ciudad'], $sinciudad)) { 
                                    $ciudad = $row['municipio'];
                                    $GLOBALS['log']->fatal("SIN CIUDAD, usando municipio: " . $row['municipio']);  
                                }
                                $nuevaDireccion = true;
                            }
                            $auxRow = $row;               
                        }
                        $args_dir_sepomex = array();

                        if($nuevaDireccion)
                        {
                            $new_sepomex['module'] = 'DireccionesQR';
                            $new_sepomex['cp'] = $auxRow['codigo_postal'];
                            $new_sepomex['indice'] = 0;
                            $new_sepomex['colonia_rfc'] = $colonia;
                            $new_sepomex['ciudad_rfc'] = $auxRow['municipio'];
                            $new_sepomex['entidad_rfc'] = $auxRow['estado'];
                            $new_sepomex['ciudad_csf'] = $ciudad;

                            array_push($args_dir_sepomex, $new_sepomex);
                            //$GLOBALS['log']->fatal("args_dir_sepomex: " . print_r($args_dir_sepomex, true));

                            //DireccionesQR/' + CP + '/0/' + Colonia + '/' + Municipio + '/' + Estado + '/' + Ciudad;
                            $apigetSepomex = new getDireccionCPQR();
                            $response = $apigetSepomex->getAddressByCPQR(null, $new_sepomex);
                            $idSepomex = $response['idCP'];
                            $auxRow = $response;
                            
                            $GLOBALS['log']->fatal("Respuesta API: " . print_r($auxRow, true));
                            
                            if (isset($auxRow['paises']['idpais'])) {
                                $idPais = $auxRow['paises']['idpais'];
                                $idEstado = $auxRow['estados']['idEstado'];
                                $idCiudad = $auxRow['ciudades']['idCiudad'];
                                $idMunicipio = $auxRow['municipios']['idMunicipio'];
                                $idColonia = $auxRow['colonias']['idColonia'];
                            }
                        }                        

                        if($nuevaDireccion){
                            $direccion_completa = $auxDireccion->calle . " " . $auxDireccion->numext . " " . ($auxDireccion->numint != "" ? "Int: " . $auxDireccion->numint : "") . ", Colonia " . $auxRow['colonias']['nameColonia'] . ", Municipio " . $auxRow['municipios']['nameMunicipio'] ;
                            $direccion_completa = $auxDireccion->calle . " " . $auxDireccion->numext . " " . 
                                ($auxDireccion->numint != "" ? "Int: " . $auxDireccion->numint : "") . 
                                ", Colonia " . $auxRow['colonias']['nameColonia'] . 
                                ", Municipio " . $auxRow['municipios']['nameMunicipio'];                            
                            $desc_aux = "{$idPais}|{$idEstado}|{$idCiudad}|{$idMunicipio}|{$idColonia}";
                        }else{
                            $direccion_completa = $auxDireccion->name;
                            $desc_aux = $auxDireccion->description;
                        }                        
                        /**********************************************/
                        $direccionBuro->name = $direccion_completa;
                        $direccionBuro->description = $desc_aux;
                        /******************************************************/
                        // Crear nueva relacion para buro
                        $direccionBuro->dir_sepomex_dire_direcciondir_sepomex_ida = $idSepomex;

                        // Establecer relación con la cuenta
                        $direccionBuro->accounts_dire_direccion_1accounts_ida = $accountId;
                        
                        // Copiar datos del equipo y usuario asignado
                        $direccionBuro->team_id = $account->team_id;
                        $direccionBuro->team_set_id = $account->team_set_id;
                        $direccionBuro->assigned_user_id = $account->assigned_user_id;
                        
                        // Guardar dirección Buró
                        $direccionBuro->save();
                        
                        // Actualizar bandera seguimiento_bc_c
                        $activaFlagBC = "UPDATE tct02_resumen_cstm SET seguimiento_bc_c = 1 WHERE id_c = '{$accountId}'";
                        $resultBC = $db->query($activaFlagBC);
                        $GLOBALS['log']->fatal("Bandera seguimiento_bc_c actualizada a 1 para la cuenta: '{$accountId}'");
                        
                        // Desactivar bandera de creación
                        $query = "UPDATE tct02_resumen_cstm SET crear_direccion_buro_c = 0 WHERE id_c = '{$accountId}'";
                        $result = $db->query($query);
                        $GLOBALS['log']->fatal("Bandera crear_direccion_buro_c desactivada para cuenta: '{$accountId}'");
                        $GLOBALS['log']->fatal("Dirección Buró creada exitosamente para cuenta: {$accountId}");
                    }                        
                }

            } catch (Exception $e) {
                $cuentasConError++;
                $GLOBALS['log']->fatal("Error procesando cuenta {$accountId}: " . $e->getMessage());
            }
        }
        
        $mensaje = "Job completado. Cuentas encontradas: {$cuentasEncontradas}, Procesadas: {$cuentasProcesadas}, Errores: {$cuentasConError}";
        $GLOBALS['log']->fatal($mensaje);
        
        return true;
        
    } catch (Exception $e) {
        $GLOBALS['log']->fatal("Error en Job CrearDireccionBuro: " . $e->getMessage());
        return false;
    }
}
