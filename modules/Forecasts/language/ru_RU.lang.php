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
    'LBL_FORECASTS_DASHBOARD' => 'Информационная панель прогнозов',
    'LBL_FORECASTS_RECORD_DASHBOARD' => 'Информационная панель записей прогнозов',

    //module strings.
    'LBL_MODULE_NAME' => 'Прогнозы',
    'LBL_MODULE_NAME_SINGULAR' => 'Прогноз',
    'LNK_NEW_OPPORTUNITY' => 'Новая сделка',
    'LBL_MODULE_TITLE' => 'Прогнозы',
    'LBL_LIST_FORM_TITLE' => 'Прогнозы ожидаемого',
    'LNK_UPD_FORECAST' => 'Лист прогнозов',
    'LNK_QUOTA' => 'Плановые объемы продаж',
    'LNK_FORECAST_LIST' => 'История прогноза',
    'LBL_FORECAST_HISTORY' => 'Прогнозы: история',
    'LBL_FORECAST_HISTORY_TITLE' => 'История',
    'LBL_TAB_COMMITMENT' => 'Подтверждение',
    'LBL_CURRENT' => 'Текущий',

    //var defs
    'LBL_TIMEPERIOD_NAME' => 'Период отчета',
    'LBL_USER_NAME' => 'Имя пользователя',
    'LBL_REPORTS_TO_USER_NAME' => 'Руководитель',

    //forecast table
    'LBL_FORECAST_ID' => 'ID',
    'LBL_FORECAST_TIME_ID' => 'ID периода отчета',
    'LBL_FORECAST_TYPE' => 'Тип прогноза',
    'LBL_FORECAST_OPP_COUNT' => 'Итоговая сумма сделки',
    'LBL_FORECAST_PIPELINE_OPP_COUNT' => 'Количество сделок по воронке',
    'LBL_FORECAST_OPP_WEIGH' => 'Взвешенная сумма',
    'LBL_FORECAST_OPP_COMMIT' => 'Вероятный случай',
    'LBL_FORECAST_OPP_BEST_CASE' => 'Лучшая продажа',
    'LBL_FORECAST_OPP_WORST' => 'Худшая продажа',
    'LBL_FORECAST_USER' => 'Пользователь',
    'LBL_DATE_COMMITTED' => 'Дата совершения продажи',
    'LBL_DATE_ENTERED' => 'Дата создания',
    'LBL_DATE_MODIFIED' => 'Дата изменения',
    'LBL_CREATED_BY' => 'Создано',
    'LBL_DELETED' => 'Удалено',
    'LBL_MODIFIED_USER_ID' => 'Изменено',
    'LBL_WK_VERSION' => 'Версия',
    'LBL_WK_REVISION' => 'Ревизия',

    // API message labels
    'LBL_METRICS_API_NO_ACCESS' => 'Для доступа к API показателей требуется Sugar Sell Premier',

    //Quick Commit labels.
    'LBL_QC_TIME_PERIOD' => 'Период отчета:',
    'LBL_QC_OPPORTUNITY_COUNT' => 'Количество сделок:',
    'LBL_QC_WEIGHT_VALUE' => 'Средняя стоимость:',
    'LBL_QC_COMMIT_VALUE' => 'Ожидаемая стоимость:',
    'LBL_QC_COMMIT_BUTTON' => 'Подтвердить',
    'LBL_QC_WORKSHEET_BUTTON' => 'Лист прогнозов',
    'LBL_QC_ROLL_COMMIT_VALUE' => 'Смещение суммы прогноза:',
    'LBL_QC_DIRECT_FORECAST' => 'Мой прямой прогноз',
    'LBL_QC_ROLLUP_FORECAST' => 'Мой групповой прогноз:',
    'LBL_QC_UPCOMING_FORECASTS' => 'Мои прогнозы',
    'LBL_QC_LAST_DATE_COMMITTED' => 'Дата последнего прогноза:',
    'LBL_QC_LAST_COMMIT_VALUE' => 'Последняя сумма прогноза:',
    'LBL_QC_HEADER_DELIM' => 'Кому',

    //opportunity worksheet list view labels
    'LBL_OW_OPPORTUNITIES' => 'Сделка',
    'LBL_OW_ACCOUNTNAME' => 'Контрагент',
    'LBL_OW_REVENUE' => 'Сумма',
    'LBL_OW_WEIGHTED' => 'Средняя стоимость',
    'LBL_OW_MODULE_TITLE' => 'Лист сделки',
    'LBL_OW_PROBABILITY' => 'Вероятность продажи',
    'LBL_OW_NEXT_STEP' => 'Следующий шаг',
    'LBL_OW_DESCRIPTION' => 'Описание',
    'LBL_OW_TYPE' => 'Тип',

    // Forecast manager worksheet (rollup)
    'LBL_RU_TEAM_FORECAST_HEADER' => 'Прогноз команды для <span class="sicon sicon-user-group mx-1"></span> {{name}}',

    //forecast worksheet direct reports forecast
    'LBL_FDR_USER_NAME' => 'Полный отчет',
    'LBL_FDR_OPPORTUNITIES' => 'Прогноз по продажам',
    'LBL_FDR_WEIGH' => 'Взвешенная сумма продаж',
    'LBL_FDR_COMMIT' => 'Совершенные продажи',
    'LBL_FDR_DATE_COMMIT' => 'Дата совершения продажи',
    'LBL_FDR_FORECAST_HEADER' => 'Прогноз продавца для <span class="sicon sicon-user mx-1"></span> {{name}}',

    //detail view.
    'LBL_DV_HEADER' => 'Прогнозы: лист',
    'LBL_DV_MY_FORECASTS' => 'Мои прогнозы',
    'LBL_DV_MY_TEAM' => "Прогнозы моей команды",
    'LBL_DV_TIMEPERIODS' => 'Периоды отчета:',
    'LBL_DV_FORECAST_PERIOD' => 'Период прогноза',
    'LBL_DV_FORECAST_OPPORTUNITY' => 'Сделки по прогнозу',
    'LBL_SEARCH' => 'Выбрать',
    'LBL_SEARCH_LABEL' => 'Выбрать',
    'LBL_COMMIT_HEADER' => 'Дата прогноза',
    'LBL_DV_LAST_COMMIT_DATE' => 'Дата последнего прогноза:',
    'LBL_DV_LAST_COMMIT_AMOUNT' => 'Последние ожидаемые суммы:',
    'LBL_DV_FORECAST_ROLLUP' => 'Сдвиг прогноза',
    'LBL_DV_TIMEPERIOD' => 'Период отчета:',
    'LBL_DV_TIMPERIOD_DATES' => 'Диапазон дат:',
    'LBL_LOADING_COMMIT_HISTORY' => 'Загружается история по продаже…',

    //list view
    'LBL_LV_TIMPERIOD' => 'Период времени',
    'LBL_LV_TIMPERIOD_START_DATE' => 'Дата начала',
    'LBL_LV_TIMPERIOD_END_DATE' => 'Дата окончания',
    'LBL_LV_TYPE' => 'Вид прогноза',
    'LBL_LV_COMMIT_DATE' => 'Дата совершения продажи',
    'LBL_LV_OPPORTUNITIES' => 'Продажи',
    'LBL_LV_WEIGH' => 'Взвешенная сумма',
    'LBL_LV_COMMIT' => 'Совершенные продажи',

    'LBL_COMMIT_NOTE' => 'Введите суммы, которые Вы хотите внести в прогноз за выбранный Период отчета:',
    'LBL_COMMIT_TOOLTIP' => 'Чтобы внести сумму: Измените цену\\стоимость в листе продажи',
    'LBL_COMMIT_MESSAGE' => 'Вы хотите внести эти суммы в прогноз?',
    'ERR_FORECAST_AMOUNT' => 'Необходимо внести сумму прогноза в виде числа.',

    //list view metrics
    'LBL_INCLUDED_PIPELINE' => 'Включена воронка продаж',
    'LBL_UPSIDE_PIPELINE' => 'Перевернутая воронка продаж',
    'LBL_EXCLUDED_PIPELINE' => 'Исключена воронка продаж',
    'LBL_WON' => 'Успешна',
    'LBL_LOST' => 'Проиграна',
    'LBL_ALL' => 'Все',

    'LBL_METRIC_LABEL_QUOTA' => 'Квота',
    'LBL_METRIC_LABEL_COMMITMENT' => 'Подтверждение',
    'LBL_METRIC_LABEL_QUOTA_COVERAGE' => 'Охват квоты',
    'LBL_METRIC_LABEL_GAP_QUOTA' => 'Расхождение с квотой',
    'LBL_METRIC_LABEL_PCT_WON_QUOTA' => '% успешных сделок от квот',
    'LBL_METRIC_LABEL_QUOTA_GAP_COVERAGE' => 'Охват расхождения с квотой',
    'LBL_METRIC_LABEL_COMMITMENT_COVERAGE' => 'Охват подтверждения',
    'LBL_METRIC_LABEL_GAP_COMMITMENT' => 'Расхождение с подтверждением',
    'LBL_METRIC_LABEL_COMMITMENT_GAP_COVERAGE' => 'Охват расхождения с подтверждением',
    'LBL_METRIC_LABEL_PCT_WON_COMMITMENT' => '% успешных сделок от подтверждения',
    'LBL_METRIC_LABEL_FORECAST_COVERAGE' => 'Охват прогноза',
    'LBL_METRIC_LABEL_GAP_FORECAST' => 'Расхождение с прогнозом',
    'LBL_METRIC_LABEL_FORECAST_GAP_COVERAGE' => 'Охват расхождения с прогнозом',
    'LBL_METRIC_LABEL_PCT_WON_FORECAST' => '% успешных сделок от прогноза',

    // Guide to Filters
    'LBL_FILTER_GUIDE_TITLE' => 'Руководство по фильтрам',
    'LBL_HELP_MAIN_BODY' => 'Все метрики включают только сделки, принадлежащие выбранному(-ым) пользователю(-ям), временной промежуток и дополнительные фильтры, которые вы применили',
    'LBL_FORECAST_HELP' => 'Сделки, которые уже были выиграны или находятся во включенном {{{forecastStage}}}',
    'LBL_INCLUDED_PIPELINE_HELP' => 'Открытые сделки с {{{forecastStage}}} из {{{commitStageValue}}}',
    'LBL_INCLUDED_PIPELINE_HELP_CUSTOM_RANGE' => 'Открытые сделки во включенном {{{forecastStage}}}',
    'LBL_UPSIDE_PIPELINE_HELP' => 'Открытые сделки с {{{forecastStage}}} из {{{commitStageValue}}}',
    'LBL_EXCLUDED_PIPELINE_HELP' => 'Открытые сделки с {{{forecastStage}}} из {{{commitStageValue}}}',
    'LBL_EXCLUDED_PIPELINE_HELP_CUSTOM_RANGE' => 'Открытые сделки в исключенном {{{forecastStage}}}',
    'LBL_WON_HELP' => 'Сделки, которые были уже выиграны',
    'LBL_LOST_HELP' => 'Сделки, которые были уже проиграны',
    'LBL_ALL_HELP' => 'Все сделки',

    'LBL_METRIC_HELP_QUOTA' => 'Квота',
    'LBL_METRIC_HELP_COMMITMENT' => 'Вероятное подтверждение',
    'LBL_METRIC_HELP_QUOTA_COVERAGE' => '(Воронка продаж + Успешная сделка + Несостоявшаяся сделка)/Квота',
    'LBL_METRIC_HELP_GAP_QUOTA' => 'Квота – успешная сделка',
    'LBL_METRIC_HELP_PCT_WON_QUOTA' => 'Успешная сделка/квота',
    'LBL_METRIC_HELP_QUOTA_GAP_COVERAGE' => 'Воронка продаж/расхождение с квотой',
    'LBL_METRIC_HELP_COMMITMENT_COVERAGE' => '(Воронка продаж + Успешная сделка + Несостоявшаяся сделка)/Подтверждение',
    'LBL_METRIC_HELP_GAP_COMMITMENT' => 'Вероятное подтверждение – успешная сделка',
    'LBL_METRIC_HELP_COMMITMENT_GAP_COVERAGE' => 'Воронка продаж/расхождение с вероятным подтверждением',
    'LBL_METRIC_HELP_PCT_WON_COMMITMENT' => 'Успешная сделка/вероятное подтверждение',
    'LBL_METRIC_HELP_FORECAST_COVERAGE' => '(Воронка продаж + Успешная сделка + Несостоявшаяся сделка)/Прогноз',
    'LBL_METRIC_HELP_GAP_FORECAST' => 'Прогноз – успешная сделка (включая воронку продаж)',
    'LBL_METRIC_HELP_FORECAST_GAP_COVERAGE' => 'Воронка продаж/расхождение с прогнозом',
    'LBL_METRIC_HELP_PCT_WON_FORECAST' => 'Успешная сделка/прогноз',

    // js error strings
    'LBL_FC_START_DATE' => 'Дата начала',
    'LBL_FC_USER' => 'Назначено для',

    'LBL_NO_ACTIVE_TIMEPERIOD' => 'Нет активного временного периода для прогнозирования',
    'LBL_FDR_ADJ_AMOUNT' => 'Установленная сумма',
    'LBL_SAVE_WOKSHEET' => 'Сохранить лист',
    'LBL_RESET_WOKSHEET' => 'Сбросить данные листа',
    'LBL_SHOW_CHART' => 'Посмотреть график',
    'LBL_RESET_CHECK' => 'Все данные листа выбранного временного диапазона и зарегистрированного пользователя, будут удалены. Продолжить?',

    'LB_FS_LIKELY_CASE' => 'Вероятная продажа',
    'LB_FS_WORST_CASE' => 'Пессимистичная продажа',
    'LB_FS_BEST_CASE' => 'Оптимистичная продажа',
    'LBL_FDR_WK_LIKELY_CASE' => 'Установленная вероятная продажа',
    'LBL_FDR_WK_BEST_CASE' => 'установленная оптимистичная продажа',
    'LBL_FDR_WK_WORST_CASE' => 'установленная пессимистичная продажа',
    'LBL_FDR_C_BEST_CASE' => 'Оптимистичная продажа',
    'LBL_FDR_C_WORST_CASE' => 'Пессимистичная продажа',
    'LBL_FDR_C_LIKELY_CASE' => 'Вероятная продажа',
    'LBL_QC_LAST_BEST_CASE' => 'Последняя внесенная сумма (лучшая продажа)',
    'LBL_QC_LAST_LIKELY_CASE' => 'Последняя внесенная сумма (вероятная продажа)',
    'LBL_QC_LAST_WORST_CASE' => 'Последняя внесенная сумма (Худшая продажа)',
    'LBL_QC_ROLL_BEST_VALUE' => 'Увеличенная внесенная сумма (лучшая продажа)',
    'LBL_QC_ROLL_LIKELY_VALUE' => 'Увеличенная внесенная сумма (вероятная продажа)',
    'LBL_QC_ROLL_WORST_VALUE' => 'Увеличенная внесенная продажа (худшая продажа)',
    'LBL_QC_COMMIT_BEST_CASE' => 'Внести сумму (лучшая продажа)',
    'LBL_QC_COMMIT_LIKELY_CASE' => 'Внести сумму (вероятная продажа)',
    'LBL_QC_COMMIT_WORST_CASE' => 'Внести сумму (худшая продажа)',
    'LBL_CURRENCY' => 'Валюта',
    'LBL_CURRENCY_ID' => 'ID валюты',
    'LBL_CURRENCY_RATE' => 'Валютный курс',
    'LBL_BASE_RATE' => 'Базовая процентная ставка',

    'LBL_QUOTA' => 'Плановый объем продаж',
    'LBL_QUOTA_ADJUSTED' => 'Скорректированный объем продаж',

    'LBL_FORECAST_FOR' => 'Лист прогноза для:',
    'LBL_FMT_ROLLUP_FORECAST' => '(Увеличенный)',
    'LBL_FMT_DIRECT_FORECAST' => '(Полный)',

    //labels used by the chart.
    'LBL_GRAPH_TITLE' => 'История по прогнозу',
    'LBL_GRAPH_QUOTA_ALTTEXT' => 'Квота на %s',
    'LBL_GRAPH_COMMIT_ALTTEXT' => 'Подтвержденная сумма для %s',
    'LBL_GRAPH_OPPS_ALTTEXT' => 'Значение сделок, закрытых в %s',

    'LBL_GRAPH_QUOTA_LEGEND' => 'Объемы продаж',
    'LBL_GRAPH_COMMIT_LEGEND' => 'Прогноз применен',
    'LBL_GRAPH_OPPS_LEGEND' => 'Совершенные продажи',
    'LBL_TP_QUOTA' => 'Объемы продаж:',
    'LBL_CHART_FOOTER' => 'История по прогнозу Объемы продаж относительно запрогнозированной суммы по совершенным продажам',
    'LBL_TOTAL_VALUE' => 'Итого',
    'LBL_COPY_AMOUNT' => 'Итоговая сумма',
    'LBL_COPY_WEIGH_AMOUNT' => 'Итоговая взвешенная сумма',
    'LBL_WORKSHEET_AMOUNT' => 'Итоговая оцениваемая сумма',
    'LBL_COPY' => 'Копировать цены',
    'LBL_COMMIT_AMOUNT' => 'Сумма по запланированной стоимости',
    'LBL_CUMULATIVE_TOTAL' => 'Общая сумма',
    'LBL_COPY_FROM' => 'Копировать цену/стоимость из:',

    'LBL_CHART_TITLE' => 'Объемы продаж Запланированные Фактические',

    'LBL_FORECAST' => 'Прогноз',
    'LBL_COMMITMENT' => 'Подтверждение',
    'LBL_TEAM_COMMITMENT' => 'Подтверждение команды',
    'LBL_FORECASTED' => 'Спрогнозировано',
    'LBL_OPPORTUNITY_FORECAST' => 'Прогноз по сделке',
    'LBL_REVENUELINEITEM_FORECAST' => 'Прогноз дохода по продукту',
    'LBL_LAST_COMMITMENT' => 'Последнее подтверждение',
    'LBL_ADJUSTED_TOTAL' => 'Скорректированный общий',
    'LBL_COMMIT_STAGE' => 'Стадия совершения продажи',
    'LBL_SALES_STAGE' => 'Стадия',
    'LBL_AMOUNT' => 'Сумма',
    'LBL_PERCENT' => 'Проценты',
    'LBL_DATE_CLOSED' => 'Ожидаемая дата закрытия:',
    'LBL_PRODUCT_ID' => 'ID продукта',
    'LBL_QUOTA_ID' => 'ID квоты',
    'LBL_VERSION' => 'Версия',
    'LBL_CHART_BAR_LEGEND_CLOSE' => 'Скрыть панель условных обозначений',
    'LBL_CHART_BAR_LEGEND_OPEN' => 'Показать панель условных обозначений',
    'LBL_CHART_LINE_LEGEND_CLOSE' => 'Скрыть строку условных обозначений',
    'LBL_CHART_LINE_LEGEND_OPEN' => 'Показать строку условных обозначений',

    //Labels for forecasting history log and endpoint
    'LBL_ERROR_NOT_MANAGER' => 'Ошибка: у пользователя (0) нет доступа для запроса прогнозов для (1)',
    'LBL_UP' => 'веерх',
    'LBL_DOWN' => 'вниз',
    'LBL_PREVIOUS_COMMIT' => 'Последняя дата, когда можно совершить продажу',

    'LBL_COMMITTED_HISTORY_SETUP_FORECAST' => 'план/настройка прогноза',
    'LBL_COMMITTED_HISTORY_UPDATED_FORECAST' => 'Обновленный прогноз',
    'LBL_COMMITTED_HISTORY_1_SHOWN' => '{{{intro}}} {{{first}}}',
    'LBL_COMMITTED_HISTORY_2_SHOWN' => '{{{intro}}} {{{first}}}, {{{second}}}',
    'LBL_COMMITTED_HISTORY_3_SHOWN' => '{{{intro}}} {{{first}}}, {{{second}}}, и {{{third}}}',
    'LBL_COMMITTED_HISTORY_LIKELY_CHANGED' => 'Подтвержденный (вероятный) {{{direction}}} с {{{from}}} до {{{to}}}',
    'LBL_COMMITTED_HISTORY_BEST_CHANGED' => 'Подтвержденный (оптимистичный) {{{direction}}} с {{{from}}} до {{{to}}}',
    'LBL_COMMITTED_HISTORY_WORST_CHANGED' => 'Подтвержденный (пессимистичный) {{{direction}}} с {{{from}}} до {{{to}}}',
    'LBL_COMMITTED_HISTORY_LIKELY_SAME' => 'Подтвержденный (вероятный) остался прежним',
    'LBL_COMMITTED_HISTORY_BEST_SAME' => 'Подтвержденный (оптимистичный) остался прежним',
    'LBL_COMMITTED_HISTORY_WORST_SAME' => 'Подтвержденный (пессимистичный) остался прежним',


    'LBL_COMMITTED_THIS_MONTH' => 'В этом месяце на {0}',
    'LBL_COMMITTED_MONTHS_AGO' => '{0} месяцев назад на {1}',

    //Labels for jsTree implementation
    'LBL_TREE_PARENT' => 'Родительский элемент/источник/исходный элемент',

    // Label for Current User Rep Worksheet Line
    // &#x200E; tells the browser to interpret as left-to-right
    'LBL_MY_MANAGER_LINE' => '{0} (me)',

    //Labels for worksheet items
    'LBL_EXPECTED_OPPORTUNITIES' => 'Ожидаемые сделки',
    'LBL_DISPLAYED_TOTAL' => 'Отображена общая сумма',
    'LBL_TOTAL' => 'Итого',
    'LBL_OVERALL_TOTAL' => 'Всего',
    'LBL_EDITABLE_INVALID' => 'Неправильное значение для {{field_name}}',
    'LBL_EDITABLE_INVALID_RANGE' => 'Значение должно быть между {0} и {1}',
    'LBL_WORKSHEET_SAVE_CONFIRM_UNLOAD' => 'вы не сохранили изменения в Вашем листе прогнозов',
    'LBL_WORKSHEET_EXPORT_CONFIRM' => 'Заметьте, что только сохраненные и применимые данные могут быть экспортированы. Нажмите Ок, чтобы продолжить экпортирование, или нажмите Отклонить, чтобы вернуться к листу',
    'LBL_WORKSHEET_ID' => 'ID листа',

    // Labels for Chart Options
    'LBL_DATA_SET' => 'Набор данных:',
    'LBL_GROUP_BY' => 'Группировать по',
    'LBL_CHART_OPTIONS' => 'Дополнительные настройки диаграммы',
    'LBL_CHART_AMOUNT' => 'Сумма',
    'LBL_CHART_TYPE' => 'Тип',

    // Labels for Data Filters
    'LBL_FILTERS' => 'Фильтры',

    // Labels for toggle buttons
    'LBL_MORE' => 'Больше',
    'LBL_LESS' => 'Меньше',

    // Labels for Progress
    'LBL_PROJECTED' => 'Задано',
    'LBL_DISTANCE_ABOVE_LIKELY_FROM_QUOTA' => 'Вероятный объем продаж свыше заданного',
    'LBL_DISTANCE_LEFT_LIKELY_TO_QUOTA' => 'Вероятный объем продаж ниже заданного',
    'LBL_DISTANCE_ABOVE_BEST_FROM_QUOTA' => 'Оптимистичный объем продаж выше заданного',
    'LBL_DISTANCE_LEFT_BEST_TO_QUOTA' => 'Оптимистичный объем продаж ниже заданного',
    'LBL_DISTANCE_ABOVE_WORST_FROM_QUOTA' => 'Пессимистичный объем продаж выше заданного',
    'LBL_DISTANCE_LEFT_WORST_TO_QUOTA' => 'Пессимистичный объем продаж ниже заданного',
    'LBL_CLOSED' => 'Закрытая состоявшаяся продажа',
    'LBL_DISTANCE_ABOVE_LIKELY_FROM_CLOSED' => 'Вероятность выше закрытой состоявшейся продажи',
    'LBL_DISTANCE_LEFT_LIKELY_TO_CLOSED' => 'Вероятность ниже закрытой состоявшейся продажи',
    'LBL_DISTANCE_ABOVE_BEST_FROM_CLOSED' => 'Оптимистично выше закрытой состоявшейся продажи',
    'LBL_DISTANCE_LEFT_BEST_TO_CLOSED' => 'Оптимистично ниже закрытой состоявшейся продажи',
    'LBL_DISTANCE_ABOVE_WORST_FROM_CLOSED' => 'Пессимистично выше закрытой состоявшейся продажи',
    'LBL_DISTANCE_LEFT_WORST_TO_CLOSED' => 'Пессимистично ниже закрытой состоявшейся продажи',
    'LBL_REVENUE' => 'Доход',
    'LBL_PIPELINE_REVENUE' => 'Доход по воронке продаж',
    'LBL_PIPELINE_OPPORTUNITIES' => 'Продажи в воронке',
    'LBL_LOADING' => 'Загрузка...',
    'LBL_IN_FORECAST' => 'Прогноз',

    // Actions Dropdown
    'LBL_ACTIONS' => 'Действия',
    'LBL_EXPORT_CSV' => 'Экспорт CSV',
    'LBL_CANCEL' => 'Отмена',

    'LBL_CHART_FORECAST_FOR' => 'Для  {0}',
    'LBL_FORECAST_TITLE' => 'Прогноз: {0}',
    'LBL_CHART_INCLUDED' => 'Включено',
    'LBL_CHART_NOT_INCLUDED' => 'Не включено',
    'LBL_CHART_ADJUSTED' => 'Установлено',
    'LBL_SAVE_DRAFT' => 'Сохранить',
    'LBL_CHANGES_BY' => 'изменяется на {0}',
    'LBL_FORECAST_SETTINGS' => 'Настройки прогнозирования',

    // config panels strings
    'LBL_FORECASTS_CONFIG_TITLE' => 'Настройки прогнозирования',

    'LBL_FORECASTS_MISSING_STAGE_TITLE' => 'Ошибка в настройке прогнозирования',
    'LBL_FORECASTS_MISSING_SALES_STAGE_VALUES' => 'Модуль прогнозов был некорректно настроен и поэтому недоступен. Стадии продаж: Продажа состоялась и Продажа не состоялась недоступны. Пожалуйста, обратитесь к Вашему администратору',
    'LBL_FORECASTS_ACLS_NO_ACCESS_TITLE' => 'Ошибка доступа к модулю прогнозов',
    'LBL_FORECASTS_ACLS_NO_ACCESS_MSG' => 'У Вас нет доступа к модулю прогнозов. Пожалуйста, обратитесь к Вашему администратору',

    'LBL_FORECASTS_RECORDS_ACLS_NO_ACCESS_MSG' => 'You do not have access to the Forecasts module&#39;s records. Please contact your Administrator.',

    // Panel and BreadCrumb Labels
    'LBL_FORECASTS_CONFIG_BREADCRUMB_WORKSHEET_LAYOUT' => 'Внешний вид листа прогнозов',
    'LBL_FORECASTS_CONFIG_BREADCRUMB_RANGES' => 'Диапазоны',
    'LBL_FORECASTS_CONFIG_BREADCRUMB_SCENARIOS' => 'Сценарии',
    'LBL_FORECASTS_CONFIG_BREADCRUMB_TIMEPERIODS' => 'Промежутки времени',
    'LBL_FORECASTS_CONFIG_BREADCRUMB_VARIABLES' => 'Переменные величины',

    // Admin UI
    'LBL_FORECASTS_CONFIG_TITLE_FORECAST_SETTINGS' => 'Настройки прогнозирования',
    'LBL_FORECASTS_CONFIG_TITLE_TIMEPERIODS' => 'Временной диапазон',
    'LBL_FORECASTS_CONFIG_TITLE_RANGES' => 'Диапазоны прогноза',
    'LBL_FORECASTS_CONFIG_TITLE_SCENARIOS' => 'Сценарии',
    'LBL_FORECASTS_CONFIG_TITLE_WORKSHEET_COLUMNS' => 'Колонки листа прогнозов',
    'LBL_FORECASTS_CONFIG_TITLE_FORECAST_BY' => 'Просмотр листа прогнозов по',

    'LBL_FORECASTS_CONFIG_HOWTO_TITLE_FORECAST_BY' => 'Прогноз по',

    'LBL_FORECASTS_CONFIG_TITLE_MESSAGE_TIMEPERIODS' => 'Дата начала финансового года',

    'LBL_FORECASTS_CONFIG_HELP_TIMEPERIODS' => 'Настройте временной диапазон, который будет использоваться в модуле Прогнозов. Начните с выбора Даты начала Вашего финансового года. Затем выберите тип временного диапазона, в рамках которого хотите произвести прогноз. Диапазон дат для временного диапазона автоматически высчитается на основании произведенного выбора. Доп. временной диапазон используется в качестве основы для работы с листом прогнозов. Возможность просмотра будущего и прошедшего временного диапазона определит количество видимых доп. диапазонов в модуле Прогнозов. Пользователям будет доступен просмотр и редактирование данных прогноза в рамках видимого временного диапазона',
    'LBL_FORECASTS_CONFIG_HELP_RANGES' => 'Настройте способ категоризации {{forecastByModule}}. <br><br>Обратите внимание, что настройки диапазона не могут быть изменены после первого подтверждения. Для обновленных экземпляров параметр «Диапазон» привязан к существующим данным прогноза.<br><br>Вы можете выбрать две или более категорий на основе диапазонов вероятности или создать категории, которые не основаны на вероятности. <br><br>Слева от ваших пользовательских категорий есть поля для установки флажков; используйте их, чтобы решить, какие диапазоны будут включены в сумму прогноза, подтвержденную и переданную в сообщении менеджерам. <br><br>Пользователь может изменить статус включения/исключения и категорию {{forecastByModule}} вручную со своего рабочего листа.',
    'LBL_FORECASTS_CONFIG_HELP_SCENARIOS' => 'Выберите колонки, которые потребуются пользователю для заполнения при составлении прогнозов {{forecastByModuleSingular}}. Заметьте, вероятная сумма привязана к сумме, показанной в {{forecastByModule}}; по этой причине колонка со значением вероятной суммы не может быть скрыта.',
    'LBL_FORECASTS_CONFIG_HELP_WORKSHEET_COLUMNS' => 'Выберите колонки для просмотра в модуле прогнозов. Перечень полей будет содержать лист прогнозов и позволит пользователю выбрать то, каким образом настроить его вид/просмотр.',
    'LBL_FORECASTS_CONFIG_HELP_FORECAST_BY' => 'Указатель места заполнения в модуле Прогнозов для текста поиска справки',
    'LBL_FORECASTS_CONFIG_SETTINGS_SAVED' => 'Настройки модуля прогнозов были сохранены',

    // timeperiod config
    //TODO-sfa remove this once the ability to map buckets when they get changed is implemented (SFA-215).
    'LBL_FORECASTS_CONFIG_TIMEPERIOD_SETUP_NOTICE' => 'Настройки времени не могут быть изменены после их первичной настройки',
    'LBL_FORECASTS_CONFIG_TIMEPERIOD_DESC' => 'Настраивайте временной диапазон, используемый для модуля прогнозов',
    'LBL_FORECASTS_CONFIG_TIMEPERIOD_TYPE' => 'Выберите тип года, который использует Ваша организация для отчетности',
    'LBL_FORECASTS_CONFIG_TIMEPERIOD' => 'Выберите тип временного диапазона',
    'LBL_FORECASTS_CONFIG_LEAFPERIOD' => 'Выберите доп. временной период, который Вы хотите просмотреть в рамках временного диапазона',
    'LBL_FORECASTS_CONFIG_START_DATE' => 'Выберите дату начала финансового года',
    'LBL_FORECASTS_CONFIG_TIMEPERIODS_FORWARD' => 'Выберите количество будущих временных периодов для просмотра в листе прогнозов.<br><i> Это количество будет применено к основному выбранному временному диапазону. К примеру, выбор 2 годовых временных периодов выведет 8 будущих кварталов</i>',
    'LBL_FORECASTS_CONFIG_TIMEPERIODS_BACKWARD' => 'Выберите количество прошедших временных периодов для просмотра в листе прогнозов.<br><i>Это количество будет применено к основному выбранному временному диапазону. К примеру, выбор 2 квартальных временных периодов выведет 6 прошедших месяцев.</i>',
    'LBL_FORECASTS_CONFIG_TIMEPERIOD_FISCAL_YEAR' => 'Выбранная дата начала указывает, что финансовый год может охватывать два года. Выберите, пожалуйста, какой год использовать в качестве финансового года:',
    'LBL_FISCAL_YEAR' => 'Финансовый год',

    // worksheet layout config
    'LBL_FORECASTS_CONFIG_GENERAL_FORECAST_BY_TEXT' => 'Выберите, каким образом Вы хотите заполнить лист прогнозов',
    'LBL_FORECASTS_CONFIG_GENERAL_FORECAST_BY_OPPORTUNITIES' => 'Продажи',
    'LBL_FORECASTS_CONFIG_GENERAL_FORECAST_BY_PRODUCT_LINE_ITEMS' => 'Доход по продуктам',
    'LBL_REVENUELINEITEM_NAME' => 'Наименование',
    'LBL_FORECASTS_CONFIG_WORKSHEET_LAYOUT_DETAIL_MESSAGE' => 'Листы будут заполнены:',

    // ranges config
    //TODO-sfa remove this once the ability to map buckets when they get changed is implemented (SFA-215).
    'LBL_FORECASTS_CONFIG_RANGES_SETUP_NOTICE' => 'Диапазон настроек не может быть изменен после сохранения черновика или их применения в модуле прогнозов. При переходе системы на новую версию, тем не менее, диапазон настроек не может быть изменен после первой настройки, поскольку данные по прогнозу уже доступы в новой версии',
    'LBL_FORECASTS_CONFIG_RANGES' => 'Опции диапазона прогнозов:',
    'LBL_FORECASTS_CONFIG_RANGES_OPTIONS' => 'Выберите, каким образом Вы хотите распределить прогнозы по категориям {{forecastByModule}}',
    'LBL_FORECASTS_CONFIG_SHOW_BINARY_RANGES_DESCRIPTION' => 'Эта опция дает пользователю возможность уточнить {{forecastByModule}}, что будет включено или исключено из листа прогнозов',
    'LBL_FORECASTS_CONFIG_SHOW_BUCKETS_RANGES_DESCRIPTION' => 'Эта опция дает пользователю возможность распределить по категориям {{forecastByModule}} то, что потенциально может быть совершено, если все пойдет успешно, но не было внесено в прогноз, {{forecastByModule}} и то, что следует исключить из прогноза.',
    'LBL_FORECASTS_CONFIG_SHOW_CUSTOM_BUCKETS_RANGES_DESCRIPTION' => 'Кастомизированные диапазоны: эта опция позволяет пользователю распределить продажи по следующим категориям {{forecastByModule}} : включено в прогноз с возможностью закрытия/совершения, исключено из прогноза, или любой другой диапазон, который Вы настроили',
    'LBL_FORECASTS_CONFIG_RANGES_EXCLUDE_INFO' => 'В диапазон "Исключено" относятся продажи с вероятностью от 0% до минимальной вероятности предыдущего прогноза, установленной по умолчанию',

    'LBL_FORECASTS_CONFIG_RANGES_ENTER_RANGE' => 'Введите название диапазона...',

    // scenarios config
    //TODO-sfa refactors the code references for scenarios to be scenarios (SFA-337).
    'LBL_FORECASTS_CONFIG_WORKSHEET_SCENARIOS' => 'Выберите сценарий, который необходимо включить в лист прогноза',
    'LBL_FORECASTS_CONFIG_WORKSHEET_LIKELY_INFO' => 'Вероятный сценарий высчитывается на основании суммы, внесенной в модуле {{forecastByModule}}',
    'LBL_FORECASTS_CONFIG_WORKSHEET_SCENARIOS_LIKELY' => 'Вероятность',
    'LBL_FORECASTS_CONFIG_WORKSHEET_SCENARIOS_BEST' => 'Оптимистичный сценарий',
    'LBL_FORECASTS_CONFIG_WORKSHEET_SCENARIOS_WORST' => 'Пессимистичный сценарий',
    'LBL_FORECASTS_CONFIG_PROJECTED_SCENARIOS' => 'Вывести установленный сценарий в совокупности',
    'LBL_FORECASTS_CONFIG_PROJECTED_SCENARIOS_LIKELY' => 'Вывести итоговую сумму вероятного сценария',
    'LBL_FORECASTS_CONFIG_PROJECTED_SCENARIOS_BEST' => 'Вывести итоговую сумму оптимистичного сценария',
    'LBL_FORECASTS_CONFIG_PROJECTED_SCENARIOS_WORST' => 'Вывести итоговую сумму пессимистичного сценария',

    // variables config
    'LBL_FORECASTS_CONFIG_VARIABLES' => 'Переменные величины',
    'LBL_FORECASTS_CONFIG_VARIABLES_DESC' => 'Формулы для таблицы исходных величин составляются на основании стадии продаж для {{forecastByModule}}, которые должны быть исключены из воронки продаж, то есть, {{forecastByModule}}, которые закрыты либо же потеряны',
    'LBL_FORECASTS_CONFIG_VARIABLES_CLOSED_LOST_STAGE' => 'Выберите стадию продажи, которая отображает закрытые и потерянные продажи {{forecastByModule}}:',
    'LBL_FORECASTS_CONFIG_VARIABLES_CLOSED_WON_STAGE' => 'Выберите стадию продажи, которая отображает закрытую и реализованную продажу {{forecastByModule}}',
    'LBL_FORECASTS_CONFIG_VARIABLES_FORMULA_DESC' => 'Таким образом формула воронки продаж будет следующая:',

    'LBL_FORECASTS_WIZARD_SUCCESS_TITLE' => 'Полезность/Успех/Благоприятный исход:',
    'LBL_FORECASTS_WIZARD_SUCCESS_MESSAGE' => 'Вы успешно настроили модуль прогнозирования. Подождите, пока модуль загрузится',
    'LBL_FORECASTS_TABBED_CONFIG_SUCCESS_MESSAGE' => 'Настройки сохранены. Подождите, пока модуль перезагрузится',
    // Labels for Success Messages:
    'LBL_FORECASTS_WORKSHEET_SAVE_DRAFT_SUCCESS' => 'Вы сохранили лист прогнозов как черновик для выбранного временного диапазона',
    'LBL_FORECASTS_WORKSHEET_COMMIT_SUCCESS' => 'Вы применили лист прогноза для выбранного временного диапазона',
    'LBL_FORECASTS_WORKSHEET_COMMIT_SUCCESS_TO' => 'Вы закрепили прогноз за {{manager}}',

    // custom ranges
    'LBL_FORECASTS_CUSTOM_RANGES_DEFAULT_NAME' => 'Кастомизированный диапазон',
    'LBL_UNAUTH_FORECASTS' => 'Несанкционированный доступ к настройкам модуля прогнозов',
    'LBL_FORECASTS_RANGES_BASED_TITLE' => 'Диапазоны на основании вероятностей',
    'LBL_FORECASTS_CUSTOM_BASED_TITLE' => 'Кастомизированные диапазоны на основании вероятностей',
    'LBL_FORECASTS_CUSTOM_NO_BASED_TITLE' => 'Диапазоны не основаны на вероятностях',

    // worksheet columns config
    'LBL_DISCOUNT' => 'Скидка',
    'LBL_OPPORTUNITY_STATUS' => 'Статус сделки',
    'LBL_OPPORTUNITY_NAME' => 'Название сделки',
    'LBL_PRODUCT_TEMPLATE' => 'Каталог продуктов',
    'LBL_CAMPAIGN' => 'Кампания',
    'LBL_TEAMS' => 'Команды',
    'LBL_CATEGORY' => 'Категория',
    'LBL_COST_PRICE' => 'Себестоимость',
    'LBL_TOTAL_DISCOUNT_AMOUNT' => 'Итоговая сумма по скидке',
    'LBL_FORECASTS_CONFIG_WORKSHEET_TEXT' => 'Выберите колонки для отображения при просмотре листа прогноза. По умолчанию, будут выбраны следующие поля:',

    // forecast details dashlet
    'LBL_DASHLET_FORECAST_NOT_SETUP' => 'Модуль прогнозов не был настроен. Для корректного использования виджета необходимо его установить. Обратитесь к системному администратору.',
    'LBL_DASHLET_FORECAST_NOT_SETUP_ADMIN' => 'Модуль прогнозов не был настроен. Для корректного использования виджета необходимо его установить.',
    'LBL_DASHLET_FORECAST_CONFIG_LINK_TEXT' => 'Нажмите здесь для настройки модуля прогнозов.',
    'LBL_DASHLET_MY_PIPELINE' => 'Моя воронка продаж',
    'LBL_DASHLET_MY_TEAMS_PIPELINE' => "Воронка продаж моей команды",
    'LBL_DASHLET_PIPELINE_CHART_NAME' => 'Диаграмма воронки прогнозов',
    'LBL_DASHLET_PIPELINE_CHART_DESC' => 'Отображает текущую диаграмму воронки',
    'LBL_FORECAST_DETAILS_DEFICIT' => 'Дефицит/недочет',
    'LBL_FORECAST_DETAILS_SURPLUS' => 'Остаток',
    'LBL_FORECAST_DETAILS_SHORT' => 'Недостача',
    'LBL_FORECAST_DETAILS_EXCEED' => 'Превышение',
    'LBL_FORECAST_DETAILS_NO_DATA' => 'Нет данных',
    'LBL_FORECAST_DETAILS_MEETING_QUOTA' => 'Достичь объемов продаж',
    'LBL_OPEN_LIKELY' => 'Открыть вероятный',
    'LBL_OPEN_BEST' => 'Открыть оптимистичный',
    'LBL_OPEN_WORST' => 'Открыть пессимистичный',

    // Pipeline Metrics dashlet
    'LBL_PIPELINE_METRICS_DASHLET_NAME' => 'Показатели воронки продаж',
    'LBL_PIPELINE_METRICS_DASHLET_DESC' => 'Отобразить сведения о показателях воронки продаж',
    'LBL_PIPELINE_METRICS_DASHLET_METRICS_GUIDE' => 'Руководство по показателям воронки продаж',
    'LBL_PIPELINE_METRICS_DASHLET_CONFIG_METRICS' => 'Отобразить показатели',
    'LBL_PIPELINE_METRICS_DASHLET_CONFIG_METRICS_GUIDE' => 'Руководство по показателям',
    'LBL_PIPELINE_METRICS_DASHLET_CONFIG_METRICS_REQUIRED' => 'Необходимо выбрать минимум один показатель для отображения на вкладках показателей',

    'LBL_ASSIGN_QUOTA_BUTTON' => 'Установить объем продаж',
    'LBL_ASSIGNING_QUOTA' => 'Принять установленный объем продаж',
    'LBL_QUOTA_ASSIGNED' => 'Объемы продаж были успешно установлены',
    'LBL_FORECASTS_NO_ACCESS_TO_CFG_TITLE' => 'Ошибка доступа к модулю прогнозов',
    'LBL_FORECASTS_NO_ACCESS_TO_CFG_MSG' => 'У Вас нет доступа к настройке модуля прогнозов. Обратитесь к Вашему администратору',
    'WARNING_DELETED_RECORD_RECOMMIT_1' => 'This record was included in a ',
    'WARNING_DELETED_RECORD_RECOMMIT_2' => 'It will be removed and you will need to re-commit your ',

    'LBL_DASHLET_MY_FORECAST' => 'Мои прогнозы',
    'LBL_DASHLET_MY_TEAMS_FORECAST' => "Прогнозы моей команды",

    'LBL_WARN_UNSAVED_CHANGES_CONFIRM_SORT' => 'У Вас есть не сохраненные изменения. Вы точно хотите произвести сортировку рабочего листа и отменить внесенные изменения?',

    'LBL_SAVE_TOOLTIP' => 'Сохранить изменения строк листа',
    'LBL_COMMIT_TOOLTIP_REP' => 'Сохранить изменения в записях и отправить прогноз руководителю',
    'LBL_COMMIT_TOOLTIP_MGR' => 'Отправить прогноз руководителю и назначить объем продаж',

    'LBL_SAVE_LABEL_REP' => 'Сохранить ',
    'LBL_SAVE_LABEL_MGR' => 'Сохранить скорректированные значения',

    // Forecasts Records View Help Text
    'LBL_HELP_RECORDS' => 'Модуль {{plural_module_name}} включает записи {{forecastby_singular_module}} для построения {{forecastworksheets_module}} и прогнозирования продаж. Пользователи могут работать в направлении достижения {{quotas_module}} продажи на индивидуальном, командном и организационном уровнях продаж. Для получения доступа к модулю {{plural_module_name}}, пользователь с правами администратора должен выбрать требуемые организацей периоды времени, диапазоны и сценарии. 

Торговые представители с помощью модуля {{plural_module_name}} могут работать с присвоенными им {{forecastby_module}} как с прогрессами текущего периода. Эти пользователи будут прогнозировать свои личные продажи, основанные на {{forecastby_module}}, которые они хотят закрыть. Менеджеры по продажам работают со своими собственными записями {{forecastby_singular_module}}, как и другие торговые представители. Кроме того, они составляют отчеты по проведенным сделкам, чтобы прогнозировать продажи их команды и работать в направлении выполнения квоты команды для каждого периода времени. Дополнительные функции предлагаются различными элементами расширенной панели, включая индивидуальный анализ листов и анализ листов команды менеджера.',
];
