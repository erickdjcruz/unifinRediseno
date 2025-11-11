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
    'LBL_HOMEPAGE_TITLE' => '내 Smart Guide 템플릿',
    'LBL_LIST_FORM_TITLE' => 'Smart Guide 템플릿 목록',
    'LNK_IMPORT_CUSTOMER_JOURNEY_TEMPLATES' => 'Smart Guide 템플릿 가져오기',
    'LBL_MODULE_TITLE' => 'Smart Guide 템플릿',
    'LBL_MODULE_NAME' => 'Smart Guide 템플릿',
    'LBL_NEW_FORM_TITLE' => '새 Smart Guide 템플릿',
    'LBL_REMOVE' => '제거',
    'LBL_SEARCH_FORM_TITLE' => 'Smart Guide 템플릿 검색',
    'LBL_TYPE' => '유형',
    'LNK_LIST' => 'Smart Guide 템플릿',
    'LNK_NEW_RECORD' => 'Smart Guide 템플릿 생성',
    'LBL_COPIES' => '복사',
    'LBL_COPIED_TEMPLATE' => '복사된 템플릿',
    'LBL_IMPORT_TEMPLATES_BUTTON_LABEL' => '템플릿 가져오기',
    'LBL_IMPORT_TEMPLATES_SUCCESS_MESSAGE' => '템플릿 가져오기 완료.',
    'LBL_RESAVE_TEMPLATES_BUTTON_LABEL' => '템플릿 다시 저장',
    'LBL_RESAVE_TEMPLATES_SUCCESS_MESSAGE' => '템플릿 다시 저장됨.',
    'LNK_VIEW_RECORDS' => 'Smart Guide 템플릿 보기',
    'LNK_DRI_WORKFLOW_TEMPLATE_LIST' => 'Smart Guide 템플릿 보기',
    'LBL_AVAILABLE_MODULES' => '사용 가능한 모듈',
    'LBL_CANCEL_ACTION' => '액션 취소',
    'LBL_NOT_APPLICABLE_ACTION' => '적용할 수 없는 액션',
    'LBL_POINTS' => '포인트',
    'LBL_RELATED_ACTIVITIES' => '관련 활동',
    'LBL_ACTIVE' => '활성',
    'LBL_ASSIGNEE_RULE' => '피할당자 규칙',
    'LBL_TARGET_ASSIGNEE' => '대상 피할당자',
    'LBL_STAGE_NUMBERS' => '단계 번호 매기기',
    'LBL_EXPORT_BUTTON_LABEL' => '자료 내보내기',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_BUTTON_LABEL' => '자료 가져오기',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_TEXT' => '파일 시스템에서 *.json 파일을 가져와 새Smart Guide 템플릿 레코드를 자동으로 생성/업데이트합니다.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_CREATE_SUCCESS' => '<a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a> 생성 완료.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_UPDATE_SUCCESS' => '<a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a> 업데이트 완료.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_DUPLICATE_NAME_ERROR' => '가져오기에 실패했습니다. 이름이 "<a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a>"인 템플릿이 존재합니다. 가져온 레코드의 이름을 변경하고 다시 시도하거나 "복사"를 사용하여 중복 Smart Guide 템플릿을 만드십시오.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_UPDATE_CONFIRM' => '이 ID의 템플릿이 있습니다. 기존 템플릿을 업데이트하려면 <b>확인</b>을 클릭하십시오. 기존 템플릿을 변경하지 않고 종료하려면 <b>취소</b>를 클릭하십시오.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_TEMPLATE_DELETED' => '가져오려는 템플릿이 현재 인스턴스에서 삭제되었습니다.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_EMPTY_WARNING' => '유효한 *.json 파일을 선택하십시오.',
    'LBL_CHECKING_IMPORT_UPLOAD' => '검증 중',
    'LBL_IMPORTING_TEMPLATE' => '가져오는 중',
    'LBL_DISABLED_STAGE_ACTIONS' => '비활성화된 단계 액션',
    'LBL_DISABLED_ACTIVITY_ACTIONS' => '비활성화된 활동 액션',
    'LBL_FORMS' => '양식',
    'LBL_ACTIVE_LIMIT' => '활성 Smart Guide 제한',
    'LBL_WEB_HOOKS' => '웹훅',
    'LBL_START_NEXT_JOURNEY_ACTIVITIES' => '다음 Smart Guide 시작 활동',
    'LBL_START_NEXT_JOURNEY_STAGES' => '다음 Smart Guide 단계 시작 링크',
    'LBL_SMART_GUIDE_ACCESSIBLE' => 'Smart Guide에 액세스 가능한 모듈을 선택하세요',
    'LBL_SMART_GUIDE_MODIFY_ACTIONS' => '단계에서 활동을 더 추가하거나 삭제할 수 있습니다. 이 Smart Guide에서 사용자가 액세스하지 못하게 하려는 작업을 비활성화하십시오',
    'LBL_SMART_GUIDE_DISABLE_ACTIONS' => '활동에서 더 많은 활동을 하위 활동으로 추가할 수 있습니다.이 Smart Guide에서 사용자가 액세스하지 못하게 하려는 작업을 비활성화하십시오',
    'LBL_SMART_GUIDE_ACTIVATES' => '레코드에서 동시에 활성화 가능한 Smart Guide는 몇 개입니까',
    'LBL_SMART_GUIDE_TARGET_ASSIGNEE' => '선택된 경우, 대상 피할당자 = 상위 피할당자일 경우, “할당된 담당자:” 사용자가 상위에서 변경되면, “할당된 담당자:” 사용자도 Smart Guide, 단계, 활동에서 자동으로 변경됩니다. 활동 템플릿의 대상 피할당자 설정이 Smart Guide 템플릿보다 우선합니다.',
    'LBL_SMART_GUIDE_USER_ASSIGNED' => '사용자가 활동에 배정되는 시기',
    'LBL_SMART_GUIDE_ACTIVITIES_ASSIGNED' => '활동을 배정받아야 하는 사람',
    'LBL_SMART_GUIDE_STAGE_NUMBERS' => '이 토글을 사용하면 자동 단계 번호 매기기를 표시하거나 숨길 수 있습니다.',
    'CJ_FORMS_LBL_PARENT_NAME' => 'Smart Guide/단계/활동 템플릿',
];
