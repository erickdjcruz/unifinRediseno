<?php
require_once 'include/api/SugarApiException.php';
class Validate_Email
{
    public function existsEmail($bean = null, $event = null, $args = null)
    {
        global $db;
        $id_publico_objetivo = $bean->id;
        $email = $bean->email1;

        if (!$bean->excluye_campana_c){

            $qEmailExists = "SELECT p.id id_po,pc.clean_name_c,e.email_address from prospects p
            inner join prospects_cstm pc on p.id=pc.id_c
            left join email_addr_bean_rel er on er.bean_id = p.id and er.deleted=0
            left join email_addresses e on e.id=er.email_address_id and e.deleted =0
            WHERE e.email_address = '{$email}' and pc.excluye_campana_c = 0";
            
            $queryResultEmail = $db->query($qEmailExists);
            $ids_po = array();
            if ($queryResultEmail->num_rows > 0) {
    
                while ($row = $GLOBALS['db']->fetchByAssoc($queryResultEmail)) {
                    $id_po = $row['id_po'];
                    $name_po = $row['clean_name_c'];
                    if( $id_po != $id_publico_objetivo ){
                        array_push( $ids_po, array( "id_po" => $id_po,"name_po" => $name_po ) );
                    }
                }
            }
            $str_link_po = "";
            if( count($ids_po) > 0 ){
                for ($i=0; $i < count($ids_po); $i++) { 
                    $str_link_po .= $ids_po[$i]["name_po"].',';
                }
                
            }
            if($db->getRowCount($queryResultEmail) > 0 && count($ids_po) > 0){
                if( $_SESSION['platform'] == 'base' ){
                    throw new SugarApiExceptionInvalidParameter('No se puede guardar el registro. El correo electrónico '.$email.' ya existe en Público Objetivo en los siguientes registros: ' .$str_link_po. ' favor de corregir');
                }
            } 
        }

    }

    public function checkUpdateEmailPO($bean = null, $event = null, $args = null){

        //Corroborar si el email cambió
        if( $bean->fetched_row['email1'] != $bean->email1 ){

            $GLOBALS['log']->fatal('CAMBIÓ EL EMAL DE PROSPECTO');

            $newEmail = $bean->email1;
            
            if( $bean->estatus_po_c == '3' ){ //Si es Convertido, actualizamos sus registros relacionados de Lead y Cuenta

                //Obtenemos id de Lead para actualizarlo
                $idLeadRelacionado = $bean->lead_id;

                if( !empty( $idLeadRelacionado ) ){
                    $GLOBALS['log']->fatal('ACTUALIZANDO EMAIL DE LEAD');
                    $GLOBALS['log']->fatal('NEW EMAIL: ');
                    $GLOBALS['log']->fatal(print_r($newEmail,true));
                    //Obtenemos bean de Lead
                    $beanLead = BeanFactory::retrieveBean('Leads', $idLeadRelacionado, array('disable_row_level_security' => true));
                    $GLOBALS['log']->fatal($beanLead->name);
                    //$beanLead->email = $newEmail;
                    //$beanLead->save();

                    //Establecer nuevo email al Lead;
                    $idCurrentEmail = $this->getCurrentIdEmail($beanLead->id, 'Leads');
                    $idNuevoEmail = $this->createRecordEmailAddr($newEmail);
                    $idEmailBeanRel = $this->setEmailAddrBeanRel( $beanLead->id, $idNuevoEmail, "Leads" );

                    $this->insertAuditRecord( 'leads', $beanLead->id, $idCurrentEmail[0], $idNuevoEmail );

                    $this->deleteCurrentRelation( $idCurrentEmail[1] );


                    //Comprobar si el Lead está Convertido, en caso de ser así, se obtiene la cuenta y a la cuenta se le actualiza el email
                    $statusLead = $beanLead->subtipo_registro_c;

                    if( $statusLead == '4' ){ //Convertido
                        //  Obtenemos id de la cuenta relacionada
                        $idCuentaRelacionada = $beanLead->account_id;

                        if( !empty( $idCuentaRelacionada ) ){
                            $GLOBALS['log']->fatal('ACTUALIZANDO EMAIL DE CUENTA');
                            $beanCuentaRelacionada = BeanFactory::retrieveBean('Accounts', $idCuentaRelacionada, array('disable_row_level_security' => true));
                            $beanCuentaRelacionada->email1 = $newEmail;

                            $beanCuentaRelacionada->save();

                        }else{
                            $GLOBALS['log']->fatal('NO HAY UNA CUENTA RELACIONADA');
                        }

                    }

                }else{
                    $GLOBALS['log']->fatal('NO HAY UN LEAD RELACIONADO');
                }


            }


        }



    }

    public function getCurrentIdEmail( $idRecord, $parentType ){

        $sqlGetPrimaryAddress = "SELECT
        email_addr_bean_rel.id id_relacion,
        email_addresses.id id_email,
         email_addr_bean_rel.bean_id,
         email_addr_bean_rel.bean_module,
         email_addresses.email_address
        FROM email_addr_bean_rel
        INNER JOIN email_addresses
         ON email_addresses.id = email_addr_bean_rel.email_address_id
         WHERE email_addr_bean_rel.deleted = 0
         AND email_addr_bean_rel.bean_id = '{$idRecord}'
         AND email_addr_bean_rel.primary_address = 1
         AND bean_module = '{$parentType}'
        AND email_addresses.deleted = 0";

        $queryResult = $GLOBALS['db']->query($sqlGetPrimaryAddress);
        $idEmail = '';
        $idRelacion = '';
        while($row = $GLOBALS['db']->fetchByAssoc($queryResult)){
            $idEmail  = $row['id_email'];
            $idRelacion = $row['id_relacion'];
        }

        $GLOBALS['log']->fatal('SE OBTIENE EMAIL PRINCIPAL: '.$idEmail." ".$idRelacion);
        return [$idEmail, $idRelacion];

    }

    public function createRecordEmailAddr($email){

        $idNewMail = create_guid();
        $date = TimeDate::getInstance()->nowDb();
        $emailUpper = strtoupper($email);

        $sqlInsertEmail = "INSERT INTO email_addresses (id,email_address,email_address_caps,invalid_email,opt_out,date_created,date_modified,deleted,confirmation_requested_on) VALUES ('{$idNewMail}','{$email}','{$emailUpper}',0,0,'{$date}','{$date}',0,NULL)";
        
        $GLOBALS['db']->query($sqlInsertEmail);
        $GLOBALS['log']->fatal('SE CREÓ REGISTRO DE EMAIL ADDRE: '.$idNewMail);
        return $idNewMail;

    }

    public function setEmailAddrBeanRel( $idRecord, $idEmail, $parentType ){

        $idEmailAddrBean = create_guid();
        $date = TimeDate::getInstance()->nowDb();

        $sqlInsertRel = "INSERT INTO email_addr_bean_rel (id,email_address_id,bean_id,bean_module,primary_address,reply_to_address,date_created,date_modified,deleted) VALUES ('{$idEmailAddrBean}','{$idEmail}','{$idRecord}','{$parentType}',1,0,'{$date}','{$date}',0)";

        $GLOBALS['db']->query($sqlInsertRel);
        $GLOBALS['log']->fatal('ESTABLECE RELACIÓN email_addr_bean_rel: '.$idEmailAddrBean);

        return $idEmailAddrBean;
    }

    public function insertAuditRecord( $module, $parent_id, $before_value, $after_value ){
        global $current_user;
        $idAudit = create_guid();
        $eventAudit = create_guid();
        $date = TimeDate::getInstance()->nowDb();

        $sqlInsert = "INSERT INTO {$module}_audit (id, parent_id, date_created, created_by, field_name, data_type, before_value_string, after_value_string, before_value_text, after_value_text, event_id, date_updated)
        VALUES ('{$idAudit}', '{$parent_id}', '{$date}', '{$current_user->id}', 'email', 'email', '{$before_value}', '{$after_value}', '', '', '{$eventAudit}', '{$date}')";

        $GLOBALS['db']->query($sqlInsert);
    
    }

    public function deleteCurrentRelation( $idRelation ){
        
        $sqlDeleteRelation = "UPDATE email_addr_bean_rel SET deleted = '1' WHERE (id = '{$idRelation}')";
        $GLOBALS['log']->fatal('SE BORRA RELACIÓN');
        $GLOBALS['log']->fatal($sqlDeleteRelation);
        

        $GLOBALS['db']->query($sqlDeleteRelation);
    }
}
