<?php
/**
 * Created by PhpStorm.
 * User: Milan Damen
 * Date: 5-9-2015
 * Time: 15:25
 */

include 'app/config.php';
//include 'app/database.php';
include 'app/helpers.php';

new App();

if (isset($_GET['stream'])) {
    include 'app/stream.php';
} else {
    include 'app/music.php';
}

class App {
    public static $config;
    public static $db;

    /**
     * Construct all global variables for this app
     */
    public function __construct() {
        static::$config = new Config();
        //static::$db = new Db();
    }
}