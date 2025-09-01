<?php

class Backlog_platform_user
{
    public function set_audit_user_platform($bean = null, $event = null, $args = null){
        global $app_list_strings;
        global $db;
        //Obtiene la plataforma
        $plataforma=$GLOBALS['service']->platform;
        $lista_plataformas_audit=$app_list_strings['plataformas_habilitadas_auditoria_list'];
        $plataformas_array=array();


        foreach ($lista_plataformas_audit as $clave => $valor) {
           array_push($plataformas_array,$clave);
        }
        /*$GLOBALS['log']->fatal('PLATAFORMA');
        $GLOBALS['log']->fatal($plataforma);
        $GLOBALS['log']->fatal('*********NUEVO LH DE LEADS***********');
        $GLOBALS['log']->fatal('PLATAFORMAS HABILITADAS');
        $GLOBALS['log']->fatal(print_r($plataformas_array,true));
        $GLOBALS['log']->fatal(print_r($plataforma,true));
        $GLOBALS['log']->fatal("--".in_array($plataforma,$plataformas_array));*/

        //Se establece tabla de auditoria solo para plataformas que existen en la lista plataformas habilitadas para auditoria
        if(in_array($plataforma,$plataformas_array)){

            //Obtiene el usuario relacionado a la plataforma
            $list_platform_user = $app_list_strings['plataforma_usuario_grupo_list'];

            //Obtiene el nombre de usuario dependiendo la plataforma
            $nombre_usuario_gpo=$list_platform_user[$plataforma];

            //Obtiene id del nombre de usuario
            $query_user_gpo="SELECT id FROM users WHERE user_name='{$nombre_usuario_gpo}'";
            $id_user="";
            $resultQueryUserGpo = $db->query($query_user_gpo);
            while ($row = $db->fetchByAssoc($resultQueryUserGpo)){
                $id_user = $row['id'];
            }

            /*$GLOBALS['log']->fatal("ID DE USUARIO DE GRUPO OBTENIDO");
            $GLOBALS['log']->fatal($id_user);*/

            if($id_user!=""){
                $id_u_audit=create_guid();
                $event_id=create_guid();
                $date= TimeDate::getInstance()->nowDb();
                //Establece nuevo registro en tabla de auditoria
                $sqlInsert="INSERT INTO `lev_backlog_audit` (`id`,`parent_id`,`date_created`,`created_by`,`field_name`,`data_type`,`before_value_string`,`after_value_string`,`before_value_text`,`after_value_text`,`event_id`,`date_updated`)
                VALUES ('{$id_u_audit}','{$bean->id}','{$date}','{$id_user}','plataforma','varchar','','{$id_user}',NULL,NULL,'{$event_id}',NULL)";

                $GLOBALS['log']->fatal("Insert: ",$sqlInsert);
                $db->query($sqlInsert);
            }

        }

    }

    public function actualizaSolicitud($bean = null, $event = null, $args = null)
    {
        // Corregimos la condición del log (agregando paréntesis)
        $GLOBALS['log']->fatal("platform: " . (isset($_SESSION['platform']) ? $_SESSION['platform'] : 'no definido'));
        
        // Validamos que no se ejecute desde API externas
        if (isset($_SESSION['platform']) && $_SESSION['platform'] != 'base') {
            
            $linkopor = 'lev_backlog_opportunities';

            if ($bean->load_relationship($linkopor)) {
                $linkFound = true;
                
                $GLOBALS['log']->fatal("Link encontrado: " . $linkopor);
                
                // Obtenemos los IDs relacionados
                $relatedIds = $bean->$linkopor->get();

                $GLOBALS['log']->fatal("Related IDs: " . print_r($relatedIds, true));

                if (!empty($relatedIds)) {
                    // Tomamos el primer relacionado
                    $relatedId = reset($relatedIds);

                    // Obtenemos el bean relacionado
                    $relatedBean = BeanFactory::getBean('Opportunities', $relatedId);

                    if ($relatedBean && !empty($relatedBean->idsolicitud_c)) {
                        // Seteamos el campo en el bean padre
                        $bean->numero_de_solicitud = $relatedBean->idsolicitud_c;
                        
                        $GLOBALS['log']->fatal("numero_de_solicitud actualizado: " . $bean->numero_de_solicitud);
                    } else {
                        $GLOBALS['log']->fatal("Related bean no encontrado o idsolicitud_c vacío");
                    }
                } else {
                    $GLOBALS['log']->fatal("No hay IDs relacionados");
                }
            }
            
            if (!$linkFound) {
                $GLOBALS['log']->fatal("No se pudo cargar ninguna relación");
                // Debug: verificar relaciones disponibles
                $GLOBALS['log']->fatal("Relaciones disponibles: " . print_r($bean->get_linked_fields(), true));
            }
        } else {
            $GLOBALS['log']->fatal("Ejecución diferente API, no se procesa");
        }
    }

}
