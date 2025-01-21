<?php

/**
 * Created by Levementum.com
 * User: Jorge
 * Date: 6/3/2015
 * Time: 2:35 PM
 */

require_once("custom/Levementum/UnifinAPI.php");

class Account_Hooks
{
    public function nivelSatisfaccion($bean = null, $event = null, $args = null)
    {
        if ($bean->fetched_row['nivel_satisfaccion_c'] != $bean->nivel_satisfaccion_c && $bean->nivel_satisfaccion_c != 'Sin Clasificar') {
            //Crea Notificacion al Promotor Leasing
            $notification_bean = BeanFactory::getBean("Notifications");
            $notification_bean->name = 'Resultado NPS ' . $bean->name;
            $notification_bean->description = 'ALERTA: El resultado de la encuesta de Nivel de Satisfacción de ' . $bean->name . ' es: ' . $bean->nivel_satisfaccion_c;
            $notification_bean->parent_id = $bean->id;
            $notification_bean->parent_type = 'Accounts';
            $notification_bean->assigned_user_id = $bean->user_id_c;
            $notification_bean->severity = "alert";
            $notification_bean->is_read = 0;
            $notification_bean->save();
            //Crea Notificacion al Director Leasing
            $User = new User();
            $User->retrieve($bean->user_id_c);
            $jefe = $User->reports_to_id;
            $notification_bean = BeanFactory::getBean("Notifications");
            $notification_bean->name = 'Resultado NPS ' . $bean->name;
            $notification_bean->description = 'ALERTA: El resultado de la encuesta de Nivel de Satisfacción de ' . $bean->name . ' es: ' . $bean->nivel_satisfaccion_c;
            $notification_bean->parent_id = $bean->id;
            $notification_bean->parent_type = 'Accounts';
            $notification_bean->assigned_user_id = $jefe;
            $notification_bean->severity = "alert";
            $notification_bean->is_read = 0;
            $notification_bean->save();
        }
        if ($bean->fetched_row['nivel_satisfaccion_factoring_c'] != $bean->nivel_satisfaccion_factoring_c && $bean->nivel_satisfaccion_factoring_c != 'Sin Clasificar') {
            //Crea Notificacion al Promotor Factoraje
            $notification_bean = BeanFactory::getBean("Notifications");
            $notification_bean->name = 'Resultado NPS ' . $bean->name;
            $notification_bean->description = 'ALERTA: El resultado de la encuesta de Nivel de Satisfacción de ' . $bean->name . ' es: ' . $bean->nivel_satisfaccion_factoring_c;
            $notification_bean->parent_id = $bean->id;
            $notification_bean->parent_type = 'Accounts';
            $notification_bean->assigned_user_id = $bean->user_id1_c;
            $notification_bean->severity = "alert";
            $notification_bean->is_read = 0;
            $notification_bean->save();
            //Crea Notificacion al Director de Factoraje
            $User = new User();
            $User->retrieve($bean->user_id1_c);
            $jefe = $User->reports_to_id;
            $notification_bean = BeanFactory::getBean("Notifications");
            $notification_bean->name = 'Resultado NPS ' . $bean->name;
            $notification_bean->description = 'ALERTA: El resultado de la encuesta de Nivel de Satisfacción de ' . $bean->name . ' es: ' . $bean->nivel_satisfaccion_factoring_c;
            $notification_bean->parent_id = $bean->id;
            $notification_bean->parent_type = 'Accounts';
            $notification_bean->assigned_user_id = $jefe;
            $notification_bean->severity = "alert";
            $notification_bean->is_read = 0;
            $notification_bean->save();
        }
        if ($bean->fetched_row['nivel_satisfaccion_ca_c'] != $bean->nivel_satisfaccion_ca_c && $bean->nivel_satisfaccion_ca_c != 'Sin Clasificar') {
            //Crea Notificacion al Promotor Leasing
            $notification_bean = BeanFactory::getBean("Notifications");
            $notification_bean->name = 'Resultado NPS ' . $bean->name;
            $notification_bean->description = 'ALERTA: El resultado de la encuesta de Nivel de Satisfacción de ' . $bean->name . ' es: ' . $bean->nivel_satisfaccion_ca_c;
            $notification_bean->parent_id = $bean->id;
            $notification_bean->parent_type = 'Accounts';
            $notification_bean->assigned_user_id = $bean->user_id2_c;
            $notification_bean->severity = "alert";
            $notification_bean->is_read = 0;
            $notification_bean->save();
            //Crea Notificacion al Director Leasing
            $User = new User();
            $User->retrieve($bean->user_id2_c);
            $jefe = $User->reports_to_id;
            $notification_bean = BeanFactory::getBean("Notifications");
            $notification_bean->name = 'Resultado NPS ' . $bean->name;
            $notification_bean->description = 'ALERTA: El resultado de la encuesta de Nivel de Satisfacción de ' . $bean->name . ' es: ' . $bean->nivel_satisfaccion_ca_c;
            $notification_bean->parent_id = $bean->id;
            $notification_bean->parent_type = 'Accounts';
            $notification_bean->assigned_user_id = $jefe;
            $notification_bean->severity = "alert";
            $notification_bean->is_read = 0;
            $notification_bean->save();
        }
    }

    public function detectaEstado($bean = null, $event = null, $args = null)
    {
        global $current_user;
        if (empty($bean->fetched_row['id'])) {
            $_SESSION['estadoPersona'] = 'insertando';
        } else {
            $_SESSION['estadoPersona'] = 'actualizando';
        }
        $GLOBALS['log']->fatal(__CLASS__ . "->" . __FUNCTION__ . "  <" . $current_user->user_name . "> : ESTADO: " . $_SESSION['estadoPersona']);
    }

    public function set_primary_team($bean = null, $event = null, $args = null)
    {

        global $db;
        $query = <<<SQL
SELECT team_set_id, default_team FROM users
WHERE id = '{$bean->user_id_c}'
SQL;
        $queryResult = $db->query($query);
        while ($row = $db->fetchByAssoc($queryResult)) {
            $bean->team_id = $row['default_team'];
        }
    }

    /** BEGIN CUSTOMIZATION: jgarcia@levementum.com 6/3/2015 Description: when the account is saved, bring the personal team of the Promotor leasing,
     * Promotor Factoraje, Promotor Credito, store their default team set teams into the account. and then push these teams to all the opportunities
     * related to the account*/
    /*
    public function copy_team_to_Opp($bean = null, $event = null, $args = null)
    {

        $bean->load_relationship('teams');
        $new_account_team_ids = array();
        //query the teams for Promotor leasing, Promotor Factoraje, and Promotor Credito users
        global $db;
        $query = <<<SQL
SELECT team_set_id, default_team FROM users
WHERE id IN ('{$bean->user_id_c}', '{$bean->user_id1_c}','{$bean->user_id2_c}')
SQL;
        $queryResult = $db->query($query);
        while ($row = $db->fetchByAssoc($queryResult)) {
            $new_account_team_ids[] = $row['default_team'];
        }
        $new_account_team_ids[] = '3acdb6c1-384a-6f06-ff8b-56571ff92cc3'; // Default Team para Juridico.

        //Add the teams to the account
        $bean->teams->replace($new_account_team_ids);
        //trobinson@levementum.com commented out to fix save loop likely not neccisary needs testing
//        $bean->save();

        require_once('modules/Teams/TeamSet.php');
        $teamSetBean = new TeamSet();

        //Retrieve the teams from the team_set_id
        $teams = $teamSetBean->getTeams($bean->team_set_id);
        //Retrieve Related Opportunities
        $related_opps = $bean->get_linked_beans('opportunities', 'Opportunity');

        //Add teams to related opportunities
        foreach ($related_opps as $related_opp) {
            $related_opp->load_relationship('teams');
            $related_opp->team_id = $bean->team_id;
            $related_opp->team_set_id = $bean->team_set_id;
            $new_team_ids = array();
            foreach ($teams as $aTeam) {
                $new_team_ids[] = $aTeam->id;
            }
            $bean->teams->replace($new_team_ids);
            $related_opp->save();
        }
    }*/

    /* END CUSTOMIZATION */

    /**
     * @author trobinson@levementum.com
     * @date   6/5/15
     * @brief  process telephono relationships
     *
     * @param  bean       The object.
     * @param  event      The type of logic hook that is being called.
     * @param  arguments  Additional arguments.
     * @return void
     * @hook_array tbd
     *
     */
    public function account_telefonos($bean = null, $event = null, $args = null)
    {
        $current_id_list = array();

        if ($_REQUEST['module'] != 'Import' && $_SESSION['platform'] == 'base') {
            //add update current records
            foreach ($bean->account_telefonos as $a_telefono) {
                if (!empty($a_telefono['id'])) {
                    $telefono = BeanFactory::getBean('Tel_Telefonos', $a_telefono['id']);
                } else {
                    $telefono = BeanFactory::newBean('Tel_Telefonos');
                }
                $telefono->name = $a_telefono['telefono'] . ' ' . $a_telefono['extension'];
                //$telefono->secuencia = $a_telefono['secuencia'];
                $telefono->telefono = $a_telefono['telefono'];
                $telefono->tipotelefono = $a_telefono['tipotelefono'];
                $telefono->extension = $a_telefono['extension'];
                $telefono->estatus = $a_telefono['estatus'];
                $telefono->pais = $a_telefono['pais'];
                $telefono->principal = ($a_telefono['principal'] == 1) ? 1 : 0;
                $telefono->accounts_tel_telefonos_1accounts_ida = $bean->id;
                $telefono->assigned_user_id = $bean->assigned_user_id;
                $telefono->team_set_id = $bean->team_set_id;
                $telefono->team_id = $bean->team_id;
                $telefono->whatsapp_c = ($a_telefono['tipotelefono'] == 3 || $a_telefono['tipotelefono'] == 4) && $a_telefono['whatsapp_c'] == 1 ? 1 : 0;
                $current_id_list[] = $telefono->save();
            }
            //retrieve all related records
            /*$bean->load_relationship('accounts_tel_telefonos_1');
            foreach ($bean->accounts_tel_telefonos_1->getBeans() as $a_telefono) {
                if (!in_array($a_telefono->id, $current_id_list)) {
                    //$a_telefono->mark_deleted($a_telefono->id);
                }
            }*/
        }
    }

    /**
     * @author bdekoning@levementum.com
     * @date 6/10/15
     * @brief Updates/creates new Direcciones records based on saved values from record
     *
     * @param Account $bean
     * @param $event
     * @param $args
     *
     * after_save
     */
    public function account_direcciones(&$bean, $event, $args)
    {
        global $current_user, $db;
        $current_id_list = array();
        // select type | Street (Calle) | primary | inactive
        // populate relationships from dropdowns

        //Nuevo account_direcciones_n
        if ($_REQUEST['module'] != 'Import' && $_SESSION['platform'] != 'unifinAPI' && $bean->omitir_guardado_direcciones_c == 0) {
            //$GLOBALS['log']->fatal("DIRECCIONES:*********************");
            //$GLOBALS['log']->fatal(print_r($bean->account_direcciones,true));
            foreach ($bean->account_direcciones as $direccion_row) {
                /** @var dire_Direccion $direccion */
                $direccion = BeanFactory::getBean('dire_Direccion', $direccion_row['id']);
                //$id_sepomex_anterior=$direccion->dir_sepomex_dire_direcciondir_sepomex_ida;

                if (empty($direccion_row['id'])) {
                    //generar el guid
                    $guid = create_guid();
                    $direccion->id = $guid;
                    $direccion->new_with_id = true;
                    $new = true;
                } else {
                    $new = false;
                }
                $direccion->name = $direccion_row['calle'];
                //parse array to string for multiselects
                $tipo_string = "";
                if (!empty($direccion_row['tipodedireccion'] != "")) {
                    $tipo_string .= '^' . $direccion_row['tipodedireccion'][0] . '^';
                    /*
                    for ($i = 1; $i < count($direccion_row['tipodedireccion']); $i++) {
                        $tipo_string .= ',^' . $direccion_row['tipodedireccion'][$i] . '^';
                    }
                    */
                }
                $direccion->tipodedireccion = $tipo_string;
                $direccion->calle = $direccion_row['calle'];
                $direccion->principal = ($direccion_row['principal'] == true); // ensure boolean conversion
                $direccion->inactivo = ($direccion_row['inactivo'] == true);
                $direccion->numint = $direccion_row['numint'];
                $direccion->numext = $direccion_row['numext'];
                $direccion->indicador = $direccion_row['indicador'];
                //teams
                $direccion->team_id = $bean->team_id;
                $direccion->team_set_id = $bean->team_set_id;
                $direccion->assigned_user_id = $bean->assigned_user_id;
                //
                // populate related account id
                $direccion->accounts_dire_direccion_1accounts_ida = $bean->id;

                
                $nombre_colonia_query = "Select name from dire_colonia where id ='" . $direccion_row['colonia'] . "'";
                $nombre_municipio_query = "Select name from dire_municipio where id ='" . $direccion_row['municipio'] . "'";
                $querycolonia = $db->query($nombre_colonia_query);
                $coloniaName = $db->fetchByAssoc($querycolonia);
                $querymunicipio = $db->query($nombre_municipio_query);
                $municipioName = $db->fetchByAssoc($querymunicipio);
                $direccion_completa = $direccion_row['calle'] . " " . $direccion_row['numext'] . " " . ($direccion_row['numint'] != "" ? "Int: " . $direccion_row['numint'] : "") . ", Colonia " . $coloniaName['name'] . ", Municipio " . $municipioName['name'];
                
                $direccion->name = $direccion_completa;
                
                if ($direccion->load_relationship('dire_direccion_dire_pais')) {
                    if ($direccion_row['pais'] !== $direccion->dire_direccion_dire_paisdire_pais_ida) {
                        $direccion->dire_direccion_dire_pais->delete($direccion->id);
                        $direccion->dire_direccion_dire_pais->add($direccion_row['pais']);
                    }
                }
                if ($direccion->load_relationship('dire_direccion_dire_estado')) {
                    if ($direccion_row['estado'] !== $direccion->dire_direccion_dire_estadodire_estado_ida) {
                        $direccion->dire_direccion_dire_estado->delete($direccion->id);
                        $direccion->dire_direccion_dire_estado->add($direccion_row['estado']);
                    }
                }
                if ($direccion->load_relationship('dire_direccion_dire_municipio')) {
                    if ($direccion_row['municipio'] !== $direccion->dire_direccion_dire_municipiodire_municipio_ida) {
                        $direccion->dire_direccion_dire_municipio->delete($direccion->id);
                        $direccion->dire_direccion_dire_municipio->add($direccion_row['municipio']);
                    }
                }
                if ($direccion->load_relationship('dire_direccion_dire_ciudad')) {
                    if ($direccion_row['ciudad'] !== $direccion->dire_direccion_dire_ciudaddire_ciudad_ida) {
                        $direccion->dire_direccion_dire_ciudad->delete($direccion->id);
                        $direccion->dire_direccion_dire_ciudad->add($direccion_row['ciudad']);
                    }
                }

                if ($direccion->load_relationship('dire_direccion_dire_codigopostal')) {
                    try {
                        //if (!empty($direccion_row['postal'])) {
                        if ($direccion_row['postal'] !== $direccion->dire_direccion_dire_codigopostal) {
                            $direccion->dire_direccion_dire_codigopostal->delete($direccion->id);
                            $direccion->dire_direccion_dire_codigopostal->add($direccion_row['postal']);
                        }
                    } catch (Exception $e) {
                        $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
                    }
                }

                if ($direccion->load_relationship('dire_direccion_dire_colonia')) {
                    if ($direccion_row['colonia'] !== $direccion->dire_direccion_dire_coloniadire_colonia_ida) {
                        $direccion->dire_direccion_dire_colonia->delete($direccion->id);
                        $direccion->dire_direccion_dire_colonia->add($direccion_row['colonia']);
                    }
                }

                $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : DIRECCION NOMBRE: " . $direccion_completa);
                $current_id_list[] = $direccion->id;
                if ($new) {
                    $direccion->save();
                } else {
                    $inactivo = $direccion->inactivo == 1 ? $direccion->inactivo : 0;
                    $principal = $direccion->principal == 1 ? $direccion->principal : 0;

                     $query = <<<SQL
update dire_direccion set  name = '{$direccion->name}', tipodedireccion = '{$direccion->tipodedireccion}',indicador = '{$direccion->indicador}',  calle = '{$direccion->calle}', numext = '{$direccion->numext}', numint= '{$direccion->numint}', principal=$principal, inactivo =$inactivo  where id = '{$direccion->id}';
SQL;

                    try {
                        $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Update *784 " . $query);
                        
                        $resultado = $db->query($query);
                        $callApi = new UnifinAPI();

                        if ($direccion->sincronizado_unics_c == '0') {
                            $direccion = $callApi->insertaDireccion($direccion);
                            
                        } else {
                            $direccion = $callApi->actualizaDireccion($direccion);
                        }
                        $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : resultado " . $db->getAffectedRowCount($resultado));
                        
                    } catch (Exception $e) {
                        $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
                    }
                }
                //$direccion->save();
            }
            //retrieve all related records
            $bean->load_relationship('accounts_dire_direccion_1');
            foreach ($bean->accounts_dire_direccion_1->getBeans() as $a_direccion) {
                if (!in_array($a_direccion->id, $current_id_list)) {
                    //$a_direccion->mark_deleted($a_direccion->id);
                }
            }
        }
    }

    public function esDireccionFiscal( $indicador ){

        $indicador_direcciones_fiscales = array(2,3,6,7,10,11,14,15,18,19,22,23,26,27,30,31,34,35,38,39,42,43,46,47,50,51,54,55,58,59,62,63);

        if( in_array($indicador,$indicador_direcciones_fiscales) ){
            return true;
        }
        return false;

    }

    /* TODO: Add Definition and comment */
    public function listaNegraCall($bean = null, $event = null, $args = null)
    {
        if ($bean->primernombre_c != $bean->fetched_row['primernombre_c'] || $bean->segundonombre_c != $bean->fetched_row['segundonombre_c']
            || $bean->apellidopaterno_c != $bean->fetched_row['apellidopaterno_c'] || $bean->razonsocial_c != $bean->fetched_row['razonsocial_c']) {

            //login class located at custom/Levementum/UnifinAPI.php
            global $db;
            $callApi = new UnifinAPI();
            $coincidencias = $callApi->listaNegra(
                $bean->primernombre_c,
                $bean->segundonombre_c,
                $bean->apellidopaterno_c,
                $bean->apellidomaterno_c,
                $bean->tipodepersona_c,
                $bean->razonsocial_c
            );
            $bean->lista_negra_c = $coincidencias['listaNegra'] <= 0 ? 0 : 1;
            $bean->pep_c = $coincidencias['PEP'] <= 0 ? 0 : 1;

            // todo: Validar que el valor de Alto riesgo sea solo por valor del oficial de cumplimiento - Waldo //VAlor anterior 0
            if ($bean->lista_negra_c > 0 || $bean->pep_c > 0) {
                $bean->riesgo_c = 'Alto';
            } else {
                //Validar riesgo pais y profesión antes de establecer el riesgo como bajo
                global $db, $current_user;
                $query = <<<SQL
SELECT ifnull(p.altoriesgo,0) as AltoRiesgo
FROM accounts_cstm acc
LEFT OUTER JOIN dire_pais p on acc.pais_nacimiento_c = p.id
where acc.id_c = '{$bean->user_id_c}'
SQL;
                $queryResult = $db->query($query);
                while ($row = $db->fetchByAssoc($queryResult)) {
                    if ($row['AltoRiesgo'] == 1) {
                        $bean->riesgo_c = 'Alto';
                    } else {
                        //Valida el riesgo por profesión
                        /*
                        if(!_.isEmpty(this.model.get("profesion_c")) || this.model.get("profesion_c") != null) {
                            this.model.set("riesgo_c", "Bajo");
                            var profesionActual = this.model.get("profesion_c");
                            var profesiones_de_riesgo = app.lang.getAppListStrings('profesion_riesgo_list');
                            Object.keys(profesiones_de_riesgo).forEach(function (key) {
                                if (key == profesionActual) {
                                    self.model.set("riesgo_c", "Alto");
                                }
                            });
                        }*/

                        $bean->riesgo_c = 'Bajo';
                    }
                }
            }
            $GLOBALS['log']->fatal(" <" . $current_user->user_name . "> : coincidencias['PEP'] " . print_r($coincidencias['PEP'], 1));
            $GLOBALS['log']->fatal(" <" . $current_user->user_name . "> : coincidencias['listaNegra'] " . print_r($coincidencias['listaNegra'], 1));
            $GLOBALS['log']->fatal(" <" . $current_user->user_name . "> : $bean->riesgo_c " . print_r($bean->riesgo_c, 1));
        }
    }

    public function account_direccion_buro_credito($bean = null, $event = null, $args = null)
    {
        global $current_user, $db;
        $GLOBALS['log']->fatal("******DIRECCION DE BURÓ*****");
        //Validación para evitar que entre proceso cuando no se ha establecido valor en dirección de buró
        if (is_countable($bean->account_direccion_buro_credito)) {
            if (count($bean->account_direccion_buro_credito) > 0) {
                foreach ($bean->account_direccion_buro_credito as $direccion_row) {
                    $direccion = BeanFactory::getBean('dire_Direccion', $direccion_row['id']);
                    if (empty($direccion_row['id'])) {
                        //generar el guid
                        $guid = create_guid();
                        $direccion->id = $guid;
                        $direccion->new_with_id = true;
                        $new = true;
                    } else {
                        $new = false;
                    }

                    $direccion->name = $direccion_row['calle'];
                    //parse array to string for multiselects
                    $tipo_string = "";
                    if (!empty($direccion_row['tipodedireccion'] != "")) {
                        $tipo_string .= '^' . $direccion_row['tipodedireccion'][0] . '^';
                        /*
                        for ($i = 1; $i < count($direccion_row['tipodedireccion']); $i++) {
                            $tipo_string .= ',^' . $direccion_row['tipodedireccion'][$i] . '^';
                        }
                        */
                    }
                    $direccion->tipodedireccion = $tipo_string;
                    $direccion->calle = $direccion_row['calle'];
                    $direccion->principal = ($direccion_row['principal'] == true); // ensure boolean conversion
                    $direccion->inactivo = ($direccion_row['inactivo'] == true);
                    $direccion->numint = $direccion_row['numint'];
                    $direccion->numext = $direccion_row['numext'];
                    $direccion->indicador = $direccion_row['indicador'];
                    //teams
                    $direccion->team_id = $bean->team_id;
                    $direccion->team_set_id = $bean->team_set_id;
                    $direccion->assigned_user_id = $bean->assigned_user_id;
                    //
                    // populate related account id
                    $direccion->accounts_dire_direccion_1accounts_ida = $bean->id;


                    $nombre_colonia_query = "Select name from dire_colonia where id ='" . $direccion_row['colonia'] . "'";
                    $nombre_municipio_query = "Select name from dire_municipio where id ='" . $direccion_row['municipio'] . "'";
                    $querycolonia = $db->query($nombre_colonia_query);
                    $coloniaName = $db->fetchByAssoc($querycolonia);
                    $querymunicipio = $db->query($nombre_municipio_query);
                    $municipioName = $db->fetchByAssoc($querymunicipio);
                    $direccion_completa = $direccion_row['calle'] . " " . $direccion_row['numext'] . " " . ($direccion_row['numint'] != "" ? "Int: " . $direccion_row['numint'] : "") . ", Colonia " . $coloniaName['name'] . ", Municipio " . $municipioName['name'];

                    $direccion->name = $direccion_completa;

                    if ($direccion->load_relationship('dire_direccion_dire_pais')) {
                        if ($direccion_row['pais'] !== $direccion->dire_direccion_dire_paisdire_pais_ida) {
                            $direccion->dire_direccion_dire_pais->delete($direccion->id);
                            $direccion->dire_direccion_dire_pais->add($direccion_row['pais']);
                        }
                    }
                    if ($direccion->load_relationship('dire_direccion_dire_estado')) {
                        if ($direccion_row['estado'] !== $direccion->dire_direccion_dire_estadodire_estado_ida) {
                            $direccion->dire_direccion_dire_estado->delete($direccion->id);
                            $direccion->dire_direccion_dire_estado->add($direccion_row['estado']);
                        }
                    }
                    if ($direccion->load_relationship('dire_direccion_dire_municipio')) {
                        if ($direccion_row['municipio'] !== $direccion->dire_direccion_dire_municipiodire_municipio_ida) {
                            $direccion->dire_direccion_dire_municipio->delete($direccion->id);
                            $direccion->dire_direccion_dire_municipio->add($direccion_row['municipio']);
                        }
                    }
                    if ($direccion->load_relationship('dire_direccion_dire_ciudad')) {
                        if ($direccion_row['ciudad'] !== $direccion->dire_direccion_dire_ciudaddire_ciudad_ida) {
                            $direccion->dire_direccion_dire_ciudad->delete($direccion->id);
                            $direccion->dire_direccion_dire_ciudad->add($direccion_row['ciudad']);
                        }
                    }

                    if ($direccion->load_relationship('dire_direccion_dire_codigopostal')) {
                        try {
                            //if (!empty($direccion_row['postal'])) {
                            if ($direccion_row['postal'] !== $direccion->dire_direccion_dire_codigopostal) {
                                $direccion->dire_direccion_dire_codigopostal->delete($direccion->id);
                                $direccion->dire_direccion_dire_codigopostal->add($direccion_row['postal']);
                            }
                        } catch (Exception $e) {
                            $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
                        }
                    }

                    if ($direccion->load_relationship('dire_direccion_dire_colonia')) {
                        if ($direccion_row['colonia'] !== $direccion->dire_direccion_dire_coloniadire_colonia_ida) {
                            $direccion->dire_direccion_dire_colonia->delete($direccion->id);
                            $direccion->dire_direccion_dire_colonia->add($direccion_row['colonia']);
                        }
                    }

                    $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : DIRECCION NOMBRE: " . $direccion_completa);
                    $current_id_list[] = $direccion->id;
                    if ($new) {
                        $direccion->save();
                    } else {
                        $inactivo = $direccion->inactivo == 1 ? $direccion->inactivo : 0;
                        $principal = $direccion->principal == 1 ? $direccion->principal : 0;

                        $query = <<<SQL
    update dire_direccion set  name = '{$direccion->name}', tipodedireccion = '{$direccion->tipodedireccion}',indicador = '{$direccion->indicador}',  calle = '{$direccion->calle}', numext = '{$direccion->numext}', numint= '{$direccion->numint}', principal=$principal, inactivo =$inactivo  where id = '{$direccion->id}';
    SQL;
                        $GLOBALS['log']->fatal("ACTUALIZANDO DIRECCIÓN DE BURÓ");
                        $GLOBALS['log']->fatal($query);
                        try {
                            $resultado = $db->query($query);
                        } catch (Exception $e) {
                            $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
                        }
                    }
                }
            } else {
                $GLOBALS['log']->fatal("NO SE ENCONTRÓ DIRECCIÓN BURÓ DE CRÉDITO PARA GUARDAR");
            }
        }
    }

    /* TODO: Add Definition and comment */
    public function crearFolioProspecto($bean = null, $event = null, $args = null)
    {
        global $current_user;
        if ($bean->idprospecto_c == '' && $bean->tipo_registro_cuenta_c == '2') { //2 = Propsecto
            global $db;
            $callApi = new UnifinAPI();
            $numeroDeFolio = $callApi->generarFolios(3);
            $bean->idprospecto_c = $numeroDeFolio;
            $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : numeroDeFolio   " . ": $numeroDeFolio ");
        }
    }

    /* TODO: Add Definition and comment */
    public function crearFolioCliente($bean = null, $event = null, $args = null)
    {
        global $current_user;
        //Tipo Cuenta: 3-Cliente, 4-Persona, 5-Proveedor **** SubTipo-Cuenta: 7-Interesado
        if (($bean->idcliente_c == '' || $bean->idcliente_c == '0') && ($bean->estatus_c == 'Interesado' || $bean->tipo_registro_cuenta_c == '3' || $bean->tipo_registro_cuenta_c == '5' || ($bean->tipo_registro_cuenta_c == '4' && $bean->tipo_relacion_c != "") || $bean->esproveedor_c || $bean->cedente_factor_c || $bean->deudor_factor_c || ($bean->tipo_registro_cuenta_c == "2" && $bean->subtipo_registro_cuenta_c == "7") || !empty($bean->id_uniclick_c))) {
            global $db;
            $callApi = new UnifinAPI();
            $numeroDeFolio = $callApi->generarFolios(1, $bean);
            $bean->idcliente_c = $numeroDeFolio;
            $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> :numeroDeFolio   " . ": $numeroDeFolio ");

            //$this->actualizaOportunidadProspecto($bean);
        }

        //Generación de referencia bancaria
        if ($bean->idcliente_c > 0 && (empty($bean->referencia_bancaria_c) || $bean->referencia_bancaria_c == '')) {
            $bean->referencia_bancaria_c = $this->GeneraReferenciaBancaria($bean->idcliente_c);
        }
    }

    //UNI403 1. Cuando se crea un prospecto nuevo, si se podrá agregar una nueva oportunidad (pero solamente una),
    // con los campos Monto, activo y Plazo en este momento no debes de ir por un folio de solicitud a UNICS si no solamente
    // crear la oportunidad en SUGAR, con un estatus de “OPORTUNIDAD DE PROSPECTO”. Cuando se le da interesado a este prospecto,
    // además de la funcionalidad actual (invocación de BPM y de folio de cliente), deberás de obtener un folio de solicitud
    // para actualizar el folio y estatus (“INTEGRACION DE EXPEDIENTE”) de dicha oportunidad.
    // TODO UNIFIN: Modificar función actualizaOportunidadProspecto de Account_Hook.php ya que está haciendo un Query buscando oportunidades en estatus "'Oportunidad de Prospecto'" para crear procesos
    public function actualizaOportunidadProspecto($bean)
    {
        //$GLOBALS['log']->fatal(__FILE__." - ".__CLASS__."->".__FUNCTION__."Carlos Zaragoza BEAN : " . print_r($bean,1));
        $callApi = new UnifinAPI();
        try {
            global $db, $current_user;
            $query = "SELECT
                          a.name
                          ,oppc.estatus_c as estatus
                          , oppc.id_c as opportunity_id
                          , oppc.monto_c
                          , oppc.enviar_por_correo_c
                          , oppc.tipo_producto_c
                          , oppc.usuario_bo_c
                          , oppc.tipo_de_operacion_c
                          , oppc.plazo_c
                          , oppc.id_activo_c
                          , oppc.index_activo_c
                          , oppc.ca_valor_auto_iva_c
                          , oppc.ca_tasa_c
                          , oppc.ca_pago_mensual_c
                          , oppc.ca_importe_enganche_c
                          , oppc.idcot_bpm_c
                          , oppc.id_linea_credito_c
                          , oppc.es_multiactivo_c
                          , oppc.multiactivo_c
                          , oppc.porcentaje_ca_c
                          , oppc.vrc_c
                          , oppc.vri_c
                          , oppc.deposito_garantia_c
                          , oppc.porcentaje_renta_inicial_c
                          , oppc.ratificacion_incremento_c
                          , oppc.ri_ca_tasa_c
                          , oppc.ri_deposito_garantia_c
                          , oppc.ri_porcentaje_ca_c
                          , oppc.ri_porcentaje_renta_inicial_c
                          , oppc.ri_vrc_c
                          , oppc.ri_vri_c
                          , oppc.monto_ratificacion_increment_c
                          , oppc.plazo_ratificado_incremento_c
                          , oppc.ri_usuario_bo_c
                          , oppc.instrumento_c
                          , oppc.puntos_sobre_tasa_c
                          , oppc.puntos_tasa_moratorio_c
                          , oppc.tipo_tasa_ordinario_c
                          , oppc.tipo_tasa_moratorio_c
                          , oppc.instrumento_moratorio_c
                          , oppc.factor_moratorio_c
                          , oppc.cartera_descontar_c
                          , oppc.tasa_fija_ordinario_c
                          , oppc.tasa_fija_moratorio_c
                        FROM
                          accounts_opportunities aopp
                          INNER JOIN opportunities_cstm oppc ON oppc.id_c = aopp.opportunity_id
                          INNER JOIN opportunities opp ON oppc.id_c = opp.id
                          INNER JOIN accounts a ON a.id = aopp.account_id
                        WHERE account_id = '{$bean->id}'
                        AND oppc.estatus_c = 'OP'
                        AND aopp.deleted = 0
                        ";
            $queryResult = $db->query($query);
            //$GLOBALS['log']->fatal(__FILE__." - ".__CLASS__."->".__FUNCTION__.": query " . print_r($query,1));

            while ($row = $db->fetchByAssoc($queryResult)) {
                //obten el folio de la solicitud
                $numeroDeFolio = $callApi->generarFolios(2);

                //PARAMETROS
                //TODO UNIFIN: Error: Al actualizar la persona se están disparando los hooks de operaciones e iniciando procesos (Dentro del query validar que no se tenga process ID)
                $row['idsolicitud_c'] = $numeroDeFolio;

                //Carga Promotor
                $prom = BeanFactory::getBean('Users');
                $prom->retrieve($bean->user_id_c);
                $row['promotor_leasing'] = $prom->user_name;
                //Cuenta
                $row['account_id'] = $bean->id;
                $row['idcliente_c'] = $bean->idcliente_c;
                $row['nombre_cliente'] = $bean->name;
                $row['correo'] = $bean->email1;
                $row['riesgo'] = $bean->riesgo_c;
                $row['tipo_persona'] = $bean->tipodepersona_c;
                $row['lista_negra_c'] = $bean->lista_negra_c;
                $row['pep_c'] = $bean->pep_c;
                //FIN DE PARAMETROS


                //$GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . "CZ: parametros para UNI2-01 antes de obtenSolicitud: " . print_r($row, 1));
                $solicitudCreditoResultado = $callApi->obtenSolicitudCredito($row);
                $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : solicitudCreditoResultado " . print_r($solicitudCreditoResultado, 1));

                //Actualiza la operacion con el id de solicitud y pon el estatus en Integracion de expediente
                $opp = BeanFactory::getBean('Opportunities');
                $opp->retrieve($row['opportunity_id']);
                $opp->estatus_c = 'P';
                $opp->idsolicitud_c = $solicitudCreditoResultado['idSolicitud'];
                $opp->save();

                $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Opportunity({$opp->id}) changed to Integracion de Expediente OP");
            } //while
        } catch (Exception $e) {
            error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
            $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
        }
    }


    public function liberaciondeLista($bean = null, $event = null, $args = null)
    {
        global $current_user;
        $callApi = new UnifinAPI();
        // ** jsr ** inicio
        if (($bean->id_process_c == 0 || $bean->id_process_c == null || $bean->id_process_c == "") && ($bean->lista_negra_c == 1 || $bean->pep_c == 1)) {
            $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : ** JSR ** if a ejecutar OFICIAL DE CUMPLIMIENTO   " . ": $bean->id_process_c ");
            $liberacion = $callApi->liberacionLista($bean->id, $bean->lista_negra_c, $bean->pep_c, $bean->idprospecto_c, $bean->tipo_registro_cuenta_c, $current_user->user_name, $bean->name);
            $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Inicia OFICIAL DE CUMPLIMIENTO   " . ": $liberacion ");
        }
        // ** jsr ** fin
    }

    /* CVV INICIO**/
    public function clienteCompleto($bean = null, $event = null, $args = null)
    {

        /*** ALI INICIO ***/
        if (($bean->canal_c == 1 || (!empty($bean->id_uniclick_c) && !empty($bean->idcliente_c))) && $bean->sincronizado_unics_c == 0) {
            $GLOBALS['log']->fatal("1");
            $callApi = new UnifinAPI();
            $cliente = $callApi->InsertaPersona($bean);
            $this->emailChangetoUnics($bean);
        } else {
            //Tipo Cuenta = 4 - Persona  2 - Prospecto
            if (($bean->tipo_registro_cuenta_c != '4' || ($bean->tipo_registro_cuenta_c == '4' && $bean->tipo_relacion_c != "")) && $bean->sincronizado_unics_c == 0) {

                if ($bean->estatus_c == 'Interesado' || ($bean->tipo_registro_cuenta_c != '2' && $_SESSION['estadoPersona'] == 'insertando')) {
                    $callApi = new UnifinAPI();
                    $cliente = $callApi->insertarClienteCompleto($bean);
                }
            }
            /**
             * F. Javier G. Solar 12/07/2018
             * Se realiza el proceso de envió de cuenta al sistema UNICS, solo si no se ha
             *realizado.
             *Conservar los campos que sean obligatorios de acuerdo a la opción
             *seleccionada
             */ // Tipo Cuenta 2-Prospecto, subTipo Cuenta 7-Interesado
            if ((($bean->esproveedor_c || $bean->cedente_factor_c || $bean->deudor_factor_c) || ($bean->tipo_registro_cuenta_c == "2" && $bean->subtipo_registro_cuenta_c == "7")) && $bean->sincronizado_unics_c == 0 && !empty($bean->idcliente_c)) {
                $callApi = new UnifinAPI();
                $cliente = $callApi->insertarClienteCompleto($bean);
            }
        }
        //  $this->actualizaOportunidadProspecto($bean);
        /*** ALI FIN ***/
    }

    /**
     * Cuando se graba un prospecto como Seguimiento Futuro, requerira una fecha y hora y creara una cita en el calendario para darle seguimiento al cliente
     * @param Account $bean
     * @param Array $event
     * @param Array $args
     */
    public function seguimiento_futuro($bean = null, $event = null, $args = null)
    {
        global $current_user;
        if (
            $bean->estatus_c != 'Seguimiento Futuro' || empty($bean->seguimiento_futuro_c)
            || $bean->seguimiento_futuro_c == $bean->fetched_row['seguimiento_futuro_c']
        ) {
            return null;
        }
        try {
            if ($bean->tiposeguimiento_c == 'Reunion') {
                $meeting = BeanFactory::getBean('Meetings');
                $meeting->date_start = $bean->seguimiento_futuro_c;
                $meeting->date_end = $bean->seguimiento_futuro_c;
                $meeting->parent_type = 'Accounts';
                $meeting->parent_id = $bean->id;
                $meeting->duration_minutes = '15';
                $meeting->reminder_time = 15;
                $meeting->assigned_user_id = $bean->assigned_user_id;
                $meeting->name = $bean->name . " (Seguimiento Futuro)";
                $meeting->description = $bean->descripciondetarea_c;
                $meeting->save();
            }
            if ($bean->tiposeguimiento_c == 'Llamada') {
                $call = BeanFactory::getBean('Calls');
                $call->date_start = $bean->seguimiento_futuro_c;
                $call->date_end = $bean->seguimiento_futuro_c;
                $call->parent_type = 'Accounts';
                $call->parent_id = $bean->id;
                $call->duration_minutes = '15';
                $call->reminder_time = 15;
                $call->assigned_user_id = $bean->assigned_user_id;
                $call->name = $bean->name . " (Seguimiento Futuro)";
                $call->description = $bean->descripciondetarea_c;
                $call->save();
            }
            if ($bean->tiposeguimiento_c == 'Tarea') {
                $task = BeanFactory::getBean('Tasks');
                $task->date_start = $bean->seguimiento_futuro_c;
                $task->date_due = $bean->seguimiento_futuro_c;
                $task->parent_type = 'Accounts';
                $task->parent_id = $bean->id;
                $task->assigned_user_id = $bean->assigned_user_id;
                $task->name = $bean->name . " (Seguimiento Futuro)";
                $task->description = $bean->descripciondetarea_c;
                $task->save();
            }
        } catch (Exception $e) {
            error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error: " . $e->getMessage());
            $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
        }

        $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : SEGUIMIENTO FUTURO: " . print_r($meeting, 1));
    }

    public function textToUppperCase($bean = null, $event = null, $args = null)
    {
        if( isset($_REQUEST['module']) ){

            if ($_REQUEST['module'] != 'Import') {
                foreach ($bean as $field => $value) {
                    if ($bean->field_defs[$field]['type'] == 'varchar' && $field != 'encodedkey_mambu_c' && $field != 'path_img_qr_c' && $field != 'salesforce_id_c') {
                        $value = mb_strtoupper($value, "UTF-8");
                        $bean->$field = $value;
                    }
                    if ($bean->field_defs[$field]['name'] == 'name') {
                        $value = mb_strtoupper($value, "UTF-8");
                        $bean->$field = $value;
                    }
                }
            }
        }
    }

    /** BEGIN CUSTOMIZATION: jgarcia@levementum.com 6/19/2015 Description: Logic hook to call WS Actualiza persona en UNICS*/
    /*** CVV INCIO **/
    public function actualizaPersona($bean = null, $event = null, $args = null)
    {
        global $current_user;
        global $db;
        try {
            $GLOBALS['log']->fatal(" <" . $current_user->user_name . "> :El estatus en sesion al actualizar Persona es:" . $_SESSION['estadoPersona']);
            /*
              AF - 2018/11/01
              Modica condición para  actualizar  registros que tengan idCliente y Leads que sean relación de otras cuentas
            */
            //no se debe disparar si es lead
            if (!empty($bean->idcliente_c) && $_SESSION['estadoPersona'] == 'actualizando') {
                $callApi = new UnifinAPI();

                //Evalua si envía lead
                $relacionado = 0;
                if ($bean->tipo_registro_cuenta_c == '1') { //Tipo Cuenta 1-Lead
                    // Consulta si tiene relación
                    $query = "select id_c from rel_relaciones_cstm
                          where account_id1_c ='" . $bean->id . "'
                          limit 1;";
                    $queryResult = $db->query($query);
                    //Si tiene relaciones envia petición
                    while ($row = $db->fetchByAssoc($queryResult)) {
                        $relacionado = 1;
                    }
                }
                //CVV - valida que la persona ya se encuentre sincornizada con UNICS, de lo contrario manda a insertar completo
                if ($bean->sincronizado_unics_c == 1) {
                    if (($bean->tipo_registro_cuenta_c == '1' && ($relacionado == 1 || $bean->esproveedor_c || $bean->deudor_factor_c)) || $bean->tipo_registro_cuenta_c != '1') {
                        $Actualizacliente = $callApi->actualizaPersonaUNICS($bean);
                        $this->emailChangetoUnics($bean);
                    }
                } else {
                    $Actualizacliente = $callApi->insertarClienteCompleto($bean);
                }
            }
        } catch (Exception $e) {
            error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error: " . $e->getMessage());
            $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
        }
    }

    /*** CVV FIN **/
    public function insertaPLDUNICS($bean = null, $event = null, $args = null)
    {
        global $current_user;
        try {
            if (!empty($bean->idcliente_c) && ($bean->tipo_registro_cuenta_c == '3' || $bean->tipo_registro_cuenta_c == '4')) { //Tipo Cuenta 3-Cliente, 4-Persona
                $callApi = new UnifinAPI();
                $PLD = $callApi->insertaPLD($bean, $_SESSION['estadoPersona']);
                $_SESSION['estadoPersona'] = '';
            }
        } catch (Exception $e) {
            error_log(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error: " . $e->getMessage());
            $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Error " . $e->getMessage());
        }
    }

    public function account_contacts(&$bean, $event, $args)
    {
        if ($bean->tipodepersona_c == 'Persona Moral') {
            $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " bean->account_contacts :  " . print_r($bean->account_contacts, true));

            if ($bean->account_contacts != null && $bean->account_contacts != '') {
                foreach ($bean->account_contacts as $contact_row) {
                    if ($contact_row['primerNombre'] != null) {
                        $contactoRel = BeanFactory::getBean('Accounts');
                        $contactoRel->tipodepersona_c = "Persona Fisica";
                        $contactoRel->primernombre_c = $contact_row['primerNombre'] . ($contact_row['segundoNombre'] != "" ? " " . $contact_row['segundoNombre'] : "");
                        //$contactoRel->segundonombre_c = $contact_row['segundoNombre'];
                        $contactoRel->apellidopaterno_c = $contact_row['apellidoPaterno'];
                        $contactoRel->apellidomaterno_c = $contact_row['apellidoMaterno'];
                        $contactoRel->email1 = $contact_row['emailContacto'];
                        $contactoRel->tipo_registro_cuenta_c = "4"; //Tipo cuenta 4-Persona
                        $contactoRel->tipo_relacion_c = "^Contacto^";
                        $contactoRel->assigned_user_id = $bean->assigned_user_id;
                        $contactoRel->team_set_id = $bean->team_set_id;
                        $contactoRel->team_id = $bean->team_id;
                        $contactoRel->promotorcredit_c = $bean->promotorcredit_c;
                        $contactoRel->promotorfactoraje_c = $bean->promotorfactoraje_c;
                        $contactoRel->promotorleasing_c = $bean->promotorleasing_c;

                        $contactoRel->save();
                        $Rel = BeanFactory::getBean('Rel_Relaciones');
                        $Rel->account_id1_c = $contactoRel->id;
                        $Rel->rel_relaciones_accounts_1accounts_ida = $bean->id;
                        $Rel->rel_relaciones_accountsaccounts_ida = $bean->id;
                        $Rel->name = "Temp";
                        $Rel->relaciones_activas = "^Contacto^";
                        $Rel->tipodecontacto = "^Promocion^";
                        $Rel->assigned_user_id = $bean->assigned_user_id;
                        $Rel->team_set_id = $bean->team_set_id;
                        $Rel->team_id = $bean->team_id;
                        $Rel->save();

                        if (!empty($contact_row['telefonoContacto'])) {
                            $telefono = BeanFactory::getBean('Tel_Telefonos');
                            $telefono->name = $contact_row['telefonoContacto'];
                            $telefono->telefono = $contact_row['telefonoContacto'];
                            $telefono->accounts_tel_telefonos_1accounts_ida = $contactoRel->id;
                            $telefono->tipotelefono = 1;
                            $telefono->assigned_user_id = $bean->assigned_user_id;
                            $telefono->team_set_id = $bean->team_set_id;
                            $telefono->team_id = $bean->team_id;
                            //$GLOBALS['log']->fatal('>>>>llamada desde account_contacts<<<<<<<');
                            $telefono->save();
                        }
                    }
                }
            }
        }
    }

    /*
     * @author Carlos Zaragoza Ortiz
     * @brief Envia lista de correos de un account al servicio de UNICs
     *
     * */
    public function emailChangetoUnics($bean)
    {
        global $current_user, $db;
        $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Entra a email");
        //Este es el correo anterior $bean->email1
        $GLOBALS['log']->fatal(" <" . $current_user->user_name . "> : bean correo", $bean->email1);
        //$GLOBALS['log']->fatal(" <".$current_user->user_name."> : bean", print_r($bean,true));

        $query = <<<SQL
select ac.id_c, ac.idcliente_c, e.id, e.email_address,  er.deleted from email_addr_bean_rel er, email_addresses e, accounts_cstm ac
where er.bean_id = '{$bean->id}'  and er.email_address_id = e.id and ac.id_c = er.bean_id;
SQL;
        $queryResult = $db->query($query);
        //$header = $db->getOne($query);
        while ($row = $db->fetchByAssoc($queryResult)) {
            $id = $row['id'];
            $cliente = $row['idcliente_c'];
            $correo[] = array(
                "GuidMail" => $row['id'],
                "Email" => $row['email_address'],
                "Estado" => $row['deleted'],

            );
        }
        $correos['GuidCliente'] = $id;
        $correos['IdCliente'] = $cliente;
        $correos['Emails'] = $correo;
        $main['oCorreos'] = $correos;
        $GLOBALS['log']->fatal(" <" . $current_user->user_name . "> : bean addresses", print_r($correos, true));

        $callApi = new UnifinAPI();
        if ($correos['GuidCliente'] != null) {
            $resultado = $callApi->correosDeCliente($main);

            $GLOBALS['log']->fatal(" <" . $current_user->user_name . "> : RESULTADO", print_r($resultado, true));
        }
    }

    /** @var Account $bean */
    public function rfcDuplicate($bean, $event, $args)
    {
        global $db, $current_user;
        if ($bean->tipodepersona_c == 'Persona Moral') {
            $query = "Select count(*) as duplicados from accounts_cstm
where rfc_c = '{$bean->rfc_c}' and
 razonsocial_c = '$bean->razonsocial_c' AND
 id_c <> '{$bean->id}'";
        } else {
            $query = "Select count(*) as duplicados from accounts_cstm
where rfc_c = '{$bean->rfc_c}' and
 primernombre_c = '$bean->primernombre_c' AND
 segundonombre_c = '$bean->segundonombre_c' AND
 apellidopaterno_c = '$bean->apellidopaterno_c' AND
 apellidomaterno_c = '$bean->apellidomaterno_c' AND
 id_c <> '{$bean->id}'";
        }

        $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Consulta RFC " . $query);
        $queryResult = $db->getOne($query);
        $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : Duplicados " . $queryResult);
        if ($queryResult > 0) {
            require_once 'include/api/SugarApiException.php';
            throw new SugarApiExceptionInvalidParameter("Ya existe una persona registrada con mismo nombre y RFC.");
        } else {
            $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : No hay duplicados " . $queryResult);
        }
    }

    public function GeneraReferenciaBancaria($idCliente)
    {
        $sum = 0;
        $digits = str_split($idCliente);
        for ($i = 0; $i < strlen($idCliente); $i++) {
            if ((strlen($idCliente) - ($i + 1)) % 2 == 0) {
                $sum = $sum + ($digits[$i] * 2 > 9 ? ($digits[$i] * 2) - 9 : $digits[$i] * 2);
            } else {
                $sum = $sum + $digits[$i];
            }
        }
        $sum = 10 - $sum % 10;
        return $idCliente . ($sum % 10);
    }

    public function crearFolioRelacion($bean = null, $event = null, $args = null)
    {
        global $db;
        $idCuenta = $bean->id;
        $query = "select id_c from rel_relaciones_cstm where account_id1_c = '$idCuenta'";
        $result = $db->query($query);
        $row = $db->fetchByAssoc($result);
        if ($row) {
            if ($bean->idcliente_c == '' || $bean->idcliente_c == '0') {
                $callApi = new UnifinAPI();
                $numeroDeFolio = $callApi->generarFolios(1, $bean);
                $bean->idcliente_c = $numeroDeFolio;
            }
        }
    }

    public function relacion2UNICS($bean = null, $event = null, $args = null)
    {
        global $db;
        $idCuenta = $bean->id;
        $query = "select id_c from rel_relaciones_cstm where account_id1_c = '$idCuenta'";
        $result = $db->query($query);
        $row = $db->fetchByAssoc($result);
        if ($row) {
            if ($bean->sincronizado_unics_c == 0) {
                $callApi = new UnifinAPI();
                $cliente = $callApi->insertarClienteCompleto($bean);
            }
        }
    }

    public function creaResumen($bean = null, $event = null, $args = null)
    {
        global $db;
        $idCuenta = $bean->id;
        $GLOBALS['log']->fatal('Entra a Crear Resumen de Account ');
        $bean_Resumen = BeanFactory::retrieveBean('tct02_Resumen', $idCuenta);

        if ($bean_Resumen == null || empty($bean_Resumen)) {
            // global $app_list_strings, $current_user; //Obtención de listas de valores
            // $tipo = $app_list_strings['tipo_registro_list']; //obtencion lista tipo de registro
            // $subtipo = $app_list_strings['subtipo_cuenta_list'];  //Obtiene lista de los subtipos de cuenta
            // $etitipo= $tipo[$bean->tipo_registro_cuenta_c];      //Obtiene el valor del campo obtenido de la lista con Etiqueta
            // $etisubtipo= $subtipo[$bean->subtipo_registro_cuenta_c]; //Obtiene el valor del campo obtenido de la lista con Etiqueta

            $GLOBALS['log']->fatal('Entra a condición para crear Resumen');
            $bean_Resumen = BeanFactory::newBean('tct02_Resumen');
            $bean_Resumen->new_with_id = true;
            $bean_Resumen->name = $bean->name;
            $bean_Resumen->id = $idCuenta;

            //Setea valores para los campos por producto (leasing, factoraje y CA en tipo y subtipo).
            //LEASING
            // $bean_Resumen->tct_tipo_l_txf_c= $bean->tipo_registro_c;
            // $bean_Resumen->tct_subtipo_l_txf_c=$bean->subtipo_cuenta_c;
            // $bean_Resumen->tct_tipo_cuenta_l_c= mb_strtoupper(trim($etitipo.' '.$etisubtipo));
            //FACTORAJE
            // $bean_Resumen->tct_tipo_f_txf_c= $bean->tipo_registro_c;
            // $bean_Resumen->tct_subtipo_f_txf_c=$bean->subtipo_cuenta_c;
            // $bean_Resumen->tct_tipo_cuenta_f_c= mb_strtoupper(trim($etitipo.' '.$etisubtipo));
            //CREDITO AUTOMOTRIZ
            // $bean_Resumen->tct_tipo_ca_txf_c= $bean->tipo_registro_c;
            // $bean_Resumen->tct_subtipo_ca_txf_c=$bean->subtipo_cuenta_c;
            // $bean_Resumen->tct_tipo_cuenta_ca_c= mb_strtoupper(trim($etitipo.' '.$etisubtipo));
            //FLEET
            // $bean_Resumen->tct_tipo_fl_txf_c= $bean->tipo_registro_c;
            // $bean_Resumen->tct_subtipo_fl_txf_c=$bean->subtipo_cuenta_c;
            // $bean_Resumen->tct_tipo_cuenta_fl_c= mb_strtoupper(trim($etitipo.' '.$etisubtipo));
            //UNICLICK
            // $bean_Resumen->tct_tipo_uc_txf_c= $bean->tipo_registro_c;
            // $bean_Resumen->tct_subtipo_uc_txf_c=$bean->subtipo_cuenta_c;
            // $bean_Resumen->tct_tipo_cuenta_uc_c= mb_strtoupper(trim($etitipo.' '.$etisubtipo));

            //Evalua tipo de cuenta
            // if ($bean->tipo_registro_c == 'Prospecto' && $bean->subtipo_cuenta_c == 'Integracion de Expediente' ) {
            //     //Setea valores para los campos por producto (leasing, factoraje y CA en tipo y subtipo).
            //     //LEASING
            //     $bean_Resumen->tct_tipo_l_txf_c= 'Lead';
            //     $bean_Resumen->tct_subtipo_l_txf_c= 'En Calificacion';
            //     $bean_Resumen->tct_tipo_cuenta_l_c= 'LEAD EN CALIFICACIÓN';
            //     //FACTORAJE
            //     $bean_Resumen->tct_tipo_f_txf_c= 'Lead';
            //     $bean_Resumen->tct_subtipo_f_txf_c='En Calificacion';
            //     $bean_Resumen->tct_tipo_cuenta_f_c= 'LEAD EN CALIFICACIÓN';
            //     //FLEET
            //     $bean_Resumen->tct_tipo_fl_txf_c= 'Lead';
            //     $bean_Resumen->tct_subtipo_fl_txf_c='En Calificacion';
            //     $bean_Resumen->tct_tipo_cuenta_fl_c= 'LEAD EN CALIFICACIÓN';
            //     //UNICLICK
            //     $bean_Resumen->tct_tipo_uc_txf_c= 'Lead';
            //     $bean_Resumen->tct_subtipo_uc_txf_c='En Calificacion';
            //     $bean_Resumen->tct_tipo_cuenta_uc_c= 'LEAD EN CALIFICACIÓN';
            // }
            //GUARDA REGISTRO DE RESUMEN
            $bean_Resumen->save();
        }
        $GLOBALS['log']->fatal('Finaliza y crea Resumen para vista 360');
    }

    //Función para validar que una cuenta tenga asesor asignado, si no tiene usuario establece 9.-Sin gestor
    /*
      Condiciones:
      Tipo =  Prospecto
      Asesor L,CA, F = Vacío
    */
    public function valida_asesor($bean = null, $event = null, $args = null)
    {
        // $GLOBALS['log']->fatal('L- '. $bean->user_id_c . ' - '. $bean->promotorleasing_c);
        // $GLOBALS['log']->fatal('F- '. $bean->user_id1_c . ' - '. $bean->promotorfactoraje_c);
        // $GLOBALS['log']->fatal('C- '. $bean->user_id2_c . ' - '. $bean->promotorcredit_c);

        $idSinGestor = '569246c7-da62-4664-ef2a-5628f649537e';
        //Valida Cuenta tipo promotor
        if ($bean->tipo_registro_cuenta_c == '2') { //2-Prospecto
            //Valida promotor Leasing
            if ((empty($bean->user_id_c) || $bean->user_id_c == "") && empty($bean->promotorleasing_c)) {
                $bean->user_id_c = $idSinGestor;
            }
            //Valida promotor Factoraje
            if ((empty($bean->user_id1_c) || $bean->user_id1_c == "") && empty($bean->promotorfactoraje_c)) {
                $bean->user_id1_c = $idSinGestor;
            }
            //Valida promotor CA
            if ((empty($bean->user_id2_c) || $bean->user_id2_c == "") && empty($bean->promotorcredit_c)) {
                $bean->user_id2_c = $idSinGestor;
            }
            //Valida promotor Fleet
            if ((empty($bean->user_id6_c) || $bean->user_id6_c == "") && empty($bean->promotorfleet_c)) {
                $bean->user_id6_c = $idSinGestor;
            }
            //Valida promotor Uniclick
            if ((empty($bean->user_id7_c) || $bean->user_id7_c == "") && empty($bean->promotoruniclick_c)) {
                $bean->user_id7_c = $idSinGestor;
            }
        }
    }

    public function guardapotencial($bean = null, $event = null, $args = null)
    {

        global $db;
        $idCuenta = $bean->id;
        //$GLOBALS['log']->fatal('Entra a guardar autos Potencial en Resumen');
        $PotencialAutos = json_decode($bean->potencial_autos);
        //Recupera el bean de tct02_resumen
        $bean_Resumen = BeanFactory::retrieveBean('tct02_Resumen', $idCuenta);
        //$GLOBALS['log']->fatal(print_r($PotencialAutos,true));
        //$GLOBALS['log']->fatal(print_r($bean->potencial_autos,true));

        if (!empty($bean_Resumen) && !empty($PotencialAutos)) {
            //$GLOBALS['log']->fatal('Entra Resumen OK ');
            //$GLOBALS['log']->fatal($PotencialAutos->autos->tct_no_autos_u_int_c);
            //Setea los valores en los campos de tct02_resumen
            $bean_Resumen->tct_no_autos_u_int_c = $PotencialAutos->autos->tct_no_autos_u_int_c;
            $bean_Resumen->tct_no_autos_e_int_c = $PotencialAutos->autos->tct_no_autos_e_int_c;
            $bean_Resumen->tct_no_motos_int_c = $PotencialAutos->autos->tct_no_motos_int_c;
            $bean_Resumen->tct_no_camiones_int_c = $PotencialAutos->autos->tct_no_camiones_int_c;

            //Guardar registro
            $bean_Resumen->save();
        }
    }

    public function quitaespacios($bean = null, $event = null, $args = null)
    {

        global $db;
        global $app_list_strings, $current_user; //Obtención de listas de valores
        $idCuenta = $bean->id;
        //$GLOBALS['log']->fatal('Limpia espacios');
        //Se crean variables que limpien los excesos de espacios en los campos establecidos.
        $limpianame = preg_replace('/\s\s+/', ' ', $bean->name);
        $limpianombre = preg_replace('/\s\s+/', ' ', $bean->primernombre_c);
        $limpiaapaterno = preg_replace('/\s\s+/', ' ', $bean->apellidopaterno_c);
        $limpiamaterno = preg_replace('/\s\s+/', ' ', $bean->apellidomaterno_c);
        $limpiarazon = preg_replace('/\s\s+/', ' ', $bean->razonsocial_c);
        $limpianomcomercial = preg_replace('/\s\s+/', ' ', $bean->nombre_comercial_c);

        //Actualiza valores limpios a los campos de la Cuenta
        $bean->name = $limpianame;
        $bean->primernombre_c = $limpianombre;
        $bean->apellidopaterno_c = $limpiaapaterno;
        $bean->apellidomaterno_c = $limpiamaterno;
        $bean->razonsocial_c = $limpiarazon;
        $bean->nombre_comercial_c = $limpianomcomercial;
        if ($bean->tipodepersona_c == "Persona Moral") {
            $bean->name = $bean->razonsocial_c;
        }

        //Consumir servicio de cleanName, declarado en custom api
        require_once("custom/clients/base/api/cleanName.php");
        $apiCleanName= new cleanName();
        $body=array('name'=>$bean->name);
        $response=$apiCleanName->getCleanName(null,$body);
        if ($response['status']=='200') {
            $bean->clean_name = $response['cleanName'];
        }
    }

    public function asignaSinGestor($bean = null, $event = null, $args = null)
    {

        $idSinGestor = '569246c7-da62-4664-ef2a-5628f649537e';

        //Promotor Leasing
        if (($bean->user_id_c == null || $bean->user_id_c == "") && empty($bean->promotorleasing_c)) {
            $bean->user_id_c = $idSinGestor;
        }

        //Promotor Factoraje
        if (($bean->user_id1_c == null || $bean->user_id1_c == "") && empty($bean->promotorfactoraje_c)) {
            $bean->user_id1_c = $idSinGestor;
        }

        //Promotor CA
        if (($bean->user_id2_c == null || $bean->user_id2_c == "") && empty($bean->promotorcredit_c)) {
            $bean->user_id2_c = $idSinGestor;
        }

        //Promotor Fleet
        if (($bean->user_id6_c == null || $bean->user_id6_c == "") && empty($bean->promotorfleet_c)) {
            $bean->user_id6_c = $idSinGestor;
        }

        //Promotor Uniclick
        if (($bean->user_id7_c == null || $bean->user_id7_c == "") && empty($bean->promotoruniclick_c)) {
            $bean->user_id7_c = $idSinGestor;
        }
    }

    public function idUniclick($bean = null, $event = null, $args = null)
    {
        //Valida que no exista id uniclick duplicado
        global $db;
        if (!empty($bean->id_uniclick_c) && $bean->id != "") {
            //Consulta id_uniclick_c
            $query = "SELECT id_c, id_uniclick_c FROM accounts_cstm
            inner join accounts on id=id_c
            WHERE id_c != '{$bean->id}' and id_uniclick_c = '{$bean->id_uniclick_c}' and deleted=0";
            //Ejecuta consulta
            $queryResult = $db->query($query);
            while ($row = $db->fetchByAssoc($queryResult)) {
                require_once 'include/api/SugarApiException.php';
                throw new SugarApiExceptionInvalidParameter("Ya existe una cuenta registrada con el mismo Id Cliente Uniclick");
            }
        }
    }

    public function NuevoAnalizate($bean = null, $event = null, $args = null)
    {
        //Se ejecuta para creación de nuevos registros:
        /*
        * Tipo de cuenta = Proveedor
        * o Es Proveedor = True
        */
        if (!$args['isUpdate'] && $bean->email1 != "" && ($bean->tipo_registro_cuenta_c == "5" || $bean->esproveedor_c == 1)) { //Tipo Cuenta 5-Proveedor
            $GLOBALS['log']->fatal('Genera Registro Analizate: Crear - Nuevo Proveedor');
            $this->RegistroAnalizate($bean);
        }
    }

    public function EditaAnalizate($bean = null, $event = null, $args = null)
    {
        //Se ejecuta para edición de registros existentes:
        /*
      * Se actualiza Es Proveedor = True
      */
        if ($args['isUpdate'] && $bean->fetched_row['esproveedor_c'] == 0 && $bean->esproveedor_c == 1 && $bean->email1 != "") {
            $GLOBALS['log']->fatal('Genera Registro Analizate: Edita - Nuevo Proveedor');
            $this->RegistroAnalizate($bean);
        }
    }

    public function RegistroAnalizate($bean = null, $event = null, $args = null)
    {
        //Crea nuevo bean Analizate (registro) y la relacion con acccounts (registro creado).
        $url_portalFinanciera = '&UUID=' . base64_encode($bean->id) . '&RFC_CIEC=' . base64_encode($bean->rfc_c). '&MAIL=' . base64_encode($bean->email1);
        $relacion = BeanFactory::newBean('ANLZT_analizate');
        $relacion->anlzt_analizate_accountsaccounts_ida = $bean->id;
        $relacion->empresa = 1;
        $relacion->estado = 1;
        $relacion->tipo = 1;
        $relacion->fecha_actualizacion = $bean->date_modified; //Cambiar a fecha actual
        $relacion->url_portal = $url_portalFinanciera;
        $relacion->assigned_user_id = $bean->user_id_c;
        $relacion->tipo_registro_cuenta_c = "5";
        $relacion->load_relationship('anlzt_analizate_accounts');
        $relacion->anlzt_analizate_accounts->add($bean->id);
        $relacion->save();
    }

    public function NuevaCuentaProductos($bean = null, $event = null, $args = null)
    {
        //Se ejecuta para creación productos para nuevos registros(cuentas):
        /* Dev: Erick de JEsus
        * Tipo de cuenta = Todas
        */
        //Sólo se ejecuta en la creación
        if (!$args['isUpdate']) {
            //Declara variables para generación de registros
            global $current_user,$app_list_strings,$db;
            $beanprod = null;
            $idSinGestor = '569246c7-da62-4664-ef2a-5628f649537e';
            $module = 'uni_Productos';
            $key_productos = array('1', '4', '3', '6', '8', '10','14', '2');
            $name_productos = array('-LEASING', '-FACTORAJE', '-CRÉDITO AUTOMOTRIZ', '-FLEET', '-UNICLICK', '-SEGUROS','-TARJETA DE CRÉDITO','-CRÉDITO SIMPLRE');
            $count = count($name_productos);
            $current_prod = null;
            $fechaAsignaAsesor = date("Y-m-d"); //Fecha de Hoy
            //Validación temporal- Se debe quitar cuando el campo $bean->tipo_registro_c se elimine
            $tipoCuentaServicio = !empty($bean->tipo_registro_c) ? $bean->tipo_registro_c : 'Prospecto';
            $bean->tipo_registro_cuenta_c = ($tipoCuentaServicio == 'Persona') ? '4' : $bean->tipo_registro_cuenta_c;
            $bean->tipo_registro_cuenta_c = ($tipoCuentaServicio == 'Proveedor') ? '5' : $bean->tipo_registro_cuenta_c;
            $tipo = $app_list_strings['tipo_registro_cuenta_list'];
            $subtipo = $app_list_strings['subtipo_registro_cuenta_list'];
            $etitipo = $tipo[$bean->tipo_registro_cuenta_c];
            $etisubtipo = $subtipo[$bean->subtipo_registro_cuenta_c];
            //OBTIENE EL CHECK DE ALTA CLIENTE DEL USUARIO LOGEADO
            $bean_user = BeanFactory::retrieveBean('Users', $current_user->id, array('disable_row_level_security' => true));
            if (!empty($bean_user)) {
                $user_alta_clientes = $bean_user->tct_alta_clientes_chk_c;
            }

            for ($i = 0; $i < $count; $i++) {
                //$GLOBALS['log']->fatal($current_prod);
                $beanprod = BeanFactory::newBean($module);
                $beanprod->name = $bean->name . $name_productos[$i];
                $beanprod->tipo_producto = $key_productos[$i];
                $beanprod->fecha_asignacion_c = $fechaAsignaAsesor;
                $beanprod->tipo_cuenta = empty($bean->tipo_registro_cuenta_c) ? '2' : $bean->tipo_registro_cuenta_c;
                $beanprod->subtipo_cuenta = (empty($bean->subtipo_registro_cuenta_c) && $beanprod->tipo_cuenta == '2') ? '1' : $bean->subtipo_registro_cuenta_c;
                $beanprod->tipo_subtipo_cuenta = mb_strtoupper(trim($etitipo . ' ' . $etisubtipo));
                //Caso especial: Alta portal CA
                if ($bean->user_id2_c != $idSinGestor && $beanprod->tipo_producto == '3' && empty($bean->id_uniclick_c) && $bean->tipo_registro_cuenta_c != '4' && $bean->tipo_registro_cuenta_c != '5' && $GLOBALS['service']->platform != 'base' && $GLOBALS['service']->platform != 'mobile') {
                    $beanprod->tipo_cuenta = "2"; //2-Prospecto
                    $beanprod->subtipo_cuenta = "8"; //Integración de expediente
                    $beanprod->tipo_subtipo_cuenta = "PROSPECTO INTEGRACIÓN DE EXPEDIENTE";
                    //Actualiza campo general
                    $update = "update accounts_cstm set
                      tipo_registro_cuenta_c='2', subtipo_registro_cuenta_c ='8', tct_tipo_subtipo_txf_c='PROSPECTO INTEGRACIÓN DE EXPEDIENTE'
                      where id_c = '{$bean->id}'";
                    $updateExecute = $db->query($update);
                }
                //Caso especial: Alta por sistema 3ro como tipo Lead, se convierte a Prospecto sin contactar
                if ($bean->tipo_registro_cuenta_c == '1'  && $key_productos[$i]!='8' ) {
                    $beanprod->tipo_cuenta = "2"; //2-Prospecto
                    $beanprod->subtipo_cuenta = "1"; //Sin Contactar
                    $beanprod->tipo_subtipo_cuenta = "PROSPECTO SIN CONTACTAR";
                    //Actualiza campo general
                    $update = "update accounts_cstm set
                      tipo_registro_cuenta_c='2', subtipo_registro_cuenta_c ='1', tct_tipo_subtipo_txf_c='PROSPECTO SIN CONTACTAR'
                      where id_c = '{$bean->id}'";
                    $updateExecute = $db->query($update);
                }
                //CASO ESPECIAL DE USUARIOS CON EL CHECK ACTIVO DE ALTA CLIENTES
                if ($user_alta_clientes == true) {
                    if ($key_productos[$i] != '1') { //SI ES DIFERENTE DEL PRODUCTO LEASING
                        $beanprod->tipo_cuenta = "4"; //4-Persona
                        $beanprod->subtipo_cuenta = ""; //Vacio
                        $beanprod->tipo_subtipo_cuenta = "PERSONA";

                    } else {
                        //SI ES PRODUCTO LEASING
                        $beanprod->tipo_cuenta = "3"; //3-Cliente
                        $beanprod->subtipo_cuenta = "11"; //11-Venta Activo
                        $beanprod->tipo_subtipo_cuenta = "CLIENTE VENTA ACTIVO";
                    }
                }

                //Asignación de usuario
                switch ($key_productos[$i]) {
                    case '1': //Leasing
                        $beanprod->assigned_user_id = $bean->user_id_c;
                        break;
                    case '4': //Factoraje
                        $beanprod->assigned_user_id = $bean->user_id1_c;
                        break;
                    case '3': //Credito-Automotriz
                        $beanprod->assigned_user_id = $bean->user_id2_c;
                        break;
                    case '6': //Fleet
                        $beanprod->assigned_user_id = $bean->user_id6_c;
                        break;
                    case '7': //Credito-SOS
                        $beanprod->assigned_user_id = $bean->user_id_c;
                        break;
                    case '8': //Uniclick
                        $beanprod->canal_c = 0;
                        $beanprod->assigned_user_id = $bean->user_id7_c;
                        break;
                    case '9': //Unilease
                        $beanprod->assigned_user_id = $bean->user_id7_c;
                        break;
                    case '10': //Seguros
                        $beanprod->assigned_user_id = '1';
                        $beanprod->estatus_atencion = '2';
                        $beanprod->status_management_c = '6';
                        break;
                    case '2': //Credito Simple
                        $beanprod->assigned_user_id = $bean->user_id_c;
                        break;
                    case '12': //Credito Revolvente
                        $beanprod->assigned_user_id = $bean->user_id7_c;
                        break;
                }
                //Guarda registro y vincula a cuenta
                $beanprod->save();
                $bean->load_relationship('accounts_uni_productos_1');
                $bean->accounts_uni_productos_1->add($beanprod->id);
                $beanprod = null;
            }
        }
    }

    public function set_csv_linea_vigente($bean = null, $event = null, $args = null)
    {
        //Se escribe en archivo csv únicamente cuando se ha cambiado el Tipo y Subtipo de Cuenta a Cliente Con Linea Vigente
        //Esta función se dispara a través de Proccess Author "Cliente con Línea" ****** Tipo-Cuenta: 2-Prospecto, 3-Cliente **** SubTipo-Cuenta: 18-Con linea vigente, 8-Integracion de expediente
        if(($bean->subtipo_registro_cuenta_c == '18' && $bean->tipo_registro_cuenta_c == '3' && $bean->fetched_row['subtipo_registro_cuenta_c'] != '18' && !$bean->conversion_gclid_c) || ($bean->subtipo_registro_cuenta_c == '10' && $bean->fetched_row['subtipo_registro_cuenta_c'] != '10' && !$bean->conversion_gclid_c)) {
            $GLOBALS['log']->fatal('------------ENTRA CONDICIÓN CLIENTE CON LINEA VIGENTE O RECHAZADO DISPARA DESDE PROCCESS AUTHOR------------');
            $conversion_name = 'Conv CRM';
            $email = $bean->email1;
            if ($bean->load_relationship('accounts_tel_telefonos_1')) {
                $tel_telefonos = $bean->accounts_tel_telefonos_1->getBeans();
                if (!empty($tel_telefonos)) {
                    foreach ($tel_telefonos as $tel) {
                        if (!empty($tel->id) && $tel->principal) $telefono = $tel->telefono;
                    }
                }
            }
            if ($bean->load_relationship('opportunities')) {
                $parametros = array('limit' => 1, 'orderby' => 'date_modified DESC', 'disable_row_level_security' => true);
                //Fetch related beans
                $opps_relacionadas = $bean->opportunities->getBeans($bean->id, $parametros);
                //Ordenarlas por fecha de modificación para obtener el valor de la línea de la solicitud que actuaalizó esta cuenta
                if (!empty($opps_relacionadas)) {
                    foreach ($opps_relacionadas as $opp) {
                        $conversion_value = $opp->monto_c;
                        $conversion_time = date('H:i:s',strtotime($opp->date_modified));
                    }
                }
            }
            $GLOBALS['log']->fatal('------------SE ESCRIBE EN CSV PARA SUBIR SFTP------------');
            date_default_timezone_set('America/Mexico_City');
            $ruta_archivo = "custom/plantillaCSV/leads_calidad.csv";
            if ($bean->subtipo_registro_cuenta_c == '10') $ruta_archivo = "custom/plantillaCSV/leads_no_calidad.csv";
            if (file_exists($ruta_archivo)) {
                $file = fopen($ruta_archivo, "a");
                fwrite($file, $email . ',' . $telefono . ',' . $conversion_name . ',' . $conversion_time . ',' . $conversion_value . ',MXN' . PHP_EOL);
                fclose($file);
            }
            //Actualiza Cuenta a conversión GCLID
            $bean->conversion_gclid_c = 1;
        }
    }

    public function set_account_mambu($bean = null, $event = null, $args = null)
    {
        $GLOBALS['log']->fatal('--------------MAMBU INICIA-----------------');

        global $sugar_config, $app_list_strings;
        $bank = $app_list_strings['banco_list'];
        //Cliente con Línea Vigente: 3,18
        if ($bean->subtipo_registro_cuenta_c == '18' && $bean->tipo_registro_cuenta_c == '3' && /*$bean->fetched_row['subtipo_registro_cuenta_c']!='18' &&*/
            $bean->encodedkey_mambu_c == "") {

            //variables para consumo de servicio
            $url = $sugar_config['url_mambu_gral'] . 'groups';
            $user = $sugar_config['user_mambu'];
            $pwd = $sugar_config['pwd_mambu'];
            $auth_encode = base64_encode($user . ':' . $pwd);

            //Se agrega validación para conocer si el Cliente ya existe en Mambu
            $url_check_client=$sugar_config['url_mambu_gral'] . 'groups:search?detailsLevel=FULL';

            $bodyCheckExists = array(
                "filterCriteria" => array(
                    array(
                        "field"=>"_referencias_crm._id_crm",
                        "operator"=>"EQUALS",
                        "value"=> $bean->id
                    )
                ),
            );

            $GLOBALS['log']->fatal('--------------MAMBU COMPRUEBA EXISTENCIA DE CLIENTE-----------------');
            $GLOBALS['log']->fatal(json_encode($bodyCheckExists));
            $callApiCheck = new UnifinAPI();
            $resultadoCheck = $callApiCheck->postMambu($url_check_client, $bodyCheckExists, $auth_encode);
            $GLOBALS['log']->fatal('--------------MAMBU COMPRUEBA EXISTENCIA CLIENTE RESPONSE-----------------');
            $GLOBALS['log']->fatal($resultadoCheck);
            if (!empty($resultadoCheck[0]['encodedKey'])) {

                $GLOBALS['log']->fatal('--------------MAMBU CLIENTE YA EXISTE EN MAMBU-----------------');
                $bean->encodedkey_mambu_c = $resultadoCheck[0]['encodedKey'];

            }else {
                $GLOBALS['log']->fatal('--------------MAMBU CLIENTE NO EXISTE EN MAMBU-----------------');
                $GLOBALS['log']->fatal('--------------INICIA CREACIÓN DE CLIENTE EN MAMBU-----------------');
                //variables para payload
                $id_crm = $bean->id;
                $nombre = '';
                $nombreaccount = $bean->primernombre_c . ' ' . $bean->apellidopaterno_c . ' ' . $bean->apellidomaterno_c;
                $razon_social = $bean->razonsocial_c;
                $id_cliente_corto = $bean->idcliente_c;
                //$id_cliente_corto='52597';
                //Condicion para determinar el valor de $nombre en el caso de regimen fiscal
                $nombre = $bean->tipodepersona_c != 'Persona Moral' ? $nombreaccount : $razon_social;

                //Obteniendo referencias bancarias
                $array_cta_bancaria = array();
                if ($bean->load_relationship('cta_cuentas_bancarias_accounts')) {
                    $ctas_bancarias = $bean->cta_cuentas_bancarias_accounts->getBeans();
                    if (!empty($ctas_bancarias)) {
                        foreach ($ctas_bancarias as $cta) {
                            $domiciliacion = "";
                            //Condicion para envio de domiciliacion
                            $comparacion = strpos($cta->usos, "^1^");
                            if ($comparacion === false) {
                                $domiciliacion = "FALSE";
                            } else {
                                $domiciliacion = "TRUE";
                            }
                            $nombre_banco = $bank[$cta->banco];
                            $new_cta_bancaria = array(
                                "_nombre_banco_cliente" => $nombre_banco,
                                "_domiciliacion" => $domiciliacion,
                                "_guid_crm" => $cta->id
                            );
                            if ($cta->cuenta != "") {
                                $new_cta_bancaria['_numero_cuenta_cliente'] = $cta->cuenta;
                            }
                            if ($cta->clabe != "") {
                                $new_cta_bancaria['_clabe_interbancaria'] = $cta->clabe;
                            }
                            array_push($array_cta_bancaria, $new_cta_bancaria);
                        }
                    }
                }
                $body = array(
                    "groupName" => $nombre,
                    "_referencias_crm" => array(
                        "_id_crm" => $id_crm,
                        "_id_cliente_corto" => $id_cliente_corto,
                        "_ref_bancaria" => $bean->referencia_bancaria_c
                    ),
                );
                if (count($array_cta_bancaria) > 0) {
                    $body['_cuentas_bancarias_clientes'] = $array_cta_bancaria;
                }
                //Obtener solicitudes par identificar si existe alguna con Unifactor
                $es_unificator = false;
                if ($bean->load_relationship('opportunities')) {
                    //Fetch related beans
                    $solicitudesFinan = $bean->opportunities->getBeans();
                    if (!empty($solicitudesFinan)) {
                        foreach ($solicitudesFinan as $sfinan) {
                            if ($sfinan->producto_financiero_c == '50') {
                                $es_unificator = true;
                            }
                        }
                    }
                }

                if ($es_unificator) {
                    $body_relacionados = array();
                    if ($bean->load_relationship('rel_relaciones_accounts')) {
                        //Fetch related beans
                        $relaciones = $bean->rel_relaciones_accounts->getBeans();
                        if (!empty($relaciones)) {
                            foreach ($relaciones as $rel) {
                                $beanCuentaEmail = BeanFactory::retrieveBean('Accounts', $rel->account_id1_c, array('disable_row_level_security' => true));
                                $item_relacionado=array(
                                    "_guid_relacionado_cl"=> $rel->account_id1_c,
                                    "_correo_relacionado_cl" => $beanCuentaEmail->email1,
                                    "_nombre_relacionado_cl" =>$rel->name,
                                    "_figura_relacionado_cl" => str_replace("^","",$rel->relaciones_activas)
                                );
                                array_push($body_relacionados,$item_relacionado);
                            }
                        }
                    }
                    $body['_relacionados_cliente'] = $body_relacionados;
                }
                $GLOBALS['log']->fatal(json_encode($body));
                $callApi = new UnifinAPI();
                $resultado = $callApi->postMambu($url, $body, $auth_encode);
                $GLOBALS['log']->fatal('--------------MAMBU RESPONSE-----------------');
                $GLOBALS['log']->fatal($resultado);
                if (!empty($resultado['encodedKey'])) {
                    $bean->encodedkey_mambu_c = $resultado['encodedKey'];
                }else{
                    //Mandar notificación a emails de la lista de studio
                    global $app_list_strings;
                    $cuentas_email=array();
                    $lista_correos = $app_list_strings['emails_error_mambu_list'];
                    //Recorriendo lista de emails
                    foreach ($lista_correos as $key => $value) {
                        array_push($cuentas_email,$lista_correos[$key]);
                    }
                    //$cuenta_email=$lista_correos['1'];
                    $bodyEmail=$this->estableceCuerpoCorreoErrorMambu($body,$resultado);
                    //Enviando correo
                    $this->enviarNotificacionErrorMambu("Notificación: Petición hacia Mambú generada sin éxito",$bodyEmail,$cuentas_email,"Admin");
                }
                //Obtener solicitudes
                if ($bean->load_relationship('opportunities')) {
                    //Fetch related beans
                    $solicitudes = $bean->opportunities->getBeans();
                    if (!empty($solicitudes)) {
                        global $app_list_strings;
                        $available_financiero=array();
                        $lista_productos = $app_list_strings['productos_integra_mambu_list'];
                        //Recorriendo lista de de productos
                        foreach ($lista_productos as $key => $value) {
                            array_push($available_financiero,$key);
                        }

                        foreach ($solicitudes as $sol) {
                            //Disparar integración hacia mambú de solicitudes para estatus AUTORIZADA
                            if (in_array($sol->producto_financiero_c,$available_financiero ) && $sol->tct_id_mambu_c == "" && $sol->estatus_c == 'N') {## cambiar por pPF
                                $sol->save();
                            }
                        }
                    }
                }
            }
        }
    }

    public function enviarNotificacionErrorMambu($asunto,$cuerpoCorreo,$correos,$nombreUsuario){
        //Enviando correo a asesor origen
        $GLOBALS['log']->fatal("ENVIANDO CORREO DE ERROR MAMBU A :".$correos);
        $insert = '';
        $hoy = date("Y-m-d H:i:s");
        try{
            $mailer = MailerFactory::getSystemDefaultMailer();
            $mailTransmissionProtocol = $mailer->getMailTransmissionProtocol();
            $mailer->setSubject($asunto);
            $body = trim($cuerpoCorreo);
            $mailer->setHtmlBody($body);
            $mailer->clearRecipients();
            for ($i=0; $i < count($correos); $i++) {
                $GLOBALS['log']->fatal("AGREGANDO CORREOS DESTINATARIOS: ".$correos[$i]);
                $mailer->addRecipientsTo(new EmailIdentity($correos[$i], $nombreUsuario));
            }
            //$mailer->addRecipientsTo(new EmailIdentity($correo, $nombreUsuario));
            $result = $mailer->send();

        } catch (Exception $e){
            $GLOBALS['log']->fatal("Exception: No se ha podido enviar correo al email ".$nombreUsuario);
            $GLOBALS['log']->fatal("Exception ".$e);

        } catch (MailerException $me) {
            $message = $me->getMessage();
            switch ($me->getCode()) {
                case \MailerException::FailedToConnectToRemoteServer:
                    $GLOBALS["log"]->fatal("BeanUpdatesMailer :: error sending email, system smtp server is not set");
                    break;
                default:
                    $GLOBALS["log"]->fatal("BeanUpdatesMailer :: error sending e-mail (method: {$mailTransmissionProtocol}), (error: {$message})");
                    break;
            }
        }

    }

    public function estableceCuerpoCorreoErrorMambu($contenidoPeticion,$contenidoError){

        $mailHTML = '<p align="justify"><font face="verdana" color="#635f5f"><b>Estimado usuario</b><br>
            Se le informa que se ha producido un error en la petición hacia Mambú, el cual se detalla de la siguiente forma:<br><br>'.json_encode($contenidoError).'
            <br><br>En donde la petición enviada fue la siguiente:<br><br>'.json_encode($contenidoPeticion).'
            <br><br>Atentamente Unifin</font></p>
            <br><br><img border="0" id="bannerUnifin" src="https://www.unifin.com.mx/ri/front/img/logo.png">
            <br><span style="font-size:8.5pt;color:#757b80">____________________________________________</span>
            <p class="MsoNormal" style="text-align: justify;">
              <span style="font-size: 7.5pt; font-family: \'Arial\',sans-serif; color: #212121;">
                Este correo electrónico y sus anexos pueden contener información CONFIDENCIAL para uso exclusivo de su destinatario. Si ha recibido este correo por error, por favor, notifíquelo al remitente y bórrelo de su sistema.
                Las opiniones expresadas en este correo son las de su autor y no son necesariamente compartidas o apoyadas por UNIFIN, quien no asume aquí obligaciones ni se responsabiliza del contenido de este correo, a menos que dicha información sea confirmada por escrito por un representante legal autorizado.
                No se garantiza que la transmisión de este correo sea segura o libre de errores, podría haber sido viciada, perdida, destruida, haber llegado tarde, de forma incompleta o contener VIRUS.
                Asimismo, los datos personales, que en su caso UNIFIN pudiera recibir a través de este medio, mantendrán la seguridad y privacidad en los términos de la Ley Federal de Protección de Datos Personales; para más información consulte nuestro <a href="https://www.unifin.com.mx/aviso-de-privacidad" target="_blank">Aviso de Privacidad</a>  publicado en <a href="http://www.unifin.com.mx/" target="_blank">www.unifin.com.mx</a>
              </span>
            </p>';

        return $mailHTML;

    }

    /******Funcion para guardar los valores del campo Puesto al campo de Puesto_Descriptivo*****/
    public function PuestoCuenta($bean = null, $event = null, $args = null)
    {
        $puestoCuenta = $bean->puesto_cuenta_c; //nuevo campo de puesto

        if ($puestoCuenta != '' || $puestoCuenta != null) {

            //Asignación del puesto_cuenta nuevo al puesto_c old
            switch ($puestoCuenta) {
                case '1': //Dueño
                    $bean->puesto_c = 'Duenio';
                    break;
                case '2': //Accionista
                    $bean->puesto_c = 'Accionistas';
                    break;
                case '3': //Director-General
                    $bean->puesto_c = 'Director General';
                    break;
                case '4': //Director-Comercial
                    $bean->puesto_c = 'Director Comercial';
                    break;
                case '5': //Director de Finanzas
                    $bean->puesto_c = 'Director de Finanzas';
                    break;
                case '6': //Director de Operaciones
                    $bean->puesto_c = 'Director de Operaciones';
                    break;
                case '7': //Director de Sistemas
                    $bean->puesto_c = 'Director de Sistemas';
                    break;
                case '8': //Tesorero - Contralor
                    $bean->puesto_c = 'Tesorero_Contralor';
                    break;
                case '9': //Gerente
                    $bean->puesto_c = 'Gerente';
                    break;
                case '10': //Administrativo
                    $bean->puesto_c = 'Administrativo';
                    break;
                case '11': //Otro
                    $bean->puesto_c = 'Otro';
                    break;
            }
        }
    }

    public function ActualizaTipo($bean = null, $event = null, $args = null)
    {
        global $db;
        global $app_list_strings;
        // Tipo y Subtipo de Cuenta
        $tipo = array_search($app_list_strings['tipo_registro_cuenta_list'][$bean->tipo_registro_cuenta_c], $app_list_strings['tipo_registro_list']);
        $subtipo = array_search($app_list_strings['subtipo_registro_cuenta_list'][$bean->subtipo_registro_cuenta_c], $app_list_strings['subtipo_cuenta_list']);
        $query = "update accounts_cstm set tipo_registro_c = '{$tipo}', subtipo_cuenta_c = '{$subtipo}' where id_c = '{$bean->id}'";
        $queryResult = $db->query($query);
        // Resumen Vista 360
        $beanResumen = BeanFactory::getBean('tct02_Resumen', $bean->id);
        // uni_Productos
        $bean->load_relationship('accounts_uni_productos_1');
        $relatedBeans = $bean->accounts_uni_productos_1->getBeans();
        foreach ($relatedBeans as $rel) {
            if ($rel->tipo_producto == 1) {
                $beanResumen->tct_tipo_l_txf_c = array_search($app_list_strings['tipo_registro_cuenta_list'][$rel->tipo_cuenta], $app_list_strings['tipo_registro_list']);
                $beanResumen->tct_subtipo_l_txf_c = array_search($app_list_strings['subtipo_registro_cuenta_list'][$rel->subtipo_cuenta], $app_list_strings['subtipo_cuenta_list']);
                $beanResumen->tct_tipo_cuenta_l_c = $rel->tipo_subtipo_cuenta;
            }
            if ($rel->tipo_producto == 3) {
                $beanResumen->tct_tipo_ca_txf_c = array_search($app_list_strings['tipo_registro_cuenta_list'][$rel->tipo_cuenta], $app_list_strings['tipo_registro_list']);
                $beanResumen->tct_subtipo_ca_txf_c = array_search($app_list_strings['subtipo_registro_cuenta_list'][$rel->subtipo_cuenta], $app_list_strings['subtipo_cuenta_list']);
                $beanResumen->tct_tipo_cuenta_ca_c = $rel->tipo_subtipo_cuenta;
            }
            if ($rel->tipo_producto == 4) {
                $beanResumen->tct_tipo_f_txf_c = array_search($app_list_strings['tipo_registro_cuenta_list'][$rel->tipo_cuenta], $app_list_strings['tipo_registro_list']);
                $beanResumen->tct_subtipo_f_txf_c = array_search($app_list_strings['subtipo_registro_cuenta_list'][$rel->subtipo_cuenta], $app_list_strings['subtipo_cuenta_list']);
                $beanResumen->tct_tipo_cuenta_f_c = $rel->tipo_subtipo_cuenta;
            }
            if ($rel->tipo_producto == 6) {
                $beanResumen->tct_tipo_fl_txf_c = array_search($app_list_strings['tipo_registro_cuenta_list'][$rel->tipo_cuenta], $app_list_strings['tipo_registro_list']);
                $beanResumen->tct_subtipo_fl_txf_c = array_search($app_list_strings['subtipo_registro_cuenta_list'][$rel->subtipo_cuenta], $app_list_strings['subtipo_cuenta_list']);
                $beanResumen->tct_tipo_cuenta_fl_c = $rel->tipo_subtipo_cuenta;
            }
            if ($rel->tipo_producto == 8) {
                $beanResumen->tct_tipo_uc_txf_c = array_search($app_list_strings['tipo_registro_cuenta_list'][$rel->tipo_cuenta], $app_list_strings['tipo_registro_list']);
                $beanResumen->tct_subtipo_uc_txf_c = array_search($app_list_strings['subtipo_registro_cuenta_list'][$rel->subtipo_cuenta], $app_list_strings['subtipo_cuenta_list']);
                $beanResumen->tct_tipo_cuenta_uc_c = $rel->tipo_subtipo_cuenta;
            }
        }
        $beanResumen->save();
    }

    public function ActualizaOrigen($bean_account = null, $event = null, $args = null)
    {
        switch ($bean_account->origen_cuenta_c) {
            case 1:
                $bean_account->origendelprospecto_c = "Marketing";
                break;
            case 2:
                $bean_account->origendelprospecto_c = "Inteligencia de Negocio";
                break;
            case 3:
                $bean_account->origendelprospecto_c = "Prospeccion propia";
                break;
            case 4:
                $bean_account->origendelprospecto_c = "Referido Cliente";
                break;
            case 5:
                $bean_account->origendelprospecto_c = "Referido Proveedor";
                break;
            case 6:
                $bean_account->origendelprospecto_c = "Referenciador";
                break;
            case 7:
                $bean_account->origendelprospecto_c = "Referido Director";
                break;
            case 8:
                $bean_account->origendelprospecto_c = "Referenciado Vendor";
                break;
            case 9:
                $bean_account->origendelprospecto_c = "Portal Uniclick";
                break;
            case 10:
                $bean_account->origendelprospecto_c = "Whatsapp";
                break;

        }

        //Switch para asignar los valores
        switch ($bean_account->detalle_origen_c) {
            case 1:
                $bean_account->tct_detalle_origen_ddw_c = "Base de datos";
                break;
            case 2:
                $bean_account->tct_detalle_origen_ddw_c = "Centro de Prospeccion";
                break;
            case 3:
                $bean_account->tct_detalle_origen_ddw_c = "Digital";
                break;
            case 4:
                $bean_account->tct_detalle_origen_ddw_c = "Campanas";
                break;
            case 5:
                $bean_account->tct_detalle_origen_ddw_c = "Acciones Estrategicas";
                break;
            case 6:
                $bean_account->tct_detalle_origen_ddw_c = "Afiliaciones";
                break;
            case 7:
                $bean_account->tct_detalle_origen_ddw_c = "Llamdas Inbound";
                break;
            case 8:
                $bean_account->tct_detalle_origen_ddw_c = "Parques Industriales";
                break;
            case 9:
                $bean_account->tct_detalle_origen_ddw_c = "Offline";
                break;
            case 10:
                $bean_account->tct_detalle_origen_ddw_c = "Cartera Promotores";
                break;
            case 11:
                $bean_account->tct_detalle_origen_ddw_c = "Recomendacion";
                break;
            default:
                $bean_account->tct_detalle_origen_ddw_c = $bean_account->detalle_origen_c;
                break;
        }

        switch ($bean_account->medio_detalle_origen_c) {
            case 1:
                $bean_account->medio_digital_c = "Facebook";
                break;
            case 2:
                $bean_account->medio_digital_c = "Twitter";
                break;
            case 3:
                $bean_account->medio_digital_c = "Instagram";
                break;
            case 4:
                $bean_account->medio_digital_c = "Web";
                break;
            case 5:
                $bean_account->medio_digital_c = "LinkedIn";
                break;
            case 6:
                $bean_account->medio_digital_c = "Radio Online";
                break;
            case 7:
                $bean_account->medio_digital_c = "Prensa Online";
                break;
            case 8:
                $bean_account->medio_digital_c = "TV Online";
                break;
            case 9:
                $bean_account->medio_digital_c = "Revistas Online";
                break;
            case 10:
                $bean_account->medio_digital_c = "TV";
                break;
            case 11:
                $bean_account->medio_digital_c = "Radio";
                break;
            case 12:
                $bean_account->medio_digital_c = "Prensa";
                break;
            case 13:
                $bean_account->medio_digital_c = "Revistas";
                break;
            case 14:
                $bean_account->medio_digital_c = "Espectaculares";
                break;

            default:
                $bean_account->medio_digital_c = $bean_account->medio_detalle_origen_c;
                break;
        }

        switch ($bean_account->punto_contacto_origen_c) {

            case 1:
                $bean_account->tct_punto_contacto_ddw_c = "Portal";

                break;
            case 2:
                $bean_account->tct_punto_contacto_ddw_c = "Telefono";

                break;
            case 3:
                $bean_account->tct_punto_contacto_ddw_c = "Chat";

                break;
            case 4:
                $bean_account->tct_punto_contacto_ddw_c = "Publicacion";

                break;
            default:
                $bean_account->tct_punto_contacto_ddw_c = $bean_account->punto_contacto_origen_c;
                break;
        }

        switch ($bean_account->prospeccion_propia_c) {
            case 1:
                $bean_account->metodo_prospeccion_c = "llamada_en_frio";

                break;
            case 2:
                $bean_account->metodo_prospeccion_c = "prospeccion_en_campo";

                break;
            case 3:
                $bean_account->metodo_prospeccion_c = "cartera";

                break;
        }

    }

    public function ActualizaEmpleadosDDW($bean_account = null, $event = null, $args = null)
    {
        $totalEmpleados = $bean_account->total_empleados_c;
        //ACTUALIZA LA LISTA DE NÚMERO DE EMPLEADOS CONTRA EL NÚMERO DE EMPLEADOS EXACTOS
        if ($totalEmpleados >= 0) {
            $GLOBALS['log']->fatal("TOTAL EMPLEADOS EXACTOS ". $totalEmpleados);
            if ($totalEmpleados <= 10) {
                $bean_account->empleados_c = '0a10';
            } elseif ($totalEmpleados <= 50) {
                $bean_account->empleados_c = '11a50';
            } elseif ($totalEmpleados <= 100) {
                $bean_account->empleados_c = '51a100';
            } elseif ($totalEmpleados <= 250) {
                $bean_account->empleados_c = '101a250';
            } elseif ($totalEmpleados <= 500) {
                $bean_account->empleados_c = '251a500';
            } elseif ($totalEmpleados <= 1000) {
                $bean_account->empleados_c = '501a1000';
            } else {
                $bean_account->empleados_c = '1001';
            }
            $GLOBALS['log']->fatal("ACTUALIZA LISTA DE EMPLEADOS ". $bean_account->empleados_c);
        }
    }

    public function func_grupo_empresarial($bean = null, $event = null, $args = null)
    {
        global $db;
        $idPadre = $bean->parent_id;
        $situacionGE = $bean->situacion_gpo_empresarial_c;
        $totalHijos = 0;
        $nombrePadre ='';
        $listaSituacionGE = [];
        $listaTextoSGE = [];

        //$GLOBALS['log']->fatal("LH Grupo Empresarial");
        $sql = "Select id,name from accounts a where parent_id = '{$bean->id}' and deleted = 0";
        $result = $db->query($sql);
        $totalHijos = $result->num_rows;
        //Validar relación padre
        if( !empty($idPadre) ) {
            $listaSituacionGE[] = "^2^";
            //Recupera cuenta padre
            $cuentaPadre = BeanFactory::retrieveBean('Accounts', $bean->parent_id, array('disable_row_level_security' => true));
            $nombrePadre = $cuentaPadre->name;
        }
        //Validar relación hijos
        if( $totalHijos>0 ) {
            $listaSituacionGE[] = "^1^";
        }
        //Validar Sin grupo empresaril
        if( $totalHijos==0 && empty($idPadre) && strpos($situacionGE, "3") ) {
            $listaSituacionGE[] = "^3^";
        }
        //Establece valor default
        if(count($listaSituacionGE)==0){
            $listaSituacionGE[] = "^4^";
        }

        //Armar arreglo de texto SGE
        if ( in_array("^1^", $listaSituacionGE ) ){
            $listaTextoSGE[] = 'Cuenta primaria del grupo ' . $bean->name ;
        }
        if ( in_array("^2^" , $listaSituacionGE )){
            $listaTextoSGE[] = 'Cuenta secundaria del grupo ' . $nombrePadre;
        }
        if ( in_array( "^3^" , $listaSituacionGE )){
            $listaTextoSGE[] = 'No pertenece a ningún grupo empresarial';
        }
        if ( in_array( "^4^" ,$listaSituacionGE) ){
            $listaTextoSGE[] = 'Sin Grupo Empresarial Verificado';
        }

        //Establece valores para situación de grupo empresarial
        $bean->situacion_gpo_empresarial_c = (count($listaSituacionGE)>0) ? implode(",",$listaSituacionGE) : $bean->situacion_gpo_empresarial_c;
        $bean->situacion_gpo_empresa_txt_c = (count($listaTextoSGE)>0) ? implode("\n",$listaTextoSGE) : $bean->situacion_gpo_empresa_txt_c;
        // $GLOBALS['log']->fatal("LH Grupo Empresarial_Previo_ ".$bean->fetched_row['parent_id']);
        // $GLOBALS['log']->fatal("LH Grupo Empresarial_Previo ". $bean->parent_id);
        //Actualiza cuenta padre cuando se elimina relación
        if(!empty($bean->fetched_row['parent_id']) && $bean->parent_id!=$bean->fetched_row['parent_id'] ) {
            //Recupera cuenta padre (previa)
            $bean->parent_id_previo = $bean->fetched_row['parent_id'];
            //$GLOBALS['log']->fatal("Id Padre previo: ". $bean->parent_id_previo);
        }
    }

    public function cambioRazonSocial($bean = null, $event = null, $args = null){
        //Entra funcionalidad solo en caso de que el rfc sea el mismo y se detecte algún cambio en los campos:
        // Razón social / Nombre, Apellidos
        // Dirección fiscal
        //razonsocial_c,
        $send_notification = false;
        $cambio_nombre =  false;
        $cambio_dirFiscal =  false;

        $id_direccion_buscar = "";
        $elemento_actual_direccion = null;
        $elemento_por_actualizar_direccion = null;
        //$GLOBALS['log']->fatal("############ VALIDA CAMBIO DE NOMBRE ############");
        //$GLOBALS['log']->fatal("ANTES: ". $bean->fetched_row['name']. " DESPUÉS: ".$bean->name );
        //$GLOBALS['log']->fatal( print_r( json_decode($bean->json_direccion_audit_c,true),true ) );

        $text_cambios = '';
        if( $bean->valid_cambio_razon_social_c == 1 ){
            //Se envía excepción en caso de que el registro se encuentre en proceso de validación
            //La validación solo aplica para Cliente:3 
            if( ($bean->tipo_registro_cuenta_c == '3') && $bean->origen_cuenta_c !== '11' && $bean->subtipo_registro_cuenta_c != '11' ){
                // require_once 'include/api/SugarApiException.php';
                // $GLOBALS['log']->fatal("No es posible generar cambios al registro ya que se encuentra en un proceso de revisión");
                // throw new SugarApiExceptionInvalidParameter("No es posible generar cambios al registro ya que se encuentra en un proceso de revisión");

                //Cuando se detecta cambio y razón social, permite guardado solo que los cambios en los campod:
                //nombre, apellidos, razón social y denominación social no sr verán reflejados
                $bean->name = $bean->fetched_row['name'];
                if( $bean->tipodepersona_c !== "Persona Moral" ){ //PF y PFAE

                    $bean->primernombre_c = $bean->fetched_row['primernombre_c'];
                    $bean->apellidopaterno_c = $bean->fetched_row['apellidopaterno_c'];
                    $bean->apellidomaterno_c = $bean->fetched_row['apellidomaterno_c'];
                    $bean->nombre_comercial_c = $bean->fetched_row['nombre_comercial_c'];

                }else{
                    $bean->denominacion_c = $bean->fetched_row['denominacion_c'];
                    $bean->razonsocial_c = $bean->fetched_row['razonsocial_c'];
                    $bean->nombre_comercial_c = $bean->fetched_row['nombre_comercial_c'];
                }

            }

        }else{
            if( !empty($bean->rfc_c) && ($bean->tipo_registro_cuenta_c == '3') && $bean->origen_cuenta_c !== '11' && $bean->subtipo_registro_cuenta_c != '11' ){

                if( $bean->fetched_row['rfc_c'] == $bean->rfc_c ){
                    $text_cambios .= '<ul>';

                    $source = $_REQUEST['__sugar_url'];
                    $endpoint = 'AprobarCambiosRazonSocialDireFiscal';
                    //$pos - controlar el origen desde donde se dispara el guardado del registro (desde api custom ó desde el guardado normal directo en el registro)
                    $pos = strpos($source,$endpoint);

                    if( $bean->fetched_row['name'] !== $bean->name && $pos==false ){
                        $GLOBALS['log']->fatal("El nombre cambió, se envía notificación");
                        $send_notification = true;
                        $cambio_nombre = true;
                        $text_cambios .= '<li><b>Razón social / Nombre</b>: <b>tenía el valor</b> '. $bean->fetched_row['name'] .'<b> y cambió a </b>'.$bean->name.'</li>';
                    }

                    if( $bean->tipodepersona_c == 'Persona Moral' && $pos==false ){
                        $name_actual = $bean->fetched_row['name'];
                        $name_nuevo = $bean->name;
                        $regimen_capital = $bean->regimen_capital_c;

                        $send_notification = $this->evaluaCambioRegimenCapital( $name_actual, $name_nuevo, $regimen_capital );
                    } 

                    
                    //Detectar cambio dirección fiscal
                    $direccion_anterior = $this->getDireccionFiscalBD($bean);
                    if( !empty( $direccion_anterior ) ){

                        $id_direccion_buscar = $direccion_anterior[0];
                        $direccion_anterior_completa = $direccion_anterior[1];
                        $elemento_actual_direccion = $direccion_anterior[2];
                        //$GLOBALS['log']->fatal("********** Direccion anterior **********");
                        //$GLOBALS['log']->fatal(print_r($direccion_anterior,true));

                        $direccion_nueva_completa = $this->getDireccionFiscalActual($bean->account_direcciones);
                        //$GLOBALS['log']->fatal("********** Direccion nueva **********");
                        //$GLOBALS['log']->fatal(print_r($direccion_nueva_completa,true));
                        if( !empty($direccion_nueva_completa[0]) ){
                            $GLOBALS['log']->fatal("#####Direccion actual: ". strtoupper($direccion_anterior_completa));
                            $GLOBALS['log']->fatal("#####Direccion por actualizar: ". strtoupper($direccion_nueva_completa[0]));
                            if( strtoupper($direccion_anterior_completa) !== strtoupper($direccion_nueva_completa[0]) && $pos==false ){
                                $GLOBALS['log']->fatal("La dirección cambió, se envía notificación");
                                $elemento_por_actualizar_direccion = $direccion_nueva_completa[1];
                                $send_notification = true;
                                $cambio_dirFiscal = true;
                                $text_cambios .= '<li><b>Dirección fiscal</b>: <b>tenía el valor </b>'. ucwords($direccion_anterior_completa) .'<b> y cambió a </b>'.ucwords($direccion_nueva_completa[0]).'</li>';
                                //$this->insertCambiosDireFiscalAudit( $bean->id, strtoupper($direccion_anterior_completa), strtoupper($direccion_nueva_completa), $id_direccion_buscar );
                            }

                        }
                        
                    }
                    
                    $text_cambios .= '</ul>';
                }

            }
        }

        if( $send_notification ){
            global $app_list_strings;

            $producto_envio = $this->getMultiproductoParaEnvioNotificacion( $bean );
            if( $producto_envio == 'Uniclick' ){
                $emails_responsables_cambios_list = $app_list_strings['emails_uniclick_list'];
            }

            if( $producto_envio == 'Credito' ){
                $emails_responsables_cambios_list = $app_list_strings['emails_multiproducto_list'];
            }

            if( $producto_envio == 'Credito-Uniclick' ){
                $emails_responsables_cambios_list = array_merge( $app_list_strings['emails_uniclick_list'], $app_list_strings['emails_multiproducto_list'] );
            }

        
            //$emails_responsables_cambios_list = ( $producto_envio == 'Uniclick' ) ? $app_list_strings['emails_uniclick_list'] : $app_list_strings['emails_multiproducto_list'];
            
            $body_correo = $this->buildBodyCambioRazon( $bean->rfc_c, $text_cambios, $bean->id, $bean->fetched_row['name'] );
            $this->sendEmailCambioRazonSocial( $emails_responsables_cambios_list, $body_correo );

            //Habilita bandera para indicar que el registro se encuentra en proceso de validación
            //$bean->valid_cambio_razon_social_c = 1;
            //Enviar mensaje se establece en 0 para evitar que se envíe nuevamente la notificación desde el JOB
            $bean->enviar_mensaje_c = 0;
            $plataforma = $_SESSION['platform'];
            $fecha_cambio = TimeDate::getInstance()->nowDb();

            if( $cambio_nombre ){
                $bean->cambio_nombre_c = 1;
                $bean->valid_cambio_razon_social_c = 1;
                //Establece json con cambios y revierte valores
                if($bean->tipodepersona_c == 'Persona Moral'){
                    $razon_social_actual = $bean->fetched_row['razonsocial_c'];
                    $razon_social_por_actualizar = $bean->razonsocial_c;
                    $nombre_actual = $bean->fetched_row['name'];
                    $nombre_por_actualizar = $bean->name;
                    $json_audit = '{"tipo":"'.$bean->tipodepersona_c.'","razon_social_actual":"'.$razon_social_actual.'","razon_social_por_actualizar":"'.$razon_social_por_actualizar.'","primer_nombre_actual":" ","primer_nombre_por_actualizar":" ","paterno_actual":" ","paterno_por_actualizar":" ","materno_actual":" ","materno_por_actualizar":" ","nombre_actual":"'.$nombre_actual.'","nombre_por_actualizar":"'.$nombre_por_actualizar.'","fecha_cambio":"'.$fecha_cambio.'","plataforma":"'.$plataforma.'"}';

                    //Revierte cambios
                    $bean->razonsocial_c = $bean->fetched_row['razonsocial_c'];
                    $bean->nombre_comercial_c = $bean->fetched_row['nombre_comercial_c'];
                }else{

                    $nombre_actual = $bean->fetched_row['name'];
                    $nombre_por_actualizar = $bean->name;

                    $primer_nombre_actual = $bean->fetched_row['primernombre_c'];
                    $primer_nombre_por_actualizar = $bean->primernombre_c;

                    $paterno_actual = $bean->fetched_row['apellidopaterno_c'];
                    $paterno_por_actualizar = $bean->apellidopaterno_c;

                    $materno_actual = $bean->fetched_row['apellidomaterno_c'];
                    $materno_por_actualizar = $bean->apellidomaterno_c;

                    $json_audit = '{"tipo":"'.$bean->tipodepersona_c.'","razon_social_actual":" ","razon_social_por_actualizar":" ","primer_nombre_actual":"'.$primer_nombre_actual.'","primer_nombre_por_actualizar":"'.$primer_nombre_por_actualizar.'","paterno_actual":"'.$paterno_actual.'","paterno_por_actualizar":"'.$paterno_por_actualizar.'","materno_actual":"'.$materno_actual.'","materno_por_actualizar":"'.$materno_por_actualizar.'","nombre_actual":"'.$nombre_actual.'","nombre_por_actualizar":"'.$nombre_por_actualizar.'","fecha_cambio":"'.$fecha_cambio.'","plataforma":"'.$plataforma.'"}';

                    //Revierte cambios
                    $bean->primernombre_c = $bean->fetched_row['primernombre_c'];
                    $bean->apellidopaterno_c = $bean->fetched_row['apellidopaterno_c'];
                    $bean->apellidomaterno_c = $bean->fetched_row['apellidomaterno_c'];
                }

                $bean->name = $bean->fetched_row['name'];
                $GLOBALS['log']->fatal("Establece json");
                $GLOBALS['log']->fatal($json_audit);
                $bean->json_audit_c = $json_audit;
            }

            if( $cambio_dirFiscal ){
                //$bean->cambio_dirfiscal_c = 1;
                //Se habilita bandera para evitar guarar las direcciones
                //$bean->omitir_guardado_direcciones_c = 1;
                $bean->omitir_caso= 1;

                //Valores actuales (names) para armar la dirección completa
                $current_calle = $elemento_actual_direccion->calle;
                $current_numext = $elemento_actual_direccion->numext;
                $current_numint = $elemento_actual_direccion->numint;
                $current_cp = $elemento_actual_direccion->dire_direccion_dire_codigopostal_name;
                $current_pais = $elemento_actual_direccion->dire_direccion_dire_pais_name;
                $current_estado = $elemento_actual_direccion->dire_direccion_dire_estado_name;
                $current_municipio = $elemento_actual_direccion->dire_direccion_dire_municipio_name;
                $current_ciudad = $elemento_actual_direccion->dire_direccion_dire_ciudad_name;
                $current_colonia = $elemento_actual_direccion->dire_direccion_dire_colonia_name;
                $full_direccion_actual = "Calle: ". $current_calle .", CP: ". $current_cp .", País: ". $current_pais .", Estado: ". $current_estado .", Municipio: ". $current_municipio .", Ciudad: ". $current_ciudad .", Colonia: ". $current_colonia .", Número exterior: ". $current_numext .", Número interior: ".$current_numint;


                //Valores por actualizar (names) para armar la dirección completa
                $calle_act = $elemento_por_actualizar_direccion['calle'];
                $numext_act = $elemento_por_actualizar_direccion['numext'];
                $numint_act = $elemento_por_actualizar_direccion['numint'];
                $cp_act = $elemento_por_actualizar_direccion['valCodigoPostal'];
                $pais_act = $elemento_por_actualizar_direccion['listPais'][$elemento_por_actualizar_direccion['pais']];
                $estado_act = $elemento_por_actualizar_direccion['listEstado'][$elemento_por_actualizar_direccion['estado']];
                $municipio_act = $elemento_por_actualizar_direccion['listMunicipio'][$elemento_por_actualizar_direccion['municipio']];
                $ciudad_act = $elemento_por_actualizar_direccion['listCiudad'][$elemento_por_actualizar_direccion['ciudad']];
                $colonia_act = $elemento_por_actualizar_direccion['listColonia'][$elemento_por_actualizar_direccion['colonia']];
                $full_direccion_por_actualizar = "Calle: ". $calle_act .", CP: ". $cp_act .", País: ". $pais_act .", Estado: ". $estado_act .", Municipio: ". $municipio_act .", Ciudad: ". $ciudad_act .", Colonia: ". $colonia_act .", Número exterior: ". $numext_act .", Número interior: ".$numint_act;


                $cp_actual = $elemento_actual_direccion->dire_direccion_dire_codigopostaldire_codigopostal_ida;
                //Obtener el id, ya que al actualizar la dirección no se está obteniendo el atributo 'postal'
                $cp_por_actualizar = $elemento_por_actualizar_direccion['valCodigoPostal'];

                $pais_actual = $elemento_actual_direccion->dire_direccion_dire_paisdire_pais_ida;
                $pais_por_actualizar = $elemento_por_actualizar_direccion['pais'];

                $estado_actual = $elemento_actual_direccion->dire_direccion_dire_estadodire_estado_ida;
                $estado_por_actualizar = $elemento_por_actualizar_direccion['estado'];

                $municipio_actual = $elemento_actual_direccion->dire_direccion_dire_municipiodire_municipio_ida;
                $municipio_por_actualizar = $elemento_por_actualizar_direccion['municipio'];

                $ciudad_actual = $elemento_actual_direccion->dire_direccion_dire_ciudaddire_ciudad_ida;
                $ciudad_por_actualizar = $elemento_por_actualizar_direccion['ciudad'];

                $colonia_actual = $elemento_actual_direccion->dire_direccion_dire_coloniadire_colonia_ida;
                $colonia_por_actualizar = $elemento_por_actualizar_direccion['colonia'];

                $calle_actual = $elemento_actual_direccion->calle;
                $calle_por_actualizar = $elemento_por_actualizar_direccion['calle'];

                $numext_actual = $elemento_actual_direccion->numext;
                $numext_por_actualizar = $elemento_por_actualizar_direccion['numext'];

                $numint_actual = $elemento_actual_direccion->numint;
                $numint_por_actualizar = $elemento_por_actualizar_direccion['numint'];

                $json_audit_direccion='{
                    "id_direccion":"'. $elemento_actual_direccion->id . '",
                    "indicador":"'. $elemento_por_actualizar_direccion['indicador'] . '",
                    "cp_actual":"'. $cp_actual . '",
                    "cp_por_actualizar":"'. $cp_por_actualizar . '",
                    "pais_actual":"'. $pais_actual . '",
                    "pais_por_actualizar":"'. $pais_por_actualizar . '",
                    "estado_actual":"'. $estado_actual . '",
                    "estado_por_actualizar":"'. $estado_por_actualizar . '",
                    "municipio_actual":"'. $municipio_actual . '",
                    "municipio_por_actualizar":"'. $municipio_por_actualizar . '",
                    "ciudad_actual":"'. $ciudad_actual . '",
                    "ciudad_por_actualizar":"'. $ciudad_por_actualizar . '",
                    "colonia_actual":"'. $colonia_actual . '",
                    "colonia_por_actualizar":"'. $colonia_por_actualizar . '",
                    "calle_actual":"'. $calle_actual . '",
                    "calle_por_actualizar":"'. $calle_por_actualizar . '",
                    "numext_actual":"'. $numext_actual . '",
                    "numext_por_actualizar":"'. $numext_por_actualizar . '",
                    "numint_actual":"'. $numint_actual . '",
                    "numint_por_actualizar":"'. $numint_por_actualizar . '",
                    "direccion_completa_actual":"'. $full_direccion_actual . '",
                    "direccion_completa_por_actualizar":"'. $full_direccion_por_actualizar . '",
                    "fecha_cambio":"'. $fecha_cambio .'",
                    "plataforma":"'. $plataforma .'"
                }';

                $GLOBALS['log']->fatal("JSON DIRECCION");
                $GLOBALS['log']->fatal($json_audit_direccion);

                //Revierte cambios en direccion, para esto, se establece un nuevo atributo al objeto account_direcciones para que sirva de switch y se tenga el privilegio de actualización
                //Cuando se detecta cambio en dirección fiscal, ésta no se debe de guardar sino hasta que se aprueben cambios
                //if( $id_direccion_buscar !== "" ){
                /*
                $indice_direccion = $this->buscarFiscalParaModificar($bean->account_direcciones);
                if( $indice_direccion !=="" ){
                    $bean->account_direcciones[$indice_direccion]['noGuardar'] = '1';

                    //Si la dirección fiscal que se eligió como "nueva" ya era existente, se toma el id para actualizar el json que se toma en cuenta al aprobar los cambios
                    if( !empty($bean->account_direcciones[$indice_direccion]['id']) ){
                        $id_direccion_buscar = $bean->account_direcciones[$indice_direccion]['id'];
                        $date_now = TimeDate::getInstance()->nowDb();
                        //Actualiza el campo json de la dirección directamente a la bd
                        $queryUpdateJSON = "UPDATE dire_direccion_cstm SET json_audit_c = '{$json_audit_direccion}' WHERE id_c ='{$id_direccion_buscar}'";
                        $queryUpdateDireccionModified = "UPDATE dire_direccion SET date_modified = '{$date_now}' WHERE id = '{$id_direccion_buscar}'";
                        $GLOBALS['log']->fatal("UPDATE JSON DE DIRECCION");
                        $GLOBALS['log']->fatal($queryUpdateJSON);

                        $GLOBALS['db']->query($queryUpdateJSON);
                        $GLOBALS['db']->query($queryUpdateDireccionModified);
                        
                    }else{
                        //ToDo: ¿Como obtener id de una dirección que no existía (es nueva) y se necesita que viaje con json_audit para una futura actualización de datos al Aprobar?
                    }

                }
                */
                //}
            }

            //$GLOBALS['log']->fatal("*******DIRECCIONES NUEVO ATRIBUTO*******");
            //$GLOBALS['log']->fatal(print_r($bean->account_direcciones,true));
            //$GLOBALS['log']->fatal($bean->omitir_caso);
            if(!$bean->omitir_caso){
              $this->creaCaso( $bean->id );
            }
        }

    }

    function evaluaCambioRegimenCapital( $name_actual, $name_nuevo, $regimen_capital ){
        $name_actual = trim($name_actual);
        $name_nuevo = trim ($name_nuevo);
        $name_nuevo = str_replace($regimen_capital,"",$name_nuevo);
        $name_nuevo = trim ($name_nuevo);
        $notificacion = false;
        //$GLOBALS['log']->fatal("name_actual-".$name_actual."-");
        //$GLOBALS['log']->fatal("name_nuevo-".$name_nuevo."-");
        //$GLOBALS['log']->fatal("regimen_capital-".$regimen_capital."-");
        if( $this->startsWith( $name_actual, $name_nuevo ) ){
            $GLOBALS['log']->fatal("SE PROCEDE A VALIDAR REGIMEN CAPITAL");
            $regimen_capital_actual = str_replace($name_nuevo,"",$name_actual );

            $regimen_capital_actual_clean = preg_replace("/\s+/", "", str_replace(['.',','],"", $regimen_capital_actual));
            $GLOBALS['log']->fatal("CLEAN REGIMEN CAPITAL ACTUAL CLEAN: ".$regimen_capital_actual_clean);

            $regimen_capital_nuevo_clean = preg_replace("/\s+/", "",str_replace(['.',','],"", $regimen_capital));
            $GLOBALS['log']->fatal("CLEAN REGIMEN CAPITAL NUEVO CLEAN: ".$regimen_capital_nuevo_clean);

            if( $regimen_capital_actual_clean != $regimen_capital_nuevo_clean ){
                $notificacion = true;
            }else{
                $notificacion = false;
            }

        }else{
            $notificacion = true;
        }

        return $notificacion;

    }

    /*
    * Función para evaluar si una cadena empieza con otra cadena
    */
    function startsWith($string, $startString) { 
        $len = strlen($startString); 
        return (substr($string, 0, $len) === $startString); 
    }
    /*
    * Obtiene productos relacionados de la cuenta para saber a cual grupo de correos electrónicos de Email se envia
    * Si es Cliente en cualquier producto, se envía a un grupo
    * Si UNICAMENTE es Cliente en producto Uniclick, se envía a otro grupo diferente
    */
    function getMultiproductoParaEnvioNotificacion ( $beanPersona ){

        $area_interna = "";

        if ($beanPersona->load_relationship('accounts_uni_productos_1')) {
            //Recupera Productos para conocer el tipo de cuenta por cada uno
            $relateProduct = $beanPersona->accounts_uni_productos_1->getBeans($beanPersona->id,array('disable_row_level_security' => true));
            $array_tipo_cuenta_producto = array();
            foreach ($relateProduct as $product) {
                    //Recupera valores por producto
                    $tipoCuenta = $product->tipo_cuenta;
                    $tipoProducto = $product->tipo_producto;
                    $subtipo = $product->subtipo_cuenta; // 11 - Venta Activo
                    if( $subtipo != "11" ){

                        switch ($tipoProducto) {
                            case '1': //Leasing
                                $array_tipo_cuenta_producto['leasing'] = $tipoCuenta;
                                break;
                            case '2': //Crédito Simple
                                $array_tipo_cuenta_producto['cs'] = $tipoCuenta;
                                break;
                            case '3': //Credito-Automotriz
                                $array_tipo_cuenta_producto['ca'] = $tipoCuenta;
                                break;
                            case '4': //Factoraje
                                $array_tipo_cuenta_producto['factoraje'] = $tipoCuenta;
                                break;
                            case '6': //Fleet
                                $array_tipo_cuenta_producto['fleet'] = $tipoCuenta;
                                break;
                            case '8': //Uniclick
                                $array_tipo_cuenta_producto['uniclick'] = $tipoCuenta;
                                break;
                            case '14': //Tarjeta Crédito
                                $array_tipo_cuenta_producto['tc'] = $tipoCuenta;
                                break;
                            
                        }
                    }
            }

            //Recorre arreglo generado para conocer si es multiproducto y el caso se debe asignar a Area Interna Crédito o Uniclick
            $contador_cliente = 0;
            $contador_cliente_uniclick = 0;
            foreach ( $array_tipo_cuenta_producto as $key => $value ){
                if( $value == '3' ){
                        if( $key == 'uniclick' ){
                            $contador_cliente_uniclick += 1; 
                        }else{
                            $contador_cliente += 1;
                        }
                    }
                }
                if( $contador_cliente_uniclick > 0 && $contador_cliente == 0 ){
                    //ES CLIENTE UNICLICK, SE ESTABLECE ÁREA INTERNA UNICLICK
                    $GLOBALS['log']->fatal("ES CLIENTE UNICLICK, SE ESTABLECE ÁREA INTERNA UNICLICK");
                    $area_interna = 'Uniclick';
                
                }
                if( $contador_cliente > 0 && $contador_cliente_uniclick > 0 ){
                    //ES MULTIPRODUCTO, SE ESTABLECE ÁREA INTERNA CRÉDITO
                    $GLOBALS['log']->fatal("ES MULTIPRODUCTO Y CLIENTE UNICLICK");
                    $area_interna = 'Credito-Uniclick';
                }

                if( $contador_cliente > 0 && $contador_cliente_uniclick == 0 ){
                    //ES MULTIPRODUCTO, SE ESTABLECE ÁREA INTERNA CRÉDITO
                    $GLOBALS['log']->fatal("ES MULTIPRODUCTO, SE ESTABLECE ÁREA INTERNA CRÉDITO");
                    $area_interna = 'Credito';
                }
                
                if( $contador_cliente == 0 && $contador_cliente_uniclick == 0){
                    //NO ES CLIENTE EN NINGÚN PRODUCTO
                    $GLOBALS['log']->fatal("NO ES CLIENTE EN NINGÚN PRODUCTO");
                    $area_interna = 'Credito';
                }

        }
        

        return $area_interna;

    }

    public function buscarFiscalParaModificar( $direcciones ){
        $indice="";
        $indicador_direcciones_fiscales = array(2,3,6,7,10,11,14,15,18,19,22,23,26,27,30,31,34,35,38,39,42,43,46,47,50,51,54,55,58,59,62,63);
        if( count($direcciones) > 0 ){
            for ($i=0; $i < count($direcciones); $i++) { 

                if( in_array($direcciones[$i]['indicador'],$indicador_direcciones_fiscales) ){
                    $indice = $i;

                    //$i se establece con el count para salir del ciclo for
                    $i = count($direcciones);
                }
            }
        }

        return $indice;

    }

    /**
     * @param $direccion_anterior, string de la dirección anterior, antes de ser cambiada
     * @param $direccion_nueva, string de la dirección nueva, contiene el valor de la dirección actualizada
     */
    public function insertCambiosDireFiscalAudit( $id_record, $direccion_anterior, $direccion_nueva, $id_direccion){
        global $current_user;
        $id_user = $current_user->id;
        $parent_id = $id_record;
        $id_audit = create_guid();
        $date = TimeDate::getInstance()->nowDb();

        //$event_id - Guardará el id de la dirección fiscal a nivel de la tabla dire_Direccion
        $event_id = $id_direccion;

        $insertQuery ="INSERT INTO `accounts_audit` (`id`,`parent_id`,`date_created`,`created_by`,`field_name`,`data_type`,`before_value_string`,`after_value_string`,`before_value_text`,`after_value_text`,`event_id`,`date_updated`) VALUES ('{$id_audit}','{$parent_id}','{$date}','{$id_user}','dire_Direccion','varchar','{$direccion_anterior}','{$direccion_nueva}',NULL,NULL,'{$event_id}',NULL)";
        $GLOBALS['log']->fatal("QUERY INSERT ACCOUNTS");
        $GLOBALS['log']->fatal($insertQuery);
        $GLOBALS['db']->query($insertQuery);
    }

    /**
     * @param $bean - Bean de la cuenta
     * @return $return_array - Primera posicion: id de la dirección fiscal, segunda posición: string de la dirección completa
     */
    public function getDireccionFiscalBD($bean){
        $direccion_completa = '';
        $return_array = array();
        $indicador_direcciones_fiscales = array(2,3,6,7,10,11,14,15,18,19,22,23,26,27,30,31,34,35,38,39,42,43,46,47,50,51,54,55,58,59,62,63);
        if ($bean->load_relationship('accounts_dire_direccion_1')) {
            $relatedBeans = $bean->accounts_dire_direccion_1->getBeans();

            if (!empty($relatedBeans)) {

                foreach ($relatedBeans as $direccion) {

                    //Valida si tiene dirección fiscal
                    $indicador = $direccion->indicador;
                    if( in_array($indicador,$indicador_direcciones_fiscales) && $direccion->inactivo == 0 ){
                        $GLOBALS['log']->fatal( "ES FISCAL");
                        $id = $direccion->id;
                        $cp = $direccion->dire_direccion_dire_codigopostal_name;
                        $calle = $direccion->calle;
                        $pais = $direccion->dire_direccion_dire_pais_name;
                        $estado = $direccion->dire_direccion_dire_estado_name; 
                        $municipio = $direccion->dire_direccion_dire_municipio_name;
                        $ciudad = $direccion->dire_direccion_dire_ciudad_name;
                        $colonia = $direccion->dire_direccion_dire_colonia_name;
                        $numext = $direccion->numext;
                        $numint = $direccion->numint;

                        $direccion_completa = "Calle: ". $calle .", CP: ". $cp .", País: ". $pais .", Estado: ". $estado .", Municipio: ". $municipio .", Ciudad: ". $ciudad .", Colonia: ". $colonia .", Número exterior: ". $numext .", Número interior: ".$numint;

                        array_push( $return_array, $id, $direccion_completa, $direccion);

                        break;
                    }

                }
            }
        }

        return $return_array;

    }

    public function getDireccionFiscalActual($direcciones){
        $direccion_completa = "";
        $elementoDirFiscalActual ="";
        $indicador_direcciones_fiscales = array(2,3,6,7,10,11,14,15,18,19,22,23,26,27,30,31,34,35,38,39,42,43,46,47,50,51,54,55,58,59,62,63);
        if(!empty($direcciones)){

            if( count($direcciones) > 0 ){
                $posicion_direccion_fiscal = "";
                for ($i=0; $i < count($direcciones); $i++) { 
                    //Se busca la fiscal sobre las direcciones por actualizar
                    if( in_array($direcciones[$i]['indicador'],$indicador_direcciones_fiscales) && empty( $direcciones[$i]['inactivo'] )){
                        
                        $posicion_direccion_fiscal = $i;
                        //El indice se establece con count para cortar el ciclo for y salir de el
                        $i = count($direcciones);
                    }
                }
                //$GLOBALS['log']->fatal("******OBJETO DIRECCIONES*****");
                //$GLOBALS['log']->fatal(print_r($direcciones,true));
                $elementoDirFiscalActual = $direcciones[$posicion_direccion_fiscal];

                $cp = $elementoDirFiscalActual['valCodigoPostal'];
                $calle = $elementoDirFiscalActual['calle'];
                $pais = $elementoDirFiscalActual['listPais'][$elementoDirFiscalActual['pais']];
                $estado = $elementoDirFiscalActual['listEstado'][$elementoDirFiscalActual['estado']]; 
                $municipio = $elementoDirFiscalActual['listMunicipio'][$elementoDirFiscalActual['municipio']]; 
                $ciudad = $elementoDirFiscalActual['listCiudad'][$elementoDirFiscalActual['ciudad']];
                $colonia = $elementoDirFiscalActual['listColonia'][$elementoDirFiscalActual['colonia']];
                $numext = $elementoDirFiscalActual['numext'];
                $numint = $elementoDirFiscalActual['numint'];

                $direccion_completa .= "Calle: ". $calle .", CP: ". $cp .", País: ". $pais .", Estado: ". $estado .", Municipio: ". $municipio .", Ciudad: ". $ciudad .", Colonia: ". $colonia .", Número exterior: ". $numext .", Número interior: ".$numint;
            }

        }
        
        return array($direccion_completa,$elementoDirFiscalActual);
    }

    public function buildBodyCambioRazon( $rfc, $text_cambios, $idCuenta, $nombreCuenta ){
        global $sugar_config;
        $url = $sugar_config['site_url'];

        $linkCuenta = '<a href="'.$url.'/#Accounts/'. $idCuenta .'">'.$nombreCuenta.'</a>';

        $mailHTML = '<p align="justify"><font face="verdana" color="#635f5f">
            Se han detectado cambios sobre la cuenta con RFC: <b>'.$rfc.'</b>.<br>
            <br>A continuación se muestra las modificaciones generadas:<br>'.
            $text_cambios.'<br>
            Se solicita la revisión de esta cuenta ' .$linkCuenta. ' para autorizar o rechazar los cambios correspondientes.
            <br><br>Atentamente Unifin</font></p>
            <br><br><img border="0" id="bannerUnifin" src="https://www.unifin.com.mx/ri/front/img/logo.png">
            <br><span style="font-size:8.5pt;color:#757b80">____________________________________________</span>
            <p class="MsoNormal" style="text-align: justify;">
              <span style="font-size: 7.5pt; font-family: \'Arial\',sans-serif; color: #212121;">
                Este correo electrónico y sus anexos pueden contener información CONFIDENCIAL para uso exclusivo de su destinatario. Si ha recibido este correo por error, por favor, notifíquelo al remitente y bórrelo de su sistema.
                Las opiniones expresadas en este correo son las de su autor y no son necesariamente compartidas o apoyadas por UNIFIN, quien no asume aquí obligaciones ni se responsabiliza del contenido de este correo, a menos que dicha información sea confirmada por escrito por un representante legal autorizado.
                No se garantiza que la transmisión de este correo sea segura o libre de errores, podría haber sido viciada, perdida, destruida, haber llegado tarde, de forma incompleta o contener VIRUS.
                Asimismo, los datos personales, que en su caso UNIFIN pudiera recibir a través de este medio, mantendrán la seguridad y privacidad en los términos de la Ley Federal de Protección de Datos Personales; para más información consulte nuestro <a href="https://www.unifin.com.mx/aviso-de-privacidad" target="_blank">Aviso de Privacidad</a>  publicado en <a href="http://www.unifin.com.mx/" target="_blank">www.unifin.com.mx</a>
              </span>
            </p>';

        return $mailHTML;

    }

    public function sendEmailCambioRazonSocial( $emails_address,$body_correo ){

        try{
            global $app_list_strings;
            $mailer = MailerFactory::getSystemDefaultMailer();
            $mailTransmissionProtocol = $mailer->getMailTransmissionProtocol();
            $mailer->setSubject('UNIFIN CRM - Cambio de valores en cuenta con mismo RFC');
            $body = trim($body_correo);
            $mailer->setHtmlBody($body);
            $mailer->clearRecipients();
            for ($i=0; $i < count($emails_address); $i++) {
                $GLOBALS['log']->fatal("AGREGANDO CORREOS DESTINATARIOS: ".$emails_address[$i]);
                $mailer->addRecipientsTo(new EmailIdentity($emails_address[$i], $emails_address[$i]));
            }
            $result = $mailer->send();

        } catch (Exception $e){
            $GLOBALS['log']->fatal("Exception: No se ha podido enviar correo al email ");
            $GLOBALS['log']->fatal(print_r($e,true));

        }

    }

    public function creaCaso($idCuenta){
        $GLOBALS['log']->fatal('GENERA CASO RELACIONADO');
        $plataforma = $_SESSION['platform'];
        $idKarla = 'd51dd49e-b1e6-572f-d6de-5654bfeb8b3e';
        $idSamuel = '92b04f7d-e547-9d4f-c96a-5a31da014bdd';
        $asunto = 'Cambio de información con mismo RFC';
        // 8 - Crédito Uniclick, 1 - Arrendamiento
        $producto = ( $plataforma == 'cvimUniclick' || $plataforma == 'UNICLICK' ) ?  '8' : '1';
        $tipo = '8'; // Modificación de datos
        $subtipo = '48'; // Actualización de datos de contacto
        $prioridad = 'P4'; // Urgente
        $status = '1'; // No iniciado
        $asignado = ( $plataforma == 'cvimUniclick' || $plataforma == 'UNICLICK' ) ? $idSamuel : $idKarla;
        $area_interna = ( $plataforma == 'cvimUniclick' || $plataforma == 'UNICLICK' ) ?  'Uniclick': 'Credito'; // Agregar id de uniclick

        $caso = BeanFactory::newBean('Cases');
        $caso->name = $asunto;
        $caso->producto_c = $producto;
        $caso->type = $tipo;
        $caso->subtipo_c = $subtipo;
        $caso->priority = $prioridad;
        $caso->status = $status;
        $caso->assigned_user_id = $asignado;
        $caso->account_id = $idCuenta;
        $caso->area_interna_c = $area_interna;

        //Fix: Añade bandera para que al crear caso, la validación de asignación únicamente se aplique en cambio de razón social o direccion fiscal
        $caso->valida_cambio_fiscal_c = 1;

        $caso->save();
    }

    public function estableceRegimenesFiscalesSAT($bean = null, $event = null, $args = null){

        //Cuando se detecta cambio en el campo que guarda el json con la estructura de los diferentes regimenes fiscales,
        //automáticamente se actualizan los valores en el campo regimen_fiscal_sat_c para mantener sincronizados los valores con el campo custom (account_regimenes_fiscal)
        if( $bean->fetched_row['regimenes_fiscal_sat_c'] !== $bean->regimenes_fiscal_sat_c &&  $bean->regimenes_fiscal_sat_c !== ""){
            $GLOBALS['log']->fatal("Regimen Fiscal Cambia, Procede a establecer valor en regimen_fiscal_sat_c");
            $regimenes = json_decode( $bean->regimenes_fiscal_sat_c , true );
            $string_regimenes = '';
            if( count($regimenes) > 0 ){
                for ($i=0; $i < count($regimenes); $i++) {

                    $string_regimenes .= "^" . $regimenes[$i]['code']."^,";
                }

                //Elimina la última coma (,) de la cadena
                $string_regimenes_clean = substr($string_regimenes, 0, -1);

                $bean->regimen_fiscal_sat_c = $string_regimenes_clean;

            }
        }

    }

    public function setEmailPrincipal( $bean = null, $event = null, $args = null ){

        $newPrimaryEmail = $bean->email1;

        $GLOBALS['log']->fatal("EVALUANDO EMAIL PRINCIPAL");

        if ( $GLOBALS['service']->platform != 'base' ) {
            if( $bean->fetched_row['email1'] != $bean->email1 ){
                $GLOBALS['log']->fatal("El email cambió");
                
                // Recuperar todas las direcciones de email asociadas
                $existingEmails = $bean->emailAddress->getAddressesByGUID($bean->id, 'Accounts');
                //$GLOBALS['log']->fatal( print_r($existingEmails,true) );

                $emailAddresses = [];

                // Verificar si el nuevo email ya existe y guardarlo como principal
                $emailExists = false;
                foreach ($existingEmails as &$email) {
                    if ($email['email_address'] === $newPrimaryEmail) {
                        $email['primary_address'] = true;
                        $emailExists = true;
                    } else {
                        $email['primary_address'] = false; // Marcar los demás como secundarios
                    }
                    $emailAddresses[] = $email;
                }

                // Si el nuevo email no existe, agrégalo como el principal
                if (!$emailExists) {
                    $emailAddresses[] = [
                        'email_address' => $newPrimaryEmail,
                        'primary_address' => true,
                        'invalid_email' => false,
                        'opt_out' => false,
                    ];
                }

                // Asignar el nuevo conjunto de correos electrónicos a la cuenta
                $bean->emailAddress->addresses = $emailAddresses;
                $bean->emailAddress->save($bean->id, $bean->module_name);


            }
        }

        /*
          Evaluar cambios a Onboarding:
          Detecta cambios en; Teléfono móvil, Correo, Apellidos, Nombre
          Sólo se ejecuta con Régimen fiscal; PF y PFAE
        */
        
        if($GLOBALS['service']->platform == 'base' && $bean->tipodepersona_c != 'Persona Moral'){
          //Recupera datos actuales
          //$GLOBALS['log']->fatal("Correo viejo: ".$bean->fetched_row['email1']);
          //$GLOBALS['log']->fatal("Correo nuevo: ".$bean->email[0]['email_address']);
          //$GLOBALS['log']->fatal(print_r($bean->email,true));
          $email = $bean->email[0]['email_address'];
          $phone = '';
          $nombre = $bean->primernombre_c;
          $aPaterno = $bean->apellidopaterno_c;
          $aMaterno = $bean->apellidomaterno_c;
          $cambiaTelefono = false;
          
          //Recupera teléfono celular
          foreach ($bean->account_telefonos as $a_telefono) {
              if($a_telefono['tipotelefono'] == '3'){
                if (!empty($a_telefono['id'])) {
                  $telefono = BeanFactory::getBean('Tel_Telefonos', $a_telefono['id']);
                  $cambiaTelefono = ($telefono->telefono != $a_telefono['telefono']) ? true : $cambiaTelefono;
                } else {
                  $cambiaTelefono = true;
                }
                $phone = $a_telefono['telefono'];  
              }
          }
          
          //Valida cambio de información
          if( $bean->fetched_row['email1'] != $email || $bean->fetched_row['primernombre_c'] != $nombre || $bean->fetched_row['apellidopaterno_c'] != $aPaterno || $bean->fetched_row['apellidomaterno_c'] != $aMaterno || $cambiaTelefono){
            $GLOBALS['log']->fatal("Identifica cambios para enviar a Onboarding");
            global $sugar_config, $db;
            
            //Recupera Lead & PO asociados
            $idLead = '';
            $idPO = '';
            
            //Recupera Lead
            $queryL = "select id,account_id
                  from leads
                  where deleted=0
                  and account_id='{$bean->id}'
                  order by date_modified desc
                  limit 1;";
            $queryResultL = $db->query($queryL);
            while ($row = $db->fetchByAssoc($queryResultL)) {
                $idLead = $row['id_c'] ;
            }
            if(!empty($idLead)){
              //Obtiene bean de Lead
              $beanLead = BeanFactory::retrieveBean('Leads', $idLead, array('disable_row_level_security' => true));
              if (!empty($beanLead)) {
                  //Compara datos por actualizar
                  if( $bean->email1 != $beanLead->email1 || $bean->primernombre_c != $beanLead->nombre_c  || $bean->apellidopaterno_c != $beanLead->apellido_paterno_c  || $bean->apellidomaterno_c != $beanLead->apellido_materno_c ){
                    $GLOBALS['log']->fatal("Setea datos por actualizar en Lead");
                    $beanLead->email1 = $email; 
                    $beanLead->nombre_c = $bean->primernombre_c; 
                    $beanLead->apellido_paterno_c = $bean->apellidopaterno_c;
                    $beanLead->apellido_materno_c = $bean->apellidomaterno_c;
                    $beanLead->phone_mobile = ($phone!='') ? $phone : $beanLead->phone_mobile;
                    $beanLead->save();
                    $GLOBALS['log']->fatal("Lead actualizado");
                  }
              }
            }
            
            //Recupera PO
            $queryPO = "select pc.id_c
                  from prospects_cstm pc
                  inner join prospects p on p.id =pc.id_c
                  where ifnull(excluye_campana_c,0) = 0
                  and p.deleted=0
                  and pc.account_id2_c='{$bean->id}'
                  order by p.date_modified desc
                  limit 1;";
            $queryResultPO = $db->query($queryPO);
            while ($row = $db->fetchByAssoc($queryResultPO)) {
                $idPO = $row['id_c'] ;
            }
            if(!empty($idPO)){
              //Obtiene bean de PO
              $beanPO = BeanFactory::retrieveBean('Prospects', $idPO, array('disable_row_level_security' => true));
              if (!empty($beanPO)) {
                //Compara datos por actualizar
                if( $bean->email1 != $beanPO->email1 || $bean->primernombre_c != $beanPO->nombre_c  || $bean->apellidopaterno_c != $beanPO->apellido_paterno_c  || $bean->apellidomaterno_c != $beanPO->apellido_materno_c ){
                  $GLOBALS['log']->fatal("Setea datos por actualizar en PO");
                  $beanPO->email1 = $email; 
                  $beanPO->nombre_c = $bean->primernombre_c; 
                  $beanPO->apellido_paterno_c = $bean->apellidopaterno_c;
                  $beanPO->apellido_materno_c = $bean->apellidomaterno_c;
                  $beanPO->phone_mobile = ($phone!='') ? $phone : $beanPO->phone_mobile;
                  $beanPO->save();
                  $GLOBALS['log']->fatal("PO actualizado");
                  
                  //Genera petición para actualizar datos Onboarding
                  $callApi = new UnifinAPI();
                  $hostOnboarding = $sugar_config['urlOnboarding'];
                  $tokenOnboarding = $sugar_config['tokenOnboarding'];

                  $urlOnboarding = $hostOnboarding.'/api/crm/contact/'.$idPO;

                  $body = array(
                      "name" => $nombre,
                      "last_name" => $aPaterno,
                      "mother_last_name" => $aMaterno,
                      "email" => $email,
                      "phone" => ($phone!='') ? $phone : $beanPO->phone_mobile,
                  );
                  
                  $GLOBALS['log']->fatal("Request PO Onboarding");
                  $GLOBALS['log']->fatal($urlOnboarding);
                  $GLOBALS['log']->fatal(print_r( $body,true ));

                  $resp = $callApi->postOnboardingPO($urlOnboarding, $tokenOnboarding ,$body);

                  $GLOBALS['log']->fatal("Response PO Onboarding");
                  $GLOBALS['log']->fatal(print_r($resp, true));
                }    
              }
            }
          }
        }        


    }

}
