<?php
/**
 * Created by Levementum.
 * User: jgarcia@levementum.com
 * Date: 3/8/2016
 * Time: 10:42 AM
 */

$hook_array['before_save'][] = Array(
    1,
    'format the name',
    'custom/modules/lev_Backlog/backlog_hooks.php',
    'backlog_hooks',
    'setFormatName'
);

$hook_array['before_save'][] = Array(
    2,
    'add username and date to description field',
    'custom/modules/lev_Backlog/backlog_hooks.php',
    'backlog_hooks',
    'setComentarios'    
);

$hook_array['before_save'][] = Array(
    3,
    'add username and date to description field',
    'custom/modules/lev_Backlog/backlog_hooks.php',
    'backlog_hooks',
    'setMontosMultiEtapa'
);

$hook_array['before_save'][] = Array(
    5,
    'update fields old',
    'custom/modules/lev_Backlog/update_fields_hooks.php',
    'update_fields_hooks',
    'actualizaCamposBacklog'
);
$hook_array['before_save'][] = Array(
    6,
    'Establece registro en tabla de auditoría dependiendo de la plataforma proveniento de los servicios BackLog',
    'custom/modules/lev_Backlog/Backlog_platform.php',
    'Backlog_platform_user',
    'set_audit_user_platform'
);
$hook_array['before_save'][] = Array(
    7,
    'Valida check lumo de la cuenta relacionada y actualiza lumo a nivel de backlog',
    'custom/modules/lev_Backlog/admin_Backlog.php',
    'class_Backlog_Admin',
    'func_Backlog_Admin'
);
$hook_array['before_save'][] = Array(
    8,
    'Con la relación de solicitud se obtiene el número de solicitud',
    'custom/modules/lev_Backlog/Backlog_platform.php',
    'Backlog_platform_user',
    'actualizaSolicitud'
);
$hook_array['after_save'][] = Array(
    9,
    'Envia correo de notificacion cuando cambia el valor en los campos de monto, fecha y estatus Declinada al Director',
    'custom/modules/lev_Backlog/notificaciones_backlog.php',
    'class_Backlog_Notificacion',
    'func_Backlog_Notificacion'
);