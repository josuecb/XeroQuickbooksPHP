<?php

/**
 * Created by PhpStorm.
 * User: Josue
 * Date: 7/16/2016
 * Time: 1:27 PM
 */

namespace JC\Database;

use Exception;
use PDO;

class DBO extends PDO
{
    private $sqlConnection = null;
    private $db_admin_user = null;
    private $db_admin_pass = null;
    private $charSet = "UTF8";

    private $dbName = null;
    private $dbUser = null;
    private $dbPass = null;

    private $connectionAlive = false;


    /**
     * DB_TF constructor creates a connection to our simple database giving the username and password
     * @param $DB_NAME : database name that we wants to access
     * @param $DB_USER : username credential to access the database
     * @param $DB_PASS : password credential to access the database
     */
    function __construct($DB_NAME, $DB_USER, $DB_PASS)
    {
        $this->dbName = $DB_NAME;
        $this->dbUser = $DB_USER;
        $this->dbPass = $DB_PASS;

        if ($DB_NAME == null) {
            $this->sql_connection($DB_USER, $DB_PASS);
        } else {
            parent::__construct("mysql:host=localhost;dbname=$DB_NAME", $DB_USER, $DB_PASS, null);
            $this->dbName = $DB_NAME;
            $this->dbUser = $DB_USER;
            $this->dbPass = $DB_PASS;

            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $this->exec('SET NAMES "' . $this->charSet . '"');
            $this->connectionAlive = true;
        }
    }


    /**
     * Creates a user with password to certain database, though this simple function grants global credentials
     * (must be improved)
     * @param $DB_USER : unique username
     * @param $DB_PASS : unique password
     * @param $DB_NAME : name of the targeted database
     */
    public function grant_privileges($DB_USER, $DB_PASS, $DB_NAME)
    {
        $this->sqlQuery("CREATE USER $DB_USER@localhost IDENTIFIED BY '$DB_PASS';");
        $this->sqlQuery("GRANT ALL ON $DB_NAME.* TO $DB_USER@localhost;");
    }


    /**
     * This function is only for any user though try to use it when the connection is used by root only
     * (this function is only for testing purposes so it must never be used on production environment)
     * @param $query : custom sql query you would like to do specifying the database
     * @return bool|string
     */
    public function sqlQuery($query, $db = null)
    {
//            echo $query . "<br>";
        if ($db != null) {
            mysqli_select_db($this->sqlConnection, $db);
        }

        if (mysqli_query($this->sqlConnection, $query)) {
            return true;
        } else {
            return mysqli_error($this->sqlConnection);
        }
    }

    /**
     * Closes the connection
     * return Void
     */
    public function sql_close()
    {
        mysqli_close($this->sqlConnection);
        $this->db_admin_user = null;
        $this->db_admin_pass = null;
        $this->sqlConnection = null;
    }

    /**
     * @param $DB_USER : username credential to targeted database
     * @param $DB_PASS : password credential from targeted database
     * @throws Exception
     */
    private function sql_connection($DB_USER, $DB_PASS)
    {
        if ($DB_USER == null || $DB_PASS == null) {
            if ($DB_USER == null) {
                throw new Exception("Admin username must not be Null.");
            } else if ($DB_PASS == null) {
                throw new Exception("Admin password must not be Null.");
            } else {
                throw new Exception("Enter username and password.");
            }
        } else {
            $this->db_admin_user = $DB_USER;
            $this->db_admin_pass = $DB_PASS;

            echo "connecting...<br>";
            $this->sqlConnection = mysqli_connect('localhost', $DB_USER, $DB_PASS);
            if (mysqli_connect_errno()) {
                printf("connect failed: %s\n", mysqli_connect_errno());
                exit();
            }
            echo "connected!...<br>";
        }
    }

}


