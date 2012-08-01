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
 * Define the MODX path constants necessary for installation
 *
 * @package     uploadtousers
 * @subpackage  build
 */
$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

/* define version */
define('PKG_NAME', 'Upload to Users CMP');
define('PKG_NAME_LOWER', 'uploadtousers');
define('PKG_VERSION', '1.0');
define('PKG_RELEASE', 'beta3');

/* override with your own defines here (see build.config.sample.php) */
require_once dirname(__FILE__) . '/build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

/* define sources */
$root = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR;
$sources = array(
    'root' => $root,
    'build' => BUILD_PATH,
    'validators' => BUILD_PATH . 'validators' . DIRECTORY_SEPARATOR,
    'data' => BUILD_PATH . 'data' . DIRECTORY_SEPARATOR,
    'source_core' => realpath(MODX_CORE_PATH . 'components') . DIRECTORY_SEPARATOR . PKG_NAME_LOWER,
    'source_assets' => realpath(MODX_ASSETS_PATH . 'components') . DIRECTORY_SEPARATOR . PKG_NAME_LOWER,
    'docs' => realpath(MODX_CORE_PATH . 'components/' . PKG_NAME_LOWER . '/docs/') . DIRECTORY_SEPARATOR,
    'lexicon' => realpath(MODX_CORE_PATH . 'components/' . PKG_NAME_LOWER . '/lexicon/') . DIRECTORY_SEPARATOR,
);
unset($root);

$modx = new modX();
$modx->initialize('mgr');
echo '<pre>';
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');

$modx->loadClass('transport.modPackageBuilder', '', false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME_LOWER, PKG_VERSION, PKG_RELEASE);
$builder->registerNamespace(PKG_NAME_LOWER, false, true, '{core_path}components/' . PKG_NAME_LOWER . '/');
$modx->getService('lexicon', 'modLexicon');
$modx->lexicon->load('uploadtousers:default');

/* load system settings */
$modx->log(modX::LOG_LEVEL_INFO, 'Packaging in settings...');
flush();
$settings = include_once $sources['data'] . 'transport.settings.php';
$attributes = array(
    xPDOTransport::UNIQUE_KEY => 'key',
    xPDOTransport::PRESERVE_KEYS => true,
    xPDOTransport::UPDATE_OBJECT => false,
);
if (!is_array($settings)) {
    $modx->log(modX::LOG_LEVEL_FATAL, 'Adding settings failed.');
}
foreach ($settings as $setting) {
    $vehicle = $builder->createVehicle($setting, $attributes);
    $builder->putVehicle($vehicle);
}
$modx->log(modX::LOG_LEVEL_INFO, count($settings) . ' system settings done.');
flush();
unset($settings, $setting, $attributes);

/* create category */
$modx->log(modX::LOG_LEVEL_INFO, 'Packaging in category...');
flush();
$category = $modx->newObject('modCategory');
$category->set('id', 1);
$category->set('category', PKG_NAME);
$modx->log(modX::LOG_LEVEL_INFO, 'Category done.');
flush();

/* create category vehicle */
$modx->log(modX::LOG_LEVEL_INFO, 'Packaging in category...');
flush();
$attr = array(
    xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array(
        'Snippets' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        ),
    )
);
$vehicle = $builder->createVehicle($category, $attr);

$modx->log(modX::LOG_LEVEL_INFO, 'Adding file resolvers to category...');
flush();
$vehicle->resolve('file', array(
    'source' => $sources['source_assets'],
    'target' => "return MODX_ASSETS_PATH . 'components/';",
));
$vehicle->resolve('file', array(
    'source' => $sources['source_core'],
    'target' => "return MODX_CORE_PATH . 'components/';",
));

$modx->log(modX::LOG_LEVEL_INFO, 'File resolvers done.');
flush();

$modx->log(modX::LOG_LEVEL_INFO, 'Adding in PHP validators...');
flush();
$vehicle->resolve('php', array(
    'source' => $sources['validators'] . 'setup.options.validator.php',
));
$builder->putVehicle($vehicle);
unset($vehicle);

$modx->log(modX::LOG_LEVEL_INFO, 'PHP validators done.');
flush();

/* now pack in the license file, readme and setup options */
$modx->log(modX::LOG_LEVEL_INFO, 'Packaging in attributes');
flush();
$builder->setPackageAttributes(array(
    'license' => file_get_contents($sources['docs'] . 'license.txt'),
    'readme' => file_get_contents($sources['docs'] . 'readme.txt'),
    'changelog' => file_get_contents($sources['docs'] . 'changelog.txt'),
    'setup-options' => array(
        'source' => $sources['build'] . 'setup.options.php'
    )
));
$modx->log(modX::LOG_LEVEL_INFO, 'Attributes done.');
flush();

/* load action/menu */
$modx->log(modX::LOG_LEVEL_INFO, 'Packaging in menu...');
flush();
$menu = include $sources['data'] . 'transport.menu.php';
if (empty($menu))
    $modx->log(modX::LOG_LEVEL_ERROR, 'Could not package in menu.');

$vehicle = $builder->createVehicle($menu, array(
    xPDOTransport::PRESERVE_KEYS => true,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::UNIQUE_KEY => 'text',
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array(
        'Action' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => array('namespace', 'controller'),
    ))));
$builder->putVehicle($vehicle);
unset($vehicle, $menu);
$modx->log(modX::LOG_LEVEL_INFO, 'Menu done.');
flush();

$modx->log(modX::LOG_LEVEL_INFO, 'Packing...');
flush();
$builder->pack();

$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tend = $mtime;
$totalTime = ($tend - $tstart);
$totalTime = sprintf("%2.4f s", $totalTime);

$modx->log(modX::LOG_LEVEL_INFO, "\n<br />" . PKG_NAME . " package built.<br />\nExecution time: {$totalTime}\n");

exit();