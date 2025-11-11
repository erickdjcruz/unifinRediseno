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
    'LBL_OPPORTUNITIES_LIST_DASHBOARD' => 'Paneli i listës së shanseve',
    'LBL_OPPORTUNITIES_RECORD_DASHBOARD' => 'Paneli i regjistrimeve të shanseve',
    'LBL_OPPORTUNITIES_MULTI_LINE_DASHBOARD' => 'Përqendruesi i fokusit te mundësitë - Paneli',
    'LBL_OPPORTUNITIES_FOCUS_DRAWER_DASHBOARD' => 'Përqendruesi i fokusit te mundësitë',
    'LBL_RENEWAL_OPPORTUNITY' => 'Mundësia e rinovimit',

    'LBL_MODULE_NAME' => 'Mundësitë',
    'LBL_MODULE_NAME_SINGULAR' => 'Mundësi:',
    'LBL_MODULE_TITLE' => 'Mundësite: Ballina',
    'LBL_SEARCH_FORM_TITLE' => 'Kerkim te Mundësive',
    'LBL_VIEW_FORM_TITLE' => 'Shikimi i mundësive',
    'LBL_LIST_FORM_TITLE' => 'Lista e mundësive',
    'LBL_OPPORTUNITY_NAME' => 'Emri i Mundësisë:',
    'LBL_OPPORTUNITY' => 'Mundësi:',
    'LBL_NAME' => 'Emri i Mundësisë:',
    'LBL_TIME' => 'Ora',
    'LBL_INVITEE' => 'Kontaktet',
    'LBL_CURRENCIES' => 'Monedhat',
    'LBL_LIST_OPPORTUNITY_NAME' => 'Emri',
    'LBL_LIST_ACCOUNT_NAME' => 'Emri i llogarisë:',
    'LBL_LIST_DATE_CLOSED' => 'Mbyll',
    'LBL_LIST_AMOUNT' => 'I përshtatshëm',
    'LBL_LIST_AMOUNT_USDOLLAR' => 'Vlerë',
    'LBL_ACCOUNT_ID' => 'ID e Llogarisë',
    'LBL_CURRENCY_RATE' => 'Kursi i valutës',
    'LBL_CURRENCY_ID' => 'ID e Monedhës',
    'LBL_CURRENCY_NAME' => 'Emri i Monedhës',
    'LBL_CURRENCY_SYMBOL' => 'Sym',
//DON'T CONVERT THESE THEY ARE MAPPINGS
    'db_sales_stage' => 'LBL_LIST_SALES_STAGE',
    'db_name' => 'LBL_NAME',
    'db_amount' => 'LBL_LIST_AMOUNT',
    'db_date_closed' => 'LBL_LIST_DATE_CLOSED',
//END DON'T CONVERT
    'UPDATE' => 'Shtijet- rinovim i monedhës',
    'UPDATE_DOLLARAMOUNTS' => 'Rinovim i vlerava të dollarëve amerikan',
    'UPDATE_VERIFY' => 'Verifikim i sasive',
    'UPDATE_VERIFY_TXT' => 'Verifikon që sasitë e vlerave të shitjes janë numra decimal valid me karaktera (0-9) dhe decimalë (.)',
    'UPDATE_FIX' => 'Sasitë fikse',
    'UPDATE_FIX_TXT' => 'Përpjekje për ndreqje të çdo sasie jovalide duke krijuar decimal valid nga vlera aktuale. Çdo sasi e modifikuar është e ruajtur në kopje rezervë në bazën e të dhënave të fushës.Në qoftë se e drejton këtë dhe vëren gabime, mos e ridrejto në qoftë se nuk i ke rikthyer nga kopja rezervë pasi që mund të rishkruaj të dhëna të reja jovalide.',
    'UPDATE_DOLLARAMOUNTS_TXT' => 'Azhurnoni shumat e dollarit amerikan për shitje të bazuara në normat aktuale të monedhës së caktuar. Kjo vlerë është përdorur për të llogaritur grafikët dhe shih shumat e listës së monedhës.',
    'UPDATE_CREATE_CURRENCY' => 'Krijimi i monedhës së re:',
    'UPDATE_VERIFY_FAIL' => 'Verifikim i dështuar i regjistrimit:',
    'UPDATE_VERIFY_CURAMOUNT' => 'Sasia aktuale:',
    'UPDATE_VERIFY_FIX' => 'Drejtimi i ndreqjes do të jepte',
    'UPDATE_INCLUDE_CLOSE' => 'Përfshinë regjistrimet e mbyllura',
    'UPDATE_VERIFY_NEWAMOUNT' => 'Sasia  e re:',
    'UPDATE_VERIFY_NEWCURRENCY' => 'Monedha e re:',
    'UPDATE_DONE' => 'E bërë',
    'UPDATE_BUG_COUNT' => 'Janë gjetur gabime dhe munduar të përmisohen:',
    'UPDATE_BUGFOUND_COUNT' => 'Janë gjetur gabinme',
    'UPDATE_COUNT' => 'Rifreskim i regjistrimeve',
    'UPDATE_RESTORE_COUNT' => 'Sasitë e rikrijuara të regjistrimeve:',
    'UPDATE_RESTORE' => 'Rivendos sasitë',
    'UPDATE_RESTORE_TXT' => 'Vlerat e rikrijuara të sasive nga ruajtjet e krijuara të një rezerve gjatë ndreqjes.',
    'UPDATE_FAIL' => 'Nuk mund të rifreskoj',
    'UPDATE_NULL_VALUE' => 'Vlera është Zero vendose në 0-',
    'UPDATE_MERGE' => 'Bashko monedhat',
    'UPDATE_MERGE_TXT' => 'Bashko monedhat e shumfishta në monedhë të vetme. Në qoftë se ka regjistrime të monedhave të shumfishte, bashkoi së bashku. Kjo gjithashtu do të bashkoj monedhat e modulave të tjera.',
    'LBL_ACCOUNT_NAME' => 'Emri i llogarisë:',
    'LBL_CURRENCY' => 'monedha:',
    'LBL_DATE_CLOSED' => 'Data e përfundimit e pritur',
    'LBL_DATE_CLOSED_TIMESTAMP' => 'Mbyllja e pritet për Timestamp',
    'LBL_TYPE' => 'Lloji:',
    'LBL_CAMPAIGN' => 'Fushata:',
    'LBL_NEXT_STEP' => 'Hapi vijues',
    'LBL_SERVICE_START_DATE' => 'Data e fillimit të shërbimit',
    'LBL_LEAD_SOURCE' => 'Burimi i udhëheqjes',
    'LBL_SALES_STAGE' => 'Faza e shitjes',
    'LBL_SALES_STATUS' => 'Statusi',
    'LBL_PROBABILITY' => 'Probabiliteti (%)',
    'LBL_DESCRIPTION' => 'Përshkrim',
    'LBL_DUPLICATE' => 'Mundësi të mundshme të dubluara',
    'MSG_DUPLICATE' => 'Regjistrimi i mundësive që kryeni mund të jetë dublim i mundësive të regjistruar që tashmë ekziston. Mundësitë e regjistruara që përmbajnë emra të ngjashëm janë të rënditura posht. Shtyp Ruaj për të vazhduar me krijimin e mundësisë së re, ose shtyp Anulim pr tu kthyer në modulë duke mos krijuar mundësi.',
    'LBL_NEW_FORM_TITLE' => 'Krijo mundësi',
    'LNK_NEW_OPPORTUNITY' => 'Krijo mundësi',
    'LNK_CREATE' => 'Krijo marrëveshje',
    'LNK_OPPORTUNITY_LIST' => 'Shiko mundësitë',
    'ERR_DELETE_RECORD' => 'Duhet përcaktuar numrin e regjistrimit për të fshirë mundësinë',
    'LBL_TOP_OPPORTUNITIES' => 'Mundësitë e mia top të hapura',
    'NTC_REMOVE_OPP_CONFIRMATION' => 'A jeni të sigurt që dëshironi të largoni këtë kontakt nga mundësia?',
    'OPPORTUNITY_REMOVE_PROJECT_CONFIRM' => 'A jeni të sigurt që dëshironi të largoni këtë mundësi nga projekti?',
    'LBL_DEFAULT_SUBPANEL_TITLE' => 'Mundësitë',
    'LBL_ACTIVITIES_SUBPANEL_TITLE' => 'Aktivitetet',
    'LBL_HISTORY_SUBPANEL_TITLE' => 'Historia',
    'LBL_RAW_AMOUNT' => 'shuma të papërpunuara',
    'LBL_LEADS_SUBPANEL_TITLE' => 'udhëheqjet',
    'LBL_CONTACTS_SUBPANEL_TITLE' => 'Kontaktet',
    'LBL_DOCUMENTS_SUBPANEL_TITLE' => 'Dokumentacionet',
    'LBL_PROJECTS_SUBPANEL_TITLE' => 'Projektet',
    'LBL_ASSIGNED_TO_NAME' => 'Drejtuar:',
    'LBL_LIST_ASSIGNED_TO_NAME' => 'Përdorues i caktuar',
    'LBL_LIST_SALES_STAGE' => 'Faza e shitjes',
    'LBL_MY_CLOSED_OPPORTUNITIES' => 'Mundësitë e mia të mbyllura',
    'LBL_TOTAL_OPPORTUNITIES' => 'Mundësitë totale',
    'LBL_CLOSED_WON_OPPORTUNITIES' => 'Mundësitë e fituara të mbyllura',
    'LBL_ASSIGNED_TO_ID' => 'Përdorues i caktuar:',
    'LBL_CREATED_ID' => 'Krijuar nga Id',
    'LBL_MODIFIED_ID' => 'Modifikuar nga Id',
    'LBL_MODIFIED_NAME' => 'Modifikuar nga emri i përdoruesit',
    'LBL_CREATED_USER' => 'Përdorues i krijuar',
    'LBL_MODIFIED_USER' => 'përdorues i modifikuar',
    'LBL_CAMPAIGN_OPPORTUNITY' => 'fushatat',
    'LBL_PROJECT_SUBPANEL_TITLE' => 'Projektet',
    'LABEL_PANEL_ASSIGNMENT' => 'Detyrë',
    'LNK_IMPORT_OPPORTUNITIES' => 'Importo mundësitë',
    'LBL_EDITLAYOUT' => 'Ndrysho formatin' /*for 508 compliance fix*/,
    //For export labels
    'LBL_EXPORT_CAMPAIGN_ID' => 'ID e fushatës',
    'LBL_OPPORTUNITY_TYPE' => 'Lloji i mundësisë',
    'LBL_EXPORT_ASSIGNED_USER_NAME' => 'Emri i përdoruesit të caktuar',
    'LBL_EXPORT_ASSIGNED_USER_ID' => 'ID e përdoruesit të caktuar',
    'LBL_EXPORT_MODIFIED_USER_ID' => 'Modifikuar nga ID',
    'LBL_EXPORT_CREATED_BY' => 'Krijuar Nga ID',
    'LBL_EXPORT_NAME' => 'Emri',
    // SNIP
    'LBL_CONTACT_HISTORY_SUBPANEL_TITLE' => 'Emailet e kontakteve të lidhura',
    'LBL_FILENAME' => 'Bashkëngjitje',
    'LBL_PRIMARY_QUOTE_ID' => 'Oferta primare',
    'LBL_CONTRACTS' => 'Kontratat',
    'LBL_CONTRACTS_SUBPANEL_TITLE' => 'Kontratat',
    'LBL_PRODUCTS' => 'Produktet',
    'LBL_RLI' => 'Rreshti i llojeve të të ardhurave',
    'LNK_OPPORTUNITY_REPORTS' => 'Shiko raportet e mundësive',
    'LBL_QUOTES_SUBPANEL_TITLE' => 'Kuotat',
    'LBL_TEAM_ID' => 'Id e grupit',
    'LBL_TIMEPERIODS' => 'Periudhat kohore',
    'LBL_TIMEPERIOD_ID' => 'ID ë Periudhat kohore',
    'LBL_COMMITTED' => 'Drejtuar',
    'LBL_FORECAST' => 'Parashikim',
    'LBL_COMMIT_STAGE' => 'Faza e perkushtimit',
    'LBL_COMMIT_STAGE_FORECAST' => 'Faza e parashikimit',
    'LBL_WORKSHEET' => 'Fletë pune',
    'LBL_PURCHASED_LINE_ITEMS' => 'Artikujt e blerë të rreshtit',

    // KPI Metrics
    'LBL_ORGANIZE' => 'Organizo',
    'LBL_CREATE_NEW' => 'Krijo të re',
    'LBL_MANAGE' => 'Menaxho',
    'LBL_SEE_DETAILS' => 'Shiko detajet',
    'LBL_HIDE_NEW' => 'Fshih',

    'LBL_FORECASTED_LIKELY' => 'Parashikuar si të mundshme',
    'LBL_LOST' => 'Humbur',
    'LBL_RENEWAL' => 'Rinovimi',
    'LBL_RENEWAL_OPPORTUNITIES' => 'Mundësitë e rinovimit',
    'LBL_RENEWAL_PARENT' => 'Mundësia kryesore',
    'LBL_PARENT_RENEWAL_OPPORTUNITY_ID' => 'ID-ja kryesore e rinovimit',
    'LBL_MONTH_YEAR_RENEWAL' => '{{month}}, {{year}}',

    'LBL_WIDGET_SALES_STAGE' => 'Faza e shitjeve',
    'LBL_WIDGET_DATE_CLOSED' => 'Data e parashikuar e përfundimit',
    'LBL_WIDGET_AMOUNT' => 'Shuma',

    'TPL_RLI_CREATE' => 'Një mundësi patjetër duhet të ketë të shoqëruar rreshtin e llojit të të ardhurave',
    'TPL_RLI_CREATE_LINK_TEXT' => 'Krijo rresht të të ardhurave',
    'LBL_PRODUCTS_SUBPANEL_TITLE' => 'Artikujt e rreshtave të kuotuar',
    'LBL_RLI_SUBPANEL_TITLE' => 'Artikujt e rreshtave të të ardhurave',

    'LBL_TOTAL_RLIS' => '# të artikujve total të rreshtave të të ardhurave',
    'LBL_CLOSED_RLIS' => '# të artikujve të mbyllur të rreshtave të të ardhurave',
    'LBL_CLOSED_WON_RLIS' => 'Nr. i elementeve të linjës me të ardhura të mbyllura',
    'LBL_SERVICE_OPEN_FLEX_DURATION_RLIS' => 'Nr. i artikujve të rreshtit të të ardhurave me kohëzgjatje fleksible të shërbimit të hapur',
    'NOTICE_NO_DELETE_CLOSED_RLIS' => 'Ju nuk mund të fshini mundësitë të cilat përmbajnë artikuj të mbyllur të rreshtave të të ardhurave',
    'WARNING_NO_DELETE_CLOSED_SELECTED' => 'Një ose më shumë nga të dhënat e selektuara përmbajnë artikuj të mbyllur të rreshtave të të ardhurave dhe nuk mund të fshihen',
    'LBL_INCLUDED_RLIS' => '# i artikujve të linjës së të ardhurave të përfshira',
    'LBL_UPDATE_OPPORTUNITIES_RLIS' => 'Përditësim i hapur',
    'LBL_CASCADE_RLI_EDIT' => 'Përditëso artikujt në rresht të të ardhurave të hapura',
    'LBL_CASCADE_RLI_CREATE' => 'Cakto artikujt në rresht përtej të ardhurave',
    'LBL_SERVICE_START_DATE_INVALID' => 'Data e fillimit të shërbimit nuk mund të caktohet pas datës së mbarimit të shërbimit për çdo artikull të hapur shtesë të linjës së të ardhurave.',

    'LBL_QUOTE_SUBPANEL_TITLE' => 'Kuotat',
    'LBL_FILTER_OPPORTUNITY_TEMPLATE' => 'Mundësitë nga një llogari dinamike',
    'LBL_TOP_10_OPP' => '10 mundësitë e para të hapura',
    'LBL_DASHLET_MY_ACTIVE_OPP' => 'Nënvizimi: Mundësitë e mia aktive',
    'LBL_MY_ACTIVE_OPP' => 'Mundësitë e mia aktive',


    // Config
    'LBL_OPPS_CONFIG_VIEW_BY_LABEL' => 'Hierarkia e mundësisë',
    'LBL_OPPS_CONFIG_VIEW_BY_DATE_ROLLUP' => 'Vlerat e kalkuluara nga Rrjeshti i të të Ardhurave nga Mundësitë',

    //Dashlet
    'LBL_PIPELINE_TOTAL_IS' => 'Totali i gazsjellësit është',

    'LBL_OPPORTUNITY_ROLE' => 'Roli i mundësisë',
    'LBL_NOTES_SUBPANEL_TITLE' => 'Shënime',
    'LBL_TAB_OPPORTUNITY' => 'Rishiko {{module}}',

    // Help Text
    'LBL_OPPS_CONFIG_ALERT' => 'Duke klikuar Konfirmo, ju do të fshini të gjitha të dhënat e parashikimeve dhe do të ndërroni shikimin e mundësive. Nëse nuk e keni këtë për qëllim, klikoni anulo për të kthyer konfigurimet e mëparshme.',
    'LBL_OPPS_CONFIG_ALERT_TO_OPPS' =>
        'Duke klikuar "Konfirmo", do të fshish TË GJITHA të dhënat e parashikimeve dhe do të ndryshosh shikimin e mundësive. '
        . 'Do të çaktivizohen edhe TË GJITHA përcaktimet e procesit me një modul të synuar për rreshtin e të ardhurave. '
        . 'Nëse nuk ke këtë për qëllim, kliko anulo për t&#39;u kthyer në cilësimet e mëparshme.',
    'LBL_OPPS_CONFIG_SALES_STAGE_1a' => 'Nëse të gjitha rrjeshtat e të të ardhurave janë të mbyllura dhe të paktën një ka fituar,',
    'LBL_OPPS_CONFIG_SALES_STAGE_1b' => 'faza e shitjeve të mundësisë është caktuar si "E mbyllur si e fituar".',
    'LBL_OPPS_CONFIG_SALES_STAGE_2a' => 'Nëse të gjitha rreshtat e të të ardhurave janë fazën e shitjes "Të mbyllura si të humbura",',
    'LBL_OPPS_CONFIG_SALES_STAGE_2b' => 'faza e shitjeve të mundësisë është caktuar si "E mbyllur si e humbur".',
    'LBL_OPPS_CONFIG_SALES_STAGE_3a' => 'Nëse rrjeshtat e të të ardhurave janë ende të hapura,',
    'LBL_OPPS_CONFIG_SALES_STAGE_3b' => 'Mundësia do të shënohet si fazë e shitjes më pak e avancuar.',

// BEGIN ENT/ULT

    // Opps Config - View By Opportunities
    'LBL_HELP_CONFIG_OPPS' => 'Pas fillimit të ndryshimit, shënimet e përmbledhjes së artikullit të linjës të së ardhurave do të krijohen në sfond. Kur shënimet të përfundojnë dhe të jenë të disponueshme, tek adresa e emailit në profilin e përdoruesit do të dërgohet një njoftim. Nëse shembulli yt konfigurohet për {{forecasts_module}}, Sugar do të dërgojë gjithashtu një njoftim kur regjistrimet e {{module_name}} sinkronizohen me modulin {{forecasts_module}} dhe disponohen për modulin e ri {{forecasts_module}}. Ki parasysh se shembulli yt duhet të konfigurohet për të dërguar email nëpërmjet "Admin > Cilësimet e emailit" në mënyrë që të dërgohen njoftimet.',

    // Opps Config - View By Opportunities And RLIs
    'LBL_HELP_CONFIG_RLIS' => 'Pas fillimit të ndryshimit, raportet e artikujve të linjës të së ardhurave do të krijohen për çdo modul {{module_name}} ekzistues në sfond. Kur artikujt e linjës të së ardhurave të plotësohen dhe të disponohen, në adresën e emailit në profilin e përdoruesit do të dërgohet një njoftim. Ki parasysh se shembulli yt duhet të konfigurohet për të dërguar email nëpërmjet "Admin > Cilësimet e emailit" në mënyrë që të dërgohet njoftimi.',
    // List View Help Text
    'LBL_HELP_RECORDS' => 'Moduli {{plural_module_name}} të lejon të gjurmosh shitjet individuale nga fillimi në fund. Çdo regjistër {{module_name}} përfaqëson një shitje të mundshme dhe përfshin të dhënat përkatëse të shitjes, si dhe të dhëna që kanë të bëjnë me regjistra të tjerë të rëndësishëm, të tillë si {{quotes_module}}, {{contacts_module}} etj. Zakonisht, një {{module_name}} kalon në disa faza të shitjes derisa të shënohet si "Mbyllje e suksesshme" ose "Mbyllje e pasuksesshme". {{plural_module_name}} mund të shfrytëzohet edhe më tej duke përdorur modulin {{forecasts_singular_module}} të Sugar për të kuptuar dhe parashikuar tendencat e shitjeve, si dhe për të përqendruar punën në arritjen e kuotave të shitjeve.',

    // Record View Help Text
    'LBL_HELP_RECORD' => 'Moduli {{plural_module_name}} të lejon të gjurmosh shitjet individuale dhe artikujt që u përkasin atyre shitjeve nga fillimi deri në fund. Çdo regjistër {{module_name}} përfaqëson një shitje të mundshme dhe përfshin të dhënat përkatëse të shitjes, si dhe të dhëna që kanë të bëjnë me regjistra të tjerë të rëndësishëm, të tillë si {{quotes_module}}, {{contacts_module}} etj.

- Modifiko fushat e këtij regjistri duke klikuar një fushë individuale ose butonin "Modifiko".
- Shiko ose modifiko lidhjet për te regjistrat e tjerë te nënpanelet duke e ndryshuar panelin e poshtëm majtas në opsionin "Pamja e të dhënave".
- Bëj komente dhe shiko komentet e përdoruesve, si dhe regjistro historikun e ndryshimeve te {{activitystream_singular_module}} duke e ndryshuar panelin e poshtëm majtas në opsionin "Transmetimi i aktivitetit".
- Ndiq ose bëje të preferuar këtë regjistër duke përdorur ikonat në të djathtë të emrit të regjistrit.
- Veprimet shtesë disponohen në menynë me zbritje "Veprimet" në të djathtë të butonit "Modifiko".',

    // Create View Help Text
    'LBL_HELP_CREATE' => 'Moduli {{plural_module_name}} të lejon të gjurmosh shitjet individuale dhe artikujt që u përkasin atyre shitjeve nga fillimi deri në fund. Çdo regjistër {{module_name}} përfaqëson një shitje të mundshme dhe përfshin të dhënat përkatëse të shitjes, si dhe të dhëna që kanë të bëjnë me regjistra të tjerë të rëndësishëm, të tillë si {{quotes_module}}, {{contacts_module}}, etj.

Për të krijuar një {{module_name}}:
1. Jep vlerat për fushat sipas dëshirës.
 - Fushat e shënuara si "Të domosdoshme" duhet të plotësohen përpara ruajtjes.
 - Kliko "Shfaq më shumë" për të shfaqur fusha shtesë nëse është e nevojshme.
2. Kliko "Ruaj" për të finalizuar regjistrin e ri dhe për t&#39;u kthyer në faqen e mëparshme.',

// END ENT/ULT

    //Marketo
    'LBL_MKTO_SYNC' => 'Sinkronizo me Marketo®',
    'LBL_MKTO_ID' => 'Marketo kontakti ID',

    'LBL_DASHLET_TOP10_SALES_OPPORTUNITIES_NAME' => '10 rreshtat më të lartë të të ardhurave',
    'LBL_TOP10_OPPORTUNITIES_CHART_DESC' => 'Shfaq dhjetë rreshtat më të lartë në diagram fluskash',
    'LBL_TOP10_OPPORTUNITIES_MY_OPP' => 'Rrjeshti i të ardhurave të mia',
    'LBL_TOP10_OPPORTUNITIES_MY_TEAMS_OPP' => "Rrjeshti i të ardhurave të ekipit tim",

    'LBL_PIPELINE_ERR_CLOSED_SALES_STAGE' => 'E pamundur të ndryshohet {{fieldName}}, pasi ky {{moduleSingular}} nuk ka artikuj të hapur në linjë.',
    'TPL_ACTIVITY_TIMELINE_DASHLET' => 'Vija kohore e mundësisë',

    'LBL_CASCADE_SERVICE_WARNING' => ' nuk mund të caktohet te asnjë prej artikujve në rresht të të ardhurave sepse ato nuk janë shërbime. Dëshiron të vazhdosh me krijimin?',
    'LBL_CASCADE_DURATION_WARNING' => ' nuk mund të caktohet te asnjë prej artikujve në rresht të të ardhurave sepse kohëzgjatjet e tyre janë të kyçura. Dëshiron të vazhdosh me krijimin?',

    // AI Predict
    'LBL_AI_OPPORTUNITY_CLOSE_PREDICTION_NAME' => 'Parashikimi i mbylljes së mundësisë',
    'LBL_AI_OPPORTUNITY_CLOSE_PREDICTION_DESC' => 'Shfaq detajet e parashikimit për një mundësi specifike',
    'LBL_AI_WINRATE' => 'Norma e fitores',
    'LBL_AI_WONOPP' => 'Mundësitë e fituara',
    'LBL_AI_CLOSINGTIME' => 'Koha e mbylljes',
    'LBL_AI_CLOSEDOPP' => 'Mundësitë e mbyllura',
    'LBL_AI_LEADTIMESPAN' => 'Koha midis krijimit të mundësisë dhe mbylljes së suksesshme',
];
