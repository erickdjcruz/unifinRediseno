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
    'LBL_HOMEPAGE_TITLE' => 'Mijn slimme gids-sjablonen',
    'LBL_LIST_FORM_TITLE' => 'Lijst met slimme gids-sjablonen',
    'LNK_IMPORT_CUSTOMER_JOURNEY_TEMPLATES' => 'Slimme gids-sjablonen importeren',
    'LBL_MODULE_TITLE' => 'Slimme gids-sjablonen',
    'LBL_MODULE_NAME' => 'Slimme gids-sjablonen',
    'LBL_NEW_FORM_TITLE' => 'Nieuw slimme gids-sjabloon',
    'LBL_REMOVE' => 'Verwijderen',
    'LBL_SEARCH_FORM_TITLE' => 'Slimme gids-sjablonen zoeken',
    'LBL_TYPE' => 'Type',
    'LNK_LIST' => 'Slimme gids-sjablonen',
    'LNK_NEW_RECORD' => 'Slimme gids-sjabloon maken',
    'LBL_COPIES' => 'Kopieën',
    'LBL_COPIED_TEMPLATE' => 'Gekopieerd sjabloon',
    'LBL_IMPORT_TEMPLATES_BUTTON_LABEL' => 'Sjablonen importeren',
    'LBL_IMPORT_TEMPLATES_SUCCESS_MESSAGE' => 'Sjablonen zijn geïmporteerd.',
    'LBL_RESAVE_TEMPLATES_BUTTON_LABEL' => 'Sjablonen opnieuw opslaan',
    'LBL_RESAVE_TEMPLATES_SUCCESS_MESSAGE' => 'Sjablonen zijn opnieuw opgeslagen.',
    'LNK_VIEW_RECORDS' => 'Slimme gids-sjablonen bekijken',
    'LNK_DRI_WORKFLOW_TEMPLATE_LIST' => 'Bekijk slimme gids-sjablonen',
    'LBL_AVAILABLE_MODULES' => 'Beschikbare modules',
    'LBL_CANCEL_ACTION' => 'Actie annuleren',
    'LBL_NOT_APPLICABLE_ACTION' => 'Actie is niet van toepassing',
    'LBL_POINTS' => 'Punten',
    'LBL_RELATED_ACTIVITIES' => 'Gerelateerde activiteiten',
    'LBL_ACTIVE' => 'Actief',
    'LBL_ASSIGNEE_RULE' => 'Regel voor toewijzing',
    'LBL_TARGET_ASSIGNEE' => 'Toegewezen target',
    'LBL_STAGE_NUMBERS' => 'Fasenummering',
    'LBL_EXPORT_BUTTON_LABEL' => 'Exporteren',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_BUTTON_LABEL' => 'Importeren',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_TEXT' => 'Automatisch een nieuw record van een slimme gids-sjabloon maken/updaten door een *.json-bestand uit uw bestandssysteem te importeren.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_CREATE_SUCCESS' => 'Sjabloon <a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a> is gemaakt.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_UPDATE_SUCCESS' => 'Sjabloon <a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a> is geüpdatet.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_DUPLICATE_NAME_ERROR' => 'Importeren is mislukt. Er bestaat al een sjabloon met de naam <a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a>. Wijzig de naam van het geïmporteerde record en probeer het opnieuw, of gebruik &#39;Kopiëren&#39; om een dubbele slimme gids-sjabloon te maken.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_UPDATE_CONFIRM' => 'Er bestaat al een sjabloon met deze ID. Als u het bestaande sjabloon wilt updaten, klikt u op <b>Bevestigen</b>. Als u wilt afsluiten zonder het bestaande sjabloon te wijzigen, klikt u op <b>Annuleren</b>.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_TEMPLATE_DELETED' => 'Het sjabloon dat u probeert te importeren, is in de huidige instantie verwijderd.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_EMPTY_WARNING' => 'Selecteer een geldig *.json-bestand.',
    'LBL_CHECKING_IMPORT_UPLOAD' => 'Valideren',
    'LBL_IMPORTING_TEMPLATE' => 'Importeren',
    'LBL_DISABLED_STAGE_ACTIONS' => 'Uitgeschakelde fase-acties',
    'LBL_DISABLED_ACTIVITY_ACTIONS' => 'Uitgeschakelde activiteitenacties',
    'LBL_FORMS' => 'Formulieren',
    'LBL_ACTIVE_LIMIT' => 'Limiet actieve slimme gids(en)',
    'LBL_WEB_HOOKS' => 'Webhooks',
    'LBL_START_NEXT_JOURNEY_ACTIVITIES' => 'Startactiviteit van de volgende slimme gids',
    'LBL_START_NEXT_JOURNEY_STAGES' => 'Fasekoppeling starten van de volgende slimme gids',
    'LBL_SMART_GUIDE_ACCESSIBLE' => 'De modules kiezen waar toegang is tot de slimme gids',
    'LBL_SMART_GUIDE_MODIFY_ACTIONS' => 'Op een fase kunt u meer activiteiten toevoegen of verwijderen. Schakel de acties uit waartoe u de gebruiker geen toegang wilt geven in deze slimme gids',
    'LBL_SMART_GUIDE_DISABLE_ACTIONS' => 'Op een activiteit kunt u meer activiteiten toevoegen als subactiviteiten. Schakel de acties uit waartoe u de gebruiker geen toegang wilt geven in deze slimme gids',
    'LBL_SMART_GUIDE_ACTIVATES' => 'Hoeveel van deze slimme gids kunnen tegelijkertijd actief zijn op een record',
    'LBL_SMART_GUIDE_TARGET_ASSIGNEE' => 'Als de optie Toegewezen target = Bovenliggende toegewezene ingeschakeld is, wanneer de gebruiker die is &#39;Toegewezen aan&#39; wordt gewijzigd op een bovenliggende, zullen de gebruikers die zijn &#39;Toegewezen aan&#39; ook automatisch veranderen in de slimme gidsen, fasen en activiteiten. Houd er rekening mee dat de instellingen van de Toegewezen target op activiteitssjablonen voorrang hebben op de sjabloon voor slimme gidsen',
    'LBL_SMART_GUIDE_USER_ASSIGNED' => 'Wanneer moet een gebruiker aan de activiteiten toegewezen worden',
    'LBL_SMART_GUIDE_ACTIVITIES_ASSIGNED' => 'Aan wie moeten de activiteiten toegewezen worden',
    'LBL_SMART_GUIDE_STAGE_NUMBERS' => 'Met deze schakelaar kunt u automatische fasenummering weergeven of verbergen.',
    'CJ_FORMS_LBL_PARENT_NAME' => 'Sjabloon van slimme gids/fase/activiteit',
];
