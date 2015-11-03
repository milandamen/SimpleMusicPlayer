<?php
/**
 * Created by PhpStorm.
 * User: Milan Damen
 * Date: 5-9-2015
 * Time: 15:33
 */

$relpath = fromSmpEntitiesPath($_GET['stream']);
$fullpath = App::$config->music_dir . DIRECTORY_SEPARATOR . $relpath;

if (checkPath($fullpath) && file_exists($fullpath)) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 200 OK');
    //header('Cache-Control: public'); // needed for i.e.
    header('Content-Type: audio/mpeg');
    header('Content-Transfer-Encoding: Binary');
    header('Content-Disposition: attachment; filename=file.mp3');
    header('Content-Length:' . filesize($fullpath));
    readfile($fullpath);
    die();
} else {
    http_response_code(404);
    die('Error: File not found.');
}