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
    'LBL_HOMEPAGE_TITLE' => 'Moje szablony Smart Guide',
    'LBL_LIST_FORM_TITLE' => 'Lista szablonów Smart Guide',
    'LNK_IMPORT_CUSTOMER_JOURNEY_TEMPLATES' => 'Importuj szablony Smart Guide',
    'LBL_MODULE_TITLE' => 'Szablony Smart Guide',
    'LBL_MODULE_NAME' => 'Szablony Smart Guide',
    'LBL_NEW_FORM_TITLE' => 'Nowy szablon Smart Guide',
    'LBL_REMOVE' => 'Usuń',
    'LBL_SEARCH_FORM_TITLE' => 'Wyszukaj szablony Smart Guide',
    'LBL_TYPE' => 'Typ',
    'LNK_LIST' => 'Szablony Smart Guide',
    'LNK_NEW_RECORD' => 'Utwórz szablon Smart Guide',
    'LBL_COPIES' => 'Kopie',
    'LBL_COPIED_TEMPLATE' => 'Skopiowany szablon',
    'LBL_IMPORT_TEMPLATES_BUTTON_LABEL' => 'Importuj szablony',
    'LBL_IMPORT_TEMPLATES_SUCCESS_MESSAGE' => 'Szablony zostały zaimportowane.',
    'LBL_RESAVE_TEMPLATES_BUTTON_LABEL' => 'Pon. zapisz szablony',
    'LBL_RESAVE_TEMPLATES_SUCCESS_MESSAGE' => 'Szablony zostały ponownie zapisane.',
    'LNK_VIEW_RECORDS' => 'Wyświetl szablony Smart Guide',
    'LNK_DRI_WORKFLOW_TEMPLATE_LIST' => 'Wyświetl szablony Smart Guide',
    'LBL_AVAILABLE_MODULES' => 'Dostępne moduły',
    'LBL_CANCEL_ACTION' => 'Anuluj czynność',
    'LBL_NOT_APPLICABLE_ACTION' => 'Czynność dot. działań Nie dotyczy',
    'LBL_POINTS' => 'Punkty',
    'LBL_RELATED_ACTIVITIES' => 'Powiązane działania',
    'LBL_ACTIVE' => 'Aktywne',
    'LBL_ASSIGNEE_RULE' => 'Reguła osoby przydzielonej',
    'LBL_TARGET_ASSIGNEE' => 'Docelowa osoba przydzielona',
    'LBL_STAGE_NUMBERS' => 'Numerowanie etapów',
    'LBL_EXPORT_BUTTON_LABEL' => 'Eksportuj',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_BUTTON_LABEL' => 'Importuj',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_TEXT' => 'Automatycznie utwórz/aktualizuj nowy rekord szablonu Smart Guide, importując plik *.json z systemu plików.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_CREATE_SUCCESS' => 'Pomyślnie utworzono szablon <a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a>.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_UPDATE_SUCCESS' => 'Pomyślnie zaktualizowano szablon <a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a>.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_DUPLICATE_NAME_ERROR' => 'Import nie powiódł się. Szablon o nazwie „<a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a>” już istnieje. Zmień nazwę importowanego rekordu i spróbuj ponownie lub użyj funkcji „Kopiuj”, aby utworzyć duplikat szablonu Smart Guide.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_UPDATE_CONFIRM' => 'Istnieje już szablon o takim ID. Aby zaktualizować istniejący szablon, kliknij przycisk <b>Potwierdź</b>. W celu zamknięcia bez wprowadzania zmian w istniejącym szablonie kliknij przycisk <b>Anuluj</b>.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_TEMPLATE_DELETED' => 'Szablon, który chcesz zaimportować, został usunięty w bieżącym wystąpieniu.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_EMPTY_WARNING' => 'Wybierz prawidłowy plik *.json.',
    'LBL_CHECKING_IMPORT_UPLOAD' => 'Walidacja',
    'LBL_IMPORTING_TEMPLATE' => 'Importowanie',
    'LBL_DISABLED_STAGE_ACTIONS' => 'Wyłączone czynności etapu',
    'LBL_DISABLED_ACTIVITY_ACTIONS' => 'Wyłączone czynności działania',
    'LBL_FORMS' => 'Formularze',
    'LBL_ACTIVE_LIMIT' => 'Aktywny limit Smart Guide',
    'LBL_WEB_HOOKS' => 'Elementy webhook',
    'LBL_START_NEXT_JOURNEY_ACTIVITIES' => 'Działanie początkowe następnego Smart Guide',
    'LBL_START_NEXT_JOURNEY_STAGES' => 'Łącze etapu rozpoczęcia następnego Smart Guide',
    'LBL_SMART_GUIDE_ACCESSIBLE' => 'Wybierz moduły, w których Smart Guide będzie dostępny',
    'LBL_SMART_GUIDE_MODIFY_ACTIONS' => 'W etapie możesz dodać więcej działań lub je z niego usunąć. Możesz też wyłączyć czynności w tym Smart Guide, do których chcesz zabronić użytkownikowi dostępu',
    'LBL_SMART_GUIDE_DISABLE_ACTIONS' => 'W działaniu możesz dodać więcej działań jako elementy podrzędne. Możesz też wyłączyć czynności w tym Smart Guide, do których chcesz zabronić użytkownikowi dostępu',
    'LBL_SMART_GUIDE_ACTIVATES' => 'Liczba Smart Guide, które mogą być aktywne w rekordzie równocześnie',
    'LBL_SMART_GUIDE_TARGET_ASSIGNEE' => 'Jeśli opcja jest zaznaczona i opcja Docelowa osoba przydzielona jest taka sama jak Nadrzędna osoba przydzielona, po zmianie użytkownika „Przydzielono do” w elemencie nadrzędnym zostaną również automatycznie zmienieni użytkownicy „Przydzielono do” w Smart Guide, etapach i działaniach. Pamiętaj, że ustawienie opcji Docelowa osoba przydzielona z szablonów działania ma wyższy priorytet niż to ustawienie z szablonu Smart Guide',
    'LBL_SMART_GUIDE_USER_ASSIGNED' => 'Moment przydzielania użytkownika do działań',
    'LBL_SMART_GUIDE_ACTIVITIES_ASSIGNED' => 'Osoby przydzielane do działań',
    'LBL_SMART_GUIDE_STAGE_NUMBERS' => 'Ten przełącznik umożliwia pokazywanie lub ukrywanie automatycznego numerowania etapów.',
    'CJ_FORMS_LBL_PARENT_NAME' => 'Szablon Smart Guide / etapu / działania',
];
