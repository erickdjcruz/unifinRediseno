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
    'LBL_CASES_LIST_DASHBOARD' => 'Nadzorna ploča za popis slučajeva',
    'LBL_CASES_RECORD_DASHBOARD' => 'Nadzorna ploča za zapise o slučajevima',
    'LBL_CASES_MULTI_LINE_DASHBOARD' => 'Ladica fokusa za slučajeve - konzola',
    'LBL_CASES_FOCUS_DRAWER_DASHBOARD' => 'Ladica fokusa za slučajeve',
    'LBL_CLAIM_AND_CLOSE' => 'Tipka za radnju povezanu sa slučajem',
    'LBL_ACTION_CLAIM' => 'Zahtjev za radnju',
    'LBL_ACTION_CLOSE' => 'Zatvaranje radnje',
    'LBL_ACTION_UNASSIGN' => 'Poništavanje dodjele radnje',
    'LBL_CLAIM_BUTTON_TEXT' => 'Zatraži',
    'LBL_CLOSE_BUTTON_TEXT' => 'Zatvori',
    'LBL_UNASSIGN_BUTTON_TEXT' => 'Poništi dodjelu',

    'ERR_DELETE_RECORD' => 'Morate navesti broj zapisa da biste izbrisali račun.',
    'LBL_ACCOUNT_ID' => 'ID računa',
    'LBL_ACCOUNT_NAME' => 'Naziv računa:',
    'LBL_ACCOUNTS_SUBPANEL_TITLE' => 'Računi',
    'LBL_ACTIVITIES_SUBPANEL_TITLE' => 'Aktivnosti',
    'LBL_ATTACH_NOTE' => 'Priloži bilješku',
    'LBL_BUGS_SUBPANEL_TITLE' => 'Pogreške',
    'LBL_CASE_NUMBER' => 'Broj slučaja:',
    'LBL_CASE_SUBJECT' => 'Predmet slučaja:',
    'LBL_CASE' => 'Slučaj:',
    'LBL_CONTACT_CASE_TITLE' => 'Kontakt-slučaj:',
    'LBL_CONTACT_NAME' => 'Ime kontakta:',
    'LBL_CONTACT_ROLE' => 'Uloga:',
    'LBL_CONTACTS_SUBPANEL_TITLE' => 'Kontakti',
    'LBL_DEFAULT_SUBPANEL_TITLE' => 'Slučajevi',
    'LBL_DESCRIPTION' => 'Opis:',
    'LBL_FILENANE_ATTACHMENT' => 'Datoteka priloga',
    'LBL_HISTORY_SUBPANEL_TITLE' => 'Povijest',
    'LBL_INVITEE' => 'Kontakti',
    'LBL_MEMBER_OF' => 'Račun',
    'LBL_MODULE_NAME' => 'Slučajevi',
    'LBL_MODULE_NAME_SINGULAR' => 'Slučaj',
    'LBL_MODULE_TITLE' => 'Slučajevi: Poč. str.',
    'LBL_NEW_FORM_TITLE' => 'Novi slučaj',
    'LBL_NUMBER' => 'Broj:',
    'LBL_PRIORITY' => 'Prioritet:',
    'LBL_PROJECTS_SUBPANEL_TITLE' => 'Projekti',
    'LBL_DOCUMENTS_SUBPANEL_TITLE' => 'Dokumenti',
    'LBL_RESOLUTION' => 'Rješenje:',
    'LBL_SEARCH_FORM_TITLE' => 'Pretraživ. slučajeva',
    'LBL_STATUS' => 'Status:',
    'LBL_SOURCE' => 'Izvor:',
    'LBL_SUBJECT' => 'Predmet:',
    'LBL_LIST_ACCOUNT_NAME' => 'Naziv računa',
    'LBL_LIST_ASSIGNED' => 'Dodijeljeno',
    'LBL_LIST_CLOSE' => 'Zatvori',
    'LBL_LIST_FORM_TITLE' => 'Popis slučajeva',
    'LBL_LIST_LAST_MODIFIED' => 'Posljednja izmjena',
    'LBL_LIST_MY_CASES' => 'Moji otvoreni slučajevi',
    'LBL_LIST_NUMBER' => 'Br.',
    'LBL_LIST_PRIORITY' => 'Prioritet',
    'LBL_LIST_STATUS' => 'Status',
    'LBL_LIST_SUBJECT' => 'Predmet',
    'LBL_LIST_ASSIGNED_TO_NAME' => 'Dodijeljeni korisnik',
    'LBL_SHOW_MORE' => 'Prikaži više slučajeva',
    'LNK_CASE_LIST' => 'Prikaži slučajeve',
    'LNK_NEW_CASE' => 'Stvori slučaj',
    'LBL_CASE_FROM_TWITTER_TITLE' => 'Tweet',
    'LNK_CREATE' => 'Stvori slučaj',
    'LNK_CREATE_WHEN_EMPTY' => 'Stvorite slučaj sada.',
    'NTC_REMOVE_FROM_BUG_CONFIRMATION' => 'Jeste li sigurni da želite ukloniti ovaj slučaj iz pogreške?',
    'NTC_REMOVE_INVITEE' => 'Jeste li sigurni da želite ukloniti ovaj kontakt iz slučaja?',
    'LBL_LIST_DATE_CREATED' => 'Datum stvaranja',
    'LBL_ASSIGNED_TO_NAME' => 'Dodijeljeno',
    'LBL_TYPE' => 'Vrsta',
    'LBL_WORK_LOG' => 'Zapisnik rada',
    'LNK_IMPORT_CASES' => 'Uvezi slučajeve',
    'LNK_CASE_REPORTS' => 'Prikaži izvješća o slučajevima',
    'LBL_SHOW_IN_PORTAL' => 'Prikaži na portalu',
    'LBL_CREATE_KB_DOCUMENT' => 'Stvori članak',
    'LBL_KBCONTENTS_SUBPANEL_TITLE' => 'Baza znanja',
    'LBL_REQUEST_CLOSE' => 'Zatraži zatvaranje',
    'LBL_REQUEST_CLOSE_DATE' => 'Zatraži datum zatvaranja',
    'LBL_REQUEST_CLOSE_LABEL' => 'Zahtjev za zatvaranje',
    'LBL_REQUEST_CLOSE_TOOLTIP' => 'Pošalji zahtjev za zatvaranje ovog slučaja',
    'LBL_REQUEST_CLOSE_MESSAGE' => 'Želite li zatražiti zatvaranje ovog slučaja?',
    'LBL_DASHLET_REQUESTED_CLOSE_CASES' => 'Moji slučajevi sa zahtjevom za zatvaranje',
    'LBL_MY_REQUESTED_CLOSE_CASES' => 'Moji slučajevi sa zahtjevom za zatvaranje',
    'LBL_DASHLET_MY_REQUESTED_CLOSE_CASES_NAME' => 'Moji slučajevi sa zahtjevom za zatvaranje',
    'LBL_DASHLET_REQUESTED_CLOSE_CASES_NAME' => 'Popis slučajeva sa zahtjevom za zatvaranje',
    'LBL_DASHLET_REQUESTED_CLOSE_CASES_DESCRIPTION' => 'Prikazuje popis dodijeljenih slučajeva sa zahtjevom za zatvaranje',

    'LBL_CREATED_USER' => 'Stvoreni korisnik',
    'LBL_MODIFIED_USER' => 'Izmijenjeni korisnik',
    'LBL_PROJECT_SUBPANEL_TITLE' => 'Projekti',
    'LBL_CASE_INFORMATION' => 'Pregled',

    //For export labels
    'LBL_MODIFIED_BY_NAME_OWNER' => 'Izmijenio vlasnik imena',
    'LBL_MODIFIED_BY_NAME_MOD' => 'Izmijenio mod imena',
    'LBL_CREATED_BY_NAME_OWNER' => 'Stvorio vlasnik imena',
    'LBL_CREATED_BY_NAME_MOD' => 'Stvorio mod imena',
    'LBL_ASSIGNED_USER_NAME_OWNER' => 'Dodijeljeni vlasnik korisničkog imena',
    'LBL_ASSIGNED_USER_NAME_MOD' => 'Dodijeljeni mod korisničkog imena',
    'LBL_TEAM_COUNT_OWNER' => 'Vlasnik broja članova u timu',
    'LBL_TEAM_COUNT_MOD' => 'Mod broja članova u timu',
    'LBL_TEAM_NAME_OWNER' => 'Vlasnik naziva tima',
    'LBL_TEAM_NAME_MOD' => 'Mod naziva tima',
    'LBL_ACCOUNT_NAME_OWNER' => 'Vlasnik imena računa',
    'LBL_ACCOUNT_NAME_MOD' => 'Mod imena računa',
    'LBL_MODIFIED_USER_NAME' => 'Izmijenjeno korisničko ime',
    'LBL_MODIFIED_USER_NAME_OWNER' => 'Izmijenjen vlasnik korisničkog imena',
    'LBL_MODIFIED_USER_NAME_MOD' => 'Izmijenjen mod korisničkog imena',
    'LBL_PORTAL_VIEWABLE' => 'Portal se može pregledavati',
    'LBL_EXPORT_ASSIGNED_USER_ID' => 'ID dodijeljenog korisnika',
    'LBL_EXPORT_MODIFIED_USER_ID' => 'Izmijenio ID',
    'LBL_EXPORT_CREATED_BY' => 'Stvorio ID',
    'LBL_EXPORT_CREATED_BY_NAME' => 'Stvorio korisnik s kor. imenom',
    'LBL_EXPORT_ASSIGNED_USER_NAME' => 'Ime dodijeljenog korisnika',
    'LBL_EXPORT_TEAM_COUNT' => 'Broj članova u timu',
    'LBL_CHANGETIMERS' => 'Promijeni mjerače vremena',
    'LBL_PURCHASES_SUBPANEL_TITLE' => 'Kupnje',

    // SNIP
    'LBL_CONTACT_HISTORY_SUBPANEL_TITLE' => 'E-pošta povezanih kontakata',

    // Portal Tutorial content
    'LBL_PORTAL_TOUR_RECORDS_INTRO' => 'Modul slučajeva služi za rješavanje problema s podrškom koji utječu na vaš račun. Upotrijebite strelice u nastavku za brzi vodič.',
    'LBL_PORTAL_TOUR_RECORDS_PAGE' => 'Ova stranica prikazuje popis postojećih slučajeva povezanih s vašim računom.',
    'LBL_PORTAL_TOUR_RECORDS_FILTER' => 'Možete filtrirati popis slučajeva tako da unesete pojam za pretraživanje.',
    'LBL_PORTAL_TOUR_RECORDS_FILTER_EXAMPLE' => 'Tako, primjerice, možete pronaći problem koji je prethodno prijavljen.',
    'LBL_PORTAL_TOUR_RECORDS_CREATE' => 'Ako imate novi slučaj podrške koji biste željeli prijaviti, možete kliknuti ovdje da biste prijavili novi slučaj.',
    'LBL_PORTAL_TOUR_RECORDS_RETURN' => 'Možete se u bilo kojem trenutku vratiti na ovaj prikaz tako da kliknete ovdje.',
    'TPL_ACTIVITY_TIMELINE_DASHLET' => 'Vremenska linija slučaja',

    'LBL_NOTES_SUBPANEL_TITLE' => 'Bilješke',

    // Help Text
    // List View Help Text
    'LBL_HELP_RECORDS' => 'Modul {{plural_module_name}} upotrebljava se za praćenje problema povezanih s proizvodima ili uslugama koje vašoj organizaciji prijave klijenti te za upravljanje tim problemima. {{plural_module_name}} su obično povezani sa zapisom o modulu {{accounts_singular_module}} te više modula {{plural_module_name}} može biti povezano s jednim modulom {{accounts_singular_module}}. {{plural_module_name}} se mogu stvoriti u aplikaciji Sugar na više načina, npr. putem modula {{plural_module_name}}, uvozom {{plural_module_name}} ili pretvaranjem iz e-pošte. Kad stvorite {{module_name}}, možete vidjeti i uređivati informacije koje pripadaju modulu {{module_name}} putem prikaza zapisa o modulu {{module_name}}. Svaki zapis o modulu {{module_name}} zatim se može povezati s drugim zapisima u aplikaciji Sugar, poput modula {{calls_module}}, {{contacts_module}}, {{bugs_module}} i mnogih drugih.',

    // Record View Help Text
    'LBL_HELP_RECORD' => 'Modul {{plural_module_name}} upotrebljava se za praćenje problema povezanih s proizvodima ili uslugama koje vašoj organizaciji prijave klijenti te za upravljanje tim problemima.

- Uredite polja ovog zapisa tako da kliknete na pojedino polje ili gumb Uredi.
- Pogledajte ili izmijenite poveznice na ostale zapise u podskupinama tako da prebacite donje lijevo okno na „Prikaz podataka”.
- Objavljujte i pregledavajte komentare korisnika i bilježite povijest promjena u modulu {{activitystream_singular_module}} tako da prebacite donje lijevo okno na „Pregled aktivnosti”.
- Slijedite ili označite ovaj zapis kao omiljen s pomoću ikona koje se nalaze desno od naziva zapisa.
- Dodatne radnje dostupne su u padajućem izborniku Radnje koji se nalazi desno od gumba Uredi.',

    // Create View Help Text
    'LBL_HELP_CREATE' => 'Modul {{plural_module_name}} upotrebljava se za praćenje problema povezanih s proizvodima ili uslugama koje vašoj organizaciji prijave klijenti te za upravljanje tim problemima.

Da biste stvorili {{module_name}}:
1. Unesite vrijednosti polja po želji.
 - Polja označena „Obavezno” moraju se ispuniti prije spremanja.
 - Kliknite na „Prikaži više” da biste otkrili dodatna polja ako je potrebno.
2. Kliknite na „Spremi” da biste završili novi zapis i vratili se na prethodnu stranicu.',
    // Case deflection
    'LBL_NO_LOOKING_FOR' => 'Niste pronašli što ste tražili?',
    'LBL_CREATE_CASE' => 'Izradi modul {{module_name}}',

    'LBL_SLA_DETAILS' => 'Detalji sporazuma o razini usluga',
    'LBL_HOURS' => 'Sati',
    'LBL_WIDGET_STATUS' => 'Status',

    // KPI Metrics
    'LBL_ORGANIZE' => 'Organiziraj',
    'LBL_CREATE_NEW' => 'Stvori novo',
    'LBL_MANAGE' => 'Upravljaj',
    'LBL_SEE_DETAILS' => 'Prikaži detalje',
    'LBL_HIDE_NEW' => 'Sakrij',

    //For Kiosk labels
    'LBL_KIOSK_MODULE_NAME' => 'Slučajevi',
    'LBL_KIOSK_MODULE_NAME_SINGULAR' => 'Slučaj',
    'LBL_KIOSK_CREATE' => 'Stvori slučaj',
    'LBL_KIOSK_SUBJECT' => 'Predmet',
    'LBL_KIOSK_CASE_ID' => 'ID slučaja',
    'LBL_KIOSK_STATUS' => 'Status',
    'LBL_KIOSK_CATEGORY' => 'Kategorija',
    'LBL_KIOSK_SUBMITTED_BY' => 'Poslano {{date}}',
    'LBL_KIOSK_PRIORITY' => 'Prioritet',
    'LBL_KIOSK_CREATE_CASE' => 'Obratite nam se',
    'LBL_KIOSK_DESCRIPTION' => 'Opis',
    'LBL_KIOSK_CREATE_SUCCESS' => 'Vaš je zahtjev uspješno podnesen.',
    'LBL_KIOSK_ATTACHED' => 'Sve priloženo',

    // External Users Subpanel
    'LBL_EXTERNAL_USERS_SUBPANEL_TITLE' => 'Vanjski korisnici',
    'LBL_ESCALATED_CASES' => 'Eskalirani slučajevi',
];
