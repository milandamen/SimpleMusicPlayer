<?php
/**
 * Created by PhpStorm.
 * User: Milan Damen
 * Date: 5-9-2015
 * Time: 15:46
 */

class Db {
    // Connection settings are set in app/config.php

    const FETCH_ASSOC = PDO::FETCH_ASSOC;	// Return fetch result as an associaive array.
    const FETCH_OBJ = PDO::FETCH_OBJ;		// Return fetch result as an array with objects.
    // Default: FETCH_OBJ

    private $database;						// PDO object for database connection handling.

    // Smart statements
    private $usesmartstmt;					// Boolean for enable/disable of smart statements mode.
    private $lastquery;						// When smart statements mode is enabled, this variable will contain the string of the last query.
    private $laststmt;						// When smart statements mode is enabled, this variable will contain the PDOStatement object of the last query.
    // Default: Enabled

    /**
     * Constructor for this database library. Sets use of smart statements and connects to the database.
     */
    function __construct() {
        $this->usesmartstmt = true;
        $this->dbConnect();
    }

    /**
     * Destructor for this database library. Closes the database connection and any open statements.
     */
    function __destruct() {
        $this->dbClose();
    }

    /**
     * Connect to the database.
     */
    public function dbConnect() {
        try {
            $this->database = new PDO(
                'mysql:host=' . App::$config->database->host .
                ';port=' . App::$config->database->port .
                ';dbname=' . App::$config->database->dbname,
                App::$config->database->username,
                App::$config->database->password
            );
        } catch(PDOException $e){
            die('Database error: '.$e->getMessage());
        }
    }

    /**
     * Close the database connection and any open statements.
     */
    public function dbClose() {
        $this->closeLastStatement();
        $this->database = null;
    }

    /**
     * If you enabled smart statements, manually disconnect and remove the last statement.
     */
    public function closeLastStatement() {
        $this->lastquery = null;
        $this->laststmt = null;
    }

    /**
     * Execute a query.
     * @param $query
     * @param array $params
     */
    public function execQuery($query, $params = array()) {
        $this->requireConnected();

        $stmt = null;
        if ($this->usesmartstmt && $this->isLastQuery($query)) {
            $stmt = $this->laststmt;
        } else {
            $this->closeLastStatement();
            $stmt = $this->database->prepare($query);
        }

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        if ($this->usesmartstmt) {
            $this->lastquery = $query;
            $this->laststmt = $stmt;
        } else {
            $stmt = null;
        }
    }

    /**
     * Execute a query. If the query returns rows, the result gets returned depending on what mode is set.
     * Mode Db::FETCH_ASSOC:   return an associative array
     * Mode Db::FETCH_OBJ:     return an array with objects
     *
     * Example:
     *     $id = 3;
     *     $hidden = false;
     *
     *     $result = $db->getQuery(
     *         'SELECT *
     *         FROM news
     *         WHERE id = :id AND hidden = :hidden',
     *         array(
     *             ':id' => $id,
     *             ':hidden' => $hidden
     *         )
     *     );
     *
     * @param $query
     * @param array $params
     * @param int $mode
     * @return array
     */
    public function getQuery($query, $params = array(), $mode = Db::FETCH_OBJ) {
        $this->requireConnected();

        $stmt = null;
        if ($this->usesmartstmt && $this->isLastQuery($query)) {
            $stmt = $this->laststmt;
        } else {
            $this->closeLastStatement();
            $stmt = $this->database->prepare($query);
        }

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        $result = null;

        if ($mode == Db::FETCH_ASSOC) {
            $result = $stmt->fetchAll();
            if (!isset($result) || !is_array($result)) {
                $result = array();
            }
        }
        if ($mode == Db::FETCH_OBJ) {
            $result = array();
            while ($row = $stmt->fetchObject()) {
                $result[] = $row;
            }
        }

        if ($this->usesmartstmt) {
            $this->lastquery = $query;
            $this->laststmt = $stmt;
        } else {
            $stmt = null;
        }

        return $result;
    }

    /**
     * Enables or disables the use of smart statements.
     * @param $bool
     */
    public function setUseSmartStatement($bool) {
        $this->usesmartstmt = $bool;
    }

    /**
     * Is the currently pending query the same as last one?
     * @param $query
     * @return bool
     */
    private function isLastQuery($query) {
        return $this->lastquery === $query;
    }

    /**
     * Die if not connected to database.
     */
    private function requireConnected() {
        if (!$this->database) {
            die('Database error: Tried executing a query while not connected.');
        }
    }
}