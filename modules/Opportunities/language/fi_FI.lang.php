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
    'LBL_OPPORTUNITIES_LIST_DASHBOARD' => 'Mahdollisuusluettelon työpöytä',
    'LBL_OPPORTUNITIES_RECORD_DASHBOARD' => 'Mahdollisuusluettelon työpöytä',
    'LBL_OPPORTUNITIES_MULTI_LINE_DASHBOARD' => 'Mahdollisuuksien keskitetty koontinäyttö – konsoli',
    'LBL_OPPORTUNITIES_FOCUS_DRAWER_DASHBOARD' => 'Myyntimahdollisuudet-tietolaatikko',
    'LBL_RENEWAL_OPPORTUNITY' => 'Uusittu myyntimahdollisuus',

    'LBL_MODULE_NAME' => 'Myyntimahdollisuudet',
    'LBL_MODULE_NAME_SINGULAR' => 'Myyntimahdollisuus',
    'LBL_MODULE_TITLE' => 'Myyntimahdollisuudet: Etusivu',
    'LBL_SEARCH_FORM_TITLE' => 'Myyntimahdollisuuksien haku',
    'LBL_VIEW_FORM_TITLE' => 'Myyntimahdollisuuksien näkymä',
    'LBL_LIST_FORM_TITLE' => 'Myyntimahdollisuuksien luettelo',
    'LBL_OPPORTUNITY_NAME' => 'Mahdollisuuden nimi:',
    'LBL_OPPORTUNITY' => 'Myyntimahdollisuus:',
    'LBL_NAME' => 'Myyntimahdollisuuden nimi',
    'LBL_TIME' => 'Aika',
    'LBL_INVITEE' => 'Kontaktit',
    'LBL_CURRENCIES' => 'Valuutat',
    'LBL_LIST_OPPORTUNITY_NAME' => 'Nimi',
    'LBL_LIST_ACCOUNT_NAME' => 'Asiakkaan nimi',
    'LBL_LIST_DATE_CLOSED' => 'Odotettu sulkupäivämäärä',
    'LBL_LIST_AMOUNT' => 'Todennäköinen',
    'LBL_LIST_AMOUNT_USDOLLAR' => 'Muunnettu määrä',
    'LBL_ACCOUNT_ID' => 'Asiakkaan ID',
    'LBL_CURRENCY_RATE' => 'Valuuttakurssi',
    'LBL_CURRENCY_ID' => 'Valuutta ID',
    'LBL_CURRENCY_NAME' => 'Valuutan nimi',
    'LBL_CURRENCY_SYMBOL' => 'Valuuttasymboli',
//DON'T CONVERT THESE THEY ARE MAPPINGS
    'db_sales_stage' => 'LBL_LIST_SALES_STAGE',
    'db_name' => 'LBL_NAME',
    'db_amount' => 'LBL_LIST_AMOUNT',
    'db_date_closed' => 'LBL_LIST_DATE_CLOSED',
//END DON'T CONVERT
    'UPDATE' => 'Myyntimahdollisuus - Valuuttapäivitys',
    'UPDATE_DOLLARAMOUNTS' => 'Päivitä USD-määriä',
    'UPDATE_VERIFY' => 'Varmista määrät',
    'UPDATE_VERIFY_TXT' => 'Varmistaa, että mahdollisuuksissa olevat määrät ovat sallittuja desimaalinumeroita, jotka sisältävät vain numeroita (0-9) ja desimaaleja (.)',
    'UPDATE_FIX' => 'Korjaa määrät',
    'UPDATE_FIX_TXT' => 'Yrittää korjata mahdolliset virheelliset määrät luomalla oikeamuotoisen desimaalin nykyisestä määrästä. Jokainen muunnettu määrä varmuuskopioidaan amount_backup tietokantakenttään. Jos suoritat tämän ja ilmenee vikoja, älä suorita sitä palauttamatta vanhoja arvoja varmuuskopiosta, sillä varmuuskopio saattaa joutua ylikirjoitetuksi virheellisillä arvoilla.',
    'UPDATE_DOLLARAMOUNTS_TXT' => 'Päivitä mahdollisuuksien USD-määrät nykyisten valuuttakurssien perusteella. Tätä arvoa käytetään kaavojen ja listanäkymien valuuttamäärien laskemiseen.',
    'UPDATE_CREATE_CURRENCY' => 'Luodaan uutta valuuttaa:',
    'UPDATE_VERIFY_FAIL' => 'Tietue ei läpäissyt vahvistusta:',
    'UPDATE_VERIFY_CURAMOUNT' => 'Nykyinen määrä:',
    'UPDATE_VERIFY_FIX' => 'Korjauksen suorittaminen antaisi',
    'UPDATE_INCLUDE_CLOSE' => 'Sisällytä suljetut tietueet',
    'UPDATE_VERIFY_NEWAMOUNT' => 'Uusi määrä:',
    'UPDATE_VERIFY_NEWCURRENCY' => 'Uusi valuutta:',
    'UPDATE_DONE' => 'Tehty',
    'UPDATE_BUG_COUNT' => 'Virheitä löydetty ja yritetty korjata:',
    'UPDATE_BUGFOUND_COUNT' => 'Vikoja todettu:',
    'UPDATE_COUNT' => 'Tietueita päivitetty:',
    'UPDATE_RESTORE_COUNT' => 'Tietueita palautettu:',
    'UPDATE_RESTORE' => 'Palauta määrät',
    'UPDATE_RESTORE_TXT' => 'Palauttaa määräarvot korjauksen aikana tehtyistä varmuuskopioista.',
    'UPDATE_FAIL' => 'Ei voitu päivittää -',
    'UPDATE_NULL_VALUE' => 'Määrä on NULL, arvoksi asetetaan 0 -',
    'UPDATE_MERGE' => 'Yhdistä valuutat',
    'UPDATE_MERGE_TXT' => 'Yhdistää useita valuuttoja yhteen. Jos löydetään useita saman valuutan valuuttatietuita, ne yhdistetään. Tämä yhdistää kyseisen valuutan myös kaikille muille moduuleille.',
    'LBL_ACCOUNT_NAME' => 'Asiakkaan nimi:',
    'LBL_CURRENCY' => 'Valuutta:',
    'LBL_DATE_CLOSED' => 'Odotettu sulkupäivämäärä:',
    'LBL_DATE_CLOSED_TIMESTAMP' => 'Odotettu sulkeutumispäivämäärän aikaleima',
    'LBL_TYPE' => 'Tyyppi:',
    'LBL_CAMPAIGN' => 'Kampanja:',
    'LBL_NEXT_STEP' => 'Seuraava vaihe:',
    'LBL_SERVICE_START_DATE' => 'Palvelun aloituspäivä',
    'LBL_LEAD_SOURCE' => 'Liidin lähde:',
    'LBL_SALES_STAGE' => 'Myyntivaihe:',
    'LBL_SALES_STATUS' => 'Tila',
    'LBL_PROBABILITY' => 'Todennäköisyys (%):',
    'LBL_DESCRIPTION' => 'Kuvaus',
    'LBL_DUPLICATE' => 'Myyntimahdollisuus on mahdollinen kaksoiskappale',
    'MSG_DUPLICATE' => 'Myyntimahdollisuustietue jonka aiot luoda saattaa olla kopio jo olemassa olevasta myyntimahdollisuustietueesta. Myyntimahdollisuustietueet, joilla on samankaltainen nimi, on listattu alla.<br/>Klikkaa Tallenna jatkaaksesi tämän mahdollisuuden luomista, tai klikkaa Peruuta palataksesi moduuliin luomatta mahdollisuutta.',
    'LBL_NEW_FORM_TITLE' => 'Luo myyntimahdollisuus',
    'LNK_NEW_OPPORTUNITY' => 'Luo myyntimahdollisuus',
    'LNK_CREATE' => 'Luo diili',
    'LNK_OPPORTUNITY_LIST' => 'Näytä myyntimahdollisuudet',
    'ERR_DELETE_RECORD' => 'Tietuenumero tulee määritellä, jotta voit poistaa tilin.',
    'LBL_TOP_OPPORTUNITIES' => '10 parasta myyntimahdollisuutta',
    'NTC_REMOVE_OPP_CONFIRMATION' => 'Oletko varma, että haluat poistaa tämän yhteystiedon myyntimahdollisuudesta?',
    'OPPORTUNITY_REMOVE_PROJECT_CONFIRM' => 'Oletko varma, että haluat poistaa tämän myyntimahdollisuuden projektista?',
    'LBL_DEFAULT_SUBPANEL_TITLE' => 'Myyntimahdollisuudet',
    'LBL_ACTIVITIES_SUBPANEL_TITLE' => 'Aktiviteetit',
    'LBL_HISTORY_SUBPANEL_TITLE' => 'Historia',
    'LBL_RAW_AMOUNT' => 'Raaka määrä',
    'LBL_LEADS_SUBPANEL_TITLE' => 'Liidit',
    'LBL_CONTACTS_SUBPANEL_TITLE' => 'Kontaktit',
    'LBL_DOCUMENTS_SUBPANEL_TITLE' => 'Dokumentit',
    'LBL_PROJECTS_SUBPANEL_TITLE' => 'Projektit',
    'LBL_ASSIGNED_TO_NAME' => 'Vastuuhenkilö',
    'LBL_LIST_ASSIGNED_TO_NAME' => 'Vastuuhenkilö',
    'LBL_LIST_SALES_STAGE' => 'Myynnin vaihe',
    'LBL_MY_CLOSED_OPPORTUNITIES' => 'Suljetut myyntimahdollisuudet',
    'LBL_TOTAL_OPPORTUNITIES' => 'Myyntimahdollisuuksia yhteensä',
    'LBL_CLOSED_WON_OPPORTUNITIES' => 'Suljetut voitetut mahdollisuudet',
    'LBL_ASSIGNED_TO_ID' => 'Vastuuhenkilö:',
    'LBL_CREATED_ID' => 'Luoneen käyttäjän ID',
    'LBL_MODIFIED_ID' => 'Muokkaajan ID',
    'LBL_MODIFIED_NAME' => 'Muokkaajan nimi',
    'LBL_CREATED_USER' => 'Luonut',
    'LBL_MODIFIED_USER' => 'Muokannut',
    'LBL_CAMPAIGN_OPPORTUNITY' => 'Kampanjamahdollisuus',
    'LBL_PROJECT_SUBPANEL_TITLE' => 'Projektit',
    'LABEL_PANEL_ASSIGNMENT' => 'Tehtävä',
    'LNK_IMPORT_OPPORTUNITIES' => 'Tuo myyntimahdollisuuksia',
    'LBL_EDITLAYOUT' => 'Muokkaa asettelua' /*for 508 compliance fix*/,
    //For export labels
    'LBL_EXPORT_CAMPAIGN_ID' => 'Kampanjan ID',
    'LBL_OPPORTUNITY_TYPE' => 'Myyntimahdollisuuden tyyppi',
    'LBL_EXPORT_ASSIGNED_USER_NAME' => 'Vastuuhenkilö',
    'LBL_EXPORT_ASSIGNED_USER_ID' => 'Vastuuhenkilö',
    'LBL_EXPORT_MODIFIED_USER_ID' => 'Muokkaajan ID',
    'LBL_EXPORT_CREATED_BY' => 'Tekijän ID',
    'LBL_EXPORT_NAME' => 'Nimi',
    // SNIP
    'LBL_CONTACT_HISTORY_SUBPANEL_TITLE' => 'Liittyvien kontaktien sähköpostit',
    'LBL_FILENAME' => 'Liitetiedosto',
    'LBL_PRIMARY_QUOTE_ID' => 'Ensisijainen tarjous',
    'LBL_CONTRACTS' => 'Sopimukset',
    'LBL_CONTRACTS_SUBPANEL_TITLE' => 'Sopimukset',
    'LBL_PRODUCTS' => 'Tarjotut tuoterivit',
    'LBL_RLI' => 'Tuoterivit',
    'LNK_OPPORTUNITY_REPORTS' => 'Näytä myyntimahdollisuusraportit',
    'LBL_QUOTES_SUBPANEL_TITLE' => 'Tarjoukset',
    'LBL_TEAM_ID' => 'Tiimin ID',
    'LBL_TIMEPERIODS' => 'Ajanjaksot',
    'LBL_TIMEPERIOD_ID' => 'Ajanjakson ID',
    'LBL_COMMITTED' => 'Commitoitu',
    'LBL_FORECAST' => 'Sisällytä ennusteeseen',
    'LBL_COMMIT_STAGE' => 'Commitin vaihe',
    'LBL_COMMIT_STAGE_FORECAST' => 'Ennustetaso',
    'LBL_WORKSHEET' => 'Työkirja',
    'LBL_PURCHASED_LINE_ITEMS' => 'Ostojen tuoterivit',

    // KPI Metrics
    'LBL_ORGANIZE' => 'Järjestä',
    'LBL_CREATE_NEW' => 'Luo uusi',
    'LBL_MANAGE' => 'Hallinnoi',
    'LBL_SEE_DETAILS' => 'Näytä tiedot',
    'LBL_HIDE_NEW' => 'Piilota',

    'LBL_FORECASTED_LIKELY' => 'Ennuste: todennäköinen',
    'LBL_LOST' => 'Hävitty',
    'LBL_RENEWAL' => 'Uusinta',
    'LBL_RENEWAL_OPPORTUNITIES' => 'Uusintamahdollisuudet',
    'LBL_RENEWAL_PARENT' => 'Päätason myyntimahdollisuus',
    'LBL_PARENT_RENEWAL_OPPORTUNITY_ID' => 'Uusinnan päätason tunnus',
    'LBL_MONTH_YEAR_RENEWAL' => '{{month}}, {{year}}',

    'LBL_WIDGET_SALES_STAGE' => 'Myyntivaihe',
    'LBL_WIDGET_DATE_CLOSED' => 'Odotettu sulkupäivämäärä',
    'LBL_WIDGET_AMOUNT' => 'Määrä',

    'TPL_RLI_CREATE' => 'Myyntimahdollisuudella pitää olla liittyvä tuoterivi.',
    'TPL_RLI_CREATE_LINK_TEXT' => 'Luo tuoterivi',
    'LBL_PRODUCTS_SUBPANEL_TITLE' => 'Tarjotut tuoterivit',
    'LBL_RLI_SUBPANEL_TITLE' => 'Tuoterivit',

    'LBL_TOTAL_RLIS' => 'Tuoterivien yhteismäärä',
    'LBL_CLOSED_RLIS' => 'Suljettujen tuoterivien määrä',
    'LBL_CLOSED_WON_RLIS' => 'Suljettujen voitettujen tuoterivien määrä',
    'LBL_SERVICE_OPEN_FLEX_DURATION_RLIS' => 'Tuoterivien määrä, joissa avoin palvelu ja joustava kesto',
    'NOTICE_NO_DELETE_CLOSED_RLIS' => 'Et voi poistaa myyntimahdollisuuksia joissa on suljettuja tuoterivejä',
    'WARNING_NO_DELETE_CLOSED_SELECTED' => 'Valituista tietueista yhdessä tai useammassa on suljettuja tuoterivejä, eikä niitä voi poistaa.',
    'LBL_INCLUDED_RLIS' => 'Mukana olevien tuoterivien #',
    'LBL_UPDATE_OPPORTUNITIES_RLIS' => 'Päivitys avoinna',
    'LBL_CASCADE_RLI_EDIT' => 'Päivitä avoinna olevat tuoterivit',
    'LBL_CASCADE_RLI_CREATE' => 'Määritä tuoteriveille',
    'LBL_SERVICE_START_DATE_INVALID' => 'Avointen lisätuoterivien palvelun aloituspäivä ei voi olla palvelun päättymispäivää myöhemmin.',

    'LBL_QUOTE_SUBPANEL_TITLE' => 'Tarjoukset',
    'LBL_FILTER_OPPORTUNITY_TEMPLATE' => 'Myyntimahdollisuudet dynaamisen tilin mukaan',
    'LBL_TOP_10_OPP' => 'Top 10 av. mahd.',
    'LBL_DASHLET_MY_ACTIVE_OPP' => 'Dashlet: Omat aktiiviset mahdollisuudet',
    'LBL_MY_ACTIVE_OPP' => 'Omat akt. mahd.',


    // Config
    'LBL_OPPS_CONFIG_VIEW_BY_LABEL' => 'Myyntimahdollisuushierarkia',
    'LBL_OPPS_CONFIG_VIEW_BY_DATE_ROLLUP' => 'Aseta tulostietueiden "odotettu sulkupäivämäärä" -kentän arvo aikaisimmaksi tai myöhäisimmäksi olemassaolevista tuoteriveistä.',

    //Dashlet
    'LBL_PIPELINE_TOTAL_IS' => 'Myyntiputken summa on',

    'LBL_OPPORTUNITY_ROLE' => 'Myyntimahdollisuuden rooli',
    'LBL_NOTES_SUBPANEL_TITLE' => 'Muistiot',
    'LBL_TAB_OPPORTUNITY' => '{{module}} arvio',

    // Help Text
    'LBL_OPPS_CONFIG_ALERT' => 'Klikkaamalla "hyväksy", poistat -kaikki- ennustetiedot ja myytät myyntimahdollisuusnäkymääsi. Jos tämä ei ole mitä haluat, klikkaa "peruuta" palataksesi vanhoihin asetuksiin.',
    'LBL_OPPS_CONFIG_ALERT_TO_OPPS' =>
        'Napsauttamalla Vahvista voit poistaa KAIKKI ennustetiedot ja muuttaa Myyntimahdollisuudet-näkymää.'
        . 'Myös KAIKKI prosessimääritelmät Tuoterivit-kohdemoodulilla poistetaan käytöstä. '
        . 'Jos tämä ei ole haluamasi toiminto, palaa edellisiin asetuksiin napsauttamalla Peruuta.',
    'LBL_OPPS_CONFIG_SALES_STAGE_1a' => 'Jos kaikki tuoterivit ovat suljettu ja ainakin yksi on voitettu,',
    'LBL_OPPS_CONFIG_SALES_STAGE_1b' => 'myyntimahdollisuuden myyntivaiheen arvoksi asetetaan “suljettu - voitettu”.',
    'LBL_OPPS_CONFIG_SALES_STAGE_2a' => 'Jos kaikkien tuoterivien myyntivaiheet ovat “suljettu - hävitty”,',
    'LBL_OPPS_CONFIG_SALES_STAGE_2b' => 'myyntimahdollisuuden myyntivaiheen arvoksi asetetaaun “suljettu - hävitty”.',
    'LBL_OPPS_CONFIG_SALES_STAGE_3a' => 'Jos yksikään tuoterivi on viellä auki,',
    'LBL_OPPS_CONFIG_SALES_STAGE_3b' => 'myyntimahdollisuus merkataan vähiten edistyneellä myyntivaiheella.',

// BEGIN ENT/ULT

    // Opps Config - View By Opportunities
    'LBL_HELP_CONFIG_OPPS' => 'Kun aloitat tämän muutosprosessin, tuoterivien yhteenvetomuistiot laaditaan taustalla. Kun muistiot ovat valmiit, käyttäjäasetuksissasi määriteltyyn sähköpostiosoitteeseen lähetetään ilmoitus. Jos instanssissasi on {{forecasts_module}} käytössä, Sugar lähettää muistutuksen myös, kun {{module_name}}-moduulin tietueet on synkronoitu {{forecasts_module}}-moduuliin (jolloin {{module_name_singular}}-tietueet ovat käytettävissä uusissa {{forecasts_module}}-tietueissa). [Huomioi, että instanssisi sähköpostiasetusten on oltava kunnossa.].',

    // Opps Config - View By Opportunities And RLIs
    'LBL_HELP_CONFIG_RLIS' => 'Kun aloitat tämän muutosprosessin, jokaista {{module_name}}-tietuetta kohti luodaan tuoterivitietueita. Kun tuoterivit ovat valmiit ja saatavilla, sähköpostiosoiteeseesi lähetetään ilmoitus. [Instanssisi sähköpostiasetusten on oltava kunnossa, jotta sähköposti voidaan lähettää.].',
    // List View Help Text
    'LBL_HELP_RECORDS' => '{{plural_module_name}}-moduulin avulla pystyt seuraamaan yksittäisiä myyntitapahtumia alusta loppuun. Kukin {{module_name}}-tietue kuvastaa mahdollista myyntitapahtumaa ja sisältää liittyvät myyntitiedot sekä tiedot muista liittyvistä tärkeistä tietueista, kuten {{quotes_module}}, {{contacts_module}} jne. {{module_name}} kulkee tavallisesti usean myyntivaiheen läpi, kunnes se merkitään joko ”suljetuksi voitetuksi” tai ”suljetuksi hävityksi”. {{plural_module_name}} voidaan hyödyntää entistä pidemmälle käyttäen Sugarin {{forecasts_singular_module}}-moduulia, jonka avulla voidaan ymmärtää ja ennustaa myyntitrendejä sekä keskittää työ myyntikiintiöiden saavuttamiseksi.',

    // Record View Help Text
    'LBL_HELP_RECORD' => '{{plural_module_name}}-moduulin avulla pystyt seuraamaan yksittäisiä myyntitapahtumia alusta loppuun. Kukin {{module_name}}-tietue kuvastaa mahdollista myyntitapahtumaa ja sisältää liittyvät myyntitiedot sekä tiedot muista liittyvistä tärkeistä tietueista, kuten {{quotes_module}}, {{contacts_module}} jne.

- Muokkaa tämän tietueen kenttiä napsauttamalla yksittäistä kenttää tai Muokkaa-painiketta.
- Tarkastele tai muokkaa linkkejä muihin tietueisiin alapaneeleissa kytkemällä vasen alapaneeli asentoon ”Tietonäkymä”.
- Tee ja tarkastele käyttäjäkommentteja ja tietueen muutoshistoriaa {{activitystream_singular_module}}-moduulissa kytkemällä vasen alapaneeli asentoon ”Aktiviteettivirta”.
- Seuraa tätä tietuetta tai merkitse se suosikkeihin käyttäen tietueen nimen oikealla olevia kuvakkeita.
- Lisätoimintoja on saatavilla Toiminnot-pudotusvalikossa Muokkaa-painikkeen oikealla puolella.',

    // Create View Help Text
    'LBL_HELP_CREATE' => '{{plural_module_name}}-moduulin avulla pystyt seuraamaan yksittäisiä myyntitapahtumia alusta loppuun. Kukin {{module_name}}-tietue kuvastaa mahdollista myyntitapahtumaa ja sisältää liittyvät myyntitiedot sekä tiedot muista liittyvistä tärkeistä tietueista, kuten {{quotes_module}}, {{contacts_module}} jne.

{{module_name}}-moduulin luominen:
1. Anna kenttiin haluamasi arvot.
 - ”Pakollinen”-merkityt kentät on täytettävä ennen tallentamista.
 - Paljasta lisää kenttiä tarvittaessa napsauttamalla ”Näytä lisää”.
2. Viimeistele uusi tietue ja palaa edelliselle sivulle napsauttamalla ”Tallenna”.',

// END ENT/ULT

    //Marketo
    'LBL_MKTO_SYNC' => 'Synkronoi Marketoon',
    'LBL_MKTO_ID' => 'Marketo liidi-ID',

    'LBL_DASHLET_TOP10_SALES_OPPORTUNITIES_NAME' => '10 parasta myynnin myyntimahdollisuutta',
    'LBL_TOP10_OPPORTUNITIES_CHART_DESC' => 'Listaa 10 parasta myyntimahdollisuutta kuplakaaviossa.',
    'LBL_TOP10_OPPORTUNITIES_MY_OPP' => 'Myyntimahdollisuuteni',
    'LBL_TOP10_OPPORTUNITIES_MY_TEAMS_OPP' => "Tiimini myyntimahdollisuudet",

    'LBL_PIPELINE_ERR_CLOSED_SALES_STAGE' => 'Moduulin {{fieldName}} nimeä ei voida muuttaa, koska tässä moduulissa {{moduleSingular}} ei ole avoinna olevia tuoterivejä.',
    'TPL_ACTIVITY_TIMELINE_DASHLET' => 'Myyntimahdollisuuden aikajana',

    'LBL_CASCADE_SERVICE_WARNING' => ' ei voida määrittää millekään näille tuoteriveille, koska ne eivät ole palveluita. Haluatko jatkaa luontia?',
    'LBL_CASCADE_DURATION_WARNING' => ' ei voida määrittää millekään näille tuoteriveille, koska niiden kestot on lukittu. Haluatko jatkaa luontia?',

    // AI Predict
    'LBL_AI_OPPORTUNITY_CLOSE_PREDICTION_NAME' => 'Myyntimahdollisuuden sulkeutumisennuste',
    'LBL_AI_OPPORTUNITY_CLOSE_PREDICTION_DESC' => 'Tarkastele tietyn myyntimahdollisuuden ennustetietoja',
    'LBL_AI_WINRATE' => 'Voittoprosentti',
    'LBL_AI_WONOPP' => 'Voitetut mahdollisuudet',
    'LBL_AI_CLOSINGTIME' => 'Sulkemisaika',
    'LBL_AI_CLOSEDOPP' => 'Suljetut myyntimahdollisuudet',
    'LBL_AI_LEADTIMESPAN' => 'Mahdollisuuden luonnin ja suljetun voitetun välinen aika',
];
