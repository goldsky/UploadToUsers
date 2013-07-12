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
 * CMP update file database.
 *
 * @package     uploadtousers
 * @subpackage  controller
 */
class FilesCreateProcessor extends modObjectCreateProcessor {

    /** @var string $classKey The class key of the Object to iterate */
    public $classKey = 'Addendum';

    /**
     * {@inheritDoc}
     * @return boolean
     */
    public function initialize() {
        $props = $this->getProperties();
        $this->setProperty('dir_path', $props['dirPath']);
        $this->object = $this->modx->newObject($this->classKey);
        return true;
    }

    /**
     * Process the Object create processor
     * {@inheritDoc}
     * @return mixed
     */
    public function process() {
        $this->object->fromArray($this->getProperties());

        /* run object validation */
        if (!$this->object->validate()) {
            /** @var modValidator $validator */
            $validator = $this->object->getValidator();
            if ($validator->hasMessages()) {
                foreach ($validator->getMessages() as $message) {
                    $this->addFieldError($message['field'], $this->modx->lexicon($message['message']));
                }
            }
        }

        $preventSave = $this->fireBeforeSaveEvent();
        if (!empty($preventSave)) {
            return $this->failure($preventSave);
        }

        /* save element */
        if ($this->object->save() == false) {
            $this->modx->error->checkValidation($this->object);
            return $this->failure($this->modx->lexicon($this->objectType . '_err_save'));
        }

        $this->afterSave();

        $this->fireAfterSaveEvent();
        $this->logManagerAction();
        return $this->cleanup();
    }

    /**
     * LEAVE THIS EMPTY!
     * Log the removal manager action
     * @return void
     */
    public function logManagerAction() {

    }

}

return 'FilesCreateProcessor';