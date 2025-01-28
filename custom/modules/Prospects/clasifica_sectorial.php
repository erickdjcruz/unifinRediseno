<?php
// if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');


// class clasifica_sectorial_class
// {
//     public function clasifica_sectorial_function($bean, $event, $args)
//     {
//         //Llena el campo de Macro Sector
//         global $db;
//         if(!empty($bean->pb_grupo_c) && empty($bean->actividad_economica_c)){
//           	$query = "SELECT DISTINCT id_cnbv_macrosector FROM catalogo_clasificacion_sectorial_pb WHERE id_pb_grupo = '{$bean->pb_grupo_c}'";
//           	$queryResult = $db->query($query);
//             $row = $db->fetchByAssoc($queryResult);
//           	$bean->macrosector_c = $row['id_cnbv_macrosector'];
//         }
//         //Manda a llamar función para llenar Subsector
//         $this->llenaSubsector($bean,$event,$args);
//         if(!empty($bean->actividad_economica_c) && empty($bean->sector_economico_c)) $this->llenaSectorial($bean,$event,$args);
//         if(!empty($bean->fetched_row['id']) && $bean->fetched_row['actividad_economica_c'] != $bean->actividad_economica_c && !empty($bean->actividad_economica_c)) $this->llenaSectorial($bean,$event,$args);
//     }

//     public function llenaSubsector($bean, $event, $args){
//         //Llena el campo de Subsector
//         global $db;
//         if(!empty($bean->pb_grupo_c) && empty($bean->actividad_economica_c)){
//             $query = "SELECT DISTINCT id_cnbv_subsector FROM catalogo_clasificacion_sectorial_pb WHERE id_pb_grupo = '{$bean->pb_grupo_c}'";
//             $queryResult = $db->query($query);
//             $row = $db->fetchByAssoc($queryResult);
//             $bean->subsector_c = $row['id_cnbv_subsector'];
//         }
//     }

//     public function llenaSectorial($bean, $event, $args){
//         //Llena el campos de clasificación sectorial
//         global $db;
//         $query = "SELECT * FROM catalogo_clasificacion_sectorial WHERE id_actividad_economica_cnbv = '{$bean->actividad_economica_c}'";
//         $queryResult = $db->query($query);
//         $row = $db->fetchByAssoc($queryResult);
//         $bean->subsector_c = $row['id_subsector_economico_cnbv'];
//         $bean->sector_economico_c = $row['id_sector_economico_cnbv'];
//         $bean->macrosector_c = $row['id_macro_sector_cnbv'];
//         $bean->inegi_clase_c = $row['id_clase_inegi'];
//         $bean->inegi_subrama_c = $row['id_subrama_inegi'];
//         $bean->inegi_rama_c = $row['id_rama_inegi'];
//         $bean->inegi_subsector_c = $row['id_subsector_inegi'];
//         $bean->inegi_sector_c = $row['id_sector_inegi'];
//         $bean->inegi_macro_c = $row['id_macro_inegi'];
//     }
    
// }
