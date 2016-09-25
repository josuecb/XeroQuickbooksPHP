<?php
/**
 * Created by PhpStorm.
 * User: Josue
 * Date: 7/28/2016
 * Time: 3:32 PM
 */

use JC\Helpers;

ob_start();

$root_folder = __DIR__;
$root_folder = str_replace("pages", "", $root_folder);
include_once $root_folder . 'src/config.php';

$root_path = Helpers::get_root_folder($root_folder);
if ($root_path == "localhost" or $root_path == "htdocs")
    $root_path = "";
else
    $root_path .= "/";

error_reporting(E_ERROR | E_PARSE);
session_start();

?>

<html>
<head>
    <title>Welcome to <?php echo $company_name; ?></title>
    <script type="text/javascript" src="/<?php echo $root_path; ?>init/src/js/jquery.js"></script>
    <script type="text/javascript" src="https://appcenter.intuit.com/Content/IA/intuit.ipp.anywhere.js"></script>
    <script type="text/javascript" src="/<?php echo $root_path; ?>init/src/js/JC.js"></script>
</head>

<body class="main-page">

<div>
    <div style="display: inline-flex; height: 600px;">
        <div style="margin: auto; display: inline-block;">
            <div style="padding-top: 48px;">
                <div class="sub-title">
                    Do you have an account with?
                </div>

                <div class="option-container">
                    <div class="option-box">
                        <a onclick="openWindow()" title="Xero">
                            <img class="button-img" src="/<?php echo $root_path; ?>init/src/imges/xero_logo.png">
                        </a>
                    </div>
                    <div style="transform: translate(0px, -40%);" class="option-box">
                        <ipp:connectToIntuit>
                        </ipp:connectToIntuit>
                    </div>
                </div>
            </div>
            <script>

            </script>
        </div>
    </div>
</div>

<?php
include_once 'returnButton.html.php';
?>

<footer>

</footer>
<script type="text/javascript" src="/<?php echo $root_folder; ?>init/src/js/JC.js"></script>

</body>

</html>
