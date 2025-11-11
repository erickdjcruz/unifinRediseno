<?php 
 $GLOBALS["dictionary"]["SchedulersJob"]=array (
  'table' => 'job_queue',
  'favorites' => true,
  'activity_enabled' => true,
  'comment' => 'Job queue keeps the list of the jobs executed by this instance',
  'fields' => 
  array (
    'id' => 
    array (
      'name' => 'id',
      'vname' => 'LBL_NAME',
      'type' => 'id',
      'required' => true,
      'reportable' => false,
    ),
    'name' => 
    array (
      'name' => 'name',
      'vname' => 'LBL_NAME',
      'type' => 'name',
      'dbType' => 'varchar',
      'len' => 255,
      'required' => true,
    ),
    'deleted' => 
    array (
      'name' => 'deleted',
      'vname' => 'LBL_DELETED',
      'type' => 'bool',
      'required' => true,
      'default' => '0',
      'reportable' => false,
    ),
    'date_entered' => 
    array (
      'name' => 'date_entered',
      'vname' => 'LBL_DATE_ENTERED',
      'type' => 'datetime',
      'required' => true,
    ),
    'date_modified' => 
    array (
      'name' => 'date_modified',
      'vname' => 'LBL_DATE_MODIFIED',
      'type' => 'datetime',
      'required' => true,
    ),
    'scheduler_id' => 
    array (
      'name' => 'scheduler_id',
      'vname' => 'LBL_SCHEDULER',
      'type' => 'id',
      'required' => false,
      'reportable' => false,
    ),
    'execute_time' => 
    array (
      'name' => 'execute_time',
      'vname' => 'LBL_EXECUTE_TIME',
      'type' => 'datetime',
      'required' => false,
    ),
    'status' => 
    array (
      'name' => 'status',
      'vname' => 'LBL_STATUS',
      'type' => 'enum',
      'options' => 'schedulers_status_dom',
      'len' => 20,
      'required' => true,
      'reportable' => true,
      'readonly' => true,
    ),
    'resolution' => 
    array (
      'name' => 'resolution',
      'vname' => 'LBL_RESOLUTION',
      'type' => 'enum',
      'options' => 'schedulers_resolution_dom',
      'len' => 20,
      'required' => true,
      'reportable' => true,
      'readonly' => true,
    ),
    'message' => 
    array (
      'name' => 'message',
      'vname' => 'LBL_MESSAGE',
      'type' => 'text',
      'required' => false,
      'reportable' => false,
    ),
    'target' => 
    array (
      'name' => 'target',
      'vname' => 'LBL_TARGET_ACTION',
      'type' => 'varchar',
      'len' => 255,
      'required' => true,
      'reportable' => true,
    ),
    'data' => 
    array (
      'name' => 'data',
      'vname' => 'LBL_DATA',
      'type' => 'longtext',
      'required' => false,
      'reportable' => true,
    ),
    'requeue' => 
    array (
      'name' => 'requeue',
      'vname' => 'LBL_REQUEUE',
      'type' => 'bool',
      'default' => 0,
      'required' => false,
      'reportable' => true,
    ),
    'retry_count' => 
    array (
      'name' => 'retry_count',
      'vname' => 'LBL_RETRY_COUNT',
      'type' => 'tinyint',
      'required' => false,
      'reportable' => true,
      'readonly' => true,
    ),
    'failure_count' => 
    array (
      'name' => 'failure_count',
      'vname' => 'LBL_FAIL_COUNT',
      'type' => 'tinyint',
      'required' => false,
      'reportable' => true,
      'readonly' => true,
    ),
    'job_delay' => 
    array (
      'name' => 'job_delay',
      'vname' => 'LBL_INTERVAL',
      'type' => 'int',
      'required' => false,
      'reportable' => false,
    ),
    'client' => 
    array (
      'name' => 'client',
      'vname' => 'LBL_CLIENT',
      'type' => 'varchar',
      'len' => 255,
      'required' => true,
      'reportable' => true,
    ),
    'percent_complete' => 
    array (
      'name' => 'percent_complete',
      'vname' => 'LBL_PERCENT',
      'type' => 'int',
      'required' => false,
    ),
    'job_group' => 
    array (
      'name' => 'job_group',
      'vname' => 'LBL_JOB_GROUP',
      'type' => 'varchar',
      'len' => 255,
      'required' => false,
      'reportable' => true,
    ),
    'schedulers' => 
    array (
      'name' => 'schedulers',
      'vname' => 'LBL_SCHEDULER_ID',
      'type' => 'link',
      'relationship' => 'schedulers_jobs_rel',
      'source' => 'non-db',
      'link_type' => 'one',
    ),
    'module' => 
    array (
      'name' => 'module',
      'vname' => 'LBL_MODULE',
      'type' => 'varchar',
      'len' => 255,
      'required' => false,
      'reportable' => true,
    ),
    'fallible' => 
    array (
      'name' => 'fallible',
      'vname' => 'LBL_FALLIBLE',
      'type' => 'bool',
      'default' => '0',
      'comment' => 'An indicator of whether parents failure depends on subtask.',
    ),
    'rerun' => 
    array (
      'name' => 'rerun',
      'vname' => 'LBL_RERUN',
      'type' => 'bool',
      'default' => '0',
      'comment' => 'If a job can be rerun.',
    ),
    'interface' => 
    array (
      'name' => 'interface',
      'vname' => 'LBL_INTERFACE',
      'type' => 'bool',
      'default' => '0',
      'comment' => 'Mark the task as interface for a job in job server.',
    ),
    'notes' => 
    array (
      'name' => 'notes',
      'vname' => 'LBL_NOTES',
      'type' => 'link',
      'relationship' => 'schedulersjob_notes',
      'module' => 'Notes',
      'bean_name' => 'Note',
      'source' => 'non-db',
    ),
    'following' => 
    array (
      'massupdate' => false,
      'name' => 'following',
      'vname' => 'LBL_FOLLOWING',
      'type' => 'bool',
      'source' => 'non-db',
      'comment' => 'Is user following this record',
      'studio' => 'false',
      'link' => 'following_link',
      'rname' => 'id',
      'rname_exists' => true,
    ),
    'following_link' => 
    array (
      'name' => 'following_link',
      'type' => 'link',
      'relationship' => 'schedulersjobs_following',
      'source' => 'non-db',
      'vname' => 'LBL_FOLLOWING',
      'reportable' => false,
    ),
    'my_favorite' => 
    array (
      'massupdate' => false,
      'name' => 'my_favorite',
      'vname' => 'LBL_FAVORITE',
      'type' => 'bool',
      'source' => 'non-db',
      'comment' => 'Favorite for the user',
      'studio' => 
      array (
        'list' => false,
        'recordview' => false,
        'basic_search' => false,
        'advanced_search' => false,
      ),
      'link' => 'favorite_link',
      'rname' => 'id',
      'rname_exists' => true,
    ),
    'favorite_link' => 
    array (
      'name' => 'favorite_link',
      'type' => 'link',
      'relationship' => 'schedulersjobs_favorite',
      'source' => 'non-db',
      'vname' => 'LBL_FAVORITE',
      'reportable' => false,
      'workflow' => false,
      'full_text_search' => 
      array (
        'type' => 'favorites',
        'enabled' => true,
        'searchable' => false,
        'aggregations' => 
        array (
          'favorite_link' => 
          array (
            'type' => 'MyItems',
            'options' => 
            array (
              'field' => 'user_favorites',
            ),
          ),
        ),
      ),
    ),
    'assigned_user_id' => 
    array (
      'name' => 'assigned_user_id',
      'vname' => 'LBL_ASSIGNED_TO_ID',
      'group' => 'assigned_user_name',
      'type' => 'id',
      'reportable' => false,
      'isnull' => 'false',
      'audited' => true,
      'duplicate_on_record_copy' => 'always',
      'comment' => 'User ID assigned to record',
      'duplicate_merge' => 'disabled',
      'mandatory_fetch' => true,
      'massupdate' => false,
      'full_text_search' => 
      array (
        'enabled' => true,
        'searchable' => false,
        'aggregations' => 
        array (
          'assigned_user_id' => 
          array (
            'type' => 'MyItems',
            'label' => 'LBL_AGG_ASSIGNED_TO_ME',
          ),
        ),
      ),
    ),
    'assigned_user_name' => 
    array (
      'name' => 'assigned_user_name',
      'link' => 'assigned_user_link',
      'vname' => 'LBL_ASSIGNED_TO',
      'rname' => 'full_name',
      'type' => 'relate',
      'reportable' => false,
      'source' => 'non-db',
      'table' => 'users',
      'id_name' => 'assigned_user_id',
      'module' => 'Users',
      'duplicate_merge' => 'disabled',
      'duplicate_on_record_copy' => 'always',
      'sort_on' => 
      array (
        0 => 'last_name',
      ),
      'exportable' => true,
      'related_fields' => 
      array (
        0 => 'assigned_user_id',
      ),
    ),
    'assigned_user_link' => 
    array (
      'name' => 'assigned_user_link',
      'type' => 'link',
      'relationship' => 'schedulersjobs_assigned_user',
      'vname' => 'LBL_ASSIGNED_TO_USER',
      'link_type' => 'one',
      'module' => 'Users',
      'bean_name' => 'User',
      'source' => 'non-db',
      'duplicate_merge' => 'enabled',
      'id_name' => 'assigned_user_id',
      'table' => 'users',
      'side' => 'right',
    ),
  ),
  'relationships' => 
  array (
    'schedulersjob_notes' => 
    array (
      'lhs_module' => 'SchedulersJobs',
      'lhs_table' => 'job_queue',
      'lhs_key' => 'id',
      'rhs_module' => 'Notes',
      'rhs_table' => 'notes',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
    ),
    'schedulersjobs_following' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'SchedulersJobs',
      'rhs_table' => 'job_queue',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'subscriptions',
      'join_key_lhs' => 'created_by',
      'join_key_rhs' => 'parent_id',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'SchedulersJobs',
      'user_field' => 'created_by',
    ),
    'schedulersjobs_favorite' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'SchedulersJobs',
      'rhs_table' => 'job_queue',
      'rhs_key' => 'id',
      'relationship_type' => 'user-based',
      'join_table' => 'sugarfavorites',
      'join_key_lhs' => 'modified_user_id',
      'join_key_rhs' => 'record_id',
      'relationship_role_column' => 'module',
      'relationship_role_column_value' => 'SchedulersJobs',
      'user_field' => 'created_by',
    ),
    'schedulersjobs_assigned_user' => 
    array (
      'lhs_module' => 'Users',
      'lhs_table' => 'users',
      'lhs_key' => 'id',
      'rhs_module' => 'SchedulersJobs',
      'rhs_table' => 'job_queue',
      'rhs_key' => 'assigned_user_id',
      'relationship_type' => 'one-to-many',
    ),
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'job_queuepk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'idx_resolution_executetime',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'resolution',
        1 => 'execute_time',
      ),
    ),
    2 => 
    array (
      'name' => 'idx_target_jobgroup',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'target',
        1 => 'job_group',
        2 => 'resolution',
      ),
    ),
    3 => 
    array (
      'name' => 'idx_status_sched_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'status',
        1 => 'scheduler_id',
      ),
    ),
    4 => 
    array (
      'name' => 'idx_del_time_status',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'execute_time',
        2 => 'status',
      ),
    ),
    'assigned_user_id' => 
    array (
      'name' => 'idx_job_queue_assigned_del',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'assigned_user_id',
        1 => 'deleted',
      ),
    ),
  ),
  'acls' => 
  array (
    'SugarACLAdminOnly' => true,
  ),
  'name_format_map' => 
  array (
  ),
  'visibility' => 
  array (
  ),
  'templates' => 
  array (
    'following' => 'following',
    'favorite' => 'favorite',
    'assignable' => 'assignable',
  ),
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
  ),
);