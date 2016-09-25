<?php
/**
 * Created by PhpStorm.
 * User: Josue
 * Date: 7/17/2016
 * Time: 10:04 PM
 */

use JC\Helpers;

$root_folder = __DIR__;
$root_folder = str_replace("pages", "", $root_folder);
include_once $root_folder . 'src/config.php';

$users = Helpers::getAllUsers();

?>

<html>

<head>
    <title>Welcome to <?php echo $company_name; ?> Admin page</title>
</head>

<body>
<div style="display: inline-flex; height: 600px; width: 100%;;">
    <div style="margin: auto; display: inline-block; width: 80%;">
        <div>
            <h1 class="title">Welcome Admin, pick the user to check the invoices.</h1>
        </div>
        <div>
            <?php
            include 'registeredUserList.html.php';
            ?>
        </div>
    </div>
</div>

<?php
include_once 'returnButton.html.php';
?>

</body>
</html>
