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
    'LBL_OPPORTUNITIES_LIST_DASHBOARD' => 'Dashboard opportunitylijst',
    'LBL_OPPORTUNITIES_RECORD_DASHBOARD' => 'Dashboard opportunityrecord',
    'LBL_OPPORTUNITIES_MULTI_LINE_DASHBOARD' => 'Focus drawer-opportunity&#39;s - console',
    'LBL_OPPORTUNITIES_FOCUS_DRAWER_DASHBOARD' => 'Focus drawer opportunity&#39;s',
    'LBL_RENEWAL_OPPORTUNITY' => 'Verlenging opportunity',

    'LBL_MODULE_NAME' => 'Opportunities',
    'LBL_MODULE_NAME_SINGULAR' => 'Opportunity',
    'LBL_MODULE_TITLE' => 'Opportunities: Start',
    'LBL_SEARCH_FORM_TITLE' => 'Opportunity zoeken',
    'LBL_VIEW_FORM_TITLE' => 'Opportunity overzicht',
    'LBL_LIST_FORM_TITLE' => 'Opportunities',
    'LBL_OPPORTUNITY_NAME' => 'Opportunitynaam:',
    'LBL_OPPORTUNITY' => 'Opportunity:',
    'LBL_NAME' => 'Opportunitynaam',
    'LBL_TIME' => 'Tijd',
    'LBL_INVITEE' => 'Personen',
    'LBL_CURRENCIES' => 'Valutas',
    'LBL_LIST_OPPORTUNITY_NAME' => 'Opportunity',
    'LBL_LIST_ACCOUNT_NAME' => 'Organisatienaam',
    'LBL_LIST_DATE_CLOSED' => 'Verwachte sluitingsdatum',
    'LBL_LIST_AMOUNT' => 'Meest waarschijnlijk',
    'LBL_LIST_AMOUNT_USDOLLAR' => 'Totaal Bedrag',
    'LBL_ACCOUNT_ID' => 'Organisatie ID',
    'LBL_CURRENCY_RATE' => 'Valutakoers',
    'LBL_CURRENCY_ID' => 'Valuta ID',
    'LBL_CURRENCY_NAME' => 'Valutanaam',
    'LBL_CURRENCY_SYMBOL' => 'Valuta symbool',
//DON'T CONVERT THESE THEY ARE MAPPINGS
    'db_sales_stage' => 'LBL_LIST_SALES_STAGE',
    'db_name' => 'LBL_NAME',
    'db_amount' => 'LBL_LIST_AMOUNT',
    'db_date_closed' => 'LBL_LIST_DATE_CLOSED',
//END DON'T CONVERT
    'UPDATE' => 'Opportunity - Valuta update',
    'UPDATE_DOLLARAMOUNTS' => 'Update U.S. Dollar bedragen',
    'UPDATE_VERIFY' => 'Controleer bedragen',
    'UPDATE_VERIFY_TXT' => 'Controleert of de bedragen in Opportunities geldige numerieke waarden bevatten met slechts numerieke karakters (0-9) en decimalen (.).',
    'UPDATE_FIX' => 'Herstel bedragen',
    'UPDATE_FIX_TXT' => 'Probeert ongeldige waarden te herstellen door hiervan een decimale waarde te maken. De originele waarden worden gebackup-ed in het database veld "amount_backup". Wanneer u deze functie gebruikt en u stuit op fouten, start de actie dan pas opnieuw nadat u de backup terug hebt gezet. Anders zal de backup overschreven kunnen worden met nieuwe ongeldige data.',
    'UPDATE_DOLLARAMOUNTS_TXT' => 'Update de U.S. Dollar bedragen voor Opportunities gebaseerd op de huidige set van wisselkoersen. Deze waarde wordt gebruikt voor de berekening van grafieken en de weergave in de List View.',
    'UPDATE_CREATE_CURRENCY' => 'Nieuwe valuta aanmaken:',
    'UPDATE_VERIFY_FAIL' => 'Controle van record mislukt:',
    'UPDATE_VERIFY_CURAMOUNT' => 'Huidig bedrag:',
    'UPDATE_VERIFY_FIX' => 'Na herstel wordt dit',
    'UPDATE_INCLUDE_CLOSE' => 'Inclusief gesloten records',
    'UPDATE_VERIFY_NEWAMOUNT' => 'Nieuw bedrag:',
    'UPDATE_VERIFY_NEWCURRENCY' => 'Nieuwe Valuta:',
    'UPDATE_DONE' => 'Gereed',
    'UPDATE_BUG_COUNT' => 'Fouten gevonden en geprobeerd op te lossen:',
    'UPDATE_BUGFOUND_COUNT' => 'Gevonden fouten:',
    'UPDATE_COUNT' => 'Bijgewerkte records:',
    'UPDATE_RESTORE_COUNT' => 'Records waarvan bedragen zijn teruggezet:',
    'UPDATE_RESTORE' => 'Bedragen terugzetten',
    'UPDATE_RESTORE_TXT' => 'Zet bedragen terug uit de backup die is gemaakt gedurende het herstel.',
    'UPDATE_FAIL' => 'Kon niet bijwerken -',
    'UPDATE_NULL_VALUE' => 'Bedrag is NULL, bezig met instellen op 0 -',
    'UPDATE_MERGE' => 'Valuta samenvoegen',
    'UPDATE_MERGE_TXT' => 'Voeg meerdere valuta samen tot een enkele valuta. Als er meerdere valuta records zijn voor dezelfde valuta, kunt u deze samenvoegen. Dit zal ook de valuta voor andere modules samenvoegen.',
    'LBL_ACCOUNT_NAME' => 'Organisatienaam:',
    'LBL_CURRENCY' => 'Valuta:',
    'LBL_DATE_CLOSED' => 'Verwachte afsluitdatum:',
    'LBL_DATE_CLOSED_TIMESTAMP' => 'Verwachte sluitingsdatum',
    'LBL_TYPE' => 'Type:',
    'LBL_CAMPAIGN' => 'Campagne:',
    'LBL_NEXT_STEP' => 'Volgende stap:',
    'LBL_SERVICE_START_DATE' => 'Startdatum dienst',
    'LBL_LEAD_SOURCE' => 'Bron voor lead:',
    'LBL_SALES_STAGE' => 'Verkoopstadium:',
    'LBL_SALES_STATUS' => 'Status',
    'LBL_PROBABILITY' => 'Waarschijnlijkheid (%):',
    'LBL_DESCRIPTION' => 'Beschrijving:',
    'LBL_DUPLICATE' => 'Mogelijk dubbele Opportunity',
    'MSG_DUPLICATE' => 'Het opportunity record dat u nu wilt aanmaken zou een duplicaat kunnen zijn van een reeds bestaande opportunity. Opportunities die dezelfde namen hebben worden hieronder getoond.<br>Klik op Opslaan om door te gaan met het aanmaken van deze nieuwe opportunity, of klik op annuleren om terug te gaan naar de module zonder de opportunity aan te maken.',
    'LBL_NEW_FORM_TITLE' => 'Nieuwe Opportunity',
    'LNK_NEW_OPPORTUNITY' => 'Nieuwe Opportunity',
    'LNK_CREATE' => 'Maak nieuwe Deal',
    'LNK_OPPORTUNITY_LIST' => 'Bekijk Opportunities',
    'ERR_DELETE_RECORD' => 'U dient een recordnummer op te geven om deze opportunity te verwijderen.',
    'LBL_TOP_OPPORTUNITIES' => 'Mijn openstaande top Opportunities',
    'NTC_REMOVE_OPP_CONFIRMATION' => 'Weet u zeker dat u deze persoon wilt verwijderen bij deze Opportunity?',
    'OPPORTUNITY_REMOVE_PROJECT_CONFIRM' => 'Weet je zeker dat je deze Opportunity uit het project wilt verwijderen?',
    'LBL_DEFAULT_SUBPANEL_TITLE' => 'Opportunities',
    'LBL_ACTIVITIES_SUBPANEL_TITLE' => 'Activiteiten',
    'LBL_HISTORY_SUBPANEL_TITLE' => 'Geschiedenis',
    'LBL_RAW_AMOUNT' => 'Ruw Bedrag',
    'LBL_LEADS_SUBPANEL_TITLE' => 'Leads',
    'LBL_CONTACTS_SUBPANEL_TITLE' => 'Personen',
    'LBL_DOCUMENTS_SUBPANEL_TITLE' => 'Documenten',
    'LBL_PROJECTS_SUBPANEL_TITLE' => 'Projecten',
    'LBL_ASSIGNED_TO_NAME' => 'Toegewezen aan:',
    'LBL_LIST_ASSIGNED_TO_NAME' => 'Toegewezen aan',
    'LBL_LIST_SALES_STAGE' => 'Verkoopstadium',
    'LBL_MY_CLOSED_OPPORTUNITIES' => 'Mijn gewonnen Opportunities',
    'LBL_TOTAL_OPPORTUNITIES' => 'Totaal Opportunities',
    'LBL_CLOSED_WON_OPPORTUNITIES' => 'Gewonnen Opportunities',
    'LBL_ASSIGNED_TO_ID' => 'Toegewezen aan ID',
    'LBL_CREATED_ID' => 'Aangemaakt door ID',
    'LBL_MODIFIED_ID' => 'Gewijzigd door ID',
    'LBL_MODIFIED_NAME' => 'Gewijzigd door Gebruikersnaam',
    'LBL_CREATED_USER' => 'Aangemaakt door Gebruiker',
    'LBL_MODIFIED_USER' => 'Gewijzigd door Gebruiker',
    'LBL_CAMPAIGN_OPPORTUNITY' => 'Campagnes',
    'LBL_PROJECT_SUBPANEL_TITLE' => 'Projecten',
    'LABEL_PANEL_ASSIGNMENT' => 'Toewijzing',
    'LNK_IMPORT_OPPORTUNITIES' => 'Importeer Opportunities',
    'LBL_EDITLAYOUT' => 'Wijzig Lay-out' /*for 508 compliance fix*/,
    //For export labels
    'LBL_EXPORT_CAMPAIGN_ID' => 'Campagne ID',
    'LBL_OPPORTUNITY_TYPE' => 'Type opportunity',
    'LBL_EXPORT_ASSIGNED_USER_NAME' => 'Toegewezen aan',
    'LBL_EXPORT_ASSIGNED_USER_ID' => 'Toegewezen aan ID',
    'LBL_EXPORT_MODIFIED_USER_ID' => 'Gewijzigd door ID',
    'LBL_EXPORT_CREATED_BY' => 'Gemaakt door ID',
    'LBL_EXPORT_NAME' => 'Naam',
    // SNIP
    'LBL_CONTACT_HISTORY_SUBPANEL_TITLE' => 'E-mails van gerelateerde contactpersonen',
    'LBL_FILENAME' => 'Bijlage',
    'LBL_PRIMARY_QUOTE_ID' => 'Primaire Offerte',
    'LBL_CONTRACTS' => 'Contracten',
    'LBL_CONTRACTS_SUBPANEL_TITLE' => 'Contracten',
    'LBL_PRODUCTS' => 'Producten',
    'LBL_RLI' => 'Opportunityregel',
    'LNK_OPPORTUNITY_REPORTS' => 'Opportunity Rapporten',
    'LBL_QUOTES_SUBPANEL_TITLE' => 'Offertes',
    'LBL_TEAM_ID' => 'Team-ID',
    'LBL_TIMEPERIODS' => 'Periodes',
    'LBL_TIMEPERIOD_ID' => 'TimePeriod ID',
    'LBL_COMMITTED' => 'Toegewezen',
    'LBL_FORECAST' => 'Meenemen in de forecast',
    'LBL_COMMIT_STAGE' => 'Commit stadium',
    'LBL_COMMIT_STAGE_FORECAST' => 'Fase Forecast',
    'LBL_WORKSHEET' => 'Werkblad',
    'LBL_PURCHASED_LINE_ITEMS' => 'Aangeschafte regelitems',

    // KPI Metrics
    'LBL_ORGANIZE' => 'Organiseren',
    'LBL_CREATE_NEW' => 'Maak Nieuwe',
    'LBL_MANAGE' => 'Beheren',
    'LBL_SEE_DETAILS' => 'Bekijk details',
    'LBL_HIDE_NEW' => 'Verbergen',

    'LBL_FORECASTED_LIKELY' => 'Voorspeld hoogstwaarschijnlijk',
    'LBL_LOST' => 'Verloren',
    'LBL_RENEWAL' => 'Verlenging',
    'LBL_RENEWAL_OPPORTUNITIES' => 'Opportunities verlenging',
    'LBL_RENEWAL_PARENT' => 'Bovenliggende opportunity',
    'LBL_PARENT_RENEWAL_OPPORTUNITY_ID' => 'Bovenliggende ID verlenging',
    'LBL_MONTH_YEAR_RENEWAL' => '{{month}}, {{year}}',

    'LBL_WIDGET_SALES_STAGE' => 'Verkoopstadium',
    'LBL_WIDGET_DATE_CLOSED' => 'Verwachte afsluitdatum',
    'LBL_WIDGET_AMOUNT' => 'Bedrag',

    'TPL_RLI_CREATE' => 'Een opportunity moet een bijbehorende opportunityregel hebben.',
    'TPL_RLI_CREATE_LINK_TEXT' => 'Maak een Opportunityregel aan.',
    'LBL_PRODUCTS_SUBPANEL_TITLE' => 'Geoffreerde producten',
    'LBL_RLI_SUBPANEL_TITLE' => 'Opportunityregels',

    'LBL_TOTAL_RLIS' => '# aantal opportunityregels',
    'LBL_CLOSED_RLIS' => '# aantal gesloten opportunityregels',
    'LBL_CLOSED_WON_RLIS' => '# aantal gesloten gewonnen omzetregelitems',
    'LBL_SERVICE_OPEN_FLEX_DURATION_RLIS' => 'Aantal open service flexibele duur opportunityregels',
    'NOTICE_NO_DELETE_CLOSED_RLIS' => 'U kunt geen Opportunities verwijderen die afgesloten opportunityregels bevatten',
    'WARNING_NO_DELETE_CLOSED_SELECTED' => 'Één of meer van de geselecteerde gegevens bevat gesloten opportunityregels en kan daarom niet worden verwijderd.',
    'LBL_INCLUDED_RLIS' => '# inbegrepen onderdelen van de Omzetregel',
    'LBL_UPDATE_OPPORTUNITIES_RLIS' => 'Update geopend',
    'LBL_CASCADE_RLI_EDIT' => 'Open opportunityregels bijgewerken',
    'LBL_CASCADE_RLI_CREATE' => 'Instellen binnen Revenue Line Items',
    'LBL_SERVICE_START_DATE_INVALID' => 'De startdatum van de service kan niet na de einddatum van de service liggen voor eventuele geopende add-on omzetregelitems.',

    'LBL_QUOTE_SUBPANEL_TITLE' => 'Offertes',
    'LBL_FILTER_OPPORTUNITY_TEMPLATE' => 'Opportunity&#39;s op een dynamisch account',
    'LBL_TOP_10_OPP' => 'Top 10 open opps',
    'LBL_DASHLET_MY_ACTIVE_OPP' => 'Dashlet: Mijn actieve mogelijkheden',
    'LBL_MY_ACTIVE_OPP' => 'Mijn actieve opps',


    // Config
    'LBL_OPPS_CONFIG_VIEW_BY_LABEL' => 'Opportunity Hiërarchie',
    'LBL_OPPS_CONFIG_VIEW_BY_DATE_ROLLUP' => 'Vul de Verwachte afsluitdatum van de bovenliggende Opportunities met de eerste of laatste afsluitdatum van de onderliggende Revenue Line Items<br /><br />Set the Expected Close Date field on the resulting Opportunity records to be the earliest or latest close dates of the existing Revenue Line Items',

    //Dashlet
    'LBL_PIPELINE_TOTAL_IS' => 'Totaal in de pijplijn is',

    'LBL_OPPORTUNITY_ROLE' => 'Opportunity Rol',
    'LBL_NOTES_SUBPANEL_TITLE' => 'Notities',
    'LBL_TAB_OPPORTUNITY' => 'Beoordeling {{module}}',

    // Help Text
    'LBL_OPPS_CONFIG_ALERT' => 'Door op Bevestigen te klikken wist u ALLE Voorspellingen en wijzigt u uw Mogelijkhedenweergave. Als dit niet is wat u wilde klikt u op Annuleren om terug te keren naar de eerdere instellingen.',
    'LBL_OPPS_CONFIG_ALERT_TO_OPPS' =>
        'Door op Bevestigen te klikken, wist u ALLE voorspellingen en wijzigt u uw Opportunities weergave.'
        . 'Ook ALLE procesdefinities met een doelmodule of omzetregels worden uitgeschakeld.'
        . 'Als dit niet uw bedoeling is, klikt u op annuleren om terug te keren naar de vorige instellingen.',
    'LBL_OPPS_CONFIG_SALES_STAGE_1a' => 'Wanneer alle Revenue Line Items gesloten zijn en tenminste één is Gewonnen,',
    'LBL_OPPS_CONFIG_SALES_STAGE_1b' => 'dan zal de Opportunity Verkoopstadium op "Gewonnen" worden gezet.',
    'LBL_OPPS_CONFIG_SALES_STAGE_2a' => 'Wanneer alle Revenue Line Items het Verkoopstadium &#39;Verloren&#39; hebben,',
    'LBL_OPPS_CONFIG_SALES_STAGE_2b' => 'dan zal de Opportunity Verkoopstadium op "Verloren" worden gezet.',
    'LBL_OPPS_CONFIG_SALES_STAGE_3a' => 'Wanneer enkele Revenue Line Items nog open zijn,',
    'LBL_OPPS_CONFIG_SALES_STAGE_3b' => 'dan zal de Opportunity Verkoopstadium de laagste overnemen.',

// BEGIN ENT/ULT

    // Opps Config - View By Opportunities
    'LBL_HELP_CONFIG_OPPS' => 'Nadat u deze wijziging heeft doorgevoerd worden de samenvattingsnotities van het onderdeel Omzetregel op de achtergrond opgebouwd. Zodra de notities zijn voltooid en beschikbaar zijn zal een bericht naar het e-mailadres van uw gebruikersprofiel worden gestuurd. Als uw exemplaar bijvoorbeeld is geïnstalleerd voor de {{forecasts_module}} zal Sugar u ook een bericht sturen zodra uw {{module_name}} records gesynchroniseerd zijn naar de {{forecasts_module}} module en beschikbaar zijn voor nieuwe {{forecasts_module}}. Houd er rekening mee dat uw exemplaar zo moet zijn geconfigureerd dat er een e-mail wordt gestuurd. Dit kunt u doen via Beheer E-mailinstellingen om berichten te kunnen verzenden.',

    // Opps Config - View By Opportunities And RLIs
    'LBL_HELP_CONFIG_RLIS' => 'Nadat u deze wijziging heeft doorgevoerd zullen de records van het onderdeel Omzetregel op de achtergrond worden aangemaakt voor elke huidige {{module_name}}. Zodra de onderdelen van de Omzetregel voltooid en beschikbaar zijn zal een bericht naar het e-mailadres van uw gebruikersprofiel worden gestuurd. Houd er rekening mee dat uw exemplaar zo moet zijn geconfigureerd dat er een e-mail wordt gestuurd. Dit kunt u doen via Beheer > E-mailinstellingen om het bericht te verzenden.',
    // List View Help Text
    'LBL_HELP_RECORDS' => 'De {{plural_module_name}} Met de module kunt u individuele verkopen van begin tot eind volgen. Elk {{module_name}}-record vertegenwoordigt een potentiële verkoop en bevat relevante verkoopgegevens en met betrekking tot andere belangrijke records, zoals {{quotes_module}}, {{contacts_module}}etc. Een {{module_name}} doorloopt doorgaans verschillende verkoopfasen totdat het is gemarkeerd als "Gesloten gewonnen" of "Gesloten verloren". {{plural_module_name}} kan nog verder worden benut door gebruik te maken van de {{forecasts_singular_module}}-module van Sugar om verkooptrends te begrijpen en te voorspellen en om het werk te richten op het bereiken van verkoopquota.',

    // Record View Help Text
    'LBL_HELP_RECORD' => 'Met de {{plural_module_name}}-module kunt u van begin tot eind de individuele verkoop volgen en de regelitems die bij elke verkoop horen. Elke {{module_name}}-record vertegenwoordigt een mogelijke verkoop en bevat zowel relevante verkoopgegevens als gegevens die betrekking hebben op andere belangrijke records, zoals {{quotes_module}}, {{contacts_module}}, etc.

- Bewerk de velden van deze record door op een individueel veld te klikken of op de knop Bewerken.
- Bekijk of wijzig links naar andere records in de subpanelen door het paneel linksonder op Gegevensoverzicht te zetten.
- Maak en bekijk gebruikersreacties en de wijzigingsgeschiedenis van records in de {{activitystream_singular_module}} door het paneel linksonder op Activity Stream te zetten.
- Volg deze record of markeer dit als favoriet via de iconen aan de rechterkant van de recordnaam.
- Meer acties zijn beschikbaar via het dropdown-actiemenu aan de rechterkant van de knop Bewerken.',

    // Create View Help Text
    'LBL_HELP_CREATE' => 'Met de {{plural_module_name}}-module kunt u van begin tot eind de individuele verkoop volgen en de regelitems die bij elke verkoop horen. Elke {{module_name}}-record vertegenwoordigt een potentiële verkoop en bevat zowel relevante verkoopgegevens als gegevens die betrekking hebben op andere belangrijke records zoals {{quotes_module}}, {{contacts_module}}, etc.

Een {{module_name}} maken:
1. Voer de gewenste gegevens in.
 - Velden die verplicht zijn, moeten ingevuld zijn voordat de record opgeslagen wordt.
 - Klik op Meer tonen om zo nodig extra velden te tonen.
2. Klik op Opslaan om de nieuwe record op te slaan en terug te keren naar de vorige pagina.',

// END ENT/ULT

    //Marketo
    'LBL_MKTO_SYNC' => 'Synchroniseren naar Markto&reg;',
    'LBL_MKTO_ID' => 'Marketo Lead ID',

    'LBL_DASHLET_TOP10_SALES_OPPORTUNITIES_NAME' => 'Top 10 opportunities',
    'LBL_TOP10_OPPORTUNITIES_CHART_DESC' => 'Toont de top 10 Revenue Line Items in een &#39;bubble&#39; diagram.',
    'LBL_TOP10_OPPORTUNITIES_MY_OPP' => 'Mijn Opportunities',
    'LBL_TOP10_OPPORTUNITIES_MY_TEAMS_OPP' => "Mijn Team Opportunities",

    'LBL_PIPELINE_ERR_CLOSED_SALES_STAGE' => 'Kan {{fieldName}} niet wijzigen gezien {{moduleSingular}} geen open regelitems bevat.',
    'TPL_ACTIVITY_TIMELINE_DASHLET' => 'Tijdlijn opportunity',

    'LBL_CASCADE_SERVICE_WARNING' => ' kan niet worden ingesteld in een van deze Revenue Line Items omdat dit geen diensten zijn. Wilt u het aanmaken vervolgen?',
    'LBL_CASCADE_DURATION_WARNING' => ' kan niet worden ingesteld in een van deze Revenue Line Items omdat de duur is vergendeld. Wilt u het aanmaken vervolgen?',

    // AI Predict
    'LBL_AI_OPPORTUNITY_CLOSE_PREDICTION_NAME' => 'Voorspelling sluiting opportunity',
    'LBL_AI_OPPORTUNITY_CLOSE_PREDICTION_DESC' => 'Voorspellingsgegevens bekijken voor een specifieke opportunity',
    'LBL_AI_WINRATE' => 'Winstpercentage',
    'LBL_AI_WONOPP' => 'Gewonnen opportunity&#39;s',
    'LBL_AI_CLOSINGTIME' => 'Sluitingstijd',
    'LBL_AI_CLOSEDOPP' => 'Gesloten opportunities',
    'LBL_AI_LEADTIMESPAN' => 'Tijd tussen het maken van de opportunity en gesloten gewonnen',
];
