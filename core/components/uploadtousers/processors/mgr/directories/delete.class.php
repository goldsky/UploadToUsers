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
 * CMP delete file controller.
 *
 * @package     uploadtousers
 * @subpackage  controller
 */
include_once MODX_CORE_PATH . 'model/modx/processors/browser/file/remove.class.php';

class DirectoriesDeleteProcessor extends modBrowserFileRemoveProcessor {

    public function getLanguageTopics() {
        return array('uploadtousers', 'file');
    }

    public function process() {
        $props = $this->getProperties();
        if (is_dir($props['dirPath'])) {
            $props['dir'] = $props['dirPath'];
            $dirRemove = $this->modx->runProcessor('browser/directory/remove', $props);
            if ($dirRemove) {
                $response = $dirRemove->getResponse();
                return $response;
            }
        } else {
            $response = parent::process();
            return $response;
        }
    }

}

return 'DirectoriesDeleteProcessor';