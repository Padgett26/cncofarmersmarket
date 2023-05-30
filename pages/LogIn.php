<?php
$displayReset = 0;
$err = "x";

if ($displayReset == 1) {
    ?>
    <div style="text-align: center; font-size: 1.25em; color: #cc4541; margin: 30px 0px;">
        Change your password
    </div>
    <div style="width: 210px; margin:auto;">
        <?php

    echo ($pwdResetErr != "") ? $pwdResetErr : "";
    ?>
        <form method="post" action="index.php?page=Home">
            <input type="password" name="pwd1" value="" size="30" /></br></br>
            <input type="password" name="pwd2" value="" size="30" /></br></br>
            <input type="hidden" name="pwdreset" value="<?php

    echo $rId;
    ?>" />
            <input type="submit" value=" Change my password " />
        </form>
    </div>
    <?php
} else {
    echo ($err == "x") ? "" : "<div style='text-align: center; font-size: 1.5em; color: #cc4541; margin-bottom: 20px;'>$err</div>";

    if (filter_input(INPUT_GET, 'logout', FILTER_SANITIZE_STRING) == "yep") {
        echo "You have been logged out.";
    } else {
        if (isBoard($myId) || isVendor($myId) || $custId >= 1) {
            ?>
            <div style='text-align: center; font-size: 1.5em; color: #cc4541; margin-bottom: 20px;'>You are already logged in.</div>
            <div style="width: 100px; margin:auto;"><form method="get" action="index.php?page=LogIn"><input type="hidden" name="logout" value="yep" /><input type="submit" value=" Log Out " /></form></div>
            <?php
        }
    }

    if ($myId == 0 && $vendorId == 0 && $custId == 0) {
        ?>
        <div style='text-align: center; font-size: 1.5em; color: #cc4541; cursor:pointer;' onclick="toggleview('logInWindow'), toggleview('createLogIn')">Log In.</div>
        <div id="logInWindow" style="width: 210px; margin:30px auto; display:block;">
            <?php
        if ($custId >= 1 && $loginErr == "x") {
            echo "You are logged in. Please select any menu item to continue.";
        } else {
            echo ($loginErr != "x") ? "<div style='text-align: center; font-size: 1.5em; color: #cc4541; margin-bottom: 20px;'>$loginErr</div>" : "";
            ?>
                <form method="post" action="index.php?page=Store">
                    User Name:</br><input type="text" name="name" value="" size="30" /><br><br>
                    Password:<br><input type="password" name="pwd" value="" size="30" /><br><br>
                    <input type="hidden" name="login" value="1" />
                    <input type="submit" value=" Log In " />
                </form><br><br>
                <a href="index.php?page=PWReset&t=forgot">Forgot your password?</a>
            </div>
            <div style='text-align: center; font-size: 1.5em; color: #cc4541; cursor:pointer; margin-top:50px;' onclick="toggleview('logInWindow'), toggleview('createLogIn')">Create Log In.</div>
            <div id="createLogIn" style="width: 210px; margin:30px auto; display:none;">
                <span style="text-align:center;">This form will create a customer account so you can make purchases in the store.<br>
                    If you are a vendor, please use the vendor agreement form on the Legal page.<br>
                    Being a vendor will also give you access to the store and log in options.</span>
                <form method="post" action="index.php?page=Store">
                    Your Name: <input type="text" name="displayName" value="" size="30"><br>
                    Email: <input type="text" name="email" value="" size="30"><br>
                    Phone: <input type="text" name="phone" value="" size="30"><br>
                    Street Address: <input type="text" name="address1" value="" size="30"><br>
                    City State Zip: <input type="text" name="address2" value="" size="30"><br><br>
                    User Name: <input type="text" name="logInName" value="" size="30"><br>
                    Password: <input type="password" name="pwd1" value="" size="30" /><br>
                    Retype pwd: <input type="password" name="pwd2" value="" size="30" /><br><br>
                    <input type="hidden" name="createLogIn" value="1" />
                    <input type="submit" value=" Create Log In " />
                </form><br><br>
            </div>
            <?php
        }
    }
}