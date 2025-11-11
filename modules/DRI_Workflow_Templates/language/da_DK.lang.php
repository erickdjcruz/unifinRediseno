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
    'LBL_HOMEPAGE_TITLE' => 'Mine Smart Guide-skabeloner',
    'LBL_LIST_FORM_TITLE' => 'Smart Guide-skabelonliste',
    'LNK_IMPORT_CUSTOMER_JOURNEY_TEMPLATES' => 'Importér Smart Guide-skabeloner',
    'LBL_MODULE_TITLE' => 'Smart Guide-skabeloner',
    'LBL_MODULE_NAME' => 'Smart Guide-skabeloner',
    'LBL_NEW_FORM_TITLE' => 'Ny Smart Guide-skabelon',
    'LBL_REMOVE' => 'Fjern',
    'LBL_SEARCH_FORM_TITLE' => 'Søg blandt Smart Guide-skabeloner',
    'LBL_TYPE' => 'Type',
    'LNK_LIST' => 'Smart Guide-skabeloner',
    'LNK_NEW_RECORD' => 'Opret Smart Guide-skabelon',
    'LBL_COPIES' => 'Kopier',
    'LBL_COPIED_TEMPLATE' => 'Kopieret skabelon',
    'LBL_IMPORT_TEMPLATES_BUTTON_LABEL' => 'Importér skabeloner',
    'LBL_IMPORT_TEMPLATES_SUCCESS_MESSAGE' => 'Skabeloner er blevet importeret.',
    'LBL_RESAVE_TEMPLATES_BUTTON_LABEL' => 'Gem skabeloner igen',
    'LBL_RESAVE_TEMPLATES_SUCCESS_MESSAGE' => 'Skabeloner er blevet gemt igen.',
    'LNK_VIEW_RECORDS' => 'Vis Smart Guide-skabeloner',
    'LNK_DRI_WORKFLOW_TEMPLATE_LIST' => 'Vis Smart Guide-skabeloner',
    'LBL_AVAILABLE_MODULES' => 'Tilgængelige moduler',
    'LBL_CANCEL_ACTION' => 'Annuller handling',
    'LBL_NOT_APPLICABLE_ACTION' => 'Ikke relevant handling',
    'LBL_POINTS' => 'Punkter',
    'LBL_RELATED_ACTIVITIES' => 'Relaterede aktiviteter',
    'LBL_ACTIVE' => 'Aktiv',
    'LBL_ASSIGNEE_RULE' => 'Regel for modtager',
    'LBL_TARGET_ASSIGNEE' => 'Målmodtager',
    'LBL_STAGE_NUMBERS' => 'Fasenummerering',
    'LBL_EXPORT_BUTTON_LABEL' => 'Eksportér',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_BUTTON_LABEL' => 'Importér',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_TEXT' => 'Opret/opdater automatisk en ny post for e-mailskabelon ved at importere en *.json-fil fra dit filsystem.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_CREATE_SUCCESS' => 'Skabelonen <a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a> er oprettet.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_UPDATE_SUCCESS' => 'Skabelonen <a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a> er opdateret.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_DUPLICATE_NAME_ERROR' => 'Importen mislykkedes. Der findes allerede en skabelon med navnet "<a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a>". Skift navnet på den importerede post og prøv igen, eller brug "Kopier" til at oprette en duplikeret Smart Guide-skabelon.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_UPDATE_CONFIRM' => 'Der findes allerede en skabelon med dette id. Hvis du vil opdatere den eksisterende skabelon, skal du klikke på <b>Bekræft</b>. Hvis du vil afslutte uden at foretage ændringer i den eksisterende skabelon, skal du klikke på <b>Annuller</b>.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_TEMPLATE_DELETED' => 'Den skabelon, du forsøger at importere, slettes i den aktuelle hændelse.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_EMPTY_WARNING' => 'Vælg en gyldig *.json-fil.',
    'LBL_CHECKING_IMPORT_UPLOAD' => 'Validerer',
    'LBL_IMPORTING_TEMPLATE' => 'Importerer',
    'LBL_DISABLED_STAGE_ACTIONS' => 'Deaktiverede fasehandlinger',
    'LBL_DISABLED_ACTIVITY_ACTIONS' => 'Deaktiverede aktivitetshandlinger',
    'LBL_FORMS' => 'Formularer',
    'LBL_ACTIVE_LIMIT' => 'Aktiv Smart Guides-grænse',
    'LBL_WEB_HOOKS' => 'Webhooks',
    'LBL_START_NEXT_JOURNEY_ACTIVITIES' => 'Næste Smart Guide-startaktivitet',
    'LBL_START_NEXT_JOURNEY_STAGES' => 'Start næste Smart Guide-faselink',
    'LBL_SMART_GUIDE_ACCESSIBLE' => 'Vælg de moduler, hvor Smart Guide skal være tilgængelig',
    'LBL_SMART_GUIDE_MODIFY_ACTIONS' => 'På en fase kan du tilføje eller slette aktiviteter. Deaktiver de handlinger, du ikke vil have, at brugeren skal have adgang til i denne Smart Guide',
    'LBL_SMART_GUIDE_DISABLE_ACTIONS' => 'På en aktivitet kan du tilføje eller slette underaktiviteter. Deaktiver de handlinger, du ikke vil have, at brugeren skal have adgang til i denne Smart Guide',
    'LBL_SMART_GUIDE_ACTIVATES' => 'Hvilket antal af denne Smart Guide, der kan være aktive på en post på samme tid',
    'LBL_SMART_GUIDE_TARGET_ASSIGNEE' => 'Når denne indstilling er aktiveret: Hvis Målmodtager = Overordnet modtager, hvis Tildelt Til-brugeren ændres på en overordnet bruger, vil Tildelt Til-brugerne i Smart Guides, faser og aktiviteter automatisk også ændre sig. Bemærk, at indstillingerne for målmodtager i aktivitetsskabeloner har forrang frem for Smart Guide-skabelonen',
    'LBL_SMART_GUIDE_USER_ASSIGNED' => 'Hvornår skal en bruger tildeles aktiviteterne',
    'LBL_SMART_GUIDE_ACTIVITIES_ASSIGNED' => 'Hvem skal tildeles aktiviteterne',
    'LBL_SMART_GUIDE_STAGE_NUMBERS' => 'Denne til/fra-knap giver dig mulighed for at vise eller skjule automatisk fasenummerering.',
    'CJ_FORMS_LBL_PARENT_NAME' => 'Smart Guide-/Fase-/Aktivitetsskabelon',
];
