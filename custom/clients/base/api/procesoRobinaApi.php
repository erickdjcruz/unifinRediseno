<?php

if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class procesoRobinaApi extends SugarApi
{
    public function registerApiRest()
    {
        return array(
            'retrieve' => array(
                'reqType' => 'POST',
                'noLoginRequired' => true,
                'path' => array('procesoRobinaApi'),
                'pathVars' => array('method'),
                'method' => 'registraProcesoRobina',
                'shortHelp' => 'Registra los nuevos proceso robina',
                'longHelp' => '',
            ),
        );
    }

    public function registraProcesoRobina($api, $args)
    {
        $GLOBALS['log']->fatal("...INICIA REGISTRO DE PROCESO ROBINA API...");
        $rfc = isset($args['rfc']) ? $args['rfc'] : '';
        $ticket = isset($args['ticket']) ? $args['ticket'] : '';
        $estatus_procesado = isset($args['estatus_procesado']) ? $args['estatus_procesado'] : '';
        $fecha_emision = isset($args['fecha_emision']) ? $args['fecha_emision'] : '';

        // Validación RFC
        if (empty($rfc)) {
            return array(
                "status" => "error",
                "code" => 400,
                "message" => "El registro no se pudo crear. RFC es obligatorio.",
                "required_fields" => ["rfc"]
            );
        }

        //SE CREA EL REGISTRO PROCESO ROBINA
        $beanProcesoRobina = BeanFactory::newBean('pr_Procesos_Robina');
        $beanProcesoRobina->rfc = $rfc;
        $beanProcesoRobina->ticket = $ticket;
        $beanProcesoRobina->estatus_procesado = $estatus_procesado;
        $beanProcesoRobina->fecha_emision = $fecha_emision;
        $beanProcesoRobina->save();

        $GLOBALS['log']->fatal("Proceso registrado: ".$ticket."  -id_registro: ".$beanProcesoRobina->id);

        return array(
            "status" => 200,
            "message" => "El registro se ha creado correctamente",
            "detail" => $beanProcesoRobina->id
        );
        
    }
}
