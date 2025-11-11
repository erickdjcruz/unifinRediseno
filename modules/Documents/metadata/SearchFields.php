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
$searchFields['Documents'] =
    [
        'document_name' => ['query_type' => 'default'],
        'category_id' => ['query_type' => 'default', 'options' => 'document_category_dom', 'template_var' => 'CATEGORY_OPTIONS'],
        'subcategory_id' => ['query_type' => 'default', 'options' => 'document_subcategory_dom', 'template_var' => 'SUBCATEGORY_OPTIONS'],
        'active_date' => ['query_type' => 'default'],
        'exp_date' => ['query_type' => 'default'],
        'assigned_user_id' => ['query_type' => 'default'],
        'filename' => [
            'query_type' => 'format',
            'operator' => 'subquery',
            'subquery' => 'SELECT document_revisions.id FROM document_revisions
			           WHERE document_revisions.deleted=0
				   AND document_revisions.filename LIKE \'{0}\'',
            'db_field' => [0 => 'document_revision_id'],
        ],
        'favorites_only' => [
            'query_type' => 'format',
            'operator' => 'subquery',
            'subquery' => 'SELECT sugarfavorites.record_id FROM sugarfavorites 
			                    WHERE sugarfavorites.deleted=0 
			                        and sugarfavorites.module = \'Documents\' 
			                        and sugarfavorites.assigned_user_id = {0}',
            'db_field' => ['id']],
        //Range Search Support
        'range_date_entered' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'start_range_date_entered' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'end_range_date_entered' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'range_date_modified' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'start_range_date_modified' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'end_range_date_modified' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        //Range Search Support
    ];
