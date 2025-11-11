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
    'LBL_OPPORTUNITIES_LIST_DASHBOARD' => 'Dashboard elenco opportunità',
    'LBL_OPPORTUNITIES_RECORD_DASHBOARD' => 'Dashboard record opportunità',
    'LBL_OPPORTUNITIES_MULTI_LINE_DASHBOARD' => 'Dashboard focus opportunità - Console',
    'LBL_OPPORTUNITIES_FOCUS_DRAWER_DASHBOARD' => 'Opportunità Focus Drawer',
    'LBL_RENEWAL_OPPORTUNITY' => 'Opportunità di rinnovo',

    'LBL_MODULE_NAME' => 'Opportunità',
    'LBL_MODULE_NAME_SINGULAR' => 'Opportunità',
    'LBL_MODULE_TITLE' => 'Opportunità: Home',
    'LBL_SEARCH_FORM_TITLE' => 'Cerca Opportunità',
    'LBL_VIEW_FORM_TITLE' => 'Vista Opportunità',
    'LBL_LIST_FORM_TITLE' => 'Elenco Opportunità',
    'LBL_OPPORTUNITY_NAME' => 'Nome Opportunità:',
    'LBL_OPPORTUNITY' => 'Opportunità:',
    'LBL_NAME' => 'Nome Opportunità',
    'LBL_TIME' => 'Ora',
    'LBL_INVITEE' => 'Contatti',
    'LBL_CURRENCIES' => 'Valute',
    'LBL_LIST_OPPORTUNITY_NAME' => 'Nome',
    'LBL_LIST_ACCOUNT_NAME' => 'Nome Azienda',
    'LBL_LIST_DATE_CLOSED' => 'Chiusa',
    'LBL_LIST_AMOUNT' => 'Importo',
    'LBL_LIST_AMOUNT_USDOLLAR' => 'Importo',
    'LBL_ACCOUNT_ID' => 'ID Azienda',
    'LBL_CURRENCY_RATE' => 'Tasso di Valuta',
    'LBL_CURRENCY_ID' => 'ID Valuta',
    'LBL_CURRENCY_NAME' => 'Nome Valuta',
    'LBL_CURRENCY_SYMBOL' => 'Simbolo Valuta',
//DON'T CONVERT THESE THEY ARE MAPPINGS
    'db_sales_stage' => 'LBL_LIST_SALES_STAGE',
    'db_name' => 'LBL_NAME',
    'db_amount' => 'LBL_LIST_AMOUNT',
    'db_date_closed' => 'LBL_LIST_DATE_CLOSED',
//END DON'T CONVERT
    'UPDATE' => 'Aggiornamento Opportunità - Valuta',
    'UPDATE_DOLLARAMOUNTS' => 'Aggiorna Importi Dollari U.S.',
    'UPDATE_VERIFY' => 'Controlla Importi',
    'UPDATE_VERIFY_TXT' => 'Verifica che gli importi nelle opportunità siano numeri decimali validi composti soltanto da caratteri numerici (0-9) e da decimali (.)',
    'UPDATE_FIX' => 'Importo Fisso',
    'UPDATE_FIX_TXT' => 'Prova a correggere gli importi non validi a partire dagli importi correnti. Verrà creata una copia di backup in un campo amount_backup. Se esegui la correzione e noti degli errori ripristina i valori precedenti prima di effettuare altre correzioni, altrimenti perderai  i valori memorizzati nella copia di backup.',
    'UPDATE_DOLLARAMOUNTS_TXT' => 'Aggiorna gli importi in Dollari USA per le opportunità basandosi sul cambio corrente. Questo valore viene utilizzato per costruire i Grafici e l´elenco delle viste degli importi in valuta.',
    'UPDATE_CREATE_CURRENCY' => 'Creazione Nuova Valuta:',
    'UPDATE_VERIFY_FAIL' => 'Verifica Fallita del Record:',
    'UPDATE_VERIFY_CURAMOUNT' => 'Importo Attuale:',
    'UPDATE_VERIFY_FIX' => 'L´avvio della correzione comporterebbe',
    'UPDATE_INCLUDE_CLOSE' => 'Includi Record Chiusi',
    'UPDATE_VERIFY_NEWAMOUNT' => 'Nuovo Importo:',
    'UPDATE_VERIFY_NEWCURRENCY' => 'Nuova Valuta:',
    'UPDATE_DONE' => 'Fatto',
    'UPDATE_BUG_COUNT' => 'Bugs trovati e in attesa di essere risolti:',
    'UPDATE_BUGFOUND_COUNT' => 'Bugs Trovati:',
    'UPDATE_COUNT' => 'Record Aggiornati:',
    'UPDATE_RESTORE_COUNT' => 'Importi del record ripristinati:',
    'UPDATE_RESTORE' => 'Ripristina Importi',
    'UPDATE_RESTORE_TXT' => 'Ripristina gli importi dalle copie di backup create durante la correzione.',
    'UPDATE_FAIL' => 'Impossibile aggiornare -',
    'UPDATE_NULL_VALUE' => 'L´importo è NULLO impostandolo a 0 -',
    'UPDATE_MERGE' => 'Unisci Valute',
    'UPDATE_MERGE_TXT' => 'Unisci più valute in un´unica valuta. Se noti che la stessa valuta si ripete più volte puoi scegliere di unirle. Quest´operazione unirà le valute anche per tutti gli altri moduli.',
    'LBL_ACCOUNT_NAME' => 'Nome Azienda:',
    'LBL_CURRENCY' => 'Valuta:',
    'LBL_DATE_CLOSED' => 'Data di chiusura prevista:',
    'LBL_DATE_CLOSED_TIMESTAMP' => 'Timestamp data di chiusura prevista',
    'LBL_TYPE' => 'Tipo:',
    'LBL_CAMPAIGN' => 'Campagna:',
    'LBL_NEXT_STEP' => 'Prossimo Passo:',
    'LBL_SERVICE_START_DATE' => 'Data di inizio servizio',
    'LBL_LEAD_SOURCE' => 'Fonte del Lead:',
    'LBL_SALES_STAGE' => 'Fase di Vendita:',
    'LBL_SALES_STATUS' => 'Stato',
    'LBL_PROBABILITY' => 'Probabilità (%):',
    'LBL_DESCRIPTION' => 'Descrizione:',
    'LBL_DUPLICATE' => 'Possibile Duplicato dell´Opportunità',
    'MSG_DUPLICATE' => 'L´opportunità che stai creando potrebbe generare un duplicato. L´opportunità ha un nome simile rispetto a quelle elencate qui sotto. Clicca Salva per continuare con la creazione di questa nuova opportunità, o clicca Annulla per ritornare al modulo senza creare l´opportunità.',
    'LBL_NEW_FORM_TITLE' => 'Nuova Opportunità',
    'LNK_NEW_OPPORTUNITY' => 'Nuova Opportunità',
    'LNK_CREATE' => 'Crea Trattativa',
    'LNK_OPPORTUNITY_LIST' => 'Visualizza Opportunità',
    'ERR_DELETE_RECORD' => 'Per eliminare questa opportunità deve essere specificato un numero del record.',
    'LBL_TOP_OPPORTUNITIES' => 'Le mie 10 migliori Opportunità',
    'NTC_REMOVE_OPP_CONFIRMATION' => 'Sei sicuro di voler eliminare il contatto da questa opportunità?',
    'OPPORTUNITY_REMOVE_PROJECT_CONFIRM' => 'Sei sicuro di rimuovere questa opportunità dal progetto?',
    'LBL_DEFAULT_SUBPANEL_TITLE' => 'Opportunità',
    'LBL_ACTIVITIES_SUBPANEL_TITLE' => 'Attività',
    'LBL_HISTORY_SUBPANEL_TITLE' => 'Cronologia',
    'LBL_RAW_AMOUNT' => 'Importo Riga',
    'LBL_LEADS_SUBPANEL_TITLE' => 'Lead',
    'LBL_CONTACTS_SUBPANEL_TITLE' => 'Contatti',
    'LBL_DOCUMENTS_SUBPANEL_TITLE' => 'Documenti',
    'LBL_PROJECTS_SUBPANEL_TITLE' => 'Progetti',
    'LBL_ASSIGNED_TO_NAME' => 'Assegnato a:',
    'LBL_LIST_ASSIGNED_TO_NAME' => 'Assegnato a',
    'LBL_LIST_SALES_STAGE' => 'Fase di Vendita',
    'LBL_MY_CLOSED_OPPORTUNITIES' => 'Le mie Oppurtunità Chiuse',
    'LBL_TOTAL_OPPORTUNITIES' => 'Totale Oppurtunità',
    'LBL_CLOSED_WON_OPPORTUNITIES' => 'Oppurtunità Chiuse Vinte',
    'LBL_ASSIGNED_TO_ID' => 'Assegnato a:',
    'LBL_CREATED_ID' => 'Creato da ID',
    'LBL_MODIFIED_ID' => 'Modificato da ID',
    'LBL_MODIFIED_NAME' => 'Modificato da Nome Utente',
    'LBL_CREATED_USER' => 'Utente Creato',
    'LBL_MODIFIED_USER' => 'Utente Modificato',
    'LBL_CAMPAIGN_OPPORTUNITY' => 'Campagne',
    'LBL_PROJECT_SUBPANEL_TITLE' => 'Progetti',
    'LABEL_PANEL_ASSIGNMENT' => 'Assegnazione',
    'LNK_IMPORT_OPPORTUNITIES' => 'Importa Oppurtunità',
    'LBL_EDITLAYOUT' => 'Modifica Layout' /*for 508 compliance fix*/,
    //For export labels
    'LBL_EXPORT_CAMPAIGN_ID' => 'ID Campagna',
    'LBL_OPPORTUNITY_TYPE' => 'Tipo Opportunità',
    'LBL_EXPORT_ASSIGNED_USER_NAME' => 'Nome Utente Assegnato',
    'LBL_EXPORT_ASSIGNED_USER_ID' => 'ID Utente Assegnato',
    'LBL_EXPORT_MODIFIED_USER_ID' => 'Modificato da ID',
    'LBL_EXPORT_CREATED_BY' => 'Creato da ID',
    'LBL_EXPORT_NAME' => 'Nome',
    // SNIP
    'LBL_CONTACT_HISTORY_SUBPANEL_TITLE' => 'Email Contatti Relazionati',
    'LBL_FILENAME' => 'Allegato',
    'LBL_PRIMARY_QUOTE_ID' => 'Offerta Primaria',
    'LBL_CONTRACTS' => 'Contratti',
    'LBL_CONTRACTS_SUBPANEL_TITLE' => 'Contratti',
    'LBL_PRODUCTS' => 'Prodotti',
    'LBL_RLI' => 'Elemento dell´Opportunità',
    'LNK_OPPORTUNITY_REPORTS' => 'Visualizza Report Opportunità',
    'LBL_QUOTES_SUBPANEL_TITLE' => 'Offerte',
    'LBL_TEAM_ID' => 'ID Gruppo',
    'LBL_TIMEPERIODS' => 'Archi Temporali',
    'LBL_TIMEPERIOD_ID' => 'ID Arco Temporale',
    'LBL_COMMITTED' => 'Confermato',
    'LBL_FORECAST' => 'Includi nella Previsione',
    'LBL_COMMIT_STAGE' => 'Fase di Conferma',
    'LBL_COMMIT_STAGE_FORECAST' => 'Fase di Previsione',
    'LBL_WORKSHEET' => 'Matrice',
    'LBL_PURCHASED_LINE_ITEMS' => 'Prodotti acquistati',

    // KPI Metrics
    'LBL_ORGANIZE' => 'Organizza',
    'LBL_CREATE_NEW' => 'Crea Nuovo',
    'LBL_MANAGE' => 'Gestisci',
    'LBL_SEE_DETAILS' => 'Mostra Dettagli',
    'LBL_HIDE_NEW' => 'Nascondi',

    'LBL_FORECASTED_LIKELY' => 'Probabilmente previsto',
    'LBL_LOST' => 'Persa',
    'LBL_RENEWAL' => 'Rinnovo',
    'LBL_RENEWAL_OPPORTUNITIES' => 'Opportunità di rinnovo',
    'LBL_RENEWAL_PARENT' => 'Opportunità padre',
    'LBL_PARENT_RENEWAL_OPPORTUNITY_ID' => 'ID padre rinnovo',
    'LBL_MONTH_YEAR_RENEWAL' => '{{month}}, {{year}}',

    'LBL_WIDGET_SALES_STAGE' => 'Fase di Vendita',
    'LBL_WIDGET_DATE_CLOSED' => 'Data di chiusura prevista',
    'LBL_WIDGET_AMOUNT' => 'Importo',

    'TPL_RLI_CREATE' => 'Un´Opportunità deve avere una Elemento dell´Opportunità associato. <a href="javascript:void(0);" id="createRLI">Creare un Elemento dell´Opportunità</a>.',
    'TPL_RLI_CREATE_LINK_TEXT' => 'Crea un Elemento dell´Opportunità.',
    'LBL_PRODUCTS_SUBPANEL_TITLE' => 'Prodotti',
    'LBL_RLI_SUBPANEL_TITLE' => 'Elementi dell´Opportunità',

    'LBL_TOTAL_RLIS' => '# Elementi dell´Opportunità Totali',
    'LBL_CLOSED_RLIS' => '# Elementi dell´Opportunità Chiusi',
    'LBL_CLOSED_WON_RLIS' => '# di voci vinte chiuse',
    'LBL_SERVICE_OPEN_FLEX_DURATION_RLIS' => 'Numero di prodotti per il ricavo con durata flessibile per servizio aperto',
    'NOTICE_NO_DELETE_CLOSED_RLIS' => 'Non si possono cancellare Opportunità con Elementi dell´Opportunità chiusi',
    'WARNING_NO_DELETE_CLOSED_SELECTED' => 'Uno o più record tra quelli selezionati contiene Elementi dell´Opportunità chiusi e non può essere cancellato.',
    'LBL_INCLUDED_RLIS' => '# of Included Revenue Line Items',
    'LBL_UPDATE_OPPORTUNITIES_RLIS' => 'Aggiorna apertura',
    'LBL_CASCADE_RLI_EDIT' => 'Aggiorna elementi ricavi aperti',
    'LBL_CASCADE_RLI_CREATE' => 'Imposta tra elementi di ricavi',
    'LBL_SERVICE_START_DATE_INVALID' => 'La data di inizio del servizio non può essere impostata oltre la data di fine del servizio di qualsiasi voce di ricavo aggiuntiva aperta.',

    'LBL_QUOTE_SUBPANEL_TITLE' => 'Offerte',
    'LBL_FILTER_OPPORTUNITY_TEMPLATE' => 'Opportunità per account dinamico',
    'LBL_TOP_10_OPP' => 'Le 10 migliori opportunità aperte',
    'LBL_DASHLET_MY_ACTIVE_OPP' => 'Dashlet: le mie opportunità attive',
    'LBL_MY_ACTIVE_OPP' => 'Le mie opportunità attive',


    // Config
    'LBL_OPPS_CONFIG_VIEW_BY_LABEL' => 'Gerarchia Opportunità',
    'LBL_OPPS_CONFIG_VIEW_BY_DATE_ROLLUP' => 'Imposta la Data di Chiusura Prevista nelle seguenti Opportunità che sarà la prima o l´ultima data di chiusura degli Elementi dell´Opportunità esistenti.',

    //Dashlet
    'LBL_PIPELINE_TOTAL_IS' => 'Pipeline Totale',

    'LBL_OPPORTUNITY_ROLE' => 'Ruolo Opportunità',
    'LBL_NOTES_SUBPANEL_TITLE' => 'Note',
    'LBL_TAB_OPPORTUNITY' => 'Revisione {{module}}',

    // Help Text
    'LBL_OPPS_CONFIG_ALERT' => 'Cliccando Conferma, cancellerai TUTTI i dati delle previsioni e modificherai la Vista Opportunità. Se non  è questo che intendi fare, clicca annulla per ritornare alle impostazioni precedenti.',
    'LBL_OPPS_CONFIG_ALERT_TO_OPPS' =>
        'Facendo clic su Conferma, sarà possibile cancellare TUTTI i dati sulle Previsioni e modificare la vista Opportunità. '
        . 'Saranno, inoltre, disabilitate TUTTE le definizioni dei processi con un modulo obiettivo di Elementi dell&#39;opportunità. '
        . 'Se questo non è ciò che si intendeva, fare clic su Annulla per tornare alle impostazioni precedenti.',
    'LBL_OPPS_CONFIG_SALES_STAGE_1a' => 'Se tutte gli Elementi dell´Opportunità sono chiusi o almeno uno è stato vinto,',
    'LBL_OPPS_CONFIG_SALES_STAGE_1b' => 'la Fase di Vendita dell´Opportunità è "Chiuso Vinto".',
    'LBL_OPPS_CONFIG_SALES_STAGE_2a' => 'Se tutti gli Elementi dell´Opportunità sono in Fase di vendita "Chiuso Perso",',
    'LBL_OPPS_CONFIG_SALES_STAGE_2b' => 'la Fase di Vendita dell´Opportunità è impostata in "Chiuso Perso".',
    'LBL_OPPS_CONFIG_SALES_STAGE_3a' => 'Se ci sono Elementi dell´Opportunità ancora aperti,',
    'LBL_OPPS_CONFIG_SALES_STAGE_3b' => 'l´Opportunità verrà contrassegnata con la Fase di Vendita meno avanzata.',

// BEGIN ENT/ULT

    // Opps Config - View By Opportunities
    'LBL_HELP_CONFIG_OPPS' => 'Dopo aver avviato questa modifica, le note di riepilogo degli Elementi dell´Opportunità saranno costruiti in background. Quando le note sono complete e disponibili, sarà inviata una notifica all´indirizzo email del tuo profilo utente. Se la tua istanza è stata impostata per il modulo {{forecasts_module}}, Sugar ti invierà una notifica quando i records del modulo {{module_name}} saranno sincronizzati al modulo {{forecasts_module}} e disponibili per nuove {{forecasts_module}}. Si prega di notare che la tua istanza deve essere configurata per inviare email via Admin > Impostazioni Email in modo tale che le notifiche vengano inviate.',

    // Opps Config - View By Opportunities And RLIs
    'LBL_HELP_CONFIG_RLIS' => 'Dopo aver avviato questa modifica, gli Elementi dell´Opportunità saranno creati per ogni {{module_name}} esistente in background. Quando gli Elementi dell´Opportunità sono completi e disponibili, sarà inviata una notifica all´indirizzo email del tuo profilo utente. Si prega di notare che la tua istanza deve essere configurata per inviare email via Admin > Impostazioni Email in modo tale che le notifiche vengano inviate.',
    // List View Help Text
    'LBL_HELP_RECORDS' => 'Il modulo {{plural_module_name}} ti consente di monitorare le vendite individuali dall&#39;inizio alla fine. Ogni record {{module_name}} rappresenta una potenziale vendita e include dati di vendita rilevanti nonché relativi ad altri record importanti come {{quotes_module}}, {{contacts_module}}, ecc. Un {{module_name}} in genere avanzerà attraverso diverse fasi di vendita finché non sarà contrassegnato come "Chiuso vinto" o "Chiuso perso". {{plural_module_name}} può essere ulteriormente sfruttato utilizzando il modulo {{forecasts_singular_module}} di Sugar per comprendere e prevedere le tendenze delle vendite e concentrare il lavoro per raggiungere il budget di vendita.',

    // Record View Help Text
    'LBL_HELP_RECORD' => 'Il modulo {{plural_module_name}} ti consente di monitorare le singole vendite e gli elementi appartenenti a tali vendite dall&#39;inizio alla fine. Ogni record {{module_name}} rappresenta una potenziale vendita e include dati di vendita rilevanti nonché relativi ad altri record importanti come {{quotes_module}}, {{contacts_module}}, ecc.

- Modifica i campi di questo record facendo clic su un singolo campo o sul pulsante Modifica.
- Visualizza o modifica i collegamenti ad altri record nei pannelli secondari spostando il riquadro in basso a sinistra su "Visualizzazione dati".
- Crea e visualizza i commenti degli utenti e registra la cronologia delle modifiche in {{activitystream_singular_module}} attivando il riquadro in basso a sinistra su "Streaming di attività".
- Segui o aggiungi ai preferiti questo record utilizzando le icone a destra del nome del record.
- Ulteriori azioni sono disponibili nel menu a discesa Azioni a destra del pulsante Modifica.',

    // Create View Help Text
    'LBL_HELP_CREATE' => 'Il modulo {{plural_module_name}} ti consente di monitorare le singole vendite e gli elementi appartenenti a tali vendite dall&#39;inizio alla fine. Ogni record {{module_name}} rappresenta una potenziale vendita e include dati di vendita rilevanti nonché relativi ad altri record importanti come {{quotes_module}}, {{contacts_module}}, ecc.

Per creare un {{module_name}}:
1. Fornire i valori per i campi come desiderato.
 - I campi contrassegnati come "Obbligatorio" devono essere completati prima del salvataggio.
 - Fare clic su "Altri dettagli" per visualizzare campi aggiuntivi, se necessario.
2. Fare clic su "Salva" per finalizzare il nuovo record e tornare alla pagina precedente.',

// END ENT/ULT

    //Marketo
    'LBL_MKTO_SYNC' => 'Sincronizza con Marketo®',
    'LBL_MKTO_ID' => 'ID Lead Marketo',

    'LBL_DASHLET_TOP10_SALES_OPPORTUNITIES_NAME' => 'Top 10 Opportunità',
    'LBL_TOP10_OPPORTUNITIES_CHART_DESC' => 'Visualizza le 10 migliori Opportunità in un grafico a bolle.',
    'LBL_TOP10_OPPORTUNITIES_MY_OPP' => 'Le mie Opportunità',
    'LBL_TOP10_OPPORTUNITIES_MY_TEAMS_OPP' => "Le Opportunità del mio Gruppo",

    'LBL_PIPELINE_ERR_CLOSED_SALES_STAGE' => 'Impossibile modificare {{fieldName}} in quanto in questo {{moduleSingular}} non vi sono prodotti aperti.',
    'TPL_ACTIVITY_TIMELINE_DASHLET' => 'Tempistica opportunità',

    'LBL_CASCADE_SERVICE_WARNING' => ' non può essere impostato su nessuna di queste voci di ricavi perché non sono servizi. Vuoi procedere con la creazione?',
    'LBL_CASCADE_DURATION_WARNING' => ' non può essere impostato su nessuno di questi elementi riga ricavi perché le loro durate sono bloccate. Vuoi procedere con la creazione?',

    // AI Predict
    'LBL_AI_OPPORTUNITY_CLOSE_PREDICTION_NAME' => 'Previsione chiusura opportunità',
    'LBL_AI_OPPORTUNITY_CLOSE_PREDICTION_DESC' => 'Visualizza dettagli di previsione per un&#39;opportunità specifica',
    'LBL_AI_WINRATE' => 'Tasso di vincita',
    'LBL_AI_WONOPP' => 'Opportunità vinte',
    'LBL_AI_CLOSINGTIME' => 'Ora di chiusura',
    'LBL_AI_CLOSEDOPP' => 'Opportunità chiuse',
    'LBL_AI_LEADTIMESPAN' => 'Tempo tra la creazione e la chiusura vinta dell&#39;opportunità',
];
