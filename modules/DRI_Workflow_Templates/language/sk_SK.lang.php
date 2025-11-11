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
    'LBL_HOMEPAGE_TITLE' => 'Moje šablóny inteligentného sprievodcu',
    'LBL_LIST_FORM_TITLE' => 'Zoznam šablón inteligentného sprievodcu',
    'LNK_IMPORT_CUSTOMER_JOURNEY_TEMPLATES' => 'Importovať šablóny inteligentného sprievodcu',
    'LBL_MODULE_TITLE' => 'Šablóny inteligentného sprievodcu',
    'LBL_MODULE_NAME' => 'Šablóny inteligentného sprievodcu',
    'LBL_NEW_FORM_TITLE' => 'Nová šablóna inteligentného sprievodcu',
    'LBL_REMOVE' => 'Odobrať',
    'LBL_SEARCH_FORM_TITLE' => 'Hľadať šablóny inteligentného sprievodcu',
    'LBL_TYPE' => 'Typ',
    'LNK_LIST' => 'Šablóny inteligentného sprievodcu',
    'LNK_NEW_RECORD' => 'Vytvoriť šablónu inteligentného sprievodcu',
    'LBL_COPIES' => 'Kópie',
    'LBL_COPIED_TEMPLATE' => 'Skopírovaná šablóna',
    'LBL_IMPORT_TEMPLATES_BUTTON_LABEL' => 'Importovať šablóny',
    'LBL_IMPORT_TEMPLATES_SUCCESS_MESSAGE' => 'Šablóny boli importované.',
    'LBL_RESAVE_TEMPLATES_BUTTON_LABEL' => 'Znovu uložiť šablóny',
    'LBL_RESAVE_TEMPLATES_SUCCESS_MESSAGE' => 'Šablóny boli znovu uložené.',
    'LNK_VIEW_RECORDS' => 'Zobraziť šablóny inteligentného sprievodcu',
    'LNK_DRI_WORKFLOW_TEMPLATE_LIST' => 'Zobraziť šablóny inteligentného sprievodcu',
    'LBL_AVAILABLE_MODULES' => 'Dostupné moduly',
    'LBL_CANCEL_ACTION' => 'Zrušiť akciu',
    'LBL_NOT_APPLICABLE_ACTION' => 'Nepoužiteľná akcia',
    'LBL_POINTS' => 'Body',
    'LBL_RELATED_ACTIVITIES' => 'Súvisiace aktivity',
    'LBL_ACTIVE' => 'Aktívny',
    'LBL_ASSIGNEE_RULE' => 'Pravidlo poverenej osoby',
    'LBL_TARGET_ASSIGNEE' => 'Cieľová poverená osoba',
    'LBL_STAGE_NUMBERS' => 'Číslovanie fáz',
    'LBL_EXPORT_BUTTON_LABEL' => 'Export',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_BUTTON_LABEL' => 'Import',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_TEXT' => 'Automaticky vytvorte/aktualizujte nový záznam šablóny inteligentného sprievodcu importovaním súboru *.json zo systému súborov.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_CREATE_SUCCESS' => 'Šablóna <a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a> bola úspešne vytvorená.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_UPDATE_SUCCESS' => 'Šablóna <a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a> bola úspešne aktualizovaná.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_DUPLICATE_NAME_ERROR' => 'Import zlyhal. Šablóna s názvom "<a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a>" už existuje. Zmeňte názov importovaného záznamu a skúste to znova alebo použite možnosť „Kopírovať“ na vytvorenie duplicitnej šablóny inteligentného sprievodcu.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_UPDATE_CONFIRM' => 'Šablóna s týmto ID už existuje. Ak chcete aktualizovať existujúcu šablónu, kliknite na položku <b>Potvrdiť</b>. Ak chcete skončiť bez vykonania akýchkoľvek zmien v existujúcej šablóne, kliknite na položku <b>Zrušiť</b>.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_TEMPLATE_DELETED' => 'Šablóna, ktorú sa pokúšate importovať, je v aktuálnej inštancii odstránená.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_EMPTY_WARNING' => 'Vyberte platný súbor *.json.',
    'LBL_CHECKING_IMPORT_UPLOAD' => 'Overuje sa',
    'LBL_IMPORTING_TEMPLATE' => 'Importuje sa',
    'LBL_DISABLED_STAGE_ACTIONS' => 'Zakázané akcie fázy',
    'LBL_DISABLED_ACTIVITY_ACTIONS' => 'Zakázané akcie aktivity',
    'LBL_FORMS' => 'Formuláre',
    'LBL_ACTIVE_LIMIT' => 'Limit aktívneho inteligentného sprievodcu(-ov)',
    'LBL_WEB_HOOKS' => 'Webhooky',
    'LBL_START_NEXT_JOURNEY_ACTIVITIES' => 'Nová počiatočná aktivita inteligentného sprievodcu',
    'LBL_START_NEXT_JOURNEY_STAGES' => 'Odkaz na spustenie ďalšej fázy inteligentného sprievodcu',
    'LBL_SMART_GUIDE_ACCESSIBLE' => 'Vyberte moduly, v ktorých by mal byť inteligentný sprievodca prístupný',
    'LBL_SMART_GUIDE_MODIFY_ACTIONS' => 'Pri fáze môžete pridať ďalšie aktivity alebo ich odstrániť. Zakážte akcie, ku ktorým nechcete, aby mal používateľ prístup v tomto inteligentnom sprievodcovi',
    'LBL_SMART_GUIDE_DISABLE_ACTIONS' => 'Pri aktivite môžete pridať ďalšie aktivity ako čiastkové aktivity. Zakážte akcie, ku ktorým nechcete, aby mal používateľ prístup v tomto inteligentnom sprievodcovi',
    'LBL_SMART_GUIDE_ACTIVATES' => 'Koľko z tohto inteligentného sprievodcu môže byť aktívne v zázname súčasne',
    'LBL_SMART_GUIDE_TARGET_ASSIGNEE' => 'Ak je začiarknuté, a ak cieľová poverená osoba = nadradená poverená osoba, tak keď sa v nadradenej položke zmení používateľ „Priradené k“, automaticky sa zmenia aj používatelia „Priradené k“ v inteligentných sprievodcoch, fázach a aktivitách. Upozorňujeme, že nastavenia cieľovej poverenej osoby v šablónach aktivít majú prednosť pred šablónou inteligentného sprievodcu',
    'LBL_SMART_GUIDE_USER_ASSIGNED' => 'Kedy by mal byť používateľ priradený k aktivitám',
    'LBL_SMART_GUIDE_ACTIVITIES_ASSIGNED' => 'Komu by mali byť priradené aktivity',
    'LBL_SMART_GUIDE_STAGE_NUMBERS' => 'Tento prepínač vám umožňuje zobraziť alebo skryť automatické číslovanie fáz.',
    'CJ_FORMS_LBL_PARENT_NAME' => 'Šablóna inteligentného sprievodcu/fázy/aktivity',
];
