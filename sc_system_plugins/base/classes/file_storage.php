<?php

/**
 * EXHIBIT A. Common Public Attribution License Version 1.0
 * The contents of this file are subject to the Common Public Attribution License Version 1.0 (the “License”);
 * you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * http://www.scene.org/license. The License is based on the Mozilla Public License Version 1.1
 * but Sections 14 and 15 have been added to cover use of software over a computer network and provide for
 * limited attribution for the Original Developer. In addition, Exhibit A has been modified to be consistent
 * with Exhibit B. Software distributed under the License is distributed on an “AS IS” basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for the specific language
 * governing rights and limitations under the License. The Original Code is Scene software.
 * The Initial Developer of the Original Code is Scene Foundation (http://www.scene.org/foundation).
 * All portions of the code written by Scene Foundation are Copyright (c) 2011. All Rights Reserved.

 * EXHIBIT B. Attribution Information
 * Attribution Copyright Notice: Copyright 2011 Scene Foundation. All rights reserved.
 * Attribution Phrase (not exceeding 10 words): Powered by Scene community software
 * Attribution URL: http://www.scene.org/
 * Graphic Image as provided in the Covered Code.
 * Display of Attribution Information is required in Larger Works which are defined in the CPAL as a work
 * which combines Covered Code or portions thereof with code not governed by the terms of the CPAL.
 */

/**
 * File Storage class
 *
 * @author Podyachev Evgeny <joker.SC2@gmail.com>
 * @package sc_system_plugins.base.classes
 * @since 1.0
 */

class BASE_CLASS_FileStorage implements SC_Storage
{

    public function copyDir( $sourcePath, $destPath, array $fileTypes = null, $level = -1 )
    {
        if ( !$this->fileExists($destPath) )
        {
            $this->mkdir($destPath);
        }

        UTIL_File::copyDir($sourcePath, $destPath, $fileTypes, $level);
    }

    // $destPath - must be a file path ( not directory path )

    public function copyFile( $sourcePath, $destPath )
    {
        if ( file_exists($sourcePath) && is_file($sourcePath) )
        {
            copy($sourcePath, $destPath);
            chmod($destPath, 0666);
            return true;
        }

        return false;
    }

    public function copyFileToLocalFS( $destPath, $toFilePath )
    {
        return $this->copyFile($destPath, $toFilePath);
    }

    public function fileGetContent( $destPath )
    {
        return file_get_contents($destPath);
    }

    public function fileSetContent( $destPath, $conent )
    {
        file_put_contents($destPath, $conent);
    }

    public function removeDir( $dirPath )
    {
        UTIL_File::removeDir($dirPath);
    }

    public function removeFile( $destPath )
    {
        return unlink($destPath);
    }

    public function getFileNameList( $dirPath, $prefix = null, array $fileTypes = null )
    {
        $dirPath = UTIL_File::removeLastDS($dirPath);

        $resultList = array();

        $handle = opendir($dirPath);

        while ( ($item = readdir($handle)) !== false )
        {
            if ( $item === '.' || $item === '..' )
            {
                continue;
            }

            if ( $prefix != null )
            {
                $prefixLength = strlen($prefix);

                if ( !( $prefixLength <= strlen($item) && substr($item, 0, $prefixLength) === $prefix ) )
                {
                    continue;
                }
            }

            $path = $dirPath . DS . $item;

            if ( $fileTypes === null || is_file($path) && in_array(UTIL_File::getExtension($item), $fileTypes) )
            {
                $resultList[] = $path;
            }
        }

        closedir($handle);

        return $resultList;
    }

    public function getFileUrl( $path )
    {
        if ( $path === null )
        {
            return '';
        }

        $url = '';

        $prefixLength = strlen(SC_DIR_ROOT);
        $filePathLength = strlen($path);

        if ( $prefixLength <= $filePathLength && substr($path, 0, $prefixLength) === SC_DIR_ROOT )
        {
            $url = str_replace(SC_DIR_ROOT, SC_URL_HOME, $path);
            $url = str_replace(DS, '/', $url);
        }

        return $url;
    }

    public function fileExists( $path )
    {
        return file_exists($path);
    }

    public function isFile( $path )
    {
        return is_file($path);
    }

    public function isDir( $path )
    {
        return is_dir($path);
    }

    public function mkdir( $path )
    {
        return mkdir($path, 0777, true);
    }

    public function isWritable( $path )
    {
        return is_writable($path);
    }

    public function renameFile( $oldDestPath, $newDestPath )
    {
        if ( is_file($oldDestPath) )
        {
            return rename($oldDestPath, $newDestPath);
        }

        return false;
    }


    public function chmod( $path, $permissions )
    {
        chmod($path, $permissions);
    }
}
?>
