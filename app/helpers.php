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
        if ($folderItem != "." AND $folderItem != "..") {
            if (is_dir($directory.$folderItem)) {
                $folderContents[$folderItem] = scandir_recursive( $directory.$folderItem );
            } else {
                $folderContents[] = $directory.$folderItem;
            }
        }
    }

    return $folderContents;
}

/**
 * Convert an absolute file path to a relative file path.
 * @param $filePath
 * @param $referencePath
 * @return null|string
 */
function toRelativePath($filePath, $referencePath) {
    if (strpos($filePath, $referencePath) === 0 &&
        strpos($filePath, '/../') === false &&
        strripos($filePath, '.mp3') === strlen($filePath) - 4)
    {
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