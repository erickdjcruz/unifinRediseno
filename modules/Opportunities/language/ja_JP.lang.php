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
    'LBL_OPPORTUNITIES_LIST_DASHBOARD' => '商談リスト ダッシュ ボード',
    'LBL_OPPORTUNITIES_RECORD_DASHBOARD' => '商談レコードダッシュ ボード',
    'LBL_OPPORTUNITIES_MULTI_LINE_DASHBOARD' => '商談フォーカス ドロワ - コンソール',
    'LBL_OPPORTUNITIES_FOCUS_DRAWER_DASHBOARD' => '商談フォーカス ドロワ',
    'LBL_RENEWAL_OPPORTUNITY' => '商談の更新',

    'LBL_MODULE_NAME' => '商談',
    'LBL_MODULE_NAME_SINGULAR' => '商談',
    'LBL_MODULE_TITLE' => '商談: ホーム',
    'LBL_SEARCH_FORM_TITLE' => '商談検索',
    'LBL_VIEW_FORM_TITLE' => '商談一覧',
    'LBL_LIST_FORM_TITLE' => '商談一覧',
    'LBL_OPPORTUNITY_NAME' => '商談名:',
    'LBL_OPPORTUNITY' => '商談名:',
    'LBL_NAME' => '商談名',
    'LBL_TIME' => '時間',
    'LBL_INVITEE' => '取引先担当者',
    'LBL_CURRENCIES' => '通貨',
    'LBL_LIST_OPPORTUNITY_NAME' => '名前',
    'LBL_LIST_ACCOUNT_NAME' => '取引先',
    'LBL_LIST_DATE_CLOSED' => '受注予定日',
    'LBL_LIST_AMOUNT' => '達成可能',
    'LBL_LIST_AMOUNT_USDOLLAR' => '合計金額',
    'LBL_ACCOUNT_ID' => '取引先ID',
    'LBL_CURRENCY_RATE' => '通貨レート',
    'LBL_CURRENCY_ID' => '通貨ID',
    'LBL_CURRENCY_NAME' => '通貨名',
    'LBL_CURRENCY_SYMBOL' => '通貨シンボル',
//DON'T CONVERT THESE THEY ARE MAPPINGS
    'db_sales_stage' => 'LBL_LIST_SALES_STAGE',
    'db_name' => 'LBL_NAME',
    'db_amount' => 'LBL_LIST_AMOUNT',
    'db_date_closed' => 'LBL_LIST_DATE_CLOSED',
//END DON'T CONVERT
    'UPDATE' => '商談の通貨更新',
    'UPDATE_DOLLARAMOUNTS' => 'USドルの金額を更新',
    'UPDATE_VERIFY' => '金額を検証',
    'UPDATE_VERIFY_TXT' => '商談の金額が数字（0-9）と小数点（.）で正しく入力されているかどうかを検証します。',
    'UPDATE_FIX' => '金額を修正',
    'UPDATE_FIX_TXT' => '現在の金額から正しい区切り文字を使って不正な金額を修正します。修正された金額のバックアップはデータベースのamount_backupフィールドに保存されます。これを実行して問題が発生した場合、バックアップから以前の値を戻してください。さもないと、再度実行すると不正な値でバックアップ値が上書きされます',
    'UPDATE_DOLLARAMOUNTS_TXT' => '商談のUSドルの更新は設定されている通貨レートに基づきます。グラフと一覧画面での金額の値に反映されます。',
    'UPDATE_CREATE_CURRENCY' => '通貨作成中:',
    'UPDATE_VERIFY_FAIL' => '検証に失敗しました:',
    'UPDATE_VERIFY_CURAMOUNT' => '現在の金額:',
    'UPDATE_VERIFY_FIX' => '修正は',
    'UPDATE_INCLUDE_CLOSE' => '完了したレコードを含む',
    'UPDATE_VERIFY_NEWAMOUNT' => '金額作成:',
    'UPDATE_VERIFY_NEWCURRENCY' => '通貨作成:',
    'UPDATE_DONE' => '完了',
    'UPDATE_BUG_COUNT' => '実行時に不具合が見つかりました:',
    'UPDATE_BUGFOUND_COUNT' => '不具合が見つかりました:',
    'UPDATE_COUNT' => 'レコードが更新されました:',
    'UPDATE_RESTORE_COUNT' => '金額がリストアされました:',
    'UPDATE_RESTORE' => '金額をリストア',
    'UPDATE_RESTORE_TXT' => '修復中にバックアップした内容をリストア',
    'UPDATE_FAIL' => '更新できません -',
    'UPDATE_NULL_VALUE' => '金額は空です。0に設定されました。  -',
    'UPDATE_MERGE' => '通貨を統合',
    'UPDATE_MERGE_TXT' => '複数の通貨を１つにまとめます。同じ通貨のレコードが複数ある場合、それらを１つにします。これは他のモジュールの通貨も統合します。',
    'LBL_ACCOUNT_NAME' => '取引先:',
    'LBL_CURRENCY' => '通貨:',
    'LBL_DATE_CLOSED' => '受注予定日:',
    'LBL_DATE_CLOSED_TIMESTAMP' => '予想クローズ日のタイムスタンプ',
    'LBL_TYPE' => 'タイプ:',
    'LBL_CAMPAIGN' => 'キャンペーン:',
    'LBL_NEXT_STEP' => '次のステップ:',
    'LBL_SERVICE_START_DATE' => 'サービス開始日',
    'LBL_LEAD_SOURCE' => 'リードソース:',
    'LBL_SALES_STAGE' => '商談ステージ:',
    'LBL_SALES_STATUS' => 'ステータス',
    'LBL_PROBABILITY' => '確度 (%):',
    'LBL_DESCRIPTION' => '詳細:',
    'LBL_DUPLICATE' => '重複の可能性がある商談',
    'MSG_DUPLICATE' => '作成しようとしている商談は既存の商談と重複する可能性があります。類似の商談は以下に表示されています。保存をクリックして新たに商談を作成するか、キャンセルをクリックして商談を作成せずにモジュールに戻ります。',
    'LBL_NEW_FORM_TITLE' => '商談作成',
    'LNK_NEW_OPPORTUNITY' => '商談作成',
    'LNK_CREATE' => '取引作成',
    'LNK_OPPORTUNITY_LIST' => '商談一覧',
    'ERR_DELETE_RECORD' => '商談を削除するにはレコード番号を指定する必要があります。',
    'LBL_TOP_OPPORTUNITIES' => '交渉中の私の商談',
    'NTC_REMOVE_OPP_CONFIRMATION' => '本当にこの取引先担当者を商談からはずしてよいですか？',
    'OPPORTUNITY_REMOVE_PROJECT_CONFIRM' => '本当にこの商談をプロジェクトからはずしてよいですか？',
    'LBL_DEFAULT_SUBPANEL_TITLE' => '商談',
    'LBL_ACTIVITIES_SUBPANEL_TITLE' => 'アクティビティ',
    'LBL_HISTORY_SUBPANEL_TITLE' => '履歴',
    'LBL_RAW_AMOUNT' => '金額',
    'LBL_LEADS_SUBPANEL_TITLE' => 'リード',
    'LBL_CONTACTS_SUBPANEL_TITLE' => '取引先担当者',
    'LBL_DOCUMENTS_SUBPANEL_TITLE' => 'ドキュメント',
    'LBL_PROJECTS_SUBPANEL_TITLE' => 'プロジェクト',
    'LBL_ASSIGNED_TO_NAME' => 'アサイン先:',
    'LBL_LIST_ASSIGNED_TO_NAME' => 'アサイン先',
    'LBL_LIST_SALES_STAGE' => '商談ステージ',
    'LBL_MY_CLOSED_OPPORTUNITIES' => '私のクローズ済み商談',
    'LBL_TOTAL_OPPORTUNITIES' => '商談総額',
    'LBL_CLOSED_WON_OPPORTUNITIES' => '受注済み商談',
    'LBL_ASSIGNED_TO_ID' => 'アサイン先:',
    'LBL_CREATED_ID' => '作成者ID',
    'LBL_MODIFIED_ID' => '更新者ID',
    'LBL_MODIFIED_NAME' => '更新者',
    'LBL_CREATED_USER' => '作成者',
    'LBL_MODIFIED_USER' => '更新者',
    'LBL_CAMPAIGN_OPPORTUNITY' => 'キャンペーン',
    'LBL_PROJECT_SUBPANEL_TITLE' => 'プロジェクト',
    'LABEL_PANEL_ASSIGNMENT' => '担当',
    'LNK_IMPORT_OPPORTUNITIES' => '商談のインポート',
    'LBL_EDITLAYOUT' => 'レイアウト編集' /*for 508 compliance fix*/,
    //For export labels
    'LBL_EXPORT_CAMPAIGN_ID' => 'キャンペーンID',
    'LBL_OPPORTUNITY_TYPE' => '商談タイプ',
    'LBL_EXPORT_ASSIGNED_USER_NAME' => 'アサイン先',
    'LBL_EXPORT_ASSIGNED_USER_ID' => 'アサイン先ID',
    'LBL_EXPORT_MODIFIED_USER_ID' => '更新者ID',
    'LBL_EXPORT_CREATED_BY' => '作成者ID',
    'LBL_EXPORT_NAME' => '名前',
    // SNIP
    'LBL_CONTACT_HISTORY_SUBPANEL_TITLE' => '関連取引先担当者のEメール',
    'LBL_FILENAME' => '添付ファイル',
    'LBL_PRIMARY_QUOTE_ID' => '主な見積',
    'LBL_CONTRACTS' => '契約',
    'LBL_CONTRACTS_SUBPANEL_TITLE' => '契約',
    'LBL_PRODUCTS' => '商品',
    'LBL_RLI' => '商談品目',
    'LNK_OPPORTUNITY_REPORTS' => '商談レポート',
    'LBL_QUOTES_SUBPANEL_TITLE' => '見積',
    'LBL_TEAM_ID' => 'チーム:',
    'LBL_TIMEPERIODS' => '期間',
    'LBL_TIMEPERIOD_ID' => '期間ID',
    'LBL_COMMITTED' => 'コミット済',
    'LBL_FORECAST' => '売上予測',
    'LBL_COMMIT_STAGE' => 'コミットステージ',
    'LBL_COMMIT_STAGE_FORECAST' => '売上予測ステージ',
    'LBL_WORKSHEET' => 'ワークシート',
    'LBL_PURCHASED_LINE_ITEMS' => '購入商品',

    // KPI Metrics
    'LBL_ORGANIZE' => '編成',
    'LBL_CREATE_NEW' => '新規作成',
    'LBL_MANAGE' => '管理',
    'LBL_SEE_DETAILS' => 'レポートの詳細',
    'LBL_HIDE_NEW' => '非表示',

    'LBL_FORECASTED_LIKELY' => '予想される可能性が高い',
    'LBL_LOST' => '失注',
    'LBL_RENEWAL' => '更新',
    'LBL_RENEWAL_OPPORTUNITIES' => '商談の更新',
    'LBL_RENEWAL_PARENT' => '親商談',
    'LBL_PARENT_RENEWAL_OPPORTUNITY_ID' => '親IDの更新',
    'LBL_MONTH_YEAR_RENEWAL' => '{{month}}、{{year}}',

    'LBL_WIDGET_SALES_STAGE' => '商談ステージ',
    'LBL_WIDGET_DATE_CLOSED' => '受注予定日',
    'LBL_WIDGET_AMOUNT' => '金額',

    'TPL_RLI_CREATE' => '商談は商談品目と関係がなければなりません。商談品目を新規作成する。',
    'TPL_RLI_CREATE_LINK_TEXT' => '商談品目を作成する。',
    'LBL_PRODUCTS_SUBPANEL_TITLE' => '見積済商品',
    'LBL_RLI_SUBPANEL_TITLE' => '商談品目',

    'LBL_TOTAL_RLIS' => '商談品目総数',
    'LBL_CLOSED_RLIS' => 'クローズされた商談品目総数',
    'LBL_CLOSED_WON_RLIS' => '商談成立の商談品目総数',
    'LBL_SERVICE_OPEN_FLEX_DURATION_RLIS' => '# 個のオープンサービスのフレックス期間の収益商品',
    'NOTICE_NO_DELETE_CLOSED_RLIS' => 'クローズされた商談品目を含む商談を削除することはできません',
    'WARNING_NO_DELETE_CLOSED_SELECTED' => '選択されたレコードがクローズされた商談品目を含んでいるため削除できません。',
    'LBL_INCLUDED_RLIS' => '含まれている商品数',
    'LBL_UPDATE_OPPORTUNITIES_RLIS' => '未処理を更新',
    'LBL_CASCADE_RLI_EDIT' => '開いている収益項目の更新',
    'LBL_CASCADE_RLI_CREATE' => '商談品目間での設定',
    'LBL_SERVICE_START_DATE_INVALID' => '開いている商談品目アドオンのサービス開始日をサービス終了日より前の日付に設定することはできません。',

    'LBL_QUOTE_SUBPANEL_TITLE' => '見積',
    'LBL_FILTER_OPPORTUNITY_TEMPLATE' => '動的アカウントによる機会',
    'LBL_TOP_10_OPP' => '上位10件のオープンな機会',
    'LBL_DASHLET_MY_ACTIVE_OPP' => 'ダッシュレット：私のアクティブな機会',
    'LBL_MY_ACTIVE_OPP' => '私のアクティブな機会',


    // Config
    'LBL_OPPS_CONFIG_VIEW_BY_LABEL' => '商談階層',
    'LBL_OPPS_CONFIG_VIEW_BY_DATE_ROLLUP' => '既存の商談品目の最初のもしくは最新のクローズ日に近い日付であるの商談レコードの、予想クローズ日のフィールドを設定します',

    //Dashlet
    'LBL_PIPELINE_TOTAL_IS' => 'パイプラインの合計は',

    'LBL_OPPORTUNITY_ROLE' => '商談での役割',
    'LBL_NOTES_SUBPANEL_TITLE' => 'メモ',
    'LBL_TAB_OPPORTUNITY' => '{{module}} のレビュー',

    // Help Text
    'LBL_OPPS_CONFIG_ALERT' => '確認をクリックすると、すべての売上予測データを消去し、商談ビューを変更します。これが意図したものではない場合は、キャンセルをクリックして以前の設定に戻ります。',
    'LBL_OPPS_CONFIG_ALERT_TO_OPPS' =>
        '確認をクリックすると、すべての売上予測データを消去して商談ビューが変更されます。'
        . 'また、商談品目のターゲットモジュールが含まれるすべてのプロセス定義は無効になります。'
        . 'この操作が意図したものでなければ、キャンセルをクリックして以前の設定に戻ってください。',
    'LBL_OPPS_CONFIG_SALES_STAGE_1a' => '全ての商談品目がクローズされ、少なくとも一つ受注した場合、',
    'LBL_OPPS_CONFIG_SALES_STAGE_1b' => '商談セールスステージは「クローズされた受注」にセットされます。',
    'LBL_OPPS_CONFIG_SALES_STAGE_2a' => '全ての商談品目がクローズされた失注ステージにある場合、',
    'LBL_OPPS_CONFIG_SALES_STAGE_2b' => '商談セールスステージは「クローズされた失注」にセットされます。',
    'LBL_OPPS_CONFIG_SALES_STAGE_3a' => '商談品目がまだオープンの場合、',
    'LBL_OPPS_CONFIG_SALES_STAGE_3b' => '商談は進捗が少ないセールスステージにマークされます。',

// BEGIN ENT/ULT

    // Opps Config - View By Opportunities
    'LBL_HELP_CONFIG_OPPS' => 'この変更を開始した後、商談品目の要約ノートはバックグラウンドで構築されます。ノートが完了し利用可能な場合、通知がユーザプロファイルのEメールアドレスに送信されます。あなたのインスタンスが{{forecasts_module}} が設定されている場合は、Sugarは{{module_name}} レコードが{{forecasts_module}} モジュールに同期された時に通知を送り、{{forecasts_module}} が使用可能になります。インスタンスは通知が送信されるために　管理＞Eメール設定　でEメールを送信するように設定する必要がありますのでご注意ください。',

    // Opps Config - View By Opportunities And RLIs
    'LBL_HELP_CONFIG_RLIS' => 'この変更を開始した後、既存の{{module_name}} の商談品目のレコードが作成されます。商談品目が完了し使用可能の場合、通知がユーザプロファイルのEメールアドレスに送信されます。インスタンスは通知が送信されるために　管理＞Eメール設定　でEメールを送信するように設定する必要がありますのでご注意ください。',
    // List View Help Text
    'LBL_HELP_RECORDS' => '{{plural_module_name}} モジュールを使用すると、個々の商談を最初から最後まで追跡できます。各 {{module_name}} レコードは、商談見込みを表し、関連する商談データだけでなく、{{quotes_module}}、{{contacts_module}} などの他の重要なレコードに関連するものも含まれます。{{module_name}} は通常、「完了」または「失注」と表示されるまで、いくつかのセールスステージを進みます。{{plural_module_name}} をさらに活用するには、Sugarの {{forecasts_singular_module}} モジュールを使用して、商談傾向を理解および予測し、営業ノルマを達成するための作業に集中します。',

    // Record View Help Text
    'LBL_HELP_RECORD' => '{{plural_module_name}}モジュールにより、個々の商談とそれらの商談に属している商談品目を最初から最後まで追跡できます。各{{module_name}}レコードは商談見込みを表し、関連する商談データだけでなく{{quotes_module}}、{{contacts_module}}などの他の重要なレコードに関連しているデータも含まれます。

・個々のフィールドまたは[編集]ボタンをクリックすると、このレコードのフィールドを編集できます。
・左下のペインを[データビュー]に切り替えると、サブパネルにある他のレコードへのリンクを表示または修正できます。
・左下のペインを[アクティビティストリーム]に切り替えると、{{activitystream_singular_module}}でユーザーコメントを作成・閲覧したり、変更履歴を記録したりできます。
・レコード名の右側にあるアイコンを使用すると、このレコードをフォローしたり、お気に入りに追加したりできます。
・[編集]ボタンの右側にあるドロップダウンの[アクション]メニューから、追加のアクションが利用できます。',

    // Create View Help Text
    'LBL_HELP_CREATE' => '{{plural_module_name}}モジュールにより、個々の商談とそれらの商談に属している商談品目を最初から最後まで追跡できます。各{{module_name}}レコードは商談見込みを表し、関連する商談データだけでなく{{quotes_module}}、{{contacts_module}}などの他の重要なレコードに関連しているデータも含まれます。

{{module_name}}の作成方法
1. 必要に応じてフィールドに値を入力します。
・[必須]とマークされたフィールドは入力してから保存する必要があります。
・必要に応じて[さらに表示]をクリックすると、追加フィールドが表示されます。
2. [保存]をクリックすると、新しいレコードが確定されて前のページに戻ります。',

// END ENT/ULT

    //Marketo
    'LBL_MKTO_SYNC' => 'Marketo®に同期する',
    'LBL_MKTO_ID' => 'Marketoリード ID',

    'LBL_DASHLET_TOP10_SALES_OPPORTUNITIES_NAME' => 'TOP10営業商談',
    'LBL_TOP10_OPPORTUNITIES_CHART_DESC' => 'トップ10の商談をバブルチャートで表示する',
    'LBL_TOP10_OPPORTUNITIES_MY_OPP' => '私の商談',
    'LBL_TOP10_OPPORTUNITIES_MY_TEAMS_OPP' => "チームの商談",

    'LBL_PIPELINE_ERR_CLOSED_SALES_STAGE' => 'この{{moduleSingular}}に未処理品目がないため、{{fieldName}}を変更できません。',
    'TPL_ACTIVITY_TIMELINE_DASHLET' => '商談のタイムライン',

    'LBL_CASCADE_SERVICE_WARNING' => '商談品目はサービスではないため、これらの商談品目のいずれかには設定できません。作成を続行しますか？',
    'LBL_CASCADE_DURATION_WARNING' => 'これらの商談品目の期間がロックされているため、これらの商談品目のいずれかに設定できません。作成を続行しますか？',

    // AI Predict
    'LBL_AI_OPPORTUNITY_CLOSE_PREDICTION_NAME' => '商談クローズ予測',
    'LBL_AI_OPPORTUNITY_CLOSE_PREDICTION_DESC' => '指定の商談の予測詳細を見る',
    'LBL_AI_WINRATE' => '受注率',
    'LBL_AI_WONOPP' => '受注済み商談',
    'LBL_AI_CLOSINGTIME' => '完了時間',
    'LBL_AI_CLOSEDOPP' => '完了商談',
    'LBL_AI_LEADTIMESPAN' => '商談の作成から受注済みまでの時間',
];
