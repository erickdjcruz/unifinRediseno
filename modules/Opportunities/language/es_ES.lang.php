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
    'LBL_OPPORTUNITIES_LIST_DASHBOARD' => 'Cuadro de mando de la lista de oportunidades',
    'LBL_OPPORTUNITIES_RECORD_DASHBOARD' => 'Cuadro de mando de la lista de oportunidades',
    'LBL_OPPORTUNITIES_MULTI_LINE_DASHBOARD' => 'Panel de enfoque en las oportunidades - Consola',
    'LBL_OPPORTUNITIES_FOCUS_DRAWER_DASHBOARD' => 'Panel de enfoque en las oportunidades',
    'LBL_RENEWAL_OPPORTUNITY' => 'Oportunidad de renovación',

    'LBL_MODULE_NAME' => 'Oportunidades',
    'LBL_MODULE_NAME_SINGULAR' => 'Oportunidad',
    'LBL_MODULE_TITLE' => 'Oportunidades: Inicio',
    'LBL_SEARCH_FORM_TITLE' => 'Búsqueda de Oportunidades',
    'LBL_VIEW_FORM_TITLE' => 'Vista de Oportunidades',
    'LBL_LIST_FORM_TITLE' => 'Lista de Oportunidades',
    'LBL_OPPORTUNITY_NAME' => 'Nombre Oportunidad:',
    'LBL_OPPORTUNITY' => 'Oportunidad:',
    'LBL_NAME' => 'Nombre Oportunidad',
    'LBL_TIME' => 'Tiempo',
    'LBL_INVITEE' => 'Contactos',
    'LBL_CURRENCIES' => 'Monedas',
    'LBL_LIST_OPPORTUNITY_NAME' => 'Nombre',
    'LBL_LIST_ACCOUNT_NAME' => 'Cuenta',
    'LBL_LIST_DATE_CLOSED' => 'Fecha Cierre',
    'LBL_LIST_AMOUNT' => 'Probable',
    'LBL_LIST_AMOUNT_USDOLLAR' => 'Cantidad convertida',
    'LBL_ACCOUNT_ID' => 'ID de Cuenta',
    'LBL_CURRENCY_RATE' => 'Tipo de Cambio',
    'LBL_CURRENCY_ID' => 'ID de Moneda',
    'LBL_CURRENCY_NAME' => 'Nombre de Moneda',
    'LBL_CURRENCY_SYMBOL' => 'Símbolo de Moneda',
//DON'T CONVERT THESE THEY ARE MAPPINGS
    'db_sales_stage' => 'LBL_LIST_SALES_STAGE',
    'db_name' => 'LBL_NAME',
    'db_amount' => 'LBL_LIST_AMOUNT',
    'db_date_closed' => 'LBL_LIST_DATE_CLOSED',
//END DON'T CONVERT
    'UPDATE' => 'Oportunidad - Actualizar Moneda',
    'UPDATE_DOLLARAMOUNTS' => 'Actualizar Cantidades en Dólares EEUU',
    'UPDATE_VERIFY' => 'Verificar Cantidades',
    'UPDATE_VERIFY_TXT' => 'Verifica que los valores de las cantidades en las oportunidades son números decimales válidos con sólo caracteres numéricos (0-9) y decimales(.)',
    'UPDATE_FIX' => 'Corregir Cantidades',
    'UPDATE_FIX_TXT' => 'Intenta corregir cualquier cantidad no válida creando un número decimal válido a partir de la cantidad actual. Antes realiza una copia de seguridad de todas las cantidades modificadas en el campo de base de datos amount_backup. Si tras la correción detecta incidencias, no vuelva a realizar esta operación sin restaurar los valores previos desde la copia de seguridad ya que si no sobrescribirá la copia de seguridad con nuevos datos no válidos.',
    'UPDATE_DOLLARAMOUNTS_TXT' => 'Actualiza las cantidades en Dólares EEUU para las oportunidades basadas en el conjunto actual de cambios de moneda. Este valor se usa para calcular gráficas y vistas de listas de cantidades monetarias.',
    'UPDATE_CREATE_CURRENCY' => 'Creación de nueva moneda:',
    'UPDATE_VERIFY_FAIL' => 'Fallo de verificación de registro:',
    'UPDATE_VERIFY_CURAMOUNT' => 'Cantidad actual:',
    'UPDATE_VERIFY_FIX' => 'La corrección daría',
    'UPDATE_INCLUDE_CLOSE' => 'Registros cerrados incluidos',
    'UPDATE_VERIFY_NEWAMOUNT' => 'Nueva cantidad:',
    'UPDATE_VERIFY_NEWCURRENCY' => 'Nueva moneda:',
    'UPDATE_DONE' => 'Hecho',
    'UPDATE_BUG_COUNT' => 'Problemas detectados cuya resolución se ha intentado:',
    'UPDATE_BUGFOUND_COUNT' => 'Problemas detectados:',
    'UPDATE_COUNT' => 'Registros actualizados:',
    'UPDATE_RESTORE_COUNT' => 'Registros con cantidades restauradas:',
    'UPDATE_RESTORE' => 'Restaurar Cantidades',
    'UPDATE_RESTORE_TXT' => 'Restaura los valores de las cantidades desde la copia de seguridad creada durante la corrección.',
    'UPDATE_FAIL' => 'No ha podido actualizarse -',
    'UPDATE_NULL_VALUE' => 'La cantidad es NULL, estableciéndola a 0 -',
    'UPDATE_MERGE' => 'Unificar Monedas',
    'UPDATE_MERGE_TXT' => 'Unifica múltiples monedas en una única moneda. Si detecta que hay múltiples registros de moneda para la misma moneda, puede unificarlas. Esto también unificará las monedas para el resto de módulos.',
    'LBL_ACCOUNT_NAME' => 'Cuenta:',
    'LBL_CURRENCY' => 'Moneda:',
    'LBL_DATE_CLOSED' => 'Fecha de Cierre Prevista:',
    'LBL_DATE_CLOSED_TIMESTAMP' => 'Fecha Prevista de Cierre Timestamp',
    'LBL_TYPE' => 'Tipo:',
    'LBL_CAMPAIGN' => 'Campaña:',
    'LBL_NEXT_STEP' => 'Próximo paso:',
    'LBL_SERVICE_START_DATE' => 'Fecha de inicio del servicio',
    'LBL_LEAD_SOURCE' => 'Toma de contacto',
    'LBL_SALES_STAGE' => 'Etapa de ventas:',
    'LBL_SALES_STATUS' => 'Estado',
    'LBL_PROBABILITY' => 'Probabilidad (%):',
    'LBL_DESCRIPTION' => 'Descripción:',
    'LBL_DUPLICATE' => 'Posible oportunidad duplicada',
    'MSG_DUPLICATE' => 'El registro de oportunicad que va a crear podría ser un duplicado de otro registro de oportunidad existente. Los registros de oportunidad con nombres similares aparecen en una lista a continuación.<br>Haga clic en Guardar para continuar con la creación de esta oportunidad, o en Cancelar para volver al módulo sin crear la oportunidad.',
    'LBL_NEW_FORM_TITLE' => 'Nueva Oportunidad',
    'LNK_NEW_OPPORTUNITY' => 'Nueva Oportunidad',
    'LNK_CREATE' => 'Nueva Venta',
    'LNK_OPPORTUNITY_LIST' => 'Ver Oportunidades',
    'ERR_DELETE_RECORD' => 'Debe especificar un número de registro para eliminar la oportunidad.',
    'LBL_TOP_OPPORTUNITIES' => 'Mis Principales Oportunidades',
    'NTC_REMOVE_OPP_CONFIRMATION' => '¿Está seguro de que desea eliminar este contacto de la oportunidad?',
    'OPPORTUNITY_REMOVE_PROJECT_CONFIRM' => '¿Está seguro de que desea eliminar esta oportunidad del proyecto?',
    'LBL_DEFAULT_SUBPANEL_TITLE' => 'Oportunidades',
    'LBL_ACTIVITIES_SUBPANEL_TITLE' => 'Actividades',
    'LBL_HISTORY_SUBPANEL_TITLE' => 'Historial',
    'LBL_RAW_AMOUNT' => 'Importe Bruto',
    'LBL_LEADS_SUBPANEL_TITLE' => 'Clientes Potenciales',
    'LBL_CONTACTS_SUBPANEL_TITLE' => 'Contactos',
    'LBL_DOCUMENTS_SUBPANEL_TITLE' => 'Documentos',
    'LBL_PROJECTS_SUBPANEL_TITLE' => 'Proyectos',
    'LBL_ASSIGNED_TO_NAME' => 'Asignado a:',
    'LBL_LIST_ASSIGNED_TO_NAME' => 'Usuario Asignado',
    'LBL_LIST_SALES_STAGE' => 'Etapa de Ventas',
    'LBL_MY_CLOSED_OPPORTUNITIES' => 'Mis Oportunidades Cerradas',
    'LBL_TOTAL_OPPORTUNITIES' => 'Oportunidades Totales',
    'LBL_CLOSED_WON_OPPORTUNITIES' => 'Oportunidades Ganadas',
    'LBL_ASSIGNED_TO_ID' => 'Usuario Asignado:',
    'LBL_CREATED_ID' => 'Creado por ID',
    'LBL_MODIFIED_ID' => 'Modificado por ID',
    'LBL_MODIFIED_NAME' => 'Modificada por Nombre de Usuario',
    'LBL_CREATED_USER' => 'Usuario Creado',
    'LBL_MODIFIED_USER' => 'Usuario Modificado',
    'LBL_CAMPAIGN_OPPORTUNITY' => 'Oportunidad de campaña',
    'LBL_PROJECT_SUBPANEL_TITLE' => 'Proyectos',
    'LABEL_PANEL_ASSIGNMENT' => 'Asignación',
    'LNK_IMPORT_OPPORTUNITIES' => 'Importar Oportunidades',
    'LBL_EDITLAYOUT' => 'Editar diseño' /*for 508 compliance fix*/,
    //For export labels
    'LBL_EXPORT_CAMPAIGN_ID' => 'Id de campaña',
    'LBL_OPPORTUNITY_TYPE' => 'Tipo de oportunidad',
    'LBL_EXPORT_ASSIGNED_USER_NAME' => 'Nombre del usuario asignado',
    'LBL_EXPORT_ASSIGNED_USER_ID' => 'ID del usuario asignado',
    'LBL_EXPORT_MODIFIED_USER_ID' => 'Modificado por ID',
    'LBL_EXPORT_CREATED_BY' => 'Creado por ID',
    'LBL_EXPORT_NAME' => 'Nombre',
    // SNIP
    'LBL_CONTACT_HISTORY_SUBPANEL_TITLE' => 'Emails de contactos relacionados',
    'LBL_FILENAME' => 'Adjunto',
    'LBL_PRIMARY_QUOTE_ID' => 'Presupuesto Principal',
    'LBL_CONTRACTS' => 'Contratos',
    'LBL_CONTRACTS_SUBPANEL_TITLE' => 'Contratos',
    'LBL_PRODUCTS' => 'Elementos presupuestados',
    'LBL_RLI' => 'Línea de Ingreso',
    'LNK_OPPORTUNITY_REPORTS' => 'Ver Informes de Oportunidades',
    'LBL_QUOTES_SUBPANEL_TITLE' => 'Presupuestos',
    'LBL_TEAM_ID' => 'ID de Equipo',
    'LBL_TIMEPERIODS' => 'Periodos de Tiempo',
    'LBL_TIMEPERIOD_ID' => 'ID de Periodo de tiempo',
    'LBL_COMMITTED' => 'Objectivo Alcanzado',
    'LBL_FORECAST' => 'Incluido en Previsión',
    'LBL_COMMIT_STAGE' => 'Fase de Compromiso',
    'LBL_COMMIT_STAGE_FORECAST' => 'Etapa de la previsión',
    'LBL_WORKSHEET' => 'Hoja de Trabajo',
    'LBL_PURCHASED_LINE_ITEMS' => 'Elementos comprados',

    // KPI Metrics
    'LBL_ORGANIZE' => 'Organizar',
    'LBL_CREATE_NEW' => 'Crear nuevo',
    'LBL_MANAGE' => 'Gestionar',
    'LBL_SEE_DETAILS' => 'Mostrar detalles',
    'LBL_HIDE_NEW' => 'Ocultar',

    'LBL_FORECASTED_LIKELY' => 'Previsto probable',
    'LBL_LOST' => 'Perdido',
    'LBL_RENEWAL' => 'Renovación',
    'LBL_RENEWAL_OPPORTUNITIES' => 'Oportunidades de renovación',
    'LBL_RENEWAL_PARENT' => 'Oportunidad de padre',
    'LBL_PARENT_RENEWAL_OPPORTUNITY_ID' => 'ID del padre de renovación',
    'LBL_MONTH_YEAR_RENEWAL' => '{{month}}, {{year}}',

    'LBL_WIDGET_SALES_STAGE' => 'Etapa de venta',
    'LBL_WIDGET_DATE_CLOSED' => 'Fecha de cierre prevista',
    'LBL_WIDGET_AMOUNT' => 'Cantidad',

    'TPL_RLI_CREATE' => 'Una oportunidad debe tener una Línea de Ingreso asociada.',
    'TPL_RLI_CREATE_LINK_TEXT' => 'Crear una línea de ingreso',
    'LBL_PRODUCTS_SUBPANEL_TITLE' => 'Elementos presupuestados',
    'LBL_RLI_SUBPANEL_TITLE' => 'Líneas de Ingreso',

    'LBL_TOTAL_RLIS' => 'nº Total de Líneas de Ingreso',
    'LBL_CLOSED_RLIS' => 'nº de Líneas de Ingreso Cerradas',
    'LBL_CLOSED_WON_RLIS' => '# de partidas de ingresos cerradas',
    'LBL_SERVICE_OPEN_FLEX_DURATION_RLIS' => 'Número de elementos de ingresos de duración flexible de Open Service',
    'NOTICE_NO_DELETE_CLOSED_RLIS' => 'No puede eliminar Oportunidades que contienen Líneas de Ingreso cerradas',
    'WARNING_NO_DELETE_CLOSED_SELECTED' => 'Uno o más de los registros seleccionados contienen Líneas de Ingreso cerradas y no pueden ser eliminados.',
    'LBL_INCLUDED_RLIS' => 'Nº de artículos incluidos en líneas de ingresos',
    'LBL_UPDATE_OPPORTUNITIES_RLIS' => 'Actualización de apertura',
    'LBL_CASCADE_RLI_EDIT' => 'Actualizar una línea de ingreso abierta',
    'LBL_CASCADE_RLI_CREATE' => 'Establecer entre líneas de ingresos',
    'LBL_SERVICE_START_DATE_INVALID' => 'La fecha de inicio del servicio no puede ser posterior a la fecha de fin del servicio de cualquier línea de ingreso adicional abierta.',

    'LBL_QUOTE_SUBPANEL_TITLE' => 'Presupuestos',
    'LBL_FILTER_OPPORTUNITY_TEMPLATE' => 'Oportunidades por una cuenta dinámica',
    'LBL_TOP_10_OPP' => 'Las 10 mejores oportunidades abiertas',
    'LBL_DASHLET_MY_ACTIVE_OPP' => 'Dashlet: Mis oportunidades activas',
    'LBL_MY_ACTIVE_OPP' => 'Mis oportunidades activas',


    // Config
    'LBL_OPPS_CONFIG_VIEW_BY_LABEL' => 'Jerarquía de oportunidades',
    'LBL_OPPS_CONFIG_VIEW_BY_DATE_ROLLUP' => 'Configure el campo de Fecha de cierre prevista en los registros de Oportunidades resultantes para que sean las fechas de cierre más tempranas o más tardías de las líneas de ingresos existentes',

    //Dashlet
    'LBL_PIPELINE_TOTAL_IS' => 'Total de Pipeline es',

    'LBL_OPPORTUNITY_ROLE' => 'Rol en Oportunidad',
    'LBL_NOTES_SUBPANEL_TITLE' => 'Notas',
    'LBL_TAB_OPPORTUNITY' => 'Revisión de {{module}}',

    // Help Text
    'LBL_OPPS_CONFIG_ALERT' => 'Si hace clic en Confirmar borrará TODOS los datos de Previsiones y cambiará la Vista de Oportunidades. Si esto no es lo que desea hacer, haga clic en cancelar para volver a la configuración anterior.',
    'LBL_OPPS_CONFIG_ALERT_TO_OPPS' =>
        'Al hacer clic en Confirmar, borrará TODOS los datos de previsiones y cambiará la vista Oportunidades. '
        . 'Además, se deshabilitarán TODAS las definiciones de procesos con un módulo de público objetivo de las líneas de ingresos. '
        . 'Si no es lo que desea, haga clic en Cancelar para volver a la configuración anterior.',
    'LBL_OPPS_CONFIG_SALES_STAGE_1a' => 'Si todos las líneas de ingresos están cerrados y al menos se ha ganado uno,',
    'LBL_OPPS_CONFIG_SALES_STAGE_1b' => 'la Fase de ventas de oportunidades se define como "Ganada".',
    'LBL_OPPS_CONFIG_SALES_STAGE_2a' => 'Si todas las líneas de ingresos están en la Fase de ventas "Cerrado perdido",',
    'LBL_OPPS_CONFIG_SALES_STAGE_2b' => 'la Fase de ventas de oportunidades se define como "Perdida".',
    'LBL_OPPS_CONFIG_SALES_STAGE_3a' => 'Si alguna línea de ingresos todavía está abierta,',
    'LBL_OPPS_CONFIG_SALES_STAGE_3b' => 'la Oportunidad se marcará con la Fase de ventas menos avanzada.',

// BEGIN ENT/ULT

    // Opps Config - View By Opportunities
    'LBL_HELP_CONFIG_OPPS' => 'Tras iniciar este cambio, las notas resumen de las líneas de ingresos se construirán en segundo plano. Cuando las notas se completen y estén disponibles, se enviará una notificación a la dirección de correo electrónico de su perfil de usuario. Si su instancia está configurada como {{forecasts_module}}, Sugar también le enviará una notificación cuando sus registros {{module_name}} se sincronicen con el módulo {{forecasts_module}} y estén disponibles para nuevos {{forecasts_module}}. Tenga en cuenta que su instancia se debe configurar para enviar correos electrónicos en Admin > Configuración de correo electrónico para que se envíen las notificaciones.',

    // Opps Config - View By Opportunities And RLIs
    'LBL_HELP_CONFIG_RLIS' => 'Tras iniciar este cambio, se crearán registros de las líneas de ingresos para cada {{module_name}} existente en segundo plano. Cuando los Elementos de las líneas de ingresos se completen y estén disponibles, se enviará una notificación a la dirección de correo electrónico de su perfil de usuario. Tenga en cuenta que su instancia se debe configurar para enviar correos electrónicos en Admin > Configuración de correo electrónico para que se envíen las notificaciones.',
    // List View Help Text
    'LBL_HELP_RECORDS' => 'El módulo {{plural_module_name}} le permite realizar un seguimiento de las ventas individuales de principio a fin. Cada registro de {{module_name}} representa una posible venta e incluye datos relevantes de la venta, así como los relativos a otros registros importantes como {{quotes_module}}, {{contacts_module}}, etc. Un {{module_name}} suele progresar a través de varias Etapas de Venta hasta que se marca como "Ganado" o "Perdido". {{plural_module_name}} se pueden aprovechar aún más mediante el uso del módulo de {{forecasts_singular_module}} de Sugar para comprender y predecir las tendencias de ventas, así como enfocar el trabajo para alcanzar las cuotas de ventas.',

    // Record View Help Text
    'LBL_HELP_RECORD' => 'El módulo {{plural_module_name}} le permite realizar un seguimiento de las ventas individuales y los artículos pertenecientes a esas ventas de principio a fin. Cada registro de {{module_name}} representa una venta potencial e incluye datos de venta relevantes, además de los relacionados con otros registros importantes como {{quotes_module}}, {{contacts_module}}, etc.

- Edite el registro haciendo clic en el campo individual o el botón Editar.
- Vea o modifique enlaces a otros registros en los subpaneles cambiando el panel inferior izquierdo a "Vista de datos".
- Comente o vea comentarios de otros usuarios y el historial de cambios del registro en {{activitystream_singular_module}} cambiando el panel inferior izquierdo a "Flujo de Actividades".
- Siga o guarde como favorito el registro con los iconos a la derecha del nombre del registro.
- Hay acciones adicionales disponibles en el menú desplegable Acciones a la derecha del botón Editar.',

    // Create View Help Text
    'LBL_HELP_CREATE' => 'El módulo {{plural_module_name}} le permite realizar un seguimiento de las ventas individuales y los artículos pertenecientes a esas ventas de principio a fin. Cada registro de {{module_name}} representa una venta potencial e incluye datos de venta relevantes, además de los relacionados con otros registros importantes como {{quotes_module}}, {{contacts_module}}, etc.

Para crear un {{module_name}}:
1. Proporcione los valores para los campos según desee.
 - Debe completar los campos marcados como "Obligatorio" antes de guardar.
 - Haga clic en "Mostrar más" para mostrar campos adicionales si fuese necesario.
2. Haga clic en "Guardar" para finalizar el registro nuevo y volver a la página anterior.',

// END ENT/ULT

    //Marketo
    'LBL_MKTO_SYNC' => 'Sincronización con Marketo&reg;',
    'LBL_MKTO_ID' => 'ID de cliente potencial de Marketo',

    'LBL_DASHLET_TOP10_SALES_OPPORTUNITIES_NAME' => '10 principales Oportunidades de venta',
    'LBL_TOP10_OPPORTUNITIES_CHART_DESC' => 'Muestra las diez principales Oportunidades en un gráfico de burbujas.',
    'LBL_TOP10_OPPORTUNITIES_MY_OPP' => 'Mis oportunidades',
    'LBL_TOP10_OPPORTUNITIES_MY_TEAMS_OPP' => "Las oportunidades de mi equipo",

    'LBL_PIPELINE_ERR_CLOSED_SALES_STAGE' => 'No se ha podido cambiar el {{fieldName}} ya que este {{moduleSingular}} no tiene elementos de línea abiertos.',
    'TPL_ACTIVITY_TIMELINE_DASHLET' => 'Línea de tiempo de oportunidad',

    'LBL_CASCADE_SERVICE_WARNING' => ' no se puede establecer entre ninguna de estas Líneas de ingresos porque no son servicios. ¿Desea continuar con la creación?',
    'LBL_CASCADE_DURATION_WARNING' => ' no se puede establecer entre ninguna de estas Líneas de ingresos porque sus duraciones están bloqueadas. ¿Desea continuar con la creación?',

    // AI Predict
    'LBL_AI_OPPORTUNITY_CLOSE_PREDICTION_NAME' => 'Predicción de cierre de oportunidades',
    'LBL_AI_OPPORTUNITY_CLOSE_PREDICTION_DESC' => 'Ver los detalles de la predicción para una oportunidad específica',
    'LBL_AI_WINRATE' => 'Tasa de ganados',
    'LBL_AI_WONOPP' => 'Oportunidades ganadas',
    'LBL_AI_CLOSINGTIME' => 'Horario de cierre',
    'LBL_AI_CLOSEDOPP' => 'Oportunidades cerradas',
    'LBL_AI_LEADTIMESPAN' => 'Tiempo transcurrido entre la creación de la oportunidad y la oportunidad ganada',
];
