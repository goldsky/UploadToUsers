<?php

/**
 * Upload to Users CMP
 *
 * Copyright 2013 by goldsky <goldsky@virtudraft.com>
 *
 * This file is part of Upload to Users CMP, a back end manager to upload files
 * into the registered members' folders.
 *
 * Upload to Users CMP is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * Upload to Users CMP is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Upload to Users CMP; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
 * Build the setup options form.
 *
 * @package     uploadtousers
 * @subpackage  build
 */
$settings = array();
$settings['uploadtousers.base_path']= $modx->newObject('modSystemSetting');
$settings['uploadtousers.base_path']->fromArray(array(
    'key' => 'uploadtousers.base_path',
    'value' => '{assets_path}userfiles/',
    'xtype' => 'textfield',
    'namespace' => 'uploadtousers',
    'area' => 'file',
),'',true,true);

$settings['uploadtousers.foldername']= $modx->newObject('modSystemSetting');
$settings['uploadtousers.foldername']->fromArray(array(
    'key' => 'uploadtousers.foldername',
    'value' => 'id',
    'xtype' => 'textfield',
    'namespace' => 'uploadtousers',
    'area' => 'file',
),'',true,true);

return $settings;