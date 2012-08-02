<?php

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

        $parentUrl = str_replace(realpath(MODX_BASE_PATH) . DIRECTORY_SEPARATOR, '', $dirPath);
        $parentUrl = str_replace('\\', '/', $parentUrl);

        foreach (glob($dirPath . '/*') as $filename) {
            $size = filesize($filename);
            $lastmod = filemtime($filename) * 1000;
            $basePath = realpath(MODX_BASE_PATH) . DIRECTORY_SEPARATOR;
            $url = str_replace(array($basePath, '\\'), array('', '/'), $filename);
            $url = str_replace('//', '/', $url);
            if (!is_file($filename)) {
                $dirs[] = array(
                    'name' => basename($filename) . '/', // last slash is required as an identification!
                    'size' => '',
                    'lastmod' => $lastmod,
                    'url' => $url . '/',
                    'parentUrl' => $parentUrl,
                    'children' => array()
                );
            } else {
                $files[] = array(
                    'name' => basename($filename),
                    'size' => $size,
                    'lastmod' => $lastmod,
                    'url' => $url,
                    'parentUrl' => $parentUrl
                );
            }
        }
        $output = array_merge($dirs, $files);

        return $this->outputArray($output);
    }

}

return 'FilesGetFilesProcessor';