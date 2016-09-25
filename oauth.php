<?php
use JC\Helpers;

$root_folder = __DIR__;
include_once $root_folder . '/init/src/config.php';

$root_path = Helpers::get_root_folder($root_folder);
if ($root_path == "localhost" or $root_path == "htdocs")
    $root_path = "";
else
    $root_path .= "/";


define('OAUTH_REQUEST_URL', 'https://oauth.intuit.com/oauth/v1/get_request_token');
define('OAUTH_ACCESS_URL', 'https://oauth.intuit.com/oauth/v1/get_access_token');
define('OAUTH_AUTHORISE_URL', 'https://appcenter.intuit.com/Connect/Begin');
session_start();

// The url to this page. it needs to be dynamic to handle runnable's dynamic urls
$root_path2 = 'http://' . $_SERVER['HTTP_HOST'] . "/" . $root_path . 'oauth.php';
define('CALLBACK_URL', $root_path2);
//echo CALLBACK_URL;
// cleans out the token variable if comming from
// connect to QuickBooks button
if (isset($_GET['start'])) {
    unset($_SESSION['token']);
}

try {
    $oauth = new OAuth(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);
    $oauth->enableDebug();
    $oauth->disableSSLChecks(); //To avoid the error: (Peer certificate cannot be authenticated with given CA certificates)
    if (!isset($_GET['oauth_token']) && !isset($_SESSION['token'])) {
        // step 1: get request token from Intuit
        $request_token = $oauth->getRequestToken(OAUTH_REQUEST_URL, CALLBACK_URL);
        $_SESSION['secret'] = $request_token['oauth_token_secret'];
        // step 2: send user to intuit to authorize
        header('Location: ' . OAUTH_AUTHORISE_URL . '?oauth_token=' . $request_token['oauth_token']);
    }

    if (isset($_GET['oauth_token']) && isset($_GET['oauth_verifier'])) {
        // step 3: request a access token from Intuit
        $oauth->setToken($_GET['oauth_token'], $_SESSION['secret']);
        $access_token = $oauth->getAccessToken(OAUTH_ACCESS_URL);

        $_SESSION['token'] = serialize($access_token);
        $_SESSION['realmId'] = $_REQUEST['realmId'];  // realmId is legacy for customerId
        $_SESSION['dataSource'] = $_REQUEST['dataSource'];

        $token = $_SESSION['token'];
        $realmId = $_SESSION['realmId'];
        $dataSource = $_SESSION['dataSource'];
        $secret = $_SESSION['secret'];


        $token = unserialize($_SESSION['token']);

        /**
         * My Implementation
         */
        Helpers::insertUser($_SESSION['realmId'], Helpers::QUICKBOOKS_CODE);
        $userId = Helpers::getUserId($_SESSION['realmId']);

        try {
            Helpers::insertCredentials($userId, $token['oauth_token'], $token['oauth_token_secret'], Helpers::QUICKBOOKS_CODE);
        } catch (Exception $e) {
            Helpers::updateCredentials($userId, $token['oauth_token'], $token['oauth_token_secret']);
        }

        // write JS to pup up to refresh parent and close popup
        ?>
        <script type="text/javascript">
            // gets current path plus folder
            window.opener.location.href = "<?php echo ""; ?>success";
            window.close();
        </script>
        <?php
    }

} catch (OAuthException $e) {
    echo "Got auth exception";
    echo '<pre>';
    print_r($e);
}

?>
