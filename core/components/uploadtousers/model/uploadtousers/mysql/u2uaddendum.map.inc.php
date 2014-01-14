<?php
$xpdo_meta_map['u2uAddendum']= array (
  'package' => 'uploadtousers',
  'version' => '1.1',
  'table' => 'addendum',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'dir_path' => NULL,
    'name' => NULL,
    'title' => NULL,
    'description' => NULL,
  ),
  'fieldMeta' => 
  array (
    'dir_path' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
    ),
    'name' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
    ),
    'title' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
    ),
    'description' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
    ),
  ),
);
