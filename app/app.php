<?php
/**
 * Created by PhpStorm.
 * User: Milan Damen
 * Date: 15-9-2015
 * Time: 13:55
 */

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

