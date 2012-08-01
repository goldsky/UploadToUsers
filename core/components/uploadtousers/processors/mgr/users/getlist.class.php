<?php

class UsersGetListProcessor extends modObjectGetListProcessor {

    public $classKey = 'modUser';
    public $languageTopics = array('uploadtousers:default');
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'ASC';
    public $objectType = 'uploadtousers.mgr';

    public function initialize() {
        $initialized = parent::initialize();
        $this->setDefaultProperties(array(
            'usergroup' => false,
            'query' => '',
        ));
        if ($this->getProperty('sort') == 'username_link')
            $this->setProperty('sort', 'username');
        if ($this->getProperty('sort') == 'id')
            $this->setProperty('sort', 'modUser.id');
        return $initialized;
    }

    public function prepareQueryBeforeCount(xPDOQuery $c) {
        $c->leftJoin('modUserProfile', 'Profile');

        $query = $this->getProperty('query', '');
        if (!empty($query)) {
            $c->where(array('modUser.username:LIKE' => '%' . $query . '%'));
            $c->orCondition(array('Profile.fullname:LIKE' => '%' . $query . '%'));
            $c->orCondition(array('Profile.email:LIKE' => '%' . $query . '%'));
        }

        return $c;
    }

    public function prepareQueryAfterCount(xPDOQuery $c) {
        $c->select($this->modx->getSelectColumns('modUser', 'modUser', '', array('id', 'username')));
        $c->select($this->modx->getSelectColumns('modUserProfile', 'Profile', '', array('fullname', 'email')));
        return $c;
    }

    /**
     * Prepare the row for iteration
     * @param xPDOObject $object
     * @return array
     */
    public function prepareRow(xPDOObject $object) {
        $objectArray = $object->toArray();
        unset(
                $objectArray['password']
                , $objectArray['cachepwd']
                , $objectArray['salt']
                , $objectArray['class_key']
                , $objectArray['active']
                , $objectArray['remote_key']
                , $objectArray['remote_data']
                , $objectArray['hash_class']
                , $objectArray['primary_group']
                , $objectArray['session_stale']
                , $objectArray['sudo']
                , $objectArray['blocked']
        );
        $objectArray['uid'] = $objectArray['id'];
        unset($objectArray['id']); // avoid Ext component's ID
        $objectArray['text'] = $objectArray['username'];

        $basePath = $this->modx->getOption('uploadtousers.base_path', null, $this->modx->getOption('assets_path') . 'userfiles/');
        if (!is_dir($basePath)) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, $basePath . ' for Upload to Users package does not exist.');
            return $objectArray;
        }
        $folderName = $this->modx->getOption('uploadtousers.foldername');
        if (!empty($folderName)) {
            switch ($folderName) {
                case 'id':
                    $dirPath = $basePath . $objectArray['uid'] . '/';
                    break;
                case 'username':
                    $dirPath = $basePath . $objectArray['username'] . '/';
                    break;
                default:
                    break;
            }
            $objectArray['leaf'] = $this->_leaf($dirPath);
        }
        if (!is_dir($dirPath)) {
            @mkdir($dirPath);
        }
        $objectArray['dirPath'] = $dirPath;
        return $objectArray;
    }

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

return 'UsersGetListProcessor';