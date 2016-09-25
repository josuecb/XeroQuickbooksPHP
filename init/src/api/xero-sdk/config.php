<?php
/**
 * Created by PhpStorm.
 * User: Josue
 * Date: 9/22/2016
 * Time: 8:23 PM
 */


define('XERO_OAUTH_CONSUMER_KEY', '1OHZ91QKUO0PL1K5OMT6W8FBWY5WUB'); // Change to your costumer key
define('XERO_OAUTH_CONSUMER_SECRET', 'YLC76M8WKJBIXV8DQXZBBXHLMC9GJU'); // Change to your costumer secret

/**
 * Get extension root folder
 */
$root_folder = __DIR__;

$getPrevPath = explode("init", $root_folder);
$get_root = explode("\\", $getPrevPath[0]);

$size = sizeof($get_root);
# last item will always be empty because it is always '\'
$root_path  =  $get_root[$size - 2];
if ($root_path == "localhost" or $root_path == "htdocs")
    $root_path = "";
else
    $root_path .= "/";

define('EXTENSION_ROOT', '/' . $root_path);

include 'lib/XeroOAuth.php';

function oauth_config()
{
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    /**
     * Define for file includes
     */
    define('BASE_PATH', dirname(__FILE__));

    /**
     * Define which app type you are using:
     * Private - private app method
     * Public - standard public app method
     * Public - partner app method
     */
    define("XRO_APP_TYPE", "Public");

    /**
     * Set a user agent string that matches your application name as set in the Xero developer centre
     */
    $useragent = "Xero-OAuth-PHP Public";

    /**
     * Set your callback url or set 'oob' if none required
     * Make sure you've set the callback URL in the Xero Dashboard
     * Go to https://api.xero.com/Application/List and select your application
     * Under OAuth callback domain enter localhost or whatever domain you are using.
     */
    define("OAUTH_CALLBACK", 'http://localhost/' . EXTENSION_ROOT . '/init/src/api/xero-sdk/public.php');
    /**
     * Application specific settings
     * Not all are required for given application types
     * consumer_key: required for all applications
     * consumer_secret: for partner applications, set to: s (cannot be blank)
     * rsa_private_key: application certificate private key - not needed for public applications
     * rsa_public_key: application certificate public cert - not needed for public applications
     */

    include 'tests/testRunner.php';

    $signatures = array(
        'consumer_key' => XERO_OAUTH_CONSUMER_KEY,
        'shared_secret' => XERO_OAUTH_CONSUMER_SECRET,
        // API versions
        'core_version' => '2.0',
        'payroll_version' => '1.0',
        'file_version' => '1.0'
    );

    if (XRO_APP_TYPE == "Private" || XRO_APP_TYPE == "Partner") {
        $signatures ['rsa_private_key'] = BASE_PATH . '/certs/privatekey.pem';
        $signatures ['rsa_public_key'] = BASE_PATH . '/certs/publickey.cer';
    }
    if (XRO_APP_TYPE == "Partner") {
        $signatures ['curl_ssl_cert'] = BASE_PATH . '/certs/entrust-cert-RQ3.pem';
        $signatures ['curl_ssl_password'] = '1234';
        $signatures ['curl_ssl_key'] = BASE_PATH . '/certs/entrust-private-RQ3.pem';
    }

    $XeroOAuth = new XeroOAuth (array_merge(array(
        'application_type' => XRO_APP_TYPE,
        'oauth_callback' => OAUTH_CALLBACK,
        'user_agent' => $useragent
    ), $signatures));

    return $XeroOAuth;
}