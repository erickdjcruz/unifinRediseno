<?php
/**
 * Created by PhpStorm.
 * User: salvadorlopez
 * Date: 23/12/19
 * Time: 10:20
 */
class AuditTable
{
    function insertAuditFields($bean = null, $event = null, $args = null){

        global $current_user;
        global $app_list_strings;
        $date= TimeDate::getInstance()->nowDb();

        //Valida cambio de puesto
        if($args['isUpdate']){
            //Valida cambio en el campo puestousuario
            if($bean->fetched_row['puestousuario_c'] != $bean->puestousuario_c){

                $id_u_audit=create_guid();
                $tipo=$this->getFieldType($bean,'puestousuario_c');
                $anterior=$bean->fetched_row["puestousuario_c"];
                $actual=$bean->puestousuario_c;
                $sqlInsert="insert into users_audit (id, parent_id, date_created, created_by, field_name, data_type, before_value_string, after_value_string, before_value_text, after_value_text, event_id, date_updated)
                  VALUES ('{$id_u_audit}', '{$bean->id}', '{$date}', '{$current_user->id}', 'puestousuario_c', '{$tipo}', '{$anterior}', '{$actual}', '{$app_list_strings["puestousuario_c_list"][$anterior]}', '{$app_list_strings["puestousuario_c_list"][$actual]}', '1', '{$date}')";
                $GLOBALS['db']->query($sqlInsert);

            }
            //Valida cambio en el campo informs_to_id
            if($bean->fetched_row['reports_to_id'] != $bean->reports_to_id){

                $id_u_audit=create_guid();
                $tipo=$this->getFieldType($bean,'reports_to_id');
                $anterior=$bean->fetched_row["reports_to_id"];
                $actual=$bean->reports_to_id;
                $sqlInsert="insert into users_audit (id, parent_id, date_created, created_by, field_name, data_type, before_value_string, after_value_string, before_value_text, after_value_text, event_id, date_updated)
                  VALUES ('{$id_u_audit}', '{$bean->id}', '{$date}', '{$current_user->id}', 'reports_to_id', '{$tipo}', '{$anterior}', '{$actual}', '{$app_list_strings["puestousuario_c_list"][$anterior]}', '{$app_list_strings["puestousuario_c_list"][$actual]}', '1', '{$date}')";
                $GLOBALS['db']->query($sqlInsert);

            }

            //Valida cambio en el campo Id UNICS
            if($bean->fetched_row['tct_id_unics_txf_c'] != $bean->tct_id_unics_txf_c){

                $id_u_audit=create_guid();
                $tipo=$this->getFieldType($bean,'tct_id_unics_txf_c');
                $anterior=$bean->fetched_row["tct_id_unics_txf_c"];
                $actual=$bean->tct_id_unics_txf_c;
                $sqlInsert="insert into users_audit (id, parent_id, date_created, created_by, field_name, data_type, before_value_string, after_value_string, before_value_text, after_value_text, event_id, date_updated)
                  VALUES ('{$id_u_audit}', '{$bean->id}', '{$date}', '{$current_user->id}', 'tct_id_unics_txf_c', '{$tipo}', '{$anterior}', '{$actual}', '{$anterior}', '{$actual}', '1', '{$date}')";
                $GLOBALS['db']->query($sqlInsert);

            }

            //Valida cambio en el campo Id UNI2
            if($bean->fetched_row['tct_id_uni2_txf_c'] != $bean->tct_id_uni2_txf_c){

                $id_u_audit=create_guid();
                $tipo=$this->getFieldType($bean,'tct_id_uni2_txf_c');
                $anterior=$bean->fetched_row["tct_id_uni2_txf_c"];
                $actual=$bean->tct_id_uni2_txf_c;
                $sqlInsert="insert into users_audit (id, parent_id, date_created, created_by, field_name, data_type, before_value_string, after_value_string, before_value_text, after_value_text, event_id, date_updated)
                  VALUES ('{$id_u_audit}', '{$bean->id}', '{$date}', '{$current_user->id}', 'tct_id_uni2_txf_c', '{$tipo}', '{$anterior}', '{$actual}', '{$anterior}', '{$actual}', '1', '{$date}')";
                $GLOBALS['db']->query($sqlInsert);

            }

            //Valida cambio en el campo ID Active Directory
            if($bean->fetched_row['id_active_directory_c'] != $bean->id_active_directory_c){

                $id_u_audit=create_guid();
                $tipo=$this->getFieldType($bean,'id_active_directory_c');
                $anterior=$bean->fetched_row["id_active_directory_c"];
                $actual=$bean->id_active_directory_c;
                $sqlInsert="insert into users_audit (id, parent_id, date_created, created_by, field_name, data_type, before_value_string, after_value_string, before_value_text, after_value_text, event_id, date_updated)
                  VALUES ('{$id_u_audit}', '{$bean->id}', '{$date}', '{$current_user->id}', 'id_active_directory_c', '{$tipo}', '{$anterior}', '{$actual}', '{$anterior}', '{$actual}', '1', '{$date}')";
                $GLOBALS['db']->query($sqlInsert);

            }
        
            //Valida cambio en el campo Tipo Producto
            if($bean->fetched_row['tipodeproducto_c'] != $bean->tipodeproducto_c){

                $id_u_audit=create_guid();
                $tipo=$this->getFieldType($bean,'tipodeproducto_c');
                $anterior=$bean->fetched_row["tipodeproducto_c"];
                $actual=$bean->tipodeproducto_c;
                $sqlInsert="insert into users_audit (id, parent_id, date_created, created_by, field_name, data_type, before_value_string, after_value_string, before_value_text, after_value_text, event_id, date_updated)
                  VALUES ('{$id_u_audit}', '{$bean->id}', '{$date}', '{$current_user->id}', 'tipodeproducto_c', '{$tipo}', '{$anterior}', '{$actual}', '{$anterior}', '{$actual}', '1', '{$date}')";
                $GLOBALS['db']->query($sqlInsert);

            }

            //Valida cambio en el campo Fecha de Baja
            if($bean->fetched_row['fecha_baja_c'] != $bean->fecha_baja_c){

                $id_u_audit=create_guid();
                $tipo=$this->getFieldType($bean,'fecha_baja_c');
                $anterior=$bean->fetched_row["fecha_baja_c"];
                $actual=$bean->fecha_baja_c;
                $sqlInsert="insert into users_audit (id, parent_id, date_created, created_by, field_name, data_type, before_value_string, after_value_string, before_value_text, after_value_text, event_id, date_updated)
                  VALUES ('{$id_u_audit}', '{$bean->id}', '{$date}', '{$current_user->id}', 'fecha_baja_c', '{$tipo}', '{$anterior}', '{$actual}', '{$anterior}', '{$actual}', '1', '{$date}')";
                $GLOBALS['db']->query($sqlInsert);

            }

            //Valida cambio en el campo Posicion Operativa
            if($bean->fetched_row['posicion_operativa_c'] != $bean->posicion_operativa_c){

              $id_u_audit=create_guid();
              $tipo=$this->getFieldType($bean,'posicion_operativa_c');
              $anterior=$bean->fetched_row["posicion_operativa_c"];
              $actual=$bean->posicion_operativa_c;
              $sqlInsert="insert into users_audit (id, parent_id, date_created, created_by, field_name, data_type, before_value_string, after_value_string, before_value_text, after_value_text, event_id, date_updated)
                VALUES ('{$id_u_audit}', '{$bean->id}', '{$date}', '{$current_user->id}', 'posicion_operativa_c', '{$tipo}', '{$anterior}', '{$actual}', '{$anterior}', '{$actual}', '1', '{$date}')";
              $GLOBALS['db']->query($sqlInsert);

            }
            
            //Valida cambio en el campo Define Origen
            if($bean->fetched_row['define_origen_po_c'] != $bean->define_origen_po_c){

              $id_u_audit=create_guid();
              $tipo=$this->getFieldType($bean,'define_origen_po_c');
              $anterior=$bean->fetched_row["define_origen_po_c"];
              $actual=$bean->define_origen_po_c;
              $sqlInsert="insert into users_audit (id, parent_id, date_created, created_by, field_name, data_type, before_value_string, after_value_string, before_value_text, after_value_text, event_id, date_updated)
                VALUES ('{$id_u_audit}', '{$bean->id}', '{$date}', '{$current_user->id}', 'define_origen_po_c', '{$tipo}', '{$anterior}', '{$actual}', '{$anterior}', '{$actual}', '1', '{$date}')";
              $GLOBALS['db']->query($sqlInsert);

            }

        }

    }

    function getFieldType($bean,$field){
        return $bean->field_defs[$field]['type'];
    }

}
