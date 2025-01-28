<?php
/**
 * Created by erick.cruz@tactos.com.mx.
 * 09/01/2025
 */

 if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class setTelefonoCliente extends SugarApi
{
    public function registerApiRest()
    {
        return array(
            'setTelefonoClienteEndpoint' => array(
                //request type
                'reqType' => 'POST',
                //set authentication
                'noLoginRequired' => false,
                //endpoint path
                'path' => array('setTelefonoCliente'),
                //endpoint variables
                'pathVars' => array(''),
                //method to call
                'method' => 'validaTelefono',
                //short help string to be displayed in the help documentation
                'shortHelp' => 'Servicio para insertar o actualizar teléfonos de clientes.',
                //long help to be displayed in the help documentation
                'longHelp' => '',
            )
        );
    }

    public function validaTelefono($api, $args)
    {
        $inputData = $args;
        global $sugar_config, $db; 
        // Parámetros requeridos
        $requiredParams = ['id_cliente', 'tipotelefono', 'telefono'];
        //$GLOBALS['log']->fatal('Inicia inserción teléfono.');

        // Validar si la entrada es un solo registro y convertirlo a un formato de arreglo uniforme
        if (isset($inputData['id_cliente'])) {
            $inputData = ['data' => [$inputData]];
        }

        // Verificar que se recibió un arreglo con datos
        if (!isset($inputData['data']) || !is_array($inputData['data'])) {
            $GLOBALS['log']->fatal('Formato JSON inválido. Se esperaba un objeto o un arreglo de datos.');
            throw new SugarApiExceptionInvalidParameter('Formato JSON inválido. Se esperaba un objeto o un arreglo de datos.');
        }
        //$GLOBALS['log']->fatal($inputData['data']);

        $response = [];
        foreach ($inputData['data'] as $registro) {
            try {
                $missingParams= null;

                $idCliente = $registro['id_cliente'];
                $tipoTelefono = $registro['tipotelefono'];
                $pais = $registro['pais'];
                $telefono = $registro['telefono'];
                $extension = $registro['extension'] ?? '';
                $principal = $registro['principal'] ?? false;
                $estatus = $registro['estatus'] ?? 'Activo';
                $telefonoPrevio = $registro['telefono_previo'] ?? '';
                
                // Validar parámetros requeridos
                foreach ($requiredParams as $param) {
                    if (empty($registro[$param])) {
                        $missingParams[] = $param;
                    }
                }               
                // Si faltan parámetros, devolver un error
                if (!empty($missingParams)) {
                    throw new Exception('Datos requeridos incompletos. '. implode(', ', $missingParams));                    
                }

                // Validaciones
                if (!preg_match('/^\d+$/', $telefono)) {
                    throw new Exception('Solo números son permitidos.');
                }
                if (strlen($telefono) !== 10) {
                    throw new Exception('Debe contener 10 dígitos.');
                }
                if (count(array_unique(str_split($telefono))) === 1) {
                    throw new Exception('Carácter repetido.');
                }

                if($telefonoPrevio!=''){
                    $telefonobusqueda = $telefonoPrevio;
                }else{
                    $telefonobusqueda = $telefono;
                }
                
                $telId = $this->busqueda_telefono($idCliente, $tipoTelefono, $telefonobusqueda);
                
                if ( !empty($telId) ) {
                    $GLOBALS['log']->fatal("actualizado" );
                    // Actualizar registro existente
                    $GLOBALS['log']->fatal("telId: ".$telId );
                    $telefonoBean = BeanFactory::retrieveBean('Tel_Telefonos', $telId);
                    if($telefonoPrevio!=''){
                        $tel = $telefono;
                        $telefonoBean->name = $tel;
                        $telefonoBean->telefono = $tel;
                    }
                    $telefonoBean->extension = $extension;
                    $telefonoBean->principal = $principal;
                    $telefonoBean->estatus = $estatus;
                    $telefonoBean->pais = $pais;
                    $telefonoBean->save();

                    $response[] = [
                        'id_cliente' => $idCliente,
                        'estatus' => 'actualizado'
                    ];
                } else {
                    $GLOBALS['log']->fatal("nuevo" );
                    // Insertar nuevo registro
                    $telefonoBean = BeanFactory::newBean("Tel_Telefonos");
                    $telefonoBean->accounts_tel_telefonos_1accounts_ida = $idCliente;
                    $telefonoBean->name = $telefono;
                    $telefonoBean->telefono = $telefono;
                    $telefonoBean->tipotelefono = $tipoTelefono;
                    $telefonoBean->principal = $principal;
                    $telefonoBean->estatus = $estatus;
                    $telefonoBean->pais = $pais;
                    $telefonoBean->save();

                    $response[] = [
                        'id_cliente' => $idCliente,
                        'estatus' => 'insertado'
                    ];
                }
            } catch (Exception $e) {
                $response[] = [
                    'id_cliente' => $registro['id_cliente'] ?? null,
                    'estatus' => 'error',
                    'descripcion' => $e->getMessage()
                ];
            }
        }

        return $response;
    }

    public function busqueda_telefono($idCliente, $tipoTelefono, $telefonobusqueda)
    {
        $telid = "";
        global $sugar_config, $db; 
        $sql = "SELECT tel.id,tel.name,tel.secuencia,tel.telefono,tel.tipotelefono,tel.principal
        ,tel.estatus,tel.extension, tel.pais ,
        acctel.accounts_tel_telefonos_1accounts_ida, acctel.accounts_tel_telefonos_1tel_telefonos_idb 
        FROM tel_telefonos AS tel INNER JOIN 
        accounts_tel_telefonos_1_c as acctel ON
        tel.id = acctel.accounts_tel_telefonos_1tel_telefonos_idb
        WHERE acctel.accounts_tel_telefonos_1accounts_ida = '".$idCliente."'
        AND tel.deleted = 0
        AND tel.tipotelefono = ".$tipoTelefono."
        AND tel.estatus = 'Activo'
        AND tel.telefono= '".$telefonobusqueda."'";

        //$GLOBALS['log']->fatal("sql: " . $sql );
        try{
            $results = $db->query($sql);
        } catch (Exception $ex) {
            $GLOBALS['log']->fatal("Exception " . $ex);
            $estado = 400;
        }
        //$GLOBALS['log']->fatal("Result Row: " . print_r($results, true));
        while ($row = $db->fetchByAssoc($results)) {
            $telId = $row['id'];
        }
        return $telId;
    }
}