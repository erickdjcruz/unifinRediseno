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
    'LBL_HOMEPAGE_TITLE' => 'Meine Smart Guide Vorlagen',
    'LBL_LIST_FORM_TITLE' => 'Smart Guide Vorlagenliste',
    'LNK_IMPORT_CUSTOMER_JOURNEY_TEMPLATES' => 'Smart Guide Vorlagen importieren',
    'LBL_MODULE_TITLE' => 'Smart Guide Vorlagen',
    'LBL_MODULE_NAME' => 'Smart Guide Vorlagen',
    'LBL_NEW_FORM_TITLE' => 'Neue Smart Guide Vorlage',
    'LBL_REMOVE' => 'Entfernen',
    'LBL_SEARCH_FORM_TITLE' => 'Smart Guide Vorlagen durchsuchen',
    'LBL_TYPE' => 'Typ',
    'LNK_LIST' => 'Smart Guide Vorlagen',
    'LNK_NEW_RECORD' => 'Smart Guide Vorlage erstellen',
    'LBL_COPIES' => 'Kopien',
    'LBL_COPIED_TEMPLATE' => 'Kopierte Vorlage',
    'LBL_IMPORT_TEMPLATES_BUTTON_LABEL' => 'Vorlagen importieren',
    'LBL_IMPORT_TEMPLATES_SUCCESS_MESSAGE' => 'Vorlagen wurden importiert.',
    'LBL_RESAVE_TEMPLATES_BUTTON_LABEL' => 'Vorlagen erneut speichern',
    'LBL_RESAVE_TEMPLATES_SUCCESS_MESSAGE' => 'Vorlagen wurden erneut gespeichert.',
    'LNK_VIEW_RECORDS' => 'Smart Guide Vorlagen ansehen',
    'LNK_DRI_WORKFLOW_TEMPLATE_LIST' => 'Smart Guide Vorlagen ansehen',
    'LBL_AVAILABLE_MODULES' => 'Verfügbare Module',
    'LBL_CANCEL_ACTION' => 'Aktion abbrechen',
    'LBL_NOT_APPLICABLE_ACTION' => 'Nicht anwendbare Aktion',
    'LBL_POINTS' => 'Punkte',
    'LBL_RELATED_ACTIVITIES' => 'Verwandte Aktivitäten',
    'LBL_ACTIVE' => 'Aktiv',
    'LBL_ASSIGNEE_RULE' => 'Regel für Zugewiesene',
    'LBL_TARGET_ASSIGNEE' => 'Ziel-Zugewiesener',
    'LBL_STAGE_NUMBERS' => 'Stufennummerierung',
    'LBL_EXPORT_BUTTON_LABEL' => 'Exportieren',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_BUTTON_LABEL' => 'Importieren',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_TEXT' => 'Automatisches Erstellen/Aktualisieren eines neuen Smart Guide Vorlagen-Datensatzes, indem eine *.json-Datei aus Ihrem Dateisystem importiert wird.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_CREATE_SUCCESS' => 'Vorlage <a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a> wurde erfolgreich erstellt.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_UPDATE_SUCCESS' => 'Vorlage <a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a> wurde erfolgreich aktualisiert.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_DUPLICATE_NAME_ERROR' => 'Import fehlgeschlagen. Eine Vorlage mit dem Namen "<a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a>" ist bereits vorhanden. Bitte ändern Sie den Namen des importierten Datensatzes und versuchen Sie es erneut oder verwenden Sie "Kopieren", um eine doppelte Smart Guide-Vorlage zu erstellen.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_UPDATE_CONFIRM' => 'Eine Vorlage mit dieser ID ist bereits vorhanden. Um die vorhandene Vorlage zu aktualisieren, klicken Sie auf <b>Bestätigen</b>. Um den Vorgang zu beenden, ohne Änderungen an der vorhandenen Vorlage vorzunehmen, klicken Sie auf <b>Abbrechen</b>.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_TEMPLATE_DELETED' => 'Die Vorlage, die Sie importieren möchten, wurde in der aktuellen Instanz gelöscht.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_EMPTY_WARNING' => 'Bitte wählen Sie eine gültige *.json-Datei.',
    'LBL_CHECKING_IMPORT_UPLOAD' => 'Wird überprüft',
    'LBL_IMPORTING_TEMPLATE' => 'Wird importiert',
    'LBL_DISABLED_STAGE_ACTIONS' => 'Deaktivierte Stufenaktionen',
    'LBL_DISABLED_ACTIVITY_ACTIONS' => 'Deaktivierte Stufenaktionen',
    'LBL_FORMS' => 'Formulare',
    'LBL_ACTIVE_LIMIT' => 'Grenzwert für aktive(n) Smart Guide(s)',
    'LBL_WEB_HOOKS' => 'Web-Hooks',
    'LBL_START_NEXT_JOURNEY_ACTIVITIES' => 'Nächste Smart Guide Startaktivität',
    'LBL_START_NEXT_JOURNEY_STAGES' => 'Nächsten Smart Guide Stufenlink starten',
    'LBL_SMART_GUIDE_ACCESSIBLE' => 'Wählen Sie die Module aus, auf die der Smart Guide zugreifen soll',
    'LBL_SMART_GUIDE_MODIFY_ACTIONS' => 'Auf einer Stufe können Sie weitere Aktivitäten hinzufügen oder löschen. Deaktivieren Sie die Aktionen, auf die der Benutzer in diesem Smart Guide keinen Zugriff haben soll',
    'LBL_SMART_GUIDE_DISABLE_ACTIONS' => 'Für eine Aktivität können Sie weitere Aktivitäten als Unteraktivitäten hinzufügen. Deaktivieren Sie die Aktionen, auf die der Benutzer in diesem Smart Guide keinen Zugriff haben soll',
    'LBL_SMART_GUIDE_ACTIVATES' => 'Wie viele dieser Smart Guides, die gleichzeitig in einem Datensatz aktiv sein können',
    'LBL_SMART_GUIDE_TARGET_ASSIGNEE' => 'Wenn diese Option aktiviert ist: Wenn Ziel-Zugewiesener = Übergeordneter Zugewiesener, wenn der Benutzer "Zugewiesen an" auf einem übergeordneten Benutzer geändert wird, ändern sich automatisch auch die Benutzer "Zugewiesen an" in den intelligenten Führungslinien, Phasen und Aktivitäten. Beachten Sie, dass die Einstellungen des Ziel-Zugewiesenen in Aktivitätsvorlagen Vorrang vor der Smart Guide Vorlage haben',
    'LBL_SMART_GUIDE_USER_ASSIGNED' => 'Wann sollte ein Benutzer den Aktivitäten zugewiesen werden',
    'LBL_SMART_GUIDE_ACTIVITIES_ASSIGNED' => 'Wer sollte den Aktivitäten zugewiesen werden',
    'LBL_SMART_GUIDE_STAGE_NUMBERS' => 'Mit diesem Schalter können Sie die automatische Stufennummerierung ein- oder ausblenden.',
    'CJ_FORMS_LBL_PARENT_NAME' => 'Smart Guide/Stufe/Aktivitätsvorlage',
];
