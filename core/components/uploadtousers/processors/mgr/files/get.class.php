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
 * CMP get file details controller.
 *
 * @package     uploadtousers
 * @subpackage  controller
 */
class FilesGetProcessor extends modObjectGetProcessor {

    public $classKey = 'Addendum';

    /**
     * {@inheritDoc}
     * @return boolean
     */
    public function initialize() {
        $props = $this->getProperties();

        $this->object = $this->modx->getObject($this->classKey, array(
            'dir_path' => $props['dirPath'],
            'name' => $props['name']
        ));

        if (empty($this->object)) {
            return $this->modx->lexicon($this->objectType . '_err_nfs', $props);
        }

        if ($this->checkViewPermission && $this->object instanceof modAccessibleObject && !$this->object->checkPolicy('view')) {
            return $this->modx->lexicon('access_denied');
        }

        return true;
    }

}

return 'FilesGetProcessor';