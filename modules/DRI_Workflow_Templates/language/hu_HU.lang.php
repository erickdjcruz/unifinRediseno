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
    'LBL_HOMEPAGE_TITLE' => 'Saját Smart Guide sablonok',
    'LBL_LIST_FORM_TITLE' => 'Smart Guide sablonlista',
    'LNK_IMPORT_CUSTOMER_JOURNEY_TEMPLATES' => 'Smart Guide sablonok importálása',
    'LBL_MODULE_TITLE' => 'Smart Guide sablonok',
    'LBL_MODULE_NAME' => 'Smart Guide sablonok',
    'LBL_NEW_FORM_TITLE' => 'Új Smart Guide sablon',
    'LBL_REMOVE' => 'Eltávolítás',
    'LBL_SEARCH_FORM_TITLE' => 'Smart Guide sablonok keresése',
    'LBL_TYPE' => 'Típus',
    'LNK_LIST' => 'Smart Guide sablonok',
    'LNK_NEW_RECORD' => 'Smart Guide sablon létrehozása',
    'LBL_COPIES' => 'Másolatok',
    'LBL_COPIED_TEMPLATE' => 'Másolt sablon',
    'LBL_IMPORT_TEMPLATES_BUTTON_LABEL' => 'Sablonok importálása',
    'LBL_IMPORT_TEMPLATES_SUCCESS_MESSAGE' => 'A sablonok importálása megtörtént.',
    'LBL_RESAVE_TEMPLATES_BUTTON_LABEL' => 'Sablonok újramentése',
    'LBL_RESAVE_TEMPLATES_SUCCESS_MESSAGE' => 'A sablonok újra mentésre kerültek.',
    'LNK_VIEW_RECORDS' => 'Smart Guide sablonok megtekintése',
    'LNK_DRI_WORKFLOW_TEMPLATE_LIST' => 'Smart Guide sablonok megtekintése',
    'LBL_AVAILABLE_MODULES' => 'Elérhető modulok',
    'LBL_CANCEL_ACTION' => 'Művelet megszakítása',
    'LBL_NOT_APPLICABLE_ACTION' => 'Nem alkalmazható intézkedés',
    'LBL_POINTS' => 'Pontok',
    'LBL_RELATED_ACTIVITIES' => 'Kapcsolódó tevékenységek',
    'LBL_ACTIVE' => 'Aktív',
    'LBL_ASSIGNEE_RULE' => 'Kedvezményezett szabály',
    'LBL_TARGET_ASSIGNEE' => 'Cél megbíztott',
    'LBL_STAGE_NUMBERS' => 'Szint számozása',
    'LBL_EXPORT_BUTTON_LABEL' => 'Exportálás',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_BUTTON_LABEL' => 'Importálás',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_TEXT' => 'Egy új Smart Guide sablon adat automatikus létrehozása/frissítése egy *.json fájl importálásával az Ön fájlrendszeréből.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_CREATE_SUCCESS' => 'A <a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a> sablon sikeresen létrejött.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_UPDATE_SUCCESS' => 'A <a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a> sablon frissítése sikeres.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_DUPLICATE_NAME_ERROR' => 'Importálás sikertelen. A <a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a> sablon már létezik. Kérjük, változtassa meg az importálni kívánt rekord nevét, és próbálja újra, vagy használja a "Másolás" lehetőséget egy duplikált Smart Guide sablon létrehozásához.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_UPDATE_CONFIRM' => 'Ezzel az azonosítóval már létezik sablon. A meglévő sablon frissítéséhez kattintson <b>Megerősítés</b> gombra. Ha a meglévő sablon módosítása nélkül szeretne kilépni, kattintson <b>Mégse</b> gombra.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_TEMPLATE_DELETED' => 'Az importálni kívánt sablon törölve van a jelenlegi munkamenetben.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_EMPTY_WARNING' => 'Kérjük, válasszon ki egy érvényes *.json fájlt.',
    'LBL_CHECKING_IMPORT_UPLOAD' => 'Hitelesítés',
    'LBL_IMPORTING_TEMPLATE' => 'Importálás',
    'LBL_DISABLED_STAGE_ACTIONS' => 'Letiltott szintműveletek',
    'LBL_DISABLED_ACTIVITY_ACTIONS' => 'Letiltott tevékenységműveletek',
    'LBL_FORMS' => 'Nyomtatványok',
    'LBL_ACTIVE_LIMIT' => 'Aktív Smart Guide(-ok) maximális száma',
    'LBL_WEB_HOOKS' => 'Webhookok',
    'LBL_START_NEXT_JOURNEY_ACTIVITIES' => 'Következő Smart Guide-indító tevékenység',
    'LBL_START_NEXT_JOURNEY_STAGES' => 'Következő Smart Guide szint hivatkozásának indítása',
    'LBL_SMART_GUIDE_ACCESSIBLE' => 'Válassza ki azokat a modulokat, ahol a Smart Guide-oknak elérhetőnek kell lennie',
    'LBL_SMART_GUIDE_MODIFY_ACTIONS' => 'A szinten belül hozzáadhat vagy törölhet tevékenységeket. Tiltsa le azokat a műveleteket, amelyekhez nem szeretné, hogy a felhasználók hozzáférjenek ebben a Smart Guide-ban',
    'LBL_SMART_GUIDE_DISABLE_ACTIONS' => 'Egy tevékenységen belül egy vagy több altevékenységet hozzáadhat. Tiltsa le azokat a műveleteket, amelyekhez nem szeretné, hogy a felhasználók hozzáférjenek ebben a Smart Guide-ban',
    'LBL_SMART_GUIDE_ACTIVATES' => 'Hány lehet egyszerre aktív ebből a Smart Guide-ból egy bejegyzésen belül',
    'LBL_SMART_GUIDE_TARGET_ASSIGNEE' => 'Ha be van jelölve, ha a Cél megbízott = Szülő megbízott, ha a "Hozzárendelt" felhasználó megváltozik egy szülőn, a "Hozzárendelt" felhasználók automatikusan megváltoznak a Smart Guide-okban, szakaszokon és tevékenységeken is. Vegye figyelembe, hogy a cél megbízott tevékenységsablonok beállításai elsőbbséget élveznek Smart Guide sablonnal szemben',
    'LBL_SMART_GUIDE_USER_ASSIGNED' => 'Mikor kell egy felhasználót hozzárendelni a tevékenységekhez',
    'LBL_SMART_GUIDE_ACTIVITIES_ASSIGNED' => 'Kihez kell hozzárendelni a tevékenységeket',
    'LBL_SMART_GUIDE_STAGE_NUMBERS' => 'Ez a kapcsoló lehetővé teszi az automatikus szintszámozás megjelenítését vagy elrejtését.',
    'CJ_FORMS_LBL_PARENT_NAME' => 'Smart Guide/szint/tevékenységsablon',
];
