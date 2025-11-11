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
    'LBL_OPPORTUNITIES_LIST_DASHBOARD' => 'Tablero de Lista de Oportunidades',
    'LBL_OPPORTUNITIES_RECORD_DASHBOARD' => 'Tablero de Registro de Oportunidades',
    'LBL_OPPORTUNITIES_MULTI_LINE_DASHBOARD' => 'Panel de enfoque en las oportunidades - Consola',
    'LBL_OPPORTUNITIES_FOCUS_DRAWER_DASHBOARD' => 'Panel de Enfoque de Oportunidades',
    'LBL_RENEWAL_OPPORTUNITY' => 'Oportunidad de Renovación',

    'LBL_MODULE_NAME' => 'Oportunidades',
    'LBL_MODULE_NAME_SINGULAR' => 'Oportunidad',
    'LBL_MODULE_TITLE' => 'Oportunidades: Inicio',
    'LBL_SEARCH_FORM_TITLE' => 'Búsqueda de Oportunidades',
    'LBL_VIEW_FORM_TITLE' => 'Vista de Oportunidades',
    'LBL_LIST_FORM_TITLE' => 'Lista de Oportunidades',
    'LBL_OPPORTUNITY_NAME' => 'Nombre Oportunidad:',
    'LBL_OPPORTUNITY' => 'Oportunidad:',
    'LBL_NAME' => 'Nombre de Oportunidad',
    'LBL_TIME' => 'Hora',
    'LBL_INVITEE' => 'Contactos',
    'LBL_CURRENCIES' => 'Monedas',
    'LBL_LIST_OPPORTUNITY_NAME' => 'Nombre',
    'LBL_LIST_ACCOUNT_NAME' => 'Cuenta',
    'LBL_LIST_DATE_CLOSED' => 'Fecha Esperada de Cierre',
    'LBL_LIST_AMOUNT' => 'Probablemente',
    'LBL_LIST_AMOUNT_USDOLLAR' => 'Cantidad Convertida',
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
    'UPDATE' => 'Oportunidad - Actualizar moneda',
    'UPDATE_DOLLARAMOUNTS' => 'Actualizar Cantidades en Dólares EEUU',
    'UPDATE_VERIFY' => 'Verificar Cantidades',
    'UPDATE_VERIFY_TXT' => 'Verifica que los valores de las cantidades en las oportunidades son números decimales válidos con sólo caracteres numéricos (0-9) y decimales(.)',
    'UPDATE_FIX' => 'Corregir Cantidades',
    'UPDATE_FIX_TXT' => 'Intenta corregir cualquier cantidad no válida creando un número decimal válido a partir de la cantidad actual. Antes realiza una copia de seguridad de todas las cantidades modificadas en el campo de base de datos amount_backup. Si tras la corrección detecta problemas, no vuelva a realizar esta operación sin restaurar los valores previos desde la copia de seguridad ya que si no sobrescribirá la copia de seguridad con nuevos datos no válidos.',
    'UPDATE_DOLLARAMOUNTS_TXT' => 'Actualiza las cantidades en Dólares Eeuu para las oportunidades basadas en el conjunto actual de tipos de cambio. Este valor se usa para calcular gráficas y vistas de listas de cantidades monetarias.',
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
    'UPDATE_MERGE_TXT' => 'Unifica múltiples monedas en una única moneda. Si detecta que hay múltiples registros de tipo moneda para la misma moneda, puede unificarlas. Esto también unificará las monedas para el resto de módulos.',
    'LBL_ACCOUNT_NAME' => 'Cuenta:',
    'LBL_CURRENCY' => 'Moneda:',
    'LBL_DATE_CLOSED' => 'Fecha Esperada de Cierre:',
    'LBL_DATE_CLOSED_TIMESTAMP' => 'Fecha Esperada de Cierre Timestamp',
    'LBL_TYPE' => 'Tipo:',
    'LBL_CAMPAIGN' => 'Campaña:',
    'LBL_NEXT_STEP' => 'Próximo Paso:',
    'LBL_SERVICE_START_DATE' => 'Fecha de Inicio del Servicio',
    'LBL_LEAD_SOURCE' => 'Fuente del Cliente Potencial',
    'LBL_SALES_STAGE' => 'Etapa de Ventas:',
    'LBL_SALES_STATUS' => 'Estado',
    'LBL_PROBABILITY' => 'Probabilidad (%):',
    'LBL_DESCRIPTION' => 'Descripción:',
    'LBL_DUPLICATE' => 'Posible oportunidad duplicada',
    'MSG_DUPLICATE' => 'El registro para la oportunidad que va a crear podría ser un duplicado de otro registro de oportunidad existente. Los registros de oportunidad con nombres similares se listan a continuación.<br>Haga clic en Guardar para continuar con la creación de esta oportunidad, o en Cancelar para volver al módulo sin crear la oportunidad.',
    'LBL_NEW_FORM_TITLE' => 'Nueva Oportunidad',
    'LNK_NEW_OPPORTUNITY' => 'Nueva Oportunidad',
    'LNK_CREATE' => 'Crear Negocio',
    'LNK_OPPORTUNITY_LIST' => 'Ver Oportunidades',
    'ERR_DELETE_RECORD' => 'Debe especificar un número de registro a eliminar.',
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
    'LBL_CREATED_ID' => 'Creada por ID',
    'LBL_MODIFIED_ID' => 'Modificada por ID',
    'LBL_MODIFIED_NAME' => 'Modificada por Usuario',
    'LBL_CREATED_USER' => 'Usuario Creado',
    'LBL_MODIFIED_USER' => 'Usuario Modificado',
    'LBL_CAMPAIGN_OPPORTUNITY' => 'Oportunidad de Campaña',
    'LBL_PROJECT_SUBPANEL_TITLE' => 'Proyectos',
    'LABEL_PANEL_ASSIGNMENT' => 'Asignación',
    'LNK_IMPORT_OPPORTUNITIES' => 'Importar Oportunidades',
    'LBL_EDITLAYOUT' => 'Editar diseño' /*for 508 compliance fix*/,
    //For export labels
    'LBL_EXPORT_CAMPAIGN_ID' => 'Id de campaña',
    'LBL_OPPORTUNITY_TYPE' => 'Tipo de Oportunidad',
    'LBL_EXPORT_ASSIGNED_USER_NAME' => 'Nombre de Usuario Asignado',
    'LBL_EXPORT_ASSIGNED_USER_ID' => 'ID de Usuario Asignado',
    'LBL_EXPORT_MODIFIED_USER_ID' => 'Modificador por ID',
    'LBL_EXPORT_CREATED_BY' => 'Creado por ID',
    'LBL_EXPORT_NAME' => 'Nombre',
    // SNIP
    'LBL_CONTACT_HISTORY_SUBPANEL_TITLE' => 'Correos Electrónicos de contactos relacionados',
    'LBL_FILENAME' => 'Archivo Adjunto',
    'LBL_PRIMARY_QUOTE_ID' => 'Presupuesto Principal',
    'LBL_CONTRACTS' => 'Contratos',
    'LBL_CONTRACTS_SUBPANEL_TITLE' => 'Contratos',
    'LBL_PRODUCTS' => 'Partidas Individuales Cotizadas',
    'LBL_RLI' => 'Artículos de Línea de Ganancia',
    'LNK_OPPORTUNITY_REPORTS' => 'Ver Informes de Oportunidades',
    'LBL_QUOTES_SUBPANEL_TITLE' => 'Cotizaciones',
    'LBL_TEAM_ID' => 'ID de Equipo',
    'LBL_TIMEPERIODS' => 'Periodos de Tiempo',
    'LBL_TIMEPERIOD_ID' => 'Periodo de tiempo',
    'LBL_COMMITTED' => 'Comprometido',
    'LBL_FORECAST' => 'Incluyen en el pronóstico',
    'LBL_COMMIT_STAGE' => 'Fase de Compromiso',
    'LBL_COMMIT_STAGE_FORECAST' => 'Etapa de la previsión',
    'LBL_WORKSHEET' => 'Hoja de Trabajo',
    'LBL_PURCHASED_LINE_ITEMS' => 'Partidas individuales adquiridas',

    // KPI Metrics
    'LBL_ORGANIZE' => 'Organizar',
    'LBL_CREATE_NEW' => 'Crear nuevo',
    'LBL_MANAGE' => 'Gestionar',
    'LBL_SEE_DETAILS' => 'Ver detalles',
    'LBL_HIDE_NEW' => 'Ocultar',

    'LBL_FORECASTED_LIKELY' => 'Pronosticado probablemente',
    'LBL_LOST' => 'Perdido',
    'LBL_RENEWAL' => 'Renovación',
    'LBL_RENEWAL_OPPORTUNITIES' => 'Oportunidades de renovación',
    'LBL_RENEWAL_PARENT' => 'Oportunidad de padre',
    'LBL_PARENT_RENEWAL_OPPORTUNITY_ID' => 'ID del padre de renovación',
    'LBL_MONTH_YEAR_RENEWAL' => '{{month}}, {{year}}',

    'LBL_WIDGET_SALES_STAGE' => 'Etapa de Venta',
    'LBL_WIDGET_DATE_CLOSED' => 'Fecha de Cierre Prevista',
    'LBL_WIDGET_AMOUNT' => 'Cantidad',

    'TPL_RLI_CREATE' => 'Una oportunidad debe estar asociada con la Partida Individual de Ingresos.',
    'TPL_RLI_CREATE_LINK_TEXT' => 'Crear una Partida Individual de Ingresos.',
    'LBL_PRODUCTS_SUBPANEL_TITLE' => 'Partidas Individuales Cotizadas',
    'LBL_RLI_SUBPANEL_TITLE' => 'Artículos de Línea de Ganancia',

    'LBL_TOTAL_RLIS' => '# del Total de Partidas Individuales de Ingresos',
    'LBL_CLOSED_RLIS' => '# de Partidas Individuales de Ingresos Cerradas',
    'LBL_CLOSED_WON_RLIS' => '# de Partidas Individuales de Ingresos Cerradas Ganadas',
    'LBL_SERVICE_OPEN_FLEX_DURATION_RLIS' => '# de Partidas individuales de ingresos con duración flexible de Open Service',
    'NOTICE_NO_DELETE_CLOSED_RLIS' => 'No puede eliminar Oportunidades que contienen Partidas Individuales de Ingresos cerradas',
    'WARNING_NO_DELETE_CLOSED_SELECTED' => 'Uno o más de los registros seleccionados contiene Partidas Individuales de Ingresos cerradas y no se puede eliminar.',
    'LBL_INCLUDED_RLIS' => '# de Partidas Individuales de Ingresos Incluidas',
    'LBL_UPDATE_OPPORTUNITIES_RLIS' => 'Actualización de Apertura',
    'LBL_CASCADE_RLI_EDIT' => 'Actualizar Partidas individuales de Ingresos Abiertas',
    'LBL_CASCADE_RLI_CREATE' => 'Establecer entre Partidas de Ingresos',
    'LBL_SERVICE_START_DATE_INVALID' => 'La fecha de inicio del servicio no puede ser posterior a la fecha de finalización del servicio en ninguna partida individual de ingresos adicional abierta.',

    'LBL_QUOTE_SUBPANEL_TITLE' => 'Cotizaciones',
    'LBL_FILTER_OPPORTUNITY_TEMPLATE' => 'Oportunidades por cuenta dinámica',
    'LBL_TOP_10_OPP' => 'Las 10 mejores oportunidades abiertas',
    'LBL_DASHLET_MY_ACTIVE_OPP' => 'Dashlet: Mis oportunidades activas',
    'LBL_MY_ACTIVE_OPP' => 'Mis oportunidades activas',


    // Config
    'LBL_OPPS_CONFIG_VIEW_BY_LABEL' => 'Jerarquía de Oportunidad',
    'LBL_OPPS_CONFIG_VIEW_BY_DATE_ROLLUP' => 'Establezca el campo de Fecha de Cierre Esperada en los resultados de registros de Oportunidad por ser la fecha de cierre previa o última en Sub-Oportunidades existentes',

    //Dashlet
    'LBL_PIPELINE_TOTAL_IS' => 'Total de Proyectos de Ventas ',

    'LBL_OPPORTUNITY_ROLE' => 'Rol en Oportunidad',
    'LBL_NOTES_SUBPANEL_TITLE' => 'Notas',
    'LBL_TAB_OPPORTUNITY' => 'Revisión de {{module}}',

    // Help Text
    'LBL_OPPS_CONFIG_ALERT' => 'Al dar clic en Confirmar borrarás toda la información de pronósticos y también cambiará tu vista de Oportunidades. Si no deseas eso, dar clic en cancelar para regresar a la configuración previa.',
    'LBL_OPPS_CONFIG_ALERT_TO_OPPS' =>
        'Al hacer clic en Confirmar, borrará TODOS los datos de Pronósticos y cambiará la vista Oportunidades. '
        . 'Además, se deshabilitarán TODAS las definiciones de procesos con un módulo de público objetivo de suboportunidades. '
        . 'Si no es lo que deseaba, haga clic en Cancelar para volver a la configuración anterior.',
    'LBL_OPPS_CONFIG_SALES_STAGE_1a' => 'Si todas las Sub-Oportunidades se encuentran cerradas y como mínimo haya una ganada,',
    'LBL_OPPS_CONFIG_SALES_STAGE_1b' => 'la Etapa de Venta de la Oportunidad se encuentra en "Cerrada/Ganada".',
    'LBL_OPPS_CONFIG_SALES_STAGE_2a' => 'Si todas las Sub-Oportunidades están en Etapa de Venta "Cerrada/Perdida",',
    'LBL_OPPS_CONFIG_SALES_STAGE_2b' => 'la Etapa de Venta de la Oportunidad se encuentra en "Cerrada/Perdida".',
    'LBL_OPPS_CONFIG_SALES_STAGE_3a' => 'Si cualquier Sub-Oportunidad se encuentra abierta,',
    'LBL_OPPS_CONFIG_SALES_STAGE_3b' => 'la oportunidad de será marcada con la Etapa de Venta menos avanzada.',

// BEGIN ENT/ULT

    // Opps Config - View By Opportunities
    'LBL_HELP_CONFIG_OPPS' => 'Después de iniciar el cambio, las notas de integración de la Partida Individual de Ingresos de la Sub-Oportunidad serán creadas. Cuando las notas estén disponibles y completadas, será enviada a una notificación al correo electrónico del usuario. Si tu estancia se encuentra para {{forecasts_module}}, Sugar le enviará una notificación cuando tus registros en {{module_name}} estén sincronizados y disponibles para el módulo {{forecasts_module}} para el nuevo {{forecasts_module}}. Por favor, tenga en cuenta que tu instancia debe estar configurada en Admin > Ajustes Email para que las notificaciones sean enviadas.',

    // Opps Config - View By Opportunities And RLIs
    'LBL_HELP_CONFIG_RLIS' => 'Después de que haya iniciado este cambio, los registros de Sub-Oportunidades serán creados para cada {{module_name}} existente. Cuando las Sub-Oportunidades estén completadas y disponibles, será enviada una notificación al correo electrónico de registrado en tu perfil. Por favor toma en cuenta que la instancia debe estar configurada a través de Admin > Ajustes Email para que la notificaciónes sean enviadas.',
    // List View Help Text
    'LBL_HELP_RECORDS' => 'El módulo {{plural_module_name}} le permite realizar un seguimiento de las ventas individuales de principio a fin. Cada registro de {{module_name}} representa una posible venta e incluye datos relevantes de la venta, así como los relativos a otros registros importantes como {{quotes_module}}, {{contacts_module}}, etc. Un {{module_name}} suele progresar a través de varias Etapas de Venta hasta que se marca como "Ganado" o "Perdido". {{plural_module_name}} se pueden aprovechar aún más mediante el uso del módulo de {{forecasts_singular_module}} de Sugar para comprender y predecir las tendencias de ventas, así como enfocar el trabajo para alcanzar las cuotas de ventas.',

    // Record View Help Text
    'LBL_HELP_RECORD' => 'El módulo {{plural_module_name}} le permite realizar un seguimiento de las ventas individuales y los artículos pertenecientes a esas ventas de principio a fin. Cada registro de {{module_name}} representa una venta potencial e incluye datos de venta relevantes, además de los relacionados con otros registros importantes como {{quotes_module}}, {{contacts_module}}, etc.

- Edite el registro haciendo clic en el campo individual o el botón Editar.
- Vea o modifique enlaces a otros registros en los subpaneles cambiando el panel inferior izquierdo a "Vista de datos".
- Comente o vea comentarios de otros usuarios y el historial de cambios del registro en {{activitystream_singular_module}} cambiando el panel inferior izquierdo a "Flujo de actividades".
- Siga o guarde como favorito el registro con los iconos a la derecha del nombre del registro.
- Hay acciones adicionales disponibles en el menú desplegable Acciones a la derecha del botón Editar.',

    // Create View Help Text
    'LBL_HELP_CREATE' => 'El módulo {{plural_module_name}} le permite realizar un seguimiento de las ventas individuales y los artículos pertenecientes a esas ventas de principio a fin. Cada registro {{module_name}} representa una posible venta e incluye datos relevantes de la venta, así como relativos a otros registros importantes como {{quotes_module}}, {{contacts_module}}, etc.

Para crear un {{module_name}}:
1. Proporcione valores para los campos que desee.
 - Los campos marcados como "Obligatorio" se deben completar antes de guardar.
 - Haga clic en "Mostrar Más" para ver los campos adicionales si es necesario.
2. Haga clic en "Guardar" para finalizar el nuevo registro y volver a la página anterior.',

// END ENT/ULT

    //Marketo
    'LBL_MKTO_SYNC' => 'Sincronizar a Marketo&reg;',
    'LBL_MKTO_ID' => 'ID del Marketo Lead',

    'LBL_DASHLET_TOP10_SALES_OPPORTUNITIES_NAME' => 'Top 10 en Oportunidades de Ventas',
    'LBL_TOP10_OPPORTUNITIES_CHART_DESC' => 'Muestra el top ten de Oportunidades en un gráfico de burbujas.',
    'LBL_TOP10_OPPORTUNITIES_MY_OPP' => 'Mis Oportunidades',
    'LBL_TOP10_OPPORTUNITIES_MY_TEAMS_OPP' => "Mis Oportunidades de Equipo",

    'LBL_PIPELINE_ERR_CLOSED_SALES_STAGE' => 'Imposible cambiar el {{fieldName}} ya que este {{moduleSingular}} no tiene partidas abiertas.',
    'TPL_ACTIVITY_TIMELINE_DASHLET' => 'Línea de Tiempo de la Oportunidad',

    'LBL_CASCADE_SERVICE_WARNING' => ' no se puede establecer entre ninguna de estas Partidas Individuales, ya que no son servicios. ¿Desea continuar con la creación?',
    'LBL_CASCADE_DURATION_WARNING' => ' no se puede establecer entre ninguna de estas Partidas Individuales, ya que sus duraciones están bloqueadas. ¿Desea continuar con la creación?',

    // AI Predict
    'LBL_AI_OPPORTUNITY_CLOSE_PREDICTION_NAME' => 'Predicción de Cierre de Oportunidad',
    'LBL_AI_OPPORTUNITY_CLOSE_PREDICTION_DESC' => 'Ver detalles de predicción para una Oportunidad específica',
    'LBL_AI_WINRATE' => 'Tasa de ganancias',
    'LBL_AI_WONOPP' => 'Oportunidades ganadas',
    'LBL_AI_CLOSINGTIME' => 'Horario de cierre',
    'LBL_AI_CLOSEDOPP' => 'Oportunidades cerradas',
    'LBL_AI_LEADTIMESPAN' => 'Tiempo transcurrido entre la creación de la oportunidad y la oportunidad ganada',
];
