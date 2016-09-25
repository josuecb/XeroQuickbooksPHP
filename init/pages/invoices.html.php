<?php
/**
 * Created by PhpStorm.
 * User: Josue
 * Date: 7/19/2016
 * Time: 10:17 AM
 */
use JC\Helpers;

$root_folder = __DIR__;
$root_folder = str_replace("pages", "", $root_folder);
include_once $root_folder . 'src/api/quickbooks-sdk/config.php';  // Default V3 PHP SDK (v2.0.1) from IPP

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
        $Invoices = $dataService->FindAll('Invoice', 1, 500);

        if ($storeInDatabase) {
            $Payments = $dataService->FindAll('Payment', 1, 500);
            if ($Payments || (0 > count($Payments)))
                foreach ($Payments as $payment) {
                    $payment = (array)$payment;
                    $payment = json_decode(json_encode($payment), true);
                    Helpers::insertData(TB_PAYMENTS, Helpers::qbPaymentStructure($payment, $request_time));
                }
        }


        if (!$Invoices || (0 == count($Invoices)))
            die("No Invoices");
        $apiType = Helpers::QUICKBOOKS_CODE;
        include 'html_invoices.html.php';
        ?>
    </div>

    <?php
    include_once 'returnButton.html.php';
    ?>
</div>

</body>
</html>
