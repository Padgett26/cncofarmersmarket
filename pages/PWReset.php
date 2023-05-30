<?php
$t = (filter_input(INPUT_GET, 't', FILTER_SANITIZE_STRING) == "forgot") ? "forgot" : "x";

if ($t == "forgot") {
    ?>
    <div style="width: 210px; margin:auto;">
        <?php

    echo ($loginErr != "x") ? $loginErr : "";
    ?>
        <form method="post" action="index.php?page=PWReset">
            User Name:</br><input type="text" name="logInName" value="" size="50" /><br><br>
            Email address:</br><input type="text" name="email" value="" size="50" /><br><br>
            <input type="hidden" name="sendEmail" value="1" />
            <input type="submit" value=" Email verification " />
        </form>
    </div>
    <?php
}