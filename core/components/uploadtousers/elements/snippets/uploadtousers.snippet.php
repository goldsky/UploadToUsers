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
 * @package     uploadtousers
 * @subpackage  snippet
 */
$output = '';
$path = $modx->getOption('path', $scriptProperties);
$field = $modx->getOption('field', $scriptProperties, 'title');

if (empty($path) || empty($field)) {
    return '';
}

$fields = array('id', 'dir_path', 'name', 'title', 'description');
if (!in_array($field, $fields)) {
    return 'Valid &field values are: id, dir_path, name, title, description';
}

$uptousers = $modx->getService('uploadtousers'
        , 'Uploadtousers'
        , $modx->getOption('core_path') . 'components/uploadtousers/model/'
);

if (!($uptousers instanceof Uploadtousers))
    return 'instanceof error.';

$c = $modx->newQuery('u2uAddendum');
$dirPath = dirname($path);
$dirPath = str_replace('\\', '/', $dirPath) . '/';
$slash = is_dir($path) ? '/' : '';
$name = basename($path) . $slash;
$c->where(array(
    'dir_path' => $dirPath,
    'name' => $name
));
$fileDetail = $modx->getObject('u2uAddendum', $c);
if (!empty($fileDetail)) {
    if (!empty($toArray)) {
        $contents = array();
        foreach ($fields as $v) {
            $contents[$v] = $fileDetail->get($v);
        }
        $output = '<pre>' . print_r($contents, true) . '</pre>';
    } else {
        $output = $fileDetail->get($field);
    }
    if (!empty($toPlaceholder)) {
        $modx->setPlaceholder($toPlaceholder, $output);
        return '';
    }
}

return $output;