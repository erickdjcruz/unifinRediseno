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

        try
        {
            $query = "SELECT a.*, m.name nMunicipio , concat(u.first_name, ' ', u.last_name)uName FROM unifin_asignacion_po a
            INNER JOIN users u on u.id=a.modified_by
            INNER join dire_municipio m on a.municipio = m.id 
            WHERE (zona_geografica != '' AND zona_geografica IS NOT NULL) AND
            (municipio != '' AND municipio IS NOT NULL) AND (equipos IS NULL OR equipos = '' )
            AND zona_geografica = ".$zonaGeografica."
            ORDER BY nMunicipio ASC";
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
