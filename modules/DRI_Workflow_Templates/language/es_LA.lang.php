<?php
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */
$mod_strings = [
    'LBL_HOMEPAGE_TITLE' => 'Mis plantillas de la guía inteligente',
    'LBL_LIST_FORM_TITLE' => 'Lista de plantillas de la guía inteligente',
    'LNK_IMPORT_CUSTOMER_JOURNEY_TEMPLATES' => 'Importar plantillas de la guía inteligente',
    'LBL_MODULE_TITLE' => 'Plantillas de la guía inteligente',
    'LBL_MODULE_NAME' => 'Plantillas de la guía inteligente',
    'LBL_NEW_FORM_TITLE' => 'Nueva plantilla de la guía inteligente',
    'LBL_REMOVE' => 'Eliminar',
    'LBL_SEARCH_FORM_TITLE' => 'Buscar plantillas de la guía inteligente',
    'LBL_TYPE' => 'Tipo',
    'LNK_LIST' => 'Plantillas de la guía inteligente',
    'LNK_NEW_RECORD' => 'Crear plantilla de la guía inteligente',
    'LBL_COPIES' => 'Copias',
    'LBL_COPIED_TEMPLATE' => 'Plantilla copiada',
    'LBL_IMPORT_TEMPLATES_BUTTON_LABEL' => 'Importar plantillas',
    'LBL_IMPORT_TEMPLATES_SUCCESS_MESSAGE' => 'Se han importado plantillas.',
    'LBL_RESAVE_TEMPLATES_BUTTON_LABEL' => 'Volver a guardar las plantillas',
    'LBL_RESAVE_TEMPLATES_SUCCESS_MESSAGE' => 'Se han vuelto a guardar las plantillas.',
    'LNK_VIEW_RECORDS' => 'Ver plantillas de la guía inteligente',
    'LNK_DRI_WORKFLOW_TEMPLATE_LIST' => 'Ver las plantillas de la guía inteligente',
    'LBL_AVAILABLE_MODULES' => 'Módulos disponibles',
    'LBL_CANCEL_ACTION' => 'Cancelar acción',
    'LBL_NOT_APPLICABLE_ACTION' => 'Acción no aplicable',
    'LBL_POINTS' => 'Puntos',
    'LBL_RELATED_ACTIVITIES' => 'Actividades relacionadas',
    'LBL_ACTIVE' => 'Activo',
    'LBL_ASSIGNEE_RULE' => 'Regla de asignado',
    'LBL_TARGET_ASSIGNEE' => 'Destinatario de destino',
    'LBL_STAGE_NUMBERS' => 'Numeración de etapas',
    'LBL_EXPORT_BUTTON_LABEL' => 'Exportar',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_BUTTON_LABEL' => 'Importar',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_TEXT' => 'Crear/actualizar automáticamente un nuevo registro de la plantilla de la guía inteligente mediante la importación de un archivo *.json desde su sistema de archivos.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_CREATE_SUCCESS' => 'La plantilla <a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a> se ha creado correctamente.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_UPDATE_SUCCESS' => 'La plantilla <a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a> se actualizó correctamente.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_DUPLICATE_NAME_ERROR' => 'Error de importación. Ya existe una plantilla denominada "<a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a>". Cambie el nombre del registro importado e inténtelo de nuevo o utilice "Copiar" para crear una plantilla de guía inteligente duplicada.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_UPDATE_CONFIRM' => 'Ya existe una plantilla con este ID. Para actualizar la plantilla existente, haga clic en <b>Confirmar</b>. Para salir sin hacer ningún cambio en la plantilla existente, haga clic en <b>Cancelar</b>.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_TEMPLATE_DELETED' => 'La plantilla que intenta importar se ha eliminado en la instancia actual.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_EMPTY_WARNING' => 'Seleccione un archivo *.json válido.',
    'LBL_CHECKING_IMPORT_UPLOAD' => 'Validando',
    'LBL_IMPORTING_TEMPLATE' => 'Importando',
    'LBL_DISABLED_STAGE_ACTIONS' => 'Acciones de fase deshabilitadas',
    'LBL_DISABLED_ACTIVITY_ACTIONS' => 'Acciones de actividad deshabilitadas',
    'LBL_FORMS' => 'Formularios',
    'LBL_ACTIVE_LIMIT' => 'Límite de guías inteligentes activas',
    'LBL_WEB_HOOKS' => 'Web Hooks',
    'LBL_START_NEXT_JOURNEY_ACTIVITIES' => 'Siguiente actividad de inicio de la guía inteligente',
    'LBL_START_NEXT_JOURNEY_STAGES' => 'Iniciar siguiente enlace de fase de la guía inteligente',
    'LBL_SMART_GUIDE_ACCESSIBLE' => 'Elija los módulos donde la Guía inteligente debe ser accesible',
    'LBL_SMART_GUIDE_MODIFY_ACTIONS' => 'En una fase puede añadir más actividades o eliminarlas. Deshabilite las acciones a las que no desea que el usuario tenga acceso en esta guía inteligente',
    'LBL_SMART_GUIDE_DISABLE_ACTIONS' => 'En una actividad puede añadir más actividades como subactividades. Deshabilite las acciones a las que no desea que el usuario tenga acceso en esta guía inteligente',
    'LBL_SMART_GUIDE_ACTIVATES' => 'Cuántas de estas guías inteligentes pueden estar activas en un registro a la vez',
    'LBL_SMART_GUIDE_TARGET_ASSIGNEE' => 'Si está marcado, si el asignado objetivo = asignado principal, cuando se cambia el usuario "Asignado a" en un elemento principal, los usuarios "Asignado a" también cambiarán automáticamente en las guías inteligentes, fases y actividades. Tenga en cuenta que la configuración de Asignado objetivo en las plantillas de actividad tiene prioridad sobre la plantilla de la guía inteligente',
    'LBL_SMART_GUIDE_USER_ASSIGNED' => 'Cuándo se debe asignar un usuario a las actividades',
    'LBL_SMART_GUIDE_ACTIVITIES_ASSIGNED' => 'A quién se le debe asignar la actividad',
    'LBL_SMART_GUIDE_STAGE_NUMBERS' => 'Este interruptor le permite mostrar u ocultar la numeración automática de etapas.',
    'CJ_FORMS_LBL_PARENT_NAME' => 'Guía inteligente/Fase/Plantilla de actividades',
];
