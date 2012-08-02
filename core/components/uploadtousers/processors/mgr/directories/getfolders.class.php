<?php

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

        foreach (glob($dirPath . '*', GLOB_ONLYDIR) as $dir) {
            $lastmod = filemtime($dir) * 1000;
            $dirs[] = array(
                'name' => basename($dir) . '/', // last slash is required as an identification!
                'text' => basename($dir),
                'size' => '',
                'lastmod' => $lastmod,
                'dirPath' => $dir . '/',
                'leaf' => $this->_leaf($dir . '/')
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