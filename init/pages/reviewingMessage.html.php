<?php
/**
 * Created by PhpStorm.
 * User: Josue
 * Date: 8/2/2016
 * Time: 1:58 PM
 */
$root_folder = __DIR__;
$root_folder = str_replace("pages", "", $root_folder);
include_once $root_folder . 'src/config.php';

?>

<html>
<head>
    <title>Welcome to <?php echo $company_name; ?></title>
</head>

<body class="main-page">
<div>
    <div style="display: inline-flex; height: 600px;">
        <div style="margin: auto; display: inline-block; width: 80%;">
            <div>
                <h1 class="title">Your application has been submitted, We are going to review your account
                    information.</h1>
            </div>
        </div>
    </div>

    <?php
        include_once 'returnButton.html.php';
    ?>
</div>


<footer>

</footer>
</body>
</html>