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
 * Chunks
 *
 * @package     uploadtousers
 * @subpackage  build
 */
$chunks = array();

$chunks[0] = $modx->newObject('modChunk');
$chunks[0]->fromArray(array(
    'id' => 0,
    'name' => 'fdl-u2u-file-row',
    'description' => 'Chunk example for FileDownload\'s file template',
    'snippet' => file_get_contents($sources['source_core'] . '/elements/chunks/filedownload.file.chunk.tpl'),
    'properties' => '',
        ), '', true, true);

$chunks[1] = $modx->newObject('modChunk');
$chunks[1]->fromArray(array(
    'id' => 1,
    'name' => 'fdl-u2u-dir-row',
    'description' => 'Chunk example for FileDownload\'s dir template',
    'snippet' => file_get_contents($sources['source_core'] . '/elements/chunks/filedownload.dir.chunk.tpl'),
    'properties' => '',
        ), '', true, true);

return $chunks;