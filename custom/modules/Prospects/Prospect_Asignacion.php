<?php
class Prospects_AsignacionPO
{
  public function set_assigned($bean = null, $event = null, $args = null)
  {
    global $db, $app_list_strings;
    //Sólo aplica en creación
    if (!$args['isUpdate'] && $_SESSION['platform'] != 'base') {

      //Entra validación para nueva asignación de alianza
      $GLOBALS['log']->fatal("ENTRA ASIGNACIÓN DESDE API");

      if (!empty($bean->zona_geografica_c)) {
        $val_original = $bean->zona_geografica_c;
        $valor_zona_geografica = $app_list_strings['mapeo_dire_estado_zona_geografica_list'][$bean->zona_geografica_c];

        $GLOBALS['log']->fatal("ZONA GEOGRAFICA ENCONTRADA: " . $valor_zona_geografica);

        if (!empty($valor_zona_geografica)) {
          $bean->zona_geografica_c = $valor_zona_geografica;
        }
      }

      //Valida existencia de relación entre estado (zona_geografica) y municipio (municipio_po_c)
      //Se aplica validación para evitar obtener municipio_po_c NULL y traiga resultados de la bd equivocados
      $municipio = (empty($bean->municipio_po_c)) ? "" : $bean->municipio_po_c;
      $queryZonaGeograficaMunicipio = "SELECT * FROM unifin_asignacion_po where zona_geografica='{$bean->zona_geografica_c}' AND municipio='{$municipio}'";

      $GLOBALS['log']->fatal("QUERY PARA OBTENER ASIGNADO: " . $queryZonaGeograficaMunicipio);

      $resultZonaGeograficaMunicipio = $db->query($queryZonaGeograficaMunicipio);
      $id_asignado = "";
      if ($resultZonaGeograficaMunicipio->num_rows > 0) {
        
        while ($row = $db->fetchByAssoc($resultZonaGeograficaMunicipio)) {

          $id_asignado = $row['asignado_id'];
          $GLOBALS['log']->fatal("ID ENCONTRADO PARA ASIGNACIÓN: " . $id_asignado);
        }

        $bean->assigned_user_id = $id_asignado;
        //Alianzas
        // $bean->origen_c = '12';
        $bean->compania_po_c = '1'; //Compañia: Unifin

      } else {

        $id_asignado_q='';

        $queryValMunicipio = "SELECT * FROM dir_sepomex where id_municipio='{$municipio}'";
        $resultsepomun = $db->query($queryValMunicipio);
        if ($resultsepomun->num_rows > 0) {
          while ($row1 = $db->fetchByAssoc($resultsepomun)) {
            $lbl_municipio = $row1['municipio'];
            $id_estado = $row1['id_estado'];
            $GLOBALS['log']->fatal("ID ENCONTRADO PARA ASIGNACIÓN: " . $id_asignado);
          }
          
          $queryMunicipio = "SELECT * FROM unifin_asignacion_po where zona_geografica='{$valor_zona_geografica}' and municipio = ( 
            SELECT DISTINCT(id_municipio) FROM dir_sepomex sepo 
            WHERE id_estado = '{$id_estado}' 
              AND UPPER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE('{$lbl_municipio}', 'Á','A'), 'É','E'), 'Í','I'), 'Ó','O'), 'Ú','U')) = 
                UPPER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(municipio, 'Á','A'), 'É','E'), 'Í','I'), 'Ó','O'), 'Ú','U'))
              AND LENGTH(id_municipio) < 5
            GROUP BY id_municipio, municipio, id_estado , estado
          );";

          $GLOBALS['log']->fatal("QUERY NUEVA ZONA:". $queryMunicipio);
          $resultNpo = $db->query($queryMunicipio);
          if ($resultNpo->num_rows > 0) {
            while ($row2 = $db->fetchByAssoc($resultNpo)) {
              $municipio_q = $row2['municipio'];
              $id_asignado_q = $row2['asignado_id'];
              $id_asignado = $id_asignado_q;
              $insert_asignacion_po = "INSERT INTO `unifin_asignacion_po` (`id`,`zona_geografica`,`municipio`,`equipos`,`date_modified`,`modified_by`,`asignado_id`) VALUES 
              (UUID(),'{$valor_zona_geografica}','{$municipio}','',NOW(),'300c54cc-4fcd-11e8-8bcb-00155d967407','{$id_asignado_q}');";
              $GLOBALS['log']->fatal("insert asigna po:". $insert_asignacion_po);
              $GLOBALS['db']->query($insert_asignacion_po);

              $bean->assigned_user_id = $id_asignado_q;
              //Alianzas
              // $bean->origen_c = '12';
              $bean->compania_po_c = '1'; //Compañia: Unifin
            }
          }        
        }

        if($id_asignado_q == ''){
          $GLOBALS['log']->fatal("ENTRA ASIGNACIÓN EXISTENTE - CARROUSEL");
          
          //Valida asignación para PO creados fuera de CRM
          //Recupera usuario por núm empleaso
          if (!empty($bean->numero_empleado_c)) {
            $query = "select id_c from users_cstm
                  where no_empleado_c = '{$bean->numero_empleado_c}' limit 1;";
            $resultado = $db->query($query);
            while ($row = $db->fetchByAssoc($resultado)) {
              $id_asignado = $row['id_c'];
            }
          }

          //Recupera usuario por carrousel
          if (!empty($bean->zona_geografica_c) && empty($id_asignado)) {
            $equipos = '';
            $query = "select equipos,asignado_id from unifin_asignacion_po 
                  where zona_geografica = '{$bean->zona_geografica_c}' and municipio IS NULL limit 1;";
            $resultado = $db->query($query);
            while ($row = $db->fetchByAssoc($resultado)) {
              $equipos = $row['equipos'];
            }
            if (!empty($equipos)) {
              $equipos = str_replace("^", "'", $equipos);
              $query = "select u.id, u.last_name, u.status, uc.equipo_c, b.fecha_reporte, bc.vacaciones_c, a.zona_geografica, a.asignado_id
                    from users u
                    inner join users_cstm uc on uc.id_c=u.id
                    left join uni_brujula b on b.assigned_user_id = u.id  and b.fecha_reporte = curdate()
                    left join uni_brujula_cstm bc on bc.id_c = b.id
                    left join unifin_asignacion_po a on a.zona_geografica = '{$bean->zona_geografica_c}'
                    where uc.equipo_c in ({$equipos})
                    and a.municipio IS NULL
                    and u.status='Active'
                    and u.deleted=0
                    and u.is_group=0
                    and (bc.vacaciones_c = 0 or bc.vacaciones_c is null)
                    and a.zona_geografica is not null
                    and uc.posicion_operativa_c like '%^3^%'
                    order by u.last_name asc;";
              $resultadoC = $db->query($query);
              $countRows = 0;
              $indexA = 0;
              $nextIndex = 1;
              $usuarios = [];
              while ($rowC = $db->fetchByAssoc($resultadoC)) {
                $countRows++;
                $usuarios[] = $rowC['id'];
                $indexA = $rowC['asignado_id'];
              }
              if ($countRows > 0) {
                if ($indexA <= $countRows) {
                  $id_asignado = $usuarios[$indexA - 1];
                } else {
                  $id_asignado = $usuarios[0];
                }

                $nextIndex = ($indexA + 1 > $countRows) ? 1 : $indexA + 1;
              }

              //Actualiza indice
              $query = "update unifin_asignacion_po a
                    set a.asignado_id = '{$nextIndex}'
                    where a.zona_geografica='{$bean->zona_geografica_c}' and a.municipio IS NULL;";
              $resultado = $db->query($query);
            }
          }
        }
      }

      $GLOBALS['log']->fatal("id_asignado:".$id_asignado);
      //Establece asignado
      if (!empty($id_asignado)) {
        //Valida si esta de vacaciones
        $queryholiday = "SELECT holiday_date FROM holidays WHERE person_id = '{$id_asignado}' and holiday_date = curdate() and deleted = 0;";
        $GLOBALS['log']->fatal("QUERY HOLIDAYS:". $queryholiday);
        $resultadoH = $db->query($queryholiday);
        if ($resultadoH->num_rows > 0) {
          $row1 = $db->fetchByAssoc($resultadoH);
          //if (!empty($row1)) $id_asignado = $app_list_strings['lider_generation_center_list']['Ricardo Gerardo'];
           // Obtener el primer valor de la lista lider_generation_center_list
          $lideres_gc = $app_list_strings['lider_generation_center_list'];
          $primer_id_asignado = reset($lideres_gc);
          $id_asignado = $primer_id_asignado;
          $GLOBALS['log']->fatal("id_asignado - lider_generation_center_list:".$id_asignado);
          $bean->assigned_user_id = $id_asignado;

          require_once("custom/clients/base/api/SendEmailPO.php");
          $apiSendEmailPO = new SendEmailPO();
          $body = array(
            'id_po' => $bean->id,
            'id_lider_gc' => $id_asignado
          );
          //ENVIA CORREO DE NOTIFICACION AL LIDER DE GENERATION CENTER
          $response = $apiSendEmailPO->notificaReasignacionLiderGenerationCenterPO(null, $body);
        }
      }
    }
    //SE APLICA ACTUALIZACIÓN
    if ($args['isUpdate'] && $_SESSION['platform'] != 'base') {
      //VALIDA SOLO SI HA CAMBIADO DE VALOR LA ZONA GEOGRAFICA
      if (!empty($bean->zona_geografica_c) && $bean->fetched_row['zona_geografica_c'] != $bean->zona_geografica_c) {

        $valor_zona_geografica = $app_list_strings['mapeo_dire_estado_zona_geografica_list'][$bean->zona_geografica_c];
        $GLOBALS['log']->fatal("ACTUAIZACION DE ZONA GEOGRAFICA ENCONTRADA: " . $valor_zona_geografica);

        if (!empty($valor_zona_geografica)) {
          $bean->zona_geografica_c = $valor_zona_geografica;
        }
      }
    }

    //Valida Bloqueo de Origen
    if (($bean->origen_c != '' && $bean->fetched_row['origen_c'] != $bean->origen_c) || ($bean->detalle_origen_c != '' && $bean->fetched_row['detalle_origen_c'] != $bean->detalle_origen_c)) {
      $bean->origen_bloqueado_c = true;
    }
  }
}
