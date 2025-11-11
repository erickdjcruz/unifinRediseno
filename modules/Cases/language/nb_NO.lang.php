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
    'LBL_CASES_LIST_DASHBOARD' => 'Dashbord for saksliste',
    'LBL_CASES_RECORD_DASHBOARD' => 'Dashbord for saksoppføring',
    'LBL_CASES_MULTI_LINE_DASHBOARD' => 'Oppgaver fokusskuff - Konsoll',
    'LBL_CASES_FOCUS_DRAWER_DASHBOARD' => 'Saker fokusskuff',
    'LBL_CLAIM_AND_CLOSE' => 'Handlingsknapp for sak',
    'LBL_ACTION_CLAIM' => 'Handlingskrav',
    'LBL_ACTION_CLOSE' => 'Handlingslukking',
    'LBL_ACTION_UNASSIGN' => 'Fjerning av tilordning av handling',
    'LBL_CLAIM_BUTTON_TEXT' => 'Krev',
    'LBL_CLOSE_BUTTON_TEXT' => 'Lukk',
    'LBL_UNASSIGN_BUTTON_TEXT' => 'Ikke tilordne',

    'ERR_DELETE_RECORD' => 'Du må oppgi et registreringsnummer for å slette denne bedrift.',
    'LBL_ACCOUNT_ID' => 'Bedrift-ID',
    'LBL_ACCOUNT_NAME' => 'Bedriftnavn:',
    'LBL_ACCOUNTS_SUBPANEL_TITLE' => 'Bedrifter',
    'LBL_ACTIVITIES_SUBPANEL_TITLE' => 'Aktiviteter',
    'LBL_ATTACH_NOTE' => 'Legg merke til',
    'LBL_BUGS_SUBPANEL_TITLE' => 'Feil',
    'LBL_CASE_NUMBER' => 'Saksnummer:',
    'LBL_CASE_SUBJECT' => 'Saksemne:',
    'LBL_CASE' => 'Sak',
    'LBL_CONTACT_CASE_TITLE' => 'Kontaktemne:',
    'LBL_CONTACT_NAME' => 'Kontaktens navn:',
    'LBL_CONTACT_ROLE' => 'Rolle:',
    'LBL_CONTACTS_SUBPANEL_TITLE' => 'Kontakter',
    'LBL_DEFAULT_SUBPANEL_TITLE' => 'Saker',
    'LBL_DESCRIPTION' => 'Beskrivelse:',
    'LBL_FILENANE_ATTACHMENT' => 'Vedlegg',
    'LBL_HISTORY_SUBPANEL_TITLE' => 'Historie',
    'LBL_INVITEE' => 'Kontakter',
    'LBL_MEMBER_OF' => 'Bedrift',
    'LBL_MODULE_NAME' => 'Saker',
    'LBL_MODULE_NAME_SINGULAR' => 'Sak',
    'LBL_MODULE_TITLE' => 'Saker: Hovedside',
    'LBL_NEW_FORM_TITLE' => 'Ny sak',
    'LBL_NUMBER' => 'Nummer:',
    'LBL_PRIORITY' => 'Prioritet:',
    'LBL_PROJECTS_SUBPANEL_TITLE' => 'Prosjekter',
    'LBL_DOCUMENTS_SUBPANEL_TITLE' => 'Dokumenter',
    'LBL_RESOLUTION' => 'Løsning',
    'LBL_SEARCH_FORM_TITLE' => 'Søk etter sak',
    'LBL_STATUS' => 'Status:',
    'LBL_SOURCE' => 'Kilde:',
    'LBL_SUBJECT' => 'Emne:',
    'LBL_LIST_ACCOUNT_NAME' => 'Bedriftnavn',
    'LBL_LIST_ASSIGNED' => 'Tildelt til',
    'LBL_LIST_CLOSE' => 'Lukk',
    'LBL_LIST_FORM_TITLE' => 'Saksliste',
    'LBL_LIST_LAST_MODIFIED' => 'Sist endret',
    'LBL_LIST_MY_CASES' => 'Mine åpne saker',
    'LBL_LIST_NUMBER' => 'Num.',
    'LBL_LIST_PRIORITY' => 'Prioritet',
    'LBL_LIST_STATUS' => 'Status',
    'LBL_LIST_SUBJECT' => 'Emne',
    'LBL_LIST_ASSIGNED_TO_NAME' => 'Tildelt bruker',
    'LBL_SHOW_MORE' => 'Vis flere feil',
    'LNK_CASE_LIST' => 'Saker',
    'LNK_NEW_CASE' => 'Opprett sak',
    'LBL_CASE_FROM_TWITTER_TITLE' => 'Tweet',
    'LNK_CREATE' => 'Opprett sak',
    'LNK_CREATE_WHEN_EMPTY' => 'Opprett en Sak nå.',
    'NTC_REMOVE_FROM_BUG_CONFIRMATION' => 'Er du sikker på at du vil slette?',
    'NTC_REMOVE_INVITEE' => 'Er du sikker på at du vil slette denne kontakten?',
    'LBL_LIST_DATE_CREATED' => 'Opprettet dato',
    'LBL_ASSIGNED_TO_NAME' => 'Tildelt',
    'LBL_TYPE' => 'Type',
    'LBL_WORK_LOG' => 'Arbeidslogg',
    'LNK_IMPORT_CASES' => 'Importer Saker',
    'LNK_CASE_REPORTS' => 'Saks rapporter',
    'LBL_SHOW_IN_PORTAL' => 'Vis i portal',
    'LBL_CREATE_KB_DOCUMENT' => 'Skap artikel',
    'LBL_KBCONTENTS_SUBPANEL_TITLE' => 'KB-dokumenter',
    'LBL_REQUEST_CLOSE' => 'Be om å lukke',
    'LBL_REQUEST_CLOSE_DATE' => 'Be om lukkedato',
    'LBL_REQUEST_CLOSE_LABEL' => 'Forespørsel om lukking',
    'LBL_REQUEST_CLOSE_TOOLTIP' => 'Send en forespørsel om å lukke denne saken',
    'LBL_REQUEST_CLOSE_MESSAGE' => 'Vil du be om at denne saken lukkes?',
    'LBL_DASHLET_REQUESTED_CLOSE_CASES' => 'Mine saker anmodet for lukking',
    'LBL_MY_REQUESTED_CLOSE_CASES' => 'Mine saker anmodet for lukking',
    'LBL_DASHLET_MY_REQUESTED_CLOSE_CASES_NAME' => 'Mine saker anmodet for lukking',
    'LBL_DASHLET_REQUESTED_CLOSE_CASES_NAME' => 'Liste over saker anmodet for lukking',
    'LBL_DASHLET_REQUESTED_CLOSE_CASES_DESCRIPTION' => 'Viser en liste over tilordnede saker som er anmodet for lukking',

    'LBL_CREATED_USER' => 'Opprettet bruker',
    'LBL_MODIFIED_USER' => 'Endret bruker',
    'LBL_PROJECT_SUBPANEL_TITLE' => 'Prosjekter',
    'LBL_CASE_INFORMATION' => 'Sak oversikt',

    //For export labels
    'LBL_MODIFIED_BY_NAME_OWNER' => 'Endre Av Navn Eier',
    'LBL_MODIFIED_BY_NAME_MOD' => 'Endret Av Navn Mod',
    'LBL_CREATED_BY_NAME_OWNER' => 'Opprettet Av Navn Eier',
    'LBL_CREATED_BY_NAME_MOD' => 'Opprettet Av Navn Endret',
    'LBL_ASSIGNED_USER_NAME_OWNER' => 'Tildelt Brukernavn Eier',
    'LBL_ASSIGNED_USER_NAME_MOD' => 'Tildelt Brukernavn Endret',
    'LBL_TEAM_COUNT_OWNER' => 'Gruppe-antall Eier',
    'LBL_TEAM_COUNT_MOD' => 'Gruppe-antall Mod',
    'LBL_TEAM_NAME_OWNER' => 'Gruppenavn Eier',
    'LBL_TEAM_NAME_MOD' => 'Gruppenavn Mod',
    'LBL_ACCOUNT_NAME_OWNER' => 'Konto-navn Eier',
    'LBL_ACCOUNT_NAME_MOD' => 'Konto-navn Endret',
    'LBL_MODIFIED_USER_NAME' => 'Endret Brukernavn',
    'LBL_MODIFIED_USER_NAME_OWNER' => 'Endret Brukernavn Eier',
    'LBL_MODIFIED_USER_NAME_MOD' => 'Endret Brukernavn Mod',
    'LBL_PORTAL_VIEWABLE' => 'Portal Synlig',
    'LBL_EXPORT_ASSIGNED_USER_ID' => 'Tildelt Bruker-ID',
    'LBL_EXPORT_MODIFIED_USER_ID' => 'Endret av ID',
    'LBL_EXPORT_CREATED_BY' => 'Opprettet Av ID',
    'LBL_EXPORT_CREATED_BY_NAME' => 'Opprettet Av Brukernavn',
    'LBL_EXPORT_ASSIGNED_USER_NAME' => 'Tildelt Brukernavn',
    'LBL_EXPORT_TEAM_COUNT' => 'Gruppe-antall',
    'LBL_CHANGETIMERS' => 'Endre timere',
    'LBL_PURCHASES_SUBPANEL_TITLE' => 'Kjøp',

    // SNIP
    'LBL_CONTACT_HISTORY_SUBPANEL_TITLE' => 'Relaterte kontakers e-poster',

    // Portal Tutorial content
    'LBL_PORTAL_TOUR_RECORDS_INTRO' => 'Case modulen er for å administrere kundestøtteproblemer som påvirker din konto. Bruk pilene nedenfor for å gå gjennom en rask tour.',
    'LBL_PORTAL_TOUR_RECORDS_PAGE' => 'Denne siden viser en liste over eksisterende Cases knyttet til din konto.',
    'LBL_PORTAL_TOUR_RECORDS_FILTER' => 'Du kan filtrere ned sakslisten ved å oppgi et søkeord.',
    'LBL_PORTAL_TOUR_RECORDS_FILTER_EXAMPLE' => 'For eksempel kan du bruke dette til å finne et tema som har blitt sendt inn tidligere.',
    'LBL_PORTAL_TOUR_RECORDS_CREATE' => 'Hvis du har en ny support sak du ønsker å sende, kan du klikke her for å sende inn en ny sak.',
    'LBL_PORTAL_TOUR_RECORDS_RETURN' => 'Ved å klikke her kommer du til denne visningen til enhver tid.',
    'TPL_ACTIVITY_TIMELINE_DASHLET' => 'Sakstidslinje',

    'LBL_NOTES_SUBPANEL_TITLE' => 'Notater',

    // Help Text
    // List View Help Text
    'LBL_HELP_RECORDS' => 'Den {{plural_module_name}} modulen brukes til å spore og administrere produkt eller tjenesterelaterte problemer rapportert til din organisasjon av dine kunder. {{plural_module_name}} er vanligvis knyttet til en {{accounts_singular_module}} post, og flere {{plural_module_name}} kan være forbundet til en enkelt {{accounts_singular_module}}. Det finnes ulike måter du kan opprette {{plural_module_name}} i Sugar eksempel via {{plural_module_name}} modul, importerer {{plural_module_name}}, eller konvertert fra e-post. Når {{module_name}} er opprettet, kan du se og redigere informasjon knyttet til {{module_name}} via {{module_name}}&#39;s postvisning. Hver {{module_name}} posten kan da forholde seg til andre Sugar poster som {{calls_module}}, {{contacts_module}}, {{bugs_module}}, og mange andre.',

    // Record View Help Text
    'LBL_HELP_RECORD' => 'Den {{plural_module_name}} modulen brukes til å spore og administrere produkt eller tjenesterelaterte problemer rapportert til din organisasjonen av dine kunder. - Rediger dtte postfelt ved å klikke på et enkelt felt eller Rediger-knappen. - Vis eller endre lenker til andre poster i underpaneler ved veksling av nedre venstre rute til "Data View". - Lag og vis bruker kommentarer og postendringshistorie i {{activitystream_singular_module}} ved veksling av nedre venstre rute til "Aktivitetstrøm". - Følg eller favoritt denne posten ved hjelp av ikonene til høyre for postens navn. - Ytterligere tiltak er tilgjengelig i rullgardinmenyen Handlinger til høyre på Rediger-knappen.',

    // Create View Help Text
    'LBL_HELP_CREATE' => '{{plural_module_name}} modulen brukes til å spore og administrere produkter som er relatert til problemer som er rapportert til organisasjonen av kunder. 

For å opprette {{module_name}}: 
1. Legg inn verdier i feltene som ønsket. 
- Felt som er merket "Obligatorisk" må oppdateres før du lagrer. 
- Klikk "Vis mer" for å avsløre flere felt hvis det er nødvendig. 
2. Klikk "Lagre" for å sluttføre den nye posten og gå tilbake til forrige side.',
    // Case deflection
    'LBL_NO_LOOKING_FOR' => 'Fant du ikke det du ser etter?',
    'LBL_CREATE_CASE' => 'Opprett en {{module_name}}',

    'LBL_SLA_DETAILS' => 'SLA-detaljer',
    'LBL_HOURS' => 'Timer',
    'LBL_WIDGET_STATUS' => 'Status',

    // KPI Metrics
    'LBL_ORGANIZE' => 'Organiser',
    'LBL_CREATE_NEW' => 'Opprett ny',
    'LBL_MANAGE' => 'Administrer',
    'LBL_SEE_DETAILS' => 'Vis detaljer',
    'LBL_HIDE_NEW' => 'Skjul',

    //For Kiosk labels
    'LBL_KIOSK_MODULE_NAME' => 'Saker',
    'LBL_KIOSK_MODULE_NAME_SINGULAR' => 'Sak',
    'LBL_KIOSK_CREATE' => 'Opprett sak',
    'LBL_KIOSK_SUBJECT' => 'Emne',
    'LBL_KIOSK_CASE_ID' => 'Sak-ID',
    'LBL_KIOSK_STATUS' => 'Status',
    'LBL_KIOSK_CATEGORY' => 'Kategori',
    'LBL_KIOSK_SUBMITTED_BY' => 'Sendt inn: {{date}}',
    'LBL_KIOSK_PRIORITY' => 'Prioritet',
    'LBL_KIOSK_CREATE_CASE' => 'Kontakt oss',
    'LBL_KIOSK_DESCRIPTION' => 'Beskrivelse',
    'LBL_KIOSK_CREATE_SUCCESS' => 'Din forespørsel ble sendt.',
    'LBL_KIOSK_ATTACHED' => 'Alle vedlagt',

    // External Users Subpanel
    'LBL_EXTERNAL_USERS_SUBPANEL_TITLE' => 'Eksterne brukere',
    'LBL_ESCALATED_CASES' => 'Opptrappede saker',
];
