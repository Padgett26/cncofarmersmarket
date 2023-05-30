<div style="text-align: center; color: #cc4541; font-size: 1.5em; margin-bottom: 20px; text-decoration: underline;">Online Market</div>

<?php
// Grab products
$products = array();
$pro = $db->prepare("SELECT * FROM product");
$pro->execute();
while ($prod = $pro->fetch()) {
    $products[] = new storeProduct($prod['id'], $prod['vendorId'],
            $prod['name'], $prod['description'], $prod['quantity'],
            $prod['size'], $prod['price'], $prod['picName'], $prod['ref']);
}

// Clean up cart
$xTime = ($time - 86400);
$x2 = $db->prepare(
        "DELETE FROM onlineSales WHERE inCart = ? && transactionDate < ?");
$x2->execute(array(
        '1',
        $xTime
));

if ($custId == 0) {
    echo ($loginErr != "x") ? $loginErr : "";
    echo "<div style=''>To place orders online, please <a href='index.php?page=LogIn'>sign / register</a></div>";
} else {

    if (isBoard($myId)) {
        echo "<table cellpaddding='5px' cellspacing='2px' style='border:1px solid black; margin-bottom:10px;'>\n";
        echo "<tr>\n";
        echo "<td colspan='3' style='text-align:center;'>";
        echo "<span style='cursor:pointer; color: #cc4541; font-weight:bold;'>Edit delivery methods</span>";
        echo "</td></tr>\n";
        echo "<tr><td style='border:1px solid black;'>Delivery method</td><td style='border:1px solid black;'>Delivery fee</td><td style='border:1px solid black;'>Delete<form action='index.php?page=Store' method='post'></td></tr>";
        $getDM = $db->query("SELECT * FROM deliveryMethods");
        while ($getDMR = $getDM->fetch()) {
            $dId = $getDMR['id'];
            $dm = $getDMR['deliveryMethod'];
            $df = $getDMR['deliveryFee'];
            echo "<tr><td style='border:1px solid black;'><input type='text' name='deliveryMethod$dId' value='$dm'></td><td style='border:1px solid black;'><input type='text' name='deliveryFee$dId' value='$df'></td><td style='border:1px solid black;'><input type='checkbox' name='del$dId' value='1'></td></tr>";
        }
        echo "<tr><td style='border:1px solid black;'><input type='text' name='deliveryMethod0' value=''></td><td style='border:1px solid black;'><input type='text' name='deliveryFee0' value=''></td><td style='border:1px solid black;'>NEW</td></tr>";
        echo "<tr><td><input type='hidden' name='deliveryUp' value='1'><input type='submit' value=' Make Changes '></td><td colspan='2'></form></td></tr>";
        echo "</table>";
    }

    $cart = $db->prepare(
            "SELECT COUNT(*) FROM onlineSales WHERE custId = ? && inCart = '1'");
    $cart->execute(array(
            $custId
    ));
    $cartR = $cart->fetch();
    $cartItems = $cartR[0];
    echo "<div style='text-align:right; width:100%;'><span style='color: #cc4541; font-weight:bold; cursor:pointer;' onclick='toggleview(\"myCart\")'>$cartItems Items in cart</span></div>";
    echo "<table id='myCart' cellpaddding='5px' cellspacing='2px' style='width:100%; border:1px solid black; display:none;'>\n";
    echo "<tr><td colspan='6'><div style='text-align: center; color: #cc4541; font-size: 1.25em; text-decoration: underline; width:100%; margin-bottom:10px;'>What is in my cart</div></td></tr>";
    echo "<tr style='border:1px solid black;'>\n";
    echo "<td style='text-align:center;' colspan='3'><form action='index.php?page=Store' method='post'>Delivery Method: <select name='deliveryMethod' size='1'>";
    $delivery = $db->query("SELECT * FROM deliveryMethods ORDER BY deliveryFee");
    while ($deliveryR = $delivery->fetch()) {
        $deliveryId = $deliveryR['id'];
        $deliveryMethod = $deliveryR['deliveryMethod'];
        $deliveryFee = $deliveryR['deliveryFee'];
        echo "<option value='$deliveryId'>$deliveryMethod -- $$deliveryFee</option>";
    }
    echo "</select><input type='hidden' name='placeOrder' value='$custId'><input type='submit' value=' Place Order '></form></td>\n";
    echo "<td style='text-align:center;' colspan='3'><form action='index.php?page=Store' method='post'><input type='hidden' name='deleteOrder' value='$custId'><input type='submit' value=' Delete Order '></form></td>\n";
    echo "</tr>\n";
    echo "<tr><td colspan='6' style='width:100%; height:2px; background-color:#cc4541;'></td></tr>\n";
    echo "<tr>\n";
    echo "<th></th>\n";
    echo "<th>Product Name</th>\n";
    echo "<th>Quantity</th>\n";
    echo "<th>Unit of Measure</th>\n";
    echo "<th>Price</th>\n";
    echo "<th>Delete Item</th>\n";
    echo "</tr>\n";
    echo "<tr><td colspan='6' style='width:100%; height:2px; background-color:#cc4541;'></td></tr>\n";
    echo "<form action='index.php?page=Store' method='post'>\n";
    $totalCartPrice = 0;
    $getCart = $db->prepare(
            "SELECT * FROM onlineSales WHERE custId = ? && inCart = '1'");
    $getCart->execute(array(
            $custId
    ));
    while ($getCartR = $getCart->fetch()) {
        $cId = $getCartR['id'];
        $productId = $getCartR['productId'];
        $quantity = $getCartR['quantity'];
        $price = $getCartR['price'];
        $totalCartPrice += ($quantity * $price);

        $getCart2 = $db->prepare(
                "SELECT vendorId, name, picName, size FROM product WHERE id = ?");
        $getCart2->execute(array(
                $productId
        ));
        $getCart2R = $getCart2->fetch();
        $vendorId = $getCart2R['vendorId'];
        $name = $getCart2R['name'];
        $picName = $getCart2R['picName'];
        $s = $getCart2R['size'];
        $getSize = $db->prepare("SELECT size FROM productSizes WHERE id = ?");
        $getSize->execute(array(
                $s
        ));
        $getSizeR = $getSize->fetch();
        $size = $getSizeR['size'];

        echo "<tr>\n";
        echo "<td>";
        if ($picName != "noPic.png" &&
                file_exists("img/vendors/$vendorId/thumb/$picName")) {
            echo "<img src='img/vendors/$vendorId/thumb/$picName' style='border:1px solid #cc4541; padding:2px; max-width:100px; max-height:100px;'><br><br>";
        }
        echo "</td>\n";
        echo "<td>$name</td>\n";
        echo "<td>";
        $available = (qtyAvailable($productId, $db) + $quantity);
        if ($available >= 1) {
            echo "<select name='quantity$cId' size='1'>";
            for ($i = 0; $i <= $available; $i ++) {
                echo "<option value='$i'";
                if ($quantity > $available && $i == $available) {
                    echo " selected";
                } elseif ($quantity == $i) {
                    echo " selected";
                }
                echo ">$i</option>\n";
            }
            echo "</select>\n";
        } else {
            echo "Out of stock<input type='hidden' name='quantity$cId' value='0'>";
        }
        echo "</td>\n";
        echo "<td>$size</td>\n";
        echo "<td>" . money($price) . "</td>\n";
        echo "<td><input type='checkbox' name='del$cId' value='1'></td>\n";
        echo "</tr>\n";
        echo "<tr><td colspan='6' style='width:100%; height:2px; background-color:#cc4541;'></td></tr>\n";
    }
    echo "<tr><td style='text-align:center;' colspan='6'><input type='hidden' name='updateOrder' value='$custId'><input type='submit' value=' Update Order Changes '></form></td></tr>\n";
    echo "<tr><td colspan='4'></td><td style='text-align:right; font-weight:bold;'>Total</td><td>" .
            money($totalCartPrice) . "</td></tr>\n";
    echo "<tr><td colspan='4'></td><td style='text-align:center;' colspan='2'>There may be an additional charge for deleivery.<br>Select delivery method at top of form.</td></tr>\n";
    echo "</table>\n";

    echo "<table cellpadding='5px' cellspacing='2px' style='border:1px solid black; width:100%; margin-top:30px;'>";
    if ($storeOpen == 0) {
        echo "<tr><td colspan='7'><div style='text-align: center; color: #cc4541; font-size: 1.25em;'>The store is currently closed.<br>It is open " .
                $weekdays[($firstDayOpen - 1)] . " through " .
                $weekdays[($lastDayOpen - 1)] . "</div></td></tr>\n";
    } else {
        echo "<tr>\n";
        echo "<th style=''></th>\n";
        echo "<th style='border:1px solid black;'>Product Name</th>\n";
        echo "<th style='border:1px solid black;'>Description</th>\n";
        echo "<th style='border:1px solid black;'>Quantity</th>\n";
        echo "<th style='border:1px solid black;'>Unit of Measure</th>\n";
        echo "<th style='border:1px solid black;'>Price</th>\n";
        echo "<th style=''></th>\n";
        echo "</tr>";
        echo "<tr><td colspan='7' style='width:100%; height:10px;'></td></tr>";
        $availableVendors = array();
        $gI = $db->query(
                "SELECT vendorId FROM product WHERE quantity >= '1' ORDER BY RAND()");
        while ($gIR = $gI->fetch()) {
            $availableVendors[] = $gIR['vendorId'];
        }
        $aV = array_unique($availableVendors);
        foreach ($aV as $k => $v) {
            $getV = $db->prepare("SELECT displayName FROM vendors WHERE id = ?");
            $getV->execute(array(
                    $v
            ));
            $getVR = $getV->fetch();
            if ($getVR) {
                $headerName = $getVR['displayName'];
                echo "<tr><td colspan='7'><div style='text-align: center; color: #cc4541; font-size: 1.25em; font-weight:bold; cursor:pointer; width:100%;'>$headerName</div></td></tr>\n";
            }
            for ($i = 0; $i < count($products); ++ $i) {
                if ($products[$i] . get_vendor() == $v) {
                    $products[$i] . display();
                }
            }
        }
    }
    echo "</table>";
}