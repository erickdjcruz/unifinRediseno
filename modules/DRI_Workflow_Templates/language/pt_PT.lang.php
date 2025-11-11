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
    'LBL_HOMEPAGE_TITLE' => 'Os Meus Modelos do Guia Inteligente',
    'LBL_LIST_FORM_TITLE' => 'Lista de Modelos do Guia Inteligente',
    'LNK_IMPORT_CUSTOMER_JOURNEY_TEMPLATES' => 'Importar Modelos do Guia Inteligente',
    'LBL_MODULE_TITLE' => 'Modelos do Guia Inteligente',
    'LBL_MODULE_NAME' => 'Modelos do Guia Inteligente',
    'LBL_NEW_FORM_TITLE' => 'Novo Modelo do Guia Inteligente',
    'LBL_REMOVE' => 'Remover',
    'LBL_SEARCH_FORM_TITLE' => 'Pesquisar Modelos do Guia Inteligente',
    'LBL_TYPE' => 'Tipo',
    'LNK_LIST' => 'Modelos do Guia Inteligente',
    'LNK_NEW_RECORD' => 'Criar Modelo do Guia Inteligente',
    'LBL_COPIES' => 'Cópias',
    'LBL_COPIED_TEMPLATE' => 'Modelo Copiado',
    'LBL_IMPORT_TEMPLATES_BUTTON_LABEL' => 'Importar Modelos',
    'LBL_IMPORT_TEMPLATES_SUCCESS_MESSAGE' => 'Os modelos foram importados.',
    'LBL_RESAVE_TEMPLATES_BUTTON_LABEL' => 'Voltar a Guardar Modelos',
    'LBL_RESAVE_TEMPLATES_SUCCESS_MESSAGE' => 'Os modelos foram guardados novamente.',
    'LNK_VIEW_RECORDS' => 'Visualizar Modelos do Guia Inteligente',
    'LNK_DRI_WORKFLOW_TEMPLATE_LIST' => 'Visualizar Modelos do Guia Inteligente',
    'LBL_AVAILABLE_MODULES' => 'Módulos Disponíveis',
    'LBL_CANCEL_ACTION' => 'Cancelar Ação',
    'LBL_NOT_APPLICABLE_ACTION' => 'Sem Ação Aplicável',
    'LBL_POINTS' => 'Pontos',
    'LBL_RELATED_ACTIVITIES' => 'Atividades Relacionadas',
    'LBL_ACTIVE' => 'Ativo',
    'LBL_ASSIGNEE_RULE' => 'Regra do Atribuído',
    'LBL_TARGET_ASSIGNEE' => 'Atribuído Alvo',
    'LBL_STAGE_NUMBERS' => 'Numeração de estágios',
    'LBL_EXPORT_BUTTON_LABEL' => 'Exportar',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_BUTTON_LABEL' => 'Importar',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_TEXT' => 'Crie/atualize automaticamente um novo registo de Modelo do Guia Inteligente ao importar um ficheiro *.json a partir do seu sistema de ficheiros.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_CREATE_SUCCESS' => 'O modelo <a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a> foi criado com sucesso.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_UPDATE_SUCCESS' => 'O modelo <a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a> foi atualizado com sucesso.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_DUPLICATE_NAME_ERROR' => 'Falha de importação. Um modelo com o nome "<a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a>" já existe. Altere o nome do registo importado e tente novamente ou utilize "Copiar" para criar um modelo do Guia Inteligente duplicado.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_UPDATE_CONFIRM' => 'Já existe um modelo com este ID. Para atualizar o modelo existente, clique em <b>Confirmar</b>. Para sair sem fazer quaisquer alterações ao modelo existente, clique em <b>Cancelar</b>.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_TEMPLATE_DELETED' => 'O modelo que está a tentar importar foi eliminado na instância atual.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_EMPTY_WARNING' => 'Selecione um ficheiro *.json válido.',
    'LBL_CHECKING_IMPORT_UPLOAD' => 'A validar',
    'LBL_IMPORTING_TEMPLATE' => 'A importar',
    'LBL_DISABLED_STAGE_ACTIONS' => 'Ações da Fase Desativadas',
    'LBL_DISABLED_ACTIVITY_ACTIONS' => 'Ações da Atividade Desativadas',
    'LBL_FORMS' => 'Formulários',
    'LBL_ACTIVE_LIMIT' => 'Limite do(s) Guia(s) Inteligente(s) Ativo(s)',
    'LBL_WEB_HOOKS' => 'Web Hooks',
    'LBL_START_NEXT_JOURNEY_ACTIVITIES' => 'Próxima Atividade a Começar do Guia Inteligente',
    'LBL_START_NEXT_JOURNEY_STAGES' => 'Iniciar Próxima Ligação da Fase do Guia Inteligente',
    'LBL_SMART_GUIDE_ACCESSIBLE' => 'Escolha os módulos nos quais o Guia Inteligente deve estar acessível',
    'LBL_SMART_GUIDE_MODIFY_ACTIONS' => 'Numa Fase, pode adicionar mais atividades ou eliminar. Desative as ações às quais não pretende que o utilizador tenha acesso neste Guia Inteligente',
    'LBL_SMART_GUIDE_DISABLE_ACTIONS' => 'Numa Atividade, pode adicionar mais atividades como subatividades. Desative as ações às quais não pretende que o utilizador tenha acesso neste Guia Inteligente',
    'LBL_SMART_GUIDE_ACTIVATES' => 'Quantas deste Guia Inteligente podem estar ativas num registo em simultâneo',
    'LBL_SMART_GUIDE_TARGET_ASSIGNEE' => 'Se selecionado, Se Atribuído Alvo = Atribuído Principal, quando o utilizar "Atribuído A" é alterado num principal, os utilizadores "Atribuído A" também serão alterados automaticamente nos guias inteligentes, nas fases e nas atividades. Observe que as definições do Atribuído Alvo nos Modelos de Atividade prevalecem em relação ao Modelo do Guia Inteligente',
    'LBL_SMART_GUIDE_USER_ASSIGNED' => 'Quando deve um utilizador ser atribuído às Atividades',
    'LBL_SMART_GUIDE_ACTIVITIES_ASSIGNED' => 'Quem deve ser atribuído às Atividades',
    'LBL_SMART_GUIDE_STAGE_NUMBERS' => 'Este botão permite-lhe mostrar ou ocultar a numeração de estágios automática.',
    'CJ_FORMS_LBL_PARENT_NAME' => 'Modelo de Atividade/Fase/Guia Inteligente',
];
