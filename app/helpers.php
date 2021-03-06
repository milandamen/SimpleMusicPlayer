<?php
/**
 * Created by PhpStorm.
 * User: Milan Damen
 * Date: 5-9-2015
 * Time: 21:05
 */

/**
 * Print a (nested) array that looks better than var_dump.
 * @param array $array
 */
function printArray(array $array) {
    echo '<ul>';

    foreach ($array as $k => $v) {
        echo '<li>' . $k . ' => ';
        if (is_array($v)) {
            printArray($v);
        } else {
            echo $v;
        }
        echo '</li>';
    }

    echo '</ul>';
}



/**
 * Return a nested array of the folder and file structure inside $directory. It includes subdirectories.
 * @param $directory
 * @return array
 */
function scandir_recursive($directory) {
    $folderContents = array();
    $directory = realpath($directory).DIRECTORY_SEPARATOR;

    foreach (scandir($directory) as $folderItem) {
        if ($folderItem != "." AND $folderItem != ".." && checkPathBounds($directory.$folderItem)) {
            if (is_dir($directory.$folderItem)) {
                $currentFolderContents = scandir_recursive( $directory.$folderItem );
                if (count($currentFolderContents)) {
                    $folderContents[$folderItem] = $currentFolderContents;
                }
            } else {
                if (isMP3($folderItem)) {
                    $folderContents[] = $directory . $folderItem;
                }
            }
        }
    }

    return $folderContents;
}

/**
 * Get the file list.
 * @return array
 */
function getFileList() {
    if (App::$config->cache_enabled) {
        if (file_exists('cache.json')) {
            return getFileListFromCache();
        }

        $filelist = scandir_recursive(App::$config->music_dir);
        saveFileListToCache($filelist);
        return $filelist;
    }

    return scandir_recursive(App::$config->music_dir);
}

/**
 * Get the file list from the cache file.
 * @return array
 */
function getFileListFromCache() {
    $cachefile = fopen('cache.json', 'r');
    $filelist = fread($cachefile, filesize('cache.json'));
    fclose($cachefile);
    return json_decode($filelist, true);
}

/**
 * Save the file list to the cache file.
 * @param $filelist
 */
function saveFileListToCache($filelist) {
    $cachefile = fopen('cache.json', 'w');
    fwrite($cachefile, json_encode($filelist));
    fclose($cachefile);
}

/**
 * Convert an absolute file path to a relative file path.
 * @param $filePath
 * @param $referencePath
 * @return null|string
 */
function toRelativePath($filePath, $referencePath) {
    if (checkPathBounds($filePath) && checkPath($filePath)) {
        return substr($filePath, strlen($referencePath)+1);
    } else {
        return null;
    }
}

/**
 * Convert a Windows path to a Unix path. (Replaces \ with /)
 * @param $filePath
 * @return string
 */
function toUnixPath($filePath) {
    return str_replace('\\', '/', $filePath);
}

/**
 * Encodes path to a path that doesn't break Javascript.
 * These symbols will get encoded: ' " ? &
 * @param $filePath
 * @return string
 */
function toSmpEntitiesPath($filePath) {
    $path = str_replace("'", 'smp-e=1', $filePath);
    $path = str_replace('"', 'smp-e=2', $path);
    $path = str_replace('?', 'smp-e=3', $path);
    $path = str_replace('&', 'smp-e=4', $path);
	$path = str_replace('+', '%2B', $path);
    return $path;
}

/**
 * Decodes path to a path that will break Javascript. Reverse operation of toSmpEntitiesPath().
 * These symbols will get decoded: ' " ? &
 * @param $filePath
 * @return mixed
 */
function fromSmpEntitiesPath($filePath) {
    $path = str_replace('smp-e=1', "'", $filePath);
    $path = str_replace('smp-e=2', '"', $path);
    $path = str_replace('smp-e=3', '?', $path);
    $path = str_replace('smp-e=4', '&', $path);
    return $path;
}

/**
 * Check if the file path is valid.
 * @param $filePath
 * @return bool
 */
function checkPath($filePath) {
    return strpos($filePath, '/../') === false && isMP3($filePath);
}

function isMP3($filePath) {
    return strripos($filePath, '.mp3') === strlen($filePath) - 4;
}

/**
 * Check if path is inside music directory.
 * @param $filePath
 * @return bool
 * @throws Exception
 */
function checkPathBounds($filePath) {
    if (strpos($filePath, App::$config->music_dir) !== 0) {
        throw new Exception('The path specified is not inside the music directory. Please check your config file.');
    }

    return true;
}