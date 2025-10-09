<?php
array_push($job_strings, 'job_crea_direccion_buro_credito');

function job_crea_direccion_buro_credito()
{
    global $db, $app_list_strings;
    
    $GLOBALS['log']->fatal("*** INICIANDO JOB: CrearDireccionBuroJob - Buscando cuentas con bandera activa ***");
    
    try {
        // Consultar cuentas que tienen la bandera activa
        $query = "SELECT 
            ac.id_c as account_id,
            ac.crear_direccion_buro_c,
            a.date_entered,
            TIMESTAMPDIFF(MINUTE, a.date_entered, NOW()) as minutos_desde_creacion
        FROM tct02_resumen a
        INNER JOIN tct02_resumen_cstm ac ON ac.id_c = a.id
        WHERE ac.crear_direccion_buro_c = 1 
        AND a.deleted = 0
        AND TIMESTAMPDIFF(MINUTE, a.date_entered, NOW()) >= 5
        ORDER BY a.date_entered ASC"; 
        
        $GLOBALS['log']->fatal("consulta");
        $GLOBALS['log']->fatal($query);

        $result = $db->query($query);
        $cuentasProcesadas = 0;
        $cuentasConError = 0;
        $cuentasEncontradas = 0;
        $tieneburo = false;
        
        while ($row = $db->fetchByAssoc($result)) {
            $cuentasEncontradas++;
            $accountId = $row['account_id'];
            $GLOBALS['log']->fatal("Procesando cuenta con bandera Buró: " . $accountId);

            try {
                $account = BeanFactory::getBean('Accounts', $accountId);
                
                if (empty($account->id)) {
                    $cuentasConError++;
                    $GLOBALS['log']->fatal("Error: No se pudo cargar la cuenta: " . $accountId);
                    continue;
                }
                
                $GLOBALS['log']->fatal("Cuenta encontrada: " . $accountId);
                $cuentasProcesadas++;
                
                // Verificar si ya tiene dirección Buró
                $direccion_buro = false;
                $direccion_buro_id = null;
                
                if ($account->load_relationship('accounts_dire_direccion_1')) {
                    $relatedDirecciones = $account->accounts_dire_direccion_1->getBeans();
                    
                    if (!empty($relatedDirecciones)) {
                        foreach ($relatedDirecciones as $direccion) {
                            if ($direccion->indicador == '64' && !$direccion->inactivo) {
                                $GLOBALS['log']->fatal("La dirección Buró de Crédito encontrada es: " . $direccion->id);
                                $direccion_buro = true;
                                $direccion_buro_id = $direccion->id;
                                break;
                            }
                        }
                    }
                }

                if ($direccion_buro) {
                    $GLOBALS['log']->fatal("La cuenta {$accountId} ya tiene dirección Buró ({$direccion_buro_id}), desactivando bandera");
                    
                    // Desactivar bandera de creación
                    $query = "UPDATE tct02_resumen_cstm SET crear_direccion_buro_c = 0 WHERE id_c = '{$accountId}'";
                    $resultUpdate = $db->query($query);
                    $GLOBALS['log']->fatal("Bandera crear_direccion_buro_c desactivada para cuenta: '{$accountId}'");
                    
                } else {
                    $GLOBALS['log']->fatal("La cuenta {$accountId} NO tiene dirección Buró, procediendo a crear una");
                    
                    // Buscar dirección fiscal activa
                    $indicador_direcciones_fiscales = array(2,3,6,7,10,11,14,15,18,19,22,23,26,27,30,31,34,35,38,39,42,43,46,47,50,51,54,55,58,59,62,63);
                    $auxDireccion = null;
                    
                    if ($account->load_relationship('accounts_dire_direccion_1')) {
                        $relatedDirecciones = $account->accounts_dire_direccion_1->getBeans();
                        
                        if (!empty($relatedDirecciones)) {
                            foreach ($relatedDirecciones as $direccion) {
                                if (in_array($direccion->indicador, $indicador_direcciones_fiscales) && !$direccion->inactivo) {
                                    $auxDireccion = $direccion;
                                    $GLOBALS['log']->fatal("Dirección fiscal encontrada: " . $direccion->id);
                                    break;
                                }
                            }
                        }
                    }

                    if (empty($auxDireccion)) {
                        $GLOBALS['log']->fatal("No se encontró dirección fiscal para cuenta: " . $accountId);
                        $cuentasConError++;
                    } else {
                        // Crear dirección Buró como copia de la fiscal
                        $direccionBuro = BeanFactory::newBean('dire_Direccion');
                        $direccionBuro->id = create_guid();
                        $direccionBuro->new_with_id = true;
                        
                        // Copiar datos de la dirección fiscal
                        $direccionBuro->name = $auxDireccion->name . " (Buró de Crédito)";
                        $direccionBuro->tipodedireccion = $auxDireccion->tipodedireccion;
                        $direccionBuro->calle = $auxDireccion->calle;
                        $direccionBuro->numext = $auxDireccion->numext;
                        $direccionBuro->numint = $auxDireccion->numint;
                        $direccionBuro->principal = false;
                        $direccionBuro->inactivo = false;
                        $direccionBuro->indicador = '64';
                        $direccionBuro->description = $auxDireccion->description;
                        
                        // Crear nueva relacion para buro
                        $direccionBuro->dir_sepomex_dire_direcciondir_sepomex_ida = $auxDireccion->dir_sepomex_dire_direcciondir_sepomex_ida;

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