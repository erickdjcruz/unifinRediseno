<?php
class Prospects_AsignacionPO
{
    public function set_assigned($bean = null, $event = null, $args = null){
        global $db, $app_list_strings;
        //Sólo aplica en creación
        if (!$args['isUpdate'] && $_SESSION['platform'] != 'base') {

          //Entra validación para nueva asignación de alianza
          $GLOBALS['log']->fatal("ENTRA ASIGNACIÓN DESDE API");

          if( !empty($bean->zona_geografica_c) ){
            $valor_zona_geografica = $app_list_strings['mapeo_dire_estado_zona_geografica_list'][$bean->zona_geografica_c];

            $GLOBALS['log']->fatal("ZONA GEOGRAFICA ENCONTRADA: ". $valor_zona_geografica);

            if( !empty( $valor_zona_geografica ) ){
              $bean->zona_geografica_c = $valor_zona_geografica;
            }
          }

          //Valida existencia de relación entre estado (zona_geografica) y municipio (municipio_po_c)
          //Se aplica validación para evitar obtener municipio_po_c NULL y traiga resultados de la bd equivocados
          $municipio = ( empty($bean->municipio_po_c) ) ? "" : $bean->municipio_po_c;
          $queryZonaGeograficaMunicipio = "SELECT * FROM unifin_asignacion_po where zona_geografica='{$bean->zona_geografica_c}' AND municipio='{$municipio}'";

          $GLOBALS['log']->fatal("QUERY PARA OBTENER ASIGNADO: ".$queryZonaGeograficaMunicipio);

          $resultZonaGeograficaMunicipio = $db->query($queryZonaGeograficaMunicipio);

          if( $resultZonaGeograficaMunicipio->num_rows > 0 ){
            $id_asignado = "";
            while ($row = $db->fetchByAssoc($resultZonaGeograficaMunicipio)) {

              $id_asignado = $row['asignado_id'];
              $GLOBALS['log']->fatal("ID ENCONTRADO PARA ASIGNACIÓN: " . $id_asignado);
            }

            $bean->assigned_user_id = $id_asignado;
            //Alianzas
            // $bean->origen_c = '12';
            $bean->compania_po_c = '1'; //Compañia: Unifin


          }else{
            $GLOBALS['log']->fatal("ENTRA ASIGNACIÓN EXISTENTE ");

            //Valida asignación para PO creados fuera de CRM
            $asignado_id = '';
            
            //Recupera usuario por núm empleaso
            if(!empty($bean->numero_empleado_c)){
              $query = "select id_c from users_cstm
                where no_empleado_c = '{$bean->numero_empleado_c}' limit 1;";
              $resultado = $db->query($query);
              while ($row = $db->fetchByAssoc($resultado)) {
                  $asignado_id = $row['id_c'];
              }              
            }
            
            //Recupera usuario por carrousel
            if(!empty($bean->zona_geografica_c) && empty($asignado_id)){
              $equipos = '';
              $query = "select equipos,asignado_id from unifin_asignacion_po 
                where zona_geografica = '{$bean->zona_geografica_c}' and municipio IS NULL limit 1;";
              $resultado = $db->query($query);
              while ($row = $db->fetchByAssoc($resultado)) {
                  $equipos = $row['equipos'];
              }
              if(!empty($equipos)){
                $equipos = str_replace("^","'",$equipos);
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
                  $usuarios[]=$rowC['id'];
                  $indexA = $rowC['asignado_id'];
                }
                if ($countRows>0) {
                  if($indexA<=$countRows){
                    $asignado_id = $usuarios[$indexA-1];
                  }else{
                    $asignado_id = $usuarios[0];
                  }
                  
                  $nextIndex = ($indexA+1 > $countRows) ? 1 : $indexA+1;
                  
                }
                
                //Actualiza indice
                $query = "update unifin_asignacion_po a
                  set a.asignado_id = '{$nextIndex}'
                  where a.zona_geografica='{$bean->zona_geografica_c}' and a.municipio IS NULL;";
                $resultado = $db->query($query);
              }   
            }
            
            //Establece asignado
            if(!empty($asignado_id)){
              $bean->assigned_user_id = $asignado_id;
            }
          }   
        }
        //SE APLICA ACTUALIZACIÓN
        if ($args['isUpdate'] && $_SESSION['platform'] != 'base') {       
          //VALIDA SOLO SI HA CAMBIADO DE VALOR LA ZONA GEOGRAFICA
          if(!empty($bean->zona_geografica_c) && $bean->fetched_row['zona_geografica_c'] != $bean->zona_geografica_c){

            $valor_zona_geografica = $app_list_strings['mapeo_dire_estado_zona_geografica_list'][$bean->zona_geografica_c];
            $GLOBALS['log']->fatal("ACTUAIZACION DE ZONA GEOGRAFICA ENCONTRADA: ". $valor_zona_geografica);

            if(!empty($valor_zona_geografica)){
              $bean->zona_geografica_c = $valor_zona_geografica;
            }
          }        
        }
        
        //Valida Bloqueo de Origen
        if( ($bean->origen_c!='' && $bean->fetched_row['origen_c'] != $bean->origen_c) || ($bean->detalle_origen_c!='' && $bean->fetched_row['detalle_origen_c'] != $bean->detalle_origen_c )){
            $bean->origen_bloqueado_c = true;
        }
    }
}
