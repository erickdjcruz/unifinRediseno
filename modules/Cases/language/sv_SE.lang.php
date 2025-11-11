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
    'LBL_CASES_LIST_DASHBOARD' => 'Panel för fallistor',
    'LBL_CASES_RECORD_DASHBOARD' => 'Panel för fallposter',
    'LBL_CASES_MULTI_LINE_DASHBOARD' => 'Fokuslåda för boxar - konsol',
    'LBL_CASES_FOCUS_DRAWER_DASHBOARD' => 'Fokuslåda för ärenden',
    'LBL_CLAIM_AND_CLOSE' => 'Knapp för ärendeåtgärd',
    'LBL_ACTION_CLAIM' => 'Åtgärdsanspråk',
    'LBL_ACTION_CLOSE' => 'Åtgärdsavstängning',
    'LBL_ACTION_UNASSIGN' => 'Borttagning av åtgärdstilldelning',
    'LBL_CLAIM_BUTTON_TEXT' => 'Anspråk',
    'LBL_CLOSE_BUTTON_TEXT' => 'Stäng',
    'LBL_UNASSIGN_BUTTON_TEXT' => 'Ta bort tilldelning',

    'ERR_DELETE_RECORD' => 'Ett objektnummer måste specificeras för att kunna radera organisationen.',
    'LBL_ACCOUNT_ID' => 'Konto-ID',
    'LBL_ACCOUNT_NAME' => 'Organisationsnamn:',
    'LBL_ACCOUNTS_SUBPANEL_TITLE' => 'Organisationer',
    'LBL_ACTIVITIES_SUBPANEL_TITLE' => 'Aktiviteter',
    'LBL_ATTACH_NOTE' => 'Lägg till anteckning',
    'LBL_BUGS_SUBPANEL_TITLE' => 'Buggar',
    'LBL_CASE_NUMBER' => 'Ärendenummer:',
    'LBL_CASE_SUBJECT' => 'Ärendeämne:',
    'LBL_CASE' => 'Ärende:',
    'LBL_CONTACT_CASE_TITLE' => 'Kontakt-Ärende:',
    'LBL_CONTACT_NAME' => 'Kontaktnamn:',
    'LBL_CONTACT_ROLE' => 'Roll:',
    'LBL_CONTACTS_SUBPANEL_TITLE' => 'Kontakter',
    'LBL_DEFAULT_SUBPANEL_TITLE' => 'Ärenden',
    'LBL_DESCRIPTION' => 'Beskrivning:',
    'LBL_FILENANE_ATTACHMENT' => 'Filbilaga',
    'LBL_HISTORY_SUBPANEL_TITLE' => 'Historik',
    'LBL_INVITEE' => 'Kontakter',
    'LBL_MEMBER_OF' => 'Organisation',
    'LBL_MODULE_NAME' => 'Ärenden',
    'LBL_MODULE_NAME_SINGULAR' => 'Ärende',
    'LBL_MODULE_TITLE' => 'Ärenden: Hem',
    'LBL_NEW_FORM_TITLE' => 'Nytt ärende',
    'LBL_NUMBER' => 'Nummer:',
    'LBL_PRIORITY' => 'Prioritet:',
    'LBL_PROJECTS_SUBPANEL_TITLE' => 'Projekt',
    'LBL_DOCUMENTS_SUBPANEL_TITLE' => 'Dokument',
    'LBL_RESOLUTION' => 'Lösning:',
    'LBL_SEARCH_FORM_TITLE' => 'Sök ärende',
    'LBL_STATUS' => 'Status:',
    'LBL_SOURCE' => 'Källa:',
    'LBL_SUBJECT' => 'Ämne',
    'LBL_LIST_ACCOUNT_NAME' => 'Organisationsnamn',
    'LBL_LIST_ASSIGNED' => 'Tilldelad till',
    'LBL_LIST_CLOSE' => 'Stäng',
    'LBL_LIST_FORM_TITLE' => 'Lista ärende',
    'LBL_LIST_LAST_MODIFIED' => 'Senast ändrad',
    'LBL_LIST_MY_CASES' => 'Mina öppna ärenden',
    'LBL_LIST_NUMBER' => 'Nr.',
    'LBL_LIST_PRIORITY' => 'Prioritet',
    'LBL_LIST_STATUS' => 'Status',
    'LBL_LIST_SUBJECT' => 'Ämne',
    'LBL_LIST_ASSIGNED_TO_NAME' => 'Tilldelad användare',
    'LBL_SHOW_MORE' => 'Visa Fler Ärenden',
    'LNK_CASE_LIST' => 'Ärenden',
    'LNK_NEW_CASE' => 'Skapa ärende',
    'LBL_CASE_FROM_TWITTER_TITLE' => 'Tweet',
    'LNK_CREATE' => 'Skapa ärende',
    'LNK_CREATE_WHEN_EMPTY' => 'Skapa nytt protokoll nu.',
    'NTC_REMOVE_FROM_BUG_CONFIRMATION' => 'Är du säker på att du vill ta bort ärendet från buggen?',
    'NTC_REMOVE_INVITEE' => 'Är du säker på att du vill ta bort kontakten från ärendet?',
    'LBL_LIST_DATE_CREATED' => 'Datum skapat',
    'LBL_ASSIGNED_TO_NAME' => 'Tilldelad till',
    'LBL_TYPE' => 'Typ',
    'LBL_WORK_LOG' => 'Arbetslogg',
    'LNK_IMPORT_CASES' => 'Importera ärenden',
    'LNK_CASE_REPORTS' => 'Ärenderapporter',
    'LBL_SHOW_IN_PORTAL' => 'Visa i portal',
    'LBL_CREATE_KB_DOCUMENT' => 'Skapa artikel',
    'LBL_KBCONTENTS_SUBPANEL_TITLE' => 'Kunskapsbas',
    'LBL_REQUEST_CLOSE' => 'Begär avslut',
    'LBL_REQUEST_CLOSE_DATE' => 'Begär avslutsdatum',
    'LBL_REQUEST_CLOSE_LABEL' => 'Begäran om att avsluta',
    'LBL_REQUEST_CLOSE_TOOLTIP' => 'Skicka en begäran om att avsluta detta ärende',
    'LBL_REQUEST_CLOSE_MESSAGE' => 'Vill du göra en begäran om att detta ärende avslutas?',
    'LBL_DASHLET_REQUESTED_CLOSE_CASES' => 'Mina ärenden som begärts att avslutas',
    'LBL_MY_REQUESTED_CLOSE_CASES' => 'Mina ärenden som begärts att avslutas',
    'LBL_DASHLET_MY_REQUESTED_CLOSE_CASES_NAME' => 'Mina ärenden som begärts att avslutas',
    'LBL_DASHLET_REQUESTED_CLOSE_CASES_NAME' => 'Lista över ärenden som begärts att avslutas',
    'LBL_DASHLET_REQUESTED_CLOSE_CASES_DESCRIPTION' => 'Visar en lista över tilldelade ärenden som har begärts att avslutas',

    'LBL_CREATED_USER' => 'Skapad användare',
    'LBL_MODIFIED_USER' => 'Ändrad användare',
    'LBL_PROJECT_SUBPANEL_TITLE' => 'Projekt',
    'LBL_CASE_INFORMATION' => 'Översikt',

    //For export labels
    'LBL_MODIFIED_BY_NAME_OWNER' => 'Ändrad Av Namn Ägare',
    'LBL_MODIFIED_BY_NAME_MOD' => 'Ändrad Av Namn Mod',
    'LBL_CREATED_BY_NAME_OWNER' => 'Skapad Av Namn Ägare',
    'LBL_CREATED_BY_NAME_MOD' => 'Skapad Av Namn Mod',
    'LBL_ASSIGNED_USER_NAME_OWNER' => 'Tilldelad Användare Namn Ägare',
    'LBL_ASSIGNED_USER_NAME_MOD' => 'Tilldelad Användare Namn Mod',
    'LBL_TEAM_COUNT_OWNER' => 'Team Räknare Ägare',
    'LBL_TEAM_COUNT_MOD' => 'Team Räknare Mod',
    'LBL_TEAM_NAME_OWNER' => 'Team Namn Ägare',
    'LBL_TEAM_NAME_MOD' => 'Team Namn Mod',
    'LBL_ACCOUNT_NAME_OWNER' => 'Konto Namn Ägare',
    'LBL_ACCOUNT_NAME_MOD' => 'Konto Namn Mod',
    'LBL_MODIFIED_USER_NAME' => 'Ändrad Användare Namn',
    'LBL_MODIFIED_USER_NAME_OWNER' => 'Ändrad Användare Namn Ägare',
    'LBL_MODIFIED_USER_NAME_MOD' => 'Ändrad Användare Namn Mod',
    'LBL_PORTAL_VIEWABLE' => 'Portabel Visning Möjlig',
    'LBL_EXPORT_ASSIGNED_USER_ID' => 'Tilldelad Användar ID',
    'LBL_EXPORT_MODIFIED_USER_ID' => 'Ändrad av ID',
    'LBL_EXPORT_CREATED_BY' => 'Skapad av ID',
    'LBL_EXPORT_CREATED_BY_NAME' => 'Skapad Av Användare Namn',
    'LBL_EXPORT_ASSIGNED_USER_NAME' => 'Tilldelat Användarnamn',
    'LBL_EXPORT_TEAM_COUNT' => 'Team Räknare',
    'LBL_CHANGETIMERS' => 'Ändra timers',
    'LBL_PURCHASES_SUBPANEL_TITLE' => 'Köp',

    // SNIP
    'LBL_CONTACT_HISTORY_SUBPANEL_TITLE' => 'Relaterad Kontakts Emails',

    // Portal Tutorial content
    'LBL_PORTAL_TOUR_RECORDS_INTRO' => 'Case modulen är för att hantera supportfrågor som påverkar ditt konto. Använd pilarna nedan för att gå igenom en snabbtur.',
    'LBL_PORTAL_TOUR_RECORDS_PAGE' => 'Denna sida visar en lista över befintliga Cases är kopplade till ditt konto.',
    'LBL_PORTAL_TOUR_RECORDS_FILTER' => 'Du kan filtrera ner i listan över Cases genom att tillhandahålla en sökterm.',
    'LBL_PORTAL_TOUR_RECORDS_FILTER_EXAMPLE' => 'Till exempel kan du använda detta för att hitta en fråga som tidigare har lämnats in.',
    'LBL_PORTAL_TOUR_RECORDS_CREATE' => 'Om du har en ny support case du vill skicka in kan du klicka här för att skicka in ett nytt case.',
    'LBL_PORTAL_TOUR_RECORDS_RETURN' => 'Genom att klicka här kommer du tillbaka till den här vyn när som helst.',
    'TPL_ACTIVITY_TIMELINE_DASHLET' => 'Ärende tidslinje',

    'LBL_NOTES_SUBPANEL_TITLE' => 'Anteckningar',

    // Help Text
    // List View Help Text
    'LBL_HELP_RECORDS' => '{{plural_module_name}}-modulen används för att spåra och hantera produkten eller tjänsterelaterade problem som rapporteras till organisationen av kunderna. {{plural_module_name}} är vanligtvis relaterad till en {{accounts_singular_module}}post, och flera {{plural_module_name}} kan associeras till ett {{accounts_singular_module}}. Det finns olika sätt som du kan skapa {{plural_module_name}} i Sugar exempel via {{plural_module_name}} modul, importera {{plural_module_name}}, eller konverteras från e-post. När {{module}} har skapats kan du visa och redigera information som rör den {{module}} via {{module}}ens vy. Varje {{module}}post kan då avse andra Sugar dokument såsom {{calls_module}}, {{contacts_module}}, {{bugs_module}}, och många andra.',

    // Record View Help Text
    'LBL_HELP_RECORD' => '{{plural_module_name}}modulen används för att spåra och hantera produkten eller tjänsterelaterade problem som rapporteras till organisationen av kunderna.  Redigera denna postens fält genom att klicka ett enskilt fält eller på knappen Redigera. - Visa eller ändra länkar till andra poster i underpaneler, även {{campaigns_singular_module}} mottagare, genom att växla den nedre vänstra rutan till "Data View". 
- Utför och se användarkommentarer och eller se förändringar i {{activitystream_singular_module}} genom att växla den nedre vänstra rutan på "Activity Stream". - Följ som favorit med hjälp av ikonerna till höger om namnet. - Ytterligare åtgärder finns i dropdown menyn Åtgärder till höger om knappen Redigera.',

    // Create View Help Text
    'LBL_HELP_CREATE' => '{{plural_module_name}}-modulen används för att spåra och hantera produkt- eller tjänsterelaterade problem som rapporteras till organisationen av dina kunder. 

För att skapa en {{module_name}}:
1. Fyll i fälten med aktuella värden.
 - Fält markerade som &#39;Obligatoriska&#39; måste fyllas i innan du sparar.
 - Klicka på &#39;Visa fler&#39; för att få fler fält om det behövs.
2. Klicka på &#39;Spara&#39; för att färdigställa posten och gå tillbaks till den förra sidan.',
    // Case deflection
    'LBL_NO_LOOKING_FOR' => 'Hittade du inte vad du letade efter?',
    'LBL_CREATE_CASE' => 'Skapa ett {{module_name}}',

    'LBL_SLA_DETAILS' => 'SLA-detaljer',
    'LBL_HOURS' => 'Timmar',
    'LBL_WIDGET_STATUS' => 'Status',

    // KPI Metrics
    'LBL_ORGANIZE' => 'Organisera',
    'LBL_CREATE_NEW' => 'Skapa ny',
    'LBL_MANAGE' => 'Hantera',
    'LBL_SEE_DETAILS' => 'Visa detaljer',
    'LBL_HIDE_NEW' => 'Dölj',

    //For Kiosk labels
    'LBL_KIOSK_MODULE_NAME' => 'Ärenden',
    'LBL_KIOSK_MODULE_NAME_SINGULAR' => 'Ärende',
    'LBL_KIOSK_CREATE' => 'Skapa ärende',
    'LBL_KIOSK_SUBJECT' => 'Ämne',
    'LBL_KIOSK_CASE_ID' => 'Ärende-ID',
    'LBL_KIOSK_STATUS' => 'Status',
    'LBL_KIOSK_CATEGORY' => 'Kategori',
    'LBL_KIOSK_SUBMITTED_BY' => 'Inskickat den {{date}}',
    'LBL_KIOSK_PRIORITY' => 'Prioritet',
    'LBL_KIOSK_CREATE_CASE' => 'Kontakta oss',
    'LBL_KIOSK_DESCRIPTION' => 'Beskrivning',
    'LBL_KIOSK_CREATE_SUCCESS' => 'Din begäran har skickats.',
    'LBL_KIOSK_ATTACHED' => 'Alla Bifogade',

    // External Users Subpanel
    'LBL_EXTERNAL_USERS_SUBPANEL_TITLE' => 'Externa användare',
    'LBL_ESCALATED_CASES' => 'Eskalerade ärenden',
];
