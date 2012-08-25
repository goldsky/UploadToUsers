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
 * CMP get files controller.
 *
 * @package     uploadtousers
 * @subpackage  controller
 */
include_once MODX_CORE_PATH . 'model/modx/processors/browser/directory/getfiles.class.php';

class FilesGetFilesProcessor extends modBrowserFolderGetFilesProcessor {

    public function initialize() {
        $this->setDefaultProperties(array(
            'dir' => '',
        ));

        $basePath = $this->modx->getOption('uploadtousers.base_path', null, $this->modx->getOption('assets_path') . 'userfiles/');
        if (!is_dir($basePath)) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, $basePath . ' for Upload to Users package does not exist.');
            return FALSE;
        }
        $basePath = realpath($basePath) . DIRECTORY_SEPARATOR;
        if ($this->getProperty('dir') == 'usersRoot') {
            $this->setProperty('dir', $basePath);
        }

        return true;
    }

    public function process() {
        $dirPath = $this->getProperty('dir');
        if (!is_dir($dirPath)) {
            @mkdir($dirPath);
        }

        $dirs = array();
        $files = array();

        //$parentPath = str_replace(realpath(MODX_BASE_PATH) . DIRECTORY_SEPARATOR, '', $dirPath);
        //$parentPath = str_replace('\\', '/', $parentPath);
        $parentPath = str_replace('\\', '/', $dirPath);

        foreach (glob($dirPath . '/*') as $filename) {
            $size = filesize($filename);
            $lastmod = filemtime($filename) * 1000;
            $basePath = realpath(MODX_BASE_PATH) . DIRECTORY_SEPARATOR;
            $path = str_replace(array($basePath, '\\'), array('', '/'), $filename);
            $path = str_replace('//', '/', $path);
            $dataArray = array();
            if (!is_file($filename)) {
                $props = array(
                    'name' => basename($filename) . '/', // last slash is required as an identification!
                    'size' => '',
                    'lastmod' => $lastmod,
                    'dirPath' => $parentPath,
                    'children' => array()
                );
                $getData = $this->modx->runProcessor(
                        'mgr/files/get'
                        , $props
                        , array('processors_path' => $this->modx->uploadtousers->config['processorsPath'])
                );
                if ($getData->isError()) {
                    if ($getData->getMessage() === 'object_err_nfs') {
                        $createData = $this->modx->runProcessor(
                                'mgr/files/create'
                                , $props
                                , array('processors_path' => $this->modx->uploadtousers->config['processorsPath'])
                        );
                        if (!$createData->isError()) {
                            $dataArray = $createData->getObject();
                        } else {
                            $this->modx->log(modX::LOG_LEVEL_ERROR, __METHOD__ . " \$createData->getMessage(): " . $createData->getMessage());
                        }
                    } else {
                        $this->modx->log(modX::LOG_LEVEL_ERROR, __METHOD__ . " \$getData->getMessage(): " . $getData->getMessage());
                    }
                } else {
                    $response = $getData->getResponse();
                    $dataArray = array(
                        'id' => $response['object']['id'],
                        'title' => $response['object']['title'],
                        'description' => $response['object']['description']
                    );
                }
                $props = array_merge($props, $dataArray);
                $dirs[] = $props;
            } else {
                $props = array(
                    'name' => basename($filename),
                    'size' => $size,
                    'lastmod' => $lastmod,
                    'dirPath' => $parentPath
                );
                $getData = $this->modx->runProcessor(
                        'mgr/files/get'
                        , $props
                        , array('processors_path' => $this->modx->uploadtousers->config['processorsPath'])
                );
                if ($getData->isError()) {
                    if ($getData->getMessage() === 'object_err_nfs') {
                        $createData = $this->modx->runProcessor(
                                'mgr/files/create'
                                , $props
                                , array('processors_path' => $this->modx->uploadtousers->config['processorsPath'])
                        );
                        if (!$createData->isError()) {
                            $dataArray = $createData->getObject();
                        } else {
                            $this->modx->log(modX::LOG_LEVEL_ERROR, __METHOD__ . " \$createData->getMessage(): " . $createData->getMessage());
                        }
                    } else {
                        $this->modx->log(modX::LOG_LEVEL_ERROR, __METHOD__ . " \$getData->getMessage(): " . $getData->getMessage());
                    }
                } else {
                    $response = $getData->getResponse();
                    $dataArray = array(
                        'id' => $response['object']['id'],
                        'title' => $response['object']['title'],
                        'description' => $response['object']['description']
                    );
                }
                $props = array_merge($props, $dataArray);

                $files[] = $props;
            }
        }
        $output = array_merge($dirs, $files);

        return $this->outputArray($output);
    }

}

return 'FilesGetFilesProcessor';