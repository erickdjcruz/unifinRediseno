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
    'LBL_CASES_LIST_DASHBOARD' => '客户反馈列表仪表板',
    'LBL_CASES_RECORD_DASHBOARD' => '客户反馈记录仪表板',
    'LBL_CASES_MULTI_LINE_DASHBOARD' => '案例重点仪表板 - 控制台',
    'LBL_CASES_FOCUS_DRAWER_DASHBOARD' => '客户反馈焦点抽屉',
    'LBL_CLAIM_AND_CLOSE' => '案例操作按钮',
    'LBL_ACTION_CLAIM' => '操作要求',
    'LBL_ACTION_CLOSE' => '操作关闭',
    'LBL_ACTION_UNASSIGN' => '操作取消分配',
    'LBL_CLAIM_BUTTON_TEXT' => '要求',
    'LBL_CLOSE_BUTTON_TEXT' => '关闭',
    'LBL_UNASSIGN_BUTTON_TEXT' => '取消分配',

    'ERR_DELETE_RECORD' => '必须指定记录编号才能删除帐户。',
    'LBL_ACCOUNT_ID' => '帐户 ID',
    'LBL_ACCOUNT_NAME' => '客户名称:',
    'LBL_ACCOUNTS_SUBPANEL_TITLE' => '帐户',
    'LBL_ACTIVITIES_SUBPANEL_TITLE' => '活动',
    'LBL_ATTACH_NOTE' => '附加笔记',
    'LBL_BUGS_SUBPANEL_TITLE' => '缺陷',
    'LBL_CASE_NUMBER' => '客户编号:',
    'LBL_CASE_SUBJECT' => '客户反馈编号:',
    'LBL_CASE' => '客户反馈:',
    'LBL_CONTACT_CASE_TITLE' => '客户反馈联系人:',
    'LBL_CONTACT_NAME' => '联系人姓名：',
    'LBL_CONTACT_ROLE' => '角色：',
    'LBL_CONTACTS_SUBPANEL_TITLE' => '联系人',
    'LBL_DEFAULT_SUBPANEL_TITLE' => '客户反馈',
    'LBL_DESCRIPTION' => '说明:',
    'LBL_FILENANE_ATTACHMENT' => '附加文件',
    'LBL_HISTORY_SUBPANEL_TITLE' => '历史记录',
    'LBL_INVITEE' => '联系人',
    'LBL_MEMBER_OF' => '帐户',
    'LBL_MODULE_NAME' => '客户反馈',
    'LBL_MODULE_NAME_SINGULAR' => '客户反馈',
    'LBL_MODULE_TITLE' => '客户反馈：首页',
    'LBL_NEW_FORM_TITLE' => '新增客户反馈',
    'LBL_NUMBER' => '数量：',
    'LBL_PRIORITY' => '优先级：',
    'LBL_PROJECTS_SUBPANEL_TITLE' => '项目',
    'LBL_DOCUMENTS_SUBPANEL_TITLE' => '文档',
    'LBL_RESOLUTION' => '分析:',
    'LBL_SEARCH_FORM_TITLE' => '查找客户反馈',
    'LBL_STATUS' => '状态:',
    'LBL_SOURCE' => '来源:',
    'LBL_SUBJECT' => '主题:',
    'LBL_LIST_ACCOUNT_NAME' => '账户名称',
    'LBL_LIST_ASSIGNED' => '负责人',
    'LBL_LIST_CLOSE' => '关闭',
    'LBL_LIST_FORM_TITLE' => '客户反馈列表',
    'LBL_LIST_LAST_MODIFIED' => '最新修改',
    'LBL_LIST_MY_CASES' => '我的待处理的客户反馈',
    'LBL_LIST_NUMBER' => '编号',
    'LBL_LIST_PRIORITY' => '优先级',
    'LBL_LIST_STATUS' => '状态',
    'LBL_LIST_SUBJECT' => '主题',
    'LBL_LIST_ASSIGNED_TO_NAME' => '负责人',
    'LBL_SHOW_MORE' => '显示更多客户反馈',
    'LNK_CASE_LIST' => '查看客户反馈',
    'LNK_NEW_CASE' => '新增客户反馈',
    'LBL_CASE_FROM_TWITTER_TITLE' => '推特',
    'LNK_CREATE' => '创建客户反馈',
    'LNK_CREATE_WHEN_EMPTY' => '现在创建一条客户反馈',
    'NTC_REMOVE_FROM_BUG_CONFIRMATION' => '您确定要从此错误中移除这个客户反馈？',
    'NTC_REMOVE_INVITEE' => '您确定要从客户反馈中移除这个联系人？',
    'LBL_LIST_DATE_CREATED' => '创建日期',
    'LBL_ASSIGNED_TO_NAME' => '负责人',
    'LBL_TYPE' => '类型',
    'LBL_WORK_LOG' => '工作日志',
    'LNK_IMPORT_CASES' => '导入客户反馈',
    'LNK_CASE_REPORTS' => '查看客户反馈报表',
    'LBL_SHOW_IN_PORTAL' => '显示在门户中',
    'LBL_CREATE_KB_DOCUMENT' => '创建文章',
    'LBL_KBCONTENTS_SUBPANEL_TITLE' => '知识库',
    'LBL_REQUEST_CLOSE' => '请求关闭',
    'LBL_REQUEST_CLOSE_DATE' => '请求关闭日期',
    'LBL_REQUEST_CLOSE_LABEL' => '请求关闭',
    'LBL_REQUEST_CLOSE_TOOLTIP' => '发送请求关闭这个客户反馈',
    'LBL_REQUEST_CLOSE_MESSAGE' => '您是否要关闭这个客户反馈？',
    'LBL_DASHLET_REQUESTED_CLOSE_CASES' => '我已请求关闭的客户反馈',
    'LBL_MY_REQUESTED_CLOSE_CASES' => '我已请求关闭的客户反馈',
    'LBL_DASHLET_MY_REQUESTED_CLOSE_CASES_NAME' => '我已请求关闭的客户反馈',
    'LBL_DASHLET_REQUESTED_CLOSE_CASES_NAME' => '请求关闭的客户反馈列表',
    'LBL_DASHLET_REQUESTED_CLOSE_CASES_DESCRIPTION' => '显示请求关闭的已分配客户反馈列表',

    'LBL_CREATED_USER' => '已创建的用户',
    'LBL_MODIFIED_USER' => '已修改的用户',
    'LBL_PROJECT_SUBPANEL_TITLE' => '项目',
    'LBL_CASE_INFORMATION' => '概述',

    //For export labels
    'LBL_MODIFIED_BY_NAME_OWNER' => '由名字所有者修改',
    'LBL_MODIFIED_BY_NAME_MOD' => '由模组名称修改',
    'LBL_CREATED_BY_NAME_OWNER' => '由名字所有者创建',
    'LBL_CREATED_BY_NAME_MOD' => '由模组名称创建',
    'LBL_ASSIGNED_USER_NAME_OWNER' => '被分配到的名字所有者',
    'LBL_ASSIGNED_USER_NAME_MOD' => '被分配的模块名称',
    'LBL_TEAM_COUNT_OWNER' => '团队数所有者',
    'LBL_TEAM_COUNT_MOD' => '团队数模组',
    'LBL_TEAM_NAME_OWNER' => '团队名称所有者',
    'LBL_TEAM_NAME_MOD' => '团队模组名称',
    'LBL_ACCOUNT_NAME_OWNER' => '帐户名称所有者',
    'LBL_ACCOUNT_NAME_MOD' => '帐户名称模块',
    'LBL_MODIFIED_USER_NAME' => '更改过的用户名',
    'LBL_MODIFIED_USER_NAME_OWNER' => '更改过的用户名所有者',
    'LBL_MODIFIED_USER_NAME_MOD' => '更改过的用户模组名称',
    'LBL_PORTAL_VIEWABLE' => '可视门户站点',
    'LBL_EXPORT_ASSIGNED_USER_ID' => '分配的用户 ID',
    'LBL_EXPORT_MODIFIED_USER_ID' => '修改人 ID',
    'LBL_EXPORT_CREATED_BY' => '创建人 ID',
    'LBL_EXPORT_CREATED_BY_NAME' => '由用户名创建',
    'LBL_EXPORT_ASSIGNED_USER_NAME' => '分配的用户名',
    'LBL_EXPORT_TEAM_COUNT' => '团队数量',
    'LBL_CHANGETIMERS' => '更改计时器',
    'LBL_PURCHASES_SUBPANEL_TITLE' => '购买',

    // SNIP
    'LBL_CONTACT_HISTORY_SUBPANEL_TITLE' => '相关联系人的电子邮件',

    // Portal Tutorial content
    'LBL_PORTAL_TOUR_RECORDS_INTRO' => '此客户反馈模块用于管理影响您帐号的支持问题。使用下面的箭头开始快速的游览',
    'LBL_PORTAL_TOUR_RECORDS_PAGE' => '此页显示与您帐号相关已发表的客户反馈列表。',
    'LBL_PORTAL_TOUR_RECORDS_FILTER' => '您可以提供搜索条件来过滤客户反馈列表。',
    'LBL_PORTAL_TOUR_RECORDS_FILTER_EXAMPLE' => '例如，您可以使用此功能找到之前送出过的问题。',
    'LBL_PORTAL_TOUR_RECORDS_CREATE' => '如果您想报告新的客户反馈，您可以点击这里举报一个新的反馈。',
    'LBL_PORTAL_TOUR_RECORDS_RETURN' => '点击此处将随时返回到此视图。',
    'TPL_ACTIVITY_TIMELINE_DASHLET' => '客户反馈时间线',

    'LBL_NOTES_SUBPANEL_TITLE' => '笔记',

    // Help Text
    // List View Help Text
    'LBL_HELP_RECORDS' => '{{plural_module_name}} 模块用于追踪和管理与您的企业客户产品或服务相关的问题。 {{plural_module_name}} 通常涉及{{accounts_singular_module}} 记录，或与单一{{accounts_singular_module}} 相关的多个{{plural_module_name}}。在Sugar创建{{plural_module_name}} 有许多方式，例如通过{{plural_module_name}} 模块，输入{{plural_module_name}}，或从电子邮件转换。一旦{{module_name}} 创建后，您可以通过{{module_name}} 的记录视图查看和编辑与{{module_name}} 有关的资料。每个{{module_name}} 记录可能与其他Sugar记录相关联，例如{{calls_module}}，{{contacts_module}}，{{bugs_module}}，或其他。',

    // Record View Help Text
    'LBL_HELP_RECORD' => '{{plural_module_name}} 模块用于追踪和管理与您的企业客户产品或服务相关的问题。 

- 点击单个字段或编辑按钮來编辑此记录。
- 切换底部左侧窗格中的“数据视图”来查看或修改子面板上连到其他记录的连结。
- 切换底部的左侧窗格中的“活动流“，以在{{activitystream_singular_module}} 制作和查看用户意见，并记录更改历史。
- 使用记录名称右侧的图示来关注或收藏这个纪录。
- 编辑按钮右侧的下拉操作选单还有其他操作功能。',

    // Create View Help Text
    'LBL_HELP_CREATE' => '{{plural_module_name}} 模块用于追踪和管理由客户反馈至贵公司的产品或服务相关的问题。 

创建{{module_name}}：
1. 按需提供字段值。
- 标记为“必填”的字段在保存前必须先填写完整。
- 如有需要，点击“显示更多”以显示其他字段。
2. 点击“保存”以完成新记录，并返回至上一页。',
    // Case deflection
    'LBL_NO_LOOKING_FOR' => '没有找到所需的内容？',
    'LBL_CREATE_CASE' => '创建 {{module_name}}',

    'LBL_SLA_DETAILS' => 'SLA 详细信息',
    'LBL_HOURS' => '小时',
    'LBL_WIDGET_STATUS' => '状态',

    // KPI Metrics
    'LBL_ORGANIZE' => '组织',
    'LBL_CREATE_NEW' => '新建',
    'LBL_MANAGE' => '管理',
    'LBL_SEE_DETAILS' => '查看详细信息',
    'LBL_HIDE_NEW' => '隐藏',

    //For Kiosk labels
    'LBL_KIOSK_MODULE_NAME' => '案例',
    'LBL_KIOSK_MODULE_NAME_SINGULAR' => '案例',
    'LBL_KIOSK_CREATE' => '创建案例',
    'LBL_KIOSK_SUBJECT' => '主题',
    'LBL_KIOSK_CASE_ID' => '案例ID',
    'LBL_KIOSK_STATUS' => '状态',
    'LBL_KIOSK_CATEGORY' => '类别',
    'LBL_KIOSK_SUBMITTED_BY' => '提交于 {{date}}',
    'LBL_KIOSK_PRIORITY' => '优先级',
    'LBL_KIOSK_CREATE_CASE' => '联系我们',
    'LBL_KIOSK_DESCRIPTION' => '描述',
    'LBL_KIOSK_CREATE_SUCCESS' => '您的请求已成功提交。',
    'LBL_KIOSK_ATTACHED' => '全部附上',

    // External Users Subpanel
    'LBL_EXTERNAL_USERS_SUBPANEL_TITLE' => '外部用户',
    'LBL_ESCALATED_CASES' => '上报案件',
];
