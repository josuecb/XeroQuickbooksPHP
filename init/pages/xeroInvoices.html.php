<?php
/**
 * Created by PhpStorm.
 * User: Josue
 * Date: 8/4/2016
 * Time: 1:44 PM
 */
use JC\Helpers;

$apiType = Helpers::XERO_CODE;

$root_folder = __DIR__;
$root_folder = str_replace("pages", "", $root_folder);
include_once $root_folder . 'src/config.php';

?>


<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to <?php echo $company_name; ?> Admin page</title>
    <script type="text/javascript" src="https://appcenter.intuit.com/Content/IA/intuit.ipp.anywhere.js"></script>
</head>

<body>
<div>
    <div style="width: 100%;">
        <div style="width: inherit; text-align: center;">
            <h1 class="title">
                <?php echo $_POST['userid']; ?>'s Invoices
            </h1>
        </div>
    </div>
    <div>
        <?php
        include 'html_invoices.html.php';
        ?>
    </div>
</div>

</body>
</html>
