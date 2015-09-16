<?php
/**
 * Created by PhpStorm.
 * User: Milan Damen
 * Date: 5-9-2015
 * Time: 15:25
 */

require_once 'app/app.php';
require_once 'app/config.php';
//require_once 'database.php';
require_once 'app/helpers.php';
new App();

if (isset($_GET['stream'])) {
    include 'app/stream.php';
} else {
    if (!isset($_GET['verify']) || $_GET['verify'] !== 'jXHeVKFFgSFFbEKYAprFmQLz') {
        die();
    }
    include 'app/music.php';
}