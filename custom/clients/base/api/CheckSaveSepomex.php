<?php

use Sugarcrm\Sugarcrm\Util\Uuid;

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class CheckSaveSepomex extends SugarApi
{
    public function registerApiRest()
    {
        return array(
            'checkSaveRecordSepomex' => array(
                'reqType' => 'POST',
                'path' => array('CheckSaveSepomex'),
                'pathVars' => array('endpoint',),
                'method' => 'checkAndSaveSepomex',
                'shortHelp' => 'Método para guardar nuevo registro en tabla de Sepomex solo en caso de que los datos ingresados no existan en dicha tabla',
            ),
            'saveRecordSepomex' => array(
                'reqType' => 'POST',
                'path' => array('saveRecordSepomex'),
                'pathVars' => array('endpoint',),
                'method' => 'saveRecord',
                'shortHelp' => 'Método para guardar nuevo registro en tabla de Sepomex',
            ),
        );
    }
    
    public function checkAndSaveSepomex($api, $args)
    {
        global $current_user;
        $response=array();
        $cp=$args['cp'];
        $pais=$args['pais'];
        $labelPais=$args['labelPais'];
        $estado=$args['estado'];
        $labelEstado=$args['labelEstado'];
        $ciudad=$args['ciudad'];
        $labelCiudad=$args['labelCiudad'];
        $municipio=$args['municipio'];
        $labelMunicipio=$args['labelMunicipio'];
        //$colonia=$args['colonia'];
        $labelColonia=$args['labelColonia'];

        $selectSepomex="SELECT id FROM dir_sepomex WHERE codigo_postal='{$cp}' AND id_pais='{$pais}' AND id_estado='{$estado}' AND id_ciudad='{$ciudad}' AND id_municipio='{$municipio}' and colonia='{$labelColonia}'";

        $result=$GLOBALS['db']->query($selectSepomex);

        $GLOBALS['log']->fatal("Num rows: ".$result->num_rows);
        if($result->num_rows>0){
            $response['result']="error";
            $response['msg']="Este código postal ya está registrado con la colonia indicada";
        }else{
            //Inserta registro
            $new_id_sep=Uuid::uuid1();
            $id_user=$current_user->id;
            $current_date=TimeDate::getInstance()->nowDb();
            $name=$labelPais ." ".$cp." ".$labelEstado." ".$labelColonia;//labelPais CP Estado Colonia
            $qinsertRecordSepomex="INSERT INTO `dir_sepomex` (`id`, `name`, `date_entered`, `date_modified`, `modified_user_id`, `created_by`, `deleted`, `pais`, `id_pais`, `codigo_postal`, `estado`, `id_estado`, `ciudad`, `id_ciudad`, `municipio`, `id_municipio`, `colonia`) VALUES ('{$new_id_sep}', '{$name}', '{$current_date}', '{$current_date}', '{$id_user}', '{$id_user}', '0', '{$labelPais}', '{$pais}', '{$cp}', '{$labelEstado}', '{$estado}','{$labelCiudad}', '{$ciudad}', '{$labelMunicipio}', '{$municipio}', '{$labelColonia}');";
            //$GLOBALS['log']->fatal("INSERTANDO SEPOMEX");
            //$GLOBALS['log']->fatal($qinsertRecordSepomex);

            $GLOBALS['db']->query($qinsertRecordSepomex);

            $response["result"]="OK";
            $response["msg"]="La Colonia se ha insertado correctamente";

        }

        return $response;
    }

    public function saveRecord( $api, $args ){

        global $current_user;
        $response=array();
        $cp=$args['cp'];
        $pais=$args['pais'];
        $labelPais=$args['labelPais'];
        $estado=$args['estado'];
        $labelEstado=$args['labelEstado'];
        //$ciudad=$args['ciudad'];
        //Se genera un nuevo id en caso de que la ciudad se tome como nueva
        $ciudad= ( strlen($args['ciudad'] == 2) ) ? $args['ciudad']: Uuid::uuid1();
        $labelCiudad=$args['labelCiudad'];
        //$municipio=$args['municipio'];
        //Se genera un nuevo id en caso de que el municipio se tome como nuevo
        $municipio= ( strlen($args['municipio'] == 3) ) ? $args['municipio']: Uuid::uuid1();
        $labelMunicipio=$args['labelMunicipio'];
        //$colonia=$args['colonia'];
        $labelColonia=$args['labelColonia'];
        $cleanedLabelColonia = strtolower(str_replace([' ', '.', ',', '(', ')'], '', $labelColonia));

        $selectSepomex="SELECT id FROM dir_sepomex WHERE codigo_postal='{$cp}' AND id_pais='{$pais}' AND id_estado='{$estado}' AND id_ciudad='{$ciudad}' AND id_municipio='{$municipio}' and LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(colonia, ' ', ''), '.', ''), ',', ''), '(', ''), ')', ''))='{$cleanedLabelColonia}'";

        $GLOBALS['log']->fatal($selectSepomex);
        $result=$GLOBALS['db']->query($selectSepomex);

        $GLOBALS['log']->fatal("Num rows: ".$result->num_rows);
        if($result->num_rows>0){
            $response['result']="error";
            $response['msg']="Este código postal ya existe con la información proporcionada";
        }else{
            //Inserta registro
            $new_id_sep=Uuid::uuid1();
            $id_user=$current_user->id;
            $current_date=TimeDate::getInstance()->nowDb();
            $id_colonia = Uuid::uuid1();
            $name=$labelPais ." ".$cp." ".$labelEstado." ".$labelColonia;//labelPais CP Estado Colonia
            $qinsertRecordSepomex="INSERT INTO `dir_sepomex` (`id`, `name`, `date_entered`, `date_modified`, `modified_user_id`, `created_by`, `deleted`, `pais`, `id_pais`, `codigo_postal`, `estado`, `id_estado`, `ciudad`, `id_ciudad`, `municipio`, `id_municipio`, `colonia`, `id_colonia`) VALUES ('{$new_id_sep}', '{$name}', '{$current_date}', '{$current_date}', '{$id_user}', '{$id_user}', '0', '{$labelPais}', '{$pais}', '{$cp}', '{$labelEstado}', '{$estado}','{$labelCiudad}', '{$ciudad}', '{$labelMunicipio}', '{$municipio}', '{$labelColonia}', '{$id_colonia}');";
            //$GLOBALS['log']->fatal("INSERTANDO SEPOMEX");
            //$GLOBALS['log']->fatal($qinsertRecordSepomex);

            $GLOBALS['db']->query($qinsertRecordSepomex);

            $response["result"]="OK";
            $response["msg"]="El registro se ha insertado correctamente";

        }

        return $response;


    }
}