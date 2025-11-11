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
    'LBL_FORECASTS_DASHBOARD' => 'Informačný panel s predpoveďami',
    'LBL_FORECASTS_RECORD_DASHBOARD' => 'Informačný panel so záznamom prognóz',

    //module strings.
    'LBL_MODULE_NAME' => 'Prognózy',
    'LBL_MODULE_NAME_SINGULAR' => 'Prognóza',
    'LNK_NEW_OPPORTUNITY' => 'Vytvoriť príležitosť',
    'LBL_MODULE_TITLE' => 'Prognózy',
    'LBL_LIST_FORM_TITLE' => 'Potvrdené prognózy',
    'LNK_UPD_FORECAST' => 'Prognostická tabuľka',
    'LNK_QUOTA' => 'Zobrazenie kvót',
    'LNK_FORECAST_LIST' => 'Zobrazenie histórie prognóz',
    'LBL_FORECAST_HISTORY' => 'Prognózy: História',
    'LBL_FORECAST_HISTORY_TITLE' => 'História',
    'LBL_TAB_COMMITMENT' => 'Záväzok',
    'LBL_CURRENT' => 'Aktuálny',

    //var defs
    'LBL_TIMEPERIOD_NAME' => 'Časové obdobie',
    'LBL_USER_NAME' => 'Meno používateľa',
    'LBL_REPORTS_TO_USER_NAME' => 'Nadriadený',

    //forecast table
    'LBL_FORECAST_ID' => 'ID',
    'LBL_FORECAST_TIME_ID' => 'ID časového obdobia',
    'LBL_FORECAST_TYPE' => 'Typ prognózy',
    'LBL_FORECAST_OPP_COUNT' => 'Celkový počet príležitostí',
    'LBL_FORECAST_PIPELINE_OPP_COUNT' => 'Počet príležitostí na osi',
    'LBL_FORECAST_OPP_WEIGH' => 'Vážená čiastka',
    'LBL_FORECAST_OPP_COMMIT' => 'Pravdepodobný prípad',
    'LBL_FORECAST_OPP_BEST_CASE' => 'Najlepší prípad',
    'LBL_FORECAST_OPP_WORST' => 'Najhorší prípad',
    'LBL_FORECAST_USER' => 'Používateľ',
    'LBL_DATE_COMMITTED' => 'Dátum schválenia',
    'LBL_DATE_ENTERED' => 'Dátum vloženia',
    'LBL_DATE_MODIFIED' => 'Dátum úpravy',
    'LBL_CREATED_BY' => 'Vytvoril',
    'LBL_DELETED' => 'Vymazané',
    'LBL_MODIFIED_USER_ID' => 'Upravené používateľom',
    'LBL_WK_VERSION' => 'Verzia',
    'LBL_WK_REVISION' => 'Revízia',

    // API message labels
    'LBL_METRICS_API_NO_ACCESS' => 'Na prístup k API metrík je potrebný Sugar Sell Premier',

    //Quick Commit labels.
    'LBL_QC_TIME_PERIOD' => 'Časové obdobie:',
    'LBL_QC_OPPORTUNITY_COUNT' => 'Počet príležitostí:',
    'LBL_QC_WEIGHT_VALUE' => 'Vážená čiastka:',
    'LBL_QC_COMMIT_VALUE' => 'Potvrdená suma:',
    'LBL_QC_COMMIT_BUTTON' => 'Potvrdiť',
    'LBL_QC_WORKSHEET_BUTTON' => 'Tabuľka',
    'LBL_QC_ROLL_COMMIT_VALUE' => 'Schválená kumulatívna čiastka:',
    'LBL_QC_DIRECT_FORECAST' => 'Moje priame prognózy:',
    'LBL_QC_ROLLUP_FORECAST' => 'Moja skupina prognóz:',
    'LBL_QC_UPCOMING_FORECASTS' => 'Moje prognózy',
    'LBL_QC_LAST_DATE_COMMITTED' => 'Dátum posledného schválenia:',
    'LBL_QC_LAST_COMMIT_VALUE' => 'Posledná schválená čiastka:',
    'LBL_QC_HEADER_DELIM' => 'Komu',

    //opportunity worksheet list view labels
    'LBL_OW_OPPORTUNITIES' => 'Príležitosť',
    'LBL_OW_ACCOUNTNAME' => 'Účet',
    'LBL_OW_REVENUE' => 'Suma',
    'LBL_OW_WEIGHTED' => 'Vážená čiastka',
    'LBL_OW_MODULE_TITLE' => 'Tabuľka príležitostí',
    'LBL_OW_PROBABILITY' => 'Pravdepodobnosť',
    'LBL_OW_NEXT_STEP' => 'Ďalší krok',
    'LBL_OW_DESCRIPTION' => 'Popis',
    'LBL_OW_TYPE' => 'Typ',

    // Forecast manager worksheet (rollup)
    'LBL_RU_TEAM_FORECAST_HEADER' => 'Prognóza tímu pre <span class="sicon sicon-user-group mx-1"></span> {{name}}',

    //forecast worksheet direct reports forecast
    'LBL_FDR_USER_NAME' => 'Priame hlásenie',
    'LBL_FDR_OPPORTUNITIES' => 'Príležitosti v prognóze:',
    'LBL_FDR_WEIGH' => 'Vážená čiastka príležitostí:',
    'LBL_FDR_COMMIT' => 'Schválená čiastka',
    'LBL_FDR_DATE_COMMIT' => 'Dátum schválenia',
    'LBL_FDR_FORECAST_HEADER' => 'Prognóza predajcu pre <span class="sicon sicon-user mx-1"></span> {{name}}',

    //detail view.
    'LBL_DV_HEADER' => 'Prognózy: Tabuľka',
    'LBL_DV_MY_FORECASTS' => 'Moje prognózy',
    'LBL_DV_MY_TEAM' => "Moje tímové prognózy",
    'LBL_DV_TIMEPERIODS' => 'Časové obdobia:',
    'LBL_DV_FORECAST_PERIOD' => 'Časové obdobie prognózy',
    'LBL_DV_FORECAST_OPPORTUNITY' => 'Príležitosti prognózy',
    'LBL_SEARCH' => 'Vybrať',
    'LBL_SEARCH_LABEL' => 'Vybrať',
    'LBL_COMMIT_HEADER' => 'Potvrdenie prognózy',
    'LBL_DV_LAST_COMMIT_DATE' => 'Dátum posledného schválenia:',
    'LBL_DV_LAST_COMMIT_AMOUNT' => 'Posledné schválené čiastky:',
    'LBL_DV_FORECAST_ROLLUP' => 'Prognóza kumulatívne',
    'LBL_DV_TIMEPERIOD' => 'Časové obdobie:',
    'LBL_DV_TIMPERIOD_DATES' => 'Časový rozsah:',
    'LBL_LOADING_COMMIT_HISTORY' => 'Nahrávanie histórie schválení...',

    //list view
    'LBL_LV_TIMPERIOD' => 'Časové obdobie',
    'LBL_LV_TIMPERIOD_START_DATE' => 'Dátum začiatku',
    'LBL_LV_TIMPERIOD_END_DATE' => 'Dátum ukončenia',
    'LBL_LV_TYPE' => 'Typ prognózy',
    'LBL_LV_COMMIT_DATE' => 'Dátum schválenia',
    'LBL_LV_OPPORTUNITIES' => 'Príležitosti',
    'LBL_LV_WEIGH' => 'Vážená čiastka',
    'LBL_LV_COMMIT' => 'Schválená čiastka',

    'LBL_COMMIT_NOTE' => 'Zadajte čiastky, ktoré by ste chceli schváliť pre vybraté časové obdobie:',
    'LBL_COMMIT_TOOLTIP' => 'Ak chcete povoliť schválenie: Zmeňte hodnotu v tabuľke',
    'LBL_COMMIT_MESSAGE' => 'Chcete schváliť tieto čiastky?',
    'ERR_FORECAST_AMOUNT' => 'Vyžaduje sa schválená čiastka, ktorá musí byť vyjadrená číslom.',

    //list view metrics
    'LBL_INCLUDED_PIPELINE' => 'Zahrnutá pipeline',
    'LBL_UPSIDE_PIPELINE' => 'Pipeline hore',
    'LBL_EXCLUDED_PIPELINE' => 'Vylúčená pipeline',
    'LBL_WON' => 'Získané',
    'LBL_LOST' => 'Nezískané',
    'LBL_ALL' => 'Všetko',

    'LBL_METRIC_LABEL_QUOTA' => 'Kvóta',
    'LBL_METRIC_LABEL_COMMITMENT' => 'Záväzok',
    'LBL_METRIC_LABEL_QUOTA_COVERAGE' => 'Pokrytie kvóty',
    'LBL_METRIC_LABEL_GAP_QUOTA' => 'Rozdiel voči kvóte',
    'LBL_METRIC_LABEL_PCT_WON_QUOTA' => '% úspešnosti kvóty',
    'LBL_METRIC_LABEL_QUOTA_GAP_COVERAGE' => 'Pokrytie rozdielu voči kvóte',
    'LBL_METRIC_LABEL_COMMITMENT_COVERAGE' => 'Pokrytie záväzku',
    'LBL_METRIC_LABEL_GAP_COMMITMENT' => 'Rozdiel voči záväzku',
    'LBL_METRIC_LABEL_COMMITMENT_GAP_COVERAGE' => 'Pokrytie rozdielu voči záväzku',
    'LBL_METRIC_LABEL_PCT_WON_COMMITMENT' => '% úspešnosti záväzku',
    'LBL_METRIC_LABEL_FORECAST_COVERAGE' => 'Pokrytie prognózy',
    'LBL_METRIC_LABEL_GAP_FORECAST' => 'Rozdiel voči prognóze',
    'LBL_METRIC_LABEL_FORECAST_GAP_COVERAGE' => 'Pokrytie rozdielu voči prognóze',
    'LBL_METRIC_LABEL_PCT_WON_FORECAST' => '% úspešnosti prognózy',

    // Guide to Filters
    'LBL_FILTER_GUIDE_TITLE' => 'Sprievodca filtrami',
    'LBL_HELP_MAIN_BODY' => 'Všetky metriky zahŕňajú len príležitosti patriace k vybratým používateľom, časovému obdobiu a akýmkoľvek ďalším filtrom, ktoré ste použili',
    'LBL_FORECAST_HELP' => 'Príležitosti, ktoré už boli získané alebo sú v zahrnutej {{{forecastStage}}}',
    'LBL_INCLUDED_PIPELINE_HELP' => 'Otvorené príležitosti s {{{forecastStage}}} z {{{commitStageValue}}}',
    'LBL_INCLUDED_PIPELINE_HELP_CUSTOM_RANGE' => 'Otvorené príležitosti v zahrnutej {{{forecastStage}}}',
    'LBL_UPSIDE_PIPELINE_HELP' => 'Otvorené príležitosti s {{{forecastStage}}} z {{{commitStageValue}}}',
    'LBL_EXCLUDED_PIPELINE_HELP' => 'Otvorené príležitosti s {{{forecastStage}}} z {{{commitStageValue}}}',
    'LBL_EXCLUDED_PIPELINE_HELP_CUSTOM_RANGE' => 'Otvorené príležitosti v nezahrnutej {{{forecastStage}}}',
    'LBL_WON_HELP' => 'Príležitosti, ktoré už boli získané',
    'LBL_LOST_HELP' => 'Príležitosti, ktoré už boli nezískané',
    'LBL_ALL_HELP' => 'Všetky príležitosti',

    'LBL_METRIC_HELP_QUOTA' => 'Kvóta',
    'LBL_METRIC_HELP_COMMITMENT' => 'Pravdepodobný záväzok',
    'LBL_METRIC_HELP_QUOTA_COVERAGE' => '(Pipeline + Získané + Nezískané)/Kvóta',
    'LBL_METRIC_HELP_GAP_QUOTA' => 'Kvóta – Získané',
    'LBL_METRIC_HELP_PCT_WON_QUOTA' => 'Získané/Kvóta',
    'LBL_METRIC_HELP_QUOTA_GAP_COVERAGE' => 'Pipeline / Rozdiel voči kvóte',
    'LBL_METRIC_HELP_COMMITMENT_COVERAGE' => '(Pipeline + Získané + Nezískané)/Záväzok',
    'LBL_METRIC_HELP_GAP_COMMITMENT' => 'Pravdepodobný záväzok – Získané',
    'LBL_METRIC_HELP_COMMITMENT_GAP_COVERAGE' => 'Pipeline / Rozdiel voči pravdepodobnému záväzku',
    'LBL_METRIC_HELP_PCT_WON_COMMITMENT' => 'Získané/Pravdepodobný záväzok',
    'LBL_METRIC_HELP_FORECAST_COVERAGE' => '(Pipeline + Získané + Nezískané)/Prognóza',
    'LBL_METRIC_HELP_GAP_FORECAST' => 'Prognóza – Získané (vrátane pipeline)',
    'LBL_METRIC_HELP_FORECAST_GAP_COVERAGE' => 'Pipeline / Rozdiel voči prognóze',
    'LBL_METRIC_HELP_PCT_WON_FORECAST' => 'Získané/Prognóza',

    // js error strings
    'LBL_FC_START_DATE' => 'Dátum začiatku:',
    'LBL_FC_USER' => 'Plán pre',

    'LBL_NO_ACTIVE_TIMEPERIOD' => 'Žiadne aktívne časové obdobie pre prognostiku.',
    'LBL_FDR_ADJ_AMOUNT' => 'Upravená čiastka',
    'LBL_SAVE_WOKSHEET' => 'Uložiť tabuľku',
    'LBL_RESET_WOKSHEET' => 'Obnoviť tabuľku',
    'LBL_SHOW_CHART' => 'Zobraziť graf',
    'LBL_RESET_CHECK' => 'Všetky dáta tabuľky pre zvolené časové obdobie a prihláseného používateľa budú odstránené. Pokračovať?',

    'LB_FS_LIKELY_CASE' => 'Pravdepodobný prípad',
    'LB_FS_WORST_CASE' => 'Najhorší prípad',
    'LB_FS_BEST_CASE' => 'Najlepší prípad',
    'LBL_FDR_WK_LIKELY_CASE' => 'Odhad najpravdepodobnejšieho prípadu',
    'LBL_FDR_WK_BEST_CASE' => 'Odhadovaný najlepší prípad',
    'LBL_FDR_WK_WORST_CASE' => 'Odhadovaný najhorší prípad',
    'LBL_FDR_C_BEST_CASE' => 'Najlepší prípad',
    'LBL_FDR_C_WORST_CASE' => 'Najhorší prípad',
    'LBL_FDR_C_LIKELY_CASE' => 'Pravdepodobný prípad',
    'LBL_QC_LAST_BEST_CASE' => 'Posledná schválená čiastka (najlepší prípad):',
    'LBL_QC_LAST_LIKELY_CASE' => 'Posledná schválená čiastka (pravdepodobný prípad):',
    'LBL_QC_LAST_WORST_CASE' => 'Posledná schválená čiastka (najhorší prípad):',
    'LBL_QC_ROLL_BEST_VALUE' => 'Kumulatívna schválená čiastka (najlepší prípad):',
    'LBL_QC_ROLL_LIKELY_VALUE' => 'Kumulatívna schválená čiastka (pravdepodobný prípad):',
    'LBL_QC_ROLL_WORST_VALUE' => 'Kumulatívna schválená čiastka (najhorší prípad):',
    'LBL_QC_COMMIT_BEST_CASE' => 'Schválená čiastka (najlepší prípad):',
    'LBL_QC_COMMIT_LIKELY_CASE' => 'Schválená čiastka (pravdepodobný prípad):',
    'LBL_QC_COMMIT_WORST_CASE' => 'Schválená čiastka (najhorší prípad):',
    'LBL_CURRENCY' => 'Mena',
    'LBL_CURRENCY_ID' => 'ID meny',
    'LBL_CURRENCY_RATE' => 'Menový kurz',
    'LBL_BASE_RATE' => 'Základná sadzba',

    'LBL_QUOTA' => 'Kvóta',
    'LBL_QUOTA_ADJUSTED' => 'Upravená kvóta',

    'LBL_FORECAST_FOR' => 'Prognostická tabuľka pre: ',
    'LBL_FMT_ROLLUP_FORECAST' => '(Kumulatívne)',
    'LBL_FMT_DIRECT_FORECAST' => '(Priame)',

    //labels used by the chart.
    'LBL_GRAPH_TITLE' => 'História prognóz',
    'LBL_GRAPH_QUOTA_ALTTEXT' => 'Kvóta pre %s',
    'LBL_GRAPH_COMMIT_ALTTEXT' => 'Schválená čiastka pre %s',
    'LBL_GRAPH_OPPS_ALTTEXT' => 'Hodnota uzavretých obchodov v %s',

    'LBL_GRAPH_QUOTA_LEGEND' => 'Kvóta',
    'LBL_GRAPH_COMMIT_LEGEND' => 'Schválená prognóza',
    'LBL_GRAPH_OPPS_LEGEND' => 'Uzavreté obchody',
    'LBL_TP_QUOTA' => 'Kvóta:',
    'LBL_CHART_FOOTER' => 'História prognóz <br>Kvóta verzus suma prognózy verzus hodnota uzatvorených obchodov',
    'LBL_TOTAL_VALUE' => 'Celkom:',
    'LBL_COPY_AMOUNT' => 'Celková čiastka',
    'LBL_COPY_WEIGH_AMOUNT' => 'Celková vážená čiastka',
    'LBL_WORKSHEET_AMOUNT' => 'Celková odhadovaná čiastka',
    'LBL_COPY' => 'Kopírovať hodnoty',
    'LBL_COMMIT_AMOUNT' => 'Suma schválených hodnôt.',
    'LBL_CUMULATIVE_TOTAL' => 'Kumulatívny súčet',
    'LBL_COPY_FROM' => 'Kopírovať hodnotu z:',

    'LBL_CHART_TITLE' => 'Kvóta verzus schválené verzus aktuálne',

    'LBL_FORECAST' => 'Prognóza',
    'LBL_COMMITMENT' => 'Záväzok',
    'LBL_TEAM_COMMITMENT' => 'Tímový záväzok',
    'LBL_FORECASTED' => 'Prognózované',
    'LBL_OPPORTUNITY_FORECAST' => 'Prognóza príležitostí',
    'LBL_REVENUELINEITEM_FORECAST' => 'Prognóza riadkových položiek výnosov',
    'LBL_LAST_COMMITMENT' => 'Posledný záväzok',
    'LBL_ADJUSTED_TOTAL' => 'Upravené celkom',
    'LBL_COMMIT_STAGE' => 'Fáza schválenia',
    'LBL_SALES_STAGE' => 'Fáza',
    'LBL_AMOUNT' => 'Čiastka',
    'LBL_PERCENT' => 'Percento',
    'LBL_DATE_CLOSED' => 'Predpokladané uzavretie',
    'LBL_PRODUCT_ID' => 'ID produktu',
    'LBL_QUOTA_ID' => 'ID kvóty',
    'LBL_VERSION' => 'Verzia',
    'LBL_CHART_BAR_LEGEND_CLOSE' => 'Skryť lištu legendy',
    'LBL_CHART_BAR_LEGEND_OPEN' => 'Zobraziť lištu legendy',
    'LBL_CHART_LINE_LEGEND_CLOSE' => 'Skryť riadok legendy',
    'LBL_CHART_LINE_LEGEND_OPEN' => 'Zobraziť riadok legendy',

    //Labels for forecasting history log and endpoint
    'LBL_ERROR_NOT_MANAGER' => 'Chyba: používateľ {0} nemá prístup správcu na požadovanie prognóz pre {1}',
    'LBL_UP' => 'hore',
    'LBL_DOWN' => 'dole',
    'LBL_PREVIOUS_COMMIT' => 'Posledné schválenie:',

    'LBL_COMMITTED_HISTORY_SETUP_FORECAST' => 'Nastaviť prognózu',
    'LBL_COMMITTED_HISTORY_UPDATED_FORECAST' => 'Aktualizovaná prognóza',
    'LBL_COMMITTED_HISTORY_1_SHOWN' => '{{{intro}}} {{{first}}}',
    'LBL_COMMITTED_HISTORY_2_SHOWN' => '{{{intro}}} {{{first}}}, {{{second}}}',
    'LBL_COMMITTED_HISTORY_3_SHOWN' => '{{{intro}}} {{{first}}}, {{{second}}} a {{{third}}}',
    'LBL_COMMITTED_HISTORY_LIKELY_CHANGED' => 'Potvrdené (pravdepodobné) {{{direction}}} {{{from}}} až {{{to}}}',
    'LBL_COMMITTED_HISTORY_BEST_CHANGED' => 'Potvrdené (najlepšie) {{{direction}}} {{{from}}} až {{{to}}}',
    'LBL_COMMITTED_HISTORY_WORST_CHANGED' => 'Potvrdené (najhoršie) {{{direction}}} {{{from}}} až {{{to}}}',
    'LBL_COMMITTED_HISTORY_LIKELY_SAME' => 'Potvrdené (pravdepodobné) zostali rovnaké',
    'LBL_COMMITTED_HISTORY_BEST_SAME' => 'Potvrdené (najlepšie) zostali rovnaké',
    'LBL_COMMITTED_HISTORY_WORST_SAME' => 'Potvrdené (najhoršie) zostali rovnaké',


    'LBL_COMMITTED_THIS_MONTH' => 'Tento mesiac: {0}',
    'LBL_COMMITTED_MONTHS_AGO' => 'pred {0} mesiacmi: {1}',

    //Labels for jsTree implementation
    'LBL_TREE_PARENT' => 'Nadradený',

    // Label for Current User Rep Worksheet Line
    // &#x200E; tells the browser to interpret as left-to-right
    'LBL_MY_MANAGER_LINE' => '{0} (me)&#x200E;',

    //Labels for worksheet items
    'LBL_EXPECTED_OPPORTUNITIES' => 'Očakávané príležitosti',
    'LBL_DISPLAYED_TOTAL' => 'Zobrazená celková suma',
    'LBL_TOTAL' => 'Celkom',
    'LBL_OVERALL_TOTAL' => 'Celkový súčet',
    'LBL_EDITABLE_INVALID' => 'Neplatná hodnota pre {{field_name}}',
    'LBL_EDITABLE_INVALID_RANGE' => 'Hodnota musí byť medzi {0} a {1}',
    'LBL_WORKSHEET_SAVE_CONFIRM_UNLOAD' => 'V tabuľke máte neuložené zmeny.',
    'LBL_WORKSHEET_EXPORT_CONFIRM' => 'Upozorňujeme, že exportovať možno iba uložené alebo schválené dáta. Kliknutím na tlačidlo Zrušiť operáciu zrušte. Potvrdením spustite export uložených dát.',
    'LBL_WORKSHEET_ID' => 'ID tabuľky',

    // Labels for Chart Options
    'LBL_DATA_SET' => 'Dátový súbor:',
    'LBL_GROUP_BY' => 'Zoskupiť podľa:',
    'LBL_CHART_OPTIONS' => 'Možnosti grafu',
    'LBL_CHART_AMOUNT' => 'Čiastka',
    'LBL_CHART_TYPE' => 'Typ',

    // Labels for Data Filters
    'LBL_FILTERS' => 'Filtre',

    // Labels for toggle buttons
    'LBL_MORE' => 'Viac',
    'LBL_LESS' => 'Menej',

    // Labels for Progress
    'LBL_PROJECTED' => 'Predpokladané',
    'LBL_DISTANCE_ABOVE_LIKELY_FROM_QUOTA' => 'Pravdepodobné nad kvótou',
    'LBL_DISTANCE_LEFT_LIKELY_TO_QUOTA' => 'Pravdepodobné pod kvótou',
    'LBL_DISTANCE_ABOVE_BEST_FROM_QUOTA' => 'Najlepšie nad kvótou',
    'LBL_DISTANCE_LEFT_BEST_TO_QUOTA' => 'Najlepšie pod kvótou',
    'LBL_DISTANCE_ABOVE_WORST_FROM_QUOTA' => 'Najhoršie nad kvótou',
    'LBL_DISTANCE_LEFT_WORST_TO_QUOTA' => 'Najhoršie pod kvótou',
    'LBL_CLOSED' => 'Uzatvorené/získané',
    'LBL_DISTANCE_ABOVE_LIKELY_FROM_CLOSED' => 'Pravdepodobne viac ako uzatvorených',
    'LBL_DISTANCE_LEFT_LIKELY_TO_CLOSED' => 'Pravdepodobne menej ako uzatvorených',
    'LBL_DISTANCE_ABOVE_BEST_FROM_CLOSED' => 'Najlepšie nad uzatvorenými',
    'LBL_DISTANCE_LEFT_BEST_TO_CLOSED' => 'Najlepšie pod uzatvorenými',
    'LBL_DISTANCE_ABOVE_WORST_FROM_CLOSED' => 'Najhoršie nad uzatvorenými',
    'LBL_DISTANCE_LEFT_WORST_TO_CLOSED' => 'Najhoršie pod uzatvorenými',
    'LBL_REVENUE' => 'Výnos',
    'LBL_PIPELINE_REVENUE' => 'Os výnosov',
    'LBL_PIPELINE_OPPORTUNITIES' => 'Os príležitostí',
    'LBL_LOADING' => 'Načítavanie',
    'LBL_IN_FORECAST' => 'V prognóze',

    // Actions Dropdown
    'LBL_ACTIONS' => 'Akcie',
    'LBL_EXPORT_CSV' => 'Exportovať CSV',
    'LBL_CANCEL' => 'Zrušiť',

    'LBL_CHART_FORECAST_FOR' => ' pre {0}',
    'LBL_FORECAST_TITLE' => 'Prognóza: {0}',
    'LBL_CHART_INCLUDED' => 'Zahrnutý',
    'LBL_CHART_NOT_INCLUDED' => 'Nezahrnutý',
    'LBL_CHART_ADJUSTED' => '(Upravený)',
    'LBL_SAVE_DRAFT' => 'Uložiť',
    'LBL_CHANGES_BY' => 'Zmeny vykonané používateľom {0}',
    'LBL_FORECAST_SETTINGS' => 'Nastavenia prognózy',

    // config panels strings
    'LBL_FORECASTS_CONFIG_TITLE' => 'Nastavenia prognóz',

    'LBL_FORECASTS_MISSING_STAGE_TITLE' => 'Chyba konfigurácie prognóz:',
    'LBL_FORECASTS_MISSING_SALES_STAGE_VALUES' => 'Modul Prognózy bol nesprávne nakonfigurovaný a už nie je k dispozícii. Získaná fáza predaja a nezískaná fáza predaja chýbajú z dostupných hodnôt fáz predaja. Obráťte sa na administrátora.',
    'LBL_FORECASTS_ACLS_NO_ACCESS_TITLE' => 'Chyba prístupu do prognóz:',
    'LBL_FORECASTS_ACLS_NO_ACCESS_MSG' => 'Nemáte prístup k modulu Prognózy. Obráťte sa na administrátora.',

    'LBL_FORECASTS_RECORDS_ACLS_NO_ACCESS_MSG' => 'Nemáte prístup k záznamom modulu Prognózy. Obráťte sa na administrátora.',

    // Panel and BreadCrumb Labels
    'LBL_FORECASTS_CONFIG_BREADCRUMB_WORKSHEET_LAYOUT' => 'Rozloženie tabuľky',
    'LBL_FORECASTS_CONFIG_BREADCRUMB_RANGES' => 'Rozpätia',
    'LBL_FORECASTS_CONFIG_BREADCRUMB_SCENARIOS' => 'Scenáre',
    'LBL_FORECASTS_CONFIG_BREADCRUMB_TIMEPERIODS' => 'Časové obdobia',
    'LBL_FORECASTS_CONFIG_BREADCRUMB_VARIABLES' => 'Premenné',

    // Admin UI
    'LBL_FORECASTS_CONFIG_TITLE_FORECAST_SETTINGS' => 'Nastavenia prognózy',
    'LBL_FORECASTS_CONFIG_TITLE_TIMEPERIODS' => 'Časové obdobie',
    'LBL_FORECASTS_CONFIG_TITLE_RANGES' => 'Rozsahy prognózy',
    'LBL_FORECASTS_CONFIG_TITLE_SCENARIOS' => 'Scenáre',
    'LBL_FORECASTS_CONFIG_TITLE_WORKSHEET_COLUMNS' => 'Stĺpce tabuľky',
    'LBL_FORECASTS_CONFIG_TITLE_FORECAST_BY' => 'Zobraziť prognostickú tabuľku podľa',

    'LBL_FORECASTS_CONFIG_HOWTO_TITLE_FORECAST_BY' => 'Prognóza podľa',

    'LBL_FORECASTS_CONFIG_TITLE_MESSAGE_TIMEPERIODS' => 'Začiatok fiškálneho roku:',

    'LBL_FORECASTS_CONFIG_HELP_TIMEPERIODS' => 'Nastavte časové obdobie, ktoré bude použité v module Prognózy.<br><br>Upozorňujeme, že nastavenia časového obdobia po úvodnom nastavení nemožno meniť.<br><br>Začnite výberom dátumu začatia svojho fiškálneho roka. Potom zvoľte typ časového obdobia, ktoré chcete plánovať. Rozsah dát pre časové obdobie bude automaticky vypočítaný na základe vášho výberu. Čiastkové časové obdobie je základom pre prognostickú tabuľku.<br><br>Zobrazením budúcich a minulých časových období sa určí počet viditeľných čiastkových období v module Prognózy. Používatelia môžu zobraziť a upraviť prognostické čísla viditeľných čiastkových období.',
    'LBL_FORECASTS_CONFIG_HELP_RANGES' => 'Nastavte, ako chcete kategorizovať {{forecastByModule}}. <br><br>Upozorňujeme, že nastavenia rozsahu po prvom schválení nemožno meniť. V prípade inovovaných inštancií je nastavenie rozsahu zamknuté s existujúcimi údajmi prognóz.<br><br>Môžete vybrať dve alebo viac kategórií založených na rozsahoch pravdepodobnosti alebo vytvoriť kategórie, ktoré nie sú založené na pravdepodobnosti. <br><br>Naľavo od vašich vlastných kategórií sú začiarkavacie polia, prostredníctvom ktorých môžete určiť rozsahy, ktoré budú zahrnuté v schválenom množstve prognóz a nahlásené manažérom. <br><br>Používateľ môže v tabuľke manuálne meniť stav zahrnuté/nezahrnuté a kategóriu modulu {{forecastByModule}}.',
    'LBL_FORECASTS_CONFIG_HELP_SCENARIOS' => 'Vyberte stĺpce, ktoré chcete, aby používateľ vyplnil pre svoje prognózy v jednotlivých {{forecastByModuleSingular}}. Upozorňujeme, že pravdepodobné množstvo je viazané na množstvo uvedené v {{forecastByModule}}; z tohto dôvodu stĺpec s pravdepodobnými údajmi nesmie byť skrytý.',
    'LBL_FORECASTS_CONFIG_HELP_WORKSHEET_COLUMNS' => 'Vyberte stĺpce, ktoré chcete zobraziť v module Prognózy. Zoznam polí bude kombinovať tabuľku a umožní používateľovi vybrať si, ako nakonfigurovať svoje zobrazenie.',
    'LBL_FORECASTS_CONFIG_HELP_FORECAST_BY' => 'Som zástupný symbol pre text prognózy podľa metódy Ako!',
    'LBL_FORECASTS_CONFIG_SETTINGS_SAVED' => 'Konfiguračné nastavenia prognózy boli uložené.',

    // timeperiod config
    //TODO-sfa remove this once the ability to map buckets when they get changed is implemented (SFA-215).
    'LBL_FORECASTS_CONFIG_TIMEPERIOD_SETUP_NOTICE' => 'Upozorňujeme, že nastavenia časového obdobia po úvodnom nastavení nemožno meniť.',
    'LBL_FORECASTS_CONFIG_TIMEPERIOD_DESC' => 'Konfigurácia časových období použitá pre modul Prognózy.',
    'LBL_FORECASTS_CONFIG_TIMEPERIOD_TYPE' => 'Vyberte typ roka, ktorý vaša organizácia používa na účely účtovníctva.',
    'LBL_FORECASTS_CONFIG_TIMEPERIOD' => 'Vyberte typ časového obdobia',
    'LBL_FORECASTS_CONFIG_LEAFPERIOD' => 'Vyberte čiastkové obdobie, pre ktoré chcete zobraziť dobu:',
    'LBL_FORECASTS_CONFIG_START_DATE' => 'Vyberte začiatok fiškálneho roka',
    'LBL_FORECASTS_CONFIG_TIMEPERIODS_FORWARD' => 'Zvoľte počet budúcich období, ktoré sa budú zobrazovať v tabuľke.<br><i>Toto číslo sa vzťahuje na základné časové obdobie, ktoré ste vybrali. Ak napríklad vyberiete možnosť 2 s ročným časovým obdobím, zobrazí sa 8 budúcich štvrťrokov.</i>',
    'LBL_FORECASTS_CONFIG_TIMEPERIODS_BACKWARD' => 'Zvoľte počet minulých období, ktoré sa budú zobrazovať v tabuľke.<br><i>Toto číslo sa vzťahuje na základné časové obdobie, ktoré ste vybrali. Ak napríklad vyberiete možnosť 2 so štvrťročným časovým obdobím, zobrazí sa 6 predchádzajúcich mesiacov</i>',
    'LBL_FORECASTS_CONFIG_TIMEPERIOD_FISCAL_YEAR' => 'Zvolený dátum začiatku určuje, že fiškálny rok môže zahŕňať obdobie dvoch rokov. Vyberte, ktorý rok chcete použiť ako fiškálny rok:',
    'LBL_FISCAL_YEAR' => 'Fiškálny rok',

    // worksheet layout config
    'LBL_FORECASTS_CONFIG_GENERAL_FORECAST_BY_TEXT' => 'Vyberte spôsob vyplnenia tabuľky prognóz:',
    'LBL_FORECASTS_CONFIG_GENERAL_FORECAST_BY_OPPORTUNITIES' => 'Obchodné príležitosti',
    'LBL_FORECASTS_CONFIG_GENERAL_FORECAST_BY_PRODUCT_LINE_ITEMS' => 'Položky krivky výnosu',
    'LBL_REVENUELINEITEM_NAME' => 'Názov položky krivky výnosu',
    'LBL_FORECASTS_CONFIG_WORKSHEET_LAYOUT_DETAIL_MESSAGE' => 'Tabuľky sa vyplnia nasledujúcimi údajmi:',

    // ranges config
    //TODO-sfa remove this once the ability to map buckets when they get changed is implemented (SFA-215).
    'LBL_FORECASTS_CONFIG_RANGES_SETUP_NOTICE' => 'Nastavenia rozsahu nemožno po prvom uložení konceptu alebo schválení v module Prognózy zmeniť. V prípade inovovaných inštancií však nastavenia rozsahu nemožno zmeniť po počiatočnom nastavení, pretože dáta modulu Prognózy sú už k dispozícii prostredníctvom aktualizácie.',
    'LBL_FORECASTS_CONFIG_RANGES' => 'Možnosti rozsahu prognózy:',
    'LBL_FORECASTS_CONFIG_RANGES_OPTIONS' => 'Vyberte spôsob, akým by ste chceli kategorizovať {{forecastByModule}}.',
    'LBL_FORECASTS_CONFIG_SHOW_BINARY_RANGES_DESCRIPTION' => 'Táto možnosť poskytuje používateľovi možnosť zadať {{forecastByModule}}, ktoré budú zahrnuté alebo vylúčené z prognózy.',
    'LBL_FORECASTS_CONFIG_SHOW_BUCKETS_RANGES_DESCRIPTION' => 'Táto možnosť poskytuje používateľovi možnosť kategorizovať svoje {{forecastByModule}}, ktoré nie sú zahrnuté v schválení, ale sú hore a majú potenciál uzatvárania, ak všetko dobre dopadne, a {{forecastByModule}}, ktoré majú byť z prognózy vylúčené.',
    'LBL_FORECASTS_CONFIG_SHOW_CUSTOM_BUCKETS_RANGES_DESCRIPTION' => 'Vlastné rozsahy: Táto možnosť poskytuje používateľovi možnosť kategorizovať svoje {{forecastByModule}}, aby mohli byť schválené do prognózy v rámci schváleného rozsahu, vylúčeného rozsahu a ostatných, ktoré nastavujete.',
    'LBL_FORECASTS_CONFIG_RANGES_EXCLUDE_INFO' => 'Vylúčený rozsah je v predvolenom nastavení od 0% do minimálneho predchádzajúceho rozsahu prognózy.',

    'LBL_FORECASTS_CONFIG_RANGES_ENTER_RANGE' => 'Zadajte názov rozsahu ...',

    // scenarios config
    //TODO-sfa refactors the code references for scenarios to be scenarios (SFA-337).
    'LBL_FORECASTS_CONFIG_WORKSHEET_SCENARIOS' => 'Vyberte scenáre, ktoré chcete zahrnúť do prognostickej tabuľky.',
    'LBL_FORECASTS_CONFIG_WORKSHEET_LIKELY_INFO' => 'Pravdepodobne je založený na množstve zadanom v module {{forecastByModule}}.',
    'LBL_FORECASTS_CONFIG_WORKSHEET_SCENARIOS_LIKELY' => 'Pravdepodobne',
    'LBL_FORECASTS_CONFIG_WORKSHEET_SCENARIOS_BEST' => 'Najlepšie',
    'LBL_FORECASTS_CONFIG_WORKSHEET_SCENARIOS_WORST' => 'Najhoršie',
    'LBL_FORECASTS_CONFIG_PROJECTED_SCENARIOS' => 'Zobraziť celkové predpokladané scenáre',
    'LBL_FORECASTS_CONFIG_PROJECTED_SCENARIOS_LIKELY' => 'Zobraziť súčty pravdepodobných prípadov',
    'LBL_FORECASTS_CONFIG_PROJECTED_SCENARIOS_BEST' => 'Zobraziť súčty najlepších prípadov',
    'LBL_FORECASTS_CONFIG_PROJECTED_SCENARIOS_WORST' => 'Zobraziť súčty najhorších prípadov',

    // variables config
    'LBL_FORECASTS_CONFIG_VARIABLES' => 'Premenné',
    'LBL_FORECASTS_CONFIG_VARIABLES_DESC' => 'Vzorce pre metrické tabuľky sú založené na fáze predaja pre {{forecastByModule}}, ktoré treba vylúčiť z pipeline, t. j.{{forecastByModule}}, ktoré sú uzavreté a nezískané.',
    'LBL_FORECASTS_CONFIG_VARIABLES_CLOSED_LOST_STAGE' => 'Vyberte fázu predaja, ktorá predstavuje uzavreté a nezískané {{forecastByModule}}:',
    'LBL_FORECASTS_CONFIG_VARIABLES_CLOSED_WON_STAGE' => 'Vyberte fázu predaja, ktorá predstavuje uzavreté a získané {{forecastByModule}}:',
    'LBL_FORECASTS_CONFIG_VARIABLES_FORMULA_DESC' => 'Z tohto dôvodu bude vzorec pre pipeline:',

    'LBL_FORECASTS_WIZARD_SUCCESS_TITLE' => 'Úspešne dokončené:',
    'LBL_FORECASTS_WIZARD_SUCCESS_MESSAGE' => 'Úspešne ste nastavili modul Prognózy. Počkajte, kým sa modul načíta.',
    'LBL_FORECASTS_TABBED_CONFIG_SUCCESS_MESSAGE' => 'Konfiguračné nastavenie prognóz bolo uložené. Počkajte, kým sa modul načíta.',
    // Labels for Success Messages:
    'LBL_FORECASTS_WORKSHEET_SAVE_DRAFT_SUCCESS' => 'Uložili ste prognostické tabuľky ako koncept pre zvolené časové obdobie.',
    'LBL_FORECASTS_WORKSHEET_COMMIT_SUCCESS' => 'Schválili ste svoju prognózu',
    'LBL_FORECASTS_WORKSHEET_COMMIT_SUCCESS_TO' => 'Odovzdali ste svoju prognózu manažérovi {{manager}}',

    // custom ranges
    'LBL_FORECASTS_CUSTOM_RANGES_DEFAULT_NAME' => 'Vlastný rozsah',
    'LBL_UNAUTH_FORECASTS' => 'Neoprávnený prístup k nastaveniam prognózy.',
    'LBL_FORECASTS_RANGES_BASED_TITLE' => 'Rozsahy založené na pravdepodobnostiach',
    'LBL_FORECASTS_CUSTOM_BASED_TITLE' => 'Vlastné rozsahy založené na pravdepodobnostiach',
    'LBL_FORECASTS_CUSTOM_NO_BASED_TITLE' => 'Rozsahy sa nezakladajú na pravdepodobnostiach',

    // worksheet columns config
    'LBL_DISCOUNT' => 'Zľava',
    'LBL_OPPORTUNITY_STATUS' => 'Stav príležitosti',
    'LBL_OPPORTUNITY_NAME' => 'Názov obchodnej príležitosti',
    'LBL_PRODUCT_TEMPLATE' => 'Katalóg produktov',
    'LBL_CAMPAIGN' => 'Kampaň',
    'LBL_TEAMS' => 'Tímy',
    'LBL_CATEGORY' => 'Kategória',
    'LBL_COST_PRICE' => 'Obstarávacia cena',
    'LBL_TOTAL_DISCOUNT_AMOUNT' => 'Celková výška zľavy',
    'LBL_FORECASTS_CONFIG_WORKSHEET_TEXT' => 'Vyberte stĺpce, ktoré majú byť v tabuľke zobrazené. V predvolenom nastavení budú vybraté nasledujúce polia:',

    // forecast details dashlet
    'LBL_DASHLET_FORECAST_NOT_SETUP' => 'Prognózy neboli nakonfigurované a musia byť nastavené na použitie tohto softvérového komponentu. Obráťte sa na svojho systémového administrátora.',
    'LBL_DASHLET_FORECAST_NOT_SETUP_ADMIN' => 'Prognózy neboli nakonfigurované a musia byť nastavené na použitie tohto softvérového komponentu.',
    'LBL_DASHLET_FORECAST_CONFIG_LINK_TEXT' => 'Kliknite tu a nakonfigurujte modul Prognózy.',
    'LBL_DASHLET_MY_PIPELINE' => 'Moja pipeline',
    'LBL_DASHLET_MY_TEAMS_PIPELINE' => "Pipeline môjho tímu",
    'LBL_DASHLET_PIPELINE_CHART_NAME' => 'Graf pipeline prognóz',
    'LBL_DASHLET_PIPELINE_CHART_DESC' => 'Zobrazuje aktuálny graf pipeline.',
    'LBL_FORECAST_DETAILS_DEFICIT' => 'Deficit',
    'LBL_FORECAST_DETAILS_SURPLUS' => 'Prebytok',
    'LBL_FORECAST_DETAILS_SHORT' => 'Nedosiahnuté o',
    'LBL_FORECAST_DETAILS_EXCEED' => 'Presahuje o',
    'LBL_FORECAST_DETAILS_NO_DATA' => 'Žiadne údaje',
    'LBL_FORECAST_DETAILS_MEETING_QUOTA' => 'Kvóta schôdzky',
    'LBL_OPEN_LIKELY' => 'Otvoriť pravdepodobné',
    'LBL_OPEN_BEST' => 'Otvoriť najlepšie',
    'LBL_OPEN_WORST' => 'Otvoriť najhoršie',

    // Pipeline Metrics dashlet
    'LBL_PIPELINE_METRICS_DASHLET_NAME' => 'Metriky pipeline',
    'LBL_PIPELINE_METRICS_DASHLET_DESC' => 'Zobrazí podrobnosti metrík pipeline',
    'LBL_PIPELINE_METRICS_DASHLET_METRICS_GUIDE' => 'Sprievodca metrikami pipeline',
    'LBL_PIPELINE_METRICS_DASHLET_CONFIG_METRICS' => 'Zobraziť metriky',
    'LBL_PIPELINE_METRICS_DASHLET_CONFIG_METRICS_GUIDE' => 'Sprievodca metrikami',
    'LBL_PIPELINE_METRICS_DASHLET_CONFIG_METRICS_REQUIRED' => 'Musíte vybrať aspoň jednu metriku, ktorá sa má zobraziť na kartách metrík',

    'LBL_ASSIGN_QUOTA_BUTTON' => 'Priradiť kvótu',
    'LBL_ASSIGNING_QUOTA' => 'Priradenie kvóty',
    'LBL_QUOTA_ASSIGNED' => 'Kvóty boli úspešne priradené.',
    'LBL_FORECASTS_NO_ACCESS_TO_CFG_TITLE' => 'Chyba prístupu do prognóz',
    'LBL_FORECASTS_NO_ACCESS_TO_CFG_MSG' => 'Nemáte prístup ku konfigurácii prognóz. Obráťte sa na administrátora.',
    'WARNING_DELETED_RECORD_RECOMMIT_1' => 'Tento záznam bol zahrnutý v ',
    'WARNING_DELETED_RECORD_RECOMMIT_2' => 'Odstráni sa a budete musieť znova odovzdať ',

    'LBL_DASHLET_MY_FORECAST' => 'Moja prognóza',
    'LBL_DASHLET_MY_TEAMS_FORECAST' => "Prognóza môjho tímu",

    'LBL_WARN_UNSAVED_CHANGES_CONFIRM_SORT' => 'Neuložili ste zmeny. Naozaj chcete zoradiť tabuľku a zahodiť zmeny?',

    'LBL_SAVE_TOOLTIP' => 'Uložiť zmeny vykonané v riadkoch tabuľky',
    'LBL_COMMIT_TOOLTIP_REP' => 'Uložiť zmeny v záznamoch a odoslať prognózu manažérovi',
    'LBL_COMMIT_TOOLTIP_MGR' => 'Odoslať prognózu manažérovi a priradiť kvótu',

    'LBL_SAVE_LABEL_REP' => 'Uložiť ',
    'LBL_SAVE_LABEL_MGR' => 'Uložiť upravené hodnoty',

    // Forecasts Records View Help Text
    'LBL_HELP_RECORDS' => 'Modul {{plural_module_name}} využíva záznamy {{forecastby_singular_module}} na zostavenie {{forecastworksheets_module}} a predpovedanie predaja. Používatelia môžu pracovať vo vzťahu k predaju {{quotas_module}} na úrovni jednotlivca, tímu a predajnej organizácie. Pred tým, ako používatelia získajú prístup k modulu {{plural_module_name}}, administrátor musí vybrať časové obdobia, rozsahy a scenáre požadované organizáciou.

Obchodní zástupcovia v priebehu aktuálneho časového obdobia používajú modul {{plural_module_name}} na prácu s priradeným modulom {{forecastby_module}}. Títo používatelia musia potvrdzovať celkové predpovede pre svoje osobné predaje na základe {{forecastby_module}}, ktoré očakávajú, že uzatvoria. Manažéri predaja pracujú so svojimi záznamami {{forecastby_singular_module}} podobne ako obchodní zástupcovia. Okrem toho však ešte zhromažďujú hodnoty nahlásené ich podriadenými a predpovedajú celkový predaj celého tímu a stanovujú pre tím pre každé časové obdobie kvótu. Ďalšie podrobnosti možno získať po rozbalení poľa s informáciami vrátane analýzy pre tabuľku jednotlivca a analýzy pre tabuľky tímu daného manažéra.',
];
