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
/*********************************************************************************
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

$app_list_strings = [
    strtolower($object_name) . '_category_dom' => [
        '' => '',
        'Marketing' => 'Marketing',
        'Knowledge Base' => 'Base de conhecimento',
        'Sales' => 'Vendas',
    ],

    strtolower($object_name) . '_subcategory_dom' => [
        '' => '',
        'Marketing Collateral' => 'Acessório de Marketing',
        'Product Brochures' => 'Brochuras de produtos',
        'FAQ' => 'Perguntas Frequentes',
    ],

    strtolower($object_name) . '_status_dom' => [
        'Active' => 'Ativo',
        'Draft' => 'Rascunho',
        'FAQ' => 'Perguntas Frequentes',
        'Expired' => 'Expirou',
        'Under Review' => 'Em Revisão',
        'Pending' => 'Pendente',
    ],
];
