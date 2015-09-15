<?php
/**
 * Created by PhpStorm.
 * User: Milan Damen
 * Date: 15-9-2015
 * Time: 13:55
 */

require_once 'config.php';
//require_once 'database.php';
require_once 'helpers.php';

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

new App();