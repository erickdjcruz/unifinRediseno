<?php

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
require_once("custom/Levementum/UnifinAPI.php");

class DepuracionRegistros extends SugarApi
{
    public function registerApiRest()
    {
        return array(
            'GETActivoAPI' => array(
                'reqType' => 'POST',
                'path' => array('deleteRecord'),
                'pathVars' => array(''),
                'method' => 'depuraRegistro',
                'shortHelp' => 'Se establece como deleted el registro pasado como parámetro y sus respectivas relaciones',
            ),
        );
    }

    public function depuraRegistro($api, $args)
    {
        try
        {
            global $app_list_strings, $current_user;

            $response = array();

            $module = $args['module'];
            $id = $args['id'];
            $platform = $_SESSION['platform'];

            if( isset($app_list_strings['delete_modules_relation_list'][$module] ) ){

                $recordExists = $this->checkRecordExists($module,$id);

                if( $recordExists ){
                    //Se elimina registro principal
                    $this->deleteMainRecord($module,$id);
                    
                    $array_relations = json_decode($app_list_strings['delete_modules_relation_list'][$module]);

                    //$GLOBALS['log']->fatal(print_r($array_relations,true));
                    //Se eliminan relaciones del registro
                    $stringForDescription = "Tablas relacionadas eliminadas: ";
                    for ($i=0; $i < count($array_relations); $i++) { 
                        $item = (object) $array_relations[$i];

                        $tableName = $item->table_name;
                        $columnName = $item->column_name;

                        $stringForDescription .= $tableName .",";
                        
                        $stringQueryRelation = $this->buildQueryUpdate($id,$tableName,$columnName);
                        $GLOBALS['log']->fatal($stringQueryRelation);
                        $this->executeQuery($stringQueryRelation);

                    }

                    //Se inserta en la tabla de auditoría
                    $this->insertAuditTableDelete($platform,$module,$id, rtrim($stringForDescription, ','),$current_user->id);

                    $response['code'] = "200";
                    $response['status'] = "OK";
                    $response['detail'] = "El registro de ".$module. " con el id ".$id." se ha eliminado correctamente";

                }else{

                    $response['code'] = "404";
                    $response['status'] = "not_found";
                    $response['detail'] = "El registro de ".$module. " con el id ".$id." no existe";

                }
                
            }else{

                $response['code'] = "404";
                $response['status'] = "error";
                $response['detail'] = "El módulo ingresado no existe en la lista";

            }

            return $response;

        }catch (Exception $e){
            $response['status'] = "error";
            $response['detail'] = "Ha ocurrido un error";
            return $response;
            return null;
        }

    }

    public function checkRecordExists( $module, $id ){
        $beanModule = BeanFactory::retrieveBean($module, $id, array('disable_row_level_security' => true));
        $GLOBALS['log']->fatal(( is_null($beanModule) ) ? "ESTÁ VACÍO" : "SI EXISTE");
        return ( is_null($beanModule) ) ? false : true;
    }

    public function deleteMainRecord( $module, $id ){
        $module = strtolower($module);
        $GLOBALS['log']->fatal("Eliminando id: ".$id." del módulo: ".$module);
        if($module == "accounts"){ //Caso especial para accounts, se elimina registro de resumen
            $stringQueryResumen = "UPDATE tct02_resumen SET deleted = '1' WHERE id = '{$id}'";
            $GLOBALS['log']->fatal($stringQueryResumen);
            $this->executeQuery($stringQueryResumen);    
        }

        $stringQuery = "UPDATE {$module} SET deleted = '1' WHERE id = '{$id}'";
        $GLOBALS['log']->fatal($stringQuery);
        $this->executeQuery($stringQuery);
    }

    public function buildQueryUpdate( $idRecord,$tableName, $columnName ){
        return "UPDATE {$tableName} SET deleted = '1' WHERE {$columnName} = '{$idRecord}'";
    }

    public function executeQuery( $stringQuery ){

        $GLOBALS['db']->query($stringQuery);
    }

    /**
     * En el campo description se guardan todas las tablas relacionadas al módulo que se establecieron como deleted
     */
    public function insertAuditTableDelete( $platform,$module, $idRecord, $description, $userId ){
        
        $date= TimeDate::getInstance()->nowDb();
        $id_audit=create_guid();

        $stringInsert = "INSERT INTO unifin_delete_audit
        (id,
        platform,
        bean_module,
        bean_id,
        description,
        date_entered,
        date_modified,
        created_by,
        modified_user_id)
        VALUES
        (
        '{$id_audit}',
        '{$platform}',
        '{$module}',
        '{$idRecord}',
        '{$description}',
        '{$date}',
        '{$date}',
        '{$userId}',
        '{$userId}'
        )";

        //Generar insert
        $GLOBALS['log']->fatal("Insertando en la tabla de auditoria");
        $GLOBALS['log']->fatal($stringInsert);

        $GLOBALS['db']->query($stringInsert);

    }
    

}

