{*
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
*}

{if !$error}
    <script type="text/javascript">
        SUGAR.util.doWhen(
            "((SUGAR && SUGAR.mySugar && SUGAR.mySugar.sugarCharts)   || (SUGAR.loadChart && typeof loadSugarChart == 'function')  || document.getElementById('showHideChartButton') != null) && typeof(loadSugarChart) != undefined",
            function() {
                let css = [];
                let chartConfig = {ldelim}{rdelim};
                let chartParams = {ldelim}{rdelim};

                {foreach from=$config key=name item=value}
                    chartConfig['{$name}'] = '{$value}';
                {/foreach}

                {foreach from=$params key=name item=value}
                    chartParams['{$name}'] = '{$value}';
                {/foreach}

                chartConfig['reportView'] = true;

                {ldelim}
                if (chartConfig['chartType'] === 'barChart' && chartConfig['barType'] === 'grouped') {
                     chartConfig['stacked'] = false;
                }
                {rdelim}

                chartConfig['direction'] = $('html', window.parent.document).hasClass('rtl') ? 'rtl' : 'ltr';

                loadCustomChartForReports = function() {ldelim}
                    if (chartConfig.maintainAspectRatio === "1") {
                        const chartWrapperEl = $('.chartWrapper');
                        const marginOffset = 0.1;
                        const ratio = chartConfig.aspectRatio - marginOffset;
                        chartWrapperEl.css({
                            height: '',
                            width: '',
                        });
                        const size = {
                            width: chartWrapperEl.innerWidth(),
                            height: chartWrapperEl.innerHeight(),
                        };
                        if (size.height * ratio < size.width) {
                            const width = size.height * ratio;
                            chartWrapperEl.css({
                                width: width,
                            });
                        }
                    }

                    loadSugarChart('{$chartId}', '{$filename}', css, chartConfig, chartParams, chart => {
                        if (!chart) {
                            showNoData();
                        } else if (chart.wrapperProperties) {
                            updateChartWrapperCss(chart.wrapperProperties);
                        }
                    });
                {rdelim};

                // bug51857: fixed issue on report running in a loop when clicking on hide chart then run report in IE8 only
                // When hide chart button is clicked, the value of element showHideChartButton is set to $showchart.
                // Don't need to call the loadCustomChartForReports() function when hiding the chart.
                {if !isset($showchart)}
                    loadCustomChartForReports();
                {else}
                    if ($('#showHideChartButton').attr('value') !== '{$showchart}') {ldelim}
                        loadCustomChartForReports();
                    {rdelim}
                {/if}
            }
        );

        /**
         * Shows a "No data" message instead for charts that have no data
         */
        function showNoData() {
            let reportChartContainer = document.getElementsByClassName('reportChartContainer') || [];
            for (let container of reportChartContainer) {
                container.classList.add('noData');
            }
        }

        /**
         * Given a set of key/value CSS properties, applies them to the wrapper
         * element around the chart canvas
         *
         * @param properties the key/value CSS property pairs to set
         */
        function updateChartWrapperCss(properties) {
            let chartWrapper = document.getElementById(`chart_{$chartId}_wrapper`);
            if (chartWrapper) {
                Object.assign(chartWrapper.style, properties);
            }
        }
    </script>
    <div id="chart_{$chartId}_container" class="chartContainer">
        <div id="chart_{$chartId}_wrapper" class="chartWrapper" data-content="chart">
            <canvas id="chart_{$chartId}" class="sc-chart"></canvas>
            <div id="chart_{$chartId}_nodata" class="chartNoData">{{$noDataString}}</div>
        </div>
        <div class="sc-print sc-{$config.chartType}" style="width: 720px; height: 480px; top: 0;">
            <canvas id="chart_{$chartId}_print" class="sc-chart" width="720" height="480"></canvas>
        </div>
    </div>
    <div class="clear"></div>
{else}
    {$error}
{/if}
