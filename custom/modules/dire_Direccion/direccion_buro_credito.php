<?php

class class_dir_buro
{
    function func_dir_buro_credito($bean, $event, $args)
    {
        $GLOBALS['log']->fatal("*** Valida dirección de Buró si no existe *** ");

        if ($bean->load_relationship('accounts_dire_direccion_1')) {
            $relatedDirecciones = $bean->accounts_dire_direccion_1->getBeans();

            if (!empty($relatedDirecciones)) {
                foreach ($relatedDirecciones as $direccion) {
                    $id_cuenta = $direccion->id;
                    $GLOBALS['log']->fatal("ID_CUENTA_REL_DIRECCION " . $direccion->id);
                }
            }
        }

        $apiDireccionBuroCredito = new BuroCredito();
        $body = array(
            'idCliente' => $id_cuenta,
        );
        $response = $apiDireccionBuroCredito->addClienteBuroCredito(null, $body);
        $GLOBALS['log']->fatal("Dirección de Buró creada exitosamente.");
    }
}
