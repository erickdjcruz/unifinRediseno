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
    'LBL_OPPORTUNITIES_LIST_DASHBOARD' => 'Tabloul de bord Listă oportunități',
    'LBL_OPPORTUNITIES_RECORD_DASHBOARD' => 'Tabloul de bord Înregistrare oportunități',
    'LBL_OPPORTUNITIES_MULTI_LINE_DASHBOARD' => 'Sertar de focalizare oportunități - consolă',
    'LBL_OPPORTUNITIES_FOCUS_DRAWER_DASHBOARD' => 'Sertar de focalizare oportunități',
    'LBL_RENEWAL_OPPORTUNITY' => 'Oportunitate de reînnoire',

    'LBL_MODULE_NAME' => 'Oportunităţi',
    'LBL_MODULE_NAME_SINGULAR' => 'Oportunitate',
    'LBL_MODULE_TITLE' => 'Oportunitati :Acasa',
    'LBL_SEARCH_FORM_TITLE' => 'Cautare oportunitate',
    'LBL_VIEW_FORM_TITLE' => 'Vizualizare Oportunitati',
    'LBL_LIST_FORM_TITLE' => 'Lista oportunitati',
    'LBL_OPPORTUNITY_NAME' => 'Nume Oportunitate:',
    'LBL_OPPORTUNITY' => 'Oportunitati',
    'LBL_NAME' => 'Nume Oportunitate:',
    'LBL_TIME' => 'Timp',
    'LBL_INVITEE' => 'Contacte',
    'LBL_CURRENCIES' => 'Valute',
    'LBL_LIST_OPPORTUNITY_NAME' => 'Nume',
    'LBL_LIST_ACCOUNT_NAME' => 'Nume cont',
    'LBL_LIST_DATE_CLOSED' => 'Inchide',
    'LBL_LIST_AMOUNT' => 'Previzibil',
    'LBL_LIST_AMOUNT_USDOLLAR' => 'Cantitatea:',
    'LBL_ACCOUNT_ID' => 'Identitate Cont',
    'LBL_CURRENCY_RATE' => 'rata moneda',
    'LBL_CURRENCY_ID' => 'Moneda Id',
    'LBL_CURRENCY_NAME' => 'Nume Moneda',
    'LBL_CURRENCY_SYMBOL' => 'Simbol Moneda',
//DON'T CONVERT THESE THEY ARE MAPPINGS
    'db_sales_stage' => 'LBL_LIST_SALES_STAGE',
    'db_name' => 'LBL_NAME',
    'db_amount' => 'LBL_LIST_AMOUNT',
    'db_date_closed' => 'LBL_LIST_DATE_CLOSED',
//END DON'T CONVERT
    'UPDATE' => 'Vanzari - Actualizare Moneda',
    'UPDATE_DOLLARAMOUNTS' => 'Update Sume Dolari U. S.',
    'UPDATE_VERIFY' => 'Verifica sumele',
    'UPDATE_VERIFY_TXT' => 'Verifică dacă valorile in suma de vânzări sunt valabile numerele zecimale  numai cu caractere numerice (0-9) şi numărul de zecimale (.)',
    'UPDATE_FIX' => 'Sume fixe',
    'UPDATE_FIX_TXT' => 'Încercările de a rezolva orice sume incorecte, prin crearea unui zecimal valid din valoarea actuală. Orice sumă modificata este susţinuta în domeniul baza de date amount_backup . Dacă rulaţi acest anunţ şi observati probleme, nu-l rulaţi din nou fără restaurarea din backup, deoarece se poate suprascrie  cu noile date incorecte.',
    'UPDATE_DOLLARAMOUNTS_TXT' => 'Actualizaţi sumele pentru dolarul american pentru vânzări pe baza ratelor actuale valutar stabilite. Această valoare este folosită pentru a calcula grafice şi Lista Vizualizare Sume valutare.',
    'UPDATE_CREATE_CURRENCY' => 'Creează monedă nouă:',
    'UPDATE_VERIFY_FAIL' => 'Verificare a inregistrarii esuata',
    'UPDATE_VERIFY_CURAMOUNT' => 'Cantitate suma:',
    'UPDATE_VERIFY_FIX' => 'Efectuand Depanare ne va da',
    'UPDATE_INCLUDE_CLOSE' => 'Include si Inregistrarile Inchise',
    'UPDATE_VERIFY_NEWAMOUNT' => 'Suma Noua:',
    'UPDATE_VERIFY_NEWCURRENCY' => 'Moneda noua:',
    'UPDATE_DONE' => 'Terminat',
    'UPDATE_BUG_COUNT' => 'Probleme gasite si incercate sa fie rezolvate',
    'UPDATE_BUGFOUND_COUNT' => 'Probleme gasite:',
    'UPDATE_COUNT' => 'Inregistrari actualizate',
    'UPDATE_RESTORE_COUNT' => 'Inregistrari sume restaurate',
    'UPDATE_RESTORE' => 'Restabileste sume',
    'UPDATE_RESTORE_TXT' => 'Restabileste valoarea sumelor din valorile de rezerva create in timpul depanarii',
    'UPDATE_FAIL' => 'Nu au fost putut fi actualizate -',
    'UPDATE_NULL_VALUE' => 'Suma este NULA sabilind-o 0 -',
    'UPDATE_MERGE' => 'Imbina monede',
    'UPDATE_MERGE_TXT' => 'Îmbina mai multe monede într-o monedă unică. Dacă există mai multe înregistrări monedă pentru aceeaşi monedă, imbina împreună. Acest lucru va imbina, de asemenea, monedele din toate celelalte module.',
    'LBL_ACCOUNT_NAME' => 'Nume cont:',
    'LBL_CURRENCY' => 'Moneda',
    'LBL_DATE_CLOSED' => 'Data de închidere estimată:',
    'LBL_DATE_CLOSED_TIMESTAMP' => 'Data la care se asteapta sa se inchida',
    'LBL_TYPE' => 'Tip:',
    'LBL_CAMPAIGN' => 'Campanie',
    'LBL_NEXT_STEP' => 'Următorul pas:',
    'LBL_SERVICE_START_DATE' => 'Data de început a serviciului',
    'LBL_LEAD_SOURCE' => 'Sursa principala',
    'LBL_SALES_STAGE' => 'Sadiul Vanzarilor',
    'LBL_SALES_STATUS' => 'Status',
    'LBL_PROBABILITY' => 'Probabilitate (%):',
    'LBL_DESCRIPTION' => 'Descriere',
    'LBL_DUPLICATE' => 'Posibila Oportunitate Duplicata',
    'MSG_DUPLICATE' => 'Inregistrarea oportunitatii ce sunteti pe cale sa o creati poate fi un duplicat al unei inregistrari de oportunitate care exista deja. Inregistrarile de oportunitate care contin nume si/sau adrese de email similare sunt listate mai jos. Dati click pe Salvare pentru a continua sa creati aceasta noua oportunitate, sau dati click pe Revocare pentru a reveni la modul fara ca oportunitatea sa fie creata.',
    'LBL_NEW_FORM_TITLE' => 'Creeaza Oportunitate',
    'LNK_NEW_OPPORTUNITY' => 'Creeaza Oportunitate',
    'LNK_CREATE' => 'Creează ofertă',
    'LNK_OPPORTUNITY_LIST' => 'Vezi oportunitati',
    'ERR_DELETE_RECORD' => 'Trebuie să specifici un număr de înregistrare pentru a șterge oportunitatea.',
    'LBL_TOP_OPPORTUNITIES' => 'Topul celor mai deschise oportunitati',
    'NTC_REMOVE_OPP_CONFIRMATION' => 'Sunteți sigur că vreți să ștergeți acest contact din oportunitate?',
    'OPPORTUNITY_REMOVE_PROJECT_CONFIRM' => 'Sunteți sigur că vreți să ștergeți această oportunitate din proiect?',
    'LBL_DEFAULT_SUBPANEL_TITLE' => 'Oportunitati',
    'LBL_ACTIVITIES_SUBPANEL_TITLE' => 'Activitati',
    'LBL_HISTORY_SUBPANEL_TITLE' => 'Istoric',
    'LBL_RAW_AMOUNT' => 'Suma Bruta',
    'LBL_LEADS_SUBPANEL_TITLE' => 'Piste',
    'LBL_CONTACTS_SUBPANEL_TITLE' => 'Contacte',
    'LBL_DOCUMENTS_SUBPANEL_TITLE' => 'Documente',
    'LBL_PROJECTS_SUBPANEL_TITLE' => 'Proiecte',
    'LBL_ASSIGNED_TO_NAME' => 'Atribuit lui:',
    'LBL_LIST_ASSIGNED_TO_NAME' => 'Utilizator Atribuit',
    'LBL_LIST_SALES_STAGE' => 'Sadiul Vanzarilor',
    'LBL_MY_CLOSED_OPPORTUNITIES' => 'Oportunitatile mele inchise',
    'LBL_TOTAL_OPPORTUNITIES' => 'Oportunitatile totale',
    'LBL_CLOSED_WON_OPPORTUNITIES' => 'Oportunitatile castigate inchise',
    'LBL_ASSIGNED_TO_ID' => 'Atribuit ID Utilizator',
    'LBL_CREATED_ID' => 'Creat de ID',
    'LBL_MODIFIED_ID' => 'Modificat după ID',
    'LBL_MODIFIED_NAME' => 'Modificat după nume utilizator',
    'LBL_CREATED_USER' => 'Utilizator creat',
    'LBL_MODIFIED_USER' => 'Utilizator Modificat',
    'LBL_CAMPAIGN_OPPORTUNITY' => 'Campanii',
    'LBL_PROJECT_SUBPANEL_TITLE' => 'Proiecte',
    'LABEL_PANEL_ASSIGNMENT' => 'Sarcina',
    'LNK_IMPORT_OPPORTUNITIES' => 'Importa oportunitati',
    'LBL_EDITLAYOUT' => 'Editeaza Plan General' /*for 508 compliance fix*/,
    //For export labels
    'LBL_EXPORT_CAMPAIGN_ID' => 'Date de Identificare Campanie',
    'LBL_OPPORTUNITY_TYPE' => 'Tip Oportunitate',
    'LBL_EXPORT_ASSIGNED_USER_NAME' => 'Alocat utilizatorului',
    'LBL_EXPORT_ASSIGNED_USER_ID' => 'Alocat utilizatorului ID',
    'LBL_EXPORT_MODIFIED_USER_ID' => 'Modificat după ID',
    'LBL_EXPORT_CREATED_BY' => 'Creat de ID',
    'LBL_EXPORT_NAME' => 'Nume',
    // SNIP
    'LBL_CONTACT_HISTORY_SUBPANEL_TITLE' => 'Contacte Email Asociate',
    'LBL_FILENAME' => 'Atasament',
    'LBL_PRIMARY_QUOTE_ID' => 'Ofertă principală',
    'LBL_CONTRACTS' => 'Contracte',
    'LBL_CONTRACTS_SUBPANEL_TITLE' => 'Contracte',
    'LBL_PRODUCTS' => 'Produse din ofertă',
    'LBL_RLI' => 'Articole linie de venituri',
    'LNK_OPPORTUNITY_REPORTS' => 'Vezi rapoarte oportunitati',
    'LBL_QUOTES_SUBPANEL_TITLE' => 'Oferte',
    'LBL_TEAM_ID' => 'ID Echipă',
    'LBL_TIMEPERIODS' => 'Perioade de timp',
    'LBL_TIMEPERIOD_ID' => 'Perioada de timp',
    'LBL_COMMITTED' => 'Alocat',
    'LBL_FORECAST' => 'Includeți în prognoza',
    'LBL_COMMIT_STAGE' => 'Consemna Etapa',
    'LBL_COMMIT_STAGE_FORECAST' => 'Stadiu previziune',
    'LBL_WORKSHEET' => 'Foaie de lucru',
    'LBL_PURCHASED_LINE_ITEMS' => 'Articole de linie achiziționate',

    // KPI Metrics
    'LBL_ORGANIZE' => 'Organizare',
    'LBL_CREATE_NEW' => 'Creare nou',
    'LBL_MANAGE' => 'Gestionare',
    'LBL_SEE_DETAILS' => 'Vizualizare detalii',
    'LBL_HIDE_NEW' => 'Ascundere',

    'LBL_FORECASTED_LIKELY' => 'Prognozat probabil',
    'LBL_LOST' => 'Pierdut',
    'LBL_RENEWAL' => 'Reînnoire',
    'LBL_RENEWAL_OPPORTUNITIES' => 'Oportunități de reînnoire',
    'LBL_RENEWAL_PARENT' => 'Oportunitate principală',
    'LBL_PARENT_RENEWAL_OPPORTUNITY_ID' => 'Reînnoire ID părinte',
    'LBL_MONTH_YEAR_RENEWAL' => '{{month}}, {{year}}',

    'LBL_WIDGET_SALES_STAGE' => 'Stadiu vanzare',
    'LBL_WIDGET_DATE_CLOSED' => 'Data de închidere estimată',
    'LBL_WIDGET_AMOUNT' => 'Valoare',

    'TPL_RLI_CREATE' => 'O oportunitate trebuie să aibă un venit asociat articol. Creați un element linie de venituri.',
    'TPL_RLI_CREATE_LINK_TEXT' => 'Creează un element de venit.',
    'LBL_PRODUCTS_SUBPANEL_TITLE' => 'Produse din ofertă',
    'LBL_RLI_SUBPANEL_TITLE' => 'Articole linie de venituri',

    'LBL_TOTAL_RLIS' => '# Din total Articole linie de venituri',
    'LBL_CLOSED_RLIS' => '# A închis Articole linie de venituri',
    'LBL_CLOSED_WON_RLIS' => '# din Elemente linie de venit câștig închis',
    'LBL_SERVICE_OPEN_FLEX_DURATION_RLIS' => 'Nr. articole de linie venituri cu durată flexibilă servicii deschise',
    'NOTICE_NO_DELETE_CLOSED_RLIS' => 'Nu se pot șterge oportunități care conțin închise Articole linie de venituri',
    'WARNING_NO_DELETE_CLOSED_SELECTED' => 'Una sau mai multe dintre înregistrările selectate conține închise Articole linie de venituri și nu pot fi șterse.',
    'LBL_INCLUDED_RLIS' => '# din elementele de linie ale venitului',
    'LBL_UPDATE_OPPORTUNITIES_RLIS' => 'Actualizare deschisă',
    'LBL_CASCADE_RLI_EDIT' => 'Actualizare linii de venit deschise',
    'LBL_CASCADE_RLI_CREATE' => 'Stabilire pentru toate liniile de venit',
    'LBL_SERVICE_START_DATE_INVALID' => 'Data de începere a serviciului nu poate fi setată după Data de încheiere a serviciului pentru niciun articol al liniei de venit suplimentar deschis.',

    'LBL_QUOTE_SUBPANEL_TITLE' => 'Oferte',
    'LBL_FILTER_OPPORTUNITY_TEMPLATE' => 'Oportunități de la un cont dinamic',
    'LBL_TOP_10_OPP' => 'Top 10 oportunități deschise',
    'LBL_DASHLET_MY_ACTIVE_OPP' => 'Tablou: Oportunitățile mele active',
    'LBL_MY_ACTIVE_OPP' => 'Oportunitățile mele active',


    // Config
    'LBL_OPPS_CONFIG_VIEW_BY_LABEL' => 'Ierarhie oportunităţi',
    'LBL_OPPS_CONFIG_VIEW_BY_DATE_ROLLUP' => 'Setează câmpul Data previzionată de închidere din înregistrările de oportunitate rezultate la datele de închidere cele mai recente sau cele mai îndepărtate din elementele de venit',

    //Dashlet
    'LBL_PIPELINE_TOTAL_IS' => 'Pipeline Total is',

    'LBL_OPPORTUNITY_ROLE' => 'Rol Oportunitate',
    'LBL_NOTES_SUBPANEL_TITLE' => 'Note',
    'LBL_TAB_OPPORTUNITY' => 'Revizie {{module}}',

    // Help Text
    'LBL_OPPS_CONFIG_ALERT' => 'Făcând clic pe Confirmare, veţi şterge TOATE datele cu previzionări şi veţi modifica Fereastra Oportunităţi. Dacă nu asta aţi dorit, faceţi clic pe Anulare pentru a reveni la setările anterioare.',
    'LBL_OPPS_CONFIG_ALERT_TO_OPPS' =>
        'Făcând clic pe Confirmare, veţi şterge TOATE datele Previziunilor şi veţi modifica Vizualizarea de oportunităţi. '
        . 'De asemenea, se vor dezactiva TOATE definiţiile procesului cu un modul ţintă de Elemente pe linia de venituri. '
        . 'Dacă nu aţi intenţionat acest lucru, faceţi clic pe revocare pentru a reveni la setările anterioare.',
    'LBL_OPPS_CONFIG_SALES_STAGE_1a' => 'Dacă toate elementele de venit sunt încheiate şi cel puţin unul a fost câştigat,',
    'LBL_OPPS_CONFIG_SALES_STAGE_1b' => 'Stadiul de vânzări din Oportunităţi este setat la „Încheiat cu succes”.',
    'LBL_OPPS_CONFIG_SALES_STAGE_2a' => 'Dacă toate elementele de venit sunt în stadiul de vânzări „Încheiat în pierdere”,',
    'LBL_OPPS_CONFIG_SALES_STAGE_2b' => 'Stadiul de vânzări din Oportunităţi este setat la „Încheiat în pierdere”.',
    'LBL_OPPS_CONFIG_SALES_STAGE_3a' => 'Dacă mai este deschis vreun element de venit,',
    'LBL_OPPS_CONFIG_SALES_STAGE_3b' => 'Oportunitatea va fi marcată cu Stadiul de vânzări cel mai puţin avansat.',

// BEGIN ENT/ULT

    // Opps Config - View By Opportunities
    'LBL_HELP_CONFIG_OPPS' => 'După ce iniţiaţi această modificare, în fundal vor fi create note de centralizare a elementelor de venit. Atunci când notele sunt complete şi disponibile, va fi trimisă o notificare la adresa de e-mail de pe profilul dvs. de utilizator. Dacă platforma dvs. este configurată pentru {{forecasts_module}}, Sugar vă va trimite o notificare şi când înregistrările dvs. {{module_name}} sunt sincronizate cu modulul {{forecasts_module}} şi disponibile pentru noi {{forecasts_module}}. De reţinut faptul că platforma dvs. trebuie configurată pentru a trimite e-mail-uri prin Admin > Setări E-mail pentru ca notificările să fie trimise.',

    // Opps Config - View By Opportunities And RLIs
    'LBL_HELP_CONFIG_RLIS' => 'După ce iniţiaţi această modificare, în fundal vor fi create înregistrări de elemente de venit pentru fiecare {{module_name} existent. Atunci când elementele de venit sunt complete şi disponibile, va fi trimisă o notificare la adresa de e-mail de pe profilul dvs. de utilizator. De reţinut faptul că platforma dvs. trebuie configurată pentru a trimite e-mail-uri prin Admin > Setări E-mail pentru ca notificările să fie trimise.',
    // List View Help Text
    'LBL_HELP_RECORDS' => 'Modulul {{plural_module_name}} vă permite să urmăriți vânzările individuale de la început până la sfârșit. Fiecare {{module_name}} înregistrare reprezintă o vânzare potențială și include date relevante privind vânzările, precum și referitoare la alte înregistrări importante, cum ar fi: {{quotes_module}}, {{contacts_module}}etc. Un {{module_name}} va progresa de obicei prin mai multe etape de vânzare până când este marcat fie "Închis câștigat", fie "Închis pierdut". {{plural_module_name}} poate fi valorificat și mai mult prin utilizarea modulului Sugar {{forecasts_singular_module}} pentru a înțelege și prezice tendințele vânzărilor, precum și pentru a concentra activitatea pentru atingerea cotelor de vânzări.',

    // Record View Help Text
    'LBL_HELP_RECORD' => 'Modulul {{plural_module_name}} vă permite să urmăriți vânzările individuale și elementele de rând aparținând acestor vânzări de la început până la sfârșit. Fiecare înregistrare {{module_name}} reprezintă o vânzare potențială și include date relevante privind vânzările, precum și referitoare la alte înregistrări importante, cum ar fi: {{quotes_module}}, {{contacts_module}} etc.

- Editați câmpurile acestei înregistrări făcând clic pe un câmp individual sau pe butonul Editare.
- Vizualizați sau modificați linkurile către alte înregistrări din panourile secundare comutând panoul din stânga jos la „Vizualizare date".
- Faceți și vizualizați comentariile utilizatorilor și înregistrați istoricul modificărilor în {{activitystream_singular_module}} comutând panoul din stânga jos la „Flux de activitate".
- Urmați sau preferați această înregistrare folosind pictogramele din dreapta numelui înregistrării.
- Acțiuni suplimentare sunt disponibile în meniul derulant Acțiuni din dreapta butonului Editare.',

    // Create View Help Text
    'LBL_HELP_CREATE' => 'Modulul {{plural_module_name}} vă permite să urmăriți vânzările individuale și articolele de linie aferente vânzărilor respective de la început până la sfârșit. Fiecare înregistrare {{module_name}} reprezintă o vânzare potențială și include date de vânzare relevante, precum și date legate de alte înregistrări importante cum ar fi {{quotes_module}}, {{contacts_module}} etc.

Pentru a crea un {{module_name}}:
1. Introduceți valori pentru câmpuri după cum doriți.
 - Câmpurile marcate cu „Obligatoriu” trebuie completate înainte de salvare.
 - Faceți clic pe „Afișare mai multe” pentru a afișa câmpuri suplimentare dacă este necesar.
2. Faceți clic pe „Salvare” pentru a finaliza înregistrarea nouă și pentru a reveni la pagina anterioară.',

// END ENT/ULT

    //Marketo
    'LBL_MKTO_SYNC' => 'Sincronizare către Marketo®',
    'LBL_MKTO_ID' => 'ID parte introductivă Marketo',

    'LBL_DASHLET_TOP10_SALES_OPPORTUNITIES_NAME' => 'Top 10 Oportunităţi de vânzări',
    'LBL_TOP10_OPPORTUNITIES_CHART_DESC' => 'Afişează top zece Oportunităţi într-o diagramă cu bule.',
    'LBL_TOP10_OPPORTUNITIES_MY_OPP' => 'Oportunităţile mele',
    'LBL_TOP10_OPPORTUNITIES_MY_TEAMS_OPP' => "Oportunităţile echipei mele",

    'LBL_PIPELINE_ERR_CLOSED_SALES_STAGE' => 'Nu se poate modifica {{fieldName}}, pentru că acest {{moduleSingular}} nu are articole deschise.',
    'TPL_ACTIVITY_TIMELINE_DASHLET' => 'Cronologie oportunitate',

    'LBL_CASCADE_SERVICE_WARNING' => ' nu poate fi setat pentru niciunul din aceste linii de venit deoarece nu sunt servicii. Doriți să continuați cu crearea?',
    'LBL_CASCADE_DURATION_WARNING' => ' nu poate fi setat pentru niciunul din aceste linii de venit deoarece nu sunt servicii. Doriți să continuați cu crearea?',

    // AI Predict
    'LBL_AI_OPPORTUNITY_CLOSE_PREDICTION_NAME' => 'Oportunitate Închidere Predicție',
    'LBL_AI_OPPORTUNITY_CLOSE_PREDICTION_DESC' => 'Vedeți detaliile de predicție pentru o anumită Oportunitate',
    'LBL_AI_WINRATE' => 'Rata de câștig',
    'LBL_AI_WONOPP' => 'Oportunități câștigate',
    'LBL_AI_CLOSINGTIME' => 'Ora închiderii',
    'LBL_AI_CLOSEDOPP' => 'Oportunități închise',
    'LBL_AI_LEADTIMESPAN' => 'Timpul dintre crearea oportunității și câștigul închis',
];
