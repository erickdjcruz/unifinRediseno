<?php

/**
 * Created by Tactos.
 * User: Tactos
 * Date: 02/05/22
 * Time: 10:15 AM
 */
class clean_fields_class
{

    public function textToUppperCase($bean = null, $event = null, $args = null)
    {
        //$GLOBALS['log']->fatal('CONVIERTO A MAYUSCULAS');
        $moduleRequest = isset($_REQUEST['module']) ? $_REQUEST['module'] : '';
        if ($moduleRequest != 'Import') {
            foreach ($bean as $field => $value) {
                $fieldName = isset($bean->field_defs[$field]['name']) ? $bean->field_defs[$field]['name'] : '';
                $fieldType = isset($bean->field_defs[$field]['type']) ? $bean->field_defs[$field]['type'] : '';
                if ($fieldName != 'nombre_de_cargar_c' && $fieldName != 'resultado_de_carga_c' && $fieldName != 'id_director_vobo_c') {

                    if ($fieldType == 'varchar') {
                        $value = mb_strtoupper($value, "UTF-8");
                        $bean->$field = $value;
                    }
                    if ($fieldName == 'name') {
                        $value = mb_strtoupper($value, "UTF-8");
                        $bean->$field = $value;
                    }
                }
            }
        }

        //Validación de PM - Nombre empresa
        if ($bean->regimen_fiscal_c == '3' && !empty($bean->nombre_c) && empty($bean->nombre_empresa_c)) {
            $bean->nombre_empresa_c = $bean->nombre_c;
            $bean->name_c = $bean->nombre_c;
            $bean->nombre_c = '';
        }
    }


    public function setCleanName($bean = null, $event = null, $args = null)
    {
        // $GLOBALS['log']->fatal('QUITO ESPACIOS Y REEMPLAZO POR NUEVOS VALORES');

        global $db;
        global $app_list_strings, $current_user; //Obtención de listas de valores
        $idCuenta = $bean->id;
        //$GLOBALS['log']->fatal('Limpia espacios');
        //Se crean variables que limpien los excesos de espacios en los campos establecidos.
        $limpianame = preg_replace('/\s\s+/', ' ', $bean->fullname); // PENDIENTE
        $limpianombre = preg_replace('/\s\s+/', ' ', $bean->nombre_c);
        $limpiaapaterno = preg_replace('/\s\s+/', ' ', $bean->apellido_paterno_c);
        $limpiamaterno = preg_replace('/\s\s+/', ' ', $bean->apellido_materno_c);
        $limpiarazon = preg_replace('/\s\s+/', ' ', $bean->nombre_empresa_c); # prendiente
        //$limpianomcomercial= preg_replace('/\s\s+/', ' ', $bean->nombre_empresa_c);

        //Actualiza valores limpios a los campos de la Cuenta
        $bean->fullname = $limpianame;
        $bean->nombre_c = $limpianombre;
        $bean->apellido_paterno_c = $limpiaapaterno;
        $bean->apellido_materno_c = $limpiamaterno;
        $bean->nombre_empresa_c = $limpiarazon;

        //Consumir servicio de cleanName, declarado en custom api
        require_once("custom/clients/base/api/cleanName.php");
        $apiCleanName= new cleanName();
        $body=array('name'=>$bean->last_name); //Se identificó que sobre el campo last_name se concatena el nombre del lead
        $response=$apiCleanName->getCleanName(null,$body);
        if ($response['status']=='200') {
            $bean->clean_name_c = $response['cleanName'];
        }
    }


    public function ExistenciaEnCuentas($bean = null, $event = null, $args = null)
    {
        $idPadre = "";

        $servicio= isset($GLOBALS['service']->platform)?$GLOBALS['service']->platform:"base";
        if(!$bean->excluye_campana_c){
                //$GLOBALS['log']->fatal("servicio",$servicio);
                if ($servicio== "base" || $servicio == "mobile") {

                    // omitir si el leads es cancelado no se haga nada o si ya esta convertido se brinca la validación
                    if ($bean->estatus_po_c != 3 && $bean->estatus_po_c != 4) {

                        $idPadre = $this->createCleanName($bean->leads_leads_1_name);
                        //$GLOBALS['log']->fatal("cOMIENZA A vALIDAR dUPLICADO ");
                        //$GLOBALS['log']->fatal("para moral " . $bean->clean_name_c);
                        //$GLOBALS['log']->fatal("para id " . $bean->id);
                        $exprNumerica = "/^[0-9]*$/";
                        /**********************VALIDACION DE CAMPOS PB ID Y DUNS ID DEBEN SER NUMERICOS*********************/
                        if (!preg_match($exprNumerica, $bean->pb_id_c)) {
                            $bean->pb_id_c = "";
                        }
                        if (!preg_match($exprNumerica, $bean->duns_id_c)) {
                            $bean->duns_id_c = "";
                        }

                        //$duplicateproductMessageAccounts = 'Ya existe una cuenta con la misma información';
                        /*
                        $sql = new SugarQuery();
                        $sql->select(array('id', 'clean_name'));
                        $sql->from(BeanFactory::newBean('Accounts'), array('team_security' => false));
                        $sql->where()->queryAnd()->equals('clean_name',$bean->clean_name_c)->notEquals('id', $bean->id);
                        */
                        //$sql->where()->equals('clean_name', $bean->clean_name_c);
                        //$sql->where()->notEquals('id', $bean->id);

                        $query = "SELECT pc.id_c, pc.clean_name_c FROM prospects_cstm pc inner join prospects p
                        on p.id = pc.id_c WHERE pc.clean_name_c = '{$bean->clean_name_c}'
                        AND pc.id_c <> '{$bean->id}' AND p.deleted =0;";
                        $results = $GLOBALS['db']->query($query);

                        $queryL = "SELECT lc.id_c, lc.clean_name_c FROM leads_cstm lc JOIN leads l
                        on l.id = lc.id_c WHERE lc.clean_name_c = '{$bean->clean_name_c}'
                        AND lc.id_c <> '{$bean->lead_id}' AND l.deleted =0";
                        $resultsL = $GLOBALS['db']->query($queryL);
                        $countLead = $resultsL->num_rows;

                        //$result = $sql->execute();
                        //$count = count($result);
                        $count = $results->num_rows;
                        $GLOBALS['log']->fatal("pcount" . $count);
                        $GLOBALS['log']->fatal("countLeads" . $countLead);
                        /************SUGARQUERY PARA VALIDAR IMPORTACION DE REGISTROS SI TIENEN IGUAL LOS MISMOS VALORES DE CLEAN_NAME O PB_ID O DUNS_ID*********/
                        $duplicateproductMessageLeads = 'El registro que intentas guardar ya existe como Lead/Prospecto.';
                    
                        //Get the Name of the account
                    // $Leadone = $resultLead[0];

                        $idExistenteLead = $countLead>0? $resultLead[0]['id']:"";

                        //$GLOBALS['log']->fatal("c---- " . $countLead . "  " . $count);
                        if ($count > 0 || $countLead > 0) {
                            if ($_REQUEST['module'] != 'Import') {

                                throw new SugarApiExceptionInvalidParameter($duplicateproductMessageLeads);
                            } else {
                                $bean->deleted = 1;
                                $bean->resultado_de_carga_c = 'Registro Duplicado';
                            }
                        } else {
                            $bean->resultado_de_carga_c = 'Registro Exitoso';
                        }
                        $fechaCarga = date("Ymd");
                        //$GLOBALS['log']->fatal("fecha hoy ". $fechaCarga . " valor campo ". $bean->nombre_de_cargar_c);
                        $requestModule = isset($_REQUEST['module']) ? $_REQUEST['module'] : '';
                        $bean->nombre_de_cargar_c = ($bean->nombre_de_cargar_c == "" && $requestModule == 'Import') ? "Carga_" . $fechaCarga : $bean->nombre_de_cargar_c;
                    }
                }
            }        
    }

    public function createCleanName($nameCuenta)
    {
        global $db;
        $cleanName = '';
        //Consumir servicio de cleanName, declarado en custom api
        require_once("custom/clients/base/api/cleanName.php");
        $apiCleanName= new cleanName();
        $body=array('name'=>$nameCuenta); //Se identificó que sobre el campo last_name se concatena el nombre del lead
        $response=$apiCleanName->getCleanName(null,$body);
        if ($response['status']=='200') {
            $cleanName = $response['cleanName'];
        }

        $sqlLead = new SugarQuery();
        $sqlLead->select(array('id', 'clean_name_c'));
        $sqlLead->from(BeanFactory::newBean('Leads'), array('team_security' => false));
        $sqlLead->where()->equals('clean_name_c', $cleanName);
        // $sqlLead->where()->notEquals('id', $bean->id);
        $sqlLead->where()->equals('deleted', '0');
        $resultLead = $sqlLead->execute();
        $countLead = count($resultLead);

        $idPadre = $countLead>0? $resultLead[0]['id']:"";

        return $idPadre;

    }

    public function checkFormatPhones($bean = null, $event = null, $args = null){

        $phone_work = $bean->phone_work;
        $phone_home = $bean->phone_home;
        $phone_mobile = $bean->phone_mobile;

        if( $phone_work != "" ){
            if( !$this->validarNumeros($phone_work) ){ 
                throw new SugarApiExceptionInvalidParameter("Teléfono de trabajo no válido. El teléfono debe tener únicamente números y máximo 10 dígitos");
            }
        }

        if( $phone_home != "" ){
            if( !$this->validarNumeros($phone_home) ){
                throw new SugarApiExceptionInvalidParameter("Teléfono de casa no válido. El teléfono debe tener únicamente números y máximo 10 dígitos");
            }
        }

        if( $phone_mobile != "" ){
            if( !$this->validarNumeros($phone_mobile) ){
                throw new SugarApiExceptionInvalidParameter("Teléfono celular no válido. El teléfono debe tener únicamente números y máximo 10 dígitos");
            }
        }

    }

    public function validarNumeros($input) {
        // Verifica si el string contiene solo números y tiene una longitud máxima de 10 dígitos
	return true;
        if (preg_match('/^\d{1,10}$/', $input)) {
            return true;
        } else {
            return false;
        }
    }

}
