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
    'LBL_HOMEPAGE_TITLE' => 'Meus modelos do guia inteligente',
    'LBL_LIST_FORM_TITLE' => 'Lista de modelos do guia inteligente',
    'LNK_IMPORT_CUSTOMER_JOURNEY_TEMPLATES' => 'Importar modelos do guia inteligente',
    'LBL_MODULE_TITLE' => 'Modelos do guia inteligente',
    'LBL_MODULE_NAME' => 'Modelos do guia inteligente',
    'LBL_NEW_FORM_TITLE' => 'Novo modelo do guia inteligente',
    'LBL_REMOVE' => 'Remover',
    'LBL_SEARCH_FORM_TITLE' => 'Pesquisar modelos do guia inteligente',
    'LBL_TYPE' => 'Tipo',
    'LNK_LIST' => 'Modelos do guia inteligente',
    'LNK_NEW_RECORD' => 'Criar modelo do guia inteligente',
    'LBL_COPIES' => 'Cópias',
    'LBL_COPIED_TEMPLATE' => 'Modelo copiado',
    'LBL_IMPORT_TEMPLATES_BUTTON_LABEL' => 'Importar modelos',
    'LBL_IMPORT_TEMPLATES_SUCCESS_MESSAGE' => 'Os modelos foram importados.',
    'LBL_RESAVE_TEMPLATES_BUTTON_LABEL' => 'Salvar modelos novamente',
    'LBL_RESAVE_TEMPLATES_SUCCESS_MESSAGE' => 'Os modelos foram salvos novamente.',
    'LNK_VIEW_RECORDS' => 'Visualizar modelos do guia inteligente',
    'LNK_DRI_WORKFLOW_TEMPLATE_LIST' => 'Visualizar modelos do guia inteligente',
    'LBL_AVAILABLE_MODULES' => 'Módulos disponíveis',
    'LBL_CANCEL_ACTION' => 'Cancelar ação',
    'LBL_NOT_APPLICABLE_ACTION' => 'Ação não aplicável',
    'LBL_POINTS' => 'Pontos',
    'LBL_RELATED_ACTIVITIES' => 'Atividades relacionadas',
    'LBL_ACTIVE' => 'Ativo',
    'LBL_ASSIGNEE_RULE' => 'Atribuir regra',
    'LBL_TARGET_ASSIGNEE' => 'Alvo atribuído',
    'LBL_STAGE_NUMBERS' => 'Numeração de estágios',
    'LBL_EXPORT_BUTTON_LABEL' => 'Exportar',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_BUTTON_LABEL' => 'Importar',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_TEXT' => 'Crie/atualize automaticamente um novo registro do modelo do guia inteligente importando um arquivo *.json em seu sistema de arquivos.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_CREATE_SUCCESS' => 'O modelo <a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a> foi criado com sucesso.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_UPDATE_SUCCESS' => 'O modelo <a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a> foi atualizado com sucesso.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_DUPLICATE_NAME_ERROR' => 'Falha na importação. Um modelo chamado "<a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a>" já existe. Altere o nome do registro importado e tente novamente ou use "Copiar" para criar um modelo do guia inteligente duplicado.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_UPDATE_CONFIRM' => 'Já existe um modelo com esse ID. Para atualizar o modelo existente, clique em <b>Confirmar</b>. Para sair sem fazer alterações no modelo existente, clique em <b>Cancelar</b>.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_TEMPLATE_DELETED' => 'O modelo que você está tentando importar é excluído na instância atual.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_EMPTY_WARNING' => 'Selecione um arquivo *.json válido.',
    'LBL_CHECKING_IMPORT_UPLOAD' => 'Validando',
    'LBL_IMPORTING_TEMPLATE' => 'Importando',
    'LBL_DISABLED_STAGE_ACTIONS' => 'Ações de estágio desabilitadas',
    'LBL_DISABLED_ACTIVITY_ACTIONS' => 'Ações de atividade desabilitadas',
    'LBL_FORMS' => 'Formulários',
    'LBL_ACTIVE_LIMIT' => 'Limite de guia(s) inteligente(s) ativa(s)',
    'LBL_WEB_HOOKS' => 'Web Hooks',
    'LBL_START_NEXT_JOURNEY_ACTIVITIES' => 'Próximo guia inteligente iniciando a atividade',
    'LBL_START_NEXT_JOURNEY_STAGES' => 'Iniciar o próximo link de estágio do guia inteligente',
    'LBL_SMART_GUIDE_ACCESSIBLE' => 'Escolha os módulos onde o guia inteligente deve ser acessível',
    'LBL_SMART_GUIDE_MODIFY_ACTIONS' => 'Em um estágio, você pode adicionar mais atividades ou excluir. Desativar as ações às quais você não deseja que o usuário tenha acesso neste guia inteligente',
    'LBL_SMART_GUIDE_DISABLE_ACTIONS' => 'Em uma atividade, você pode adicionar mais atividades como sub atividades. Desativar as ações às quais você não deseja que o usuário tenha acesso neste guia inteligente',
    'LBL_SMART_GUIDE_ACTIVATES' => 'Quantos deste guia inteligente que podem estar ativos em um registro ao mesmo tempo',
    'LBL_SMART_GUIDE_TARGET_ASSIGNEE' => 'Se estiver marcado, Se alvo atribuído = Atribuído principal, quando o usuário "Atribuído a" for alterado em um principal, os usuários "Atribuído a" também serão alterados automaticamente nos guias, estágios e atividades inteligentes. Observe que as configurações do Alvo atribuído em Modelos de atividade têm precedência sobre o Modelo do guia inteligente',
    'LBL_SMART_GUIDE_USER_ASSIGNED' => 'Quando um usuário deve ser atribuído às Atividades',
    'LBL_SMART_GUIDE_ACTIVITIES_ASSIGNED' => 'A quem devem ser atribuídas as Atividades',
    'LBL_SMART_GUIDE_STAGE_NUMBERS' => 'Este botão permite que você mostre ou oculte a numeração automática de estágios.',
    'CJ_FORMS_LBL_PARENT_NAME' => 'Guia inteligente/estágio/modelo de atividade',
];
