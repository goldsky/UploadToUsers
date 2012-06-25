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
 * CMP main controller file.
 *
 * @package     uploadtousers
 * @subpackage  controller
 */
require_once dirname(__FILE__) . '/model/uploadtousers/uploadtousers.class.php';

abstract class UploadtousersManagerController extends modExtraManagerController {

    /** @var Uploadtousers $uploadtousers */
    public $uploadtousers;

    public function initialize() {
        $this->uploadtousers = new Uploadtousers($this->modx);

        $this->addCss($this->uploadtousers->config['cssUrl'] . 'mgr.css');
        $this->addJavascript($this->uploadtousers->config['jsUrl'] . 'mgr/uploadtousers.js');
        $this->addHtml('<script type="text/javascript">
        Ext.onReady(function() {
            Uploadtousers.config = ' . $this->modx->toJSON($this->uploadtousers->config) . ';
        });
        </script>');

        $basePath = $this->modx->getOption('uploadtousers.base_path');
        if (!is_dir($basePath)) {
            @mkdir($basePath);
        }

        return parent::initialize();
    }

    public function getLanguageTopics() {
        return array('uploadtousers:default');
    }

    public function checkPermissions() {
        return true;
    }

}

class IndexManagerController extends UploadtousersManagerController {

    public static function getDefaultController() {
        return 'home';
    }

}