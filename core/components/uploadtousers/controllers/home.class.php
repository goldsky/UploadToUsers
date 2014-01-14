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
 * CMP home controller.
 *
 * @package     uploadtousers
 * @subpackage  controller
 */
class UploadtousersHomeManagerController extends UploadtousersManagerController {

    public function process(array $scriptProperties = array()) {

    }

    public function getPageTitle() {
        return $this->modx->lexicon('uploadtousers');
    }

    public function loadCustomCssJs() {
        $this->addCss($this->uploadtousers->config['cssUrl'].'uploadtousers.css');
        $this->addJavascript($this->uploadtousers->config['jsUrl'].'ext3/ux/fileuploadfield/FileUploadField.js');
        $this->addJavascript($this->uploadtousers->config['jsUrl'].'mgr/widgets/file.window.js');
        $this->addJavascript($this->uploadtousers->config['jsUrl'].'mgr/widgets/files.grid.js');
        $this->addJavascript($this->uploadtousers->config['jsUrl'].'mgr/widgets/folder.panel.js');
        $this->addJavascript($this->uploadtousers->config['jsUrl'].'mgr/widgets/user.panel.js');
        $this->addJavascript($this->uploadtousers->config['jsUrl'].'mgr/widgets/content.panel.js');
        $this->addJavascript($this->uploadtousers->config['jsUrl'].'mgr/widgets/file.upload.window.js');
        $this->addJavascript($this->uploadtousers->config['jsUrl'].'mgr/widgets/userstree.panel.js');
        $this->addJavascript($this->uploadtousers->config['jsUrl'] . 'mgr/widgets/home.panel.js');
        $this->addLastJavascript($this->uploadtousers->config['jsUrl'] . 'mgr/sections/index.js');
    }

    public function getTemplateFile() {
        return $this->uploadtousers->config['templatesPath'] . 'home.tpl';
    }

}
