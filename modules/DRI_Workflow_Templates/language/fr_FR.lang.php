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
    'LBL_HOMEPAGE_TITLE' => 'Mes modèles du guide intelligent',
    'LBL_LIST_FORM_TITLE' => 'Liste de modèles du guide intelligent',
    'LNK_IMPORT_CUSTOMER_JOURNEY_TEMPLATES' => 'Importer les modèles du guide intelligent',
    'LBL_MODULE_TITLE' => 'Modèles du guide intelligent',
    'LBL_MODULE_NAME' => 'Modèles du guide intelligent',
    'LBL_NEW_FORM_TITLE' => 'Nouveau modèle du guide intelligent',
    'LBL_REMOVE' => 'Supprimer',
    'LBL_SEARCH_FORM_TITLE' => 'Rechercher les modèles du guide intelligent',
    'LBL_TYPE' => 'Type',
    'LNK_LIST' => 'Modèles du guide intelligent',
    'LNK_NEW_RECORD' => 'Créer un modèle du guide intelligent',
    'LBL_COPIES' => 'Copies',
    'LBL_COPIED_TEMPLATE' => 'Modèle copier',
    'LBL_IMPORT_TEMPLATES_BUTTON_LABEL' => 'Importer les modèles',
    'LBL_IMPORT_TEMPLATES_SUCCESS_MESSAGE' => 'Les modèles ont été importés.',
    'LBL_RESAVE_TEMPLATES_BUTTON_LABEL' => 'Réenregistrer les modèles',
    'LBL_RESAVE_TEMPLATES_SUCCESS_MESSAGE' => 'Les modèles ont été réenregistrés.',
    'LNK_VIEW_RECORDS' => 'Afficher les modèles du guide intelligent',
    'LNK_DRI_WORKFLOW_TEMPLATE_LIST' => 'Afficher les modèles du guide intelligent',
    'LBL_AVAILABLE_MODULES' => 'Modules disponibles',
    'LBL_CANCEL_ACTION' => 'Annuler l&#39;action',
    'LBL_NOT_APPLICABLE_ACTION' => 'Action non applicable',
    'LBL_POINTS' => 'Points',
    'LBL_RELATED_ACTIVITIES' => 'Activités associées',
    'LBL_ACTIVE' => 'Actif',
    'LBL_ASSIGNEE_RULE' => 'Règle du destinataire',
    'LBL_TARGET_ASSIGNEE' => 'Destinataire cible',
    'LBL_STAGE_NUMBERS' => 'Numération d&#39;étape',
    'LBL_EXPORT_BUTTON_LABEL' => 'Exporter',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_BUTTON_LABEL' => 'Importer',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_TEXT' => 'Crée/met à jour automatiquement un nouvel enregistrement de modèle de guide intelligent en important un fichier *.json depuis votre système de fichiers.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_CREATE_SUCCESS' => 'Le modèle <a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a> a été créé avec succès.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_UPDATE_SUCCESS' => 'Le modèle <a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a> a été mis à jour avec succès.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_DUPLICATE_NAME_ERROR' => 'L&#39;importation a échoué. Un modèle intitulé "<a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a>" existe déjà. Veuillez changer le nom de l&#39;enregistrement importé et réessayer ou utilisez "Copier" pour créer le modèle du guide intelligent en double.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_UPDATE_CONFIRM' => 'Un modèle avec cet ID existe déjà. Pour mettre à jour le modèle existant, cliquez sur <b>Confirmer</b>. Pour quitter sans apporter de modifications au modèle existant, cliquez sur <b>Annuler</b>.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_TEMPLATE_DELETED' => 'Le modèle que vous essayez d’importer est supprimé dans l’instance actuelle.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_EMPTY_WARNING' => 'Veuillez sélectionner un fichier *.json valide.',
    'LBL_CHECKING_IMPORT_UPLOAD' => 'Validation',
    'LBL_IMPORTING_TEMPLATE' => 'Importation',
    'LBL_DISABLED_STAGE_ACTIONS' => 'Actions d’étape désactivées',
    'LBL_DISABLED_ACTIVITY_ACTIONS' => 'Actions d’activité désactivée',
    'LBL_FORMS' => 'Formulaires',
    'LBL_ACTIVE_LIMIT' => 'Limite du ou des guides intelligents actifs',
    'LBL_WEB_HOOKS' => 'Webhooks',
    'LBL_START_NEXT_JOURNEY_ACTIVITIES' => 'Activité de démarrage du guide intelligent suivant',
    'LBL_START_NEXT_JOURNEY_STAGES' => 'Démarrer le lien de l’étape du guide intelligent suivant',
    'LBL_SMART_GUIDE_ACCESSIBLE' => 'Choisir les modules où le guide intelligent doit être accessible',
    'LBL_SMART_GUIDE_MODIFY_ACTIONS' => 'Il est possible d&#39;ajouter ou de supprimer des activités sur une étape. Désactivez les actions auxquelles vous ne voulez pas que les utilisateurs aient accès dans ce guide intelligent',
    'LBL_SMART_GUIDE_DISABLE_ACTIONS' => 'Il est possible d&#39;ajouter d&#39;autres activités à une activité en tant que sous-activités. Désactivez les actions auxquelles vous ne voulez pas que les utilisateurs aient accès dans ce guide intelligent',
    'LBL_SMART_GUIDE_ACTIVATES' => 'Combien de ce guide intelligent peuvent être actifs sur un enregistrement en même temps',
    'LBL_SMART_GUIDE_TARGET_ASSIGNEE' => 'Si cette option est cochée, si Destinataire cible = Destinataire parent, lorsque l&#39;utilisateur "Assigné à" est modifié sur un parent, les utilisateurs "Assigné à" seront aussi automatiquement modifiés sur les guides intelligents, les étapes et les activités. Notez que les paramètres de Destinataire cible sur les modèles d&#39;activité ont la priorité sur le modèle de guide intelligent',
    'LBL_SMART_GUIDE_USER_ASSIGNED' => 'Quand un utilisateur doit-il être assigné aux activités',
    'LBL_SMART_GUIDE_ACTIVITIES_ASSIGNED' => 'Qui doit être assigné aux activités',
    'LBL_SMART_GUIDE_STAGE_NUMBERS' => 'Cette bascule vous permet d’afficher ou de masquer la numérotation automatique des étapes.',
    'CJ_FORMS_LBL_PARENT_NAME' => 'Modèle d&#39;activité/d&#39;étape/de guide intelligent',
];
