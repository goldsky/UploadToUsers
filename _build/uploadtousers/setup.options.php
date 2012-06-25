<?php

/**
 * Upload to Users CMP
 *
 * Copyright 2012 by goldsky <goldsky@modx-id.com>
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
/* set some default values */
$values = array(
    'base_path' => '{assets_path}userfiles/',
    'foldername' => 'id'
);
/* get values based on mode */
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
        $setting = $modx->getObject('modSystemSetting', array('key' => 'uploadtousers.base_path'));
        if ($setting != null) {
            $values['base_path'] = $setting->get('value');
        }
        unset($setting);

        $setting = $modx->getObject('modSystemSetting', array('key' => 'uploadtousers.foldername'));
        if ($setting != null) {
            $values['foldername'] = $setting->get('value');
        }
        unset($setting);

        break;
    case xPDOTransport::ACTION_UPGRADE:
    case xPDOTransport::ACTION_UNINSTALL:
        break;
}

$radio['id'] = strtolower(trim($values['foldername'])) == 'id' ? 'checked="checked"' : '';
$radio['username'] = strtolower(trim($values['foldername'])) == 'username' ? 'checked="checked"' : '';

$output = '
<h2>Upload to Users CMP</h2>
<p>You are about to install "Upload to Users" Custom Manager Page (CMP).</p>
<p>Please type in the root path of the users\' folders.</p>
<div>
    <input
        type="text"
        name="base_path"
        value="' . $values['base_path'] . '"
        placeholder="{assets_path}userfiles/"
        />
</div>
<p>Please select which key that the users\' folders should be applied. They will
    be populated automatically if they don\'t exist once you open the CMP.</p>
<div style="padding: 10px;border: 1px solid rgb(224, 224, 224);background-color: #dddddd!important;">
    <input
        type="radio"
        name="foldername"
        value="id"
        ' . $radio['id'] . '
        /> ID <input
        type="radio"
        name="foldername"
        value="username"
        ' . $radio['username'] . '
        /> username<br />
</div>
';
return $output;