<?php

if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class GetPODynamics extends SugarApi
{
    public function registerApiRest()
    {
        return array(
            'GetClasfSectorialAPI' => array(
                'reqType' => 'GET',
                'noLoginRequired' => false,
                'path' => array('GetPODynamics', '?'),
                'pathVars' => array('module', 'id'),
                'method' => 'getResponsePODynamics',
                'shortHelp' => 'Obtiene el id proveedor, rfc y nombre del PO para Dynamics.',
            ),
        );
    }
    public function getResponsePODynamics($api, $args)
    {
        try {
            $GLOBALS['log']->fatal("**************** GetPODynamics *****************");
            $id_cuenta = $args['id'];
            $GLOBALS['log']->fatal("ID_CUENTA: " . $id_cuenta);
            $result = []; // Inicializamos el response

            //BUSCA LA CUENTA POR MEDIO DEL ID_CORTO 
            $selectCuenta  = "SELECT id_c 
			FROM accounts_cstm WHERE id_uniclick_c = '{$id_cuenta}'";
            $cuentaResult = $GLOBALS['db']->fetchOne($selectCuenta);
            if ($cuentaResult && !empty($cuentaResult['id_c'])) {
                $id_cuenta = $cuentaResult['id_c'];
                // BUSQUEDA DE LEAD RELACIONADO A LA CUENTA
                $selectRelacionLead  = "SELECT id as idLead 
                FROM leads WHERE account_id = '{$id_cuenta}' AND deleted = '0'";
                $leadRelResult = $GLOBALS['db']->fetchOne($selectRelacionLead);

                if ($leadRelResult && !empty($leadRelResult['idLead'])) {

                    $idLead = $leadRelResult['idLead'];
                    $GLOBALS['log']->fatal("idLead (Lead Relacionado a Cuenta): " . $idLead);

                    // BUSQUEDA DE PUBLICO OBJETIVO (PO) RELACIONADO AL LEAD
                    $selectRelacionPO  = "SELECT p.id as idPO, pc.name_c as nombrePO, pc.rfc_c as rfcPO,
                    pc.id_franquicia_vendors_c as idVendors
                    FROM prospects_leads_1_c pl
                    INNER JOIN prospects p ON p.id = pl.prospects_leads_1prospects_ida AND p.deleted = 0
                    INNER JOIN prospects_cstm pc ON pc.id_c = p.id
                    WHERE pl.prospects_leads_1leads_idb = '{$idLead}'";

                    $poRelResult = $GLOBALS['db']->fetchOne($selectRelacionPO);

                    if ($poRelResult && !empty($poRelResult['idPO'])) {

                        $idPO = $poRelResult['idPO'];
                        $nombrePO = $poRelResult['nombrePO'];
                        $rfcPO = $poRelResult['rfcPO'];
                        $idVendors = $poRelResult['idVendors'];
                        $GLOBALS['log']->fatal("idPO (PO Relacionado a Lead de la Cuenta): " . $idPO . " - " . $nombrePO . " - " . $rfcPO . " - " . $idVendors);

                        $result['idPO'] = $idPO;
                        $result['nombrePO'] = $nombrePO;
                        $result['rfcPO'] = $rfcPO;
                        $result['idVendors'] = $idVendors;
                    }
                }
            }
            return $result;

        } catch (Exception $e) {
            $GLOBALS['log']->fatal("Error: " . $e->getMessage());
        }
    }
}
