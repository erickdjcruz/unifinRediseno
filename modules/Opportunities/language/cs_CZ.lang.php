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
    'LBL_OPPORTUNITIES_LIST_DASHBOARD' => 'Řídicí panel seznamu příležitostí',
    'LBL_OPPORTUNITIES_RECORD_DASHBOARD' => 'Řídicí panel záznamů příležitostí',
    'LBL_OPPORTUNITIES_MULTI_LINE_DASHBOARD' => 'Zásuvka zaměření na příležitosti – Konzola',
    'LBL_OPPORTUNITIES_FOCUS_DRAWER_DASHBOARD' => 'Zásuvka zaměření na příležitosti',
    'LBL_RENEWAL_OPPORTUNITY' => 'Příležitost obnovení',

    'LBL_MODULE_NAME' => 'Obchody',
    'LBL_MODULE_NAME_SINGULAR' => 'Příležitost',
    'LBL_MODULE_TITLE' => 'Obchody: Hlavní stránka',
    'LBL_SEARCH_FORM_TITLE' => 'Vyhledat obchody',
    'LBL_VIEW_FORM_TITLE' => 'Přehled obchodů',
    'LBL_LIST_FORM_TITLE' => 'Seznam obchodů',
    'LBL_OPPORTUNITY_NAME' => 'Název obchodu:',
    'LBL_OPPORTUNITY' => 'Obchody:',
    'LBL_NAME' => 'Název příležitosti',
    'LBL_TIME' => 'Čas',
    'LBL_INVITEE' => 'Kontakty',
    'LBL_CURRENCIES' => 'Měny',
    'LBL_LIST_OPPORTUNITY_NAME' => 'Název',
    'LBL_LIST_ACCOUNT_NAME' => 'Jméno společnosti',
    'LBL_LIST_DATE_CLOSED' => 'Předpokládané datum uzavření',
    'LBL_LIST_AMOUNT' => 'Pravděpodobně',
    'LBL_LIST_AMOUNT_USDOLLAR' => 'Celková hodnota',
    'LBL_ACCOUNT_ID' => 'ID společnosti',
    'LBL_CURRENCY_RATE' => 'Kurz měny',
    'LBL_CURRENCY_ID' => 'ID měny',
    'LBL_CURRENCY_NAME' => 'Název měny',
    'LBL_CURRENCY_SYMBOL' => 'Symbol měny',
//DON'T CONVERT THESE THEY ARE MAPPINGS
    'db_sales_stage' => 'LBL_LIST_SALES_STAGE',
    'db_name' => 'LBL_NAME',
    'db_amount' => 'LBL_LIST_AMOUNT',
    'db_date_closed' => 'LBL_LIST_DATE_CLOSED',
//END DON'T CONVERT
    'UPDATE' => 'Obchod- Aktualizace měny',
    'UPDATE_DOLLARAMOUNTS' => 'Aktualizace CZK částek',
    'UPDATE_VERIFY' => 'Ověření částek',
    'UPDATE_VERIFY_TXT' => 'Ověří, že číselná pole zadané v obchodem jsou platná desetinná čísla - obsahují pouze číslice (0-9) and desetinou tečku(.)',
    'UPDATE_FIX' => 'Opravit částky',
    'UPDATE_FIX_TXT' => 'Pokus o opravu neplatné částky vytvořením platného čísla ze zadaných částek. Pro modifikované částky bude vytvořena záloha v databázovém poli amount_backup. Pokud zjistíte, že je tato procedura chybná, nespouštějte ji znovu bez předchozího obnovení částek ze zálohy, jinak byste mohli přepsat zazálohované částky neplatnými údaji.',
    'UPDATE_DOLLARAMOUNTS_TXT' => 'Aktualizovat částky CZK na základě současných kurzů měn. Tato částka je použitá pro výpočet grafů a v seznamu Zobrazit částky v cizí měně.',
    'UPDATE_CREATE_CURRENCY' => 'Vytvoření nové měny:',
    'UPDATE_VERIFY_FAIL' => 'Ověření záznamu selhalo:',
    'UPDATE_VERIFY_CURAMOUNT' => 'Aktuální částka:',
    'UPDATE_VERIFY_FIX' => 'Spuštění opravy znamená',
    'UPDATE_INCLUDE_CLOSE' => 'Včetně uzavřených záznamů',
    'UPDATE_VERIFY_NEWAMOUNT' => 'Nová částka:',
    'UPDATE_VERIFY_NEWCURRENCY' => 'Nová měna:',
    'UPDATE_DONE' => 'Dokončeno',
    'UPDATE_BUG_COUNT' => 'Byly nalezeny chyby a proběhla snaha je opravit:',
    'UPDATE_BUGFOUND_COUNT' => 'Počet nalezených chyb:',
    'UPDATE_COUNT' => 'Aktualizováno záznamů:',
    'UPDATE_RESTORE_COUNT' => 'Obnoveno záznamů:',
    'UPDATE_RESTORE' => 'Obnovit částky',
    'UPDATE_RESTORE_TXT' => 'Obnovit hodnoty částek ze zálohy vytvořené během opravy.',
    'UPDATE_FAIL' => 'Není možno aktualizovat -',
    'UPDATE_NULL_VALUE' => 'Částka je NULL - nastavena na 0 -',
    'UPDATE_MERGE' => 'Sloučit měny',
    'UPDATE_MERGE_TXT' => 'Sloučit různé měny do jedné. Pokud zjistíte, že existují pro stejnou měnu různé záznamy, můžete je sloučit dohromady. Měny budou sloučeny také ve všech ostatních modulech.',
    'LBL_ACCOUNT_NAME' => 'Název společnosti:',
    'LBL_CURRENCY' => 'Měna:',
    'LBL_DATE_CLOSED' => 'Předpokládané datum uzavření:',
    'LBL_DATE_CLOSED_TIMESTAMP' => 'Předpokládané datum a čas uzavření',
    'LBL_TYPE' => 'Typ:',
    'LBL_CAMPAIGN' => 'Kampaň:',
    'LBL_NEXT_STEP' => 'Další krok:',
    'LBL_SERVICE_START_DATE' => 'Datum zahájení služby',
    'LBL_LEAD_SOURCE' => 'Zdrojový zájemce',
    'LBL_SALES_STAGE' => 'Fáze prodeje:',
    'LBL_SALES_STATUS' => 'Stav',
    'LBL_PROBABILITY' => 'Pravděpodobnost (%)',
    'LBL_DESCRIPTION' => 'Popis',
    'LBL_DUPLICATE' => 'Možnost kopírovat obchody',
    'MSG_DUPLICATE' => 'Vytvořením tohoto obchodu můžete potencionálně vytvořit duplicitní obchod. Můžete buď vybrat obchod ze seznamu nebo můžete kliknout na Přidat nový obchod pro vytvoření nového obchodu s předchozími údaji.',
    'LBL_NEW_FORM_TITLE' => 'Vytvořit obchod',
    'LNK_NEW_OPPORTUNITY' => 'Vytvořit obchod',
    'LNK_CREATE' => 'Vytvořit obchod',
    'LNK_OPPORTUNITY_LIST' => 'Obchody',
    'ERR_DELETE_RECORD' => 'Pro smazání obchodu musí být zadáno číslo záznamu.',
    'LBL_TOP_OPPORTUNITIES' => 'Rozpracované obchody',
    'NTC_REMOVE_OPP_CONFIRMATION' => 'Opravdu chcete smazat tento kontakt z tohoto obchodu?',
    'OPPORTUNITY_REMOVE_PROJECT_CONFIRM' => 'Opravdu chcete smazat tento obchod z projektu?',
    'LBL_DEFAULT_SUBPANEL_TITLE' => 'Obchody',
    'LBL_ACTIVITIES_SUBPANEL_TITLE' => 'Aktivity',
    'LBL_HISTORY_SUBPANEL_TITLE' => 'Historie',
    'LBL_RAW_AMOUNT' => 'hrubá částka',
    'LBL_LEADS_SUBPANEL_TITLE' => 'Zájemci',
    'LBL_CONTACTS_SUBPANEL_TITLE' => 'Kontakty',
    'LBL_DOCUMENTS_SUBPANEL_TITLE' => 'Dokumenty',
    'LBL_PROJECTS_SUBPANEL_TITLE' => 'Projekty',
    'LBL_ASSIGNED_TO_NAME' => 'Přiřazeno (komu):',
    'LBL_LIST_ASSIGNED_TO_NAME' => 'Zodpovědný uživatel',
    'LBL_LIST_SALES_STAGE' => 'Fáze prodeje:',
    'LBL_MY_CLOSED_OPPORTUNITIES' => 'Uzavřené obchody',
    'LBL_TOTAL_OPPORTUNITIES' => 'Celkem obchodů',
    'LBL_CLOSED_WON_OPPORTUNITIES' => 'Uzavřené vyhrané obchody',
    'LBL_ASSIGNED_TO_ID' => 'Přiřazeno:',
    'LBL_CREATED_ID' => 'Vytvořeno podle ID',
    'LBL_MODIFIED_ID' => 'Změněno podle ID',
    'LBL_MODIFIED_NAME' => 'Změněno podle už. jména',
    'LBL_CREATED_USER' => 'Vytvořil',
    'LBL_MODIFIED_USER' => 'Upravil',
    'LBL_CAMPAIGN_OPPORTUNITY' => 'Kampaně',
    'LBL_PROJECT_SUBPANEL_TITLE' => 'Projekty',
    'LABEL_PANEL_ASSIGNMENT' => 'Přidělování',
    'LNK_IMPORT_OPPORTUNITIES' => 'Importovat obchody',
    'LBL_EDITLAYOUT' => 'Úprava rozvržení' /*for 508 compliance fix*/,
    //For export labels
    'LBL_EXPORT_CAMPAIGN_ID' => 'ID kampaně',
    'LBL_OPPORTUNITY_TYPE' => 'Typ obchodu',
    'LBL_EXPORT_ASSIGNED_USER_NAME' => 'Přiřazený uživatel',
    'LBL_EXPORT_ASSIGNED_USER_ID' => 'ID přiřazeného uživatele',
    'LBL_EXPORT_MODIFIED_USER_ID' => 'ID modifikátora',
    'LBL_EXPORT_CREATED_BY' => 'Vytvořeno od ID:',
    'LBL_EXPORT_NAME' => 'Název',
    // SNIP
    'LBL_CONTACT_HISTORY_SUBPANEL_TITLE' => 'Přiřazené kontaktní email. adresy',
    'LBL_FILENAME' => 'Příloha',
    'LBL_PRIMARY_QUOTE_ID' => 'Primární nabídka',
    'LBL_CONTRACTS' => 'Smlouvy',
    'LBL_CONTRACTS_SUBPANEL_TITLE' => 'Smlouvy',
    'LBL_PRODUCTS' => 'Položky nabídky',
    'LBL_RLI' => 'Řádky obchodu',
    'LNK_OPPORTUNITY_REPORTS' => 'Zobrazit reporty obchodů',
    'LBL_QUOTES_SUBPANEL_TITLE' => 'Nabídky',
    'LBL_TEAM_ID' => 'ID týmu',
    'LBL_TIMEPERIODS' => 'Časové periody',
    'LBL_TIMEPERIOD_ID' => 'ID časového období',
    'LBL_COMMITTED' => 'Schválený',
    'LBL_FORECAST' => 'Zahrnout do předpovědi',
    'LBL_COMMIT_STAGE' => 'Fáze schválení',
    'LBL_COMMIT_STAGE_FORECAST' => 'Fáze předpovědi',
    'LBL_WORKSHEET' => 'List',
    'LBL_PURCHASED_LINE_ITEMS' => 'Zakoupené řádkové položky',

    // KPI Metrics
    'LBL_ORGANIZE' => 'Uspořádat',
    'LBL_CREATE_NEW' => 'Vytvořit nový',
    'LBL_MANAGE' => 'Spravovat',
    'LBL_SEE_DETAILS' => 'Zobrazit detaily',
    'LBL_HIDE_NEW' => 'Skrýt',

    'LBL_FORECASTED_LIKELY' => 'Prognózováno jako pravděpodobné',
    'LBL_LOST' => 'Ztracené',
    'LBL_RENEWAL' => 'Obnovení',
    'LBL_RENEWAL_OPPORTUNITIES' => 'Příležitosti obnovení',
    'LBL_RENEWAL_PARENT' => 'Nadřazená příležitost',
    'LBL_PARENT_RENEWAL_OPPORTUNITY_ID' => 'ID nadřazeného obnovení',
    'LBL_MONTH_YEAR_RENEWAL' => '{{month}}, {{year}}',

    'LBL_WIDGET_SALES_STAGE' => 'Fáze prodeje',
    'LBL_WIDGET_DATE_CLOSED' => 'Předpokládané datum uzavření',
    'LBL_WIDGET_AMOUNT' => 'Částka',

    'TPL_RLI_CREATE' => 'Příležitost musí mít asociovaný řádek tržby. <a href="javascript:void(0);" id="createRLI">Vytvořit řádek tržby</a>.',
    'TPL_RLI_CREATE_LINK_TEXT' => 'Vytvořit položku',
    'LBL_PRODUCTS_SUBPANEL_TITLE' => 'Položky nabídky',
    'LBL_RLI_SUBPANEL_TITLE' => 'Řádky tržby',

    'LBL_TOTAL_RLIS' => '# celkových řádků',
    'LBL_CLOSED_RLIS' => '# uzavřených řádků',
    'LBL_CLOSED_WON_RLIS' => '# řádkových položek výnosů uzavřených úspěšně',
    'LBL_SERVICE_OPEN_FLEX_DURATION_RLIS' => '# řádkových položek výnosů z doby trvání Open Service Flex',
    'NOTICE_NO_DELETE_CLOSED_RLIS' => 'Nemůžete smazat příležitosti, které obsahují uzavřené řádky',
    'WARNING_NO_DELETE_CLOSED_SELECTED' => 'Jeden nebo více vybraných záznamů obsahuje uzavřené řádky a nemůže být smazán.',
    'LBL_INCLUDED_RLIS' => '# zahrnutých položek Příjmy',
    'LBL_UPDATE_OPPORTUNITIES_RLIS' => 'Aktualizace otevření',
    'LBL_CASCADE_RLI_EDIT' => 'Aktualizujte řádkové položky Otevřené výnosy',
    'LBL_CASCADE_RLI_CREATE' => 'Nastavit řádkové položky Výnosy napříč',
    'LBL_SERVICE_START_DATE_INVALID' => 'Datum zahájení služby nelze nastavit za datum ukončení služby u žádných otevřených doplňkových řádkových položek výnosů.',

    'LBL_QUOTE_SUBPANEL_TITLE' => 'Nabídky',
    'LBL_FILTER_OPPORTUNITY_TEMPLATE' => 'Příležitosti podle dynamického účtu',
    'LBL_TOP_10_OPP' => '10 nejlepších otevřených příležitostí',
    'LBL_DASHLET_MY_ACTIVE_OPP' => 'Dashlet: Moje aktivní příležitosti',
    'LBL_MY_ACTIVE_OPP' => 'Moje aktivní příležitosti',


    // Config
    'LBL_OPPS_CONFIG_VIEW_BY_LABEL' => 'Hierarchie obchodní příležitosti',
    'LBL_OPPS_CONFIG_VIEW_BY_DATE_ROLLUP' => 'Hodnoty kalkulované z obchodní položky do obchodní příležitosti',

    //Dashlet
    'LBL_PIPELINE_TOTAL_IS' => 'Sumář zásobníku je',

    'LBL_OPPORTUNITY_ROLE' => 'Role příležitosti',
    'LBL_NOTES_SUBPANEL_TITLE' => 'Poznámky',
    'LBL_TAB_OPPORTUNITY' => 'Kontrola modulu {{module}}',

    // Help Text
    'LBL_OPPS_CONFIG_ALERT' => 'Pokud zmáčknete potvrdit, vymažete veškerá data obchodních předpovědi a změníte celkový přehled o obchodních příležitostech. Jestliže toto nezamýšlíte, klikněte na tlačítko zrušit a vrátíte se k předchozímu nastavení.',
    'LBL_OPPS_CONFIG_ALERT_TO_OPPS' =>
        'Kliknutím na tlačítko Potvrdit smažete VŠECHNA data prognóz a změníte své Zobrazení obchodních příležitostí. '
        . 'Také budou zakázány VŠECHNY definice procesů s modulem cílů u položek tržeb. '
        . 'Pokud to není to, co jste chtěli, klikněte na tlačítko Zrušit a vrátíte se k předchozímu nastavení.',
    'LBL_OPPS_CONFIG_SALES_STAGE_1a' => 'Pokud všechny položky obchodní předpovědi jsou uzavřené a alespoň jedna je vyhraná,',
    'LBL_OPPS_CONFIG_SALES_STAGE_1b' => 'fáze obchodní předpovědi je určena jako "vyhraná"',
    'LBL_OPPS_CONFIG_SALES_STAGE_2a' => 'Pokud všechny položky obchodní příležitosti jsou označeny jako "prohráno",',
    'LBL_OPPS_CONFIG_SALES_STAGE_2b' => 'fáze obchodní příležitosti je označena jako "prohráno"',
    'LBL_OPPS_CONFIG_SALES_STAGE_3a' => 'Jestliže jakákoliv položky obchodní příležitosti je stále otevřená,',
    'LBL_OPPS_CONFIG_SALES_STAGE_3b' => 'obchodní příležitost bude označena tou nejnižší fází, ve které se nachází.',

// BEGIN ENT/ULT

    // Opps Config - View By Opportunities
    'LBL_HELP_CONFIG_OPPS' => 'Po provedení této změny bude vytvořena poznámka sumarizace řádku tržby v pozadí. Jakmile jsou poznámky kompletní a dostupné, je odesláno upozornění na email ve vašem profilu. Pokud je vaše instance nastavena na {{forecasts_module}}, Sugar vám pošle také upozornění, jakmile jsou záznamy {{module_name}} synchronizovány do modulu {{forecasts_module}} a dostupné pro nový {{forecasts_module}}. Aby vaše instance odesílala emaily, musí být správně nastavena v Správce > Nastavení emailu.',

    // Opps Config - View By Opportunities And RLIs
    'LBL_HELP_CONFIG_RLIS' => 'Po provedení této změny bude vytvořen řádek tržby pro každý {{module_name}} v pozadí. Jakmile je řádek tržby kompletní a dostupný, je odesláno upozornění na email ve vašem profilu. Aby vaše instance odesílala emaily, musí být email správně nastaven v Správce > Nastavení emailu.',
    // List View Help Text
    'LBL_HELP_RECORDS' => 'Modul {{plural_module_name}} umožňuje sledovat jednotlivé prodeje od začátku do konce. Každý záznam {{module_name}} představuje potenciální prodej a zahrnuje relevantní data o prodeji a také data týkající se dalších důležitých záznamů, jako je {{quotes_module}}, {{contacts_module}} atd. Modul {{module_name}} obvykle projde několika fázemi prodeje, dokud nebude označen jako „Uzavřeno úspěšně“ nebo „Uzavřeno neúspěšně“. Modul {{plural_module_name}} lze dále využít pomocí modulu Sugar {{forecasts_singular_module}}ing k pochopení a předvídání prodejních trendů a také k cílení práce v zájmu splnění kvót prodeje.',

    // Record View Help Text
    'LBL_HELP_RECORD' => 'Modul {{plural_module_name}} vám umožňuje sledovat jednotlivé prodeje a řádkové položky patřící k těmto prodejům od začátku do konce. Každý záznam {{module_name}} představuje potenciální prodej a obsahuje relevantní údaje o prodeji, stejně jako údaje vztahující se k jiným důležitým záznamům, jako je {{quotes_module}}, {{contacts_module}} atd. 

- Upravte pole záznamu kliknutím na jednotlivá pole nebo na tlačítko Upravit. 
- Zobrazte nebo upravte odkazy na ostatní záznamy v dílčích panelech přepnutím spodního levého panelu na „Zobrazení dat“. 
- Vytvořte a prohlédněte si uživatelské komentáře a historii změn záznamu v {{activitystream_singular_module}} přepnutím spodního levého panelu na „Tok aktivit“. 
- Označte záznam jako Sledovaný nebo Oblíbený pomocí ikon vpravo od názvu záznamu. 
- Další akce jsou dostupné v rozevírací nabídce Akce vpravo vedle tlačítka Upravit.',

    // Create View Help Text
    'LBL_HELP_CREATE' => 'Modul {{plural_module_name}} vám umožňuje sledovat jednotlivé prodeje a řádkové položky patřící k těmto prodejům od začátku do konce. Každý záznam {{module_name}} představuje potenciální prodej a obsahuje relevantní údaje o prodeji, stejně jako údaje vztahující se k jiným důležitým záznamům, jako je {{quotes_module}}, {{contacts_module}} atd. 

Chcete-li vytvořit modul {{module_name}}: 
1. Vyplňte hodnoty polí dle potřeby. 
 - Pole označená jako „Povinné“ musí být vyplněna před uložením. 
 - V případě potřeby klikněte na položku „Zobrazit více“ pro zobrazení dalších polí. 
2. Kliknutím na tlačítko „Uložit“ dokončete nový záznam a vraťte se na předchozí stránku.',

// END ENT/ULT

    //Marketo
    'LBL_MKTO_SYNC' => 'Synchronizovat do Marketo®',
    'LBL_MKTO_ID' => 'Marketo ID leada',

    'LBL_DASHLET_TOP10_SALES_OPPORTUNITIES_NAME' => 'Nejlepších 10 obchodních příležitostí',
    'LBL_TOP10_OPPORTUNITIES_CHART_DESC' => 'Zobrazuje nejlepších deset příležitostí v bublinkovém grafu',
    'LBL_TOP10_OPPORTUNITIES_MY_OPP' => 'Moje příležitosti',
    'LBL_TOP10_OPPORTUNITIES_MY_TEAMS_OPP' => "Příležitosti mého týmu",

    'LBL_PIPELINE_ERR_CLOSED_SALES_STAGE' => '{{fieldName}} nelze změnit, protože tento {{moduleSingular}} nemá žádné otevřené řádkové položky.',
    'TPL_ACTIVITY_TIMELINE_DASHLET' => 'Časová osa příležitosti',

    'LBL_CASCADE_SERVICE_WARNING' => ' nelze nastavit napříč žádnou z těchto řádkových položek výnosů, protože se nejedná o služby. Chcete pokračovat ve vytvoření?',
    'LBL_CASCADE_DURATION_WARNING' => ' nelze nastavit napříč žádnou z těchto řádkových položek výnosů, protože jejich trvání je uzamčeno. Chcete pokračovat ve vytvoření?',

    // AI Predict
    'LBL_AI_OPPORTUNITY_CLOSE_PREDICTION_NAME' => 'Predikce uzavření příležitosti',
    'LBL_AI_OPPORTUNITY_CLOSE_PREDICTION_DESC' => 'Zobrazit podrobnosti predikce pro konkrétní příležitost',
    'LBL_AI_WINRATE' => 'Míra úspěšnosti',
    'LBL_AI_WONOPP' => 'Získané příležitosti',
    'LBL_AI_CLOSINGTIME' => 'Čas ukončení',
    'LBL_AI_CLOSEDOPP' => 'Uzavřené příležitosti',
    'LBL_AI_LEADTIMESPAN' => 'Čas mezi Vytvořením příležitosti a Uzavřeno úspěšně',
];
