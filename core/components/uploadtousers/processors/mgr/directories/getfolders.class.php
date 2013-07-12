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
 * CMP get folders controller.
 *
 * @package     uploadtousers
 * @subpackage  controller
 */
include_once MODX_CORE_PATH . 'model/modx/processors/browser/directory/getlist.class.php';

class DirectoriesGetFoldersProcessor extends modBrowserFolderGetListProcessor {

    public function initialize() {
        $this->setDefaultProperties(array(
            'id' => '',
        ));
//        $dir = $this->getProperty('id');
        $dir = rawurldecode($this->getProperty('dirPath'));
        if (empty($dir))
            return false;

        if (empty($dir) || $dir == 'usersRoot') {
            $this->setProperty('id','');
        } else {
            $this->setProperty('dir', $dir);
        }

        return true;
    }

    public function process() {
        $dirPath = $this->getProperty('dir');
        if (!is_dir($dirPath)) {
            @mkdir($dirPath);
        }
        $dirs = array();

        $parentUrl = str_replace(realpath(MODX_BASE_PATH) . DIRECTORY_SEPARATOR, '', $dirPath);
        $parentUrl = str_replace('\\', '/', $parentUrl);
        $userId = $this->getProperty('uid');
        foreach (glob($dirPath . '*', GLOB_ONLYDIR) as $dir) {
            $lastmod = filemtime($dir) * 1000;
            $dirs[] = array(
                'name' => basename($dir) . '/', // last slash is required as an identification!
                'text' => basename($dir),
                'size' => '',
                'lastmod' => $lastmod,
                'dirPath' => $dir . '/',
                'leaf' => $this->_leaf($dir . '/'),
                'type' => 'dir',
                'uid' => intval($userId),
            );
        }

        return $this->success('', $dirs);
    }

    /**
     * Only detect any existing directory children, to enable [+] sign on leaf
     * @param   string  $dirPath    directory path
     * @return  boolean [TRUE] if it's a leaf, [FALSE] if it's a container
     */
    private function _leaf($dirPath) {
        $leaf = true;

        $parentUrl = str_replace(realpath(MODX_BASE_PATH) . DIRECTORY_SEPARATOR, '', $dirPath);
        $parentUrl = str_replace('\\', '/', $parentUrl);

        $dirs = array();
        foreach (glob($dirPath . '*', GLOB_ONLYDIR) as $dir) {
            $dirs[] = $dir;
        }

        if (!empty($dirs))
            $leaf = false;

        return $leaf;
    }
}

return 'DirectoriesGetFoldersProcessor';