<?php
/**
 * Created by PhpStorm.
 * User: Josue
 * Date: 7/16/2016
 * Time: 1:02 PM
 */


/**
 * App Credentials
 */

use JC\Helpers;

$root_folder = __DIR__;
include_once $root_folder . '/api/JC/Helpers.php';

$r_path = Helpers::get_root_folder($root_folder);
if ($r_path == "localhost" or $r_path == "htdocs")
    $r_path = "";
else
    $r_path = "/" . $r_path . "/";

define('OAUTH_CONSUMER_KEY', 'qyprdVzyrdcd1EvD4n1zHwKiji4eqx'); // Change to your costumer key
define('OAUTH_CONSUMER_SECRET', 'izpPFRybtswhmAwXv8H2japA7LJRp8a68fbZARTE'); // Change to your costumer secret
$extension_root = $r_path;
$company_name = "Josue C";


if (strlen(OAUTH_CONSUMER_KEY) < 5 OR strlen(OAUTH_CONSUMER_SECRET) < 5) {
    echo "<h3>Set the consumer key and secret in the config.php file before you run this example</h3>";
}


// Determine parent path for SDK
$sdkDir = $_SERVER['DOCUMENT_ROOT'] . $extension_root . '/init/src/api/quickbooks-sdk' . DIRECTORY_SEPARATOR;

if (!defined('PATH_SDK_ROOT'))
    define('PATH_SDK_ROOT', $sdkDir);

// Specify POPO class path; typically a direct child of the SDK path
if (!defined('POPO_CLASS_PATH'))
    define('POPO_CLASS_PATH', $sdkDir . 'Data' . DIRECTORY_SEPARATOR);

require_once(PATH_SDK_ROOT . 'XSD2PHP/src/com/mikebevz/xsd2php/Php2Xml.php');
require_once(PATH_SDK_ROOT . 'XSD2PHP/src/com/mikebevz/xsd2php/Bind.php');

// Includes all POPO classes; these are the source, dest, or both of the marshalling
set_include_path(get_include_path() . PATH_SEPARATOR . POPO_CLASS_PATH);
foreach (glob(POPO_CLASS_PATH . '/*.php') as $filename)
    require_once($filename);


// Specify the prefix pre-pended to POPO class names.  If you modify this value, you
// also need to rebuild the POPO classes, with the same prefix
if (!defined('PHP_CLASS_PREFIX'))
    define('PHP_CLASS_PREFIX', 'IPP');



require_once(PATH_SDK_ROOT . 'Core/ServiceContext.php');
require_once(PATH_SDK_ROOT . 'DataService/DataService.php');
require_once(PATH_SDK_ROOT . 'PlatformService/PlatformService.php');
require_once(PATH_SDK_ROOT . 'Core/OperationControlList.php');


?>

<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo $extension_root; ?>/init/src/css/style.css?" <?php echo time(); ?>>
<link rel="stylesheet"
      href="<?php echo $extension_root; ?>/init/src/font-awesome/css/font-awesome.min.css?" <?php echo time(); ?>>
