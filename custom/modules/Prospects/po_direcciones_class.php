<?php
if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

require_once("custom/Levementum/UnifinAPI.php");

class po_direcciones_class
{
    public function po_direcciones_function($bean, $event, $args)
    {

            global $current_user, $db;
            $current_id_list = array();

            if ($_REQUEST['module'] != 'Import' && $_SESSION['platform'] != 'unifinAPI') {

                foreach ($bean->prospects_direcciones as $direccion_row) {

                    $direccion = BeanFactory::getBean('dire_Direccion', $direccion_row['id']);
                    $id_sepomex_anterior=$direccion->dir_sepomex_dire_direcciondir_sepomex_ida;

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
                    if (count($direccion_row['tipodedireccion']) > 0) {
                        $tipo_string .= '^' . $direccion_row['tipodedireccion'][0] . '^';
                        for ($i = 1; $i < count($direccion_row['tipodedireccion']); $i++) {
                            $tipo_string .= ',^' . $direccion_row['tipodedireccion'][$i] . '^';
                        }
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

                    // populate related prospect id
                    $direccion->prospects_dire_direccion_1prospects_ida = $bean->id;

                    $id_postal=$direccion_row['postal'];
                    $GLOBALS['log']->fatal("POSTAL: ".$id_postal);
                    $query_sepomex="SELECT * FROM dir_sepomex WHERE id='{$id_postal}'";
                    $GLOBALS['log']->fatal("QUERY SEPOMEX");
                    $GLOBALS['log']->fatal($query_sepomex);
                    $result_sepomex = $db->query($query_sepomex);
                    while ($row = $GLOBALS['db']->fetchByAssoc($result_sepomex)) {
                        $namePais=$row['pais'];
                        $idPais=$row['id_pais'];
                        $nameCP=$row['codigo_postal'];
                        $nameEstado=$row['estado'];
                        $idEstado=$row['id_estado'];
                        $nameCiudad=$row['ciudad'];
                        $idCiudad=$row['id_ciudad'];
                        $nameColonia=$row['colonia'];
                        $idColonia=$row['id_colonia'];
                        $nameMunicipio=$row['municipio'];
                        $idMunicipio=$row['id_municipio'];
                    }

                    $direccion_completa = $direccion_row['calle'] . " " . $direccion_row['numext'] . " " . ($direccion_row['numint'] != "" ? "Int: " . $direccion_row['numint'] : "") . ", Colonia " . $nameColonia. ", Municipio " . $nameMunicipio;
                    $direccion->name = $direccion_completa;

                    //Se utiliza campo descripcion de la direccion para ya no crear campos nuevos solo para los id
                    $direccion->description="{$idPais}|{$idEstado}|{$idCiudad}|{$idMunicipio}|{$idColonia}";

                    //Se genera relación entre la dirección y Sepomex
                    $direccion->dir_sepomex_dire_direcciondir_sepomex_ida=$direccion_row['postal'];

                    
                    $GLOBALS['log']->fatal(__FILE__ . " - " . __CLASS__ . "->" . __FUNCTION__ . " <" . $current_user->user_name . "> : DIRECCION NOMBRE" . $direccion_completa);
                    $current_id_list[] = $direccion->id;
                    if ($new) {
                        $direccion->save();
                    }
                }
                //retrieve all related records
                $bean->load_relationship('prospects_dire_direccion_1');
                foreach ($bean->prospects_dire_direccion_1->getBeans() as $a_direccion) {
                    if (!in_array($a_direccion->id, $current_id_list)) {
                        //$a_direccion->mark_deleted($a_direccion->id);
                    }
                }
            }


    }
}
