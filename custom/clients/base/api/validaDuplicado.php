<?php
/**
 * Created by
 * User: tactos
 * Date: 28/06/21
 * Time: 12:19 PM
 */

if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class validaDuplicado extends SugarApi
{
    public function registerApiRest()
    {
        return array(
            'POST_validaPLD' => array(
                'reqType' => 'POST',
                'noLoginRequired' => false,
                'path' => array('validaDuplicado'),
                'pathVars' => array(''),
                'method' => 'validaRegistroDuplicado',
                'shortHelp' => 'Valida registro con similitud (duplicidad) en Leads y Cuentas',
            ),
        );
    }

    public function validaRegistroDuplicado($api, $args)
    {
        try {
            //Recupera argumentos de petición: input
            $nombre = isset($args['nombre']) ? $args['nombre'] : '';
            //Estructura resultado: output
            $respuesta = [];
            $items = [];

            //Valida existencia de nombre
            if(!empty($nombre)){
                //Procesa validación
                $items = $this->consultaRegistros($args);
                $respuesta['code'] = '200';
                $respuesta['registros'] = $items;
            }else{
                //Agrega error
                $respuesta['code'] = '400';
                $respuesta['error_message'] = 'Debe especificar el nombre del registro';
            }
        }catch(Exception $e){
            //Agrega error
            $respuesta['code'] = '500';
            $respuesta['error_message'] = $e->getMessage();
        }

        //Regresa resultado de validación
        return $respuesta;
    }


    public function consultaRegistros($args)
    {
        //Recupera argumentos de petición: input
        global $db;
        $nombre = isset($args['nombre']) ? $args['nombre'] : '';
        $correo = isset($args['correo']) ? $args['correo'] : '';
        $telefonos = isset($args['telefonos']) ? implode("','",$args['telefonos']) : '';
        $totalTelefonos = isset($args['telefonos']) ? count($args['telefonos']) : 0;
        $rfc = isset($args['rfc']) ? $args['rfc'] : '';
        $curp = isset($args['curp']) ? $args['curp'] : '';
        $consultas = [];
        //Estructura resultado: output
        $items = [];
        $item = [];

        //Consumir servicio de cleanName, declarado en custom api
        require_once("custom/clients/base/api/cleanName.php");
        $apiCleanName= new cleanName();
        $body=array('name'=>$nombre);
        $response=$apiCleanName->getCleanName(null,$body);
        if ($response['status']=='200') {
            $nombre = $response['cleanName'];
        }
        //Prepara consulta: Lead
        //Nivel 0 - Nombre (limpio con algoritmo: clean_name) + email + algún teléfono + RFC.
        if (!empty($nombre) && !empty($correo) && $totalTelefonos>0 && !empty($rfc)) {
            $consultas[] = "select '0' as nivel, 'Lead' as modulo, 'Leads' as moduloLink, lc.clean_name_c nombre, l.id as id, lc.rfc_c as rfc,
            'Nivel de match encontrado a través de la combinación del nombre, email, algún teléfono y RFC' as descripcion
                        from leads l
                        	inner join leads_cstm lc on lc.id_c=l.id
                        	left join email_addr_bean_rel er on er.bean_id = l.id and er.deleted=0
                        	left join email_addresses e on e.id=er.email_address_id and e.deleted =0
                        where
                        	lc.clean_name_c='".$nombre."'
                          and e.email_address='".$correo."'
                          and( l.phone_home in ('".$telefonos."') or l.phone_mobile in ('".$telefonos."') or l.phone_work in ('".$telefonos."')  or l.phone_other in ('".$telefonos."') )
                          and lc.rfc_c = '".$rfc."'
                        ";
            $consultas[] ="select '0' as nivel, 'Cuenta' as modulo, 'Accounts' as moduloLink, a.clean_name nombre, a.id as id, ac.rfc_c as rfc,
            'Nivel de match encontrado a través de la combinación del nombre, email, algún teléfono y RFC' as descripcion
                        from accounts a
                        	inner join accounts_cstm ac on ac.id_c=a.id
                        	left join email_addr_bean_rel er on er.bean_id = a.id and er.deleted=0
                        	left join email_addresses e on e.id=er.email_address_id and e.deleted =0
                          left join accounts_tel_telefonos_1_c at on at.accounts_tel_telefonos_1accounts_ida = a.id
                          left join tel_telefonos t on t.id = at.accounts_tel_telefonos_1tel_telefonos_idb
                        where
                        	a.clean_name='".$nombre."'
                          and e.email_address='".$correo."'
                          and t.telefono in ('".$telefonos."')
                          and ac.rfc_c = '".$rfc."'
                        ";
        }
        //Nivel 1 - Nombre (limpio con algoritmo: clean_name) + email + algún teléfono
        if(!empty($nombre) && !empty($correo) &&  $totalTelefonos>0 ) {
            $consultas[] = "select '1' as nivel, 'Lead' as modulo, 'Leads' as moduloLink, lc.clean_name_c nombre, l.id as id, lc.rfc_c as rfc,
            'Nivel de match encontrado a través de la combinación del nombre, email y algún teléfono' as descripcion
                        from leads l
                        	inner join leads_cstm lc on lc.id_c=l.id
                        	left join email_addr_bean_rel er on er.bean_id = l.id and er.deleted=0
                        	left join email_addresses e on e.id=er.email_address_id and e.deleted =0
                        where
                        	lc.clean_name_c='".$nombre."'
                          and e.email_address='".$correo."'
                          and( l.phone_home in ('".$telefonos."') or l.phone_mobile in ('".$telefonos."') or l.phone_work in ('".$telefonos."')  or l.phone_other in ('".$telefonos."') )
                        ";
            $consultas[] ="select '1' as nivel, 'Cuenta' as modulo, 'Accounts' as moduloLink, a.clean_name nombre, a.id as id, ac.rfc_c as rfc,
            'Nivel de match encontrado a través de la combinación del nombre, email y algún teléfono' as descripcion
                        from accounts a
                        	inner join accounts_cstm ac on ac.id_c=a.id
                        	left join email_addr_bean_rel er on er.bean_id = a.id and er.deleted=0
                        	left join email_addresses e on e.id=er.email_address_id and e.deleted =0
                          left join accounts_tel_telefonos_1_c at on at.accounts_tel_telefonos_1accounts_ida = a.id
                          left join tel_telefonos t on t.id = at.accounts_tel_telefonos_1tel_telefonos_idb
                        where
                        	a.clean_name='".$nombre."'
                          and e.email_address='".$correo."'
                          and t.telefono in ('".$telefonos."')
                        ";
        }
        //Nivel 2 - Nombre (limpio con algoritmo: clean_name) + email o algún teléfono
        if(!empty($nombre) && (!empty($correo) || $totalTelefonos>0) ) {
          $consultas[] = "select '2' as nivel, 'Lead' as modulo,  'Leads' as moduloLink, lc.clean_name_c nombre, l.id as id, lc.rfc_c as rfc,
          'Nivel de match encontrado a través de la combinación del nombre, email o algún teléfono' as descripcion
                      from leads l
                        inner join leads_cstm lc on lc.id_c=l.id
                        left join email_addr_bean_rel er on er.bean_id = l.id and er.deleted=0
                        left join email_addresses e on e.id=er.email_address_id and e.deleted =0
                      where
                        lc.clean_name_c='".$nombre."'
                        and ( e.email_address='".$correo."' or l.phone_home in ('".$telefonos."') or l.phone_mobile in ('".$telefonos."') or l.phone_work in ('".$telefonos."')  or l.phone_other in ('".$telefonos."') )
                      ";
          $consultas[] ="select '2' as nivel, 'Cuenta' as modulo, 'Accounts' as moduloLink, a.clean_name nombre, a.id as id, ac.rfc_c as rfc,
          'Nivel de match encontrado a través de la combinación del nombre, email o algún teléfono' as descripcion
                      from accounts a
                      	inner join accounts_cstm ac on ac.id_c=a.id
                      	left join email_addr_bean_rel er on er.bean_id = a.id and er.deleted=0
                      	left join email_addresses e on e.id=er.email_address_id and e.deleted =0
                        left join accounts_tel_telefonos_1_c at on at.accounts_tel_telefonos_1accounts_ida = a.id
                        left join tel_telefonos t on t.id = at.accounts_tel_telefonos_1tel_telefonos_idb
                      where
                      	a.clean_name='".$nombre."'
                        and (e.email_address='".$correo."' or t.telefono in ('".$telefonos."'))
                      ";
        }
        //Nivel 3 - VALIDA CURP
        if(!empty($curp)) {
            $consultas[] ="select '3' as nivel, 'Cuenta' as modulo, 'Accounts' as moduloLink, a.clean_name nombre, a.id as id, ac.rfc_c as rfc,
            'Nivel de match encontrado a través de la CURP' as descripcion
            from accounts a
            inner join accounts_cstm ac on ac.id_c=a.id
            left join email_addr_bean_rel er on er.bean_id = a.id and er.deleted=0
            left join email_addresses e on e.id=er.email_address_id and e.deleted =0
            left join accounts_tel_telefonos_1_c at on at.accounts_tel_telefonos_1accounts_ida = a.id
            left join tel_telefonos t on t.id = at.accounts_tel_telefonos_1tel_telefonos_idb
            where ac.curp_c = '".$curp."'
            ";
        }

        // $queryRegistros = isset($consultas) ? implode(" union ",$consultas)." order by nivel desc ; " : '';
        $queryRegistros = !empty($consultas) ? implode(" union ",$consultas)." order by nivel desc ; " : '';

        //Ejecuta consulta Leads y Cuentas
        if(!empty($queryRegistros)){
            try {
                $resultadoConsulta = $db->query($queryRegistros);
                while ($row = $db->fetchByAssoc($resultadoConsulta)) {
                    //Itera resultado y agrega a lista de registros
                    //$item = [];
                    $items[$row['id']]['nivelMatch']=$row['nivel'];
                    $items[$row['id']]['modulo']=$row['modulo'];
                    $items[$row['id']]['moduloLink']=$row['moduloLink'];
                    $items[$row['id']]['nombre']=$row['nombre'];
                    $items[$row['id']]['id']=$row['id'];
                    $items[$row['id']]['rfc']=$row['rfc'];
                    $items[$row['id']]['descripcion']=$row['descripcion'];
                    //$items[$row['id']]=$item;
                }
            } catch (\Exception $e) {
                $GLOBALS['log']->fatal("Error - Servicio: validaDuplicado - " . $e->getMessage());
            }
        }

        //Consume servicio de similitud
        if (!empty($nombre)) {
            //Declara variable de consumo
            global $sugar_config;
            require_once("custom/Levementum/UnifinAPI.php");
            //Define petición
            $urlSimilarityToken = $sugar_config['similarity_api'].'/auth/login/token';
            $userToken = $sugar_config['similarity_user'];
            $pwdToken = $sugar_config['similarity_pwd'];
            $instanciaAPI = new UnifinAPI();
            $responseToken = $instanciaAPI->postSimilarityToken( $urlSimilarityToken, $userToken, $pwdToken  );

            if( !empty($responseToken) ){
                
                $token = $responseToken['access_token'];
                $ambiente=$sugar_config['similarity_env'];
                $servicioURI = $sugar_config['similarity_api'].'/similarity/';
                $peticion = array(
                    "business_name" => $nombre,
                    "show_items" => "10",
                    "cnn_name"=>$ambiente,
                    "get_by"=> "ByItems"
                );

                //Ejecuta petición de servicio
                $respuestaSimilitud = $instanciaAPI->postCallSimilarity($servicioURI,$peticion,$token);
                //Interpreta resultado
                if (!empty($respuestaSimilitud)) {
                    //greater_similarity
                    foreach ($respuestaSimilitud['greater_similarity'] as $nodo => $elemento ) {
                        //Itera resultado y agrega a lista de registros
                        if ($elemento['similarity']>.80) {
                            //$item = [];
                            $similitud = (!empty($elemento['similarity'])) ? number_format($elemento['similarity']*100) : "0";
                            $items[$elemento['id_bd']]['nivelMatch'].=( !isset($items[$elemento['id_bd']]['nivelMatch']) ) ? '3 - '.$similitud.'%' : ', 3 - '.$similitud.'%';
                            $items[$elemento['id_bd']]['modulo']=($elemento['source']=='accounts') ? 'Cuenta' : 'Lead';
                            $items[$elemento['id_bd']]['moduloLink']=($elemento['source']=='accounts') ? 'Accounts' : 'Leads';
                            $items[$elemento['id_bd']]['nombre']=$elemento['business_name'];
                            $items[$elemento['id_bd']]['id']=$elemento['id_bd'];
                            $items[$elemento['id_bd']]['rfc']= (!empty($items[$elemento['id_bd']]['rfc'])) ? $items[$elemento['id_bd']]['rfc']: '';
                            $items[$elemento['id_bd']]['descripcion'].=(empty($items[$elemento['id_bd']]['descripcion'])) ? 'Nivel de match encontrado a través de similitud por nombre ' : '';
                            //="Nivel de match encontrado a través del nombre";

                            if(empty($items[$elemento['id_bd']]['rfc'])){
                                //Obtener bean del modulo
                                $beanModulo=BeanFactory::retrieveBean($items[$elemento['id_bd']]['moduloLink'], $items[$elemento['id_bd']]['id'], array('disable_row_level_security' => true));
                                if (!empty($beanModulo) && $beanModulo != null) {
                                    $items[$elemento['id_bd']]['rfc']=$beanModulo->rfc_c;
                                }
                            }

                            $items[$elemento['id_bd']]['coincidencia']=$similitud;
                            //$items[$elemento['id_bd']]=$item;
                        }
                    }
                    //others_similar
                    foreach ($respuestaSimilitud['others_similar'] as $nodo => $elemento ) {
                        //Itera resultado y agrega a lista de registros
                        if ($elemento['similarity']>.80) {
                            //$item = [];
                            $similitud = (!empty($elemento['similarity'])) ? number_format($elemento['similarity']*100) : "0";
                            $items[$elemento['id_bd']]['nivelMatch'].=(empty($items[$elemento['id_bd']]['nivelMatch'])) ? '3 - '.$similitud.'%' : ', 3 - '.$similitud.'%';
                            $items[$elemento['id_bd']]['modulo']=($elemento['source']=='accounts') ? 'Cuenta' : 'Lead';
                            $items[$elemento['id_bd']]['moduloLink']=($elemento['source']=='accounts') ? 'Accounts' : 'Leads';
                            $items[$elemento['id_bd']]['nombre']=$elemento['business_name'];
                            $items[$elemento['id_bd']]['id']=$elemento['id_bd'];
                            $items[$elemento['id_bd']]['rfc']= (!empty($items[$elemento['id_bd']]['rfc'])) ? $items[$elemento['id_bd']]['rfc']: '';
    //                        $items[$elemento['id_bd']]['descripcion']="Nivel de match encontrado a través del nombre";
                            $items[$elemento['id_bd']]['descripcion'].=(empty($items[$elemento['id_bd']]['descripcion'])) ? 'Nivel de match encontrado a través de similitud por nombre ' : '';

                            if(empty($items[$elemento['id_bd']]['rfc'])){
                                //Obtener bean del modulo
                                $beanModulo=BeanFactory::retrieveBean($items[$elemento['id_bd']]['moduloLink'], $items[$elemento['id_bd']]['id'], array('disable_row_level_security' => true));
                                if (!empty($beanModulo) && $beanModulo != null) {
                                    $items[$elemento['id_bd']]['rfc']=$beanModulo->rfc_c;
                                }
                            }

                            $items[$elemento['id_bd']]['coincidencia']=$similitud;
                            //$items[$elemento['id_bd']]=$item;
                        }
                    }
                }
            }
        }

        return $items;
    }

}
