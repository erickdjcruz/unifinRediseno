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
    'LBL_OPPORTUNITIES_LIST_DASHBOARD' => 'Verkaufschancenlisten-Dashboard',
    'LBL_OPPORTUNITIES_RECORD_DASHBOARD' => 'Verkaufschancenbericht-Dashboard',
    'LBL_OPPORTUNITIES_MULTI_LINE_DASHBOARD' => 'Fokus-Drawer für Verkaufschancen - Konsole',
    'LBL_OPPORTUNITIES_FOCUS_DRAWER_DASHBOARD' => 'Fokus-Drawer für Verkaufschancen',
    'LBL_RENEWAL_OPPORTUNITY' => 'Erneuerung der Verkaufschance',

    'LBL_MODULE_NAME' => 'Verkaufschancen',
    'LBL_MODULE_NAME_SINGULAR' => 'Verkaufschance',
    'LBL_MODULE_TITLE' => 'Verkaufschancen: Startseite',
    'LBL_SEARCH_FORM_TITLE' => 'Verkaufschancen-Suche',
    'LBL_VIEW_FORM_TITLE' => 'Verkaufschancen-Ansicht',
    'LBL_LIST_FORM_TITLE' => 'Verkaufschancen-Liste',
    'LBL_OPPORTUNITY_NAME' => 'Verkaufschance-Name:',
    'LBL_OPPORTUNITY' => 'Verkaufschance:',
    'LBL_NAME' => 'Verkaufschance-Name',
    'LBL_TIME' => 'Zeit',
    'LBL_INVITEE' => 'Kontakte',
    'LBL_CURRENCIES' => 'Währungen',
    'LBL_LIST_OPPORTUNITY_NAME' => 'Name',
    'LBL_LIST_ACCOUNT_NAME' => 'Firmenname',
    'LBL_LIST_DATE_CLOSED' => 'Schließen',
    'LBL_LIST_AMOUNT' => 'Wahrscheinlich',
    'LBL_LIST_AMOUNT_USDOLLAR' => 'Konvertierter Betrag',
    'LBL_ACCOUNT_ID' => 'Firmen-ID',
    'LBL_CURRENCY_RATE' => 'Wechselkurs',
    'LBL_CURRENCY_ID' => 'Währungs-ID',
    'LBL_CURRENCY_NAME' => 'Währungsname',
    'LBL_CURRENCY_SYMBOL' => 'Währungssymbol',
//DON'T CONVERT THESE THEY ARE MAPPINGS
    'db_sales_stage' => 'LBL_LIST_SALES_STAGE',
    'db_name' => 'LBL_NAME',
    'db_amount' => 'LBL_LIST_AMOUNT',
    'db_date_closed' => 'LBL_LIST_DATE_CLOSED',
//END DON'T CONVERT
    'UPDATE' => 'Währung aktualisieren',
    'UPDATE_DOLLARAMOUNTS' => 'Euro-Beträge aktualisieren',
    'UPDATE_VERIFY' => 'Beträge überprüfen',
    'UPDATE_VERIFY_TXT' => 'Überprüft, ob alle angegebenen Werte gültige Dezimalwerte sind (bestehend aus den Zahlen 0 - 9 und dem Dezimaltrennzeichen)',
    'UPDATE_FIX' => 'Beträge korrigieren',
    'UPDATE_FIX_TXT' => 'Versucht, ungültige Beträge über das Setzen korrekter Dezimalzeichen zu korrigieren. Für jeden geänderten Betrag existiert eine Sicherungskopie im Datenbankfeld "amount_backup". Falls Sie diese Funktion aufrufen und Fehler feststellen, müssen Sie vor einem erneuten Versuch erst die alten Beträge, die sich im Backup befinden, wieder herstellen, da ansonsten Ihre ursprünglichen Einträge in der Datenbank mit den fehlerhaften Beträgen überschrieben werden.',
    'UPDATE_DOLLARAMOUNTS_TXT' => 'Hier werden die Beträge der Angebote basierend auf dem angegebenen Wechselkurs neu berechnet. Diese Werte werden für Grafiken und Währungstabellen herangezogen.',
    'UPDATE_CREATE_CURRENCY' => 'Neue Währung:',
    'UPDATE_VERIFY_FAIL' => 'Der Datensatz konnte nicht verifiziert werden:',
    'UPDATE_VERIFY_CURAMOUNT' => 'Aktueller Betrag:',
    'UPDATE_VERIFY_FIX' => 'Durch Reparation berichtigter Betrag wäre',
    'UPDATE_INCLUDE_CLOSE' => 'Auch abgeschlossenen Angebote überprüfen',
    'UPDATE_VERIFY_NEWAMOUNT' => 'Neuer Betrag:',
    'UPDATE_VERIFY_NEWCURRENCY' => 'Neue Währung:',
    'UPDATE_DONE' => 'Fertig',
    'UPDATE_BUG_COUNT' => 'Gefundene Fehler, deren Behebung versucht wurde:',
    'UPDATE_BUGFOUND_COUNT' => 'Gefundene Fehler:',
    'UPDATE_COUNT' => 'Bearbeitete Einträge:',
    'UPDATE_RESTORE_COUNT' => 'Wiederhergestellte Beträge:',
    'UPDATE_RESTORE' => 'Betrag wiederherstellen',
    'UPDATE_RESTORE_TXT' => 'Stellt die Beträge wieder her, die während der Reparatur gesichert wurden.',
    'UPDATE_FAIL' => 'Update konnte nicht durchgeführt werden -',
    'UPDATE_NULL_VALUE' => 'Betragsfeld ist leer und wird deshalb auf 0 gesetzt -',
    'UPDATE_MERGE' => 'Währungen zusammenführen',
    'UPDATE_MERGE_TXT' => 'Zusammenführen mehrerer Währungen. Falls Sie feststellen, dass mehrere Einträge mit der gleichen Währung vorhanden sind, können Sie diese zusammenführen. Dies gilt analog für alle anderen Module.',
    'LBL_ACCOUNT_NAME' => 'Firmenname:',
    'LBL_CURRENCY' => 'Währung',
    'LBL_DATE_CLOSED' => 'Abschluss geplant:',
    'LBL_DATE_CLOSED_TIMESTAMP' => 'Zeitstempel des erwarteten Abschlusses',
    'LBL_TYPE' => 'Typ:',
    'LBL_CAMPAIGN' => 'Kampagne:',
    'LBL_NEXT_STEP' => 'Nächster Schritt:',
    'LBL_SERVICE_START_DATE' => 'Service-Startdatum',
    'LBL_LEAD_SOURCE' => 'Quelle:',
    'LBL_SALES_STAGE' => 'Verkaufsphase:',
    'LBL_SALES_STATUS' => 'Status',
    'LBL_PROBABILITY' => 'Wahrscheinlichkeit (%):',
    'LBL_DESCRIPTION' => 'Beschreibung:',
    'LBL_DUPLICATE' => 'Mögliches Verkaufschancen-Duplikat',
    'MSG_DUPLICATE' => 'Die Verkaufschance, die Sie gerade erstellen, könnte ein Duplikat einer bereits bestehenden Verkaufschance sein. Verkaufschancen mit ähnlichen Namen sind unten aufgeführt.<br>Klicken Sie auf "Speichern", um den Vorgang fortzusetzen, oder auf "Abbrechen", um zum Modul zurückzukehren, ohne die Verkaufschance zu erstellen.',
    'LBL_NEW_FORM_TITLE' => 'Neue Verkaufschance',
    'LNK_NEW_OPPORTUNITY' => 'Neue Verkaufschance',
    'LNK_CREATE' => 'Neues Ticket',
    'LNK_OPPORTUNITY_LIST' => 'Verkaufschancen anzeigen',
    'ERR_DELETE_RECORD' => 'Die Datensatznummer muss angegeben werden, sonst kann der Datensatz nicht gelöscht werden.',
    'LBL_TOP_OPPORTUNITIES' => 'Liste der Top-Verkaufschancen',
    'NTC_REMOVE_OPP_CONFIRMATION' => 'Möchten Sie diesen Kontakt wirklich aus dieser Verkaufschance entfernen?',
    'OPPORTUNITY_REMOVE_PROJECT_CONFIRM' => 'Möchten Sie dieses Verkaufschance wirklich von diesem Projekt entfernen?',
    'LBL_DEFAULT_SUBPANEL_TITLE' => 'Verkaufschancen',
    'LBL_ACTIVITIES_SUBPANEL_TITLE' => 'Aktivitäten',
    'LBL_HISTORY_SUBPANEL_TITLE' => 'Verlauf',
    'LBL_RAW_AMOUNT' => 'Ges. Summe',
    'LBL_LEADS_SUBPANEL_TITLE' => 'Interessenten',
    'LBL_CONTACTS_SUBPANEL_TITLE' => 'Kontakte',
    'LBL_DOCUMENTS_SUBPANEL_TITLE' => 'Dokumente',
    'LBL_PROJECTS_SUBPANEL_TITLE' => 'Projekte',
    'LBL_ASSIGNED_TO_NAME' => 'Zugewiesen an:',
    'LBL_LIST_ASSIGNED_TO_NAME' => 'Zugew. Benutzer',
    'LBL_LIST_SALES_STAGE' => 'Verkaufsphase',
    'LBL_MY_CLOSED_OPPORTUNITIES' => 'Meine geschlossenen Verkaufschancen',
    'LBL_TOTAL_OPPORTUNITIES' => 'Summe Verkaufschancen',
    'LBL_CLOSED_WON_OPPORTUNITIES' => 'Gewonnene Verkaufschancen',
    'LBL_ASSIGNED_TO_ID' => 'Bearbeiter',
    'LBL_CREATED_ID' => 'Ersteller',
    'LBL_MODIFIED_ID' => 'Geändert von ID',
    'LBL_MODIFIED_NAME' => 'Geändert vonnamen',
    'LBL_CREATED_USER' => 'Erstellt von',
    'LBL_MODIFIED_USER' => 'Geändert von',
    'LBL_CAMPAIGN_OPPORTUNITY' => 'Kampagnen',
    'LBL_PROJECT_SUBPANEL_TITLE' => 'Projekte',
    'LABEL_PANEL_ASSIGNMENT' => 'Aufgabe',
    'LNK_IMPORT_OPPORTUNITIES' => 'Verkaufschancen importieren',
    'LBL_EDITLAYOUT' => 'Layout bearbeiten' /*for 508 compliance fix*/,
    //For export labels
    'LBL_EXPORT_CAMPAIGN_ID' => 'Kampagnen-ID',
    'LBL_OPPORTUNITY_TYPE' => 'Verkaufschancentyp',
    'LBL_EXPORT_ASSIGNED_USER_NAME' => 'Zugewiesener Benutzer',
    'LBL_EXPORT_ASSIGNED_USER_ID' => 'Zugewiesene Benutzer-ID',
    'LBL_EXPORT_MODIFIED_USER_ID' => 'Bearbeiter:',
    'LBL_EXPORT_CREATED_BY' => 'Erstellt von ID',
    'LBL_EXPORT_NAME' => 'Name',
    // SNIP
    'LBL_CONTACT_HISTORY_SUBPANEL_TITLE' => 'Verknüpfte Kontakt-E-Mails',
    'LBL_FILENAME' => 'Anlage',
    'LBL_PRIMARY_QUOTE_ID' => 'Primäres Angebot',
    'LBL_CONTRACTS' => 'Verträge',
    'LBL_CONTRACTS_SUBPANEL_TITLE' => 'Verträge',
    'LBL_PRODUCTS' => 'Produkte',
    'LBL_RLI' => 'Umsatzposten',
    'LNK_OPPORTUNITY_REPORTS' => 'Verkaufschancen-Berichte',
    'LBL_QUOTES_SUBPANEL_TITLE' => 'Angebote',
    'LBL_TEAM_ID' => 'Team-ID',
    'LBL_TIMEPERIODS' => 'Zeitspannen',
    'LBL_TIMEPERIOD_ID' => 'Zeitspannen-ID',
    'LBL_COMMITTED' => 'Übergeben',
    'LBL_FORECAST' => 'In Prognose inkludieren',
    'LBL_COMMIT_STAGE' => 'Bestätigungsstatus',
    'LBL_COMMIT_STAGE_FORECAST' => 'Prognose-Stufe',
    'LBL_WORKSHEET' => 'Arbeitsblatt',
    'LBL_PURCHASED_LINE_ITEMS' => 'Gekaufte Produkte',

    // KPI Metrics
    'LBL_ORGANIZE' => 'Organisieren',
    'LBL_CREATE_NEW' => 'Neu erstellen',
    'LBL_MANAGE' => 'Verwalten',
    'LBL_SEE_DETAILS' => 'Details anzeigen',
    'LBL_HIDE_NEW' => 'Ausblenden',

    'LBL_FORECASTED_LIKELY' => 'Wahrscheinlich prognostiziert',
    'LBL_LOST' => 'Verloren',
    'LBL_RENEWAL' => 'Erneuerung',
    'LBL_RENEWAL_OPPORTUNITIES' => 'Erneuerung der Verkaufschancen',
    'LBL_RENEWAL_PARENT' => 'Übergeordnete Verkaufschance',
    'LBL_PARENT_RENEWAL_OPPORTUNITY_ID' => 'Erneuerungs-Parent-ID',
    'LBL_MONTH_YEAR_RENEWAL' => '{{month}}, {{year}}',

    'LBL_WIDGET_SALES_STAGE' => 'Verkaufsphase',
    'LBL_WIDGET_DATE_CLOSED' => 'Schließen',
    'LBL_WIDGET_AMOUNT' => 'Betrag',

    'TPL_RLI_CREATE' => 'Eine Verkaufschance muss einen Umsatzposten zugewiesen haben.',
    'TPL_RLI_CREATE_LINK_TEXT' => 'Einen Umsatzposten erstellen.',
    'LBL_PRODUCTS_SUBPANEL_TITLE' => 'Produkte',
    'LBL_RLI_SUBPANEL_TITLE' => 'Umsatzposten',

    'LBL_TOTAL_RLIS' => '# von allen Umsatzposten',
    'LBL_CLOSED_RLIS' => '# von abgeschlossenen Umsatzposten',
    'LBL_CLOSED_WON_RLIS' => '# von abgeschlossenen Gewinn-/Umsatzposten',
    'LBL_SERVICE_OPEN_FLEX_DURATION_RLIS' => 'Anzahl der Umsatzposten mit flexibler Open Service-Dauer',
    'NOTICE_NO_DELETE_CLOSED_RLIS' => 'Sie können keine Verkaufschancen löschen, die abgeschlossene Umsatzposten beinhalten',
    'WARNING_NO_DELETE_CLOSED_SELECTED' => 'Einer oder mehrere Eintrage beinhalten abgeschlossene Umsatzposten und können nicht gelöscht werden.',
    'LBL_INCLUDED_RLIS' => '# der enthaltenen Umsatz-Einzelposten',
    'LBL_UPDATE_OPPORTUNITIES_RLIS' => 'Aktualisierung der Öffnung',
    'LBL_CASCADE_RLI_EDIT' => 'Offene Umsatzposten aktualisieren',
    'LBL_CASCADE_RLI_CREATE' => 'Für alle Umsatzposten festlegen',
    'LBL_SERVICE_START_DATE_INVALID' => 'Das Service-Startdatum darf nicht nach dem Service-Enddatum eines abgeschlossenen zusätzlichen Umsatzpostens liegen.',

    'LBL_QUOTE_SUBPANEL_TITLE' => 'Angebote',
    'LBL_FILTER_OPPORTUNITY_TEMPLATE' => 'Verkaufschancen durch ein dynamisches Konto',
    'LBL_TOP_10_OPP' => 'Top 10 der offenen Verkaufschancen',
    'LBL_DASHLET_MY_ACTIVE_OPP' => 'Dashlet: Meine aktiven Verkaufschancen',
    'LBL_MY_ACTIVE_OPP' => 'Meine aktiven Verkaufschancen',


    // Config
    'LBL_OPPS_CONFIG_VIEW_BY_LABEL' => 'Verkaufschancen-Hierarchie',
    'LBL_OPPS_CONFIG_VIEW_BY_DATE_ROLLUP' => 'Das Feld "Erwartetes Abschlussdatum" in den resultierenden Verkaufschancen-Berichten auf die frühesten oder spätesten Abschlussdaten der bestehenden Umsatzposten setzen',

    //Dashlet
    'LBL_PIPELINE_TOTAL_IS' => 'Pipeline-Summe beträgt ',

    'LBL_OPPORTUNITY_ROLE' => 'Verkaufschancen-Rolle',
    'LBL_NOTES_SUBPANEL_TITLE' => 'Notizen',
    'LBL_TAB_OPPORTUNITY' => '{{module}} Überprüfung',

    // Help Text
    'LBL_OPPS_CONFIG_ALERT' => 'Durch Klicken von "Bestätigen" löschen Sie ALLE Prognosedaten und ändern Ihre Verkaufschancen-Ansicht. Falls Sie dies nicht wünschen, klicken Sie auf Abbrechen, um zu den vorherigen Einstellungen zurückzukehren.',
    'LBL_OPPS_CONFIG_ALERT_TO_OPPS' =>
        'Durch Klicken von "Bestätigen" löschen Sie ALLE Prognosedaten und ändern Ihre Verkaufschancen-Ansicht. '
        . 'Auch ALLE Prozessdefinitionen mit einem Zielmodul von Umsatzposten werden deaktiviert. '
        . 'Wenn Sie dies nicht wünschen, klicken Sie auf "Abbrechen", um zu den vorherigen Einstellungen zurúckzukehren.',
    'LBL_OPPS_CONFIG_SALES_STAGE_1a' => 'Falls alle Umsatzposten geschlossen sind und mindestens einer gewonnen wurde,',
    'LBL_OPPS_CONFIG_SALES_STAGE_1b' => 'wird die Verkaufsphase "Verkaufschancen" auf "Gewonnen" gesetzt.',
    'LBL_OPPS_CONFIG_SALES_STAGE_2a' => 'Falls alle Umsatzposten sich in der Verkaufsphase "Verloren" befinden,',
    'LBL_OPPS_CONFIG_SALES_STAGE_2b' => 'wird die Verkaufsphase "Verkaufschancen" auf "Verloren" gesetzt.',
    'LBL_OPPS_CONFIG_SALES_STAGE_3a' => 'Falls noch Umsatzposten offen sind,',
    'LBL_OPPS_CONFIG_SALES_STAGE_3b' => 'wird die Verkaufschance mit der niedrigsten Verkaufsphase markiert.',

// BEGIN ENT/ULT

    // Opps Config - View By Opportunities
    'LBL_HELP_CONFIG_OPPS' => 'Nachdem Sie diese Änderung veranlasst haben, werden die Summierungsbemerkungen der Umsatzposten im Hintergrund erstellt. Wenn die Bemerkungen vollständig und verfügbar sind, wird eine Benachrichtigung an die E-Mail-Adresse in Ihrem Benutzerprofil gesendet. Falls Ihre Instanz für {{forecasts_module}} eingerichtet ist, sendet Sugar auch eine Benachrichtigung, wenn Ihre Berichte {{module_name}} mit dem Modul {{forecasts_module}} synchronisiert sind und für ein neues {{forecasts_module}} verfügbar sind. Bitte beachten Sie, dass Ihre Instanz auf das Senden von E-Mails über Admin > E-Mail-Einstellungen konfiguriert sein muss, damit die Benachrichtigungen gesendet werden können.',

    // Opps Config - View By Opportunities And RLIs
    'LBL_HELP_CONFIG_RLIS' => 'Nachdem Sie diese Änderung initialisiert haben, werden für jedes bestehende {{module_name}} im Hintergrund Umsatzposten-Berichte erstellt. Wenn die Umsatzposten vollständig und verfügbar sind, wird eine Benachrichtigung an die E-Mail-Adresse in Ihrem Benutzerprofil gesendet. Bitte beachten Sie, dass Ihre Instanz so konfiguriert sein muss, dass E-Mails über Admin > E-Mail-Einstellungen gesendet werden, damit die Benachrichtigung versendet werden kann.',
    // List View Help Text
    'LBL_HELP_RECORDS' => 'Das Modul {{plural_module_name}} ermöglicht es Ihnen, einzelne Verkäufe von Anfang bis Ende zu verfolgen. Jeder Datensatz in {{module_name}} stellt einen potenziellen Verkauf dar und enthält relevante Verkaufsdaten sowie andere wichtige Aufzeichnungen wie {{quotes_module}}, {{contacts_module}}etc. Ein {{module_name}} durchläuft in der Regel mehrere Verkaufsphasen, bis es entweder als "Geschlossen, Gewonnen" oder "Abgeschlossen, Verloren" markiert wird. {{plural_module_name}} kann durch den Einsatz von des {{forecasts_singular_module}} von Sugar zum Verstehen und Vorhersagen von Verkaufstrends sowie zur Fokussierung auf das Erreichen von Verkaufsquoten eingesetzt werden.',

    // Record View Help Text
    'LBL_HELP_RECORD' => 'Mit dem {{plural_module_name}}-Modul können Sie einzelne Verkäufe und die zu diesen Verkäufen gehörenden Einzelposten von Anfang bis Ende nachverfolgen. Jeder {{module_name}}-Datensatz steht für einen potenziellen Verkauf und enthält relevante Verkaufsdaten sowie Bezüge zu anderen wichtigen Datensätzen wie {{quotes_module}}, {{contacts_module}} usw.

- Bearbeiten Sie die Felder dieses Datensatzes, indem Sie auf ein einzelnes Feld oder auf die Schaltfläche „Bearbeiten“ klicken.
- Lassen Sie sich Verknüpfungen zu anderen Datensätzen in den Unterbereichen anzeigen oder ändern Sie sie, indem Sie den unteren linken Bereich auf „Datenansicht“ umschalten.
- Erstellen Sie Benutzerkommentare und zeigen Sie sie an, und erfassen Sie den Änderungsverlauf eines Datensatzes im {{activitystream_singular_module}}, indem Sie den unteren linken Bereich auf „Aktivitäten-Stream“ umschalten.
- Verfolgen oder favorisieren Sie diesen Datensatz mit den Symbolen rechts neben dem Datensatznamen.
- Weitere Aktionen finden Sie im Dropdown-Menü „Aktionen“ rechts neben der Schaltfläche „Bearbeiten“.',

    // Create View Help Text
    'LBL_HELP_CREATE' => 'Mit dem {{plural_module_name}}-Modul können Sie einzelne Verkäufe von Anfang bis Ende verfolgen. Jeder {{module_name}}-Datensatz stellt einen potenziellen Verkauf dar und enthält relevante Verkaufsdaten sowie Bezüge zu anderen wichtigen Datensätzen, wie z. B. {{quotes_module}}, {{contacts_module}} usw.

So erstellen Sie ein(e/en) {{module_name}}:
1. Geben Sie die gewünschten Werte für die Felder an.
 - Die mit „Erforderlich“ gekennzeichneten Felder müssen vor dem Speichern ausgefüllt werden.
 - Klicken Sie auf „Mehr anzeigen“, um bei Bedarf zusätzliche Felder anzuzeigen.
2. Klicken Sie auf „Speichern“, um den neuen Datensatz abzuschließen und zur vorherigen Seite zurückzukehren.',

// END ENT/ULT

    //Marketo
    'LBL_MKTO_SYNC' => 'Mit Marketo® synchronisieren;',
    'LBL_MKTO_ID' => 'Marketo Interessenten-ID',

    'LBL_DASHLET_TOP10_SALES_OPPORTUNITIES_NAME' => 'Top 10 der Verkaufschancen',
    'LBL_TOP10_OPPORTUNITIES_CHART_DESC' => 'Zeigt Top 10 der Verkaufschancen in einem Kugeldiagramm an.',
    'LBL_TOP10_OPPORTUNITIES_MY_OPP' => 'Meine Verkaufschancen',
    'LBL_TOP10_OPPORTUNITIES_MY_TEAMS_OPP' => "Verkaufschancen in meinem Team",

    'LBL_PIPELINE_ERR_CLOSED_SALES_STAGE' => '{{fieldName}} kann nicht geändert werden, da dieser {{moduleSingular}} keine Produkte hat.',
    'TPL_ACTIVITY_TIMELINE_DASHLET' => 'Verkaufschancen-Zeitleiste',

    'LBL_CASCADE_SERVICE_WARNING' => ' kann nicht über diese Umsatzposten hinweg festgelegt werden, da sie keine Services sind. Möchten Sie mit der Erstellung fortfahren?',
    'LBL_CASCADE_DURATION_WARNING' => ' kann nicht über diese Umsatzposten hinweg festgelegt werden, da ihre Dauer gesperrt ist. Möchten Sie mit der Erstellung fortfahren?',

    // AI Predict
    'LBL_AI_OPPORTUNITY_CLOSE_PREDICTION_NAME' => 'Vorhersage des Verkaufschancenabschlusses',
    'LBL_AI_OPPORTUNITY_CLOSE_PREDICTION_DESC' => 'Vorhersagedetails für eine bestimmte Verkaufschance anzeigen',
    'LBL_AI_WINRATE' => 'Gewinnrate',
    'LBL_AI_WONOPP' => 'Gewonnene Verkaufschancen',
    'LBL_AI_CLOSINGTIME' => 'Abschlusszeit',
    'LBL_AI_CLOSEDOPP' => 'Abgeschlossene Verkaufschancen',
    'LBL_AI_LEADTIMESPAN' => 'Zeit zwischen der Erstellung einer Verkaufschance und dem gewonnenen Abschluss',
];
