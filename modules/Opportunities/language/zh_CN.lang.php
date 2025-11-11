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
    'LBL_OPPORTUNITIES_LIST_DASHBOARD' => '机会列表仪表板',
    'LBL_OPPORTUNITIES_RECORD_DASHBOARD' => '机会记录仪表板',
    'LBL_OPPORTUNITIES_MULTI_LINE_DASHBOARD' => '商业机会重点仪表板 - 控制台',
    'LBL_OPPORTUNITIES_FOCUS_DRAWER_DASHBOARD' => '商业机会焦点抽屉',
    'LBL_RENEWAL_OPPORTUNITY' => '续订商业机会',

    'LBL_MODULE_NAME' => '商业机会',
    'LBL_MODULE_NAME_SINGULAR' => '商业机会',
    'LBL_MODULE_TITLE' => '商业机会：首页',
    'LBL_SEARCH_FORM_TITLE' => '查找商业机会',
    'LBL_VIEW_FORM_TITLE' => '查看商业机会',
    'LBL_LIST_FORM_TITLE' => '商业机会列表',
    'LBL_OPPORTUNITY_NAME' => '商业机会名称：',
    'LBL_OPPORTUNITY' => '商业机会：',
    'LBL_NAME' => '商业机会名称',
    'LBL_TIME' => '时间',
    'LBL_INVITEE' => '联系人',
    'LBL_CURRENCIES' => '货币',
    'LBL_LIST_OPPORTUNITY_NAME' => '名称',
    'LBL_LIST_ACCOUNT_NAME' => '客户名称',
    'LBL_LIST_DATE_CLOSED' => '预期结束日期',
    'LBL_LIST_AMOUNT' => '可能',
    'LBL_LIST_AMOUNT_USDOLLAR' => '折算金额',
    'LBL_ACCOUNT_ID' => '帐户 ID',
    'LBL_CURRENCY_RATE' => '汇率',
    'LBL_CURRENCY_ID' => '货币 ID',
    'LBL_CURRENCY_NAME' => '货币名称',
    'LBL_CURRENCY_SYMBOL' => '货币符号',
//DON'T CONVERT THESE THEY ARE MAPPINGS
    'db_sales_stage' => 'LBL_LIST_SALES_STAGE',
    'db_name' => 'LBL_NAME',
    'db_amount' => 'LBL_LIST_AMOUNT',
    'db_date_closed' => 'LBL_LIST_DATE_CLOSED',
//END DON'T CONVERT
    'UPDATE' => '商业机会－货币更新',
    'UPDATE_DOLLARAMOUNTS' => '更新美元金额',
    'UPDATE_VERIFY' => '确认金额',
    'UPDATE_VERIFY_TXT' => '确认商业机会中的金额值为有效的十进制数字，仅由数字 (0-9) 和小数点 (.) 的组成',
    'UPDATE_FIX' => '修正金额',
    'UPDATE_FIX_TXT' => '尝试从目前的金额新增有效的小数点来修正任何错误的金额。原有的资料会备份到 amount_backup 数据库字段。如果您在执行过程中发现任何错误，记得在重新执行前先使用备份数值进行还原，避免备份数值被新增的无效数据覆盖。',
    'UPDATE_DOLLARAMOUNTS_TXT' => '按现用汇率更新商业机会的美元金额。这个数值用来计算图表与货币金额显示列表。',
    'UPDATE_CREATE_CURRENCY' => '新增货币：',
    'UPDATE_VERIFY_FAIL' => '记录验证失败：',
    'UPDATE_VERIFY_CURAMOUNT' => '目前金额：',
    'UPDATE_VERIFY_FIX' => '执行修正将会变成',
    'UPDATE_INCLUDE_CLOSE' => '包含关闭的记录',
    'UPDATE_VERIFY_NEWAMOUNT' => '新的金额：',
    'UPDATE_VERIFY_NEWCURRENCY' => '新的货币：',
    'UPDATE_DONE' => '完成',
    'UPDATE_BUG_COUNT' => '发现缺陷并且尝试解决：',
    'UPDATE_BUGFOUND_COUNT' => '发现的缺陷：',
    'UPDATE_COUNT' => '更新的记录：',
    'UPDATE_RESTORE_COUNT' => '还原的记录金额：',
    'UPDATE_RESTORE' => '还原金额',
    'UPDATE_RESTORE_TXT' => '通过修正期间新增的备份来还原金额数值。',
    'UPDATE_FAIL' => '无法更新-',
    'UPDATE_NULL_VALUE' => '没有输入金额的项目会设置为 0-',
    'UPDATE_MERGE' => '合并货币',
    'UPDATE_MERGE_TXT' => '将多种货币合并成单一货币。如果您发现同样的货币有多条记录，您可以将他们合并。这将会合并所有其他模块的货币。',
    'LBL_ACCOUNT_NAME' => '客户名称：',
    'LBL_CURRENCY' => '货币：',
    'LBL_DATE_CLOSED' => '预期完成日期：',
    'LBL_DATE_CLOSED_TIMESTAMP' => '预期截止日期时间戳',
    'LBL_TYPE' => '类型：',
    'LBL_CAMPAIGN' => '市场活动：',
    'LBL_NEXT_STEP' => '下一步：',
    'LBL_SERVICE_START_DATE' => '服务开始日期',
    'LBL_LEAD_SOURCE' => '潜在客户来源',
    'LBL_SALES_STAGE' => '销售阶段',
    'LBL_SALES_STATUS' => '状态',
    'LBL_PROBABILITY' => '成交概率 (%)：',
    'LBL_DESCRIPTION' => '说明',
    'LBL_DUPLICATE' => '可能重复的商业机会',
    'MSG_DUPLICATE' => '新增这条记录可能会重复现有商业机会记录。以下列出了包含相同名称的商业机会记录。<br>单击“保存”以继续创建此新商业机会或点击“取消”返回模块，不创建商业机会。',
    'LBL_NEW_FORM_TITLE' => '创建商业机会',
    'LNK_NEW_OPPORTUNITY' => '创建商业机会',
    'LNK_CREATE' => '新增交易',
    'LNK_OPPORTUNITY_LIST' => '查看商业机会',
    'ERR_DELETE_RECORD' => '必须指定记录编号才能删除商业机会。',
    'LBL_TOP_OPPORTUNITIES' => '我的重要未完成商业机会',
    'NTC_REMOVE_OPP_CONFIRMATION' => '您确定要从这个商业机会移除此联系人吗？',
    'OPPORTUNITY_REMOVE_PROJECT_CONFIRM' => '您确定要从项目中移除此商业机会？',
    'LBL_DEFAULT_SUBPANEL_TITLE' => '商业机会',
    'LBL_ACTIVITIES_SUBPANEL_TITLE' => '活动',
    'LBL_HISTORY_SUBPANEL_TITLE' => '历史记录',
    'LBL_RAW_AMOUNT' => '原始金额',
    'LBL_LEADS_SUBPANEL_TITLE' => '潜在客户',
    'LBL_CONTACTS_SUBPANEL_TITLE' => '联系人',
    'LBL_DOCUMENTS_SUBPANEL_TITLE' => '文件',
    'LBL_PROJECTS_SUBPANEL_TITLE' => '项目',
    'LBL_ASSIGNED_TO_NAME' => '负责人：',
    'LBL_LIST_ASSIGNED_TO_NAME' => '获指派负责的用户',
    'LBL_LIST_SALES_STAGE' => '销售阶段',
    'LBL_MY_CLOSED_OPPORTUNITIES' => '我的已完成商业机会',
    'LBL_TOTAL_OPPORTUNITIES' => '商业机会总数',
    'LBL_CLOSED_WON_OPPORTUNITIES' => '谈成结束的商业机会',
    'LBL_ASSIGNED_TO_ID' => '负责人：',
    'LBL_CREATED_ID' => '创建人 ID',
    'LBL_MODIFIED_ID' => '修改人 ID',
    'LBL_MODIFIED_NAME' => '修改人的用户名',
    'LBL_CREATED_USER' => '已创建的用户',
    'LBL_MODIFIED_USER' => '已修改的用户',
    'LBL_CAMPAIGN_OPPORTUNITY' => '市场活动机会',
    'LBL_PROJECT_SUBPANEL_TITLE' => '项目',
    'LABEL_PANEL_ASSIGNMENT' => '分配',
    'LNK_IMPORT_OPPORTUNITIES' => '导入商业机会',
    'LBL_EDITLAYOUT' => '编辑布局' /*for 508 compliance fix*/,
    //For export labels
    'LBL_EXPORT_CAMPAIGN_ID' => '市场活动 ID',
    'LBL_OPPORTUNITY_TYPE' => '商业机会类型',
    'LBL_EXPORT_ASSIGNED_USER_NAME' => '分配的用户名',
    'LBL_EXPORT_ASSIGNED_USER_ID' => '担当ID',
    'LBL_EXPORT_MODIFIED_USER_ID' => '修改人 ID',
    'LBL_EXPORT_CREATED_BY' => '创建人 ID',
    'LBL_EXPORT_NAME' => '名字',
    // SNIP
    'LBL_CONTACT_HISTORY_SUBPANEL_TITLE' => '相关联系人的电子邮件',
    'LBL_FILENAME' => '附件',
    'LBL_PRIMARY_QUOTE_ID' => '主要报价',
    'LBL_CONTRACTS' => '合同',
    'LBL_CONTRACTS_SUBPANEL_TITLE' => '合同',
    'LBL_PRODUCTS' => '已报价单项',
    'LBL_RLI' => '营收单项',
    'LNK_OPPORTUNITY_REPORTS' => '查看商业机会报表',
    'LBL_QUOTES_SUBPANEL_TITLE' => '报价',
    'LBL_TEAM_ID' => '团队编号',
    'LBL_TIMEPERIODS' => '时间周期',
    'LBL_TIMEPERIOD_ID' => '时段 ID',
    'LBL_COMMITTED' => '已提交',
    'LBL_FORECAST' => '预测包括',
    'LBL_COMMIT_STAGE' => '提交阶段',
    'LBL_COMMIT_STAGE_FORECAST' => '预测范围',
    'LBL_WORKSHEET' => '工作表',
    'LBL_PURCHASED_LINE_ITEMS' => '已购买单项',

    // KPI Metrics
    'LBL_ORGANIZE' => '组织',
    'LBL_CREATE_NEW' => '新建',
    'LBL_MANAGE' => '管理',
    'LBL_SEE_DETAILS' => '查看详细信息',
    'LBL_HIDE_NEW' => '隐藏',

    'LBL_FORECASTED_LIKELY' => '预测可能性',
    'LBL_LOST' => '丢单',
    'LBL_RENEWAL' => '续订',
    'LBL_RENEWAL_OPPORTUNITIES' => '续订商业机会',
    'LBL_RENEWAL_PARENT' => '父级商业机会',
    'LBL_PARENT_RENEWAL_OPPORTUNITY_ID' => '续订父 ID',
    'LBL_MONTH_YEAR_RENEWAL' => '{{month}}, {{year}}',

    'LBL_WIDGET_SALES_STAGE' => '销售阶段',
    'LBL_WIDGET_DATE_CLOSED' => '预期结束日期',
    'LBL_WIDGET_AMOUNT' => '金额',

    'TPL_RLI_CREATE' => '商业机会必须拥有关联的营收单项。',
    'TPL_RLI_CREATE_LINK_TEXT' => '新建营收单项。',
    'LBL_PRODUCTS_SUBPANEL_TITLE' => '已报价单项',
    'LBL_RLI_SUBPANEL_TITLE' => '营收单项',

    'LBL_TOTAL_RLIS' => '总营收单项的数量',
    'LBL_CLOSED_RLIS' => '已完成收入线项目的数量',
    'LBL_CLOSED_WON_RLIS' => '已结算获得的营收单项数额',
    'LBL_SERVICE_OPEN_FLEX_DURATION_RLIS' => '开放服务弹性持续时间营收单项 #',
    'NOTICE_NO_DELETE_CLOSED_RLIS' => '您不能删除包含已完成营收单项的商业机会',
    'WARNING_NO_DELETE_CLOSED_SELECTED' => '所选择的记录中有一个或多个包含已完成营收单项，因此不能删除。',
    'LBL_INCLUDED_RLIS' => '包含的营收单项数量',
    'LBL_UPDATE_OPPORTUNITIES_RLIS' => '更新打开',
    'LBL_CASCADE_RLI_EDIT' => '更新未结算收入单项',
    'LBL_CASCADE_RLI_CREATE' => '设置跨营收单项',
    'LBL_SERVICE_START_DATE_INVALID' => '任何开放附加营收单项的服务开始日期不能设置于服务结束日期之后。',

    'LBL_QUOTE_SUBPANEL_TITLE' => '报价',
    'LBL_FILTER_OPPORTUNITY_TEMPLATE' => '动态账户带来的商业机会',
    'LBL_TOP_10_OPP' => '前 10 个开放机会',
    'LBL_DASHLET_MY_ACTIVE_OPP' => 'Dashlet：我的活动机会',
    'LBL_MY_ACTIVE_OPP' => '我的活动机会',


    // Config
    'LBL_OPPS_CONFIG_VIEW_BY_LABEL' => '商业机会等级',
    'LBL_OPPS_CONFIG_VIEW_BY_DATE_ROLLUP' => '将所得商业机会记录上的预计结束日期字段设置为现有营收单项的最早或最后结束日期。',

    //Dashlet
    'LBL_PIPELINE_TOTAL_IS' => '管道总计',

    'LBL_OPPORTUNITY_ROLE' => '商业机会角色',
    'LBL_NOTES_SUBPANEL_TITLE' => '笔记',
    'LBL_TAB_OPPORTUNITY' => '{{module}} 评论',

    // Help Text
    'LBL_OPPS_CONFIG_ALERT' => '若单击确定，您将清除所有预测数据并更改您的商业机会视图。如果这不是您要的结果，请单击“取消”返回上一设置。',
    'LBL_OPPS_CONFIG_ALERT_TO_OPPS' =>
        '点击确认，您将清除所有的预测数据，并更改您的机会视图。 '
        . '此外，将禁用所有的流程定义，以及营收单项的目标模块。 '
        . '如果您不打算这样做，请点击取消，返回上一设置界面。',
    'LBL_OPPS_CONFIG_SALES_STAGE_1a' => '如果所有营收单项均已关闭且至少有一个谈成，',
    'LBL_OPPS_CONFIG_SALES_STAGE_1b' => '则商业机会销售阶段将设置为“谈成结束”。',
    'LBL_OPPS_CONFIG_SALES_STAGE_2a' => '如果所有营收单项均处于“丢单结束”销售阶段，',
    'LBL_OPPS_CONFIG_SALES_STAGE_2b' => '则该商业机会的销售阶段将设置为“丢单结束”。',
    'LBL_OPPS_CONFIG_SALES_STAGE_3a' => '如有任何营收单项尚未完成，',
    'LBL_OPPS_CONFIG_SALES_STAGE_3b' => '则该商业机会将标记为“最少进展销售阶段”。',

// BEGIN ENT/ULT

    // Opps Config - View By Opportunities
    'LBL_HELP_CONFIG_OPPS' => '在您第一次执行此变更之后，营收单项摘要备注将键入背景。备注完成并可用后，系统会发送通知至您保存在用户资料中的电子邮件地址。如果您的实例已经设置可用 {{forecasts_module}}，Sugar 也会在您的 {{module_name}} 记录同步到 {{forecasts_module}} 模块并可用于新 {{forecasts_module}} 后发送通知给您。请注意：您的实例必须通过“管理 > 电子邮件设置”配置为可发送电子邮件，才可能完成发送通知的动作。',

    // Opps Config - View By Opportunities And RLIs
    'LBL_HELP_CONFIG_RLIS' => '在您第一次执行此变更之后，系统会为每个现有的 {{module_name}} 在背景中创建营收单项记录。营收单项完成并可用后，系统会发送通知至您保存在用户资料中的电子邮件地址。请注意：您的实例必须通过“管理 > 电子邮件设置”配置为可发送电子邮件，才可能完成发送通知的动作。',
    // List View Help Text
    'LBL_HELP_RECORDS' => '{{plural_module_name}} 模块允许您从头到尾跟踪单笔销售。每条 {{module_name}} 记录代表一次预期销售，包括相关销售数据以及与其他重要记录相关的数据，例如 {{quotes_module}}, {{contacts_module}} 等。一个 {{module_name}} 通常会经过几个销售阶段，直到被标记为“已结束并赢得客户”或“已结束但客户流失”。 {{plural_module_name}} 可以通过使用 Sugar 的 {{forecasts_singular_module}}ing 模块，用于进一步了解和预测销售趋势，并集中精力实现销售配额。',

    // Record View Help Text
    'LBL_HELP_RECORD' => '{{plural_module_name}} 模块允许您从头到尾跟踪单次销售以及属于这些销售的明细项目。每条 {{module_name}} 记录代表一次预期销售，包括相关销售数据以及与其他重要记录相关的数据，例如 {{quotes_module}}, {{contacts_module}} 等。

- 通过单击单个字段或“编辑”按钮来编辑此记录的字段。
- 通过将左下窗格切换为“数据视图”来查看或修改子面板中其他记录的链接。
- 制作和查看用户评论，并在 {{activitystream_singular_module}} 通过将左下窗格切换为“活动流”。
- 使用记录名称右侧的图标关注或收藏此记录。
- 其他操作在“编辑”按钮右侧的“操作”下拉菜单中可用。',

    // Create View Help Text
    'LBL_HELP_CREATE' => '{{plural_module_name}} 模块允许您从头到尾跟踪单次销售以及属于这些销售的明细项目。每条 {{module_name}} 记录代表一次预期销售，包括相关销售数据以及与其他重要记录相关的数据，例如 {{quotes_module}}, {{contacts_module}} 等。

要创建 {{module_name}}：
1. 根据需要为字段提供值。
 - 标有“必填”的字段必须在保存前填写。
 - 如有必要，单击“显示更多”以显示其他字段。
2. 单击“保存”以完成新记录并返回上一页。',

// END ENT/ULT

    //Marketo
    'LBL_MKTO_SYNC' => '同步到 Marketo&reg;',
    'LBL_MKTO_ID' => 'Marketo 潜在客户编号',

    'LBL_DASHLET_TOP10_SALES_OPPORTUNITIES_NAME' => '前十个销售商业机会',
    'LBL_TOP10_OPPORTUNITIES_CHART_DESC' => '用气泡图显示前十个商业机会。',
    'LBL_TOP10_OPPORTUNITIES_MY_OPP' => '我的商业机会',
    'LBL_TOP10_OPPORTUNITIES_MY_TEAMS_OPP' => "我的团队的商业机会",

    'LBL_PIPELINE_ERR_CLOSED_SALES_STAGE' => '无法更改 {{fieldName}}，因为此 {{moduleSingular}} 没有开放的行项目。',
    'TPL_ACTIVITY_TIMELINE_DASHLET' => '商业机会时间线',

    'LBL_CASCADE_SERVICE_WARNING' => ' 不能设置这些营收单项中的任何一个，因为它们不是服务。你想继续创建吗？',
    'LBL_CASCADE_DURATION_WARNING' => ' 不能设置这些营收单项中的任何一个，因为它们的持续时间已锁定。你想继续创建吗？',

    // AI Predict
    'LBL_AI_OPPORTUNITY_CLOSE_PREDICTION_NAME' => '商业机会关闭预测',
    'LBL_AI_OPPORTUNITY_CLOSE_PREDICTION_DESC' => '查看特定商业机会的预测详细信息',
    'LBL_AI_WINRATE' => '胜率',
    'LBL_AI_WONOPP' => '赢得的商机',
    'LBL_AI_CLOSINGTIME' => '关门时间',
    'LBL_AI_CLOSEDOPP' => '失去的商机',
    'LBL_AI_LEADTIMESPAN' => '从创造商机到已结束并赢得客户之间的时间',
];
