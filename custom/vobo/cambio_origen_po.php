<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambio de Origen</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            text-align: center;
        }
        .message {
            padding: 15px;
            border-radius: 5px;
            width: 50%;
            margin: 20px auto;
            font-size: 18px;
            font-weight: bold;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        .neutral {
            background-color: #e2e3e5;
            color: #383d41;
            border: 1px solid #d6d8db;
        }
    </style>
</head>
<body>
    <?php
    // Captura el parámetro de la URL
    global $current_user, $sugar_config, $app_list_strings;
    $accion = isset($_GET['accion']) ? $_GET['accion'] : '';
    $id = isset($_GET['id']) ? $_GET['id'] : '';
    
    //Valida envío de parámetros 
    if(empty($accion) || empty($id)){
        echo '<div class="message neutral">Valores faltantes para petición</div>';
    }else{
        //Valida permisos de usuario
        if(empty($current_user->id)){
            echo '<div class="message neutral">Se requiere inciar sesión</div>';
        }else{
            $approval_list = $app_list_strings['aprueba_cambio_origen_po_list'];
            $puede_aprobar = false;
            foreach($approval_list as $key=>$value){
                if($value == $current_user->id ){
                   $puede_aprobar = true;
                }
            }
            if(!$puede_aprobar){
                echo '<div class="message neutral">No tiene permisos para realizar esta acción</div>';
            }else{
                //Recupera PO
                $beanProspect = BeanFactory::retrieveBean('Prospects', $id, array('disable_row_level_security' => true));
                //echo $beanProspect->id;
                if(!isset($beanProspect->id)){
                    echo '<div class="message neutral">El PO no existe en sistema.</div>';
                }else{
                    // Muestra el mensaje según el valor del parámetro
                    if ($accion === 'aceptar') {
                        //Valida estauts actual y desbloque PO
                        if($beanProspect->origen_bloqueado_c){
                            if($beanProspect->aprueba_cambio_origen_c == 'RECHAZAR'){
                                echo '<div class="message warning">La solicitud fue rechazada previamente</div>';
                            }else{
                                $beanProspect->origen_bloqueado_c = false;
                                $beanProspect->aprueba_cambio_origen_c = 'Aceptar';
                                $beanProspect->save();
                                
                                //Consumir servicio para enviar correo, declarado en custom api
                                require_once("custom/clients/base/api/SendEmailPO.php");
                                $apiSendEmailPO= new SendEmailPO();
                                $body=array(
                                    'id_po'=>$beanProspect->id,
                                    'id_usuario'=>$current_user->id,
                                    'accion'=>'Aceptada'
                                );
                                $response=$apiSendEmailPO->notificaCambioOrigen(null,$body);
                                if ($response['status']=='200') {
                                    echo '<div class="message success">Se autorizo el cambio de origen exitosamente!</div>';
                                } else{
                                    echo '<div class="message warning">Se ha presentado un error</div>';
                                }
                            }
                        }else{
                            echo '<div class="message neutral">El PO no se encuentra bloqueado actualmente</div>';
                        }
                    } elseif ($accion === 'rechazar') {
                        //Valida estauts actual y rechaza PO
                        if($beanProspect->origen_bloqueado_c && $beanProspect->aprueba_cambio_origen_c != 'RECHAZAR'){
                            $beanProspect->aprueba_cambio_origen_c = 'Rechazar';
                            $beanProspect->save();
                            
                            //Consumir servicio para enviar correo, declarado en custom api
                            require_once("custom/clients/base/api/SendEmailPO.php");
                            $apiSendEmailPO= new SendEmailPO();
                            $body=array(
                                'id_po'=>$beanProspect->id,
                                'id_usuario'=>$current_user->id,
                                'accion'=>'Rechazada'
                            );
                            $response=$apiSendEmailPO->notificaCambioOrigen(null,$body);
                            if ($response['status']=='200') {
                                echo '<div class="message warning">Se rechazo el cambio de origen exitosamente</div>';
                            } else{
                                echo '<div class="message warning">Se ha presentado un error</div>';
                            }
                        }elseif ($beanProspect->origen_bloqueado_c && $beanProspect->aprueba_cambio_origen_c == 'Rechazar'){
                            echo '<div class="message neutral">El PO ya ha sido rechazado anteriormente</div>';
                        } else {
                            echo '<div class="message warning">El PO no se encuentra bloqueado para cambio de Origen</div>';
                        }
                    } else {
                        echo '<div class="message neutral">La acción indicada no es válida</div>';
                    }
                }
            }          
        }        
    }
    
    
  
    ?>
</body>
</html>
