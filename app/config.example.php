<?php
/**
 * Created by PhpStorm.
 * User: Milan Damen
 * Date: 5-9-2015
 * Time: 15:43
 *
 * COPY THIS FILE TO config.php AND CHANGE THE SETTINGS TO SUIT YOUR SERVER.
 */

class Config {
    public $music_dir = 'C:\music';             // Path to the directory that contains your music
    public $cache_enabled = true;               // Use cache file to store music directory tree
    public $database = [                        // Database settings if you want to use a database
        'host' => 'localhost',                  // Host or IP of the database
        'port' => '3306',                       // Port at which the database runs
        'dbname' => 'smp',                      // Name of the database you want to use (you have to create this database yourself)
        'username' => 'smp',                    // Username with which to logon to the database
        'password' => 'password'                // Password with which to logon to the database
    ];
    public $art = [                             // NOT YET IMPLEMENTEN. Display album art if files with one of these names are in the current song folder
        'filenames' => [
            'cover.jpg',
            'cover.jpeg',
            'cover.png',
            'cover.gif',
            'cover.bmp',
        ]
    ];

    /**
     * Convert every setting in this file to the formats used in the program.
     */
    function __construct() {
        // Make objects from the above arrays
        $this->database = (object) $this->database;
        $this->art = (object) $this->art;

        // Remove trailing characters from path that we don't want.
        $this->music_dir = rtrim($this->music_dir, " \t\n\r\0\x0B\\|/");
    }
}