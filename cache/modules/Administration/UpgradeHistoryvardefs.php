<?php 
 $GLOBALS["dictionary"]["UpgradeHistory"]=array (
  'table' => 'upgrade_history',
  'archive' => false,
  'comment' => 'Tracks Sugar upgrades made over time; used by Upgrade Wizard and Module Loader',
  'fields' => 
  array (
    'id' => 
    array (
      'name' => 'id',
      'type' => 'id',
      'required' => true,
      'reportable' => false,
      'comment' => 'Unique identifier',
    ),
    'filename' => 
    array (
      'name' => 'filename',
      'type' => 'varchar',
      'len' => '255',
      'comment' => 'Cached filename containing the upgrade scripts and content',
    ),
    'md5sum' => 
    array (
      'name' => 'md5sum',
      'type' => 'varchar',
      'len' => '32',
      'comment' => 'The MD5 checksum of the upgrade file',
    ),
    'type' => 
    array (
      'name' => 'type',
      'type' => 'varchar',
      'len' => '30',
      'comment' => 'The upgrade type (module, patch, etc)',
    ),
    'status' => 
    array (
      'name' => 'status',
      'type' => 'varchar',
      'len' => '50',
      'comment' => 'The status of the upgrade (ex:  "installed")',
    ),
    'version' => 
    array (
      'name' => 'version',
      'type' => 'varchar',
      'len' => '64',
      'comment' => 'Version as contained in manifest file',
    ),
    'name' => 
    array (
      'name' => 'name',
      'type' => 'varchar',
      'len' => '255',
    ),
    'description' => 
    array (
      'name' => 'description',
      'type' => 'text',
    ),
    'id_name' => 
    array (
      'name' => 'id_name',
      'type' => 'varchar',
      'len' => '255',
      'comment' => 'The unique id of the module',
    ),
    'manifest' => 
    array (
      'name' => 'manifest',
      'type' => 'longtext',
      'comment' => 'A serialized copy of the manifest file.',
    ),
    'patch' => 
    array (
      'name' => 'patch',
      'type' => 'text',
      'comment' => 'A serialized copy of the patch applied to the package during installation',
    ),
    'date_entered' => 
    array (
      'name' => 'date_entered',
      'type' => 'datetime',
      'required' => true,
      'comment' => 'Date of create or module load',
    ),
    'date_modified' => 
    array (
      'name' => 'date_modified',
      'type' => 'datetime',
      'required' => true,
      'comment' => 'Date of create or module load',
    ),
    'published_date' => 
    array (
      'name' => 'published_date',
      'type' => 'varchar',
      'len' => '50',
      'comment' => 'Package published date from manifest. Saved as is.',
    ),
    'uninstallable' => 
    array (
      'name' => 'uninstallable',
      'type' => 'bool',
      'len' => '1',
      'default' => '1',
      'comment' => 'Is package uninstallable?',
    ),
    'enabled' => 
    array (
      'name' => 'enabled',
      'type' => 'bool',
      'len' => '1',
      'default' => '1',
    ),
    'process_status' => 
    array (
      'name' => 'process_status',
      'type' => 'text',
      'comment' => 'Package status data in JSON: installation progress and related values',
    ),
    'deleted' => 
    array (
      'name' => 'deleted',
      'vname' => 'LBL_DELETED',
      'type' => 'bool',
      'default' => '0',
      'reportable' => false,
      'duplicate_on_record_copy' => 'no',
      'comment' => 'Record deletion indicator',
    ),
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'upgrade_history_pk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'upgrade_history_md5_uk',
      'type' => 'unique',
      'fields' => 
      array (
        0 => 'md5sum',
      ),
    ),
  ),
  'custom_fields' => false,
  'has_pii_fields' => false,
  'related_calc_fields' => 
  array (
  ),
);