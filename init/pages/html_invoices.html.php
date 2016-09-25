<?php
/**
 * Created by PhpStorm.
 * User: Josue
 * Date: 8/3/2016
 * Time: 4:38 PM
 */
use JC\Helpers;


$categoryArray = array(
    "Inv. Id",
    "Inv. Number",
    "Inv. Type",
    "Com. Id",
    "Com. Name",
    "Txn Status",
    "Txn date",
    "Date Due",
    "Tax",
    "Sub Total",
    "Total",
    "Currency",
    "Paid",
    "Payments",
    "Balance", // it's amount due
    "Credited",
);

?>

<div class="table">
    <div class="table-inner-wrapper">
        <div class="rows">
            <?php
            foreach ($categoryArray as $category) {
                ?>
                <div class="columns c_small">
                    <div class="column-inner-wrapper">
                        <span>
                            <?php echo $category; ?>
                        </span>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        <?php

        foreach ($Invoices as $invoice) {
            $invoice = (array)$invoice;
//            var_dump(json_encode($invoice));
            $invoice = json_decode(json_encode($invoice), true);
            if ($apiType == Helpers::XERO_CODE) {
                $dataToInsert = Helpers::xeroStructure($_POST['userid'], $invoice, $request_time, $storeInDatabase);
            } else {
                $dataToInsert = Helpers::qbStructure($_POST['userid'], $invoice, $request_time);
            }
            if ($storeInDatabase)
                Helpers::insertData(TB_INVOICES, $dataToInsert);

            $countData = 0;
            ?>
            <div class="rows">
                <?php
                foreach ($dataToInsert as $key => $value) {
                    if ($key != 'userid' and $countData < 16) {
                        ?>
                        <div class="columns c_small" title="<?php echo $value; ?>">
                            <div class="column-inner-wrapper">
                            <span>
                                <?php
                                if (strlen($value) > 10) {
                                    echo substr($value, 0, 10) . "...";
                                } elseif ($value == null or empty($value)) {
                                    echo "NULL";
                                } else {
                                    echo $value;
                                }
                                ?>
                            </span>
                            </div>
                        </div class="columns">
                        <?php
                        $countData++;
                    }
                }
                ?>
            </div>
            <?php
        }
        ?>
    </div>

    <?php
    include_once 'returnButton.html.php';
    ?>

</div>

