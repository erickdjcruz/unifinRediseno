<?php

if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class estatusProcesoRobina extends SugarApi
{
    public function registerApiRest()
    {
        return array(
            'retrieve' => array(
                //request type
                'reqType' => 'GET',
                //endpoint path
                'path' => array('estatusProcesoRobina', '?'),
                //endpoint variables
                'pathVars' => array('method', 'idCuenta'),
                //method to call
                'method' => 'validaEstatusRobina',
                //short help string to be displayed in the help documentation
                'shortHelp' => 'Valida Estatus Proceso Robina',
                //long help to be displayed in the help documentation
                'longHelp' => '',
            ),
        );
    }

    public function validaEstatusRobina($api, $args)
    {
        global $db;
        $resultado = ['status' => '300']; // Valor por defecto
        try {
            $idCuenta = isset($args['idCuenta']) ? $args['idCuenta'] : '';
            //$GLOBALS['log']->fatal("idCuenta: " . $idCuenta);
            if (!empty($idCuenta)) {
                
                $query = "SELECT id,estatus_procesado FROM pr_procesos_robina 
                WHERE rfc = (SELECT rfc_c FROM accounts_cstm WHERE id_c = '{$idCuenta}')  
                ORDER BY date_modified DESC 
                LIMIT 1;";
                //$GLOBALS['log']->fatal("query: " . $query);
                $result = $db->query($query);

                while ($row = $db->fetchByAssoc($result)) {
                    $resultado['estatus_procesado'] = $row['estatus_procesado'];
                }
                //$GLOBALS['log']->fatal(print_r($row,true));
                //$resultado['estatus_procesado'] = $row['estatus_procesado'];
                if (!empty($row)) {
                    $resultado['status'] = '200';
                }
            }
        } catch (Exception $e) {
            $GLOBALS['log']->fatal("Error en estatusProcesoRobina: " . $e->getMessage());
            $resultado['status'] = '500';
        }

        return $resultado;
    }
}
