<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
class AsignacionAsesoresBusqueda extends SugarApi
{
    public function registerApiRest()
    {
        return array(
            'GETAsignacionAsesoresBusqueda' => array(
                'reqType' => 'GET',
                'path' => array('AsignacionAsesoresBusqueda','?'),
                'pathVars' => array('','id'),
                'method' => 'obtenerCuentas',
                'shortHelp' => 'Obtener PO, leads y cuentas de promotores',
            ),
        );
    }

    public function obtenerCuentas($api, $args)
    {
        try
        {
            global $db;
            $user_id = $args['id'];
            $product_offset = $args['PRODUCTO'];
            $product_offset = explode("?", $product_offset);
            $product = $product_offset[0];
            $offset = $product_offset[1];
            $filtroCliente = $product_offset[2];
            //Omitiendo espacios en blanco
            $filtroCliente=trim($filtroCliente);
            $filtroTipoCuenta=$args['tipos_cuenta'];
			$Director=$args['Director'];
            $tipos_separados=explode(",",$filtroTipoCuenta);
            $arr_aux=array();
            for($i=0;$i<count($tipos_separados);$i++){
                array_push($arr_aux,"'".$tipos_separados[$i]."'");
            }
            $tipos_query= join(',',$arr_aux);
             if($product == "LEASING"){
                 $user_field = "user_id_c"; //user_id_c = promotorleasing_c
             }else if($product == "FACTORAJE"){
                 $user_field = "user_id1_c"; //user_id1_c = promotorfactoraje_c
             }else if($product == "CRÉDITO AUTOMOTRIZ"){
                 $user_field = "user_id2_c"; //user_id2_c = promotorcredit_c
             }else if($product == "FLEET"){
                 $user_field = "user_id6_c";
             }else if($product == "UNICLICK"){
                 $user_field = "user_id7_c";
             }else if($product == "UNILEASE"){
                 $user_field = "user_id7_c";
             }else if($product == "RM"){
                 $user_field = "user_id8_c";
             }
            if($user_id == "undefined"){
				$total_rows = <<<SQL
select 'PO' modulo, '' tipo, p.id, pc.name_c, u.id idu, uc.nombre_completo_c, '' nuevo from prospects p, prospects_cstm pc, users u, users_cstm uc
where p.id = pc.id_c and u.id = uc.id_c and p.assigned_user_id = u.id and p.deleted = 0 and pc.estatus_po_c <> 3
and u.user_name <> 'SINGESTOR'
union
select 'Lead' modulo, lc.tipo_registro_c tipo, l.id, lc.name_c, u.id idu, uc.nombre_completo_c, '' nuevo from leads l, leads_cstm lc, users u, users_cstm uc
where l.id = lc.id_c and u.id = uc.id_c and l.assigned_user_id = u.id and l.deleted = 0 and lc.subtipo_registro_c <> 4
and u.user_name <> 'SINGESTOR'
union
select 'Cuenta' modulo, cc.tipo_registro_cuenta_c tipo, c.id, c.name, u.id idu, uc.nombre_completo_c, '' nuevo from accounts c, accounts_cstm cc, users u, users_cstm uc, tct02_resumen_cstm r
where c.id = cc.id_c and c.id = r.id_c and r.asignacion_activa_c <> 1 and u.id = uc.id_c and cc.{$user_field} = u.id and c.deleted = 0 and u.user_name <> 'SINGESTOR'
and tipo_registro_cuenta_c IN({$tipos_query})
SQL;
				if(!empty($Director)) {
					$total_rows = <<<SQL
select 'PO' modulo, '' tipo, p.id, pc.name_c, u.id idu, uc.nombre_completo_c, '' nuevo from prospects p, prospects_cstm pc, users u, users_cstm uc
where p.id = pc.id_c and u.id = uc.id_c and p.assigned_user_id = u.id and p.deleted = 0 and pc.estatus_po_c <> 3
and u.user_name <> 'SINGESTOR' and u.reports_to_id = '{$Director}'
union
select 'Lead' modulo, lc.tipo_registro_c tipo, l.id, lc.name_c, u.id idu, uc.nombre_completo_c, '' nuevo from leads l, leads_cstm lc, users u, users_cstm uc
where l.id = lc.id_c and u.id = uc.id_c and l.assigned_user_id = u.id and l.deleted = 0 and lc.subtipo_registro_c <> 4
and u.user_name <> 'SINGESTOR' and u.reports_to_id = '{$Director}'
union
select 'Cuenta' modulo, cc.tipo_registro_cuenta_c tipo, c.id, c.name, u.id idu, uc.nombre_completo_c, '' nuevo from accounts c, accounts_cstm cc, users u, users_cstm uc, tct02_resumen_cstm r
where c.id = cc.id_c and c.id = r.id_c and r.asignacion_activa_c <> 1 and u.id = uc.id_c and cc.{$user_field} = u.id and c.deleted = 0 and u.user_name <> 'SINGESTOR'
and u.reports_to_id = '{$Director}' and tipo_registro_cuenta_c IN({$tipos_query})
SQL;
				}
            }
			else{
				$total_rows = <<<SQL
select 'PO' modulo, '' tipo, p.id, pc.name_c, u.id idu, uc.nombre_completo_c, '' nuevo from prospects p, prospects_cstm pc, users u, users_cstm uc
where p.id = pc.id_c and u.id = uc.id_c and p.assigned_user_id = u.id and p.deleted = 0 and pc.estatus_po_c <> 3
and u.user_name <> 'SINGESTOR' and u.id = '{$user_id}'
union
select 'Lead' modulo, lc.tipo_registro_c tipo, l.id, lc.name_c, u.id idu, uc.nombre_completo_c, '' nuevo from leads l, leads_cstm lc, users u, users_cstm uc
where l.id = lc.id_c and u.id = uc.id_c and l.assigned_user_id = u.id and l.deleted = 0 and lc.subtipo_registro_c <> 4
and u.user_name <> 'SINGESTOR' and u.id = '{$user_id}'
union
select 'Cuenta' modulo, cc.tipo_registro_cuenta_c tipo, c.id, c.name, u.id idu, uc.nombre_completo_c, '' nuevo from accounts c, accounts_cstm cc, users u, users_cstm uc, tct02_resumen_cstm r
where c.id = cc.id_c and c.id = r.id_c and r.asignacion_activa_c <> 1 and u.id = uc.id_c and cc.{$user_field} = u.id and c.deleted = 0 and u.user_name <> 'SINGESTOR'
and tipo_registro_cuenta_c IN({$tipos_query}) and u.id = '{$user_id}'
SQL;
			}
            if(!empty($filtroCliente)) $total_rows .= " AND name LIKE '%{$filtroCliente}%'";
            $totalResult = $db->query($total_rows);
            $response['total'] = $totalResult->num_rows;
            while($row = $db->fetchByAssoc($totalResult))
            {
                $response['full_cuentas'][] = $row['id'];
            }
            if($user_id == "undefined"){
				$query = <<<SQL
select 'PO' modulo, '' tipo, p.id, pc.name_c, u.id idu, uc.nombre_completo_c, '' nuevo from prospects p, prospects_cstm pc, users u, users_cstm uc
where p.id = pc.id_c and u.id = uc.id_c and p.assigned_user_id = u.id and p.deleted = 0 and pc.estatus_po_c <> 3
and u.user_name <> 'SINGESTOR'
union
select 'Lead' modulo, lc.tipo_registro_c tipo, l.id, lc.name_c, u.id idu, uc.nombre_completo_c, '' nuevo from leads l, leads_cstm lc, users u, users_cstm uc
where l.id = lc.id_c and u.id = uc.id_c and l.assigned_user_id = u.id and l.deleted = 0 and lc.subtipo_registro_c <> 4
and u.user_name <> 'SINGESTOR'
union
select 'Cuenta' modulo, cc.tipo_registro_cuenta_c tipo, c.id, c.name, u.id idu, uc.nombre_completo_c, '' nuevo from accounts c, accounts_cstm cc, users u, users_cstm uc, tct02_resumen_cstm r
where c.id = cc.id_c and c.id = r.id_c and r.asignacion_activa_c <> 1 and u.id = uc.id_c and cc.{$user_field} = u.id and c.deleted = 0 and u.user_name <> 'SINGESTOR'
and tipo_registro_cuenta_c IN({$tipos_query})
SQL;
				if(!empty($Director)) {
					$query = <<<SQL
select 'PO' modulo, '' tipo, p.id, pc.name_c, u.id idu, uc.nombre_completo_c, '' nuevo from prospects p, prospects_cstm pc, users u, users_cstm uc
where p.id = pc.id_c and u.id = uc.id_c and p.assigned_user_id = u.id and p.deleted = 0 and pc.estatus_po_c <> 3
and u.user_name <> 'SINGESTOR' and u.reports_to_id = '{$Director}'
union
select 'Lead' modulo, lc.tipo_registro_c tipo, l.id, lc.name_c, u.id idu, uc.nombre_completo_c, '' nuevo from leads l, leads_cstm lc, users u, users_cstm uc
where l.id = lc.id_c and u.id = uc.id_c and l.assigned_user_id = u.id and l.deleted = 0 and lc.subtipo_registro_c <> 4
and u.user_name <> 'SINGESTOR' and u.reports_to_id = '{$Director}'
union
select 'Cuenta' modulo, cc.tipo_registro_cuenta_c tipo, c.id, c.name, u.id idu, uc.nombre_completo_c, '' nuevo from accounts c, accounts_cstm cc, users u, users_cstm uc, tct02_resumen_cstm r
where c.id = cc.id_c and c.id = r.id_c and r.asignacion_activa_c <> 1 and u.id = uc.id_c and cc.{$user_field} = u.id and c.deleted = 0 and u.user_name <> 'SINGESTOR'
and u.reports_to_id = '{$Director}' and tipo_registro_cuenta_c IN({$tipos_query})
SQL;
				}
            }
			else{
				$query = <<<SQL
select 'PO' modulo, '' tipo, p.id, pc.name_c, u.id idu, uc.nombre_completo_c, '' nuevo from prospects p, prospects_cstm pc, users u, users_cstm uc
where p.id = pc.id_c and u.id = uc.id_c and p.assigned_user_id = u.id and p.deleted = 0 and pc.estatus_po_c <> 3
and u.user_name <> 'SINGESTOR' and u.id = '{$user_id}'
union
select 'Lead' modulo, lc.tipo_registro_c tipo, l.id, lc.name_c, u.id idu, uc.nombre_completo_c, '' nuevo from leads l, leads_cstm lc, users u, users_cstm uc
where l.id = lc.id_c and u.id = uc.id_c and l.assigned_user_id = u.id and l.deleted = 0 and lc.subtipo_registro_c <> 4
and u.user_name <> 'SINGESTOR' and u.id = '{$user_id}'
union
select 'Cuenta'  modulo, cc.tipo_registro_cuenta_c tipo, c.id, c.name, u.id idu, uc.nombre_completo_c, '' nuevo from accounts c, accounts_cstm cc, users u, users_cstm uc, tct02_resumen_cstm r
where c.id = cc.id_c and c.id = r.id_c and r.asignacion_activa_c <> 1 and u.id = uc.id_c and cc.{$user_field} = u.id and c.deleted = 0 and u.user_name <> 'SINGESTOR'
and tipo_registro_cuenta_c IN({$tipos_query}) and u.id = '{$user_id}'
SQL;
			}
            if(!empty($filtroCliente)) $query .= " AND name LIKE '%{$filtroCliente}%'";
            $query .= " ORDER BY nombre_completo_c ASC LIMIT 20 OFFSET {$offset}";
            $queryResult = $db->query($query);
            $response['total_cuentas'] = $queryResult->num_rows;
            while($row = $db->fetchByAssoc($queryResult))
            {
                 $response['cuentas'][] = $row;
            }        
            return $response;
        }catch (Exception $e){
            $GLOBALS['log']->fatal(__FILE__." - ".__CLASS__."->".__FUNCTION__." <".$current_user->user_name."> :  Error ".$e->getMessage());
        }
    }
}