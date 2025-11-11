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
    'LBL_HOMEPAGE_TITLE' => 'I miei modelli della Guida Intelligente',
    'LBL_LIST_FORM_TITLE' => 'Elenco modelli della Guida Intelligente',
    'LNK_IMPORT_CUSTOMER_JOURNEY_TEMPLATES' => 'Importa modelli della Guida Intelligente',
    'LBL_MODULE_TITLE' => 'Modelli della Guida Intelligente',
    'LBL_MODULE_NAME' => 'Modelli della Guida Intelligente',
    'LBL_NEW_FORM_TITLE' => 'Nuovo modello della Guida Intelligente',
    'LBL_REMOVE' => 'Rimuovi',
    'LBL_SEARCH_FORM_TITLE' => 'Cerca i modelli della Guida Intelligente',
    'LBL_TYPE' => 'Tipo',
    'LNK_LIST' => 'Modelli della Guida Intelligente',
    'LNK_NEW_RECORD' => 'Crea modello della Guida Intelligente',
    'LBL_COPIES' => 'Copie',
    'LBL_COPIED_TEMPLATE' => 'Modello copiato',
    'LBL_IMPORT_TEMPLATES_BUTTON_LABEL' => 'Importa modelli',
    'LBL_IMPORT_TEMPLATES_SUCCESS_MESSAGE' => 'I modelli sono stati importati.',
    'LBL_RESAVE_TEMPLATES_BUTTON_LABEL' => 'Salva di nuovo i modelli',
    'LBL_RESAVE_TEMPLATES_SUCCESS_MESSAGE' => 'I modelli sono stati salvati di nuovo.',
    'LNK_VIEW_RECORDS' => 'Visualizza modelli della Guida Intelligente',
    'LNK_DRI_WORKFLOW_TEMPLATE_LIST' => 'Visualizza i modelli della Guida Intelligente',
    'LBL_AVAILABLE_MODULES' => 'Moduli disponibili',
    'LBL_CANCEL_ACTION' => 'Annulla azione',
    'LBL_NOT_APPLICABLE_ACTION' => 'Azione non applicabile',
    'LBL_POINTS' => 'Punti',
    'LBL_RELATED_ACTIVITIES' => 'Attività correlate',
    'LBL_ACTIVE' => 'Attivo',
    'LBL_ASSIGNEE_RULE' => 'Regola dell&#39;assegnatario',
    'LBL_TARGET_ASSIGNEE' => 'Assegnatario di destinazione',
    'LBL_STAGE_NUMBERS' => 'Numerazione fasi',
    'LBL_EXPORT_BUTTON_LABEL' => 'Esporta',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_BUTTON_LABEL' => 'Importa',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_TEXT' => 'Crea/Aggiorna automaticamente un nuovo record Modello della Guida Intelligente importando un file *.json dal tuo file system.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_CREATE_SUCCESS' => 'Il modello <a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a> è stato creato.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_UPDATE_SUCCESS' => 'Il modello <a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a> è stato aggiornato.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_DUPLICATE_NAME_ERROR' => 'Importazione non riuscita. Un modello chiamato "<a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a>" esiste già. Modificare il nome del record importato e riprovare o utilizzare "Copia" per creare un modello della Guida Intelligente duplicato.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_UPDATE_CONFIRM' => 'Esiste già un modello con questo ID. Per aggiornare il modello esistente, fare clic su <b>Conferma</b>. Per uscire senza apportare modifiche al modello esistente, fare clic su <b>Annulla</b>.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_TEMPLATE_DELETED' => 'Il modello che si sta tentando di importare è eliminato nell&#39;istanza corrente.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_EMPTY_WARNING' => 'Selezionare un file *.json valido.',
    'LBL_CHECKING_IMPORT_UPLOAD' => 'Convalida',
    'LBL_IMPORTING_TEMPLATE' => 'Importazione',
    'LBL_DISABLED_STAGE_ACTIONS' => 'Azioni di fase disabilitate',
    'LBL_DISABLED_ACTIVITY_ACTIONS' => 'Azioni attività disabilitate',
    'LBL_FORMS' => 'Moduli',
    'LBL_ACTIVE_LIMIT' => 'Limite di guide intelligenti attive',
    'LBL_WEB_HOOKS' => 'Web hook',
    'LBL_START_NEXT_JOURNEY_ACTIVITIES' => 'Prossima attività di inizio della Guida Intelligente',
    'LBL_START_NEXT_JOURNEY_STAGES' => 'Avvia il collegamento alla fase della Guida Intelligente successiva',
    'LBL_SMART_GUIDE_ACCESSIBLE' => 'Scegli i moduli in cui la Guida Intelligente dovrebbe essere accessibile',
    'LBL_SMART_GUIDE_MODIFY_ACTIONS' => 'In una fase puoi aggiungere altre attività o eliminarle. Disabilita le azioni a cui non vuoi che l&#39;utente abbia accesso in questa Guida Intelligente',
    'LBL_SMART_GUIDE_DISABLE_ACTIONS' => 'In un&#39;attività puoi aggiungere altre attività come attività secondarie. Disabilita le azioni a cui non vuoi che l&#39;utente abbia accesso in questa Guida Intelligente',
    'LBL_SMART_GUIDE_ACTIVATES' => 'Quante di questa Guida Intelligente possono essere attive su un record contemporaneamente',
    'LBL_SMART_GUIDE_TARGET_ASSIGNEE' => 'Se si seleziona Se Assegnatario di Destinazione = Assegnatario padre, quando l&#39;utente "Assegnato a" viene modificato in un genitore, gli utenti "Assegnato a" cambieranno automaticamente anche nelle Guide Intelligenti, fasi e attività. Si noti che le impostazioni di Assegnatario di Destinazione nei modelli di attività hanno la precedenza sul modello della Guida Intelligente.',
    'LBL_SMART_GUIDE_USER_ASSIGNED' => 'Quando un utente deve essere assegnato alle Attività',
    'LBL_SMART_GUIDE_ACTIVITIES_ASSIGNED' => 'A chi devono essere assegnate le Attività',
    'LBL_SMART_GUIDE_STAGE_NUMBERS' => 'Questo interruttore consente di mostrare o nascondere la numerazione automatica delle fasi.',
    'CJ_FORMS_LBL_PARENT_NAME' => 'Guida Intelligente/Fase/Modello di attività',
];
