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
    'LBL_OPPORTUNITIES_LIST_DASHBOARD' => 'Võimaluste loendi töölaud',
    'LBL_OPPORTUNITIES_RECORD_DASHBOARD' => 'Võimaluste kirjete töölaud',
    'LBL_OPPORTUNITIES_MULTI_LINE_DASHBOARD' => 'Möögivõimaluste fookuse sahtel - konsool',
    'LBL_OPPORTUNITIES_FOCUS_DRAWER_DASHBOARD' => 'Võimaluste fookuse sahtel',
    'LBL_RENEWAL_OPPORTUNITY' => 'Uuendamisvõimalus',

    'LBL_MODULE_NAME' => 'Müügivõimalused',
    'LBL_MODULE_NAME_SINGULAR' => 'Opportunity',
    'LBL_MODULE_TITLE' => 'Müügivõimalused: Avaleht',
    'LBL_SEARCH_FORM_TITLE' => 'Müügivõimaluse otsing',
    'LBL_VIEW_FORM_TITLE' => 'Müügivõimaluse vaade',
    'LBL_LIST_FORM_TITLE' => 'Müügivõimaluse loend',
    'LBL_OPPORTUNITY_NAME' => 'Müügivõimaluse nimi:',
    'LBL_OPPORTUNITY' => 'Müügivõimalus:',
    'LBL_NAME' => 'Müügivõimaluse nimi:',
    'LBL_TIME' => 'Aeg',
    'LBL_INVITEE' => 'Kontaktid',
    'LBL_CURRENCIES' => 'Valuutad',
    'LBL_LIST_OPPORTUNITY_NAME' => 'Nimi',
    'LBL_LIST_ACCOUNT_NAME' => 'Ettevõtte nimi',
    'LBL_LIST_DATE_CLOSED' => 'Sulge',
    'LBL_LIST_AMOUNT' => 'Müügivõimaluse summa',
    'LBL_LIST_AMOUNT_USDOLLAR' => 'Summa',
    'LBL_ACCOUNT_ID' => 'Ettevõtte ID',
    'LBL_CURRENCY_RATE' => 'Currency Rate',
    'LBL_CURRENCY_ID' => 'Valuuta ID',
    'LBL_CURRENCY_NAME' => 'Valuuta nimi',
    'LBL_CURRENCY_SYMBOL' => 'Valuuta sümbol',
//DON'T CONVERT THESE THEY ARE MAPPINGS
    'db_sales_stage' => 'LBL_LIST_SALES_STAGE',
    'db_name' => 'LBL_NAME',
    'db_amount' => 'LBL_LIST_AMOUNT',
    'db_date_closed' => 'LBL_LIST_DATE_CLOSED',
//END DON'T CONVERT
    'UPDATE' => 'Müügivõimalus - valuuta uuendamine',
    'UPDATE_DOLLARAMOUNTS' => 'Uuenda USD summasid',
    'UPDATE_VERIFY' => 'Kontrolli summasid',
    'UPDATE_VERIFY_TXT' => 'Konttrollib, et müügivõimaluste koguväärtused on kehtivad kümnendmurdudes ainult numbrisümbolites (0-9) ja kümnendikes(.)',
    'UPDATE_FIX' => 'Fix summad',
    'UPDATE_FIX_TXT' => 'Attempts to fix any invalid amounts by creating a valid decimal from the current amount. Any modified amount is backed up in the amount_backup database field. If you run this and notice bugs, do not rerun it without restoring from the backup as it may overwrite the backup with new invalid data.',
    'UPDATE_DOLLARAMOUNTS_TXT' => 'Update the U.S. Dollar amounts for sales based on the current set currency rates. This value is used to calculate Graphs and List View Currency Amounts.',
    'UPDATE_CREATE_CURRENCY' => 'Uue valuuta loomine:',
    'UPDATE_VERIFY_FAIL' => 'Kirje ebaõnnestumise kontroll:',
    'UPDATE_VERIFY_CURAMOUNT' => 'Praegune summa:',
    'UPDATE_VERIFY_FIX' => 'Running Fix would give',
    'UPDATE_INCLUDE_CLOSE' => 'Lisa suletud kirjed',
    'UPDATE_VERIFY_NEWAMOUNT' => 'Uus summa:',
    'UPDATE_VERIFY_NEWCURRENCY' => 'Uus valuuta:',
    'UPDATE_DONE' => 'Tehtud',
    'UPDATE_BUG_COUNT' => 'Bugid on leitud ja neid üritatakse lahendada:',
    'UPDATE_BUGFOUND_COUNT' => 'Leitud bugid:',
    'UPDATE_COUNT' => 'Uuendatud kirjed:',
    'UPDATE_RESTORE_COUNT' => 'Kirje summad taastatud:',
    'UPDATE_RESTORE' => 'Taasta summad',
    'UPDATE_RESTORE_TXT' => 'Restores amount values from the backups created during fix.',
    'UPDATE_FAIL' => 'Ei saa uuendada -',
    'UPDATE_NULL_VALUE' => 'Amount is NULL setting it to 0 -',
    'UPDATE_MERGE' => 'Mesti valuutad',
    'UPDATE_MERGE_TXT' => 'Mesti erinevad valuutad üheks valuutaks. Kui on ernevaid valuutakirjeid ühe valuuta kohta, siis mesti need kokku. See mestib valuutad ka kõigi teiste moodulite jaoks.',
    'LBL_ACCOUNT_NAME' => 'Ettevõtte nimi:',
    'LBL_CURRENCY' => 'Valuuta:',
    'LBL_DATE_CLOSED' => 'Oodatav sulgemiskuupäev:',
    'LBL_DATE_CLOSED_TIMESTAMP' => 'Expected Close Date Timestamp',
    'LBL_TYPE' => 'Tüüp:',
    'LBL_CAMPAIGN' => 'Kampaania:',
    'LBL_NEXT_STEP' => 'Järgmine samm:',
    'LBL_SERVICE_START_DATE' => 'Teenuse alguskuupäev',
    'LBL_LEAD_SOURCE' => 'Müügivihje allikas:',
    'LBL_SALES_STAGE' => 'Müügistaadium:',
    'LBL_SALES_STATUS' => 'Olek',
    'LBL_PROBABILITY' => 'Tõenäosus (%)',
    'LBL_DESCRIPTION' => 'Kirjeldus:',
    'LBL_DUPLICATE' => 'Võimalik topelt müügivõimalus',
    'MSG_DUPLICATE' => 'Müügivõimaluse kirje, mida hetkel lood võib olla duplikaat juba olemasolevast müügivõimaluse kirjest. Müügivõimaluse kirjed, mis sisaldavad sarnaseid nimesid ja/või aadresse on väljatoodud allpool. Kliki Salvesta selle uue müügivõimaluse loomiseks või kliki Tühista moodulisse tagasiminemiseks müügivõimalust loomata.',
    'LBL_NEW_FORM_TITLE' => 'Loo müügivõimalus',
    'LNK_NEW_OPPORTUNITY' => 'Loo müügivõimalus',
    'LNK_CREATE' => 'Create Deal',
    'LNK_OPPORTUNITY_LIST' => 'Vaata müügivõimalusi',
    'ERR_DELETE_RECORD' => 'Müügivõimaluse kustutamiseks täpsusta kirje numbrit.',
    'LBL_TOP_OPPORTUNITIES' => 'Minu Top avatud müügivõimalused',
    'NTC_REMOVE_OPP_CONFIRMATION' => 'Oled kindel, et soovid selle kontakti müügivõimalustest eemaldada?',
    'OPPORTUNITY_REMOVE_PROJECT_CONFIRM' => 'Oled kindel, et soovid selle müügivõimaluse projektist eemaldada?',
    'LBL_DEFAULT_SUBPANEL_TITLE' => 'Müügivõimalused',
    'LBL_ACTIVITIES_SUBPANEL_TITLE' => 'Tegevused',
    'LBL_HISTORY_SUBPANEL_TITLE' => 'Ajalugu',
    'LBL_RAW_AMOUNT' => 'Rea summa',
    'LBL_LEADS_SUBPANEL_TITLE' => 'Müügivihjed',
    'LBL_CONTACTS_SUBPANEL_TITLE' => 'Kontaktid',
    'LBL_DOCUMENTS_SUBPANEL_TITLE' => 'Dokumendid',
    'LBL_PROJECTS_SUBPANEL_TITLE' => 'Projektid',
    'LBL_ASSIGNED_TO_NAME' => 'Vastutaja:',
    'LBL_LIST_ASSIGNED_TO_NAME' => 'Määratud kasutaja',
    'LBL_LIST_SALES_STAGE' => 'Müügistaadium',
    'LBL_MY_CLOSED_OPPORTUNITIES' => 'Minu suletud müügivõimalused',
    'LBL_TOTAL_OPPORTUNITIES' => 'Müügivõimalused kokku',
    'LBL_CLOSED_WON_OPPORTUNITIES' => 'Suletud võidetud müügivõimalused',
    'LBL_ASSIGNED_TO_ID' => 'Määratud kasutaja:',
    'LBL_CREATED_ID' => 'Looja Id',
    'LBL_MODIFIED_ID' => 'Muutja Id',
    'LBL_MODIFIED_NAME' => 'Muutja nime järgi',
    'LBL_CREATED_USER' => 'Loodud kasutaja',
    'LBL_MODIFIED_USER' => 'Muudetud kasutaja',
    'LBL_CAMPAIGN_OPPORTUNITY' => 'Kampaaniad',
    'LBL_PROJECT_SUBPANEL_TITLE' => 'Projektid',
    'LABEL_PANEL_ASSIGNMENT' => 'Määramine',
    'LNK_IMPORT_OPPORTUNITIES' => 'Impordi müügivõimalused',
    'LBL_EDITLAYOUT' => 'Edit Layout' /*for 508 compliance fix*/,
    //For export labels
    'LBL_EXPORT_CAMPAIGN_ID' => 'Campaign ID',
    'LBL_OPPORTUNITY_TYPE' => 'Opportunity Type',
    'LBL_EXPORT_ASSIGNED_USER_NAME' => 'Assigned User Name',
    'LBL_EXPORT_ASSIGNED_USER_ID' => 'Assigned User ID',
    'LBL_EXPORT_MODIFIED_USER_ID' => 'Muutja ID',
    'LBL_EXPORT_CREATED_BY' => 'Created By ID',
    'LBL_EXPORT_NAME' => 'Nimi',
    // SNIP
    'LBL_CONTACT_HISTORY_SUBPANEL_TITLE' => 'Related Contacts&#39; Emails',
    'LBL_FILENAME' => 'Attachment',
    'LBL_PRIMARY_QUOTE_ID' => 'Primary Quote',
    'LBL_CONTRACTS' => 'Lepingud',
    'LBL_CONTRACTS_SUBPANEL_TITLE' => 'Lepingud',
    'LBL_PRODUCTS' => 'Pakkumuse artiklid',
    'LBL_RLI' => 'Revenue Line Items',
    'LNK_OPPORTUNITY_REPORTS' => 'Vaata müügivõimaluste aruandeid',
    'LBL_QUOTES_SUBPANEL_TITLE' => 'Pakkumised',
    'LBL_TEAM_ID' => 'Meeskonna ID:',
    'LBL_TIMEPERIODS' => 'Ajaperioodid',
    'LBL_TIMEPERIOD_ID' => 'Time Period ID',
    'LBL_COMMITTED' => 'Committed',
    'LBL_FORECAST' => 'Include in Forecast',
    'LBL_COMMIT_STAGE' => 'Commit Stage',
    'LBL_COMMIT_STAGE_FORECAST' => 'Prognoosi etapp',
    'LBL_WORKSHEET' => 'Worksheet',
    'LBL_PURCHASED_LINE_ITEMS' => 'Ostetud reaüksused',

    // KPI Metrics
    'LBL_ORGANIZE' => 'Korralda',
    'LBL_CREATE_NEW' => 'Loo uus',
    'LBL_MANAGE' => 'Halda',
    'LBL_SEE_DETAILS' => 'Kuva üksikasjad',
    'LBL_HIDE_NEW' => 'Peida',

    'LBL_FORECASTED_LIKELY' => 'Prognoosi järgi tõenäoline',
    'LBL_LOST' => 'Kaotatud',
    'LBL_RENEWAL' => 'Uuendamine',
    'LBL_RENEWAL_OPPORTUNITIES' => 'Uuendamise võimalused',
    'LBL_RENEWAL_PARENT' => 'Emavõimalus',
    'LBL_PARENT_RENEWAL_OPPORTUNITY_ID' => 'Uuenda ema ID-d',
    'LBL_MONTH_YEAR_RENEWAL' => '{{month}}, {{year}}',

    'LBL_WIDGET_SALES_STAGE' => 'Müügietapp',
    'LBL_WIDGET_DATE_CLOSED' => 'Eeldatav sulgemiskuupäev',
    'LBL_WIDGET_AMOUNT' => 'Summa',

    'TPL_RLI_CREATE' => 'An Opportunity must have an associated Revenue Line Item.',
    'TPL_RLI_CREATE_LINK_TEXT' => 'Create a Revenue Line Item.',
    'LBL_PRODUCTS_SUBPANEL_TITLE' => 'Pakkumuse artiklid',
    'LBL_RLI_SUBPANEL_TITLE' => 'Revenue Line Items',

    'LBL_TOTAL_RLIS' => '# of Total Revenue Line Items',
    'LBL_CLOSED_RLIS' => '# of Closed Revenue Line Items',
    'LBL_CLOSED_WON_RLIS' => '# lõpetatud võidetud tuluartiklitest',
    'LBL_SERVICE_OPEN_FLEX_DURATION_RLIS' => '# avatud teenuse paindliku kestuse tulude rea üksustest',
    'NOTICE_NO_DELETE_CLOSED_RLIS' => 'You cannot delete Opportunities that contain closed Revenue Line Items',
    'WARNING_NO_DELETE_CLOSED_SELECTED' => 'One or more of the selected records contains closed Revenue Line Items and cannot be deleted.',
    'LBL_INCLUDED_RLIS' => '# of Included Revenue Line Items',
    'LBL_UPDATE_OPPORTUNITIES_RLIS' => 'Värskendus avatud',
    'LBL_CASCADE_RLI_EDIT' => 'Uuenda avatud tulude rea üksusi',
    'LBL_CASCADE_RLI_CREATE' => 'Määra tulude rea üksuste ulatuses',
    'LBL_SERVICE_START_DATE_INVALID' => 'Ühegi avatud tulu reaüksuse lisandmooduli teenuse alguskuupäev ei tohi olla teenuse lõppkuupäevast hilisem.',

    'LBL_QUOTE_SUBPANEL_TITLE' => 'Quotes',
    'LBL_FILTER_OPPORTUNITY_TEMPLATE' => 'Müügivõimalused dünaamilise konto järgi',
    'LBL_TOP_10_OPP' => 'Avatud müügivõimaluste esikümme',
    'LBL_DASHLET_MY_ACTIVE_OPP' => 'Dashlet: minu aktiivsed müügivõimalused',
    'LBL_MY_ACTIVE_OPP' => 'Minu aktiivsed müügivõimalused',


    // Config
    'LBL_OPPS_CONFIG_VIEW_BY_LABEL' => 'Opportunity Hierarchy',
    'LBL_OPPS_CONFIG_VIEW_BY_DATE_ROLLUP' => 'Set the Expected Close Date field on the resulting Opportunity records to be the earliest or latest close dates of the existing Revenue Line Items',

    //Dashlet
    'LBL_PIPELINE_TOTAL_IS' => 'Pipeline Total is ',

    'LBL_OPPORTUNITY_ROLE' => 'Opportunity Role',
    'LBL_NOTES_SUBPANEL_TITLE' => 'Märkused:',
    'LBL_TAB_OPPORTUNITY' => 'Mooduli {{module}} ülevaade',

    // Help Text
    'LBL_OPPS_CONFIG_ALERT' => 'By clicking Confirm, you will be erasing ALL Forecasts data and changing your Opportunities View. If this is not what you intended, click cancel to return to previous settings.',
    'LBL_OPPS_CONFIG_ALERT_TO_OPPS' =>
        'Kui klõpsate valikut Kinnita, kustutate KÕIK prognooside andmed ja muudate oma müügivõimaluste vaadet. '
        . 'Samuti keelatakse KÕIK protsessimääratlused tulude rea üksuste sihtmoodulis. '
        . 'Kui te ei soovinud seda, klõpsake valikut Tühista, et naasta eelmiste sätete juurde.',
    'LBL_OPPS_CONFIG_SALES_STAGE_1a' => 'If all Revenue Line Items are closed and at least one was won,',
    'LBL_OPPS_CONFIG_SALES_STAGE_1b' => 'the Opportunity Sales Stage is set to "Closed Won".',
    'LBL_OPPS_CONFIG_SALES_STAGE_2a' => 'If all Revenue Line Items are in the "Closed Lost" Sales Stage,',
    'LBL_OPPS_CONFIG_SALES_STAGE_2b' => 'the Opportunity Sales Stage is set to "Closed Lost".',
    'LBL_OPPS_CONFIG_SALES_STAGE_3a' => 'If any Revenue Line Items are still open,',
    'LBL_OPPS_CONFIG_SALES_STAGE_3b' => 'the Opportunity will be marked with the least-advanced Sales Stage.',

// BEGIN ENT/ULT

    // Opps Config - View By Opportunities
    'LBL_HELP_CONFIG_OPPS' => 'After you initiate this change, the Revenue Line Item summarization notes will be built in the background. When the notes are complete and available, a notification will be sent to the email address on your user profile. If your instance is set up for {{forecasts_module}}, Sugar will also send you a notification when your {{module_name}} records are synced to the {{forecasts_module}} module and available for new {{forecasts_module}}. Please note that your instance must be configured to send email via Admin > Email Settings in order for the notifications to be sent.',

    // Opps Config - View By Opportunities And RLIs
    'LBL_HELP_CONFIG_RLIS' => 'After you initiate this change, Revenue Line Item records will be created for each existing {{module_name}} in the background. When the Revenue Line Items are complete and available, a notification will be sent to the email address on your user profile. Please note that your instance must be configured to send email via Admin > Email Settings in order for the notification to be sent.',
    // List View Help Text
    'LBL_HELP_RECORDS' => 'Moodul {{plural_module_name}} võimaldab teil jälgida individuaalset müüki algusest lõpuni. Iga mooduli {{module_name}} kirje kujutab endast potentsiaalset müüki ja sisaldab asjakohaseid müügiandmeid ning on seotud muude oluliste kirjetega, näiteks {{quotes_module}}, {{contacts_module}} jne. {{module_name}} läbib tavaliselt mitu müügietappi, kuni sellele on märgitud kas "Lõpetatud võidetud" või "Suletud kaotatud". Moodulit {{plural_module_name}} saab veelgi rohkem kasutada, kasutades Sugari moodulit {{forecasts_singular_module}}, et mõista ja ennustada müügitrende ning keskenduda tööle müügikvootide saavutamiseks.',

    // Record View Help Text
    'LBL_HELP_RECORD' => 'Moodul {{plural_module_name}} võimaldab jälgida algusest lõpuni üksikuid müüke ja nende müükide juurde kuuluvaid reaüksusi. Iga mooduli {{module_name}} kirje tähistab potentsiaalset müüki ja sisaldab asjakohaseid müügiandmeid, samuti muid olulisi kirjeid, nagu {{quotes_module}}, {{contacts_module}} jne.

- Saate muuta kirje välju, klõpsates üksikut välja või nuppu Muuda.
- Saate vaadata või muuta linke teistele alampaneelide kirjetele, lülitades vasakpoolse alumise paani valikule „Andmevaade“.
- Saate teha ja vaadata kasutajate kommentaare ning salvestada muudatuste ajalugu moodulis {{activitystream_singular_module}}, lülitades vasakpoolse alumise paani valikule „Tegevuste voog“.
- Saate jälgida seda kirjet või eelistada seda, kasutades kirje nimest paremal asuvaid ikoone.
- Täiendavad toimingud on saadaval nupust Muuda paremal asuvas rippmenüüs Toimingud.',

    // Create View Help Text
    'LBL_HELP_CREATE' => 'Moodul {{plural_module_name}} võimaldab jälgida algusest lõpuni üksikuid müüke ja nende müükide juurde kuuluvaid reaüksusi. Iga kirje {{module_name}} tähistab potentsiaalset müüki ja sisaldab asjakohaseid müügiandmeid, samuti muid olulisi kirjeid, nagu {{quotes_module}}, {{contacts_module}} jne.

{{module_name}} loomiseks toimige järgmiselt.
1. Sisestage soovitud väljade väärtused.
 - Väljad märgistusega „Nõutav“ peavad olema enne salvestamist täidetud.
 - Vajadusel klõpsake lisaväljade kuvamiseks nuppu „Kuva rohkem“.
2. Uue kirje lõpetamiseks ja eelmisele lehele naasmiseks klõpsake „Salvesta“.',

// END ENT/ULT

    //Marketo
    'LBL_MKTO_SYNC' => 'Sync to Marketo&reg;',
    'LBL_MKTO_ID' => 'Marketo Lead ID',

    'LBL_DASHLET_TOP10_SALES_OPPORTUNITIES_NAME' => 'Top 10 Sales Opportunities',
    'LBL_TOP10_OPPORTUNITIES_CHART_DESC' => 'Displays top ten Opportunities in a bubble chart.',
    'LBL_TOP10_OPPORTUNITIES_MY_OPP' => 'My Opportunities',
    'LBL_TOP10_OPPORTUNITIES_MY_TEAMS_OPP' => "My Team's Opportunities",

    'LBL_PIPELINE_ERR_CLOSED_SALES_STAGE' => 'Moodulit {{fieldName}} ei saa muuta kuna moodulil {{moduleSingular}} ei ole avatud reaüksuseid.',
    'TPL_ACTIVITY_TIMELINE_DASHLET' => 'Võimaluse ajaskaala',

    'LBL_CASCADE_SERVICE_WARNING' => ' ei saa seadistada ühegi tulude rea üksuse ulatuses, kuna need ei ole teenused. Kas soovite jätkata ilma loomiseta?',
    'LBL_CASCADE_DURATION_WARNING' => ' ei saa seadistada ühegi tulude rea üksuse ulatuses, kuna nende kestused on lukustatud. Kas soovite jätkata ilma loomiseta?',

    // AI Predict
    'LBL_AI_OPPORTUNITY_CLOSE_PREDICTION_NAME' => 'Müügivõimaluse sulgemise prognoos',
    'LBL_AI_OPPORTUNITY_CLOSE_PREDICTION_DESC' => 'Kuva konkreetse müügivõimaluse prognoosi üksikasjad',
    'LBL_AI_WINRATE' => 'Võidumäär',
    'LBL_AI_WONOPP' => 'Võidetud võimalused',
    'LBL_AI_CLOSINGTIME' => 'Sulgemise aeg',
    'LBL_AI_CLOSEDOPP' => 'Suletud võimalused',
    'LBL_AI_LEADTIMESPAN' => 'Aeg võimaluste loomise ja suletud võidu vahel',
];
