<?php
/**
 * Created by PhpStorm.
 * User: Milan Damen
 * Date: 5-9-2015
 * Time: 15:25
 */

require_once 'app/app.php';

if (isset($_GET['stream'])) {
    include 'app/stream.php';
} else {
    if (!isset($_GET['verify']) || $_GET['verify'] !== 'jXHeVKFFgSFFbEKYAprFmQLz') {
        http_response_code(401);
        die('You are not authorized to access this page.');
    }
    include 'app/music.php';
}