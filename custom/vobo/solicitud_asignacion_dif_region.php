<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reasignación Asesor - Diferente región</title>
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
        exit;
    }
    
    //Valida permisos de usuario
    if(empty($current_user->id)){
        echo '<div class="message neutral">Se requiere inciar sesión</div>';
        exit;
    }   
     
    //Recupera cuenta-Resumen
    $beanAccResume = BeanFactory::retrieveBean('tct02_Resumen', $id, array('disable_row_level_security' => true));
    $beanA = BeanFactory::retrieveBean('Accounts', $id, array('disable_row_level_security' => true));
    if (!$beanAccResume) {
        echo '<div class="message neutral">La cuenta no existe en el sistema.</div>';
        exit;
    }
    //echo '<div class="message neutral">beanId_cuenta'.$beanA->id.'beanId_aprobador'.$beanAccResume->id_director_region_aprobar_c.' - current_user'.$current_user->id.'No tiene permisos para realizar esta acción</div>';
    $approval_list = $app_list_strings['ids_aprobador_reasignacion_director_list'];
    $puede_aprobar = false;
    $puede_aprobar = in_array($current_user->id, $approval_list) || ($beanAccResume->id_director_region_aprobar_c == $current_user->id);

    if(!$puede_aprobar){
        echo '<div class="message neutral">No tiene permisos para realizar esta acción</div>';
        exit;
    }

    if(!$beanAccResume->asignacion_activa_c){
        echo '<div class="message neutral">La cuenta no cuenta con una solicitud de asignación activa</div>';
        exit;
    }

    if ($accion != 'aceptar' && $accion != 'rechazar') {
        echo '<div class="message neutral">La acción indicada no es válida</div>';
        exit;
    }
    
    // Enviar solicitud al endpoint
    require_once("custom/clients/base/api/SolicitudAsignacionEmail.php");
    $apiSolicitudAsignacion = new solicitudAsignacionEmail();
    $body = array(
        'id_cuenta' => $id,
        'id_asesor_solicita' => $beanAccResume->id_asesor_solicita_c,
        'comentarios' => $comentarios
    ); 
    if ($accion === 'aceptar') {           
        //ENDPOINT AUTORIZA ASIGNACION
        $response = $apiSolicitudAsignacion->procesoAutorizaAsignacion(null, $body);
    } else {  
        //ENDPOINT RECHAZA ASIGNACION
        $response = $apiSolicitudAsignacion->procesoRechazoAsignacion(null, $body);
    }
    // Mostrar mensaje según la respuesta
    if ($response['status'] == '200') {
        if ($accion === 'aceptar') {
            echo '<div class="message success">Se autorizó el cambio de asignación exitosamente!</div>';
        }elseif ($accion === 'rechazar') {
            echo '<div class="message warning">Se rechazo el cambio de asignación</div>';
        }
    } else {
        echo '<div class="message warning">Se ha presentado un error</div>';
    }

    ?>
</body>
</html>
