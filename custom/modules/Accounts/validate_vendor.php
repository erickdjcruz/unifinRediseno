<?php
// require_once 'include/api/SugarApiException.php';
// class class_validate_vendor
// {
//     public function func_validate_vendor_code($bean = null, $event = null, $args = null)
//     {
//         global $db;
//         if($bean->codigo_vendor_c != '' ) {
// 			$queryVendor = "SELECT codigo_vendor_c FROM accounts_cstm c
//             INNER JOIN accounts ac ON c.id_c = ac.id
//             WHERE codigo_vendor_c = '{$bean->codigo_vendor_c}'
//             AND id_c != '{$bean->id}'
//             AND ac.deleted = 0";
// 			$queryResult = $db->query($queryVendor);
// 			if($db->getRowCount($queryResult) > 0) throw new SugarApiExceptionInvalidParameter('No se puede guardar el registro. El código vendor ingresado '.$bean->codigo_vendor_c.' ya existe, favor de corregir.');
			
// 		}
//     }
// }