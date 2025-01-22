<?php
/**
 * Created by PhpStorm.
 * User: salvadorlopez
 * Date: 08/04/24
 */
if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class AltaAlianzaPO extends SugarApi
{
    /**
     * Registro de todas las rutas para consumir los servicios del API
     *
     */
    public function registerApiRest()
    {
        return array(
            //GET
            'retrieve' => array(
                //request type
                'reqType' => 'POST',
                //'noLoginRequired' => true,
                //endpoint path
                'path' => array('AltaAlianzaPO'),
                //endpoint variables
                'pathVars' => array('method'),
                //method to call
                'method' => 'altaPOAlianza',
                //short help string to be displayed in the help documentation
                'shortHelp' => 'Genera el alta de un PO para que entre proceso de asignación',
                //long help to be displayed in the help documentation
                'longHelp' => '',
            ),


        );

    }   

    public function altaPOAlianza($api, $args){
        
        $respopnse = array();
        $arr_required = array('nombre_c', 'apellido_paterno_c', 'apellido_materno_c', 'phone_mobile', 'email1', 'zona_geografica_c', 'asesor_alianza_c');

        $missing_fields = array_diff($arr_required, array_keys($args));

        if( count($missing_fields) > 0 ){

            $response = array(
                "status" => 422, //Falta algún campo
                "message" => "Falta agregar los siguientes campos requeridos",
                "detail" => array_values( $missing_fields)
            );

        }else{

            $arr_required_set = array();
            foreach ($arr_required as $key) {
                $valor = $args["{$key}"];
                $GLOBALS['log']->fatal("EL VALOR: ".$valor);
                $GLOBALS['log']->fatal($key);
                if( trim($valor) == "" ){
                    array_push($arr_required_set, $key);
                }
            }

            if( count($arr_required_set) > 0 ){
                $response = array(
                    "status" => 422, //Falta algún campo
                    "message" => "Falta agregar los siguientes campos requeridos",
                    "detail" => array_values($arr_required_set)
                );

            }else{
                //Valida existencia de po con email
                $idProspect = $this->validateExistsPO($args['email1']);

                if (!empty($idProspect)) {

                    //Regresa el registro encontrado y se marca el count match

                    $beanProspect = BeanFactory::retrieveBean('Prospects', $idProspect, array('disable_row_level_security' => true));
                    $beanProspect->count_match_c = ($beanProspect->count_match_c == "") ? 0 : $beanProspect->count_match_c;
                    $suma = $beanProspect->count_match_c + 1;
                    $beanProspect->count_match_c = $suma;
                    $beanProspect->save();

                    //Se aplica directo el UPDATE ya que existe un lh que valida existencia de email y no permite el guardado
                    //$marcaCountSQL = "UPDATE prospects_cstm SET count_match_c = '{$suma}' WHERE (`id_c` = '{$idProspect}')";

                    //$GLOBALS['db']->query($marcaCountSQL);


                    $response = array(
                        "status" => 200, //Falta algún campo
                        "message" => "El registro con el email " . $args['email1'] . " ya existe",
                        "detail" => $idProspect
                    );
                } else {

                    //Crea registro de PO
                    $beanProspect = BeanFactory::newBean("Prospects");
                    //Iterar $args obtenidos y setea bean
                    foreach ($args as $clave => $valor) {
                        if (!empty($valor) && $clave != 'id' && $clave != 'deleted') {
                            $beanProspect->$clave = $valor;
                        }
                    }
                    
                    if($beanProspect->detalle_origen_c=="12"||$beanProspect->detalle_origen_c=="13"){
                        $beanProspect->origen_c = '12';
                    }
                    
                    //Guarda bean y devuelve estructura
                    $beanProspect->save();

                    $response = array(
                        "status" => 200, //Falta algún campo
                        "message" => "El registro se ha creado correctamente",
                        "detail" => $beanProspect->id
                    );
                }
            }

        }

        return $response;
       
    }

    public function validateExistsPO($email){
        //Consulta existencia de PO con mismo Email
        global $db;
        $idProspect = '';
        $query = "select p.id, e.email_address
          from prospects p
          inner join prospects_cstm pc on pc.id_c=p.id
          inner join email_addr_bean_rel eb on eb.bean_id=p.id and eb.deleted=0
          inner join email_addresses e on e.id = eb.email_address_id and e.deleted=0
          where 
          p.deleted=0 and pc.excluye_campana_c=0 -- Exluye campana sirve para descartar registros cargados por importación masiva para campañas
          and e.email_address='{$email}'
          order by p.date_entered asc
          limit 1;";

        $GLOBALS['log']->fatal($query);
        $queryResult = $db->query($query);
        while ($row = $db->fetchByAssoc($queryResult)) {
            $idProspect = $row['id'];
        }

        $GLOBALS['log']->fatal("EL ID PROSPECT ENCONTRADO");
        $GLOBALS['log']->fatal($idProspect);
        return $idProspect;
    }
}