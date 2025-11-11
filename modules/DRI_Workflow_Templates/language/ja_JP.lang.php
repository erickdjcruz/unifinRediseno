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
    'LBL_HOMEPAGE_TITLE' => 'マイスマートガイドテンプレート',
    'LBL_LIST_FORM_TITLE' => 'スマートガイドテンプレートリスト',
    'LNK_IMPORT_CUSTOMER_JOURNEY_TEMPLATES' => 'スマートガイドテンプレートをインポート',
    'LBL_MODULE_TITLE' => 'スマートガイドテンプレート',
    'LBL_MODULE_NAME' => 'スマートガイドテンプレート',
    'LBL_NEW_FORM_TITLE' => '新規スマートガイドテンプレート',
    'LBL_REMOVE' => '削除',
    'LBL_SEARCH_FORM_TITLE' => 'スマートガイドテンプレートを検索',
    'LBL_TYPE' => 'タイプ',
    'LNK_LIST' => 'スマートガイドテンプレート',
    'LNK_NEW_RECORD' => 'スマートガイドテンプレートを作成',
    'LBL_COPIES' => 'コピー',
    'LBL_COPIED_TEMPLATE' => 'テンプレートをコピーしました',
    'LBL_IMPORT_TEMPLATES_BUTTON_LABEL' => 'テンプレートをインポート',
    'LBL_IMPORT_TEMPLATES_SUCCESS_MESSAGE' => 'テンプレートがインポートされました。',
    'LBL_RESAVE_TEMPLATES_BUTTON_LABEL' => 'テンプレートを再保存',
    'LBL_RESAVE_TEMPLATES_SUCCESS_MESSAGE' => 'テンプレートが再保存されました。',
    'LNK_VIEW_RECORDS' => 'スマートガイドテンプレートを表示',
    'LNK_DRI_WORKFLOW_TEMPLATE_LIST' => 'スマートガイドテンプレートを表示',
    'LBL_AVAILABLE_MODULES' => '利用可能なモジュール',
    'LBL_CANCEL_ACTION' => 'アクションをキャンセル',
    'LBL_NOT_APPLICABLE_ACTION' => '該当なしアクション',
    'LBL_POINTS' => 'ポイント',
    'LBL_RELATED_ACTIVITIES' => '関連アクティビティ',
    'LBL_ACTIVE' => 'アクティブ',
    'LBL_ASSIGNEE_RULE' => '担当者ルール',
    'LBL_TARGET_ASSIGNEE' => 'ターゲット担当者',
    'LBL_STAGE_NUMBERS' => 'ステージナンバリング',
    'LBL_EXPORT_BUTTON_LABEL' => 'エクスポート',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_BUTTON_LABEL' => 'インポート',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_TEXT' => 'お使いのファイルシステムから*.jsonファイルをインポートして新規スマートガイドテンプレートレコードを自動的に作成/更新できます。',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_CREATE_SUCCESS' => 'テンプレート <a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a> が正常に作成されました。',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_UPDATE_SUCCESS' => 'テンプレート <a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a> が正常に更新されました。',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_DUPLICATE_NAME_ERROR' => 'インポートに失敗しました。「<a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a>」という名前のテンプレートがすでに存在します。インポートしたレコードの名前を変更してもう一度やり直すか、[コピー] を使用して複製スマートガイドテンプレートを作成してください。',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_UPDATE_CONFIRM' => 'この ID のテンプレートはすでに存在します。既存のテンプレートを更新するには、<b>確認</b>をクリックします。既存のテンプレートを変更しないで終了するには、<b>キャンセル</b> をクリックします。',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_TEMPLATE_DELETED' => 'インポートしようとしているテンプレートは、現在のインスタンスで削除されます。',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_EMPTY_WARNING' => '有効な*.jsonファイルを選択してください。',
    'LBL_CHECKING_IMPORT_UPLOAD' => '認証中',
    'LBL_IMPORTING_TEMPLATE' => 'インポート中',
    'LBL_DISABLED_STAGE_ACTIONS' => 'ステージアクションが無効です',
    'LBL_DISABLED_ACTIVITY_ACTIONS' => 'アクティビティアクションが無効です',
    'LBL_FORMS' => 'フォーム',
    'LBL_ACTIVE_LIMIT' => 'アクティブスマートガイドの制限',
    'LBL_WEB_HOOKS' => 'ウェブフック',
    'LBL_START_NEXT_JOURNEY_ACTIVITIES' => '次のスマートガイド開始アクティビティ',
    'LBL_START_NEXT_JOURNEY_STAGES' => '次のスマートガイドステージリンクを開始',
    'LBL_SMART_GUIDE_ACCESSIBLE' => 'スマートガイドにアクセスできるモジュールを選択',
    'LBL_SMART_GUIDE_MODIFY_ACTIONS' => 'ステージでは、アクティビティの追加または削除が可能です。このスマートガイドでユーザーにアクセスさせたくないアクションを無効にすることができます',
    'LBL_SMART_GUIDE_DISABLE_ACTIONS' => 'アクティビティでは、サブアクティビティとしてさらにアクティビティの追加が可能です。このスマートガイドでユーザーにアクセスさせたくないアクションを無効にすることができます',
    'LBL_SMART_GUIDE_ACTIVATES' => 'レコードで同時にアクティブにできるこのスマートガイドの数',
    'LBL_SMART_GUIDE_TARGET_ASSIGNEE' => 'チェックを入れると、ターゲット担当者 = 親担当者の場合、親の「割り当て先」ユーザーを変更すると、スマートガイド、ステージ、アクティビティでも自動的に「割り当て先」ユーザーが変更されます。アクティビティテンプレートのターゲット担当者の設定は、スマートガイドテンプレートよりも優先されますのでご注意ください。',
    'LBL_SMART_GUIDE_USER_ASSIGNED' => 'ユーザーをアクティビティに割り当てるタイミング',
    'LBL_SMART_GUIDE_ACTIVITIES_ASSIGNED' => 'アクティビティを割り当てられる人',
    'LBL_SMART_GUIDE_STAGE_NUMBERS' => 'これは、自動ステージナンバリングの表示/非表示を切り替えるトグルです。',
    'CJ_FORMS_LBL_PARENT_NAME' => 'スマートガイド/ステージ/アクティビティテンプレート',
];
