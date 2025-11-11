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
    'LBL_CASES_LIST_DASHBOARD' => 'Falllisten-Dashboard',
    'LBL_CASES_RECORD_DASHBOARD' => 'Fallbericht-Dashboard',
    'LBL_CASES_MULTI_LINE_DASHBOARD' => 'Fokus-Drawer für Fälle - Konsole',
    'LBL_CASES_FOCUS_DRAWER_DASHBOARD' => 'Fokus-Drawer für Tickets',
    'LBL_CLAIM_AND_CLOSE' => 'Schaltfläche "Fallaktion"',
    'LBL_ACTION_CLAIM' => 'Aktion Anfordern',
    'LBL_ACTION_CLOSE' => 'Aktion Schließen',
    'LBL_ACTION_UNASSIGN' => 'Aktion Zuweisung aufheben',
    'LBL_CLAIM_BUTTON_TEXT' => 'Anfordern',
    'LBL_CLOSE_BUTTON_TEXT' => 'Schließen',
    'LBL_UNASSIGN_BUTTON_TEXT' => 'Zuweisung aufheben',

    'ERR_DELETE_RECORD' => 'Zum Löschen der Firma muss eine Datensatznummer angegeben werden.',
    'LBL_ACCOUNT_ID' => 'Firmen-ID',
    'LBL_ACCOUNT_NAME' => 'Firmenname:',
    'LBL_ACCOUNTS_SUBPANEL_TITLE' => 'Firmen',
    'LBL_ACTIVITIES_SUBPANEL_TITLE' => 'Aktivitäten',
    'LBL_ATTACH_NOTE' => 'Notiz anhängen',
    'LBL_BUGS_SUBPANEL_TITLE' => 'Fehler',
    'LBL_CASE_NUMBER' => 'Ticket-Nummer:',
    'LBL_CASE_SUBJECT' => 'Betreff:',
    'LBL_CASE' => 'Ticket:',
    'LBL_CONTACT_CASE_TITLE' => 'Kontakt-Ticket:',
    'LBL_CONTACT_NAME' => 'Kontaktname:',
    'LBL_CONTACT_ROLE' => 'Rolle:',
    'LBL_CONTACTS_SUBPANEL_TITLE' => 'Kontakte',
    'LBL_DEFAULT_SUBPANEL_TITLE' => 'Tickets',
    'LBL_DESCRIPTION' => 'Beschreibung:',
    'LBL_FILENANE_ATTACHMENT' => 'Dateianhang',
    'LBL_HISTORY_SUBPANEL_TITLE' => 'Verlauf',
    'LBL_INVITEE' => 'Kontakte',
    'LBL_MEMBER_OF' => 'Firma',
    'LBL_MODULE_NAME' => 'Tickets',
    'LBL_MODULE_NAME_SINGULAR' => 'Ticket',
    'LBL_MODULE_TITLE' => 'Tickets: Startseite',
    'LBL_NEW_FORM_TITLE' => 'Neues Ticket',
    'LBL_NUMBER' => 'Nummer:',
    'LBL_PRIORITY' => 'Priorität:',
    'LBL_PROJECTS_SUBPANEL_TITLE' => 'Projekte',
    'LBL_DOCUMENTS_SUBPANEL_TITLE' => 'Dokumente',
    'LBL_RESOLUTION' => 'Lösung:',
    'LBL_SEARCH_FORM_TITLE' => 'Ticket-Suche',
    'LBL_STATUS' => 'Status:',
    'LBL_SOURCE' => 'Quelle:',
    'LBL_SUBJECT' => 'Betreff:',
    'LBL_LIST_ACCOUNT_NAME' => 'Firmenname',
    'LBL_LIST_ASSIGNED' => 'Zugewiesen an',
    'LBL_LIST_CLOSE' => 'Schließen',
    'LBL_LIST_FORM_TITLE' => 'Ticket-Liste',
    'LBL_LIST_LAST_MODIFIED' => 'Geändert am',
    'LBL_LIST_MY_CASES' => 'Meine offenen Tickets',
    'LBL_LIST_NUMBER' => 'Num.',
    'LBL_LIST_PRIORITY' => 'Priorität',
    'LBL_LIST_STATUS' => 'Status',
    'LBL_LIST_SUBJECT' => 'Betreff',
    'LBL_LIST_ASSIGNED_TO_NAME' => 'Zugew. Benutzer',
    'LBL_SHOW_MORE' => 'Weitere Tickets anzeigen',
    'LNK_CASE_LIST' => 'Tickets anzeigen',
    'LNK_NEW_CASE' => 'Neues Tickets',
    'LBL_CASE_FROM_TWITTER_TITLE' => 'Tweet',
    'LNK_CREATE' => 'Neues Tickets',
    'LNK_CREATE_WHEN_EMPTY' => 'Erstellen Sie jetzt ein Ticket.',
    'NTC_REMOVE_FROM_BUG_CONFIRMATION' => 'Möchten Sie dieses Ticket wirklich von dem Fehlereintrag entfernen?',
    'NTC_REMOVE_INVITEE' => 'Möchten Sie diesen Kontakt wirklich von diesem Ticket entfernen?',
    'LBL_LIST_DATE_CREATED' => 'Erstellungsdatum',
    'LBL_ASSIGNED_TO_NAME' => 'Zugewiesen an',
    'LBL_TYPE' => 'Typ',
    'LBL_WORK_LOG' => 'Arbeitsprotokoll',
    'LNK_IMPORT_CASES' => 'Tickets importieren',
    'LNK_CASE_REPORTS' => 'Ticketberichte anzeigen',
    'LBL_SHOW_IN_PORTAL' => 'Im Portal anzeigen',
    'LBL_CREATE_KB_DOCUMENT' => 'Artikel erstellen',
    'LBL_KBCONTENTS_SUBPANEL_TITLE' => 'Wissensdatenbank',
    'LBL_REQUEST_CLOSE' => 'Abschluss anfordern',
    'LBL_REQUEST_CLOSE_DATE' => 'Abschlussdatum anfordern',
    'LBL_REQUEST_CLOSE_LABEL' => 'Zum Abschluss anfordern',
    'LBL_REQUEST_CLOSE_TOOLTIP' => 'Eine Anforderung zum Abschluss des Falls senden',
    'LBL_REQUEST_CLOSE_MESSAGE' => 'Möchten Sie anfordern, dass dieser Fall abgeschlossen wird?',
    'LBL_DASHLET_REQUESTED_CLOSE_CASES' => 'Meine zum Abschluss angeforderten Fälle',
    'LBL_MY_REQUESTED_CLOSE_CASES' => 'Meine zum Abschluss angeforderten Fälle',
    'LBL_DASHLET_MY_REQUESTED_CLOSE_CASES_NAME' => 'Meine zum Abschluss angeforderten Fälle',
    'LBL_DASHLET_REQUESTED_CLOSE_CASES_NAME' => 'Liste der zum Abschluss angeforderten Fälle',
    'LBL_DASHLET_REQUESTED_CLOSE_CASES_DESCRIPTION' => 'Zeigt eine Liste der zugewiesenen Fälle an, die zum Abschluss angefordert werden',

    'LBL_CREATED_USER' => 'Erstellt von',
    'LBL_MODIFIED_USER' => 'Geändert von',
    'LBL_PROJECT_SUBPANEL_TITLE' => 'Projekte',
    'LBL_CASE_INFORMATION' => 'Überblick',

    //For export labels
    'LBL_MODIFIED_BY_NAME_OWNER' => 'Geändert von',
    'LBL_MODIFIED_BY_NAME_MOD' => 'Geändert von',
    'LBL_CREATED_BY_NAME_OWNER' => 'Ersteller',
    'LBL_CREATED_BY_NAME_MOD' => 'Ersteller',
    'LBL_ASSIGNED_USER_NAME_OWNER' => 'Zugewiesen an',
    'LBL_ASSIGNED_USER_NAME_MOD' => 'Zugewiesen an',
    'LBL_TEAM_COUNT_OWNER' => 'Team Count Owner',
    'LBL_TEAM_COUNT_MOD' => 'Team Count Mod',
    'LBL_TEAM_NAME_OWNER' => 'Team Name Owner',
    'LBL_TEAM_NAME_MOD' => 'Team Name Mod',
    'LBL_ACCOUNT_NAME_OWNER' => 'Inhaber Firmenname',
    'LBL_ACCOUNT_NAME_MOD' => 'Firmenname Mod',
    'LBL_MODIFIED_USER_NAME' => 'Geändert von',
    'LBL_MODIFIED_USER_NAME_OWNER' => 'Geändert von',
    'LBL_MODIFIED_USER_NAME_MOD' => 'Geändert von',
    'LBL_PORTAL_VIEWABLE' => 'Im Portal sichtbar',
    'LBL_EXPORT_ASSIGNED_USER_ID' => 'Zugewiesene Benutzer-ID',
    'LBL_EXPORT_MODIFIED_USER_ID' => 'Bearbeiter:',
    'LBL_EXPORT_CREATED_BY' => 'Ersteller',
    'LBL_EXPORT_CREATED_BY_NAME' => 'Erstellt von',
    'LBL_EXPORT_ASSIGNED_USER_NAME' => 'Zugewiesener Benutzer',
    'LBL_EXPORT_TEAM_COUNT' => 'Team-Anzahl',
    'LBL_CHANGETIMERS' => 'Zeitmesser ändern',
    'LBL_PURCHASES_SUBPANEL_TITLE' => 'Einkäufe',

    // SNIP
    'LBL_CONTACT_HISTORY_SUBPANEL_TITLE' => 'Verknüpfte Kontakt-E-Mails',

    // Portal Tutorial content
    'LBL_PORTAL_TOUR_RECORDS_INTRO' => 'Das Modul "Tickets" dient zum Verwalten von Supportfällen, die Ihre Firma betreffen. Verwenden Sie die Pfeiltasten, um an einer kurzen Tour teil zu nehmen.',
    'LBL_PORTAL_TOUR_RECORDS_PAGE' => 'Diese Seite zeigt eine Liste von Tickets, die Ihnen zugewiesen sind.',
    'LBL_PORTAL_TOUR_RECORDS_FILTER' => 'Sie können die Liste der Tickets anhand von Suchbegriffen filtern.',
    'LBL_PORTAL_TOUR_RECORDS_FILTER_EXAMPLE' => 'Zum Beispiel können Sie hiermit ein Ticket finden, das vor kurzer Zeit erstellt wurde.',
    'LBL_PORTAL_TOUR_RECORDS_CREATE' => 'Haben Sie ein neues Ticket, das Sie erstellen möchten? Klicken Sie einfach hier.',
    'LBL_PORTAL_TOUR_RECORDS_RETURN' => 'Hier Klicken führt Sie jederzeit zurück zu dieser Ansicht.',
    'TPL_ACTIVITY_TIMELINE_DASHLET' => 'Ticket-Zeitleiste',

    'LBL_NOTES_SUBPANEL_TITLE' => 'Notizen',

    // Help Text
    // List View Help Text
    'LBL_HELP_RECORDS' => 'Das Modul {{plural_module_name}} wird verwendet, um alle mit dem Produkt und dem Service relevanten Probleme zu verwalten, die von Ihren Kunden gemeldet werden. {{plural_module_name}} sind typischerweise mit einem {{accounts_singular_module}} verknüpft. Eine {{accounts_singular_module}} kann viele {{plural_module_name}} haben. Um {{plural_module_name}} anzulegen, gibt es verschiedene Möglichkeiten, z. B. Importieren von {{plural_module_name}} oder Konvertieren einer E-Mail. Sobald der {{module_name}} erstellt ist, können Informationen in der Datenansicht eingesehen und editiert werden. Jeder {{module_name}} kann mit einem Sugar-Datensatz wie {{calls_module}}, {{contacts_module}}, {{bugs_module}} etc. verknüpft werden.',

    // Record View Help Text
    'LBL_HELP_RECORD' => 'Das Modul {{plural_module_name}} wird verwendet, um alle für ein Produkt und einen Service relevanten Probleme Ihren Kunden zu verwalten.

- Bearbeiten Sie den Datensatz, indem sie auf ein Feld oder auf die Schaltfläche "Bearbeiten" klicken.
- Sehen Sie sich Links zu anderen Datensätzen an oder ändern Sie diese durch Konfigurieren des unteren linken Bereichs auf "Datenansicht".
- Erstellen Sie Benutzerkommentare oder sehen Sie sich diese an bzw. zeichnen Sie die Änderungshistorie im Modul {{activitystream_singular_module}} auf, indem Sie den linken unteren Bereich auf "Aktivitäts-Stream" konfigurieren.
- Folgen Sie diesem Datensatz oder markieren Sie ihn als Favorit, indem Sie die Symbole rechts neben dem Namen verwenden. 
- Weitere Aktionen finden Sie im Dropdown-Menü rechts neben der Schaltfläche "Bearbeiten".',

    // Create View Help Text
    'LBL_HELP_CREATE' => 'Das Modul {{plural_module_name}} wird verwendet, um alle für ein Produkt oder einen Service relevanten Probleme Ihren Kunden zu verwalten.

So erstellen Sie ein {{module_name}}:
1. Erfassen Sie alle relevanten Informationen. 
- Felder, die als Pflichtfeld markiert sind, müssen vor dem Speichern ausgefüllt werden. 
- Klicken Sie auf "Mehr anzeigen", um weitere Felder zu erfassen. 
2. Klicken Sie auf "Speichern", um den Datensatz fertigzustellen und auf die letzte Seite zurück zu kommen.',
    // Case deflection
    'LBL_NO_LOOKING_FOR' => 'Haben Sie nicht gefunden, wonach Sie suchen?',
    'LBL_CREATE_CASE' => 'Erstellen Sie einen {{module_name}}',

    'LBL_SLA_DETAILS' => 'SLA-Details',
    'LBL_HOURS' => 'Stunden',
    'LBL_WIDGET_STATUS' => 'Status',

    // KPI Metrics
    'LBL_ORGANIZE' => 'Organisieren',
    'LBL_CREATE_NEW' => 'Neu erstellen',
    'LBL_MANAGE' => 'Verwalten',
    'LBL_SEE_DETAILS' => 'Details anzeigen',
    'LBL_HIDE_NEW' => 'Ausblenden',

    //For Kiosk labels
    'LBL_KIOSK_MODULE_NAME' => 'Fälle',
    'LBL_KIOSK_MODULE_NAME_SINGULAR' => 'Fall',
    'LBL_KIOSK_CREATE' => 'Fall erstellen',
    'LBL_KIOSK_SUBJECT' => 'Betreff',
    'LBL_KIOSK_CASE_ID' => 'Fall-ID',
    'LBL_KIOSK_STATUS' => 'Status',
    'LBL_KIOSK_CATEGORY' => 'Kategorie',
    'LBL_KIOSK_SUBMITTED_BY' => 'Abgesendet am {{date}}',
    'LBL_KIOSK_PRIORITY' => 'Priorität',
    'LBL_KIOSK_CREATE_CASE' => 'Kontaktieren Sie uns',
    'LBL_KIOSK_DESCRIPTION' => 'Beschreibung',
    'LBL_KIOSK_CREATE_SUCCESS' => 'Ihre Anfrage wurde erfolgreich übermittelt.',
    'LBL_KIOSK_ATTACHED' => 'Alles beigefügt',

    // External Users Subpanel
    'LBL_EXTERNAL_USERS_SUBPANEL_TITLE' => 'Externe Benutzer',
    'LBL_ESCALATED_CASES' => 'Eskalierte Fälle',
];
