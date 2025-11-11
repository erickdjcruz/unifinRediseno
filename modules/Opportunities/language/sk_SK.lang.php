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
    'LBL_OPPORTUNITIES_LIST_DASHBOARD' => 'Informačný panel so zoznamom príležitostí',
    'LBL_OPPORTUNITIES_RECORD_DASHBOARD' => 'Informačný panel so záznamom príležitostí',
    'LBL_OPPORTUNITIES_MULTI_LINE_DASHBOARD' => 'Informačný panel so zameraním na príležitosti – Konzola',
    'LBL_OPPORTUNITIES_FOCUS_DRAWER_DASHBOARD' => 'Zásuvka zamerania na príležitosti',
    'LBL_RENEWAL_OPPORTUNITY' => 'Príležitosť obnovenia',

    'LBL_MODULE_NAME' => 'Obchodné príležitosti',
    'LBL_MODULE_NAME_SINGULAR' => 'Obchodná príležitosť',
    'LBL_MODULE_TITLE' => 'Obchodné príležitosti: Domov',
    'LBL_SEARCH_FORM_TITLE' => 'Vyhľadávanie obchodnej príležitosti',
    'LBL_VIEW_FORM_TITLE' => 'Zobrazenie obchodných príležitostí',
    'LBL_LIST_FORM_TITLE' => 'Zoznam obchodných príležitostí',
    'LBL_OPPORTUNITY_NAME' => 'Názov obchodnej príležitosti:',
    'LBL_OPPORTUNITY' => 'Obchodná príležitosť:',
    'LBL_NAME' => 'Názov obchodnej príležitosti:',
    'LBL_TIME' => 'Čas',
    'LBL_INVITEE' => 'Kontakty',
    'LBL_CURRENCIES' => 'Meny',
    'LBL_LIST_OPPORTUNITY_NAME' => 'Názov',
    'LBL_LIST_ACCOUNT_NAME' => 'Názov účtu',
    'LBL_LIST_DATE_CLOSED' => 'Predpokladaný dátum uzávierky',
    'LBL_LIST_AMOUNT' => 'Pravdepodobné',
    'LBL_LIST_AMOUNT_USDOLLAR' => 'Konvertovaná suma',
    'LBL_ACCOUNT_ID' => 'ID účtu',
    'LBL_CURRENCY_RATE' => 'Menový kurz',
    'LBL_CURRENCY_ID' => 'ID meny',
    'LBL_CURRENCY_NAME' => 'Názov meny',
    'LBL_CURRENCY_SYMBOL' => 'Symbol meny',
//DON'T CONVERT THESE THEY ARE MAPPINGS
    'db_sales_stage' => 'LBL_LIST_SALES_STAGE',
    'db_name' => 'LBL_NAME',
    'db_amount' => 'LBL_LIST_AMOUNT',
    'db_date_closed' => 'LBL_LIST_DATE_CLOSED',
//END DON'T CONVERT
    'UPDATE' => 'Obchodná príležitosť – aktualizácia meny',
    'UPDATE_DOLLARAMOUNTS' => 'Aktualizácia súm v EUR',
    'UPDATE_VERIFY' => 'Overiť sumy',
    'UPDATE_VERIFY_TXT' => 'Overí, že hodnoty sumy v príležitostiach sú platné desatinné čísla s iba číselnými znakmi (0 – 9) a desatinnými miestami (,)',
    'UPDATE_FIX' => 'Stanoviť sumy',
    'UPDATE_FIX_TXT' => 'Pokúša sa opraviť neplatné sumy vytváraním platných desatinných čísiel z aktuálnej sumy. Akákoľvek upravená suma je zálohovaná v databázovom poli amount_backup. Ak si počas tohto postupu všimnete chyby, neskúšajte to znovu bez obnovy zálohy, mohli by ste totiž prepísať zálohu neplatnými údajmi.',
    'UPDATE_DOLLARAMOUNTS_TXT' => 'Aktualizujte sumy Eur pre príležitosti na základe aktuálneho menového kurzu. Táto hodnota sa používa na vypočítanie súm v danej mene pre grafy a náhľad zoznamu.',
    'UPDATE_CREATE_CURRENCY' => 'Vytvorenie novej meny:',
    'UPDATE_VERIFY_FAIL' => 'Záznam sa nepodarilo overiť:',
    'UPDATE_VERIFY_CURAMOUNT' => 'Aktuálna suma:',
    'UPDATE_VERIFY_FIX' => 'Spustenie opravy znamená',
    'UPDATE_INCLUDE_CLOSE' => 'Zahrnúť uzatvorené záznamy',
    'UPDATE_VERIFY_NEWAMOUNT' => 'Nová suma:',
    'UPDATE_VERIFY_NEWCURRENCY' => 'Nová mena:',
    'UPDATE_DONE' => 'Hotovo',
    'UPDATE_BUG_COUNT' => 'Nájdené chyby a pokusy o vyriešenie:',
    'UPDATE_BUGFOUND_COUNT' => 'Nájdené chyby:',
    'UPDATE_COUNT' => 'Aktualizované záznamy:',
    'UPDATE_RESTORE_COUNT' => 'Obnovené sumy záznamov:',
    'UPDATE_RESTORE' => 'Obnoviť sumy',
    'UPDATE_RESTORE_TXT' => 'Obnovuje hodnoty súm zo zálohy vytvorenej počas opravy.',
    'UPDATE_FAIL' => 'Nemožno aktualizovať -',
    'UPDATE_NULL_VALUE' => 'Množstvo je NULA nastavené na 0 -',
    'UPDATE_MERGE' => 'Zlúčiť meny',
    'UPDATE_MERGE_TXT' => 'Zlúčenie viacerých mien do jednej meny. Ak je tu viac záznamov meny pre rovnakú menu, zlúčte ich do jednej. Zlúčia sa tak aj meny všetkých modulov.',
    'LBL_ACCOUNT_NAME' => 'Názov Účtu:',
    'LBL_CURRENCY' => 'Mena:',
    'LBL_DATE_CLOSED' => 'Očakávaný dátum uzávierky:',
    'LBL_DATE_CLOSED_TIMESTAMP' => 'Očakávaný dátum a čas uzávierky',
    'LBL_TYPE' => 'Typ:',
    'LBL_CAMPAIGN' => 'Kampaň:',
    'LBL_NEXT_STEP' => 'Ďalší krok',
    'LBL_SERVICE_START_DATE' => 'Dátum začiatku služby',
    'LBL_LEAD_SOURCE' => 'Zdroj príležitostí',
    'LBL_SALES_STAGE' => 'Fáza predaja',
    'LBL_SALES_STATUS' => 'Stav',
    'LBL_PROBABILITY' => 'Pravdepodobnosť (%):',
    'LBL_DESCRIPTION' => 'Popis',
    'LBL_DUPLICATE' => 'Možná duplicita obchodnej príležitosti',
    'MSG_DUPLICATE' => 'Vytvárate záznam duplicitný k záznamu príležitosti, ktorý už existuje. V nižšie uvedenom zozname sú záznamy príležitostí obsahujúce podobné názvy.<br>Kliknite na možnosť Uložiť a pokračujte s vytvorením tejto novej príležitosti alebo na možnosť Zrušiť a prejdite späť do modulu bez vytvorenia príležitosti.',
    'LBL_NEW_FORM_TITLE' => 'Vytvoriť príležitosť',
    'LNK_NEW_OPPORTUNITY' => 'Vytvoriť príležitosť',
    'LNK_CREATE' => 'Vytvoriť obchod',
    'LNK_OPPORTUNITY_LIST' => 'Zobraziť príležitosti',
    'ERR_DELETE_RECORD' => 'Na odstránenie príležitosti musíte zadať číslo záznamu.',
    'LBL_TOP_OPPORTUNITIES' => 'Moje najlepšie otvorené príležitosti',
    'NTC_REMOVE_OPP_CONFIRMATION' => 'Naozaj chcete odstrániť tento kontakt z príležitosti?',
    'OPPORTUNITY_REMOVE_PROJECT_CONFIRM' => 'Naozaj chcete odstrániť túto príležitosť z projektu?',
    'LBL_DEFAULT_SUBPANEL_TITLE' => 'Obchodné príležitosti',
    'LBL_ACTIVITIES_SUBPANEL_TITLE' => 'Aktivity',
    'LBL_HISTORY_SUBPANEL_TITLE' => 'História',
    'LBL_RAW_AMOUNT' => 'Hrubá suma',
    'LBL_LEADS_SUBPANEL_TITLE' => 'Záujemcovia',
    'LBL_CONTACTS_SUBPANEL_TITLE' => 'Kontakty',
    'LBL_DOCUMENTS_SUBPANEL_TITLE' => 'Dokumenty',
    'LBL_PROJECTS_SUBPANEL_TITLE' => 'Projekty',
    'LBL_ASSIGNED_TO_NAME' => 'Priradené k:',
    'LBL_LIST_ASSIGNED_TO_NAME' => 'Priradený používateľ',
    'LBL_LIST_SALES_STAGE' => 'Fáza predaja',
    'LBL_MY_CLOSED_OPPORTUNITIES' => 'Moje uzatvorené príležitosti',
    'LBL_TOTAL_OPPORTUNITIES' => 'Celkový počet príležitosti',
    'LBL_CLOSED_WON_OPPORTUNITIES' => 'Príležitosti uzatvorené/získané',
    'LBL_ASSIGNED_TO_ID' => 'Priradený používateľ:',
    'LBL_CREATED_ID' => 'Vytvorené používateľom s ID',
    'LBL_MODIFIED_ID' => 'Upravené používateľom s ID',
    'LBL_MODIFIED_NAME' => 'Upravené používateľom s menom',
    'LBL_CREATED_USER' => 'Vytvorený používateľ',
    'LBL_MODIFIED_USER' => 'Upravený používateľ',
    'LBL_CAMPAIGN_OPPORTUNITY' => 'Príležitosť v rámci kampane',
    'LBL_PROJECT_SUBPANEL_TITLE' => 'Projekty',
    'LABEL_PANEL_ASSIGNMENT' => 'Priradenie',
    'LNK_IMPORT_OPPORTUNITIES' => 'importovať príležitosti',
    'LBL_EDITLAYOUT' => 'Upraviť rozloženie' /*for 508 compliance fix*/,
    //For export labels
    'LBL_EXPORT_CAMPAIGN_ID' => 'ID kampane',
    'LBL_OPPORTUNITY_TYPE' => 'Typ príležitostí',
    'LBL_EXPORT_ASSIGNED_USER_NAME' => 'Priradené meno používateľa',
    'LBL_EXPORT_ASSIGNED_USER_ID' => 'Priradené používateľské ID',
    'LBL_EXPORT_MODIFIED_USER_ID' => 'Upravené používateľom s ID',
    'LBL_EXPORT_CREATED_BY' => 'Vytvorené používateľom s ID',
    'LBL_EXPORT_NAME' => 'Meno',
    // SNIP
    'LBL_CONTACT_HISTORY_SUBPANEL_TITLE' => 'E-maily súvisiacich kontaktov',
    'LBL_FILENAME' => 'Príloha',
    'LBL_PRIMARY_QUOTE_ID' => 'Primárna ponuka',
    'LBL_CONTRACTS' => 'Kontrakty',
    'LBL_CONTRACTS_SUBPANEL_TITLE' => 'Kontrakty',
    'LBL_PRODUCTS' => 'Položky ponuky',
    'LBL_RLI' => 'Položky krivky výnosu',
    'LNK_OPPORTUNITY_REPORTS' => 'Zobraziť hlásenia o príležitostiach',
    'LBL_QUOTES_SUBPANEL_TITLE' => 'Ponuky',
    'LBL_TEAM_ID' => 'ID tímu',
    'LBL_TIMEPERIODS' => 'Časové obdobia',
    'LBL_TIMEPERIOD_ID' => 'ID časového obdobia',
    'LBL_COMMITTED' => 'Schválené',
    'LBL_FORECAST' => 'Zahrnúť do prognózy',
    'LBL_COMMIT_STAGE' => 'Fáza schválenia',
    'LBL_COMMIT_STAGE_FORECAST' => 'Fáza prognózy',
    'LBL_WORKSHEET' => 'Tabuľka',
    'LBL_PURCHASED_LINE_ITEMS' => 'Zakúpené riadkové položky',

    // KPI Metrics
    'LBL_ORGANIZE' => 'Organizovať',
    'LBL_CREATE_NEW' => 'Vytvoriť nový',
    'LBL_MANAGE' => 'Spravovať',
    'LBL_SEE_DETAILS' => 'Zobraziť podrobnosti',
    'LBL_HIDE_NEW' => 'Skryť',

    'LBL_FORECASTED_LIKELY' => 'Prognózované ako pravdepodobné',
    'LBL_LOST' => 'Nezískané',
    'LBL_RENEWAL' => 'Obnovenie',
    'LBL_RENEWAL_OPPORTUNITIES' => 'Príležitosti obnovenia',
    'LBL_RENEWAL_PARENT' => 'Nadradená príležitosť',
    'LBL_PARENT_RENEWAL_OPPORTUNITY_ID' => 'ID nadradeného obnovenia',
    'LBL_MONTH_YEAR_RENEWAL' => '{{month}}, {{year}}',

    'LBL_WIDGET_SALES_STAGE' => 'Fáza predaja',
    'LBL_WIDGET_DATE_CLOSED' => 'Predpokladaný dátum uzavretia',
    'LBL_WIDGET_AMOUNT' => 'Suma',

    'TPL_RLI_CREATE' => 'Príležitosť musí mať priradenú položku krivky výnosu.',
    'TPL_RLI_CREATE_LINK_TEXT' => 'Vytvoriť položku krivky výnosu.',
    'LBL_PRODUCTS_SUBPANEL_TITLE' => 'Položky ponuky',
    'LBL_RLI_SUBPANEL_TITLE' => 'Položky krivky výnosu',

    'LBL_TOTAL_RLIS' => '# z celkových položiek krivky výnosu',
    'LBL_CLOSED_RLIS' => '# z uzavretých položiek krivky výnosu',
    'LBL_CLOSED_WON_RLIS' => '# z uzavretých získaných riadkových položiek výnosov',
    'LBL_SERVICE_OPEN_FLEX_DURATION_RLIS' => '# riadkových položiek pre výnosy počas flexibilného trvania otvorenej služby',
    'NOTICE_NO_DELETE_CLOSED_RLIS' => 'Nemôžete vymazať príležitosti, ktoré obsahujú uzavreté položky krivky výnosu',
    'WARNING_NO_DELETE_CLOSED_SELECTED' => 'Jeden alebo viac z vybratých záznamov obsahuje uzavreté položky krivky výnosu a nemôže byť odstránený.',
    'LBL_INCLUDED_RLIS' => '# zo zahrnutých položiek krivky výnosu',
    'LBL_UPDATE_OPPORTUNITIES_RLIS' => 'Aktualizácia otvorená',
    'LBL_CASCADE_RLI_EDIT' => 'Aktualizovať otvorené riadkové položky výnosov',
    'LBL_CASCADE_RLI_CREATE' => 'Nastaviť naprieč riadkovými položkami výnosov',
    'LBL_SERVICE_START_DATE_INVALID' => 'Dátum začatia služby nie je možné nastaviť po dátume ukončenia služby žiadnych otvorených doplnkových položiek krivky výnosu.',

    'LBL_QUOTE_SUBPANEL_TITLE' => 'Ponuky',
    'LBL_FILTER_OPPORTUNITY_TEMPLATE' => 'Príležitosti podľa dynamického účtu',
    'LBL_TOP_10_OPP' => 'Top 10 otvorených príležitostí',
    'LBL_DASHLET_MY_ACTIVE_OPP' => 'Dashlet: Moje aktívne príležitosti',
    'LBL_MY_ACTIVE_OPP' => 'Moje aktívne príležitosti',


    // Config
    'LBL_OPPS_CONFIG_VIEW_BY_LABEL' => 'Hierarchia príležitostí',
    'LBL_OPPS_CONFIG_VIEW_BY_DATE_ROLLUP' => 'Nastavte pole predpokladaného termínu uzávierky pre výsledné záznamy príležitostí na najskoršie alebo najneskoršie termíny uzávierky existujúcich položiek krivky výnosu',

    //Dashlet
    'LBL_PIPELINE_TOTAL_IS' => 'Celková pipeline je ',

    'LBL_OPPORTUNITY_ROLE' => 'Rola obchodnej príležirosti',
    'LBL_NOTES_SUBPANEL_TITLE' => 'Poznámky',
    'LBL_TAB_OPPORTUNITY' => 'Revízia {{module}}',

    // Help Text
    'LBL_OPPS_CONFIG_ALERT' => 'Kliknutím na tlačidlo Potvrdiť vymažete VŠETKY dáta prognóz a zmeníte zobrazenie príležitostí. Ak tento krok nebol vaším zámerom, kliknutím na tlačidlo Zrušiť sa vrátite do predchádzajúcich nastavení.',
    'LBL_OPPS_CONFIG_ALERT_TO_OPPS' =>
        'Kliknutím na tlačidlo Potvrdiť vymažete VŠETKY údaje prognóz a zmeníte zobrazenie príležitostí. '
        . 'Blokované budú takisto VŠETKY definície procesov s cieľovým modulom položiek krivky výnosu. '
        . 'Ak tento krok nebol vaším zámerom, kliknutím na tlačidlo Zrušiť sa vrátite do predchádzajúcich nastavení.',
    'LBL_OPPS_CONFIG_SALES_STAGE_1a' => 'Ak sú všetky položky krivky výnosu uzavreté a aspoň jedna bola získaná,',
    'LBL_OPPS_CONFIG_SALES_STAGE_1b' => 'fáza predaja príležitosti sa nastaví do stavu "Uzavreté/získané".',
    'LBL_OPPS_CONFIG_SALES_STAGE_2a' => 'Ak sú všetky položky krivky výnosu v stave fázy predaja "Uzavreté/nezískané",',
    'LBL_OPPS_CONFIG_SALES_STAGE_2b' => 'fáza predaja príležitosti sa nastaví do stavu "Uzavreté/nezískané".',
    'LBL_OPPS_CONFIG_SALES_STAGE_3a' => 'Ak sú ešte nejaké položky krivky výnosu otvorené,',
    'LBL_OPPS_CONFIG_SALES_STAGE_3b' => 'príležitosť sa označí s najmenej pokročilou fázou predaja.',

// BEGIN ENT/ULT

    // Opps Config - View By Opportunities
    'LBL_HELP_CONFIG_OPPS' => 'Po vykonaní tejto zmeny budú v pozadí vytvorené poznámky sumarizácie položky krivky výnosu. Keď sú poznámky kompletné a dostupné, odošle sa upozornenie na e-mail vo vašom profile. Ak je vaša inštancia nastavená na {{forecasts_module}}, aplikácia Sugar vám pošle aj upozornenie, hneď ako budú záznamy {{module_name}} synchronizované do modulu {{forecasts_module}} a dostupné pre nový {{forecasts_module}}. Na to, aby vaša inštancia odosielala e-maily, musí byť správne nastavená v časti Administrátor > Nastavenia e-mailu.',

    // Opps Config - View By Opportunities And RLIs
    'LBL_HELP_CONFIG_RLIS' => 'Po vykonaní tejto zmeny budú v pozadí vytvorené záznamy položiek krivky výnosu pre každý existujúci {{module_name}}. Keď sú položky krivky výnosu kompletné a dostupné, odošle sa upozornenie na e-mail vo vašom profile. Na to, aby vaša inštancia odosielala e-maily, musí byť správne nastavená v časti Administrátor > Nastavenia e-mailu.',
    // List View Help Text
    'LBL_HELP_RECORDS' => 'Modul {{plural_module_name}} umožňuje sledovať jednotlivé predaje od začiatku až do konca. Každý záznam {{module_name}} predstavuje potenciálny predaj a obsahuje príslušné údaje predaja, ako aj údaje súvisiace s inými dôležitými záznamami, ako napr. {{quotes_module}}, {{contacts_module}} atď. Modul {{module_name}} štandardne prejde niekoľkými fázami predaja a na záver sa označí buď stavom „Uzatvorené/získané“, alebo „Uzatvorené/nezískané“. Moduly {{plural_module_name}} možno využiť ešte viac použitím modulu {{forecasts_singular_module}} aplikácie Sugar, vďaka čomu možno porozumieť trendom predaja a predpovedať ich, ako aj zamerať prácu s cieľom dosiahnuť kvóty predaja.',

    // Record View Help Text
    'LBL_HELP_RECORD' => 'Modul {{plural_module_name}} umožňuje sledovať jednotlivé predaje a riadkové položky prislúchajúce k týmto predajom od začiatku až do konca. Každý záznam {{module_name}} predstavuje potenciálny predaj a obsahuje príslušné údaje predaja, ako aj údaje súvisiace s inými dôležitými záznamami, ako napr. {{quotes_module}}, {{contacts_module}} atď. 

– Polia tohto záznamu upravíte kliknutím na jednotlivé polia alebo na tlačidlo Upraviť.
– Prepojenia na ďalšie záznamy v pomocných paneloch možno zobraziť alebo upraviť prepnutím ľavého spodného panela na „Zobrazenie údajov“.
– Poznámky používateľa a históriu zmien záznamov môžete vytvoriť a zobraziť v module {{activitystream_singular_module}} prepnutím ľavého spodného panela na „Aktivity“.
– Záznam môžete sledovať alebo označiť ako obľúbený pomocou ikon nachádzajúcich sa napravo od názvu záznamu.
– Ďalšie akcie sú k dispozícii v rozbaľovacej ponuke Akcie napravo od tlačidla Upraviť.',

    // Create View Help Text
    'LBL_HELP_CREATE' => 'Modul {{plural_module_name}} umožňuje sledovať jednotlivé predaje a riadkové položky prislúchajúce k týmto predajom od začiatku až do konca. Každý záznam {{module_name}} predstavuje potenciálny predaj a obsahuje príslušné údaje predaja, ako aj údaje súvisiace s inými dôležitými záznamami, napr. {{quotes_module}}, {{contacts_module}} atď.

Ak chcete vytvoriť modul {{module_name}}:
1. Zadajte požadované hodnoty do polí.
 – Polia označené ako „Povinné“ treba pred uložením vyplniť.
 – Ak chcete rozbaliť dodatočné polia, kliknite na možnosť „Zobraziť viac“.
2. Kliknite na možnosť „Uložiť“ na dokončenie nového záznamu a návrat na predchádzajúcu stránku.',

// END ENT/ULT

    //Marketo
    'LBL_MKTO_SYNC' => 'Synchronizovať s Marketo&reg;',
    'LBL_MKTO_ID' => 'ID záujmecu o Marketo',

    'LBL_DASHLET_TOP10_SALES_OPPORTUNITIES_NAME' => 'Top 10 predajných príležitostí',
    'LBL_TOP10_OPPORTUNITIES_CHART_DESC' => 'Zobrazí desať najlepších príležitostí v bublinovom grafe.',
    'LBL_TOP10_OPPORTUNITIES_MY_OPP' => 'Moje príležitosti',
    'LBL_TOP10_OPPORTUNITIES_MY_TEAMS_OPP' => "Príležitosti môjho tímu",

    'LBL_PIPELINE_ERR_CLOSED_SALES_STAGE' => 'Nie je možné zmeniť {{fieldName}}, pretože tento {{moduleSingular}} nemá žiadne otvorené riadkové položky.',
    'TPL_ACTIVITY_TIMELINE_DASHLET' => 'Časová os príležitosti',

    'LBL_CASCADE_SERVICE_WARNING' => ' nie je možné nastaviť naprieč žiadnou z týchto riadkových položiek výnosov, pretože nejde o služby. Chcete pokračovať vo vytváraní?',
    'LBL_CASCADE_DURATION_WARNING' => ' nie je možné nastaviť naprieč žiadnou z týchto riadkových položiek výnosov, pretože ich trvanie je uzamknuté. Chcete pokračovať vo vytváraní?',

    // AI Predict
    'LBL_AI_OPPORTUNITY_CLOSE_PREDICTION_NAME' => 'Predpoveď uzatvorenia príležitosti',
    'LBL_AI_OPPORTUNITY_CLOSE_PREDICTION_DESC' => 'Zobraziť podrobnosti predpovede pre konkrétnu príležitosť',
    'LBL_AI_WINRATE' => 'Miera získania',
    'LBL_AI_WONOPP' => 'Získané príležitosti',
    'LBL_AI_CLOSINGTIME' => 'Čas uzatvorenia',
    'LBL_AI_CLOSEDOPP' => 'Uzatvorené príležitosti',
    'LBL_AI_LEADTIMESPAN' => 'Čas medzi vytvorením príležitosti a uzatvorením/získaním',
];
