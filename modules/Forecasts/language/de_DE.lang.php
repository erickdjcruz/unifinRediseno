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
    'LBL_FORECASTS_DASHBOARD' => 'Prognosen-Dashboard',
    'LBL_FORECASTS_RECORD_DASHBOARD' => 'Prognosenbericht-Dashboard',

    //module strings.
    'LBL_MODULE_NAME' => 'Prognosen',
    'LBL_MODULE_NAME_SINGULAR' => 'Prognose',
    'LNK_NEW_OPPORTUNITY' => 'Neue Verkaufschance',
    'LBL_MODULE_TITLE' => 'Umsatzplanung',
    'LBL_LIST_FORM_TITLE' => 'Bestätigte Prognosen',
    'LNK_UPD_FORECAST' => 'Prognose-Arbeitsblatt',
    'LNK_QUOTA' => 'Sollvorgaben anzeigen',
    'LNK_FORECAST_LIST' => 'Prognose-Verlauf anzeigen',
    'LBL_FORECAST_HISTORY' => 'Prognose: Verlauf',
    'LBL_FORECAST_HISTORY_TITLE' => 'Umsatzplanung: Verlauf',
    'LBL_TAB_COMMITMENT' => 'Engagement',
    'LBL_CURRENT' => 'Aktuell',

    //var defs
    'LBL_TIMEPERIOD_NAME' => 'Zeitraum',
    'LBL_USER_NAME' => 'Benutzername',
    'LBL_REPORTS_TO_USER_NAME' => 'Berichtet an',

    //forecast table
    'LBL_FORECAST_ID' => 'Prognose: ID',
    'LBL_FORECAST_TIME_ID' => 'Zeitspannen-ID',
    'LBL_FORECAST_TYPE' => 'Prognose-Typ',
    'LBL_FORECAST_OPP_COUNT' => 'Verkaufschancen-Anzahl',
    'LBL_FORECAST_PIPELINE_OPP_COUNT' => 'Verkaufschancen in Pipeline',
    'LBL_FORECAST_OPP_WEIGH' => 'Gewichteter Betrag',
    'LBL_FORECAST_OPP_COMMIT' => 'Wahrscheinlicher Fall',
    'LBL_FORECAST_OPP_BEST_CASE' => 'Bester Fall',
    'LBL_FORECAST_OPP_WORST' => 'Schlechtester Fall',
    'LBL_FORECAST_USER' => 'Benutzer',
    'LBL_DATE_COMMITTED' => 'Bestätigungs-Datum',
    'LBL_DATE_ENTERED' => 'Erstellungsdatum',
    'LBL_DATE_MODIFIED' => 'Änderungsdatum',
    'LBL_CREATED_BY' => 'Erstellt von',
    'LBL_DELETED' => 'Gelöscht',
    'LBL_MODIFIED_USER_ID' => 'Geändert von',
    'LBL_WK_VERSION' => 'Version',
    'LBL_WK_REVISION' => 'Revision',

    // API message labels
    'LBL_METRICS_API_NO_ACCESS' => 'Der Zugriff auf die Metriken-API erfordert Sugar Sell Premier',

    //Quick Commit labels.
    'LBL_QC_TIME_PERIOD' => 'Zeitraum:',
    'LBL_QC_OPPORTUNITY_COUNT' => 'Verkaufschancen-Zähler:',
    'LBL_QC_WEIGHT_VALUE' => 'Gewichteter Betrag:',
    'LBL_QC_COMMIT_VALUE' => 'Bestätigter Betrag:',
    'LBL_QC_COMMIT_BUTTON' => 'Bestätigen',
    'LBL_QC_WORKSHEET_BUTTON' => 'Arbeitsblatt',
    'LBL_QC_ROLL_COMMIT_VALUE' => 'Rollup bestätigter Betrag:',
    'LBL_QC_DIRECT_FORECAST' => 'Meine direkte Prognose:',
    'LBL_QC_ROLLUP_FORECAST' => 'Meine Gruppen-Prognose:',
    'LBL_QC_UPCOMING_FORECASTS' => 'Meine Prognosen',
    'LBL_QC_LAST_DATE_COMMITTED' => 'Letztes Bestätigungsdatum:',
    'LBL_QC_LAST_COMMIT_VALUE' => 'Letzter festgelegter Betrag:',
    'LBL_QC_HEADER_DELIM' => 'An',

    //opportunity worksheet list view labels
    'LBL_OW_OPPORTUNITIES' => 'Verkaufschance',
    'LBL_OW_ACCOUNTNAME' => 'Firma',
    'LBL_OW_REVENUE' => 'Betrag',
    'LBL_OW_WEIGHTED' => 'Gewichteter Betrag',
    'LBL_OW_MODULE_TITLE' => 'Verkaufschancen-Arbeitsblatt',
    'LBL_OW_PROBABILITY' => 'Wahrscheinlichkeit',
    'LBL_OW_NEXT_STEP' => 'Nächster Schritt',
    'LBL_OW_DESCRIPTION' => 'Beschreibung',
    'LBL_OW_TYPE' => 'Typ',

    // Forecast manager worksheet (rollup)
    'LBL_RU_TEAM_FORECAST_HEADER' => 'Teamprognose für <span class="sicon sicon-user-group mx-1"></span> {{name}}',

    //forecast worksheet direct reports forecast
    'LBL_FDR_USER_NAME' => 'Direktbericht',
    'LBL_FDR_OPPORTUNITIES' => 'Verkaufschancen in Prognose:',
    'LBL_FDR_WEIGH' => 'Gewichteter Betrag der Verkaufschancen:',
    'LBL_FDR_COMMIT' => 'Bestätigter Betrag',
    'LBL_FDR_DATE_COMMIT' => 'Bestätigungsdatum',
    'LBL_FDR_FORECAST_HEADER' => 'Verkäuferprognose für <span class="sicon sicon-user mx-1"></span> {{name}}',

    //detail view.
    'LBL_DV_HEADER' => 'Prognose: Arbeitsblatt',
    'LBL_DV_MY_FORECASTS' => 'Meine Prognosen',
    'LBL_DV_MY_TEAM' => "Meine Team-Prognose",
    'LBL_DV_TIMEPERIODS' => 'Zeitspannen:',
    'LBL_DV_FORECAST_PERIOD' => 'Prognose-Zeitspanne',
    'LBL_DV_FORECAST_OPPORTUNITY' => 'Prognose-Verkaufschancen',
    'LBL_SEARCH' => 'Auswählen',
    'LBL_SEARCH_LABEL' => 'Auswählen',
    'LBL_COMMIT_HEADER' => 'Prognose-Bestätigung',
    'LBL_DV_LAST_COMMIT_DATE' => 'Letztes Bestätigungsdatum:',
    'LBL_DV_LAST_COMMIT_AMOUNT' => 'Letzte bestätigte Beträge:',
    'LBL_DV_FORECAST_ROLLUP' => 'Prognose-Rollup',
    'LBL_DV_TIMEPERIOD' => 'Zeitraum:',
    'LBL_DV_TIMPERIOD_DATES' => 'Datumsbereich:',
    'LBL_LOADING_COMMIT_HISTORY' => 'Bestätigungsverlauf wird geladen...',

    //list view
    'LBL_LV_TIMPERIOD' => 'Zeitraum',
    'LBL_LV_TIMPERIOD_START_DATE' => 'Startdatum',
    'LBL_LV_TIMPERIOD_END_DATE' => 'Enddatum',
    'LBL_LV_TYPE' => 'Prognose-Typ',
    'LBL_LV_COMMIT_DATE' => 'Bestätigungs-Datum',
    'LBL_LV_OPPORTUNITIES' => 'Verkaufschancen',
    'LBL_LV_WEIGH' => 'Gewichteter Betrag',
    'LBL_LV_COMMIT' => 'Bestätigter Betrag',

    'LBL_COMMIT_NOTE' => 'Geben Sie Beträge ein, die Sie für die gewählte Zeitspanne bestätigen möchten:',
    'LBL_COMMIT_TOOLTIP' => 'Um die Bestätigung zu aktivieren, ändern Sie einen Wert im Arbeitsblatt',
    'LBL_COMMIT_MESSAGE' => 'Möchten Sie diese Beträge bestätigen?',
    'ERR_FORECAST_AMOUNT' => 'Der bestätigte Betrag wird benötigt und muss eine Zahl sein.',

    //list view metrics
    'LBL_INCLUDED_PIPELINE' => 'Enthaltene Pipeline',
    'LBL_UPSIDE_PIPELINE' => 'Aufwärtspipeline',
    'LBL_EXCLUDED_PIPELINE' => 'Ausgeschlossene Pipeline',
    'LBL_WON' => 'Gewonnen',
    'LBL_LOST' => 'Verloren',
    'LBL_ALL' => 'Alle',

    'LBL_METRIC_LABEL_QUOTA' => 'Vorgabe',
    'LBL_METRIC_LABEL_COMMITMENT' => 'Engagement',
    'LBL_METRIC_LABEL_QUOTA_COVERAGE' => 'Vorgabenerfüllung',
    'LBL_METRIC_LABEL_GAP_QUOTA' => 'Abstand zur Vorgabe',
    'LBL_METRIC_LABEL_PCT_WON_QUOTA' => '% Gewinn der Vorgabe',
    'LBL_METRIC_LABEL_QUOTA_GAP_COVERAGE' => 'Vorgabenlückenerfüllung',
    'LBL_METRIC_LABEL_COMMITMENT_COVERAGE' => 'Engagementsabdeckung',
    'LBL_METRIC_LABEL_GAP_COMMITMENT' => 'Lücke zum Engagement',
    'LBL_METRIC_LABEL_COMMITMENT_GAP_COVERAGE' => 'Engagementslückenabdeckung',
    'LBL_METRIC_LABEL_PCT_WON_COMMITMENT' => '% Gewinn des Engagements',
    'LBL_METRIC_LABEL_FORECAST_COVERAGE' => 'Prognose-Abdeckung',
    'LBL_METRIC_LABEL_GAP_FORECAST' => 'Lücke zur Prognose',
    'LBL_METRIC_LABEL_FORECAST_GAP_COVERAGE' => 'Prognosenlücken-Abdeckung',
    'LBL_METRIC_LABEL_PCT_WON_FORECAST' => '% Gewinn der Prognose',

    // Guide to Filters
    'LBL_FILTER_GUIDE_TITLE' => 'Leitfaden zu Filtern',
    'LBL_HELP_MAIN_BODY' => 'Alle Metriken enthalten nur Chancen, die zu dem/den ausgewählten Benutzer(n) gehören, sowie den Zeitraum und alle zusätzlichen Filter, die Sie angewendet haben',
    'LBL_FORECAST_HELP' => 'Chancen, die bereits gewonnen wurden oder in einer enthaltenen {{{forecastStage}}} sind',
    'LBL_INCLUDED_PIPELINE_HELP' => 'Offene Chancen mit einem {{forecastStage}} von {{commitStageValue}}',
    'LBL_INCLUDED_PIPELINE_HELP_CUSTOM_RANGE' => 'Öffne Chancen in einer enthaltenen {{{forecastStage}}}',
    'LBL_UPSIDE_PIPELINE_HELP' => 'Öffne Chancen mit einer {{{forecastStage}}} von {{{commitStageValue}}}',
    'LBL_EXCLUDED_PIPELINE_HELP' => 'Öffne Chancen in einer {{{forecastStage}}} von {{{commitStageValue}}}',
    'LBL_EXCLUDED_PIPELINE_HELP_CUSTOM_RANGE' => 'Öffne Chancen in einer ausgeschlossenen {{{forecastStage}}}',
    'LBL_WON_HELP' => 'Changen, die bereits gewonnen wurden',
    'LBL_LOST_HELP' => 'Chancen, die bereits verloren wurden',
    'LBL_ALL_HELP' => 'Alle Chancen',

    'LBL_METRIC_HELP_QUOTA' => 'Vorgabe',
    'LBL_METRIC_HELP_COMMITMENT' => 'Wahrscheinliches Engagement',
    'LBL_METRIC_HELP_QUOTA_COVERAGE' => '(Pipeline + Gewinn + Verlust)/Vorgabe',
    'LBL_METRIC_HELP_GAP_QUOTA' => 'Vorgabe – Gewinn',
    'LBL_METRIC_HELP_PCT_WON_QUOTA' => 'Gewinn/Vorgabe',
    'LBL_METRIC_HELP_QUOTA_GAP_COVERAGE' => 'Pipeline / Lücke zur Vorgabe',
    'LBL_METRIC_HELP_COMMITMENT_COVERAGE' => '(Pipeline + Gewinn + Verlust)/Engagement',
    'LBL_METRIC_HELP_GAP_COMMITMENT' => 'Wahrscheinliches Engagement - Gewinn',
    'LBL_METRIC_HELP_COMMITMENT_GAP_COVERAGE' => 'Pipeline / Lücke zum wahrscheinlichen Engagement',
    'LBL_METRIC_HELP_PCT_WON_COMMITMENT' => 'Gewinn/Wahrscheinliches Engagement',
    'LBL_METRIC_HELP_FORECAST_COVERAGE' => '(Pipeline + Gewinn + Verlust)/Prognose',
    'LBL_METRIC_HELP_GAP_FORECAST' => 'Prognose – Gewinn (inklusive Pipeline)',
    'LBL_METRIC_HELP_FORECAST_GAP_COVERAGE' => 'Pipeline / Lücke zur Prognose',
    'LBL_METRIC_HELP_PCT_WON_FORECAST' => 'Gewinn/Prognose',

    // js error strings
    'LBL_FC_START_DATE' => 'Startdatum',
    'LBL_FC_USER' => 'Zeitplan für',

    'LBL_NO_ACTIVE_TIMEPERIOD' => 'Keine aktiven Zeiträume für Prognose',
    'LBL_FDR_ADJ_AMOUNT' => 'Angepasster Betrag',
    'LBL_SAVE_WOKSHEET' => 'Arbeitsblatt speichern',
    'LBL_RESET_WOKSHEET' => 'Arbeitsblatt zurücksetzen',
    'LBL_SHOW_CHART' => 'Diagramm anzeigen',
    'LBL_RESET_CHECK' => 'Alle Arbeitsblattdaten für den gewählten Zeitraum und den angemeldeten Benutzer werden entfernt. Fortsetzen?',

    'LB_FS_LIKELY_CASE' => 'Wahrscheinlicher Fall',
    'LB_FS_WORST_CASE' => 'Schlechtester Fall',
    'LB_FS_BEST_CASE' => 'Bester Fall',
    'LBL_FDR_WK_LIKELY_CASE' => 'Gesch. wahrsch. Fall',
    'LBL_FDR_WK_BEST_CASE' => 'Gesch. bester Fall',
    'LBL_FDR_WK_WORST_CASE' => 'Gesch. schlechtester Fall',
    'LBL_FDR_C_BEST_CASE' => 'Bester Fall',
    'LBL_FDR_C_WORST_CASE' => 'Schlechtester Fall',
    'LBL_FDR_C_LIKELY_CASE' => 'Wahrscheinlicher Fall',
    'LBL_QC_LAST_BEST_CASE' => 'Letzter bestätigter Betrag (Bester Fall):',
    'LBL_QC_LAST_LIKELY_CASE' => 'Letzter bestätigter Betrag (Wahrscheinlicher Fall):',
    'LBL_QC_LAST_WORST_CASE' => 'Letzter bestätigter Betrag (Schlechtester Fall):',
    'LBL_QC_ROLL_BEST_VALUE' => 'Rollup bestätigter Betrag (Bester Fall):',
    'LBL_QC_ROLL_LIKELY_VALUE' => 'Rollup festgelegter Betrag (Wahrscheinlicher Fall):',
    'LBL_QC_ROLL_WORST_VALUE' => 'Rollup bestätigter Betrag (Schechtester Fall):',
    'LBL_QC_COMMIT_BEST_CASE' => 'Bestätigter Betrag (Bester Fall):',
    'LBL_QC_COMMIT_LIKELY_CASE' => 'Bestätigter Betrag (Wahrscheinlicher Fall):',
    'LBL_QC_COMMIT_WORST_CASE' => 'Bestätigter Betrag (Schlechtester Fall):',
    'LBL_CURRENCY' => 'Währung',
    'LBL_CURRENCY_ID' => 'Währungs-ID',
    'LBL_CURRENCY_RATE' => 'Wechselkurs',
    'LBL_BASE_RATE' => 'Grundtrarif',

    'LBL_QUOTA' => 'Vorgabe',
    'LBL_QUOTA_ADJUSTED' => 'Angepasste Quote',

    'LBL_FORECAST_FOR' => 'Prognose-Arbeitsblatt für: ',
    'LBL_FMT_ROLLUP_FORECAST' => '(Rollup)',
    'LBL_FMT_DIRECT_FORECAST' => '(Direkt)',

    //labels used by the chart.
    'LBL_GRAPH_TITLE' => 'Prognose-Verlauf',
    'LBL_GRAPH_QUOTA_ALTTEXT' => 'Vorgabe für %s',
    'LBL_GRAPH_COMMIT_ALTTEXT' => 'Bestätigter Betrag für %s',
    'LBL_GRAPH_OPPS_ALTTEXT' => 'Wert der Verkaufschancen abgeschlossen in %s',

    'LBL_GRAPH_QUOTA_LEGEND' => 'Vorgabe',
    'LBL_GRAPH_COMMIT_LEGEND' => 'Bestätigte Prognose',
    'LBL_GRAPH_OPPS_LEGEND' => 'Abgeschlossene Verkaufschancen',
    'LBL_TP_QUOTA' => 'Vorgabe:',
    'LBL_CHART_FOOTER' => 'Prognose-Verlauf <br>Vorgabe vs prognostizierter Betrag vs Wert abgeschlossener Verkaufschancen',
    'LBL_TOTAL_VALUE' => 'Summen:',
    'LBL_COPY_AMOUNT' => 'Gesamtsumme',
    'LBL_COPY_WEIGH_AMOUNT' => 'Gewichtete Summe',
    'LBL_WORKSHEET_AMOUNT' => 'Gesamtsumme geschätzt',
    'LBL_COPY' => 'Werte kopieren',
    'LBL_COMMIT_AMOUNT' => 'Summe bestätigter Werte.',
    'LBL_CUMULATIVE_TOTAL' => 'Kumulativ gesamt',
    'LBL_COPY_FROM' => 'Wert kopieren von:',

    'LBL_CHART_TITLE' => 'Vorgabe vs. Bestätigt vs. Tatsächlich',

    'LBL_FORECAST' => 'Prognose',
    'LBL_COMMITMENT' => 'Engagement',
    'LBL_TEAM_COMMITMENT' => 'Team-Engagement',
    'LBL_FORECASTED' => 'Vorhergesagt',
    'LBL_OPPORTUNITY_FORECAST' => 'Möglichkeitenprognose',
    'LBL_REVENUELINEITEM_FORECAST' => 'Umsatzposten-Prognose',
    'LBL_LAST_COMMITMENT' => 'Letzter Einsatz',
    'LBL_ADJUSTED_TOTAL' => 'Angepasste Summe',
    'LBL_COMMIT_STAGE' => 'Bestätigungsstatus',
    'LBL_SALES_STAGE' => 'Phase',
    'LBL_AMOUNT' => 'Betrag',
    'LBL_PERCENT' => 'Prozent',
    'LBL_DATE_CLOSED' => 'Abschluss geplant',
    'LBL_PRODUCT_ID' => 'Produkt-ID',
    'LBL_QUOTA_ID' => 'Anteils-ID',
    'LBL_VERSION' => 'Version',
    'LBL_CHART_BAR_LEGEND_CLOSE' => 'Leistenlegende ausblenden',
    'LBL_CHART_BAR_LEGEND_OPEN' => 'Leistenlegende anzeigen',
    'LBL_CHART_LINE_LEGEND_CLOSE' => 'Zeilenlegende ausblenden',
    'LBL_CHART_LINE_LEGEND_OPEN' => 'Zeilenlegende anzeigen',

    //Labels for forecasting history log and endpoint
    'LBL_ERROR_NOT_MANAGER' => 'Fehler: Benutzer {0} hat keinen Managerzugriff auf die angeforderten Prognosen für {1}',
    'LBL_UP' => 'nach oben',
    'LBL_DOWN' => 'nach unten',
    'LBL_PREVIOUS_COMMIT' => 'Lezte Bestätigung:',

    'LBL_COMMITTED_HISTORY_SETUP_FORECAST' => 'Prognose konfigurieren',
    'LBL_COMMITTED_HISTORY_UPDATED_FORECAST' => 'Aktualisierte Prognose',
    'LBL_COMMITTED_HISTORY_1_SHOWN' => '{{{intro}}} {{{first}}}',
    'LBL_COMMITTED_HISTORY_2_SHOWN' => '{{{intro}}} {{{first}}}, {{{second}}}',
    'LBL_COMMITTED_HISTORY_3_SHOWN' => '{{{intro}}} {{{first}}}, {{{second}}}, and {{{third}}}',
    'LBL_COMMITTED_HISTORY_LIKELY_CHANGED' => 'Festgelegt (Wahrscheinlich) {{{direction}}} {{{from}}} nach {{{to}}}',
    'LBL_COMMITTED_HISTORY_BEST_CHANGED' => 'Festgelegt (Beste) {{{direction}}} {{{from}}} nach {{{to}}}',
    'LBL_COMMITTED_HISTORY_WORST_CHANGED' => 'Festgelegt (Schlechteste) {{{direction}}} {{{from}}} nach {{{to}}}',
    'LBL_COMMITTED_HISTORY_LIKELY_SAME' => 'Festgelegt (Wahrscheinlich) ist gleich geblieben',
    'LBL_COMMITTED_HISTORY_BEST_SAME' => 'Festgelegt (Beste) ist gleich geblieben',
    'LBL_COMMITTED_HISTORY_WORST_SAME' => 'Festgelegt (Schlechteste) ist gleich geblieben',


    'LBL_COMMITTED_THIS_MONTH' => 'Dieser Monat auf {0}',
    'LBL_COMMITTED_MONTHS_AGO' => 'Vor {0} Monaten auf {1}',

    //Labels for jsTree implementation
    'LBL_TREE_PARENT' => 'Vorläufer',

    // Label for Current User Rep Worksheet Line
    // &#x200E; tells the browser to interpret as left-to-right
    'LBL_MY_MANAGER_LINE' => '{0} (ich)&#x200E;',

    //Labels for worksheet items
    'LBL_EXPECTED_OPPORTUNITIES' => 'Erwartete Verkaufschancen',
    'LBL_DISPLAYED_TOTAL' => 'Gesamt',
    'LBL_TOTAL' => 'Gesamt',
    'LBL_OVERALL_TOTAL' => 'Insgesamt',
    'LBL_EDITABLE_INVALID' => 'Ungültiger Wert für {{field_name}}',
    'LBL_EDITABLE_INVALID_RANGE' => 'Wert muss zwischen {0} und {1} sein',
    'LBL_WORKSHEET_SAVE_CONFIRM_UNLOAD' => 'Sie haben ungesicherte Änderungen in Ihrem Arbeitsblatt.',
    'LBL_WORKSHEET_EXPORT_CONFIRM' => 'Bitte beachten Sie, dass nur gespeicherte oder bestätigte Daten exportiert werden können. Klicken Sie auf OK, um mit den Datenexport fortfahren oder "Abbrechen", um zum Arbeitsblatt zurückzukehren.',
    'LBL_WORKSHEET_ID' => 'Arbeitsblatt-ID',

    // Labels for Chart Options
    'LBL_DATA_SET' => 'Datensatz:',
    'LBL_GROUP_BY' => 'Gruppieren nach:',
    'LBL_CHART_OPTIONS' => 'Diagrammoptionen',
    'LBL_CHART_AMOUNT' => 'Betrag',
    'LBL_CHART_TYPE' => 'Typ',

    // Labels for Data Filters
    'LBL_FILTERS' => 'Filter',

    // Labels for toggle buttons
    'LBL_MORE' => 'Mehr',
    'LBL_LESS' => 'Weniger',

    // Labels for Progress
    'LBL_PROJECTED' => 'Hochgerechnet',
    'LBL_DISTANCE_ABOVE_LIKELY_FROM_QUOTA' => 'Wahrscheinlich über Soll',
    'LBL_DISTANCE_LEFT_LIKELY_TO_QUOTA' => 'Wahrscheinlich unter Soll',
    'LBL_DISTANCE_ABOVE_BEST_FROM_QUOTA' => 'Bestes über Soll',
    'LBL_DISTANCE_LEFT_BEST_TO_QUOTA' => 'Bestes unter Soll',
    'LBL_DISTANCE_ABOVE_WORST_FROM_QUOTA' => 'Schlechtestes über Soll',
    'LBL_DISTANCE_LEFT_WORST_TO_QUOTA' => 'Schlechtestes unter Soll',
    'LBL_CLOSED' => 'Abgeschlossen',
    'LBL_DISTANCE_ABOVE_LIKELY_FROM_CLOSED' => 'Wahrscheinlich über Abgeschlossen',
    'LBL_DISTANCE_LEFT_LIKELY_TO_CLOSED' => 'Wahrscheinlich unter Abgeschlossen',
    'LBL_DISTANCE_ABOVE_BEST_FROM_CLOSED' => 'Bestes über Abgeschlossen',
    'LBL_DISTANCE_LEFT_BEST_TO_CLOSED' => 'Bestes unter Abgeschlossen',
    'LBL_DISTANCE_ABOVE_WORST_FROM_CLOSED' => 'Schlechtestes über Abgeschlossen',
    'LBL_DISTANCE_LEFT_WORST_TO_CLOSED' => 'Schlechtestes unter Abgeschlossen',
    'LBL_REVENUE' => 'Umsatz',
    'LBL_PIPELINE_REVENUE' => 'Pipeline Erträge',
    'LBL_PIPELINE_OPPORTUNITIES' => 'Pipeline Verkaufschancen',
    'LBL_LOADING' => 'Laden. Bitte warten...',
    'LBL_IN_FORECAST' => 'In Prognose',

    // Actions Dropdown
    'LBL_ACTIONS' => 'Aktionen',
    'LBL_EXPORT_CSV' => 'Exportieren CSV',
    'LBL_CANCEL' => 'Abbrechen',

    'LBL_CHART_FORECAST_FOR' => 'Prognose für {0}',
    'LBL_FORECAST_TITLE' => 'Prognose: {0}',
    'LBL_CHART_INCLUDED' => 'Inkludiert',
    'LBL_CHART_NOT_INCLUDED' => 'nicht inkludiert',
    'LBL_CHART_ADJUSTED' => '(Angepasst)',
    'LBL_SAVE_DRAFT' => 'Speichern',
    'LBL_CHANGES_BY' => 'Änderungen von {0}',
    'LBL_FORECAST_SETTINGS' => 'Prognose-Einstellungen',

    // config panels strings
    'LBL_FORECASTS_CONFIG_TITLE' => 'Prognose-Einrichtung',

    'LBL_FORECASTS_MISSING_STAGE_TITLE' => 'Fehler bei Prognose-Konfiguration:',
    'LBL_FORECASTS_MISSING_SALES_STAGE_VALUES' => 'Das Prognosemodul wurde fehlerhaft konfiguriert und ist nicht mehr verfügbar. Verkaufsphase Gewinn und Verkaufsphase Verlust müssen mindestens einen Wert enthalten, der in den Verkaufsphasen zu finden ist. Bitte kontaktieren Sie Ihren Administrator.',
    'LBL_FORECASTS_ACLS_NO_ACCESS_TITLE' => 'Fehler bei Zugriff auf Prognosen:',
    'LBL_FORECASTS_ACLS_NO_ACCESS_MSG' => 'Sie haben keinen Zugriff auf das Prognosemodul. Bitte kontaktieren Sie ihren Administrator.',

    'LBL_FORECASTS_RECORDS_ACLS_NO_ACCESS_MSG' => 'Sie können nicht auf die Datensätze des Prognosemoduls zugreifen. Bitte kontaktieren Sie Ihren Administrator.',

    // Panel and BreadCrumb Labels
    'LBL_FORECASTS_CONFIG_BREADCRUMB_WORKSHEET_LAYOUT' => 'Arbeitsblatt-Layout',
    'LBL_FORECASTS_CONFIG_BREADCRUMB_RANGES' => 'Reichweiten',
    'LBL_FORECASTS_CONFIG_BREADCRUMB_SCENARIOS' => 'Szenarien',
    'LBL_FORECASTS_CONFIG_BREADCRUMB_TIMEPERIODS' => 'Zeiträume',
    'LBL_FORECASTS_CONFIG_BREADCRUMB_VARIABLES' => 'Variablen',

    // Admin UI
    'LBL_FORECASTS_CONFIG_TITLE_FORECAST_SETTINGS' => 'Prognose-Einstellungen',
    'LBL_FORECASTS_CONFIG_TITLE_TIMEPERIODS' => 'Zeitraum',
    'LBL_FORECASTS_CONFIG_TITLE_RANGES' => 'Prognose-Bereiche',
    'LBL_FORECASTS_CONFIG_TITLE_SCENARIOS' => 'Szenarien',
    'LBL_FORECASTS_CONFIG_TITLE_WORKSHEET_COLUMNS' => 'Arbeitsblatt-Spalten',
    'LBL_FORECASTS_CONFIG_TITLE_FORECAST_BY' => 'Arbeitsblatt-Ansicht nach',

    'LBL_FORECASTS_CONFIG_HOWTO_TITLE_FORECAST_BY' => 'Prognose nach',

    'LBL_FORECASTS_CONFIG_TITLE_MESSAGE_TIMEPERIODS' => 'Startdatum für Geschäftsjahr:',

    'LBL_FORECASTS_CONFIG_HELP_TIMEPERIODS' => 'Konfigurieren Sie die Zeitspanne, die im Prognosemodul verwedet wird.<br><br>Beachten Sie, dass die Zeitspanne-Einstellungen nach der anfänglichen Einrichtung nicht mehr geändert werden können.<br><br>Für den Anfang wählen Sie das Anfangsdatum des Geschäftsjahres. Dann wählen Sie den Typ der Zeitspanne, über die Sie die Prognose erhalten möchten. Die Datumsabgrenzung wird automatisch kalkuliert, basierend auf Ihrer Auswahl. Die untergeordneten Zeitspannen sind die Basis für das Prognose-Arbeitsblatt.<br><br>Die darstellbaren zukünftigen und vergangenen Zeitspannen bestimmen die Anzahl der sichtbaren untergeordneten Zeitspannen im Prognosemodul.',
    'LBL_FORECASTS_CONFIG_HELP_RANGES' => 'Konfigurieren Sie, wie Sie {{forecastByModule}} kategorisieren möchten. <br><br>Beachten Sie bitte, dass die Bereichseinstellungen nach dem ersten Bestätigen nicht mehr geändert werden können. Bei aktualisierten Instanzen ist die Einstellung „Bereich“ mit vorhandenen Prognosedaten verknüpft.<br><br>Sie können zwei oder mehr Kategorien basierend auf Wahrscheinlichkeitsbereichen auswählen oder Kategorien erstellen, die nicht auf Wahrscheinlichkeiten basieren. <br><br>Links neben Ihren benutzerdefinierten Kategorien befinden sich Kontrollkästchen. Verwenden Sie diese, um zu entscheiden, welche Bereiche in die bestätigte Prognosemenge einbezogen und den Managern gemeldet werden. <br><br>Benutzer können den Einschluss-/Ausschlussstatus und die Kategorie von {{forecastByModule}} manuell im Arbeitsblatt ändern.',
    'LBL_FORECASTS_CONFIG_HELP_SCENARIOS' => 'Wählen Sie die Spalten, welche die Benutzer für Prognosen für jedes {{forecastByModuleSingular}} ausfüllen müssen. Beachten Sie, dass die "wahrscheinliche" Summe mit der unter {{forecastByModule}} angezeigten Summe verbunden ist; deshalb kann diese Spalte nicht ausgeblendet werden.',
    'LBL_FORECASTS_CONFIG_HELP_WORKSHEET_COLUMNS' => 'Wahlen Sie die Spalten, die Sie im Prognose-Modul anzeigen möchten. Der Benutzer kann die Sicht des Arbeitsblatts mit der Liste von Feldern konfigurieren.',
    'LBL_FORECASTS_CONFIG_HELP_FORECAST_BY' => 'Ich bin ein Platzhalter für Prognosen!',
    'LBL_FORECASTS_CONFIG_SETTINGS_SAVED' => 'Prognosen-Einstellungen wurden gespeichert.',

    // timeperiod config
    //TODO-sfa remove this once the ability to map buckets when they get changed is implemented (SFA-215).
    'LBL_FORECASTS_CONFIG_TIMEPERIOD_SETUP_NOTICE' => 'Periodeneinstellungen können nach der ersten Einrichtung nicht mehr geändert werden.',
    'LBL_FORECASTS_CONFIG_TIMEPERIOD_DESC' => 'Perioden für Prognosen konfigurieren.',
    'LBL_FORECASTS_CONFIG_TIMEPERIOD_TYPE' => 'Wahlen Sie den Geschäftsjahr-Typ, den Ihr Unternehmen für die Buchführung verwendet.',
    'LBL_FORECASTS_CONFIG_TIMEPERIOD' => 'Wahlen Sie den Zeitspannen-Typ',
    'LBL_FORECASTS_CONFIG_LEAFPERIOD' => 'Wahlen Sie die untergeordnete Zeitspanne, über die Sie ihre Zeitspanne anzeigen lassen möchten:',
    'LBL_FORECASTS_CONFIG_START_DATE' => 'Wählen Sie das Startdatum des Geschäftsjahres',
    'LBL_FORECASTS_CONFIG_TIMEPERIODS_FORWARD' => 'Wählen sie die Anzahl der zukünftigen Zeitspannen, die Sie im Arbeitsblatt anzeigen möchten.<br><i>Die Anzahl bezieht sich auf die ausgewählte Basis-Zeitspanne. Zum Beispiel werden durch Auswahl von 2 bei einer jährlichen Zeitspanne 8 zukünftige Quartale angezeigt</i>',
    'LBL_FORECASTS_CONFIG_TIMEPERIODS_BACKWARD' => 'Wählen sie die Anzahl der zukünftigen Zeitspannen, die Sie im Arbeitsblatt anzeigen möchten.<br><i>Die Anzahl bezieht sich auf die ausgewählte Basis-Zeitspanne. Zum Beispiel werden durch Auswahl von 2 bei einer vierteljährlichen Zeitspanne die 6 letzten Monate angezeigt</i>',
    'LBL_FORECASTS_CONFIG_TIMEPERIOD_FISCAL_YEAR' => 'Die gewählte Startdatum gibt an, dass sich das Geschäftsjahr über zwei Jahre erstrecken kann. Bitte wählen Sie das Jahr für das Geschäftsjahr:',
    'LBL_FISCAL_YEAR' => 'Geschäftsjahr',

    // worksheet layout config
    'LBL_FORECASTS_CONFIG_GENERAL_FORECAST_BY_TEXT' => 'Wählen Sie, wie das Prognose-Arbeitsblatt ausgefüllt werden soll:',
    'LBL_FORECASTS_CONFIG_GENERAL_FORECAST_BY_OPPORTUNITIES' => 'Verkaufschancen',
    'LBL_FORECASTS_CONFIG_GENERAL_FORECAST_BY_PRODUCT_LINE_ITEMS' => 'Umsatzposten',
    'LBL_REVENUELINEITEM_NAME' => 'Umsatzposten-Name',
    'LBL_FORECASTS_CONFIG_WORKSHEET_LAYOUT_DETAIL_MESSAGE' => 'Arbeitsblätter werden ausgefüllt mit:',

    // ranges config
    //TODO-sfa remove this once the ability to map buckets when they get changed is implemented (SFA-215).
    'LBL_FORECASTS_CONFIG_RANGES_SETUP_NOTICE' => 'Bereichseinstellungen können nach der ersten Bestätigung oder dem ersten gespeicherten Entwurf im Prognosemodul nicht mehr geändert werden. Bei aktualisierten Instanzen können diese nach der ersten Einrichtung nciht mehr geändert werden, da die Prognosedaten bereits durch das Upgrade verfügbar sind.',
    'LBL_FORECASTS_CONFIG_RANGES' => 'Prognose-Bereichsoptionen:',
    'LBL_FORECASTS_CONFIG_RANGES_OPTIONS' => 'Wählen Sie, wie Sie {{forecastByModule}} kategorisieren möchten.',
    'LBL_FORECASTS_CONFIG_SHOW_BINARY_RANGES_DESCRIPTION' => 'Diese Option gibt dem Benutzer die Möglichkeit, {{forecastByModule}} für die Inklusion bzw. Exklusion in einer Prognose auszuwählen.',
    'LBL_FORECASTS_CONFIG_SHOW_BUCKETS_RANGES_DESCRIPTION' => 'Diese Option gibt dem Benutzer die Möglichkeit, {{forecastByModule}} zu kategorisieren, die nicht in der Bestätigung enthalten sind, jedoch erfolgreich abgeschlossen werden könnten, wenn alles gut geht, sowie {{forecastByModule}}, die nicht in die Prognose eingeschlossen werden sollen.',
    'LBL_FORECASTS_CONFIG_SHOW_CUSTOM_BUCKETS_RANGES_DESCRIPTION' => 'Benutzerdefinierte Bereiche: Diese Option ermöglicht es den Benutzern, {{forecastByModule}} zu kategorisieren, die in einer Prognose bestätigt werden sollen, in einem bestätigten bzw. ausgeschlossen Bereich oder anderen vom Benutzer definierten Bereichen.',
    'LBL_FORECASTS_CONFIG_RANGES_EXCLUDE_INFO' => 'Der Standardbereich für die Ausschließung ist 0% bis zum Minimum des vorherigen Prognosebereichs.',

    'LBL_FORECASTS_CONFIG_RANGES_ENTER_RANGE' => 'Bereichsnamen eingeben...',

    // scenarios config
    //TODO-sfa refactors the code references for scenarios to be scenarios (SFA-337).
    'LBL_FORECASTS_CONFIG_WORKSHEET_SCENARIOS' => 'Wahlen Sie ein Szenario, das auf den Arbeitsblatt erscheinen soll.',
    'LBL_FORECASTS_CONFIG_WORKSHEET_LIKELY_INFO' => 'Basiert voraussichtlich auf dem Wert im Modul {{forecastByModule}}.',
    'LBL_FORECASTS_CONFIG_WORKSHEET_SCENARIOS_LIKELY' => 'Wahrscheinlich',
    'LBL_FORECASTS_CONFIG_WORKSHEET_SCENARIOS_BEST' => 'Bestens',
    'LBL_FORECASTS_CONFIG_WORKSHEET_SCENARIOS_WORST' => 'Schlechtestens',
    'LBL_FORECASTS_CONFIG_PROJECTED_SCENARIOS' => 'Die geplanten Szenarien in Summen anzeigen',
    'LBL_FORECASTS_CONFIG_PROJECTED_SCENARIOS_LIKELY' => 'Summe für wahrscheinliche Fälle anzeigen',
    'LBL_FORECASTS_CONFIG_PROJECTED_SCENARIOS_BEST' => 'Summe für beste Fälle anzeigen',
    'LBL_FORECASTS_CONFIG_PROJECTED_SCENARIOS_WORST' => 'Summe für schlechteste Fälle anzeigen',

    // variables config
    'LBL_FORECASTS_CONFIG_VARIABLES' => 'Variablen',
    'LBL_FORECASTS_CONFIG_VARIABLES_DESC' => 'Die Formeln für die Metrik-Tabelle beruhen auf der Verkaufsphase für {{forecastByModule}}, die von der Pipeline ausgeschlossen werden müssen, d. h. {{forecastByModule}}, die abgeschlossen und verloren sind.',
    'LBL_FORECASTS_CONFIG_VARIABLES_CLOSED_LOST_STAGE' => 'Bitte wählen Sie die Verkaufsphase, die abgeschlossene und verlorene {{forecastByModule}} repräsentiert:',
    'LBL_FORECASTS_CONFIG_VARIABLES_CLOSED_WON_STAGE' => 'Bitte wählen Sie die Verkaufsphase, die abgeschlossene und gewonnene {{forecastByModule}} repräsentiert:',
    'LBL_FORECASTS_CONFIG_VARIABLES_FORMULA_DESC' => 'Sie haben Ihr Prognose-Modul erfolgreich konfiguriert. Bitte warten Sie, bis das Modul geladen wird:',

    'LBL_FORECASTS_WIZARD_SUCCESS_TITLE' => 'Erfolg:',
    'LBL_FORECASTS_WIZARD_SUCCESS_MESSAGE' => 'Die Prognose-Einstellungen wurden gespeichert. Bitte warten Sie, bis das Modul geladen wird.',
    'LBL_FORECASTS_TABBED_CONFIG_SUCCESS_MESSAGE' => 'Die Einstellungen wurden gesichert. Bitte warten Sie bis das Modul ladet.',
    // Labels for Success Messages:
    'LBL_FORECASTS_WORKSHEET_SAVE_DRAFT_SUCCESS' => 'Sie haben das Prognosen-Arbeitsblatt als Entwurf für die gewählte Zeitspanne gespeichert.',
    'LBL_FORECASTS_WORKSHEET_COMMIT_SUCCESS' => 'Sie haben die Prognose bestätigt',
    'LBL_FORECASTS_WORKSHEET_COMMIT_SUCCESS_TO' => 'Sie haben die Umsatzplanung an {{manager}} übergeben',

    // custom ranges
    'LBL_FORECASTS_CUSTOM_RANGES_DEFAULT_NAME' => 'Benutzerdefinierter Bereich',
    'LBL_UNAUTH_FORECASTS' => 'Nicht autorisierter Zugriff auf Prognose-Einstellungen.',
    'LBL_FORECASTS_RANGES_BASED_TITLE' => 'Bereiche basierend auf Erfolgschancen',
    'LBL_FORECASTS_CUSTOM_BASED_TITLE' => 'Benutzerdefinierte Bereiche basierend auf Erfolgschancen',
    'LBL_FORECASTS_CUSTOM_NO_BASED_TITLE' => 'Bereiche nicht basierend auf Erfolgschancen',

    // worksheet columns config
    'LBL_DISCOUNT' => 'Rabatt',
    'LBL_OPPORTUNITY_STATUS' => 'Verkaufschancen Status',
    'LBL_OPPORTUNITY_NAME' => 'Verkaufschance-Name',
    'LBL_PRODUCT_TEMPLATE' => 'Produktkatalog',
    'LBL_CAMPAIGN' => 'Kampagne',
    'LBL_TEAMS' => 'Teams',
    'LBL_CATEGORY' => 'Kategorie',
    'LBL_COST_PRICE' => 'Herstellkosten',
    'LBL_TOTAL_DISCOUNT_AMOUNT' => 'Gesamter Rabatt',
    'LBL_FORECASTS_CONFIG_WORKSHEET_TEXT' => 'Wahlen Sie die Spalten, die im Arbeitsblatt angezeigt werden sollen. Die folgenden Felder sind standardmäßig ausgewählt:',

    // forecast details dashlet
    'LBL_DASHLET_FORECAST_NOT_SETUP' => 'Die Prognosen wurden nicht konfiguriert und müssen konfiguriert werden, um dieses Widget zu verwenden. Bitte kontaktieren Sie ihren Administrator.',
    'LBL_DASHLET_FORECAST_NOT_SETUP_ADMIN' => 'Die Prognosen wurden nicht konfiguriert und müssen konfiguriert werden, um dieses Widget zu verwenden.',
    'LBL_DASHLET_FORECAST_CONFIG_LINK_TEXT' => 'Klicken Sie hier, um das Prognose-Modul zu konfigurieren.',
    'LBL_DASHLET_MY_PIPELINE' => 'Meine Pipeline',
    'LBL_DASHLET_MY_TEAMS_PIPELINE' => "Meine Team-Pipeline",
    'LBL_DASHLET_PIPELINE_CHART_NAME' => 'Prognose-Pipeline-Diagramm',
    'LBL_DASHLET_PIPELINE_CHART_DESC' => 'Zeigt das aktuelle Pipeline-Diagramm an.',
    'LBL_FORECAST_DETAILS_DEFICIT' => 'Defizit',
    'LBL_FORECAST_DETAILS_SURPLUS' => 'Mehrbetrag',
    'LBL_FORECAST_DETAILS_SHORT' => 'Kurz bei',
    'LBL_FORECAST_DETAILS_EXCEED' => 'Überschreitung von',
    'LBL_FORECAST_DETAILS_NO_DATA' => 'Keine Daten',
    'LBL_FORECAST_DETAILS_MEETING_QUOTA' => 'Sollvorgabe eingehalten',
    'LBL_OPEN_LIKELY' => 'Wahrscheinliche öffnen',
    'LBL_OPEN_BEST' => 'Beste öffnen',
    'LBL_OPEN_WORST' => 'Schlechteste öffnen',

    // Pipeline Metrics dashlet
    'LBL_PIPELINE_METRICS_DASHLET_NAME' => 'Pipeline-Metriken',
    'LBL_PIPELINE_METRICS_DASHLET_DESC' => 'Details zu Pipeline-Metriken anzeigen',
    'LBL_PIPELINE_METRICS_DASHLET_METRICS_GUIDE' => 'Leitfaden zu Pipeline-Metriken',
    'LBL_PIPELINE_METRICS_DASHLET_CONFIG_METRICS' => 'Metriken anzeigen',
    'LBL_PIPELINE_METRICS_DASHLET_CONFIG_METRICS_GUIDE' => 'Leitfaden zu Metriken',
    'LBL_PIPELINE_METRICS_DASHLET_CONFIG_METRICS_REQUIRED' => 'Es muss mindestens eine Metrik ausgewählt werden, die auf Metrikregisterkarten angezeigt werden soll',

    'LBL_ASSIGN_QUOTA_BUTTON' => 'Sollvorgabe zuweisen',
    'LBL_ASSIGNING_QUOTA' => 'Sollvorgabe wird zugewisen',
    'LBL_QUOTA_ASSIGNED' => 'Sollvorgaben wurden erfolgreich zugewiesen',
    'LBL_FORECASTS_NO_ACCESS_TO_CFG_TITLE' => 'Fehler beim Zugriff auf Prognose',
    'LBL_FORECASTS_NO_ACCESS_TO_CFG_MSG' => 'Sie haben keinen Zugriff auf die Prognose. Bitte kontaktieren Sie Ihren Administrator.',
    'WARNING_DELETED_RECORD_RECOMMIT_1' => 'Dieser Datensatz war Teil eines ',
    'WARNING_DELETED_RECORD_RECOMMIT_2' => 'Es wird entfernt werden und Sie müssen Ihr ',

    'LBL_DASHLET_MY_FORECAST' => 'Meine Prognose',
    'LBL_DASHLET_MY_TEAMS_FORECAST' => "Meine Team-Prognose",

    'LBL_WARN_UNSAVED_CHANGES_CONFIRM_SORT' => 'Sie haben nicht gespeicherte Änderungen. Möchten Sie, dass das Arbeitsblatt sortiert und die Änderungen verworfen werden?',

    'LBL_SAVE_TOOLTIP' => 'Änderungen in Arbeitsblattzeilen speichern',
    'LBL_COMMIT_TOOLTIP_REP' => 'Änderungen an Datensätzen speichern und Vorhersage an den Manager übermitteln',
    'LBL_COMMIT_TOOLTIP_MGR' => 'Vorhersage an Manager und Quote zuweisen',

    'LBL_SAVE_LABEL_REP' => 'Speichern',
    'LBL_SAVE_LABEL_MGR' => 'Angepasste Werte speichern',

    // Forecasts Records View Help Text
    'LBL_HELP_RECORDS' => 'Das Modul {{plural_module_name}} enthält Datensätze {{forecastby_singular_module}} für den Aufbau von {{forecastworksheets_module}} und die Prognose der Verkaufszahlen. Benutzer können auf Verkaufszahlen {{quotas_module}} auf individueller Ebene, mit einem Team oder auf der Ebene der Verkaufsorganisation hinarbeiten. Bevor Benutzer auf das Modul {{plural_module_name}} zugreifen können, muss ein Administrator die von der Organisation gewünschten Zeitspannen, Bereiche und Szenarios wählen.

Verkäufer verwenden das Modul {{plural_module_name}}, um mit dem ihnen {{forecastby_module}} zu arbeiten, während die aktuelle Zeitspanne fortschreitet. Diese Benutzer müssen die Gesamtprognosen für die persönlichen Verkaufszahlen bestätigen, basierend auf den {{forecastby_module}}, die voraussichtlich erfolgreich sein werden. Verkaufsmanager arbeiten mit eigenen Datensätzen {{forecastby_singular_module}}, ähnlich wie andere Verkäufer. Außerdem fassen sie die bestätigten Bereiche ihrer Teams zusammen, um die Gesamt-Verkaufszahlen des Teams zu prognostizieren und auf die Soll-Verkaufszahlen für die jeweiligen Zeitspannen hinzuarbeiten. Die Elemente des erweiterbaren Intelligence-Bereichs bieten eine noch detailliertere Analyse, u. a. für individuelle Arbeitsblätter der Teammitglieder und des Managers.',
];
