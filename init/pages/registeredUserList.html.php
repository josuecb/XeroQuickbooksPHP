<?php
/**
 * Created by PhpStorm.
 * User: Josue
 * Date: 8/2/2016
 * Time: 4:30 PM
 */
?>

<div class="table">
    <div class="table-inner-wrapper">
        <div class="rows">
            <div class="columns">
                <div class="column-inner-wrapper">
                        <span>
                            Users Id
                        </span>
                </div>
            </div>

            <div class="columns">
                <div class="column-inner-wrapper">
                    <div>
                        RealmId
                    </div>
                </div>
            </div>

            <div class="columns">
                <div class="column-inner-wrapper">
                    <div>
                        Subscribed In
                    </div>
                </div>
            </div>

            <div class="columns">
                <div class="column-inner-wrapper">
                    <div>
                        API Type
                    </div>
                </div>
            </div>

            <div class="columns">
                <div class="column-inner-wrapper">
                    <div>
                        Select
                    </div>
                </div>
            </div>
        </div>
        <?php
        if ($users != null) {
            foreach ($users as $user) {
                ?>


                <div class="rows">

                    <div class="columns" title="<?php echo $user['userid']; ?>">
                        <div class="column-inner-wrapper">
                            <span>
                                <?php echo $user['userid']; ?>
                            </span>
                        </div>
                    </div class="columns">

                    <div class="columns" title="<?php echo $user['realmid']; ?>">
                        <div class="column-inner-wrapper">
                                <span>
                                           <?php
                                           if (strlen($user['realmid']) > 10)
                                               echo substr($user['realmid'], 0, 10) . "...";
                                           else {
                                               echo $user['realmid'];
                                           }
                                           ?>
                                </span>
                        </div>
                    </div>

                    <?php $nUpdated = date("l jS F, g:i a", $user['timestamp']) ?>
                    <div class="columns" title="<?php echo $nUpdated; ?>">
                        <div class="column-inner-wrapper">
                               <span>
                                    <?php
                                    if (strlen($nUpdated) > 10)
                                        echo substr($nUpdated, 0, 10) . "...";
                                    else {
                                        echo $nUpdated;
                                    }
                                    ?>
                               </span>
                        </div>
                    </div>

                    <div class="columns">
                        <div class="column-inner-wrapper">
                               <span>
                                    <?php
                                    if ($user['accounting_api'] == '1') {
                                        echo "Xero";
                                    } else {
                                        echo "Quickbooks";
                                    }
                                    ?>
                               </span>
                        </div>
                    </div>
                    <div class="columns">
                        <div class="column-inner-wrapper">
                            <form action="?user" class="columns" method="post">
                                    <span>
                                        <input type="submit" name="usersubmit" value="View">
                                        <input type="hidden" name="userid" value="<?php echo $user['userid']; ?>">
                                        <input type="hidden" name="realmid" value="<?php echo $user['realmid']; ?>">
                                  </span>
                            </form>

                        </div>

                    </div class="columns">
                </div>
                <?php
            }
        }
        ?>
    </div>

</div>
