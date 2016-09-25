<?php
/**
 * Created by PhpStorm.
 * User: Josue
 * Date: 7/12/2016
 * Time: 12:09 PM
 */
use JC\Helpers;

$root_folder = __DIR__;
include_once $root_folder . '/init/src/config.php';

$root_path = Helpers::get_root_folder($root_folder);
if ($root_path == "localhost" or $root_path == "htdocs")
    $root_path = "";
else
    $root_path .= "/";

if (!isset($_POST['userid'])) {
    if (isset($_GET['admin'])) {
        if ($_GET['admin'] == '1') {
            include 'init/pages/adminPage.html.php';
        } else {
            include 'init/pages/authorizationPage.html.php';
        }
    } else {
        include 'init/pages/welcomePage.html.php';
    }
} else {
//    var_dump($_POST);

    $request_time = time();

    $storeInDatabase = false;

    // Only store data after 1 hour
    if (!Helpers::wasRequestedLessThanOneHour($_POST['userid'], $request_time)) {
        Helpers::insertData(TB_DATA_REQ, array(
            'userid' => $_POST['userid'],
            'timestamp' => $request_time
        ));
        $storeInDatabase = true;
    }

    if (isset($_POST['realmid']) and (preg_match('/^[0-9]+$/', $_POST['realmid']) == 0)) {
        $storedOauth = Helpers::getCredentialsFromUser($_POST['realmid']);
        include 'xero_access.php';
    } else {
        $realmId = $_POST['realmid'];
        //Specify QBO or QBD
        $serviceType = IntuitServicesType::QBO;

        if (sizeof($_POST) > 0) {
            $storedOauth = Helpers::getCredentialsFromUser($_POST['realmid']);

// Prep Service Context
            $requestValidator = new OAuthRequestValidator($storedOauth['oauthToken'],
                $storedOauth['oauthTokenSecret'],
                OAUTH_CONSUMER_KEY,
                OAUTH_CONSUMER_SECRET);
            $serviceContext = new ServiceContext($realmId, $serviceType, $requestValidator);
            if (!$serviceContext)
                exit("Problem while initializing ServiceContext.\n");

            // Prep Data Services
            $dataService = new DataService($serviceContext);
            if (!$dataService)
                exit("Problem while initializing DataService.\n");

            include 'init/pages/invoices.html.php';
        } else { // we need to verify if it gets here
            header('Location: /' . $root_path);
        }
    }
}
?>
