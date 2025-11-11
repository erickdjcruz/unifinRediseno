<?php 
 $GLOBALS["dictionary"]["EmailAddress"]=array (
  'table' => 'email_addresses',
  'archive' => false,
  'audited' => true,
  'fields' => 
  array (
    'id' => 
    array (
      'name' => 'id',
      'type' => 'id',
      'vname' => 'LBL_EMAIL_ADDRESS_ID',
      'required' => true,
    ),
    'email_address' => 
    array (
      'name' => 'email_address',
      'type' => 'varchar',
      'vname' => 'LBL_EMAIL_ADDRESS',
      'length' => 100,
      'required' => true,
      'audited' => true,
      'pii' => true,
    ),
    'email_address_caps' => 
    array (
      'name' => 'email_address_caps',
      'type' => 'varchar',
      'vname' => 'LBL_EMAIL_ADDRESS_CAPS',
      'length' => 100,
      'required' => true,
      'reportable' => false,
      'audited' => true,
      'pii' => true,
    ),
    'invalid_email' => 
    array (
      'name' => 'invalid_email',
      'type' => 'bool',
      'default' => 0,
      'vname' => 'LBL_INVALID_EMAIL',
      'audited' => true,
    ),
    'opt_out' => 
    array (
      'name' => 'opt_out',
      'type' => 'bool',
      'default' => 0,
      'vname' => 'LBL_OPT_OUT',
      'audited' => true,
    ),
    'date_created' => 
    array (
      'name' => 'date_created',
      'type' => 'datetime',
      'vname' => 'LBL_DATE_CREATE',
    ),
    'date_modified' => 
    array (
      'name' => 'date_modified',
      'type' => 'datetime',
      'vname' => 'LBL_DATE_MODIFIED',
    ),
    'confirmation_requested_on' => 
    array (
      'name' => 'confirmation_requested_on',
      'type' => 'datetime',
      'vname' => 'LBL_CONFIRMATION_REQUESTED_ON',
      'audited' => true,
    ),
    'deleted' => 
    array (
      'name' => 'deleted',
      'type' => 'bool',
      'default' => 0,
      'vname' => 'LBL_DELETED',
    ),
    'email_participants' => 
    array (
      'name' => 'email_participants',
      'vname' => 'LBL_EMAIL_PARTICIPANTS',
      'type' => 'link',
      'relationship' => 'emailaddresses_emailparticipants',
      'source' => 'non-db',
      'reportable' => false,
    ),
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'email_addressespk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'idx_ea_caps_opt_out_invalid',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'email_address_caps',
        1 => 'opt_out',
        2 => 'invalid_email',
      ),
    ),
    2 => 
    array (
      'name' => 'idx_ea_opt_out_invalid',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'email_address',
        1 => 'opt_out',
        2 => 'invalid_email',
      ),
    ),
    3 => 
    array (
      'name' => 'idx_ea_del_ea_id',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'deleted',
        1 => 'email_address',
        2 => 'id',
      ),
    ),
  ),
  'custom_fields' => false,
  'has_pii_fields' => true,
  'related_calc_fields' => 
  array (
  ),
);