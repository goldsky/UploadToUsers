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

        $objectArray['files'] = '';
        $basePath = $this->modx->getOption('uploadtousers.base_path', null, $this->modx->getOption('assets_path') . 'userfiles/');
        if (!is_dir($basePath)) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, $basePath . ' for Upload to Users package does not exist.');
            return $objectArray;
        }
        $basePath = realpath($basePath) . DIRECTORY_SEPARATOR;
        $folderName = $this->modx->getOption('uploadtousers.foldername');
        if (!empty($folderName) && $folderName === 'id' || $folderName === 'username') {
            switch ($folderName) {
                case 'id':
                    $folderName = $basePath . $objectArray['id'] . DIRECTORY_SEPARATOR;
                    break;
                case 'username':
                    $folderName = $basePath . $objectArray['username'] . DIRECTORY_SEPARATOR;
                    break;
                default:
                    break;
            }
            $objectArray['files'] = $this->_getDirContents($folderName);
        }
        return $objectArray;
    }

    private function _getDirContents($dirPath) {
        $output = array();
        if (!is_dir($dirPath)) {
            @mkdir($dirPath);
        }
        foreach (glob($dirPath . '*') as $filename) {
            if (!is_file($filename))
                continue;
            $size = filesize($filename);
            $lastmod = filemtime($filename) * 1000;
            $basePath = realpath(MODX_BASE_PATH) . DIRECTORY_SEPARATOR;
            $url = str_replace(array($basePath, '\\'), array('','/'), $filename);
            $output[] = array(
                'name' => basename($filename),
                'size' => $size,
                'lastmod' => $lastmod,
                'url' => $url
            );
        }
        return $output;
    }

}

return 'UsersGetListProcessor';