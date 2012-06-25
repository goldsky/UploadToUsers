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
 * CMP file upload controller.
 *
 * @package     uploadtousers
 * @subpackage  controller
 */
include_once MODX_CORE_PATH . 'model/modx/processors/browser/file/upload.class.php';

class FilesUploadProcessor extends modBrowserFileUploadProcessor {

    public function getLanguageTopics() {
        return array('uploadtousers', 'file');
    }

    public function initialize() {
        $basePath = $this->modx->getOption('uploadtousers.base_path');
        $basePath = str_replace(MODX_BASE_PATH, '', $basePath);
        $this->setProperty('path', $basePath);

        return parent::initialize();
    }

    public function process() {
        if (trim($this->modx->getOption('uploadtousers.foldername')) === 'id') {
            $users = @explode(',', $this->getProperty('ids'));
        } else {
            $users = @explode(',', $this->getProperty('usernames'));
        }
        $pathProp = $this->getProperty('path');

        $msg = '';
        $copyCount = 0;
        $success = $this->failure();
        $countUser = count($users);
        for ($i = 0; $i < $countUser; $i++) {
            if ($i === 0) {
                $path = $pathProp . $users[0] . '/';
                $this->setProperty('path', $path);
                $success = parent::process();
                if (empty($success)) {
                    $msg = '';
                    $errors = $this->source->getErrors();
                    foreach ($errors as $k => $msg) {
                        $this->modx->error->addField($k, $msg);
                    }
                    return $this->failure($msg);
                }
                if ($success['success'] == 1 && $countUser > 1) {
                    $basePath = $this->source->getBasePath();
                    $sourceFile = $basePath . $pathProp . $users[0] . '/' . $_FILES['file']['name'];
                }
            }

            if ($success['success'] === true && $i > 0) {
                try {
                    copy($sourceFile
                            , $basePath . $pathProp . $users[$i] . '/' . $_FILES['file']['name']);
                    $copyCount++;
                } catch (Exception $e) {
                    return $this->failure($e->getMessage());
                }
            }
        }

        if (!empty($success) && $success['success'] === true) {
            if (empty($success['message'])) {
                $msg = $_FILES['file']['name'] . $this->modx->lexicon('uploadtousers.success_msg');
                $msg .= $copyCount > 0 ? "\n+" . $copyCount . ' ' . $this->modx->lexicon('uploadtousers.copies') : '';
            } else {
                $msg = $success['message'];
            }
        }
        return $this->success($msg);
    }

}

return 'FilesUploadProcessor';