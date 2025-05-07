<?php
/**
 * @author: CVV
 * @date: 25/07/2016
 * @comments: Rest API to display states list
 */

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
class asignacionPO extends SugarApi
{
    public function registerApiRest()
    {
        return array(
            'asignacionPO_GET' => array(
                'reqType' => 'GET',
                'path' => array('getAsignacionPO'),
                'pathVars' => array(''),
                'method' => 'getAsignacionPOMethod',
                'shortHelp' => 'Obtiene la lista de asignación de PO unifin_asignacion_po',
            ),
            'asignacionPO_POST' => array(
                'reqType' => 'POST',
                'path' => array('upAsignacionPO'),
                'pathVars' => array(''),
                'method' => 'upAsignacionPOMethod',
                'shortHelp' => 'Obtiene la lista de asignación de PO unifin_asignacion_po',
            ),
            'getAsignacionSetAsignado' => array(
                'reqType' => 'GET',
                'path' => array('getAsignacionPoUsers'),
                'pathVars' => array(''),
                'method' => 'getRecordsAsignacionPOusers',
                'shortHelp' => 'Obtiene la lista de asignación de PO unifin_asignacion_po que no tenga equipos',
            ),
            'updateAsignados' => array(
                'reqType' => 'POST',
                'path' => array('updateAsignadosPO'),
                'pathVars' => array(''),
                'method' => 'updateAsignadoId',
                'shortHelp' => 'Actualiza asignado id en tabla de unifin_asignacion_po',
            ),
        );
    }

    public function getAsignacionPOMethod($api, $args)
    {
        global $db, $current_user;
        try
        {
            $query = "select a.*, concat(u.first_name, ' ', u.last_name)uName
              from unifin_asignacion_po a
              inner join users u on u.id=a.modified_by
              WHERE municipio IS NULL or municipio = '';";
            $resultado = $db->query($query);
            $restultado_list = [];

            while ($row = $db->fetchByAssoc($resultado)) {
                $restultado_list[] = $row;
            }

            return $restultado_list;

        }catch (Exception $e){
            error_log(__FILE__." - ".__CLASS__."->".__FUNCTION__." <".$current_user->user_name."> : Error: ".$e->getMessage());
            $GLOBALS['log']->fatal(__FILE__." - ".__CLASS__."->".__FUNCTION__." <".$current_user->user_name."> : Error ".$e->getMessage());
        }

    }
    
    public function upAsignacionPOMethod($api, $args)
    {
        global $db, $current_user;
        $zona_geografica = isset($args['zona_geografica']) ? $args['zona_geografica'] : '';
        $equipos = isset($args['equipos']) ? $args['equipos'] : '';
        $modified_by = $current_user->id;
        if($zona_geografica && $equipos){
          try{
              $query = "update unifin_asignacion_po a
                set a.equipos = '{$equipos}',
                modified_by = '{$modified_by}',
                date_modified = now()
                where a.zona_geografica='{$zona_geografica}';";
              $resultado = $db->query($query);
              
              return '200';

          }catch (Exception $e){
              error_log(__FILE__." - ".__CLASS__."->".__FUNCTION__." <".$current_user->user_name."> : Error: ".$e->getMessage());
              $GLOBALS['log']->fatal(__FILE__." - ".__CLASS__."->".__FUNCTION__." <".$current_user->user_name."> : Error ".$e->getMessage());
              return '500';
          }
        }else{
          return '400';
        }

    }

    public function getRecordsAsignacionPOusers($api, $args){

        global $db, $current_user;

        $zonaGeografica = $args['idZonaGeografica'];
        $id_estado = '';

        // Mapeo con switch para asignar id_estado según zonaGeografica
        switch ($zonaGeografica) {
            case '6':  $id_estado = '09'; break;
            case '7':  $id_estado = '15'; break;
            case '8':  $id_estado = '30'; break;
            case '9':  $id_estado = '31'; break;
            case '10': $id_estado = '14'; break;
            case '11': $id_estado = '22'; break;
            case '12': $id_estado = '23'; break;
            case '13': $id_estado = '07'; break;
            case '14': $id_estado = '13'; break;
            case '15': $id_estado = '28'; break;
            case '16': $id_estado = '21'; break;
            case '17': $id_estado = '24'; break;
            case '18': $id_estado = '17'; break;
            case '19': $id_estado = '19'; break;
            case '20': $id_estado = '11'; break;
            case '21': $id_estado = '04'; break;
            case '22': $id_estado = '08'; break;
            case '23': $id_estado = '06'; break;
            case '24': $id_estado = '01'; break;
            case '25': $id_estado = '29'; break;
            case '26': $id_estado = '16'; break;
            case '27': $id_estado = '12'; break;
            case '28': $id_estado = '05'; break;
            case '29': $id_estado = '27'; break;
            case '30': $id_estado = '25'; break;
            case '31': $id_estado = '10'; break;
            case '32': $id_estado = '20'; break;
            case '33': $id_estado = '03'; break;
            case '35': $id_estado = '02'; break;
            case '36': $id_estado = '18'; break;
            case '37': $id_estado = '32'; break;
            case '38': $id_estado = '26'; break;
            default: $id_estado = ''; break;
        }

        try
        {
            $query = "SELECT DISTINCT (nMunicipio), id , zona_geografica, municipio, equipos, uName,asignado_id, date_modified from (
                SELECT distinct a.*, sepomex.municipio nMunicipio ,sepomex.id_estado , sepomex.estado nEstado ,
                concat(u.first_name, ' ', u.last_name)uName 
                FROM unifin_asignacion_po a
                INNER JOIN users u on u.id=a.modified_by
                INNER JOIN dir_sepomex sepomex on a.municipio = sepomex.id_municipio
                WHERE (zona_geografica != '' AND zona_geografica IS NOT NULL) AND
                (sepomex.municipio != '' AND sepomex.municipio IS NOT NULL) AND (equipos IS NULL OR equipos = '' )
                AND zona_geografica = '".$zonaGeografica."' and id_estado = '".$id_estado."'
                ORDER BY nMunicipio ASC
            ) as tzona;";
            $GLOBALS['log']->fatal("QUERYS:".$query);
            $resultado = $db->query($query);
            $restultado_list = [];

            while ($row = $db->fetchByAssoc($resultado)) {
                $restultado_list[] = $row;
            }

            return $restultado_list;

        }catch (Exception $e){
            error_log(__FILE__." - ".__CLASS__."->".__FUNCTION__." <".$current_user->user_name."> : Error: ".$e->getMessage());
            $GLOBALS['log']->fatal(__FILE__." - ".__CLASS__."->".__FUNCTION__." <".$current_user->user_name."> : Error ".$e->getMessage());
        }
    }

    public function updateAsignadoId($api, $args){

        $arrNewAsignados = $args['newAsignados'];

        $GLOBALS['log']->fatal("QUERYS ACTUALIZACIÓN");
        try{
            global $current_user;
            for( $i=0; $i < count($arrNewAsignados); $i++ ){

                $idRegistro = $arrNewAsignados[$i]['idRegistro'];
                $idAsignado = $arrNewAsignados[$i]['asignado'];
                $currentDate = TimeDate::getInstance()->nowDb();

                $queryUpdate = "UPDATE unifin_asignacion_po SET asignado_id = '{$idAsignado}', date_modified = '{$currentDate}', modified_by = '{$current_user->id}'  WHERE id = '{$idRegistro}';";
                $GLOBALS['log']->fatal($queryUpdate);
                
                $GLOBALS['db']->query($queryUpdate);
            }

            return array(
                "status"=> "éxito",
                "msj"=> "Los registros se han actualizado correctamente"
            );

        }catch(Exception $ex) {
            $GLOBALS['log']->fatal("Error al aplicar actualización de esignado " . $ex);
            
            return array(
                "status"=> "error",
                "msj"=> "Ocurrió un error al actualizar asignado: ".$ex,
            );
        }

    }
}
