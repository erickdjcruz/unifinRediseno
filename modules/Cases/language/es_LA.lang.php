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
    // Dashboard Names
    'LBL_CASES_LIST_DASHBOARD' => 'Tablero de Lista de Casos',
    'LBL_CASES_RECORD_DASHBOARD' => 'Tablero de Registro de Casos',
    'LBL_CASES_MULTI_LINE_DASHBOARD' => 'Panel de enfoque en los casos - Consola',
    'LBL_CASES_FOCUS_DRAWER_DASHBOARD' => 'Panel de Enfoque de Casos',
    'LBL_CLAIM_AND_CLOSE' => 'Botón de acción de caso',
    'LBL_ACTION_CLAIM' => 'Reclamo de acción',
    'LBL_ACTION_CLOSE' => 'Cierre de acción',
    'LBL_ACTION_UNASSIGN' => 'No asignación de acción',
    'LBL_CLAIM_BUTTON_TEXT' => 'Reclamar',
    'LBL_CLOSE_BUTTON_TEXT' => 'Cerrar',
    'LBL_UNASSIGN_BUTTON_TEXT' => 'Desasignar',

    'ERR_DELETE_RECORD' => 'Debe especificar un número de registro a eliminar.',
    'LBL_ACCOUNT_ID' => 'ID de la Cuenta',
    'LBL_ACCOUNT_NAME' => 'Cuenta:',
    'LBL_ACCOUNTS_SUBPANEL_TITLE' => 'Cuentas',
    'LBL_ACTIVITIES_SUBPANEL_TITLE' => 'Actividades',
    'LBL_ATTACH_NOTE' => 'Adjuntar Nota',
    'LBL_BUGS_SUBPANEL_TITLE' => 'Errores',
    'LBL_CASE_NUMBER' => 'Número del Caso:',
    'LBL_CASE_SUBJECT' => 'Asunto del Caso:',
    'LBL_CASE' => 'Caso:',
    'LBL_CONTACT_CASE_TITLE' => 'Contacto-Caso:',
    'LBL_CONTACT_NAME' => 'Contacto:',
    'LBL_CONTACT_ROLE' => 'Rol:',
    'LBL_CONTACTS_SUBPANEL_TITLE' => 'Contactos',
    'LBL_DEFAULT_SUBPANEL_TITLE' => 'Casos',
    'LBL_DESCRIPTION' => 'Descripción:',
    'LBL_FILENANE_ATTACHMENT' => 'Archivar Adjunto',
    'LBL_HISTORY_SUBPANEL_TITLE' => 'Historial',
    'LBL_INVITEE' => 'Contactos',
    'LBL_MEMBER_OF' => 'Cuenta',
    'LBL_MODULE_NAME' => 'Casos',
    'LBL_MODULE_NAME_SINGULAR' => 'Caso',
    'LBL_MODULE_TITLE' => 'Casos: Inicio',
    'LBL_NEW_FORM_TITLE' => 'Nuevo Caso',
    'LBL_NUMBER' => 'Número:',
    'LBL_PRIORITY' => 'Prioridad:',
    'LBL_PROJECTS_SUBPANEL_TITLE' => 'Proyectos',
    'LBL_DOCUMENTS_SUBPANEL_TITLE' => 'Documentos',
    'LBL_RESOLUTION' => 'Resolución:',
    'LBL_SEARCH_FORM_TITLE' => 'Búsqueda de Casos',
    'LBL_STATUS' => 'Estado:',
    'LBL_SOURCE' => 'Fuente:',
    'LBL_SUBJECT' => 'Asunto:',
    'LBL_LIST_ACCOUNT_NAME' => 'Cuenta',
    'LBL_LIST_ASSIGNED' => 'Asignado a',
    'LBL_LIST_CLOSE' => 'Cerrar',
    'LBL_LIST_FORM_TITLE' => 'Lista de Casos',
    'LBL_LIST_LAST_MODIFIED' => 'Modificado',
    'LBL_LIST_MY_CASES' => 'Mis Casos Abiertos',
    'LBL_LIST_NUMBER' => 'Núm.',
    'LBL_LIST_PRIORITY' => 'Prioridad',
    'LBL_LIST_STATUS' => 'Estado',
    'LBL_LIST_SUBJECT' => 'Asunto',
    'LBL_LIST_ASSIGNED_TO_NAME' => 'Usuario Asignado',
    'LBL_SHOW_MORE' => 'Mostrar más Casos',
    'LNK_CASE_LIST' => 'Ver Casos',
    'LNK_NEW_CASE' => 'Nuevo Caso',
    'LBL_CASE_FROM_TWITTER_TITLE' => 'Tweet',
    'LNK_CREATE' => 'Nuevo Caso',
    'LNK_CREATE_WHEN_EMPTY' => 'Crear un Caso ahora',
    'NTC_REMOVE_FROM_BUG_CONFIRMATION' => '¿Está seguro de que desea quitar este caso del Error?',
    'NTC_REMOVE_INVITEE' => '¿Está seguro de que desea eliminar este Contacto del Caso?',
    'LBL_LIST_DATE_CREATED' => 'Fecha de Creación',
    'LBL_ASSIGNED_TO_NAME' => 'Asignado a',
    'LBL_TYPE' => 'Tipo',
    'LBL_WORK_LOG' => 'Registro de Trabajo',
    'LNK_IMPORT_CASES' => 'Importar Casos',
    'LNK_CASE_REPORTS' => 'Ver Informes de Casos',
    'LBL_SHOW_IN_PORTAL' => 'Mostrar en Portal',
    'LBL_CREATE_KB_DOCUMENT' => 'Crear Artículo',
    'LBL_KBCONTENTS_SUBPANEL_TITLE' => 'Base de Conocimiento',
    'LBL_REQUEST_CLOSE' => 'Solicitar cierre',
    'LBL_REQUEST_CLOSE_DATE' => 'Solicitar fecha de cierre',
    'LBL_REQUEST_CLOSE_LABEL' => 'Solicitar cerrar',
    'LBL_REQUEST_CLOSE_TOOLTIP' => 'Enviar una solicitud para cerrar este caso',
    'LBL_REQUEST_CLOSE_MESSAGE' => '¿Desea solicitar que este caso se cierre?',
    'LBL_DASHLET_REQUESTED_CLOSE_CASES' => 'Mis casos para los que se solicitó cierre',
    'LBL_MY_REQUESTED_CLOSE_CASES' => 'Mis casos para los que se solicitó cierre',
    'LBL_DASHLET_MY_REQUESTED_CLOSE_CASES_NAME' => 'Mis casos para los que se solicitó cierre',
    'LBL_DASHLET_REQUESTED_CLOSE_CASES_NAME' => 'Lista de casos para los que se solicitó cierre',
    'LBL_DASHLET_REQUESTED_CLOSE_CASES_DESCRIPTION' => 'Muestra una lista de casos asignados para los que se solicitó cierre',

    'LBL_CREATED_USER' => 'Usuario Creado',
    'LBL_MODIFIED_USER' => 'Usuario Modificado',
    'LBL_PROJECT_SUBPANEL_TITLE' => 'Proyectos',
    'LBL_CASE_INFORMATION' => 'Reseña General',

    //For export labels
    'LBL_MODIFIED_BY_NAME_OWNER' => 'Modificado por nombre del propietario',
    'LBL_MODIFIED_BY_NAME_MOD' => 'Modificado por nombre Mod',
    'LBL_CREATED_BY_NAME_OWNER' => 'Creado por nombre del propietario',
    'LBL_CREATED_BY_NAME_MOD' => 'Creado por nombre Mod',
    'LBL_ASSIGNED_USER_NAME_OWNER' => 'Propietario de nombre de usuario asignado',
    'LBL_ASSIGNED_USER_NAME_MOD' => 'Mod de nombre de usuario asignado',
    'LBL_TEAM_COUNT_OWNER' => 'Propietario del número de equipos',
    'LBL_TEAM_COUNT_MOD' => 'Mod de número de equipos',
    'LBL_TEAM_NAME_OWNER' => 'Propietario de nombre del equipo',
    'LBL_TEAM_NAME_MOD' => 'Mod de nombre del equipo',
    'LBL_ACCOUNT_NAME_OWNER' => 'Propietario de nombre de la cuenta',
    'LBL_ACCOUNT_NAME_MOD' => 'Mod de nombre de la cuenta',
    'LBL_MODIFIED_USER_NAME' => 'Nombre de usuario modificado',
    'LBL_MODIFIED_USER_NAME_OWNER' => 'Propietario de nombre de usuario modificado',
    'LBL_MODIFIED_USER_NAME_MOD' => 'Mod de nombre de usuario modificado',
    'LBL_PORTAL_VIEWABLE' => 'Visible en portal',
    'LBL_EXPORT_ASSIGNED_USER_ID' => 'ID de Usuario Asignado',
    'LBL_EXPORT_MODIFIED_USER_ID' => 'Modificador por ID',
    'LBL_EXPORT_CREATED_BY' => 'Creado por ID',
    'LBL_EXPORT_CREATED_BY_NAME' => 'Creado por nombre de usuario',
    'LBL_EXPORT_ASSIGNED_USER_NAME' => 'Nombre de Usuario Asignado',
    'LBL_EXPORT_TEAM_COUNT' => 'Número de equipos',
    'LBL_CHANGETIMERS' => 'Cambiar temporizadores',
    'LBL_PURCHASES_SUBPANEL_TITLE' => 'Adquisiciones',

    // SNIP
    'LBL_CONTACT_HISTORY_SUBPANEL_TITLE' => 'Relación de contactos en Correos Electrónicos',

    // Portal Tutorial content
    'LBL_PORTAL_TOUR_RECORDS_INTRO' => 'El módulo de Casos es para administrar problemas de soporte que afectan a su Cuenta. Utilice las flechas a continuación para hacer una visita guiada rápida.',
    'LBL_PORTAL_TOUR_RECORDS_PAGE' => 'Esta página muestra la lista de casos existentes asociados a su Cuenta.',
    'LBL_PORTAL_TOUR_RECORDS_FILTER' => 'Puede filtrar la lista de casos, proporcionando un término de búsqueda.',
    'LBL_PORTAL_TOUR_RECORDS_FILTER_EXAMPLE' => 'Por ejemplo, podría utilizar esto para encontrar un problema que se ha presentado anteriormente.',
    'LBL_PORTAL_TOUR_RECORDS_CREATE' => 'Si usted tiene un Caso de soporte nuevo que desea enviar, puede hacer clic aquí para enviar un Caso nuevo.',
    'LBL_PORTAL_TOUR_RECORDS_RETURN' => 'Al hacer clic aquí, regresará a esta vista en cualquier momento.',
    'TPL_ACTIVITY_TIMELINE_DASHLET' => 'Línea de Tiempo del Caso',

    'LBL_NOTES_SUBPANEL_TITLE' => 'Notas',

    // Help Text
    // List View Help Text
    'LBL_HELP_RECORDS' => 'El módulo {{plural_module_name}} se utiliza para realizar un seguimiento y gestionar productos y servicios relacionados con los problemas reportados a su organización por los clientes. {{plural_module_name}} normalmente están relacionados con un registro {{accounts_singular_module}} y múltiples {{plural_module_name}} pueden estar asociados a un simple {{accounts_singular_module}}. Hay varias formas de crear {{plural_module_name}} en Sugar tales como a través del módulo {{plural_module_name}}, mediante importación {{plural_module_name}}, o mediante conversión desde el correo electrónico. Una vez que {{module_name}} se crea, puede ver y editar la información relativa a la {{module_name}} vía la vista de registro {{module_name}}. Cada registro {{module_name}} puede relacionarse con otros registros de Sugar, tales como {{calls_module}}, {{contacts_module}}, {{bugs_module}}, y muchos otros.',

    // Record View Help Text
    'LBL_HELP_RECORD' => 'El módulo {{plural_module_name}} se utiliza para realizar un seguimiento y gestionar productos y servicios relacionados con los problemas informados a su organización por los clientes.

-Edite los campos de este registro, haga clic en un campo individual o en el botón Editar. 
- Visualice o modifique vínculos a otros registros en los subpaneles deslizando el panel inferior izquierdo a "Vista de Datos". 
- Haga y vea comentarios de los usuarios y registre el historial de cambios {{activitystream_singular_module}} deslizando el panel inferior izquierdo a "Últimas Acciones". 
- Siga o marque como favorito este registro utilizando los íconos a la derecha del nombre del registro. 
- Las acciones adicionales se encuentran disponibles en el menú desplegable Acciones, a la derecha del botón Editar.',

    // Create View Help Text
    'LBL_HELP_CREATE' => 'El módulo {{plural_module_name}} se utiliza para localizar y gestionar productos y servicios relacionados con los problemas que los clientes comunican a su organización.

Para crear un {{module_name}}:
1. Proporcione valores para los campos según se desee.
- Los campos marcados como "Obligatorios" deben ser completados antes de guardar.
- Haga clic en "Mostrar más" para exponer campos adicionales si es necesario.
2. Haga clic en "Guardar" para finalizar el nuevo registro y volver a la página anterior.',
    // Case deflection
    'LBL_NO_LOOKING_FOR' => '¿No ha encontrado lo que está buscando?',
    'LBL_CREATE_CASE' => 'Crear un {{module_name}}',

    'LBL_SLA_DETAILS' => 'Detalles de SLA',
    'LBL_HOURS' => 'Horas',
    'LBL_WIDGET_STATUS' => 'Estado',

    // KPI Metrics
    'LBL_ORGANIZE' => 'Organizar',
    'LBL_CREATE_NEW' => 'Crear nuevo',
    'LBL_MANAGE' => 'Gestionar',
    'LBL_SEE_DETAILS' => 'Ver detalles',
    'LBL_HIDE_NEW' => 'Ocultar',

    //For Kiosk labels
    'LBL_KIOSK_MODULE_NAME' => 'Casos',
    'LBL_KIOSK_MODULE_NAME_SINGULAR' => 'Caso',
    'LBL_KIOSK_CREATE' => 'Crear caso',
    'LBL_KIOSK_SUBJECT' => 'Asunto',
    'LBL_KIOSK_CASE_ID' => 'ID del caso',
    'LBL_KIOSK_STATUS' => 'Estado',
    'LBL_KIOSK_CATEGORY' => 'Categoría',
    'LBL_KIOSK_SUBMITTED_BY' => 'Enviado el {{date}}',
    'LBL_KIOSK_PRIORITY' => 'Prioridad',
    'LBL_KIOSK_CREATE_CASE' => 'Contáctenos',
    'LBL_KIOSK_DESCRIPTION' => 'Descripción',
    'LBL_KIOSK_CREATE_SUCCESS' => 'Su solicitud se envió correctamente.',
    'LBL_KIOSK_ATTACHED' => 'Todo lo adjunto',

    // External Users Subpanel
    'LBL_EXTERNAL_USERS_SUBPANEL_TITLE' => 'Usuarios externos',
    'LBL_ESCALATED_CASES' => 'Casos escalados',
];
