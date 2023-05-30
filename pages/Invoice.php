<div style="text-align: center; padding:5px; font-size: 1.5em; color: #cc4541; margin-bottom: 40px; text-decoration: underline;">Purchase History</div>

<?php
$viewC = 0;
if (filter_input(INPUT_POST, 'getCust', FILTER_SANITIZE_NUMBER_INT)) {
    $viewC = filter_input(INPUT_POST, 'getCust', FILTER_SANITIZE_NUMBER_INT);
}

$C = ($viewC == 0) ? $custId : $viewC;

if (filter_input(INPUT_POST, 'show', FILTER_SANITIZE_STRING)) {
    $show = filter_input(INPUT_POST, 'show', FILTER_SANITIZE_STRING);
} else {
    $show = 'x';
}

if (isBoard($myId)) {
    echo "<div style='text-align:center; margin-bottom:30px;'>Select customer to view:<br><form action='index.php?page=Invoice' method='post'><select name='getCust' size='1'>\n";
    $getC = $db->query("SELECT id, name FROM customers ORDER BY name");
    while ($getCR = $getC->fetch()) {
        $id = $getCR['id'];
        $name = $getCR['name'];
        echo "<option value='$id'";
        echo ($C == $id) ? " selected" : "";
        echo ">$name</option>\n";
    }
    echo "</select><br><input type='submit' value=' Get Customer '></form></div>\n";
}
?>

<div style="text-align: left; padding:5px; font-size: 1.25em; color: #cc4541; margin-bottom: 10px; text-decoration: underline; cursor:pointer;" onclick="toggleview('editInfo')">Edit my Information</div>
<form action="index.php?page=Invoice" method="post">
    <?php
    $getInfo = $db->prepare("SELECT * FROM customers WHERE id = ?");
    $getInfo->execute(array(
            $C
    ));
    $gIR = $getInfo->fetch();
    $name = $gIR['name'];
    $email = $gIR['email'];
    $phone = $gIR['phone'];
    $address1 = $gIR['address1'];
    $address2 = $gIR['address2'];
    ?>
    <table id="editInfo" style="border:1px solid black; display:none; margin-bottom:30px;" cellpadding="5px" cellspacing="2px">
        <tr style="border:1px solid black;">
            <td>Name:</td>
            <td><input type="text" name="name" value="<?php

            echo $name;
            ?>"></td>
        </tr>
        <tr style="border:1px solid black;">
            <td>Email:</td>
            <td><input type="text" name="email" value="<?php

            echo $email;
            ?>"></td>
        </tr>
        <tr style="border:1px solid black;">
            <td>Phone:</td>
            <td><input type="text" name="phone" value="<?php

            echo $phone;
            ?>"></td>
        </tr>
        <tr style="border:1px solid black;">
            <td>Street Address:</td>
            <td><input type="text" name="address1" value="<?php

            echo $address1;
            ?>"></td>
        </tr>
        <tr style="border:1px solid black;">
            <td>City, State Zip:</td>
            <td><input type="text" name="address2" value="<?php

            echo $address2;
            ?>"></td>
        </tr>
        <tr style="border:1px solid black;">
            <td></td>
            <td><input type="hidden" name="editInfo" value="<?php

            echo $C;
            ?>"><input type="submit" value=" Edit Info "></td>
        </tr>
    </table>
</form>
<?php
if (isBoard($myId)) {
    ?>
    <div style="text-align: left; padding:5px; font-size: 1.25em; color: #cc4541; margin-bottom: 10px; text-decoration: underline; cursor:pointer;" onclick="toggleview('openOrders')">Open Orders</div>
    <?php
    $display = ($show == "openOrders") ? "block" : "none";
    ?>
    <table id="openOrders" style="border:1px solid black; display:<?php

    echo $display;
    ?>; margin-bottom:30px;" cellpadding="5px" cellspacing="2px">
        <?php
    $getOcount = $db->prepare(
            "SELECT COUNT(*) FROM onlineSales WHERE custId = ? && paid = '0'");
    $getOcount->execute(array(
            $C
    ));
    $gOc = $getOcount->fetch();
    if ($gOc[0] >= 1) {
        ?>
            <tr>
                <td colspan='9' style='text-align:right;'>
                    <form action='index.php?page=Invoice' method='post'>
                        Delivery Method: <select name="deliveryM" size="1">
                            <?php
        $getdm = $db->query("SELECT * FROM deliveryMethods");
        while ($getdmR = $getdm->fetch()) {
            $dmId = $getdmR['id'];
            $dm = $getdmR['deliveryMethod'];
            echo "<option value='$dmId'";
            echo ($dmId == 1) ? " selected" : "";
            echo ">$dm</option>";
        }
        ?>
                        </select>
                </td>
            </tr>
            <tr>
                <th>Order Date</th>
                <th>Product</th>
                <th>Size</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Filled</th>
                <th>Delivery Method</th>
                <th>Vendor</th>
                <th>Amt Due</th>
            </tr>
            <tr><td colspan='9'><div style="width:100%; height:3px; background-color: #cc4541;"></div></td></tr>
            <?php
        $deliveryM = array();
        $currentOrder = array();
        $subtotal = 0;
        $getOpen = $db->prepare(
                "SELECT * FROM onlineSales WHERE custId = ? && paid = '0' ORDER BY transactionDate");
        $getOpen->execute(array(
                $C
        ));
        while ($gOR = $getOpen->fetch()) {
            $oId = $gOR['id'];
            $oVendorId = $gOR['vendorId'];
            $oProductId = $gOR['productId'];
            $oTransactionDate = $gOR['transactionDate'];
            $oQuantity = $gOR['quantity'];
            $oPrice = $gOR['price'];
            $oFilled = $gOR['filled'];
            $oDeliveryMethod = $gOR['deliveryMethod'];

            $currentOrder[$oId] = $oFilled;
            $deliveryM[] = $oDeliveryMethod;

            $getD = $db->prepare(
                    "SELECT deliveryMethod, deliveryFee FROM deliveryMethods WHERE id = ?");
            $getD->execute(array(
                    $oDeliveryMethod
            ));
            $getDR = $getD->fetch();
            $dm = $getDR['deliveryMethod'];
            $df = $getDR['deliveryFee'];

            $getV = $db->prepare("SELECT displayName FROM vendors WHERE id = ?");
            $getV->execute(array(
                    $oVendorId
            ));
            $getVR = $getV->fetch();
            $vendorName = $getVR['displayName'];

            $getP = $db->prepare(
                    "SELECT t1.name, t2.size FROM product AS t1 INNER JOIN productSizes AS t2 ON t1.size = t2.id WHERE t1.id = ?");
            $getP->execute(array(
                    $oProductId
            ));
            $getPR = $getP->fetch();
            $productName = $getPR[0];
            $productSize = $getPR[1];

            echo "<tr>";
            echo "<td>" . date("F j, Y, g:i a", $oTransactionDate) . "</td>";
            echo "<td>$productName</td>";
            echo "<td>$productSize</td>";
            echo "<td>" . money($oPrice) . "</td>";
            echo "<td><select name='qty$oId' size='1'>";
            for ($i = $oQuantity; $i >= 0; $i --) {
                echo "<option value='$i'";
                echo ($oQuantity == $i) ? " selected" : "";
                echo ">$i</option>";
            }
            echo "</select></td>";
            echo "<td><select name='filled$oId' size='1'><option value='0'";
            echo ($oFilled == 0) ? " selected" : "";
            echo ">0</option>";
            for ($i = $oQuantity; $i >= 1; $i --) {
                echo "<option value='$i'";
                echo ($oFilled == $i) ? " selected" : "";
                echo ">$i</option>";
            }
            echo "</select></td>";
            echo "<td>$dm - $$df</td>";
            echo "<td>$vendorName</td>";
            echo ($oFilled >= 1) ? "<td>" . money($oFilled * $oPrice) . "</td>" : "<td></td>";
            $subtotal += ($oFilled * $oPrice);
            echo "</tr>\n";
            echo "<tr><td colspan='9'><div style='width:100%; height:3px; background-color: #cc4541;'></div></td></tr>\n";
        }
        echo "<tr><td colspan='9' style='text-align:right;'><input type='hidden' name='tally' value='1'><input type='hidden' name='getCust' value='$C'><input type='hidden' name='show' value='openOrders'><input type='submit' value=' Tally '></form></td></tr>\n";
        echo "<tr><td colspan='7'></td><td><form action='index.php?page=Invoice' method='post'>Subtotal</td><td>" .
                money($subtotal) .
                "<input type='hidden' name='subtotal' value='$subtotal'></td></tr>\n";
        echo "<tr><td colspan='7'></td><td>Delivery Method</td><td>";
        $deli = array_unique($deliveryM);
        $delivery = 0;
        foreach ($deli as $k => $v) {
            $d = $db->prepare(
                    "SELECT deliveryFee FROM deliveryMethods WHERE id = ?");
            $d->execute(array(
                    $v
            ));
            $dr = $d->fetch();
            $delivery += $dr['deliveryFee'];
        }
        echo money($delivery) .
                "<input type='hidden' name='delivery' value='$delivery'></td></tr>\n";
        echo "<tr><td colspan='7'></td><td>Total bill</td><td>" .
                money($subtotal + $delivery) . "</td></tr>\n";
        echo "<tr><td colspan='7'></td><td>Received money</td><td><input type='checkbox' onchange='moneyrcvd()'></td></tr>\n";
        echo "<tr><td colspan='7'></td><td>Check Number<br>For CC enter 0</td><td><input type='text' name='ckNumber' value='' size='5'></td></tr>\n";
        echo "<tr><td colspan='8'></td><td>";
        foreach ($currentOrder as $k => $v) {
            echo "<input type='hidden' name='order$k' value='$v'>";
        }
        echo "<input type='hidden' name='finalize' value='1'><input type='hidden' name='getCust' value='$C'><input type='submit' id='invoiceSubmit' disabled value=' Finalize '></form></td></tr>\n";
    }
    ?>
    </table>
    <?php
} else {
    ?>
    <div style="text-align: left; padding:5px; font-size: 1.25em; color: #cc4541; margin-bottom: 10px; text-decoration: underline; cursor:pointer;" onclick="toggleview('openOrders')">Open Orders</div>
    <table id="openOrders" style="border:1px solid black; display:none; margin-bottom:30px;" cellpadding="5px" cellspacing="2px">
        <?php
    $getOcount = $db->prepare(
            "SELECT COUNT(*) FROM onlineSales WHERE custId = ? && paid = '0'");
    $getOcount->execute(array(
            $C
    ));
    $gOc = $getOcount->fetch();
    if ($gOc[0] >= 1) {
        ?>
            <tr>
                <th>Order Date</th>
                <th>Product</th>
                <th>Size</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Filled</th>
                <th>Delivery Method</th>
                <th>Vendor</th>
            </tr>
            <tr><td colspan='8'><div style="width:100%; height:3px; background-color: #cc4541;"></div></td></tr>
            <?php
        $getOpen = $db->prepare(
                "SELECT * FROM onlineSales WHERE custId = ? && paid = '0' ORDER BY transactionDate");
        $getOpen->execute(array(
                $C
        ));
        while ($gOR = $getOpen->fetch()) {
            $oVendorId = $gOR['vendorId'];
            $oProductId = $gOR['productId'];
            $oTransactionDate = $gOR['transactionDate'];
            $oQuantity = $gOR['quantity'];
            $oPrice = $gOR['price'];
            $oFilled = $gOR['filled'];
            $oDeliveryMethod = $gOR['deliveryMethod'];

            $getD = $db->prepare(
                    "SELECT deliveryMethod, deliveryFee FROM deliveryMethods WHERE id = ?");
            $getD->execute(array(
                    $oDeliveryMethod
            ));
            $getDR = $getD->fetch();
            $dm = $getDR['deliveryMethod'];
            $df = $getDR['deliveryFee'];

            $getV = $db->prepare("SELECT displayName FROM vendors WHERE id = ?");
            $getV->execute(array(
                    $oVendorId
            ));
            $getVR = $getV->fetch();
            $vendorName = $getVR['displayName'];

            $getP = $db->prepare(
                    "SELECT t1.name, t2.size FROM product AS t1 INNER JOIN productSizes AS t2 ON t1.size = t2.id WHERE t1.id = ?");
            $getP->execute(array(
                    $oProductId
            ));
            $getPR = $getP->fetch();
            $productName = $getPR[0];
            $productSize = $getPR[1];

            echo "<tr>";
            echo "<td>" . date("F j, Y, g:i a", $oTransactionDate) . "</td>";
            echo "<td>$productName</td>";
            echo "<td>$productSize</td>";
            echo "<td>" . money($oPrice) . "</td>";
            echo "<td>$oQuantity</td>";
            echo "<td>$oFilled</td>";
            echo "<td>$dm - $$df</td>";
            echo "<td>$vendorName</td>";
            echo "</tr>\n";
            echo "<tr><td colspan='8'><div style='width:100%; height:3px; background-color: #cc4541;'></div></td></tr>\n";
        }
    }
    ?>
    </table>
<?php
}
?>

<div style="text-align: left; padding:5px; font-size: 1.25em; color: #cc4541; margin-bottom: 10px; text-decoration: underline; cursor:pointer;" onclick="toggleview('filledOrders')">Filled Orders</div>
<table id="filledOrders" style="border:1px solid black; display:none; margin-bottom:30px;" cellpadding="5px" cellspacing="2px">
    <?php
    $getFcount = $db->prepare(
            "SELECT COUNT(*) FROM onlineSales WHERE custId = ? && paid = '1'");
    $getFcount->execute(array(
            $C
    ));
    $gFc = $getFcount->fetch();
    if ($gFc[0] >= 1) {
        ?>
        <tr>
            <th>Order Date</th>
            <th>Product</th>
            <th>Size</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Filled</th>
            <th>Delivery Method</th>
            <th>Vendor</th>
        </tr>
        <tr><td colspan='8'><div style="width:100%; height:3px; background-color: #cc4541;"></div></td></tr>
                <?php
        $getFilled = $db->prepare(
                "SELECT * FROM onlineSales WHERE custId = ? && paid = '1' ORDER BY transactionDate DESC");
        $getFilled->execute(array(
                $C
        ));
        while ($gFR = $getFilled->fetch()) {
            $fVendorId = $gFR['vendorId'];
            $fProductId = $gFR['productId'];
            $fTransactionDate = $gFR['transactionDate'];
            $fQuantity = $gFR['quantity'];
            $fPrice = $gFR['price'];
            $fFilled = $gFR['filled'];
            $fDeliveryMethod = $gFR['deliveryMethod'];

            $getD = $db->prepare(
                    "SELECT deliveryMethod FROM deliveryMethods WHERE id = ?");
            $getD->execute(array(
                    $fDeliveryMethod
            ));
            $getDR = $getD->fetch();
            $dm = $getDR['deliveryMethod'];

            $getV = $db->prepare("SELECT displayName FROM vendors WHERE id = ?");
            $getV->execute(array(
                    $fVendorId
            ));
            $getVR = $getV->fetch();
            $vendorName = $getVR['displayName'];

            $getP = $db->prepare(
                    "SELECT t1.name, t2.size FROM product AS t1 INNER JOIN productSizes AS t2 ON t1.size = t2.id WHERE t1.id = ?");
            $getP->execute(array(
                    $fProductId
            ));
            $getPR = $getP->fetch();
            $productName = $getPR[0];
            $productSize = $getPR[1];

            echo "<tr>";
            echo "<td>" . date("F j, Y, g:i a", $fTransactionDate) . "</td>";
            echo "<td>$productName</td>";
            echo "<td>$productSize</td>";
            echo "<td>" . money($fPrice) . "</td>";
            echo "<td>$fQuantity</td>";
            echo "<td>$fFilled</td>";
            echo "<td>$dm</td>";
            echo "<td>$vendorName</td>";
            echo "</tr>\n";
            echo "<tr><td colspan='8'><div style='width:100%; height:3px; background-color: #cc4541;'></div></td></tr>\n";
        }
    }
    ?>
</table>