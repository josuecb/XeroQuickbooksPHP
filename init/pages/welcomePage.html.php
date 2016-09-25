<?php
/**
 * Created by PhpStorm.
 * User: Josue
 * Date: 7/28/2016
 * Time: 3:04 PM
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
        <div style="margin: auto; display: inline-block;">
            <div>
                <h1 class="title">Welcome to <?php echo $company_name; ?> </h1>

            </div>
            <div style="padding-top: 48px;">
                <div class="sub-title">
                    Are you a?
                </div>

                <div class="option-container">
                    <div class="option-box">
                        <a href="?admin=1" title="Admin">
                            <i class="fa fa-user-secret extra_large" aria-hidden="true"></i>
                        </a>
                    </div>
                    <div class="option-box">
                        <a href="?admin=0" title="User">
                            <i class="fa fa-user extra_large" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


<footer>

</footer>
</body>
</html>