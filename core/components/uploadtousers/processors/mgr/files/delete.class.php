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
 * CMP delete file controller.
 *
 * @package     uploadtousers
 * @subpackage  controller
 */
include_once MODX_CORE_PATH . 'model/modx/processors/browser/file/remove.class.php';

class FilesDeleteProcessor extends modBrowserFileRemoveProcessor {

    public function getLanguageTopics() {
        return array('uploadtousers', 'file');
    }

    public function process() {
        $props = $this->getProperties();
        $paths = @explode(',',$props['paths']);
        $responses = array();
        $errors = array();
        foreach($paths as $path) {
            if (is_dir($path)) {
                $props['dir'] = $path;
                $dirRemove = $this->modx->runProcessor('browser/directory/remove', $props);
                if ($dirRemove) {
                    $responses[] = $dirRemove->getResponse();
                } else {
                    $errors[] = $dirRemove->getMessage();
                }
            } else {
                $path = str_replace(MODX_BASE_PATH, '', $path);
                $this->setProperty('file', $path);
                $fileRemove = parent::process();
                if ($fileRemove) {
                    $responses[] = $fileRemove;
                } else {
                    $errors[] = $dirRemove->getMessage();
                }
            }
        }
        $output = array(
            'success' => '',
            'message' => '',
            'total' => 0,
            'errors' => array(),
            'object' => array()
        );
        if (!empty($responses) || !empty($errors)){
            $output['success'] = 1;
            $output['total'] = count($responses);
            foreach ($responses as $res) {
                $output['object'][] = $res;
            }
            foreach ($errors as $err) {
                $output['errors'][] = $err;
            }
        }

        return $output;
    }

}

return 'FilesDeleteProcessor';