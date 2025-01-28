<?php
/**
 * Created by PhpStorm.
 * User: Jorge
 * Date: 6/4/2015
 * Time: 1:34 PM
 */
require_once("custom/Levementum/UnifinAPI.php");
class Tel_Hooks{

    /** BEGIN CUSTOMIZATION: jgarcia@levementum.com 6/4/2015 Description: get the last sequencia number (MAX) related to the person, and adds 1 to it then it stores the telefono record.*/
    public function setSequencia($bean = null, $event = null, $args = null){
      if($bean->accounts_tel_telefonos_1accounts_ida != null && empty($bean->secuencia)){
          global $db;
          $query = "SELECT MAX(secuencia)
            FROM tel_telefonos
            left join accounts_tel_telefonos_1_c on accounts_tel_telefonos_1tel_telefonos_idb=tel_telefonos.id
            WHERE accounts_tel_telefonos_1accounts_ida = '{$bean->accounts_tel_telefonos_1accounts_ida}'
            and tel_telefonos.deleted=0
            and accounts_tel_telefonos_1_c.deleted=0
            limit 1;";

          $queryResult = $db->getOne($query);
          $bean->secuencia = 0;
          $total = $queryResult + 1;
          $bean->secuencia = $total;
      }
    }
    /* END CUSTOMIZATION */

    /**
     * @author bdekoning@levementum.com
     * @date 6/9/15
     * @brief Sanitizes the Telefono field to 10 digits
     *
     * @param Tel_Telefonos $bean
     * @param $event
     * @param $args
     *
     * before_save
     */
    public function sanitizeTelefono(&$bean, $event, $args)
    {
        // strip non-numeric characters
        $phone_sanitized = preg_replace('/[^0-9+]/', '', $bean->telefono);
        // truncate to 10 digits
        $phone_sanitized = substr($phone_sanitized, 0, 13);

        $bean->telefono = $phone_sanitized;

        //SE AGREGA TELEFONO AL NAME
        $bean->name = $bean->telefono;
    }

    public function insertaComunicaciónUNICS($bean = null, $event = null, $args = null){
    	 //$GLOBALS['log']->fatal('>>>>Entramos a insertaComunicaciónUNICS<<<<<<<');
         global $db;
         $cliente = false;

         $query = $query = <<<SQL
SELECT idcliente_c, sincronizado_unics_c FROM accounts_cstm
WHERE id_c = '{$bean->accounts_tel_telefonos_1accounts_ida}'
SQL;
         $queryResult = $db->query($query);
         while($row = $db->fetchByAssoc($queryResult))
         {
			if (!empty($row['idcliente_c']) && $row['idcliente_c'] > 0 && $row['idcliente_c'] != '' && $row['sincronizado_unics_c'] == '1') {
                 $cliente = true;
             }
         }

        if($cliente == true) {
            $callApi = new UnifinAPI();
            //only for new records
            if ($_SESSION['estado'] == 'insertando') {
                $tel = $callApi->insertaComunicación($bean, 'insertando');
                $_SESSION['estado'] = '';
                //$GLOBALS['log']->fatal('>>>>Manda Insertado');
            } elseif ($_SESSION['estado'] == 'actualizando') {
                $tel = $callApi->insertaComunicación($bean, 'actualizando');
                $_SESSION['estado'] = '';
                //$GLOBALS['log']->fatal('>>>>Manda Actualizado');
            }
        }
    }

    public function detectaEstado ($bean = null, $event = null, $args = null){
        global $current_user;
        //$GLOBALS['log']->fatal('>>>>$args: '.$args['isUpdate']);
        if ($args['isUpdate']!=1) {
            $_SESSION['estado'] = 'insertando';
        }else{
            $_SESSION['estado'] = 'actualizando';
         }
        //$GLOBALS['log']->fatal('>>>Fetched row: '.$bean->fetched_row['id']);
        $GLOBALS['log']->fatal(__FILE__." - ".__CLASS__."->".__FUNCTION__." <".$current_user->user_name."> : ESTADO: $_SESSION[estado] ");
    }

    public function checkFormatPhones($bean = null, $event = null, $args = null){

        $phone = $bean->telefono;

        if( $phone != "" ){
            if( !$this->validarNumeros($phone) ){
                throw new SugarApiExceptionInvalidParameter("Teléfono no válido. El teléfono debe tener únicamente números y máximo 10 dígitos");
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
