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
    'LBL_OPPORTUNITIES_LIST_DASHBOARD' => 'Mulighedsliste-dashboard',
    'LBL_OPPORTUNITIES_RECORD_DASHBOARD' => 'Mulighedsoptegnelser-dashboard',
    'LBL_OPPORTUNITIES_MULTI_LINE_DASHBOARD' => 'Muligheder Fokus Dashboard - Konsol',
    'LBL_OPPORTUNITIES_FOCUS_DRAWER_DASHBOARD' => 'Fokusskuffen Muligheder',
    'LBL_RENEWAL_OPPORTUNITY' => 'Fornyelsesmulighed',

    'LBL_MODULE_NAME' => 'Salgsmuligheder',
    'LBL_MODULE_NAME_SINGULAR' => 'Salgsmulighed',
    'LBL_MODULE_TITLE' => 'Salgsmuligheder: Startside',
    'LBL_SEARCH_FORM_TITLE' => 'Søg efter salgsmulighed',
    'LBL_VIEW_FORM_TITLE' => 'Vis salgsmulighed',
    'LBL_LIST_FORM_TITLE' => 'Salgsmulighedsliste',
    'LBL_OPPORTUNITY_NAME' => 'Salgsmulighedsnavn:',
    'LBL_OPPORTUNITY' => 'Salgsmulighed:',
    'LBL_NAME' => 'Salgsmulighedsnavn',
    'LBL_TIME' => 'Klokkeslæt',
    'LBL_INVITEE' => 'Kontakter',
    'LBL_CURRENCIES' => 'Valutaer',
    'LBL_LIST_OPPORTUNITY_NAME' => 'Navn',
    'LBL_LIST_ACCOUNT_NAME' => 'Kontonavn',
    'LBL_LIST_DATE_CLOSED' => 'Luk',
    'LBL_LIST_AMOUNT' => 'Beløb',
    'LBL_LIST_AMOUNT_USDOLLAR' => 'Beløb',
    'LBL_ACCOUNT_ID' => 'Virksomheds-id',
    'LBL_CURRENCY_RATE' => 'Valuta kurs',
    'LBL_CURRENCY_ID' => 'Valuta-id',
    'LBL_CURRENCY_NAME' => 'Valutanavn',
    'LBL_CURRENCY_SYMBOL' => 'Valutasymbol',
//DON'T CONVERT THESE THEY ARE MAPPINGS
    'db_sales_stage' => 'LBL_LIST_SALES_STAGE',
    'db_name' => 'LBL_NAME',
    'db_amount' => 'LBL_LIST_AMOUNT',
    'db_date_closed' => 'LBL_LISTE_DATO_LUKKET',
//END DON'T CONVERT
    'UPDATE' => 'Salgsmulighed - valutaopdatering',
    'UPDATE_DOLLARAMOUNTS' => 'Opdater USD-beløb',
    'UPDATE_VERIFY' => 'Bekræft beløb',
    'UPDATE_VERIFY_TXT' => 'Kontrollerer, at beløbsværdierne i salgsmuligheder er gyldige decimaltal med kun numeriske tegn "0-9" og decimaler "."',
    'UPDATE_FIX' => 'Ret beløb',
    'UPDATE_FIX_TXT' => 'Forsøg på at rette ugyldige beløb ved at oprette en gyldig decimal ud fra det nuværende beløb. Ændrede beløb sikkerhedskopieres i databasefeltet amount_backup. Hvis du kører denne og får fejl, skal du ikke køre den igen uden at gendanne sikkerhedskopien, da det kan overskrive sikkerhedskopien med nye ugyldige data.',
    'UPDATE_DOLLARAMOUNTS_TXT' => 'Opdater USD-beløb for salgsmuligheder baseret på de aktuelle valutakurser. Denne værdi bruges til at beregne diagrammer og vise valutabeløb.',
    'UPDATE_CREATE_CURRENCY' => 'Opretter ny valuta:',
    'UPDATE_VERIFY_FAIL' => 'Registrer mislykket verifikation:',
    'UPDATE_VERIFY_CURAMOUNT' => 'Aktuelt beløb:',
    'UPDATE_VERIFY_FIX' => 'Kørsel af rettelse vil give',
    'UPDATE_INCLUDE_CLOSE' => 'Medtag lukkede poster',
    'UPDATE_VERIFY_NEWAMOUNT' => 'Nyt beløb:',
    'UPDATE_VERIFY_NEWCURRENCY' => 'Ny valuta:',
    'UPDATE_DONE' => 'Udført',
    'UPDATE_BUG_COUNT' => 'Der blev fundet fejl og gjort forsøg på at løse dem:',
    'UPDATE_BUGFOUND_COUNT' => 'Der blev fundet fejl:',
    'UPDATE_COUNT' => 'Poster opdateret:',
    'UPDATE_RESTORE_COUNT' => 'Postbeløb gendannet:',
    'UPDATE_RESTORE' => 'Gendan beløb',
    'UPDATE_RESTORE_TXT' => 'Gendanner beløbsværdier ud fra de sikkerhedskopier, der blev oprettet under rettelsen.',
    'UPDATE_FAIL' => 'Kunne ikke opdatere -',
    'UPDATE_NULL_VALUE' => 'Beløbet er NULL. Angiver det til 0 -',
    'UPDATE_MERGE' => 'Flet valutaer',
    'UPDATE_MERGE_TXT' => 'Fletter flere valutaer til en fælles valuta. Hvis der er flere valutaposter for samme valuta, skal du flette dem. Derved flettes også valutaerne for alle andre moduler.',
    'LBL_ACCOUNT_NAME' => 'Virksomhedsnavn:',
    'LBL_CURRENCY' => 'Valuta:',
    'LBL_DATE_CLOSED' => 'Forventet lukkedato:',
    'LBL_DATE_CLOSED_TIMESTAMP' => 'Forventet Close Date Tidsstempel',
    'LBL_TYPE' => 'Type:',
    'LBL_CAMPAIGN' => 'Kampagne:',
    'LBL_NEXT_STEP' => 'Næste trin:',
    'LBL_SERVICE_START_DATE' => 'Startdato for service',
    'LBL_LEAD_SOURCE' => 'Kilde til kundeemne:',
    'LBL_SALES_STAGE' => 'Salgsfase:',
    'LBL_SALES_STATUS' => 'Status',
    'LBL_PROBABILITY' => 'Sandsynlighed "%":',
    'LBL_DESCRIPTION' => 'Beskrivelse:',
    'LBL_DUPLICATE' => 'Mulig identisk salgsmulighed',
    'MSG_DUPLICATE' => 'Den salgsmulighedspost, du er ved at oprette, kan være en dublet af en salgsmulighedspost, der allerede findes. Salgsmulighedsposter, der indeholder lignende navne, er angivet nedenfor.<br>Klik på Gem for at fortsætte med at oprette denne nye salgsmulighed, eller klik på Annuller for at vende tilbage til modulet uden at oprette salgsmuligheden.',
    'LBL_NEW_FORM_TITLE' => 'Opret salgsmulighed',
    'LNK_NEW_OPPORTUNITY' => 'Opret salgsmulighed',
    'LNK_CREATE' => 'Opret sag',
    'LNK_OPPORTUNITY_LIST' => 'Salgsmuligheder',
    'ERR_DELETE_RECORD' => 'Der skal angives et postnummer for at slette salgsmuligheden.',
    'LBL_TOP_OPPORTUNITIES' => 'Mine bedste åbne salgsmuligheder',
    'NTC_REMOVE_OPP_CONFIRMATION' => 'Er du sikker på, at du vil fjerne denne kontakt fra salgsmuligheden?',
    'OPPORTUNITY_REMOVE_PROJECT_CONFIRM' => 'Er du sikker på, at du vil fjerne denne salgsmulighed fra projektet?',
    'LBL_DEFAULT_SUBPANEL_TITLE' => 'Salgsmuligheder',
    'LBL_ACTIVITIES_SUBPANEL_TITLE' => 'Aktiviteter',
    'LBL_HISTORY_SUBPANEL_TITLE' => 'Historik',
    'LBL_RAW_AMOUNT' => 'Bruttobeløb',
    'LBL_LEADS_SUBPANEL_TITLE' => 'Kundeemner',
    'LBL_CONTACTS_SUBPANEL_TITLE' => 'Kontakter',
    'LBL_DOCUMENTS_SUBPANEL_TITLE' => 'Dokumenter',
    'LBL_PROJECTS_SUBPANEL_TITLE' => 'Projekter',
    'LBL_ASSIGNED_TO_NAME' => 'Tildelt til:',
    'LBL_LIST_ASSIGNED_TO_NAME' => 'Tildelt bruger',
    'LBL_LIST_SALES_STAGE' => 'Salgsfase',
    'LBL_MY_CLOSED_OPPORTUNITIES' => 'Mine lukkede salgsmuligheder',
    'LBL_TOTAL_OPPORTUNITIES' => 'Samlede salgsmuligheder',
    'LBL_CLOSED_WON_OPPORTUNITIES' => 'Lukkede vundne salgsmuligheder',
    'LBL_ASSIGNED_TO_ID' => 'Tildelt til id',
    'LBL_CREATED_ID' => 'Oprettet af id',
    'LBL_MODIFIED_ID' => 'Ændret af id',
    'LBL_MODIFIED_NAME' => 'Ændret af brugernavn',
    'LBL_CREATED_USER' => 'Oprettet bruger',
    'LBL_MODIFIED_USER' => 'Ændret bruger',
    'LBL_CAMPAIGN_OPPORTUNITY' => 'Kampagner',
    'LBL_PROJECT_SUBPANEL_TITLE' => 'Projekter',
    'LABEL_PANEL_ASSIGNMENT' => 'Tildeling',
    'LNK_IMPORT_OPPORTUNITIES' => 'Importér salgsmuligheder',
    'LBL_EDITLAYOUT' => 'Rediger layout' /*for 508 compliance fix*/,
    //For export labels
    'LBL_EXPORT_CAMPAIGN_ID' => 'Kampagne-id',
    'LBL_OPPORTUNITY_TYPE' => 'Salgsmulighedstype',
    'LBL_EXPORT_ASSIGNED_USER_NAME' => 'Tildelt brugernavn',
    'LBL_EXPORT_ASSIGNED_USER_ID' => 'Tildelt bruger-id',
    'LBL_EXPORT_MODIFIED_USER_ID' => 'Ændret af id',
    'LBL_EXPORT_CREATED_BY' => 'Oprettet af id',
    'LBL_EXPORT_NAME' => 'Navn',
    // SNIP
    'LBL_CONTACT_HISTORY_SUBPANEL_TITLE' => 'Relaterede kontakters e-mail-adresser',
    'LBL_FILENAME' => 'Vedhæftet fil',
    'LBL_PRIMARY_QUOTE_ID' => 'Primært tilbud',
    'LBL_CONTRACTS' => 'Kontrakter',
    'LBL_CONTRACTS_SUBPANEL_TITLE' => 'Kontrakter',
    'LBL_PRODUCTS' => 'Relaterede produkter',
    'LBL_RLI' => 'Revenue Line Items',
    'LNK_OPPORTUNITY_REPORTS' => 'Salgsmulighedsrapporter',
    'LBL_QUOTES_SUBPANEL_TITLE' => 'Tilbud',
    'LBL_TEAM_ID' => 'Team-id',
    'LBL_TIMEPERIODS' => 'Tidsperioder',
    'LBL_TIMEPERIOD_ID' => 'Tidsperiode-id',
    'LBL_COMMITTED' => 'Forpligted',
    'LBL_FORECAST' => 'Inkluder i prognose',
    'LBL_COMMIT_STAGE' => 'Forpligtet fase',
    'LBL_COMMIT_STAGE_FORECAST' => 'Prognosestadie',
    'LBL_WORKSHEET' => 'Regneark',
    'LBL_PURCHASED_LINE_ITEMS' => 'Købte linjeprodukter',

    // KPI Metrics
    'LBL_ORGANIZE' => 'Organiser',
    'LBL_CREATE_NEW' => 'Opret ny',
    'LBL_MANAGE' => 'Administrer',
    'LBL_SEE_DETAILS' => 'Vis oplysninger',
    'LBL_HIDE_NEW' => 'Skjul',

    'LBL_FORECASTED_LIKELY' => 'Forudset som Sandsynlig',
    'LBL_LOST' => 'Mistet',
    'LBL_RENEWAL' => 'Fornyelse',
    'LBL_RENEWAL_OPPORTUNITIES' => 'Fornyelsesmuligheder',
    'LBL_RENEWAL_PARENT' => 'Overordnet mulighed',
    'LBL_PARENT_RENEWAL_OPPORTUNITY_ID' => 'Fornyelse overordnet ID',
    'LBL_MONTH_YEAR_RENEWAL' => '{{month}}, {{year}}',

    'LBL_WIDGET_SALES_STAGE' => 'Salgsfase',
    'LBL_WIDGET_DATE_CLOSED' => 'Forventet lukkedato',
    'LBL_WIDGET_AMOUNT' => 'Beløb',

    'TPL_RLI_CREATE' => 'En salgs mulighed, skal have en tilknyttet Revenue Line Item.',
    'TPL_RLI_CREATE_LINK_TEXT' => 'Skabe en Revenue Line Item.',
    'LBL_PRODUCTS_SUBPANEL_TITLE' => 'Produkter',
    'LBL_RLI_SUBPANEL_TITLE' => 'Indtægt linjeelementer',

    'LBL_TOTAL_RLIS' => '# Av Total Revenue poster',
    'LBL_CLOSED_RLIS' => '# Lukket Revenue poster',
    'LBL_CLOSED_WON_RLIS' => '# lukkede vundne indtægtsposter',
    'LBL_SERVICE_OPEN_FLEX_DURATION_RLIS' => '# af Open Service Flex-varighed Linjeprodukter',
    'NOTICE_NO_DELETE_CLOSED_RLIS' => 'Du kan ikke slette Opportunities som inneholder lukkede Revenue poster',
    'WARNING_NO_DELETE_CLOSED_SELECTED' => 'En eller flere av de valgte postene inneholder lukkede Revenue poster og kan ikke slettes.',
    'LBL_INCLUDED_RLIS' => '# af inkluderede indtægt linje-emner',
    'LBL_UPDATE_OPPORTUNITIES_RLIS' => 'Åben opdatering',
    'LBL_CASCADE_RLI_EDIT' => 'Opdater Åbne Revenue Line-produkter',
    'LBL_CASCADE_RLI_CREATE' => 'Angiv På Tværs Af Revenue Line-produkter',
    'LBL_SERVICE_START_DATE_INVALID' => 'Startdatoen for tjenesten kan ikke ligge tidligere end slutdatoen for tjenesten for evt. åbne tilføjelsesprodukter.',

    'LBL_QUOTE_SUBPANEL_TITLE' => 'Tilbud',
    'LBL_FILTER_OPPORTUNITY_TEMPLATE' => 'Salgsmuligheder for en dynamisk virksomhed',
    'LBL_TOP_10_OPP' => 'Top 10 åbne opps',
    'LBL_DASHLET_MY_ACTIVE_OPP' => 'Dashlet: Mine aktive muligheder',
    'LBL_MY_ACTIVE_OPP' => 'Min aktive Opps',


    // Config
    'LBL_OPPS_CONFIG_VIEW_BY_LABEL' => 'Muligheders hierarki',
    'LBL_OPPS_CONFIG_VIEW_BY_DATE_ROLLUP' => 'Indstil forventet lukkedatofelt på de resulterende mulighedsposter for at være de tidligste eller seneste lukkedatoer for de eksisterende indtægt linje-emner',

    //Dashlet
    'LBL_PIPELINE_TOTAL_IS' => 'Pipeline total er ',

    'LBL_OPPORTUNITY_ROLE' => 'Salgsmuligheds rolle:',
    'LBL_NOTES_SUBPANEL_TITLE' => 'Noter',
    'LBL_TAB_OPPORTUNITY' => 'Gennemgang af {{module}}',

    // Help Text
    'LBL_OPPS_CONFIG_ALERT' => 'Ved at klikke Bekræft, vil du blive slette ALLE data og prognosedata og ændre dine mulighedsvisning. Hvis dette ikke er, hvad du havde tænkt dig, skal du klikke på Annullér for at vende tilbage til tidligere indstillinger.',
    'LBL_OPPS_CONFIG_ALERT_TO_OPPS' =>
        'Ved at klikke på bekræft sletter du ALLE prognosedata og ændrer din visning af salgsmuligheder. '
        . 'ALLE procesdefinitioner med et målmodul af omsætningsenheder vil også blive slået fra. '
        . 'Klik på annuller for at vende tilbage til de tidligere indstillinger, hvis det ikke var hvad du havde tænkt dig.',
    'LBL_OPPS_CONFIG_SALES_STAGE_1a' => 'Hvis alle indtægt linje-emner er lukket og mindst én blev vundet',
    'LBL_OPPS_CONFIG_SALES_STAGE_1b' => 'bliver mulighedssalgsfasen indstillet til "Lukket vundet".',
    'LBL_OPPS_CONFIG_SALES_STAGE_2a' => 'Hvis alle indtægt linje-emner er i "Lukket tabt" salgsfasen,',
    'LBL_OPPS_CONFIG_SALES_STAGE_2b' => 'bliver mulighedssalgsfasen indstillet til "Lukket tabt".',
    'LBL_OPPS_CONFIG_SALES_STAGE_3a' => 'Hvis eventuelle indtægt linje-emner stadigt er åbne,',
    'LBL_OPPS_CONFIG_SALES_STAGE_3b' => 'vil muligheden blive markeret med den mindst avancerede salgsfase.',

// BEGIN ENT/ULT

    // Opps Config - View By Opportunities
    'LBL_HELP_CONFIG_OPPS' => 'Når du indleder denne ændring, vil indtægt linje-emnes sammendragne noter blive opbygget i baggrunden. Når noterne er fuldstændige og tilgængelige, vil en meddelelse blive sendt til e-mailadressen på din brugerprofil. Hvis dit eksempel er sat op til {{forecasts_module}}, vil Sugar også sende dig en meddelelse, når din {{module_name}} poster synkroniseres til {{forecasts_module}} modulet og er til rådighed for nye {{forecasts_module}}. Bemærk, at dit eksempel skal konfigureres til at sende e-mail via Admin > E-mail-indstillinger for at meddelelser kan sendes.',

    // Opps Config - View By Opportunities And RLIs
    'LBL_HELP_CONFIG_RLIS' => 'Efter du indleder denne ændring, vil indtægt lije. emneposter blive oprettet for hver eksisterende {{module_name}} i baggrunden. Når indtægt linje-emner er fuldstændige og tilgængelige, vil en meddelelse blive sendt til e-mailadressen på din brugerprofil. Bemærk, at dit eksempel skal konfigureres til at sende e-mail via Admin > E-mail-indstillinger, for at beskeden kan sendes.',
    // List View Help Text
    'LBL_HELP_RECORDS' => 'Modulet {{plural_module_name}} giver dig mulighed for at spore individuelle salg fra start til slut. Hver {{module_name}}-post repræsenterer et potentielt salg og indeholder relevante salgsdata og relaterer til andre vigtige poster som {{quotes_module}}, {{contacts_module}} osv. Et {{module_name}} vil typisk gennemgå flere salgsstadier, indtil det markeres som enten "Lukket Vundet" eller "Lukket Tabt". {{plural_module_name}} kan udnyttes endnu mere ved at bruge Sugars {{forecasts_singular_module}}-modul til at forstå og forudsige salgstendenser samt fokusere arbejdet på at opnå salgskvoter.',

    // Record View Help Text
    'LBL_HELP_RECORD' => 'Modulet {{plural_module_name}} giver dig mulighed for at spore individuelle salg og linjeelementer, der tilhøre disse salg, fra start til slut. Hver {{module_name}}-post repræsenterer et potentielt salg og indeholder relevante salgsdata samt relaterer til andre vigtige optegnelser som {{quotes_module}}, {{contacts_module}} osv.

- Redigér felterne for denne post ved at klikke på et individuelt felt eller på knappen Redigér.
- Få vist eller rediger links til andre poster i underpanelerne ved at skifte den nederste venstre rude til "Datavisning".
- Fremsæt og få vist brugerkommentarer, og registrér ændringshistorik i {{activitystream_singular_module}} ved at skifte den nederste venstre rude til "Aktivitetsstream".
- Følg denne post, eller indstil den som favorit, ved at bruge ikonerne til højre for postens navn.
- Der er yderligere handlinger tilgængelige i rullemenuen Handlinger til højre for knappen Redigér.',

    // Create View Help Text
    'LBL_HELP_CREATE' => 'Modulet {{plural_module_name}} giver dig mulighed for at spore individuelle salg og de linjeelementer, der tilhører disse salg, fra start til slut. Hver {{module_name}}-post repræsenterer et potentielt salg og indeholder relevante salgsdata samt relaterer til andre vigtige poster som {{quotes_module}}, {{contacts_module}} osv.

Sådan opretter du et {{module_name}}:
1. Angiv værdier for felterne efter behov.
 - Felter, der er mærket som "Påkrævet", skal udfyldes, før de gemmes.
 - Klik på "Vis mere" for at vise yderligere felter, hvis det er nødvendigt.
2. Klik på "Gem" for at afslutte den nye post og vende tilbage til den forrige side.',

// END ENT/ULT

    //Marketo
    'LBL_MKTO_SYNC' => 'Synkroniser til Marketo ®',
    'LBL_MKTO_ID' => 'Marketo Lead ID',

    'LBL_DASHLET_TOP10_SALES_OPPORTUNITIES_NAME' => 'Top 10 salgsmuligheder',
    'LBL_TOP10_OPPORTUNITIES_CHART_DESC' => 'Viser top Ti muligheder i et boblediagram.',
    'LBL_TOP10_OPPORTUNITIES_MY_OPP' => 'Mine muligheder',
    'LBL_TOP10_OPPORTUNITIES_MY_TEAMS_OPP' => "Mit teams muligheder",

    'LBL_PIPELINE_ERR_CLOSED_SALES_STAGE' => 'Kunne ikke ændre {{fieldName}}, eftersom dette {{moduleSingular}} ikke har nogle åbne varelinjer.',
    'TPL_ACTIVITY_TIMELINE_DASHLET' => 'Mulighed tidslinje',

    'LBL_CASCADE_SERVICE_WARNING' => ' kan ikke angives på tværs af nogen af disse Revenue Line-produkter, fordi de ikke er tjenester. Vil du fortsætte med oprettelsen?',
    'LBL_CASCADE_DURATION_WARNING' => ' kan ikke indstilles på tværs af nogen af disse Revenue Line-produkter, fordi deres varighed er låst. Vil du fortsætte med oprettelsen?',

    // AI Predict
    'LBL_AI_OPPORTUNITY_CLOSE_PREDICTION_NAME' => 'Mulighed Luk Prognose',
    'LBL_AI_OPPORTUNITY_CLOSE_PREDICTION_DESC' => 'Se prognosedetaljer for en specifik mulighed',
    'LBL_AI_WINRATE' => 'Gevinstrate',
    'LBL_AI_WONOPP' => 'Vundne salgsmuligheder',
    'LBL_AI_CLOSINGTIME' => 'Lukketidspunkt',
    'LBL_AI_CLOSEDOPP' => 'Lukkede salgsmuligheder',
    'LBL_AI_LEADTIMESPAN' => 'Tid mellem oprettelse af salgsmulighed og lukket vundet',
];
