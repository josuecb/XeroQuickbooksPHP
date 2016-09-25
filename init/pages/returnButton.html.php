<?php
/**
 * Created by PhpStorm.
 * User: Josue
 * Date: 9/22/2016
 * Time: 11:33 PM
 */


if (isset($_POST['action']))
    header('Location: http://' . $_SERVER['HTTP_HOST'] . $extension_root);
?>
<div class="right-corner-button">
    <form action="?action" class="columns" method="post">
        <button class="modern-button big-padding-button">Return</button>
        <input type="hidden" name="action" value="true">
    </form>
</div>