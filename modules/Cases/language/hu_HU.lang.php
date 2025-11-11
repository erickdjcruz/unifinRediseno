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
    'LBL_CASES_LIST_DASHBOARD' => 'Esetek listája műszerfal',
    'LBL_CASES_RECORD_DASHBOARD' => 'Eset bejegyzések műszerfal',
    'LBL_CASES_MULTI_LINE_DASHBOARD' => 'Esetek fókusz vezérlőpult - konzol',
    'LBL_CASES_FOCUS_DRAWER_DASHBOARD' => 'Figyelemfelhívás esetek',
    'LBL_CLAIM_AND_CLOSE' => 'Eset tevékenységgomb',
    'LBL_ACTION_CLAIM' => 'Tevékenység igény',
    'LBL_ACTION_CLOSE' => 'Tevékenyég zárás',
    'LBL_ACTION_UNASSIGN' => 'Tevékenység hozzárendelésének megszűntetése',
    'LBL_CLAIM_BUTTON_TEXT' => 'Igénybejelentés',
    'LBL_CLOSE_BUTTON_TEXT' => 'Bezár',
    'LBL_UNASSIGN_BUTTON_TEXT' => 'Hozzárendelés visszavonása',

    'ERR_DELETE_RECORD' => 'Adjon meg egy azonosítót a kliens törléséhez!',
    'LBL_ACCOUNT_ID' => 'Kliens azonosító',
    'LBL_ACCOUNT_NAME' => 'Kliensnév:',
    'LBL_ACCOUNTS_SUBPANEL_TITLE' => 'Kliensek',
    'LBL_ACTIVITIES_SUBPANEL_TITLE' => 'Tevékenységek',
    'LBL_ATTACH_NOTE' => 'Feljegyzés csatolása',
    'LBL_BUGS_SUBPANEL_TITLE' => 'Hibák',
    'LBL_CASE_NUMBER' => 'Eset száma:',
    'LBL_CASE_SUBJECT' => 'Eset tárgya:',
    'LBL_CASE' => 'Eset:',
    'LBL_CONTACT_CASE_TITLE' => 'Eset kapcsolat:',
    'LBL_CONTACT_NAME' => 'Kapcsolat neve:',
    'LBL_CONTACT_ROLE' => 'Szerepkör:',
    'LBL_CONTACTS_SUBPANEL_TITLE' => 'Kapcsolatok',
    'LBL_DEFAULT_SUBPANEL_TITLE' => 'Esetek',
    'LBL_DESCRIPTION' => 'Leírás:',
    'LBL_FILENANE_ATTACHMENT' => 'Melléklet',
    'LBL_HISTORY_SUBPANEL_TITLE' => 'Előzmények',
    'LBL_INVITEE' => 'Kapcsolatok',
    'LBL_MEMBER_OF' => 'Kliens',
    'LBL_MODULE_NAME' => 'Esetek',
    'LBL_MODULE_NAME_SINGULAR' => 'Eset',
    'LBL_MODULE_TITLE' => 'Esetek: Főoldal',
    'LBL_NEW_FORM_TITLE' => 'Új eset',
    'LBL_NUMBER' => 'Száma:',
    'LBL_PRIORITY' => 'Prioritás:',
    'LBL_PROJECTS_SUBPANEL_TITLE' => 'Projektek',
    'LBL_DOCUMENTS_SUBPANEL_TITLE' => 'Dokumentumok',
    'LBL_RESOLUTION' => 'Felbontás:',
    'LBL_SEARCH_FORM_TITLE' => 'Esetek keresése',
    'LBL_STATUS' => 'Állapot:',
    'LBL_SOURCE' => 'Forrás:',
    'LBL_SUBJECT' => 'Tárgy:',
    'LBL_LIST_ACCOUNT_NAME' => 'Kliens neve',
    'LBL_LIST_ASSIGNED' => 'Felelős',
    'LBL_LIST_CLOSE' => 'Zárás',
    'LBL_LIST_FORM_TITLE' => 'Eset lista',
    'LBL_LIST_LAST_MODIFIED' => 'Utolsó módosítás',
    'LBL_LIST_MY_CASES' => 'Nyitott eseteim',
    'LBL_LIST_NUMBER' => 'Szám',
    'LBL_LIST_PRIORITY' => 'Prioritás',
    'LBL_LIST_STATUS' => 'Állapot',
    'LBL_LIST_SUBJECT' => 'Tárgy',
    'LBL_LIST_ASSIGNED_TO_NAME' => 'Felelős felhasználó',
    'LBL_SHOW_MORE' => 'Több mutatása',
    'LNK_CASE_LIST' => 'Esetek megtekintése',
    'LNK_NEW_CASE' => 'Új eset létrehozása',
    'LBL_CASE_FROM_TWITTER_TITLE' => 'Tweet',
    'LNK_CREATE' => 'Új eset létrehozása',
    'LNK_CREATE_WHEN_EMPTY' => 'Ügy azonnali létrehozása.',
    'NTC_REMOVE_FROM_BUG_CONFIRMATION' => 'Biztos, hogy törli ezt az esetet a hibák közül?',
    'NTC_REMOVE_INVITEE' => 'Biztosan el akarja távolítani a kapcsolatot az esettől?',
    'LBL_LIST_DATE_CREATED' => 'Létrehozás dátuma',
    'LBL_ASSIGNED_TO_NAME' => 'Felelős',
    'LBL_TYPE' => 'Típus',
    'LBL_WORK_LOG' => 'Munkanapló',
    'LNK_IMPORT_CASES' => 'Esetek importálása',
    'LNK_CASE_REPORTS' => 'Eset jelentések megtekintése',
    'LBL_SHOW_IN_PORTAL' => 'Mutassa portál-formában',
    'LBL_CREATE_KB_DOCUMENT' => 'Új cikk létrehozása',
    'LBL_KBCONTENTS_SUBPANEL_TITLE' => 'Tudásbázis',
    'LBL_REQUEST_CLOSE' => 'Zárás kérése',
    'LBL_REQUEST_CLOSE_DATE' => 'Zárás kérésének ideje',
    'LBL_REQUEST_CLOSE_LABEL' => 'Kérés zárásra',
    'LBL_REQUEST_CLOSE_TOOLTIP' => 'Kérést küldhet az eset zárására',
    'LBL_REQUEST_CLOSE_MESSAGE' => 'Szeretné kérni az eset zárását?',
    'LBL_DASHLET_REQUESTED_CLOSE_CASES' => 'Zárásra kérvényezett saját esetek',
    'LBL_MY_REQUESTED_CLOSE_CASES' => 'Zárásra kérvényezett saját esetek',
    'LBL_DASHLET_MY_REQUESTED_CLOSE_CASES_NAME' => 'Zárásra kérvényezett saját esetek',
    'LBL_DASHLET_REQUESTED_CLOSE_CASES_NAME' => 'Zárásra kérvényezett saját esetek listája',
    'LBL_DASHLET_REQUESTED_CLOSE_CASES_DESCRIPTION' => 'Azon hozzárendelt esetek listáját jeleníti meg, amelyek zárását kérte',

    'LBL_CREATED_USER' => 'Felhasználó által létrehozva',
    'LBL_MODIFIED_USER' => 'Felhasználó által módosítva',
    'LBL_PROJECT_SUBPANEL_TITLE' => 'Projektek',
    'LBL_CASE_INFORMATION' => 'Eset áttekintés',

    //For export labels
    'LBL_MODIFIED_BY_NAME_OWNER' => 'Módosította',
    'LBL_MODIFIED_BY_NAME_MOD' => 'Módosította',
    'LBL_CREATED_BY_NAME_OWNER' => 'Létrehozta',
    'LBL_CREATED_BY_NAME_MOD' => 'Létrehozta',
    'LBL_ASSIGNED_USER_NAME_OWNER' => 'Kijelölt felelős',
    'LBL_ASSIGNED_USER_NAME_MOD' => 'Kijelölt felelős',
    'LBL_TEAM_COUNT_OWNER' => 'Csoport felelőse',
    'LBL_TEAM_COUNT_MOD' => 'Csoport',
    'LBL_TEAM_NAME_OWNER' => 'Csoport neve',
    'LBL_TEAM_NAME_MOD' => 'Csoport neve',
    'LBL_ACCOUNT_NAME_OWNER' => 'Kliens neve',
    'LBL_ACCOUNT_NAME_MOD' => 'Kliens neve',
    'LBL_MODIFIED_USER_NAME' => 'Módosított felhasználónév',
    'LBL_MODIFIED_USER_NAME_OWNER' => 'Módosított felhasználónév tulajdonosa',
    'LBL_MODIFIED_USER_NAME_MOD' => 'Módosított felhasználónév',
    'LBL_PORTAL_VIEWABLE' => 'Látható portál',
    'LBL_EXPORT_ASSIGNED_USER_ID' => 'Felelőse felhasználói azonosító',
    'LBL_EXPORT_MODIFIED_USER_ID' => 'Módosító azonosítója',
    'LBL_EXPORT_CREATED_BY' => 'Létrehozó azonosítója',
    'LBL_EXPORT_CREATED_BY_NAME' => 'Létrehozó neve',
    'LBL_EXPORT_ASSIGNED_USER_NAME' => 'Felelős felhasználó',
    'LBL_EXPORT_TEAM_COUNT' => 'Csoportlétszám',
    'LBL_CHANGETIMERS' => 'Időzítők módosítása',
    'LBL_PURCHASES_SUBPANEL_TITLE' => 'Vásárlások',

    // SNIP
    'LBL_CONTACT_HISTORY_SUBPANEL_TITLE' => 'Kontakszemélyek emailjei',

    // Portal Tutorial content
    'LBL_PORTAL_TOUR_RECORDS_INTRO' => 'Az Ügyek/Esetek modul a klienséhez kapcsolódó technikai jellegű tevékenységeket listázza. Alább a nyilak használatával megismerkedhet a modullal.',
    'LBL_PORTAL_TOUR_RECORDS_PAGE' => 'Az oldal a klienshez kapcsolódó ügyeket mutatja.',
    'LBL_PORTAL_TOUR_RECORDS_FILTER' => 'Keresendő kifejezés megadásával szűrheti az ügyek listáját.',
    'LBL_PORTAL_TOUR_RECORDS_FILTER_EXAMPLE' => 'Ez hasznos lehet, ha például egy korábban bejegyzett ügyet keres.',
    'LBL_PORTAL_TOUR_RECORDS_CREATE' => 'Új eset, vagy ügy hozzáadásához, kattintson ide!',
    'LBL_PORTAL_TOUR_RECORDS_RETURN' => 'Ide kattintva bármikor visszatérhet ehhez a nézethez.',
    'TPL_ACTIVITY_TIMELINE_DASHLET' => 'Eset idővonala',

    'LBL_NOTES_SUBPANEL_TITLE' => 'Feljegyzések',

    // Help Text
    // List View Help Text
    'LBL_HELP_RECORDS' => 'A {{plural_module_name}} modul a termékekkel és szolgáltatásokkal kapcsolatos problémákat listázza, külső vagy belső jelentések alapján. Egy klienshez több probléma is kapcsolódhat, amelyek bejegyzés létrehozásával, importálással vagy email konvertálásával kerülhetnek a rendszerbe. Amennyiben egy problémáról jelentés készült, az azzal kapcsolatos információk rekord nézetben követhetők nyomon. A rekordok hozzákapcsolódhatnak egyéb Sugar bejegyzésekhez, így hívásokhoz, kapcsolatokhoz vagy esetekhez is.',

    // Record View Help Text
    'LBL_HELP_RECORD' => 'A {{plural_module_name}} modul a termékekkel és szolgáltatásokkal kapcsolatos problémákat listázza, külső vagy belső jelentések alapján. - Szerkessze ennek a rekordnak a mezőit külön-külön, vagy kattintson a Szerkesztés gombra! Tekintse meg, vagy szerkessze a linkeket a bal alsó "Adatnézet" kapcsoló használatával! Olvassa el, vagy írjon felhasználói hozzászólásokat a "Tevékenységfolyam" opcióval! A rekord neve mellett található ikonok segítségével jelölje be kedvencének a tartalmat, vagy kövesse annak utóéletét! Egyéb tevékenységek a Szerkesztés gombtól jobbra szereplő műveleti gomb legördülő menüjében találhatók.',

    // Create View Help Text
    'LBL_HELP_CREATE' => 'A {{plural_module_name}} modul arra használható, hogy nyomon kövesse és igazítsa a termékekkel kapcsolatos, vásárlók által bejelentett problémákat. 

{{module_name}} létrehozásához: 
1. töltse ki a mezőket! 
- A kötelező mezők kitöltése nélkül mentés nem lehetséges. 
- Ha hiányzó mezőt talál, kattintson a "Több mutatása" opcióra a ráadás mezők felfedéséhez! 
2. Kattintson a "Mentés" gombra a rekord mentéséhez és a korábbi oldalra való visszatéréshez.',
    // Case deflection
    'LBL_NO_LOOKING_FOR' => 'Nem találta meg, amit keresett?',
    'LBL_CREATE_CASE' => '{{module_name}} létrehozása',

    'LBL_SLA_DETAILS' => 'SLA részletek',
    'LBL_HOURS' => 'Óra',
    'LBL_WIDGET_STATUS' => 'Állapot',

    // KPI Metrics
    'LBL_ORGANIZE' => 'Rendezés',
    'LBL_CREATE_NEW' => 'Új létrehozása',
    'LBL_MANAGE' => 'Kezelés',
    'LBL_SEE_DETAILS' => 'Részletek megtekintése',
    'LBL_HIDE_NEW' => 'Elrejt',

    //For Kiosk labels
    'LBL_KIOSK_MODULE_NAME' => 'Esetek',
    'LBL_KIOSK_MODULE_NAME_SINGULAR' => 'Eset',
    'LBL_KIOSK_CREATE' => 'Eset létrehozása',
    'LBL_KIOSK_SUBJECT' => 'Tárgy',
    'LBL_KIOSK_CASE_ID' => 'Eset azonosító',
    'LBL_KIOSK_STATUS' => 'Állapot',
    'LBL_KIOSK_CATEGORY' => 'Kategória',
    'LBL_KIOSK_SUBMITTED_BY' => 'Beküldve ekkor: {{date}}',
    'LBL_KIOSK_PRIORITY' => 'Prioritás',
    'LBL_KIOSK_CREATE_CASE' => 'Vegye fel a kapcsolatot velünk',
    'LBL_KIOSK_DESCRIPTION' => 'Leírás',
    'LBL_KIOSK_CREATE_SUCCESS' => 'A kérése sikeresne beküldésre került.',
    'LBL_KIOSK_ATTACHED' => 'Minden csatolmány',

    // External Users Subpanel
    'LBL_EXTERNAL_USERS_SUBPANEL_TITLE' => 'Külső felhasználók',
    'LBL_ESCALATED_CASES' => 'Eszkalált esetek',
];
