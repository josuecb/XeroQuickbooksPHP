<?php
/**
 * Created by PhpStorm.
 * User: Josue
 * Date: 7/16/2016
 * Time: 2:14 PM
 */

use JC\Database\DBO;
use JC\Debug;

include_once __DIR__ . '/src/api/JC/Debug.php';
include_once __DIR__ . '/src/config.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
try {
    /**
     *
     */
    $adminConnection = new DBO(null, 'rootAdmin', 'MyS3rv3r10');

    $sql_queries = array(
        "tables.sql"
    );

    if (Debug::CREATE_DB) {
        $adminConnection->sqlQuery(file_get_contents(__DIR__ . '/src/asset/sql/db_create.sql'));
        $adminConnection->grant_privileges(DB_USER, DB_PASS, DB_NAME_TF);
    } else if (Debug::DROP_DBs) {
        $adminConnection->sqlQuery("DROP DATABASE " . DB_NAME_TF);
    }
    $adminConnection->sql_close();

    $tfDbConnection = new DBO(DB_NAME_TF, DB_USER, DB_PASS);
    for ($array_index = 0; $array_index < sizeof($sql_queries); $array_index++) {
        $fileTableCreationString = file_get_contents(__DIR__ . "/src/asset/sql/{$sql_queries[$array_index]}", FILE_USE_INCLUDE_PATH);
        $conQuery = $tfDbConnection->prepare((string)$fileTableCreationString);
        $conQuery->execute();
        $conQuery->closeCursor();
    }

    $apiTypeQuery = $tfDbConnection->prepare("SELECT * FROM " . TB_API_TYPE);
    $apiTypeQuery->execute();
    $apiAnswer = $apiTypeQuery->fetchAll();
    $apiTypeQuery->closeCursor();

    if ((sizeof($apiAnswer) == 0)) {
        $insertApiType = $tfDbConnection->prepare("INSERT INTO " . TB_API_TYPE . " (id, api_name) VALUES ('', 'xero');");
        $insertApiType->execute();
        $apiTypeQuery->closeCursor();

        $insertApiType = $tfDbConnection->prepare("INSERT INTO " . TB_API_TYPE . " (id, api_name) VALUES ('', 'quickbooks');");
        $insertApiType->execute();
        $apiTypeQuery->closeCursor();
    }

    $tfDbConnection = null;


} catch (Exception $e) {
    echo $e->getMessage();
}