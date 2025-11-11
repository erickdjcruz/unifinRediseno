<?php 
 $GLOBALS["dictionary"]["EmailParticipant"]=array (
  'table' => 'emails_email_addr_rel',
  'comment' => 'Normalization of address fields FROM, TO, CC, and BCC',
  'activity_enabled' => false,
  'fields' => 
  array (
    'id' => 
    array (
      'name' => 'id',
      'vname' => 'LBL_ID',
      'type' => 'id',
      'required' => true,
      'reportable' => true,
      'duplicate_on_record_copy' => 'no',
      'mandatory_fetch' => true,
    ),
    'email_id' => 
    array (
      'name' => 'email_id',
      'vname' => 'LBL_EMAIL_ID',
      'type' => 'id',
    ),
    'from' => 
    array (
      'name' => 'from',
      'vname' => 'LBL_EMAILS_FROM',
      'type' => 'link',
      'relationship' => 'emails_from',
      'source' => 'non-db',
      'reportable' => false,
    ),
    'to' => 
    array (
      'name' => 'to',
      'vname' => 'LBL_EMAILS_TO',
      'type' => 'link',
      'relationship' => 'emails_to',
      'source' => 'non-db',
      'reportable' => false,
    ),
    'cc' => 
    array (
      'name' => 'cc',
      'vname' => 'LBL_EMAILS_CC',
      'type' => 'link',
      'relationship' => 'emails_cc',
      'source' => 'non-db',
      'reportable' => false,
    ),
    'bcc' => 
    array (
      'name' => 'bcc',
      'vname' => 'LBL_EMAILS_BCC',
      'type' => 'link',
      'relationship' => 'emails_bcc',
      'source' => 'non-db',
      'reportable' => false,
    ),
    'address_type' => 
    array (
      'name' => 'address_type',
      'vname' => 'LBL_ADDRESS_TYPE',
      'type' => 'varchar',
      'len' => 4,
      'required' => true,
      'comment' => 'The role (from, to, cc, bcc) that the entry plays in the email',
    ),
    'email_address_id' => 
    array (
      'name' => 'email_address_id',
      'vname' => 'LBL_EMAIL_ADDRESS_ID',
      'type' => 'id',
      'required' => false,
    ),
    'email_addresses' => 
    array (
      'name' => 'email_addresses',
      'vname' => 'LBL_EMAIL_ADDRESS',
      'type' => 'link',
      'relationship' => 'emailaddresses_emailparticipants',
      'source' => 'non-db',
    ),
    'email_address' => 
    array (
      'name' => 'email_address',
      'vname' => 'LBL_EMAIL_ADDRESS',
      'type' => 'relate',
      'rname' => 'email_address',
      'source' => 'non-db',
      'id_name' => 'email_address_id',
      'link' => 'email_addresses',
      'module' => 'EmailAddresses',
    ),
    'invalid_email' => 
    array (
      'name' => 'invalid_email',
      'vname' => 'LBL_INVALID_EMAIL',
      'type' => 'relate',
      'rname' => 'invalid_email',
      'source' => 'non-db',
      'id_name' => 'email_address_id',
      'link' => 'email_addresses',
    ),
    'opt_out' => 
    array (
      'name' => 'opt_out',
      'vname' => 'LBL_OPT_OUT',
      'type' => 'relate',
      'rname' => 'opt_out',
      'source' => 'non-db',
      'id_name' => 'email_address_id',
      'link' => 'email_addresses',
    ),
    'parent_type' => 
    array (
      'name' => 'parent_type',
      'vname' => 'LBL_PARENT_NAME',
      'type' => 'parent_type',
      'dbType' => 'varchar',
      'options' => 'record_type_display_emailparticipants',
      'required' => false,
    ),
    'parent_id' => 
    array (
      'name' => 'parent_id',
      'vname' => 'LBL_PARENT_ID',
      'type' => 'id',
      'comment' => 'The bean\'s ID',
    ),
    'parent_name' => 
    array (
      'name' => 'parent_name',
      'vname' => 'LBL_LIST_RELATED_TO',
      'type' => 'parent',
      'type_name' => 'parent_type',
      'id_name' => 'parent_id',
      'source' => 'non-db',
      'parent_type' => 'record_type_display_emailparticipants',
      'options' => 'record_type_display_emailparticipants',
    ),
    'deleted' => 
    array (
      'name' => 'deleted',
      'type' => 'bool',
      'default' => 0,
    ),
    'date_entered' => 
    array (
      'name' => 'date_entered',
      'vname' => 'LBL_DATE_ENTERED',
      'type' => 'datetime',
    ),
    'date_modified' => 
    array (
      'name' => 'date_modified',
      'vname' => 'LBL_DATE_MODIFIED',
      'type' => 'datetime',
    ),
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'emails_email_addr_relpk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'idx_eearl_email_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'email_id',
        1 => 'address_type',
      ),
    ),
    2 => 
    array (
      'name' => 'idx_eearl_email_address_deleted',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'email_address_id',
        1 => 'deleted',
      ),
    ),
    3 => 
    array (
      'name' => 'idx_eearl_email_address_role',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'email_address_id',
        1 => 'address_type',
        2 => 'deleted',
      ),
    ),
    4 => 
    array (
      'name' => 'idx_eearl_parent',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'parent_type',
        1 => 'parent_id',
        2 => 'deleted',
      ),
    ),
    5 => 
    array (
      'name' => 'idx_eearl_parent_role',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'parent_type',
        1 => 'parent_id',
        2 => 'address_type',
        3 => 'deleted',
      ),
    ),
  ),
  'relationships' => 
  array (
    'emailaddresses_emailparticipants' => 
    array (
      'lhs_module' => 'EmailAddresses',
      'lhs_table' => 'email_addresses',
      'lhs_key' => 'id',
      'rhs_module' => 'EmailParticipants',
      'rhs_table' => 'emails_email_addr_rel',
      'rhs_key' => 'email_address_id',
      'relationship_type' => 'one-to-many',
    ),
  ),
  'portal_visibility' => 
  array (
    'class' => 'EmailParticipants',
  ),
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
  ),
);