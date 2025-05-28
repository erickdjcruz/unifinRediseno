<?php

if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
require_once("custom/Levementum/UnifinAPI.php");

class GetProductosCuentas extends SugarApi
{
    public function registerApiRest()
    {
        return array(
            'GETActivoAPI' => array(
                'reqType' => 'GET',
                'noLoginRequired' => false,
                'path' => array('GetProductosCuentas', '?'),
                'pathVars' => array('module', 'id'),
                'method' => 'getcstmProductos',
                'shortHelp' => 'Obtiene todos los productos relacionados a la cuenta',
            ),
        );
    }
    public function getcstmProductos($api, $args)
    {

        $id = $args['id'];
        $records_in = [];

        /*****************SUBTIPO CUENTA = 2-Contactado or SUBTIPO CUENTA = 7-Interesado or TIPO CUENTA = 1-Lead**********/
        // $query = "SELECT PRODUCTOS.*, concat(uassign.first_name,' ',uassign.last_name) as full_name
        // ,concat(u1.first_name,' ',u1.last_name) as fullname_ingesta_c
        // ,concat(u2.first_name,' ',u2.last_name) as fullname_validacion1_c
        // ,concat(u3.first_name,' ',u3.last_name) as fullname_validacion2_c
        // FROM (SELECT
        //     case
        //         when up.tipo_producto = 1 and (up.tipo_cuenta = 2 or up.tipo_cuenta = 3) then 1
        //         when up.tipo_producto = 3 and (up.tipo_cuenta = 2 or up.tipo_cuenta = 3) then 1
        //         when up.tipo_producto = 4 and (up.tipo_cuenta = 2 or up.tipo_cuenta = 3) then 1
        //         when up.tipo_producto = 6 and (up.tipo_cuenta = 2 or up.tipo_cuenta = 3) then 1
        //         when up.tipo_producto = 8 and (up.tipo_cuenta = 2 or up.tipo_cuenta = 3) then 1
        //         else 0
        //     end 'visible_noviable', up.*, upc.*
        //     FROM accounts a
        //     inner join accounts_uni_productos_1_c ap on a.id = ap.accounts_uni_productos_1accounts_ida
        //     inner join uni_productos up on up.id = ap.accounts_uni_productos_1uni_productos_idb
        //     inner join uni_productos_cstm upc on upc.id_c = up.id
        //     and a.id = '{$id}' and up.deleted = 0
        //  ) AS PRODUCTOS
        //     LEFT JOIN users AS uassign ON PRODUCTOS.assigned_user_id = uassign.id
        //     LEFT JOIN users AS u1 ON PRODUCTOS.user_id_c = u1.id
        //     LEFT JOIN users AS u2 ON PRODUCTOS.user_id1_c = u2.id
        //     LEFT JOIN users AS u3 ON PRODUCTOS.user_id2_c = u3.id ";
        
        //SE AGREGA EN EL QUERY LOS DATOS DEL ASESOR LEASING SOLO DEL TIPO DE PRODUCTO LEASING
        $query = "SELECT PRODUCTOS.*, 
		CASE WHEN PRODUCTOS.tipo_producto = 1 THEN u1.user_name ELSE '' END AS user_name,
		CASE WHEN PRODUCTOS.tipo_producto = 1 THEN u1.first_name ELSE '' END AS first_name,
		CASE WHEN PRODUCTOS.tipo_producto = 1 THEN u1.last_name ELSE '' END AS last_name,
		CASE WHEN PRODUCTOS.tipo_producto = 1 THEN u1.status ELSE '' END AS status,
		CASE WHEN PRODUCTOS.tipo_producto = 1 THEN uc1.tipodeproducto_c ELSE '' END AS tipodeproducto_c,
		CASE WHEN PRODUCTOS.tipo_producto = 1 THEN uc1.puestousuario_c ELSE '' END AS puestousuario_c,
		CASE WHEN PRODUCTOS.tipo_producto = 1 THEN uc1.nombre_completo_c ELSE '' END AS nombre_completo_c,
		CASE WHEN PRODUCTOS.tipo_producto = 1 THEN uc1.region_c ELSE '' END AS region_c,
		CASE WHEN PRODUCTOS.tipo_producto = 1 THEN uc1.posicion_operativa_c ELSE '' END AS posicion_operativa_c
        ,concat(uassign.first_name,' ',uassign.last_name) as full_name
        ,concat(u1.first_name,' ',u1.last_name) as fullname_ingesta_c
        ,concat(u2.first_name,' ',u2.last_name) as fullname_validacion1_c
        ,concat(u3.first_name,' ',u3.last_name) as fullname_validacion2_c
        FROM (SELECT
            case
                when up.tipo_producto = 1 and (up.tipo_cuenta = 2 or up.tipo_cuenta = 3) then 1
                when up.tipo_producto = 3 and (up.tipo_cuenta = 2 or up.tipo_cuenta = 3) then 1
                when up.tipo_producto = 4 and (up.tipo_cuenta = 2 or up.tipo_cuenta = 3) then 1
                when up.tipo_producto = 6 and (up.tipo_cuenta = 2 or up.tipo_cuenta = 3) then 1
                when up.tipo_producto = 8 and (up.tipo_cuenta = 2 or up.tipo_cuenta = 3) then 1
                else 0
            end 'visible_noviable', ac.user_id_c, ac.user_id1_c, ac.user_id2_c, up.*, upc.id_c,
            upc.fecha_asignacion_c, upc.no_viable_otro_c, upc.cobranza_c, upc.canal_c, upc.multilinea_c, upc.exclu_precalif_c, upc.status_management_c, 
			upc.oficina_c, upc.metodo_asignacion_lm_c, upc.aprueba1_c, upc.aprueba2_c, upc.detalle_c, upc.motivo_c, upc.notificacion_noviable_c, 
            upc.razon_c, upc.reactivacion_c, upc.registros_historicos_c, upc.vencimiento_anexo_final_c, upc.vencimiento_anexo_prox_c, upc.registros_activos_c, 
			upc.dias_atraso_c, upc.mensualidad_activa_c, upc.proxima_mensualidad_c, upc.numero_disposiciones_c
            FROM accounts a
            inner join accounts_cstm ac on ac.id_c = a.id
            inner join accounts_uni_productos_1_c ap on a.id = ap.accounts_uni_productos_1accounts_ida
            inner join uni_productos up on up.id = ap.accounts_uni_productos_1uni_productos_idb
            inner join uni_productos_cstm upc on upc.id_c = up.id
            and a.id = '{$id}' and up.deleted = 0
         ) AS PRODUCTOS
            LEFT JOIN users AS uassign ON PRODUCTOS.assigned_user_id = uassign.id
            LEFT JOIN users AS u1 ON PRODUCTOS.user_id_c = u1.id
            LEFT JOIN users_cstm AS uc1 ON PRODUCTOS.user_id_c = uc1.id_c
            LEFT JOIN users AS u2 ON PRODUCTOS.user_id1_c = u2.id
            LEFT JOIN users AS u3 ON PRODUCTOS.user_id2_c = u3.id";

        $result = $GLOBALS['db']->query($query);

        while ($row = $GLOBALS['db']->fetchByAssoc($result)) {
            $records_in[] = $row;
        }
        return $records_in;
    }
}
