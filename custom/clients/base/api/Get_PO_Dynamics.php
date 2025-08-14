<?php

if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class Get_PO_Dynamics extends SugarApi
{
    public function registerApiRest()
    {
        return array(
            'GetClasfSectorialAPI' => array(
                'reqType' => 'GET',
                'noLoginRequired' => false,
                'path' => array('Get_PO_Dynamics', '?'),
                'pathVars' => array('module', 'id'),
                'method' => 'getRespDynamicPO1',
                'shortHelp' => 'Obtiene el id proveedor, rfc y nombre del PO para Dynamics.',
            ),
        );
    }
    public function getRespDynamicPO1($api, $args)
    {
        try {
            $GLOBALS['log']->fatal("**************** GetDynamicPO1 *****************");
            $id_corto = $args['id'];
            $id_crm = '';
            $GLOBALS['log']->fatal("ID_CORTO: " . $id_corto);
            $result = []; // Inicializamos el response

            if (strlen($id_corto) > 8 ){
                $id_crm = $id_corto;
            }
            $GLOBALS['log']->fatal("ID_CRM: " .$id_crm);
            //BUSCA LA CUENTA POR MEDIO DEL ID_CORTO 
            if ($id_corto != '') {

                if( $id_crm == '' || empty($id_crm) ){
                    //$GLOBALS['log']->fatal("ID_CORTO: " . $id_corto);
                    $selectCuenta  = "SELECT id_c FROM accounts_cstm WHERE idcliente_c = '{$id_corto}'";
                    //$GLOBALS['log']->fatal("selectCuenta: " . $selectCuenta);
                    $cuentaResult = $GLOBALS['db']->fetchOne($selectCuenta, true);
                    //$GLOBALS['log']->fatal(print_r($cuentaResult, true));

                    if (!empty($cuentaResult) && isset($cuentaResult['id_c'])) {
                        $id_cuenta = $cuentaResult['id_c'];
                    }                
                }else{
                    $id_cuenta = $id_crm;
                }
                $GLOBALS['log']->fatal('id_cuenta'.$id_cuenta);

                if (!empty($id_cuenta) && $id_cuenta != '') {
                    //$id_cuenta = $cuentaResult['id_c'];
                    //$GLOBALS['log']->fatal("id_cuenta: " . $id_cuenta);
                    //$GLOBALS['log']->fatal("BUSQUEDA DE LEAD RELACIONADO A LA CUENTA");
                    // BUSQUEDA DE LEAD RELACIONADO A LA CUENTA
                
                    // BUSQUEDA DE PUBLICO OBJETIVO (PO) RELACIONADO AL LEAD
                    $selectRelacionPO  = "SELECT le.id as idLead , p.id as idPO, pc.name_c as nombrePO, pc.rfc_c as rfcPO, pc.id_franquicia_vendors_c as idVendors 
                    FROM leads le LEFT JOIN prospects_leads_1_c pl on pl.prospects_leads_1leads_idb = le.id and le.deleted = 0 
                    INNER JOIN prospects p ON p.id = pl.prospects_leads_1prospects_ida AND p.deleted = 0 
                    INNER JOIN prospects_cstm pc ON pc.id_c = p.id 
                    WHERE le.account_id = '{$id_cuenta}' AND pc.id_franquicia_vendors_c is not null; ";

                    //$GLOBALS['log']->fatal($selectRelacionPO);
                    $poRelResultSet = $GLOBALS['db']->query($selectRelacionPO, true);
                    $resultsPO = [];

                    while ($row = $GLOBALS['db']->fetchByAssoc($poRelResultSet)) {
                        $GLOBALS['log']->fatal("idPO: {$row['idPO']} - {$row['nombrePO']} - {$row['rfcPO']} - {$row['idVendors']}");

                        $resultsPO[] = [
                            'idPO'      => $row['idPO'],
                            'nombrePO'  => $row['nombrePO'],
                            'rfcPO'     => $row['rfcPO'],
                            'idVendors' => $row['idVendors']
                        ];
                    }
                }
            }

            return $resultsPO;
        } catch (Exception $e) {
            $GLOBALS['log']->fatal("Error: " . $e->getMessage());
        }
    }
}
