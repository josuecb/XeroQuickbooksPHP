<?php
require 'config.php';

$XeroOAuth = oauth_config();

$initialCheck = $XeroOAuth->diagnostics();
$checkErrors = count($initialCheck);
if ($checkErrors > 0) {
    // you could handle any config errors here, or keep on truckin if you like to live dangerously
    foreach ($initialCheck as $check) {
        echo 'Error: ' . $check . PHP_EOL;
    }
} else {
    $here = XeroOAuth::php_self();
    session_start();
    $oauthSession = retrieveSession();

    include 'tests/tests.php';

    if (isset ($_REQUEST ['oauth_verifier'])) {
        $XeroOAuth->config ['access_token'] = $_SESSION ['oauth'] ['oauth_token'];
        $XeroOAuth->config ['access_token_secret'] = $_SESSION ['oauth'] ['oauth_token_secret'];

        $code = $XeroOAuth->request('GET', $XeroOAuth->url('AccessToken', ''), array(
            'oauth_verifier' => $_REQUEST ['oauth_verifier'],
            'oauth_token' => $_REQUEST ['oauth_token']
        ));

        if ($XeroOAuth->response ['code'] == 200) {

            $response = $XeroOAuth->extract_params($XeroOAuth->response ['response']);
            $session = persistSession($response);

            unset ($_SESSION ['oauth']);
            header("Location: {$here}");
        } else {
            outputError($XeroOAuth);
        }
        // start the OAuth dance
    } elseif (isset ($_REQUEST ['authenticate']) || isset ($_REQUEST ['authorize'])) {
        $params = array(
            'oauth_callback' => OAUTH_CALLBACK
        );

        $response = $XeroOAuth->request('GET', $XeroOAuth->url('RequestToken', ''), $params);

        if ($XeroOAuth->response ['code'] == 200) {

            $scope = "";
            // $scope = 'payroll.payrollcalendars,payroll.superfunds,payroll.payruns,payroll.payslip,payroll.employees,payroll.TaxDeclaration';
            if ($_REQUEST ['authenticate'] > 1)
                $scope = 'payroll.employees,payroll.payruns,payroll.timesheets';

            $XeroOAuth->extract_params($XeroOAuth->response ['response']);
//			print_r ( $XeroOAuth->extract_params ( $XeroOAuth->response ['response'] ) );
            $_SESSION ['oauth'] = $XeroOAuth->extract_params($XeroOAuth->response ['response']);

            $authurl = $XeroOAuth->url("Authorize", '') . "?oauth_token={$_SESSION['oauth']['oauth_token']}&scope=" . $scope;
            echo $authurl;
//			echo '<p>To complete the OAuth flow follow this URL: <a href="' . $authurl . '">' . $authurl . '</a></p>';
        } else {
            outputError($XeroOAuth);
        }
    }

//	testLinks ();
}
