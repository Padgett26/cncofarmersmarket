<div style="text-align:center; padding:5px; font-size:1.5em; color:#cc4541; margin-bottom:40px; text-decoration:underline; font-weight:bold;">Vendors</div>
<?php
$editVen = (filter_input(INPUT_GET, 'editVen', FILTER_SANITIZE_NUMBER_INT) >= 1) ? filter_input(
        INPUT_GET, 'editVen', FILTER_SANITIZE_NUMBER_INT) : 0;

if ($editVen != 0) {
    $ven2 = $db->prepare("SELECT boardId FROM vendors WHERE id = ?");
    $ven2->execute(array(
            $editVen
    ));
    $ven2Row = $ven2->fetch();
    $bId = $ven2Row['boardId'];
} else {
    $bId = 0;
}

// Send email to vendors
if (filter_input(INPUT_POST, 'emailV', FILTER_SANITIZE_NUMBER_INT) == 1) {
    $emailFrom = filter_input(INPUT_POST, 'from', FILTER_SANITIZE_STRING);
    $emailSubject = filter_input(INPUT_POST, 'emailSubject',
            FILTER_SANITIZE_STRING);
    $emailText = filter_input(INPUT_POST, 'emailText', FILTER_SANITIZE_STRING);
    $toEmail = array();
    foreach ($_POST as $key => $val) {
        if ($val == "1" &&
                preg_match("/^sendVemail([1-9][0-9]*)$/", $key, $match)) {
            $toEmail[] = $match[1];
        }
    }
    if (count($toEmail) != 0) {
        emailV($toEmail, $emailFrom, $emailSubject, $emailText);
    }
    unset($toEmail);
}

// Vendor info up
$openProd = 0;
if ((isBoard($myId) || $vendorId == $bId) && $editVen != 0) {

    // Process product info
    if (filter_input(INPUT_POST, 'productUp', FILTER_SANITIZE_STRING)) {
        $openProd = 1;
        $pu = filter_input(INPUT_POST, 'productUp', FILTER_SANITIZE_STRING);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $description = htmlEntities(
                trim(
                        filter_input(INPUT_POST, 'description',
                                FILTER_SANITIZE_STRING)), ENT_QUOTES);
        $quantity = filter_input(INPUT_POST, 'quantity',
                FILTER_SANITIZE_NUMBER_INT);
        $size = filter_input(INPUT_POST, 'size', FILTER_SANITIZE_NUMBER_INT);
        $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_STRING);
        $taxRate = filter_input(INPUT_POST, 'taxRate', FILTER_SANITIZE_STRING);
        $del = (filter_input(INPUT_POST, 'del', FILTER_SANITIZE_NUMBER_INT) == 1) ? 1 : 0;
        if ($pu == 'new') {
            $IS = $db->prepare(
                    "INSERT INTO product VALUES(NULL,?,'','','','','','noPic.png','0')");
            $IS->execute(array(
                    $editVen
            ));
            $IS2 = $db->prepare(
                    "SELECT id FROM product WHERE vendorId = ? ORDER BY id DESC LIMIT 1");
            $IS2->execute(array(
                    $editVen
            ));
            $IS2R = $IS2->fetch();
            $pu = $IS2R['id'];
        }

        $US = $db->prepare(
                "UPDATE product SET name = ?, description = ?, quantity = ?, size = ?, price = ?, ref = ? WHERE id = ?");
        $US->execute(
                array(
                        $name,
                        $description,
                        $quantity,
                        $size,
                        $price,
                        $taxRate,
                        $pu
                ));
        if ((isBoard($myId) || ($vendorId == $bId)) && $del == 1) {
            $c = $db->prepare("SELECT picName FROM product WHERE id = ?");
            $c->execute(array(
                    $pu
            ));
            $cR = $c->fetch();
            $pN = $cR['picName'];
            if (file_exists("img/vendors/$editVen/thumb/$pN")) {
                unlink("img/vendors/$editVen/thumb/$pN");
            }
            $DS = $db->prepare("DELETE FROM product WHERE id = ?");
            $DS->execute(array(
                    $pu
            ));
        }

        if (isset($_FILES["image"]['tmp_name']) &&
                $_FILES["image"]["size"] >= 1000) {
            $image = $_FILES["image"]["tmp_name"];
            $folder = "img/vendors/$editVen";
            list ($width, $height) = (getimagesize($image) != null) ? getimagesize(
                    $image) : null;
            if ($width != null && $height != null) {
                $imageType = getPicType($_FILES["image"]['type']);
                $imageName = $time . "." . $imageType;
                processThumbPic($imageName, $image, $folder);
                $c = $db->prepare("SELECT picName FROM product WHERE id = ?");
                $c->execute(array(
                        $pu
                ));
                $cR = $c->fetch();
                $pN = $cR['picName'];
                if (file_exists("img/vendors/$editVen/thumb/$pN")) {
                    unlink("img/vendors/$editVen/thumb/$pN");
                }
                $p1stmt = $db->prepare(
                        "UPDATE product SET picName=? WHERE id=?");
                $p1stmt->execute(array(
                        $imageName,
                        $pu
                ));
            }
        }
    }

    echo "<div style='padding:5px; text-align: center; color: #cc4541; font-weight: bold; font-size: 1.25em; cursor: pointer;' onclick='toggleview(\"venInfo\")'>Edit Vendor Info</div>\n";
    echo "<div id='venInfo' style='display:none;'>\n";

    $ven = $db->prepare("SELECT * FROM vendors WHERE id = ?");
    $ven->execute(array(
            $editVen
    ));
    $venRow = $ven->fetch();
    if ($venRow) {
        foreach ($venRow as $k => $v) {
            ${$k} = $v;
        }
        $description = nl2br(html_entity_decode($description, ENT_QUOTES));
        $cannedProducts = nl2br(html_entity_decode($cannedProducts, ENT_QUOTES));
        $otherProducts = nl2br(html_entity_decode($otherProducts, ENT_QUOTES));
        $detailedProducts = nl2br(
                html_entity_decode($detailedProducts, ENT_QUOTES));
        $detailedPractices = nl2br(
                html_entity_decode($detailedPractices, ENT_QUOTES));
    }

    $getPic = $db->prepare("SELECT picName FROM vendors WHERE id = ?");
    $getPic->execute(array(
            $editVen
    ));
    $getPicR = $getPic->fetch();
    $picName = ($getPicR) ? $getPicR['picName'] : "noPic.png";

    if ($picName != "noPic.png" &&
            file_exists("img/vendors/$editVen/thumb/$picName")) {
        echo "Business Logo<br><img src='img/vendors/$editVen/thumb/$picName' style='border:1px solid #cc4541; padding:2px;'><br><br>";
    }
    ?>
    <form action="index.php?page=VendorApp" method="post" enctype='multipart/form-data'>
        <input type="file" name="image" value=""> Upload a new business logo<br><br>
        I agree to the following:<br>
        <span style="font-size: .75em;">Check boxes</span><br>
        <input type="checkbox" name="readProcedures" value="1"<?php

    echo ($readProcedures == 1) ? " checked" : "";
    ?>> I have read and retain a copy of the 2020 Vendor Procedures.<br>
        <input type="checkbox" name="readPractices" value="1"<?php

    echo ($readPractices == 1) ? " checked" : "";
    ?>> I have read and retain a copy of Food Safety for Kansas Farmers Market Vendors: Regulations and Best Practices. (write NA if you are a vendor who sells NO produce or food products).<br>
        <input type="checkbox" name="assistantsTrained" value="1"<?php

    echo ($assistantsTrained == 1) ? " checked" : "";
    ?>> I will take the responsibility make sure all who sell or assist at my stall are trained in all CCFM procedures, as well.<br>
        <input type="checkbox" name="recordSales" value="1"<?php

    echo ($recordSales == 1) ? " checked" : "";
    ?>> I will complete my daily record sheet and pay my commission fees at the end of each market.<br><br>
        The following are not required, but please answer:<br>
        I have attended a food safety course or presentation (via CCFM or otherwise) in the last 3 years. <input type="radio" name="safetyCourse" value="1"<?php

    echo ($safetyCourse == 1) ? " checked" : "";
    ?>>Yes or <input type="radio" name="safetyCourse" value="0"<?php

    echo ($safetyCourse == 0) ? " checked" : "";
    ?>>No (select one)<br>
        I have attended a CCFM Informational Meeting in the last year. <input type="radio" name="infoMeeting" value="1"<?php

    echo ($infoMeeting == 1) ? " checked" : "";
    ?>>Yes or <input type="radio" name="infoMeeting" value="0"<?php

    echo ($infoMeeting == 0) ? " checked" : "";
    ?>>No (select one)<br><br>
        The Farmers Market is set up to handle and pay the required sales tax due for sales through the Farmers Market, for both online and in person sales. By default, we automatically collect and pay those taxes.<br>
        If you are licensed seller, and have a valid Kansas sales tax id, you can choose to pay your own taxes.<br>
        For online sales, we will still add the appropriate sales tax to the amount due, but instead of us paying those taxes to the state, we will forward that money to you, and then you will be responsible to get your taxes paid.<br><br>
        <?php
    echo ($handlingOwnTaxes == '0') ? "<input type='radio' name='handlingOwnTaxes' value='0' checked>" : "<input type='radio' name='handlingOwnTaxes' value='0'>";
    ?>
        I do not have a Kansas sales tax id, or want the Farmers Market to pay my taxes from the money collected from my Farmers Market sales.<br>
        <?php
    echo ($handlingOwnTaxes == '1') ? "<input type='radio' name='handlingOwnTaxes' value='1' checked>" : "<input type='radio' name='handlingOwnTaxes' value='1'>";
    ?>
        I have a Kansas sales tax id, and will be responsible for paying my own taxes.<br><br>
        <input type="text" name="salesTaxId" value="<?php

    echo $salesTaxId;
    ?>" placeholder="000-0000000000-00"> My Kansas Sales Tax Id (required if you plan to pay your own taxes)<br><br>
        <input type="text" name="agreementAddress1" value="<?php

    echo $agreementAddress1;
    ?>"><br>
        Vendor’s mailing address -- Street Address<br><br>
        <input type="text" name="agreementAddress2" value="<?php

    echo $agreementAddress2;
    ?>"><br>
        Vendor’s mailing address -- City, State Zip<br><br>
        <input type="text" name="agreementPhone" value="<?php

    echo $agreementPhone;
    ?>"><br>
        Vendor’s phone<br><br>
        <input type="text" name="agreementEmail" value="<?php

    echo $agreementEmail;
    ?>" required><input type="hidden" name="oldAgreementEmail" value="<?php

    echo $agreementEmail;
    ?>"><br>
        Vendor’s email address*<br>
        *by supplying your email address you agree to receive vendor monthly reports and other CCFM communications via email<br><br>
        <input type="text" name="agreementName" value="<?php

    echo $agreementName;
    ?>" required><br>
        Vendor’s signature (Type in your name)<br><br>
        <input type="text" name="agreementDate" value="<?php

    echo $agreementDate;
    ?>" required><br>
        Date<br><br><br>
        Vendors must read all guidelines and complete registration forms before the first day to sell at market.<br>
        If you would like to complete your registration in person or if you have any questions, please call Kelley at 303-358-9112 or email <a href="mailto:coordinator@cncofarmersmarket.com">coordinator@cncofarmersmarket.com</a> to make an appointment.<br>
        The form can also be mailed to: Cheyenne County Farmers Market Board 115 W Spencer, St Francis, KS 67756<br><br><br>
        <hr style='width: 50%; color:#cc4541;'><br><br>
        Please answer the following questions as completely as possible to help CCFM enhance promotion of the market.<br>
        Please answer only what you are willing to have published. If you DO NOT want your phone number to appear in an online or print directory, please do not list it below. Your information as listed at the beginning of this document will be for market manager contact only.<br>
        Please list your name as you would like it to appear in market directories. This can be the name of your farm or your family name. Think about how you want customers to identify you. This might be something you incorporate into your signage at the market.<br><br>
        <input type="text" name="displayName" value="<?php

    echo $displayName;
    ?>"><br>
        Name of Booth/Business<br><br>
        <input type="text" name="displayContact" value="<?php

    echo $displayContact;
    ?>"><br>
        Contact Person<br><br>
        <input type="text" name="displayPhone" value="<?php

    echo $displayPhone;
    ?>"><br>
        Phone<br><br>
        <input type="text" name="displayEmail" value="<?php

    echo $displayEmail;
    ?>"><br>
        Email<br><br>
        <input type="text" name="displayWebsite" value="<?php

    echo $displayWebsite;
    ?>"><br>
        Website<br><br>
        <input type="text" name="yearBegan" value="<?php

    echo $yearBegan;
    ?>" size="6"><br>
        Year you began selling at CCFM<br><br>
        A description of your business or products to be used on the vendors page of this website:<br>
        <textarea name='description' cols='40' rows='5'><?php

    echo $description;
    ?></textarea><br><br>
        Days at Market (generally): check all that apply: <input type="checkbox" name="availableSaturdays" value="1"<?php

    echo ($availableSaturdays == 1) ? " checked" : "";
    ?>> Saturday / <input type="checkbox" name="availableWinter" value="1"<?php

    echo ($availableWinter == 1) ? " checked" : "";
    ?>>Indoor Winter Markets<br><br>
        Products Offered:<br>
        <table cellspacing='0'>
            <tr>
                <td style='width:33%; border:1px solid #cc4541;'><input type="checkbox" name="sellingVegetables" value="1"<?php

    echo ($sellingVegetables == 1) ? " checked" : "";
    ?>> Vegetables</td>
                <td style='width:33%; border:1px solid #cc4541;'><input type="checkbox" name="sellingFruit" value="1"<?php

    echo ($sellingFruit == 1) ? " checked" : "";
    ?>> Fruit</td>
                <td style='width:33%; border:1px solid #cc4541;'><input type="checkbox" name="sellingJams" value="1"<?php

    echo ($sellingJams == 1) ? " checked" : "";
    ?>> Jams/Jellies</td>
            </tr>
            <tr>
                <td style='width:33%; border:1px solid #cc4541;'><input type="checkbox" name="sellingMeat" value="1"<?php

    echo ($sellingMeat == 1) ? " checked" : "";
    ?>> Meat</td>
                <td style='width:33%; border:1px solid #cc4541;'><input type="checkbox" name="sellingHoney" value="1"<?php

    echo ($sellingHoney == 1) ? " checked" : "";
    ?>> Honey</td>
                <td style='width:33%; border:1px solid #cc4541;'><input type="checkbox" name="sellingBaking" value="1"<?php

    echo ($sellingBaking == 1) ? " checked" : "";
    ?>> Baked Goods</td>
            </tr>
            <tr>
                <td style='width:33%; border:1px solid #cc4541;'><input type="checkbox" name="sellingEggs" value="1"<?php

    echo ($sellingEggs == 1) ? " checked" : "";
    ?>> Eggs</td>
                <td style='width:33%; border:1px solid #cc4541;'><input type="checkbox" name="sellingHerbs" value="1"<?php

    echo ($sellingHerbs == 1) ? " checked" : "";
    ?>> Herbs</td>
                <td style='width:33%; border:1px solid #cc4541;'><input type="checkbox" name="sellingPlants" value="1"<?php

    echo ($sellingPlants == 1) ? " checked" : "";
    ?>> Live Plants</td>
            </tr>
            <tr>
                <td style='width:33%; border:1px solid #cc4541;'><input type="checkbox" name="sellingFlowers" value="1"<?php

    echo ($sellingFlowers == 1) ? " checked" : "";
    ?>> Cut Flowers</td>
                <td style='width:33%; border:1px solid #cc4541;'><input type="checkbox" name="sellingPetProducts" value="1"<?php

    echo ($sellingPetProducts == 1) ? " checked" : "";
    ?>> Pet Products</td>
                <td style='width:33%; border:1px solid #cc4541;'><input type="checkbox" name="sellingBodyCare" value="1"<?php

    echo ($sellingBodyCare == 1) ? " checked" : "";
    ?>> Body Products</td>
            </tr>
            <tr>
                <td style='width:33%; border:1px solid #cc4541;'><input type="checkbox" name="sellingCrafts" value="1"<?php

    echo ($sellingCrafts == 1) ? " checked" : "";
    ?>> Artisan Crafts</td>
                <td style='width:33%; border:1px solid #cc4541;'><input type="checkbox" name="sellingPreparedFoods" value="1"<?php

    echo ($sellingPreparedFoods == 1) ? " checked" : "";
    ?>> Prepared Foods</td>
                <td style='width:33%; border:1px solid #cc4541;'></td>
            </tr>
        </table><br><br>
        Licensed/certified canned products, please list:<br>
        <textarea name='cannedProducts' cols='40' rows='5'><?php

    echo $cannedProducts;
    ?></textarea><br><br>
        Other, please list:<br>
        <textarea name='otherProducts' cols='40' rows='5'><?php

    echo $otherProducts;
    ?></textarea><br><br>
        Detailed Product Listing:<br>
        <textarea name='detailedProducts' cols='40' rows='5'><?php

    echo $detailedProducts;
    ?></textarea><br><br>
        Descriptive Details about Gardening Practices / Baked Goods Specialty / Other<br>
        <textarea name='detailedPractices' cols='40' rows='5'><?php

    echo $detailedPractices;
    ?></textarea><br><br>
        Email photos of business logo, garden or farm to <a href="mailto:coordinator@cncofarmersmarket.com">coordinator@cncofarmersmarket.com</a> for possible inclusion on our website or other market promotions.<br><br>
        For Market Manager informational purposes:<br>
        <input type="checkbox" name="okForTours" value="1"<?php

    echo ($okForTours == 1) ? " checked" : "";
    ?>> I would like my farm/garden to be considered for any CCFM farm or garden tours that might be scheduled in the future.<br>
        <input type="checkbox" name="okForArticles" value="1"<?php

    echo ($okForArticles == 1) ? " checked" : "";
    ?>> I would be open to the Market Manager and/or area press visiting my farm/garden for feature articles, market promotion in general, and photo opportunities.<br>
        <input type="checkbox" name="okForVolunteer" value="1"<?php

    echo ($okForVolunteer == 1) ? " checked" : "";
    ?>> I or a member of my family would like to serve as an occasional market volunteer.<br>
        <input type="checkbox" name="okForFundraising" value="1"<?php

    echo ($okForFundraising == 1) ? " checked" : "";
    ?>> I or a member of my family would like to assist with market fundraising events.<br>
        <input type="checkbox" name="okForTeaching" value="1"<?php

    echo ($okForTeaching == 1) ? " checked" : "";
    ?>> I would be interested in teaching a workshop or class (topics could include seed saving, seed starting, cooking, growing, high tunnels, indoor container gardening, composting, cold frames, canning, etc.) through the market’s community outreach project.<br><br>
        <?php
    if (isBoard($myId)) {
        echo "Vendor Approved <input type='radio' name='approved' value='1'";
        if ($approved == 1) {
            echo " checked";
        }
        echo "> YES / <input type='radio' name='approved' value='0'";
        if ($approved == 0) {
            echo " checked";
        }
        echo "> NO / <input type='radio' name='approved' value='2'> Delete app and all user info<br><br>\n";
    } else {
        ?>
		<input type='hidden' name='approved' value='<?php
        echo $approved;
        ?>'>
		<?php
    }
    ?>
        <input type='hidden' name='venAppUp' value='<?php

    echo $id;
    ?>'><input type='submit' value=' Update Vendor App '></form>
    <?php
    echo "</div>\n";
    echo "<div style='padding:5px; text-align: center;'><a href='pdf/FMStoreHowTo.pdf' target='_blank' style='color: #cc4541; font-weight: bold; font-size: 1.25em; cursor: pointer; text-decoration:none;'>How To: Add my product to the online store</a></div>\n";
    echo "<div style='padding:5px; text-align: center; color: #cc4541; font-weight: bold; font-size: 1.25em; cursor: pointer;' onclick='toggleview(\"productInfo\")'>Edit Available Product Info</div>\n";
    echo ($openProd == 1) ? "<div id='productInfo' style='display:block;'>" : "<div id='productInfo' style='display:none;'>";
    echo "<table cellspacing='2px' cellpadding='5px' style='width:100%; margin:0px auto; border:1px solid black;'>\n";
    echo "<tr>\n";
    echo "<th style='border:1px solid black;'>Product Image</th>\n";
    echo "<th style='border:1px solid black;'>Product Name</th>\n";
    echo "<th style='border:1px solid black;'>Product Description</th>\n";
    echo "<th style='border:1px solid black;'>Quantity<br>On-hand</th>\n";
    echo "<th style='border:1px solid black;'>Quantity minus<br># Ordered</th>\n";
    echo "<th style='border:1px solid black;'>Unit of Measure</th>\n";
    echo "<th style='border:1px solid black;'>Price</th>\n";
    echo "<th style='border:1px solid black;' title='Non-food items, prepared foods (except for baked foods: cookies, cakes, breads)'>Tax rate<br>SAICN</th>\n";
    echo "<th style='border:1px solid black;' title='Fresh Foods, baked foods, ingredients'>Tax rate<br>CNSAI</th>\n";
    echo "<th style='border:1px solid black;'>Delete Item</th>";
    echo "<th style='border:1px solid black;'>Save Item</th>";
    echo "</tr>\n";
    echo "<tr style='height:5px;'><td colspan='9'></td></tr>\n";
    $product = $db->prepare(
            "SELECT * FROM product WHERE vendorId = ? ORDER BY name");
    $product->execute(array(
            $editVen
    ));
    while ($pR = $product->fetch()) {
        $quan = 0;
        $fill = 0;
        foreach ($pR as $k => $v) {
            ${$k} = $v;
        }
        echo "<form action='index.php?page=Vendors&editVen=$editVen' method='post' enctype='multipart/form-data'>\n";
        echo "<tr>\n";
        echo "<td style='border:1px solid black;'>";
        if ($picName != "noPic.png" &&
                file_exists("img/vendors/$editVen/thumb/$picName")) {
            echo "<img src='img/vendors/$editVen/thumb/$picName' style='max-height:1000px; max-width:100px;'><br>";
        }
        echo "<input type='file' name='image'><br>Upload a new product image</td>\n";
        echo "<td style='border:1px solid black;'><input type='text' name='name' value='$name'></td>\n";
        echo "<td style='border:1px solid black;'><textarea name='description' cols='30' rows='3'>$description</textarea></td>\n";
        echo "<td style='border:1px solid black;'><input type='text' name='quantity' value='$quantity' size='5'></td>\n";
        echo "<td style='border:1px solid black;'>";
        $c = $db->prepare(
                "SELECT quantity, filled FROM onlineSales WHERE productId = ? && quantity > filled");
        $c->execute(array(
                $id
        ));
        while ($cR = $c->fetch()) {
            $quan += $cR['quantity'];
            $fill += $cR['filled'];
        }
        $owed = ($quan - $fill);
        echo "$owed <span style='color: #cc4541; cursor:pointer;' onclick='toggleview(\"productInfo\"), toggleview(\"salesInfo\")'>sold online</span>, " .
                ($quantity - $owed) . " available";
        echo "</td>\n";
        echo "<td style='border:1px solid black;'>";
        echo "<select name='size' size='1'>";
        echo "<option value=''>choose one</option>";
        $units = $db->query("SELECT * FROM productSizes ORDER BY id");
        while ($uR = $units->fetch()) {
            $uId = $uR['id'];
            $u = $uR['size'];
            echo "<option value='$uId'";
            echo ($size == $uId) ? " selected" : "";
            echo ">$u</option>";
        }
        echo "</select>";
        echo "</td>\n";
        echo "<td style='border:1px solid black;'>$<input type='text' name='price' value='$price' size='8'></td>\n";
        echo "<td style='border:1px solid black;'><input type='radio' name='taxRate' value='saicn'";
        echo ($ref == 'saicn') ? " checked" : "";
        echo "></td>\n";
        echo "<td style='border:1px solid black;'><input type='radio' name='taxRate' value='cnsai'";
        echo ($ref == 'cnsai') ? " checked" : "";
        echo "></td>\n";
        echo "<td style='border:1px solid black;'><input type='checkbox' name='del' value='1'></td>";
        echo "<td style='text-align:right;'><input type='hidden' name='productUp' value='$id'><input type='submit' value='Save Product Info'></td>";
        echo "</tr></form>\n";
        echo "<tr style='height:5px;'><td colspan='9'></td></tr>";
    }

    echo "<form action='index.php?page=Vendors&editVen=$editVen' method='post' enctype='multipart/form-data'>\n";
    echo "<tr>\n";
    echo "<td style='border:1px solid black;'><input type='file' name='image'><br>Upload a product image</td>";
    echo "<td style='border:1px solid black;'><input type='text' name='name' value=''></td>\n";
    echo "<td style='border:1px solid black;'><textarea name='description' cols='30' rows='3'></textarea></td>\n";
    echo "<td style='border:1px solid black;'><input type='text' name='quantity' value='' size='5'></td>\n";
    echo "<td style='border:1px solid black; text-align:center; verticle-align:middle; font-weight:bold; color: #cc4541; font-size:1.5em;'>NEW</td>\n";
    echo "<td style='border:1px solid black;'>";
    echo "<select name='size' size='1'>";
    echo "<option value=''>choose one</option>";
    $unitsN = $db->query("SELECT * FROM productSizes ORDER BY id");
    while ($uRow = $unitsN->fetch()) {
        $unitId = $uRow['id'];
        $unit = $uRow['size'];
        echo "<option value='$unitId'>$unit</option>";
    }
    echo "</select>";
    echo "</td>\n";
    echo "<td style='border:1px solid black;'>$<input type='text' name='price' value='' size='8'></td>\n";
    echo "<td style='border:1px solid black;'><input type='radio' name='taxRate' value='saicn' checked></td>\n";
    echo "<td style='border:1px solid black;'><input type='radio' name='taxRate' value='cnsai'></td>\n";
    echo "<td style='border:1px solid black;'></td>";
    echo "<td style='text-align:right;'><input type='hidden' name='productUp' value='new'><input type='submit' value=' Add Product Info '></td>";
    echo "</tr>";
    echo "</table></form>\n";
    echo "</div>\n";

    echo "<div style='padding:5px; text-align: center; color: #cc4541; font-weight: bold; font-size: 1.25em; cursor: pointer;'>Online Sales Info</div>\n";
    echo "<div style='padding:5px; text-align: center; color: #cc4541; font-weight: bold; font-size: 1em; cursor: pointer;' onclick='toggleview(\"openOrders\")'>Open Orders</div>\n";
    $count1 = $db->prepare(
            "SELECT COUNT(*) FROM onlineSales WHERE vendorId = ? && quantity > filled");
    $count1->execute(array(
            $editVen
    ));
    $c1R = $count1->fetch();
    echo ($c1R[0] >= 1) ? "<div id='openOrders' style='display:block;'>\n" : "<div id='openOrders' style='display:none;'>\n";
    echo "<table cellspacing='2px' cellpadding='5px' style='width:100%; margin:10px auto; border:1px solid black;'>";
    echo "<tr>\n";
    echo "<th style='border:1px solid black;'>Customer</th>\n";
    echo "<th style='border:1px solid black;'>Order Date</th>\n";
    echo "<th style='border:1px solid black;'>Product</th>\n";
    echo "<th style='border:1px solid black;'>Quantity</th>\n";
    echo "<th style='border:1px solid black;'>Size</th>\n";
    echo "<th style='border:1px solid black;'>Price</th>\n";
    echo "<th style='border:1px solid black;'>Filled</th>\n";
    echo "</tr>";
    echo "<tr style='height:5px;'><td colspan='7'></td></tr>";
    $o = $db->prepare(
            "SELECT * FROM onlineSales WHERE vendorId = ? && (quantity > filled) ORDER BY custId");
    $o->execute(array(
            $editVen
    ));
    while ($oR = $o->fetch()) {
        $fId = $oR['id'];
        $custId = $oR['custId'];
        $xactionDate = $oR['transactionDate'];
        $productId = $oR['productId'];
        $quantity = $oR['quantity'];
        $price = $oR['price'];
        $filled = $oR['filled'];
        $getSize = $db->prepare(
                "SELECT t1.size, t2.name FROM productSizes AS t1 INNER JOIN product AS t2 ON t1.id = t2.size WHERE t2.id=?");
        $getSize->execute(array(
                $productId
        ));
        $getSizeR = $getSize->fetch();
        $size = $getSizeR[0];
        $name = $getSizeR[1];
        echo "<tr>\n";
        echo "<td style='border:1px solid black;'>";
        $cust = $db->prepare("SELECT * FROM customers WHERE id = ?");
        $cust->execute(array(
                $custId
        ));
        $custR = $cust->fetch();
        $cName = $custR['name'];
        $cPhone = $custR['phone'];
        $cEmail = $custR['email'];
        $cAddress1 = $custR['address1'];
        $cAddress2 = $custR['address2'];
        echo "<div class='tooltip'>$cName<span class='tooltiptext'>$cPhone<br>$cEmail<br>$cAddress1<br>$cAddress2</span></div></td>\n";
        echo "<td style='border:1px solid black;'>" .
                date("F j, Y, g:i a", $xactionDate) . "</td>\n";
        echo "<td style='border:1px solid black;'>$name</td>\n";
        echo "<td style='border:1px solid black;'>$quantity</td>\n";
        echo "<td style='border:1px solid black;'>$size</td>\n";
        echo "<td style='border:1px solid black;'>" . money($price) . "</td>\n";
        echo "<td style='border:1px solid black;'>$filled</td>\n";
        echo "</tr>";
    }
    echo "</table>\n";
    echo "</div>\n";

    echo "<div style='padding:5px; text-align: center; color: #cc4541; font-weight: bold; font-size: 1em; cursor: pointer;' onclick='toggleview(\"productNeeded\")'>Product Needed Numbers</div>\n";
    $count2 = $db->prepare(
            "SELECT COUNT(*) FROM onlineSales WHERE vendorId = ? && quantity > filled");
    $count2->execute(array(
            $editVen
    ));
    $c2R = $count2->fetch();
    echo ($c2R[0] >= 1) ? "<div id='productNeeded' style='display:block;'>\n" : "<div id='productNeeded' style='display:none;'>\n";
    echo "<table cellspacing='2px' cellpadding='5px' style='width:100%; margin:10px auto; border:1px solid black;'>";
    echo "<tr>\n";
    echo "<th style='border:1px solid black;'>Product</th>\n";
    echo "<th style='border:1px solid black;'>Quantity</th>\n";
    echo "<th style='border:1px solid black;'>Size</th>\n";
    echo "</tr>";
    echo "<tr style='height:5px;'><td colspan='3'></td></tr>";
    $productArray = array();
    $r = $db->prepare(
            "SELECT productId FROM onlineSales WHERE vendorId = ? && quantity > filled");
    $r->execute(array(
            $editVen
    ));
    while ($rR = $r->fetch()) {
        $pId = $rR['productId'];
        $productArray[] = $pId;
    }
    $productList = array_unique($productArray);

    foreach ($productList as $k => $v) {
        $qty = 0;
        $fill = 0;
        $getProducts = $db->prepare(
                "SELECT quantity, filled FROM onlineSales WHERE vendorId = ? && productId = ? && quantity > filled");
        $getProducts->execute(array(
                $editVen,
                $v
        ));
        while ($gpR = $getProducts->fetch()) {
            $qty += $gpR['quantity'];
            $fill += $gpR['filled'];
        }
        $getProducts2 = $db->prepare(
                "SELECT t1.size, t2.name FROM productSizes AS t1 INNER JOIN product AS t2 ON t1.id = t2.size WHERE t2.vendorId = ? && t2.id=?");
        $getProducts2->execute(array(
                $editVen,
                $v
        ));
        $gpR2 = $getProducts2->fetch();
        $size = $gpR2[0];
        $name = $gpR2[1];

        echo "<tr>\n";
        echo "<td style='border:1px solid black;'>$name</td>\n";
        echo "<td style='border:1px solid black;'>" . ($qty - $fill) . "</td>\n";
        echo "<td style='border:1px solid black;'>$size</td>\n";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>\n";

    echo "<div style='padding:5px; text-align: center; color: #cc4541; font-weight: bold; font-size: 1em; cursor: pointer;' onclick='toggleview(\"filledOrders\")'>Filled Orders</div>\n";
    echo "<div id='filledOrders' style='display:none;'>\n";
    echo "<table cellspacing='2px' cellpadding='5px' style='width:100%; margin:10px auto; border:1px solid black;'>";
    echo "<tr>\n";
    echo "<th style='border:1px solid black;'>Customer</th>\n";
    echo "<th style='border:1px solid black;'>Order Date</th>\n";
    echo "<th style='border:1px solid black;'>Product</th>\n";
    echo "<th style='border:1px solid black;'>Quantity</th>\n";
    echo "<th style='border:1px solid black;'>Size</th>\n";
    echo "<th style='border:1px solid black;'>Price</th>\n";
    echo "<th style='border:1px solid black;'>Filled</th>\n";
    echo "</tr>";
    echo "<tr style='height:5px;'><td colspan='7'></td></tr>";
    $fo = $db->prepare(
            "SELECT * FROM onlineSales WHERE vendorId = ? && quantity = filled ORDER BY custId");
    $fo->execute(array(
            $editVen
    ));
    while ($foR = $fo->fetch()) {
        $fId = $foR['id'];
        $custId = $foR['custId'];
        $xactionDate = $foR['transactionDate'];
        $productId = $foR['productId'];
        $quantity = $foR['quantity'];
        $price = $foR['price'];
        $filled = $foR['filled'];
        $getSize = $db->prepare(
                "SELECT t1.size, t2.name FROM productSizes AS t1 INNER JOIN product AS t2 ON t1.id = t2.size WHERE t2.id=?");
        $getSize->execute(array(
                $productId
        ));
        $getSizeR = $getSize->fetch();
        $size = $getSizeR[0];
        $name = $getSizeR[1];
        echo "<tr>\n";
        echo "<td style='border:1px solid black;'>";
        $cust = $db->prepare("SELECT * FROM customers WHERE id = ?");
        $cust->execute(array(
                $custId
        ));
        $custR = $cust->fetch();
        $cName = $custR['name'];
        $cPhone = $custR['phone'];
        $cEmail = $custR['email'];
        $cAddress1 = $custR['address1'];
        $cAddress2 = $custR['address2'];
        echo "<div class='tooltip'>$cName<span class='tooltiptext'>$cPhone<br>$cEmail<br>$cAddress1<br>$cAddress2</span></div></td>\n";
        echo "<td style='border:1px solid black;'>" .
                date("F j, Y, g:i a", $xactionDate) . "</td>\n";
        echo "<td style='border:1px solid black;'>$name</td>\n";
        echo "<td style='border:1px solid black;'>$quantity</td>\n";
        echo "<td style='border:1px solid black;'>$size</td>\n";
        echo "<td style='border:1px solid black;'>" . money($price) . "</td>\n";
        echo "<td style='border:1px solid black;'>$filled</td>\n";
        echo "</tr>";
    }
    echo "</table>\n";
    echo "</div>\n";

    echo "<div style='padding:5px; text-align: center; color: #cc4541; font-weight: bold; font-size: 1.25em; cursor: pointer;' onclick='toggleview(\"financialStatement\")'>Financial Statement</div>\n";
    echo "<div id='financialStatement' style='display:none;'>\n";
    $getfy = $db->prepare(
            "SELECT paidDate FROM transactions WHERE vendorId = ? && xType = ? ORDER BY paidDate LIMIT 1");
    $getfy->execute(array(
            $editVen,
            '3'
    ));
    $getfyR = $getfy->fetch();
    if ($getfyR) {
        $firstYear = $getfyR['paidDate'];
        $fy = date('Y', $firstYear);
    } else {
        $fy = date("Y", $time);
    }

    $getly = $db->prepare(
            "SELECT paidDate FROM transactions WHERE vendorId = ? && xType = ? ORDER BY paidDate DESC LIMIT 1");
    $getly->execute(array(
            $editVen,
            '3'
    ));
    $getlyR = $getly->fetch();
    if ($getlyR) {
        $lastYear = $getlyR['paidDate'];
        $ly = date('Y', $lastYear);
    } else {
        $ly = date("Y", $time);
    }

    for ($i = $ly; $i >= $fy; $i --) {
        $startY = mktime(0, 0, 0, 1, 1, $i);
        $endY = mktime(23, 59, 59, 12, 31, $i);

        $baseSalesY = 0;
        $tot = $db->prepare(
                "SELECT baseSales FROM transactions WHERE vendorId = ? && xType = ? && paidDate >= ? && paidDate <= ?");
        $tot->execute(array(
                $editVen,
                '3',
                $startY,
                $endY
        ));
        while ($totR = $tot->fetch()) {
            $baseSalesY += $totR['baseSales'];
        }

        $taxesY = 0;
        $t = $db->prepare(
                "SELECT amount FROM transactions WHERE vendorId = ? && xType = ? && paidDate >= ? && paidDate <= ?");
        $t->execute(array(
                $editVen,
                '2',
                $startY,
                $endY
        ));
        while ($tR = $t->fetch()) {
            $taxesY += $tR['amount'];
        }

        $FMFeeY = 0;
        $f = $db->prepare(
                "SELECT amount FROM transactions WHERE vendorId = ? && xType = ? && paidDate >= ? && paidDate <= ?");
        $f->execute(array(
                $editVen,
                '3',
                $startY,
                $endY
        ));
        while ($fR = $f->fetch()) {
            $FMFeeY += $fR['amount'];
        }

        if ($baseSalesY != 0) {
            echo "<div style='font-weight:bold; font-size:1.5em; cursor:pointer; color: #cc4541;' onclick='toggleview(\"showYear$i\")'>$i Year Totals</div>\n";
            echo "<div id='showYear$i' style='display:";
            echo ($i == $ly) ? 'block' : 'none';
            echo ";'>";
            echo "<div style='font-weight:bold; font-size:1em;'>" .
                    money($baseSalesY) . " Total Sales</div>\n";
            echo "<div style='font-weight:bold; font-size:1em;'>" .
                    money($taxesY) . " Taxes Paid</div>\n";
            echo "<div style='font-weight:bold; font-size:1em;'>" .
                    money($FMFeeY) . " FM Fee</div>\n";

            echo "<div style='margin:20px 0px;'>";
            for ($m = 1; $m <= 12; $m ++) {
                $d = date("t", mktime(0, 0, 0, $m, 1, $i));
                $startM = mktime(0, 0, 0, $m, 1, $i);
                $endM = mktime(23, 59, 59, $m, $d, $i);

                $baseSalesM = 0;
                $tot = $db->prepare(
                        "SELECT baseSales FROM transactions WHERE vendorId = ? && xType = ? && paidDate >= ? && paidDate <= ?");
                $tot->execute(array(
                        $editVen,
                        '3',
                        $startM,
                        $endM
                ));
                while ($totR = $tot->fetch()) {
                    $baseSalesM += $totR['baseSales'];
                }

                $taxesM = 0;
                $t = $db->prepare(
                        "SELECT amount FROM transactions WHERE vendorId = ? && xType = ? && paidDate >= ? && paidDate <= ?");
                $t->execute(array(
                        $editVen,
                        '2',
                        $startM,
                        $endM
                ));
                while ($tR = $t->fetch()) {
                    $taxesM += $tR['amount'];
                }

                $FMFeeM = 0;
                $f = $db->prepare(
                        "SELECT amount FROM transactions WHERE vendorId = ? && xType = ? && paidDate >= ? && paidDate <= ?");
                $f->execute(array(
                        $editVen,
                        '3',
                        $startM,
                        $endM
                ));
                while ($fR = $f->fetch()) {
                    $FMFeeM += $fR['amount'];
                }

                if ($baseSalesM != 0) {
                    echo "<div style='font-size:1.25em; color: #cc4541;'>" .
                            $months[$m] . ", $i Sales</div>\n";
                    echo "<div style='font-size:1em;'>" . money($baseSalesM) .
                            " Total Sales</div>\n";
                    echo "<div style='font-size:1em;'>" . money($taxesM) .
                            " Taxes Paid</div>\n";
                    echo "<div style='font-size:1em;'>" . money($FMFeeM) .
                            " FM Fee</div>\n";
                }
            }
            echo "</div></div>";
        }
    }
    echo "</div>\n";
} else {
    if (isBoard($myId)) {
        ?>
        <div style='margin:20px 40px; font-weight:bold; text-align:center; cursor:pointer;' onclick='toggleview("emailVendors")'>Email Vendors</div>
        <div id='emailVendors' style='margin:20px 40px; display:none;'>
            <form action='index.php?page=Vendors' method='post'>
                <table cellpadding="5px" cellspacing="0px" style="margin:10px auto; width:100%;">
                    <tr>
                    <td style="text-align:left; font-weight:bold;">From: <select name="from" size="1"><option value="Board">Board</option><option value="Coordinator">Coordinator</option></select></td>
                    <td style="text-align:left; font-weight:bold;">(*) has open orders</td>
                    </tr>
                    <tr>
                        <?php
        $t = 0;
        $getE = $db->prepare(
                "SELECT id, displayName, mailingList FROM vendors ORDER BY displayName");
        $getE->execute();
        while ($getER = $getE->fetch()) {
            $eId = $getER['id'];
            $eDisplayName = $getER['displayName'];
            $eMailingList = $getER['mailingList'];
            $ck = ($eMailingList == '1') ? " checked" : "";

            $getO = $db->prepare(
                    "SELECT COUNT(*) FROM onlineSales WHERE vendorId = ? && inCart = '0' && quantity <> filled");
            $getO->execute(array(
                    $eId
            ));
            $getOR = $getO->fetch();
            $oCount = $getOR[0];

            echo "<td style='width:50%;'><input type='checkbox' name='sendVemail$eId' value='1'" .
                    $ck . "> $eDisplayName";
            echo ($oCount >= 1) ? " <span style='font-weight:bold;'>(*)</span>" : "";
            echo "</td>";
            echo ($t % 2 == 1) ? "</tr><tr>" : "";
            $t ++;
        }
        ?>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align:center;">
                            Email Subject:
                            <input type="text" name="emailSubject" value=""><br><br>
                            Email text:<br>
                            <textarea name="emailText" rows="5" cols="40"></textarea><br><br>
                            <input type="hidden" name="emailV" value="1"><input type="submit" value=" Send Email ">
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <?php
    }
    ?>
    <table style="width: 100%;">
        <?php
    if ($myId == 0 && $vendorId == 0) {
        echo "<div style='text-align:center; margin-bottom:30px;'>I would like to sign up as a vendor. How do I set up my vendor info? Please read the vendor proceedures and fill out the agreement form. <form action='index.php?page=Legal' method='post'><input type='hidden' name='show' value='vendorApp'><input type='submit' value=' CLICK HERE '></form></div>";
    }
    $highlight = (filter_input(INPUT_GET, 'highlight',
            FILTER_SANITIZE_NUMBER_INT) >= 1) ? filter_input(INPUT_GET,
            'highlight', FILTER_SANITIZE_NUMBER_INT) : 0;
    $filter = (isBoard($myId)) ? "" : " WHERE approved = '1'";
    $ven = $db->query("SELECT * FROM vendors$filter ORDER BY RAND()");
    while ($venRow = $ven->fetch()) {
        $venId = $venRow['id'];
        $name = $venRow['displayName'];
        $description = html_entity_decode($venRow['description'], ENT_QUOTES);
        $website = $venRow['displayWebsite'];
        $picName = $venRow['picName'];
        $approved = $venRow['approved'];
        $boardId = $venRow['boardId'];

        if ($approved == 1 || isBoard($myId) || $vendorId == $boardId) {
            if ($approved == 0 && isBoard($myId)) {
                echo "<tr>\n";
                echo "<td style='padding:5px; text-align: center;' colspan='2'>";
                echo "<a href='index.php?page=Vendors&editVen=$venId' style='color: #cc4541; font-weight: bold; font-size: 1.25em;'>*** Needs Approval ***</a>";
                echo "</td>\n";
                echo "</tr>\n";
            } elseif ($approved == 0 && $myId == 0) {
                echo "<tr>\n";
                echo "<td style='padding:5px; text-align: center; color: #cc4541; font-weight: bold; font-size: 1.25em;' colspan='2'>*** Needs Admin Approval ***</td>\n";
                echo "</tr>\n";
            }
            if (isBoard($myId) || $vendorId == $boardId) {
                echo "<tr>\n";
                echo "<td style='padding:5px; text-align: center;' colspan='2'>";
                echo "<a href='index.php?page=Vendors&editVen=$venId' style='color: #cc4541; font-weight: bold; font-size: 1.25em;'>View / Edit Orders and Info</a>";
                echo "</td>\n";
                echo "</tr>\n";
            }
            echo "<tr id='highlight$venId'>\n";
            echo "<td style='padding:5px; text-align:center; color:#cc4541; font-weight:bold; font-size:1.25em; cursor:pointer;' colspan='2' onclick='toggleview(\"ven$venId\")'>" .
                    $name . "</td>\n";
            echo "</tr>\n";
            echo "<tr><td colspan='2'>";
            echo ($highlight == $venId) ? "<div id='ven$venId' style='display:block;'>\n" : "<div id='ven$venId' style='display:none;'>\n";
            if (file_exists("img/vendors/$venId/$picName") &&
                    $picName != "noPic.png") {
                echo "<div style='text-align:center; margin:10px;'>";
                echo "<img src='img/vendors/$venId/$picName' title='' style='margin: auto 10px; border: 1px solid #cc4541; padding: 3px; max-width: 300px; max-height:300px;' />";
                echo "</div>\n";
            }
            echo "<div style='text-align: center; margin:10px;'><a href='$website' target='_blank'>$website</a></div>";
            echo "<div style='text-align: center; margin: 10px 40px;'>$description</div>";
            echo "</div></td></tr>\n";
            echo "<tr>";
            echo "<td style='padding:5px;' colspan='2'>";
            echo "<hr style='width:50%; text-align: center; color: #cc4541;'>";
            echo "</td>";
            echo "</tr>\n";
        }
    }
    echo "</table>";
}