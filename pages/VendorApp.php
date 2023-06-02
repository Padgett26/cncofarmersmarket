<?php
$venId = '0';
$logInName = "";
$displayName = "";
$description = "";
$displayPhone = "";
$displayEmail = "";
$displayWebsite = "";
$displayContact = "";
$yearBegan = '0';
$readProcedures = '0';
$readPractices = '0';
$assistantsTrained = '0';
$recordSales = '0';
$safetyCourse = '0';
$infoMeeting = '0';
$agreementAddress1 = "";
$agreementAddress2 = "";
$agreementPhone = "";
$agreementEmail = "";
$address1 = "";
$address2 = "";
$oldAgreementEmail = "";
$agreementName = "";
$agreementDate = "";
$availableSaturdays = '0';
$availableWinter = '0';
$sellingVegetables = '0';
$sellingFruit = '0';
$sellingJams = '0';
$sellingMeat = '0';
$sellingHoney = '0';
$sellingBaking = '0';
$sellingEggs = '0';
$sellingHerbs = '0';
$sellingPlants = '0';
$sellingFlowers = '0';
$sellingPetProducts = '0';
$sellingBodyCare = '0';
$sellingCrafts = '0';
$sellingPreparedFoods = '0';
$cannedProducts = "";
$otherProducts = "";
$detailedProducts = "";
$detailedPractices = "";
$okForTours = '0';
$okForArticles = '0';
$okForVolunteer = '0';
$okForFundraising = '0';
$okForTeaching = '0';
$approved = '0';
$handlingOwnTaxes = '0';
$salesTaxId = "";
if (filter_input(INPUT_POST, 'venAppUp', FILTER_SANITIZE_NUMBER_INT)) {
    $venId = filter_input(INPUT_POST, 'venAppUp', FILTER_SANITIZE_NUMBER_INT);
    $logInName = filter_input(INPUT_POST, 'logInName', FILTER_SANITIZE_STRING);
    $displayName = filter_input(INPUT_POST, 'displayName',
            FILTER_SANITIZE_STRING);
    $description = filter_var(
            htmlEntities(trim($_POST['description']), ENT_QUOTES),
            FILTER_SANITIZE_STRING);
    $displayPhone = filter_input(INPUT_POST, 'displayPhone',
            FILTER_SANITIZE_STRING);
    $displayEmail = filter_input(INPUT_POST, 'displayEmail',
            FILTER_SANITIZE_STRING);
    $displayWebsite = filter_input(INPUT_POST, 'displayWebsite',
            FILTER_SANITIZE_STRING);
    $displayContact = filter_input(INPUT_POST, 'displayContact',
            FILTER_SANITIZE_STRING);
    $yearBegan = filter_input(INPUT_POST, 'yearBegan',
            FILTER_SANITIZE_NUMBER_INT);
    $readProcedures = (filter_input(INPUT_POST, 'readProcedures',
            FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';
    $readPractices = (filter_input(INPUT_POST, 'readPractices',
            FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';
    $assistantsTrained = (filter_input(INPUT_POST, 'assistantsTrained',
            FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';
    $recordSales = (filter_input(INPUT_POST, 'recordSales',
            FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';
    $safetyCourse = (filter_input(INPUT_POST, 'safetyCourse',
            FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';
    $infoMeeting = (filter_input(INPUT_POST, 'infoMeeting',
            FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';
    $agreementAddress1 = filter_input(INPUT_POST, 'agreementAddress1',
            FILTER_SANITIZE_STRING);
    $agreementAddress2 = filter_input(INPUT_POST, 'agreementAddress2',
            FILTER_SANITIZE_STRING);
    $agreementPhone = filter_input(INPUT_POST, 'agreementPhone',
            FILTER_SANITIZE_STRING);
    $agreementEmail = filter_input(INPUT_POST, 'agreementEmail',
            FILTER_SANITIZE_STRING);
    $address1 = filter_input(INPUT_POST, 'address1', FILTER_SANITIZE_STRING);
    $address2 = filter_input(INPUT_POST, 'address2', FILTER_SANITIZE_STRING);
    $oldAgreementEmail = filter_input(INPUT_POST, 'oldAgreementEmail',
            FILTER_SANITIZE_STRING);
    $agreementName = filter_input(INPUT_POST, 'agreementName',
            FILTER_SANITIZE_STRING);
    $agreementDate = filter_input(INPUT_POST, 'agreementDate',
            FILTER_SANITIZE_STRING);
    $availableSaturdays = (filter_input(INPUT_POST, 'availableSaturdays',
            FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';
    $availableWinter = (filter_input(INPUT_POST, 'availableWinter',
            FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';
    $sellingVegetables = (filter_input(INPUT_POST, 'sellingVegetables',
            FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';
    $sellingFruit = (filter_input(INPUT_POST, 'sellingFruit',
            FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';
    $sellingJams = (filter_input(INPUT_POST, 'sellingJams',
            FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';
    $sellingMeat = (filter_input(INPUT_POST, 'sellingMeat',
            FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';
    $sellingHoney = (filter_input(INPUT_POST, 'sellingHoney',
            FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';
    $sellingBaking = (filter_input(INPUT_POST, 'sellingBaking',
            FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';
    $sellingEggs = (filter_input(INPUT_POST, 'sellingEggs',
            FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';
    $sellingHerbs = (filter_input(INPUT_POST, 'sellingHerbs',
            FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';
    $sellingPlants = (filter_input(INPUT_POST, 'sellingPlants',
            FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';
    $sellingFlowers = (filter_input(INPUT_POST, 'sellingFlowers',
            FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';
    $sellingPetProducts = (filter_input(INPUT_POST, 'sellingPetProducts',
            FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';
    $sellingBodyCare = (filter_input(INPUT_POST, 'sellingBodyCare',
            FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';
    $sellingCrafts = (filter_input(INPUT_POST, 'sellingCrafts',
            FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';
    $sellingPreparedFoods = (filter_input(INPUT_POST, 'sellingPreparedFoods',
            FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';
    $cannedProducts = filter_var(
            htmlEntities(trim($_POST['cannedProducts']), ENT_QUOTES),
            FILTER_SANITIZE_STRING);
    $otherProducts = filter_var(
            htmlEntities(trim($_POST['otherProducts']), ENT_QUOTES),
            FILTER_SANITIZE_STRING);
    $detailedProducts = filter_var(
            htmlEntities(trim($_POST['detailedProducts']), ENT_QUOTES),
            FILTER_SANITIZE_STRING);
    $detailedPractices = filter_var(
            htmlEntities(trim($_POST['detailedPractices']), ENT_QUOTES),
            FILTER_SANITIZE_STRING);
    $okForTours = (filter_input(INPUT_POST, 'okForTours',
            FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';
    $okForArticles = (filter_input(INPUT_POST, 'okForArticles',
            FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';
    $okForVolunteer = (filter_input(INPUT_POST, 'okForVolunteer',
            FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';
    $okForFundraising = (filter_input(INPUT_POST, 'okForFundraising',
            FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';
    $okForTeaching = (filter_input(INPUT_POST, 'okForTeaching',
            FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';
    $approved = filter_input(INPUT_POST, 'approved', FILTER_SANITIZE_NUMBER_INT);
    $handlingOwnTaxes = (filter_input(INPUT_POST, 'handlingOwnTaxes',
            FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';
    $salesTaxId = filter_input(INPUT_POST, 'salesTaxId', FILTER_SANITIZE_STRING);
    if ($approved == 2) {
        $get1 = $db->prepare("SELECT boardId FROM vendors WHERE id = ?");
        $get1->execute(array(
                $venId
        ));
        $get1R = $get1->fetch();
        $boaId = $get1R['boardId'];

        $del1 = $db->prepare("DELETE FROM board WHERE id = ?");
        $del1->execute(array(
                $boaId
        ));
        $del2 = $db->prepare("DELETE FROM customers WHERE boardId = ?");
        $del2->execute(array(
                $boaId
        ));
        $del3 = $db->prepare("DELETE FROM vendors WHERE id = ?");
        $del3->execute(array(
                $venId
        ));
        if (is_dir("img/vendors/$venId")) {
            delTree("img/vendors/$venId");
        }
        ?>
		<div style='text-align: center; color: #cc4541; font-weight: bold; font-size: 1.25em; margin: 20px 0px 40px 0px;'>Vendor Agreement – Cheyenne County Farmers Market</div>
<div style='text-align: center; color: #cc4541; font-weight: bold; font-size: 1em; margin: 20px 0px 40px 0px;'>All information for this customer has been deleted.</div>
		<?php
    } else {
        if ($venId == 1 && $agreementEmail != "" && $agreementName != "") {
            $pwd = filter_input(INPUT_POST, 'pwd', FILTER_SANITIZE_STRING);
            $salt = mt_rand(100000, 999999);
            $hidepwd = hash('sha512', ($salt . $pwd), FALSE);
            $b = $db->prepare(
                    "INSERT INTO board VALUES(NULL,?,?,'vendor','',?,?,?,'0')");
            $b->execute(
                    array(
                            $salt,
                            $hidepwd,
                            $displayName,
                            $logInName,
                            $agreementEmail
                    ));
            $getb = $db->prepare(
                    "SELECT id FROM board WHERE password = ? ORDER BY id DESC LIMIT 1");
            $getb->execute(array(
                    $hidepwd
            ));
            $getbR = $getb->fetch();
            $boardId = $getbR['id'];

            $cust = $db->prepare(
                    "INSERT INTO customers VALUES(NULL,?,?,?,?,?,?,'0')");
            $cust->execute(
                    array(
                            $displayName,
                            $agreementEmail,
                            $agreementPhone,
                            $agreementAddress1,
                            $agreementAddress2,
                            $boardId
                    ));

            $a = $db->prepare(
                    "INSERT INTO vendors VALUES(NULL,?,?,?,?,?,'noPic.png',?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,'1','0',?,?,?,'0','0')");
            $a->execute(
                    array(
                            $displayName,
                            $description,
                            $displayPhone,
                            $displayEmail,
                            $displayWebsite,
                            $displayContact,
                            $yearBegan,
                            $readProcedures,
                            $readPractices,
                            $assistantsTrained,
                            $recordSales,
                            $safetyCourse,
                            $infoMeeting,
                            $agreementAddress1,
                            $agreementAddress2,
                            $agreementPhone,
                            $agreementEmail,
                            $agreementName,
                            $agreementDate,
                            $availableSaturdays,
                            $availableWinter,
                            $sellingVegetables,
                            $sellingFruit,
                            $sellingJams,
                            $sellingMeat,
                            $sellingHoney,
                            $sellingBaking,
                            $sellingEggs,
                            $sellingHerbs,
                            $sellingPlants,
                            $sellingFlowers,
                            $sellingPetProducts,
                            $sellingBodyCare,
                            $sellingCrafts,
                            $sellingPreparedFoods,
                            $cannedProducts,
                            $otherProducts,
                            $detailedProducts,
                            $detailedPractices,
                            $okForTours,
                            $okForArticles,
                            $okForVolunteer,
                            $okForFundraising,
                            $okForTeaching,
                            $boardId,
                            $handlingOwnTaxes,
                            $salesTaxId
                    ));

            $a2 = $db->prepare(
                    "SELECT id FROM vendors WHERE boardId = ? ORDER BY id DESC LIMIT 1");
            $a2->execute(array(
                    $boardId
            ));
            $a2R = $a2->fetch();
            $venId = $a2R[0];
        } else {
            $a = $db->prepare(
                    "UPDATE vendors SET displayName = ?, description = ?, displayPhone = ?, displayEmail = ?, displayWebsite = ?, displayContact = ?, yearBegan = ?, readProcedures = ?, readPractices = ?, assistantsTrained = ?, recordSales = ?, safetyCourse = ?, infoMeeting = ?, agreementAddress1 = ?, agreementAddress2 = ?, agreementPhone = ?, agreementEmail = ?, agreementName = ?, agreementDate = ?, availableSaturdays = ?, availableWinter = ?, sellingVegetables = ?, sellingFruit = ?, sellingJams = ?, sellingMeat = ?, sellingHoney = ?, sellingBaking = ?, sellingEggs = ?, sellingHerbs = ?, sellingPlants = ?, sellingFlowers = ?, sellingPetProducts = ?, sellingBodyCare = ?, sellingCrafts = ?, sellingPreparedFoods = ?, cannedProducts = ?, otherProducts = ?, detailedProducts = ?, detailedPractices = ?, okForTours = ?, okForArticles = ?, okForVolunteer = ?, okForFundraising = ?, okForTeaching = ?, approved = ?, handlingOwnTaxes = ?, salesTaxId = ? WHERE id = ?");
            $a->execute(
                    array(
                            $displayName,
                            $description,
                            $displayPhone,
                            $displayEmail,
                            $displayWebsite,
                            $displayContact,
                            $yearBegan,
                            $readProcedures,
                            $readPractices,
                            $assistantsTrained,
                            $recordSales,
                            $safetyCourse,
                            $infoMeeting,
                            $agreementAddress1,
                            $agreementAddress2,
                            $agreementPhone,
                            $agreementEmail,
                            $agreementName,
                            $agreementDate,
                            $availableSaturdays,
                            $availableWinter,
                            $sellingVegetables,
                            $sellingFruit,
                            $sellingJams,
                            $sellingMeat,
                            $sellingHoney,
                            $sellingBaking,
                            $sellingEggs,
                            $sellingHerbs,
                            $sellingPlants,
                            $sellingFlowers,
                            $sellingPetProducts,
                            $sellingBodyCare,
                            $sellingCrafts,
                            $sellingPreparedFoods,
                            $cannedProducts,
                            $otherProducts,
                            $detailedProducts,
                            $detailedPractices,
                            $okForTours,
                            $okForArticles,
                            $okForVolunteer,
                            $okForFundraising,
                            $okForTeaching,
                            $approved,
                            $handlingOwnTaxes,
                            $salesTaxId,
                            $venId
                    ));

            $c = $db->prepare(
                    "SELECT boardId, picName FROM vendors WHERE id = ?");
            $c->execute(array(
                    $venId
            ));
            $cR = $c->fetch();
            if ($cR) {
                $bTableId = $cR['boardId'];
                $pN = $cR['picName'];
            }

            if (isset($_FILES["image"]["tmp_name"]) &&
                    ($_FILES['image']['size'] > 1000)) {
                $tmpFile = $_FILES["image"]["tmp_name"];
                list ($width, $height) = (getimagesize($tmpFile) != null) ? getimagesize(
                        $tmpFile) : null;
                if ($width != null && $height != null) {
                    $imageType = getPicType($_FILES["image"]['type']);
                    $imageName = $time . "." . $imageType;
                    processPic("$domain/img/vendors/$venId", $imageName,
                            $tmpFile, 600, 150);
                    if (file_exists("img/vendors/$venId/$pN")) {
                        unlink("img/vendors/$venId/$pN");
                    }
                    if (file_exists("img/vendors/$venId/thumb/$pN")) {
                        unlink("img/vendors/$venId/thumb/$pN");
                    }
                    $p1stmt = $db->prepare(
                            "UPDATE vendors SET picName=? WHERE id=?");
                    $p1stmt->execute(array(
                            $imageName,
                            $venId
                    ));
                }
            }

            $b = $db->prepare(
                    "UPDATE board SET displayName = ?, email = ? WHERE id = ?");
            $b->execute(array(
                    $displayName,
                    $agreementEmail,
                    $bTableId
            ));
        }
    }
}
if ($approved != 2) {
    ?>

<div style='text-align: center; color: #cc4541; font-weight: bold; font-size: 1.25em; margin: 20px 0px 40px 0px;'>Vendor Agreement – Cheyenne County Farmers Market</div>
<div style='text-align: center; color: #cc4541; font-weight: bold; font-size: 1em; margin: 20px 0px 40px 0px;'>Your form has been submitted. Thank you</div>

<?php
    $getPic = $db->prepare("SELECT picName FROM vendors WHERE id = ?");
    $getPic->execute(array(
            $venId
    ));
    $getPicR = $getPic->fetch();
    $picName = ($getPicR) ? $getPicR['picName'] : "noPic.png";

    if ($picName != "noPic.png" &&
            file_exists("img/vendors/$venId/thumb/$picName")) {
        echo "Business Logo<br><img src='img/vendors/$venId/thumb/$picName' style='border:1px solid #cc4541; padding:2px;'><br><br>";
    }
    ?>
I agree to the following:<br>
<?php

    echo ($readProcedures == '1') ? "&#9746;" : "&#9744;";
    ?> I have read and retain a copy of the 2020 Vendor Procedures.<br>
<?php

    echo ($readPractices == '1') ? "&#9746;" : "&#9744;";
    ?> I have read and retain a copy of Food Safety for Kansas Farmers Market Vendors: Regulations and Best Practices. (write NA if you are a vendor who sells NO produce or food products).<br>
<?php

    echo ($assistantsTrained == '1') ? "&#9746;" : "&#9744;";
    ?> I will take the responsibility make sure all who sell or assist at my stall are trained in all CCFM procedures, as well.<br>
<?php

    echo ($recordSales == '1') ? "&#9746;" : "&#9744;";
    ?> I will complete my daily record sheet and pay my commission fees at the end of each market.<br><br>
The following are not required, but please answer:<br>
I have attended a food safety course or presentation (via CCFM or otherwise) in the last 3 years. <?php

    echo ($safetyCourse == '1') ? "&#9746;" : "&#9744;";
    ?> Yes or <?php

    echo ($safetyCourse == '0') ? "&#9746;" : "&#9744;";
    ?> No (select one)<br>
I have attended a CCFM Informational Meeting in the last year. <?php

    echo ($infoMeeting == '1') ? "&#9746;" : "&#9744;";
    ?> Yes or <?php

    echo ($infoMeeting == '0') ? "&#9746;" : "&#9744;";
    ?> No (select one)<br><br>
The Farmers Market is set up to handle and pay the required sales tax due for sales through the Farmers Market, for both online and in person sales. By default, we automatically collect and pay those taxes.<br>
If you are licensed seller, and have a valid Kansas sales tax id, you can choose to pay your own taxes.<br>
For online sales, we will still add the appropriate sales tax to the amount due, but instead of us paying those taxes to the state, we will forward that money to you, and then you will be responsible to get your taxes paid.<br><br>
<?php
    if ($handlingOwnTaxes == 0) {
        ?>
    I do not have a Kansas sales tax id, or want the Farmers Market to pay my taxes from the money collected from my Farmers Market sales.<br>
    <?php
    } else {
        ?>
    I have a Kansas sales tax id, and will be responsible for paying my own taxes.<br><br>
    <?php

        echo "salesTaxId";
        ?> My Kansas Sales Tax Id (required if you plan to pay your own taxes)<br><br>
<?php
    }
    echo "$agreementAddress1";
    ?><br>
Vendor’s mailing address -- Street Address<br><br>
<?php

    echo "$agreementAddress2";
    ?><br>
Vendor’s mailing address -- City, State Zip<br><br>
<?php

    echo "$agreementPhone";
    ?><br>
Vendor’s phone<br><br>
<?php

    echo "$agreementEmail";
    ?><br>
Vendor’s email address<br>
*by supplying your email address you agree to receive vendor monthly reports and other CCFM communications via email<br><br>
<?php

    echo "$agreementName";
    ?><br>
Vendor’s signature (Type in your name)<br><br>
<?php

    echo "$agreementDate";
    ?><br>
Date<br><br><br>
Vendors must read all guidelines and complete registration forms before the first day to sell at market.<br>
If you would like to complete your registration in person or if you have any questions, please call Kelley at 303-358-9112 or email <a href="mailto:coordinator@cncofarmersmarket.com">coordinator@cncofarmersmarket.com</a> to make an appointment.<br>
The form can also be mailed to: Cheyenne County Farmers Market Board 115 W Spencer, St Francis, KS 67756<br><br><br>
<hr style='width: 50%; color:#cc4541;'><br><br>
Please answer the following questions as completely as possible to help CCFM enhance promotion of the market.<br>
Please answer only what you are willing to have published. If you DO NOT want your phone number to appear in an online or print directory, please do not list it below. Your information as listed at the beginning of this document will be for market manager contact only.<br>
Please list your name as you would like it to appear in market directories. This can be the name of your farm or your family name. Think about how you want customers to identify you. This might be something you incorporate into your signage at the market.<br><br>
<?php

    echo "$displayName";
    ?><br>
Name of Booth/Business<br><br>
<?php

    echo "$displayContact";
    ?><br>
Contact Person<br><br>
<?php

    echo "$displayPhone";
    ?><br>
Phone<br><br>
<?php

    echo "$displayEmail";
    ?><br>
Email<br><br>
<?php

    echo "$displayWebsite";
    ?><br>
Website<br><br>
<?php

    echo "$yearBegan";
    ?><br>
Year you began selling at CCFM<br><br>
A description of your business or products to be used on the vendors page of this website:<br>
<?php

    echo "$description";
    ?><br><br>
Days at Market (generally): check all that apply: <?php

    echo ($availableSaturdays == '1') ? "&#9746;" : "&#9744;";
    ?> Saturday / <?php

    echo ($availableWinter == '1') ? "&#9746;" : "&#9744;";
    ?> Indoor Winter Markets<br><br>
Products Offered:<br>
<table cellspacing='0'>
    <tr>
        <td style='width:33%; border:1px solid #cc4541;'><?php

    echo ($sellingVegetables == '1') ? "&#9746;" : "&#9744;";
    ?> Vegetables</td>
        <td style='width:33%; border:1px solid #cc4541;'><?php

    echo ($sellingFruit == '1') ? "&#9746;" : "&#9744;";
    ?> Fruit</td>
        <td style='width:33%; border:1px solid #cc4541;'><?php

    echo ($sellingJams == '1') ? "&#9746;" : "&#9744;";
    ?> Jams/Jellies</td>
    </tr>
    <tr>
        <td style='width:33%; border:1px solid #cc4541;'><?php

    echo ($sellingMeat == '1') ? "&#9746;" : "&#9744;";
    ?> Meat</td>
        <td style='width:33%; border:1px solid #cc4541;'><?php

    echo ($sellingHoney == '1') ? "&#9746;" : "&#9744;";
    ?> Honey</td>
        <td style='width:33%; border:1px solid #cc4541;'><?php

    echo ($sellingBaking == '1') ? "&#9746;" : "&#9744;";
    ?> Baked Goods</td>
    </tr>
    <tr>
        <td style='width:33%; border:1px solid #cc4541;'><?php

    echo ($sellingEggs == '1') ? "&#9746;" : "&#9744;";
    ?> Eggs</td>
        <td style='width:33%; border:1px solid #cc4541;'><?php

    echo ($sellingHerbs == '1') ? "&#9746;" : "&#9744;";
    ?> Herbs</td>
        <td style='width:33%; border:1px solid #cc4541;'><?php

    echo ($sellingPlants == '1') ? "&#9746;" : "&#9744;";
    ?> Live Plants</td>
    </tr>
    <tr>
        <td style='width:33%; border:1px solid #cc4541;'><?php

    echo ($sellingFlowers == '1') ? "&#9746;" : "&#9744;";
    ?> Cut Flowers</td>
        <td style='width:33%; border:1px solid #cc4541;'><?php

    echo ($sellingPetProducts == '1') ? "&#9746;" : "&#9744;";
    ?> Pet Products</td>
        <td style='width:33%; border:1px solid #cc4541;'><?php

    echo ($sellingBodyCare == '1') ? "&#9746;" : "&#9744;";
    ?> Body Products</td>
    </tr>
    <tr>
        <td style='width:33%; border:1px solid #cc4541;'><?php

    echo ($sellingCrafts == '1') ? "&#9746;" : "&#9744;";
    ?> Artisan Crafts</td>
        <td style='width:33%; border:1px solid #cc4541;'><?php

    echo ($sellingPreparedFoods == '1') ? "&#9746;" : "&#9744;";
    ?> Prepared Foods</td>
        <td style='width:33%; border:1px solid #cc4541;'></td>
    </tr>
</table><br><br>
Licensed/certified canned products, please list:<br>
<?php

    echo "$cannedProducts";
    ?><br><br>
Other, please list:<br>
<?php

    echo "$otherProducts";
    ?><br><br>
Detailed Product Listing:<br>
<?php

    echo "$detailedProducts";
    ?><br><br>
Descriptive Details about Gardening Practices / Baked Goods Specialty / Other<br>
<?php

    echo "$detailedPractices";
    ?><br><br>
Email photos of business logo, garden or farm to <a href="mailto:coordinator@cncofarmersmarket.com">coordinator@cncofarmersmarket.com</a> for possible inclusion on our website or other market promotions.<br><br>
For Market Manager informational purposes:<br>
<?php

    echo ($okForTours == '1') ? "&#9746;" : "&#9744;";
    ?> I would like my farm/garden to be considered for any CCFM farm or garden tours that might be scheduled in the future.<br>
<?php

    echo ($okForArticles == '1') ? "&#9746;" : "&#9744;";
    ?> I would be open to the Market Manager and/or area press visiting my farm/garden for feature articles, market promotion in general, and photo opportunities.<br>
<?php

    echo ($okForVolunteer == '1') ? "&#9746;" : "&#9744;";
    ?> I or a member of my family would like to serve as an occasional market volunteer.<br>
<?php

    echo ($okForFundraising == '1') ? "&#9746;" : "&#9744;";
    ?> I or a member of my family would like to assist with market fundraising events.<br>
<?php

    echo ($okForTeaching == '1') ? "&#9746;" : "&#9744;";
    ?> I would be interested in teaching a workshop or class (topics could include seed saving, seed starting, cooking, growing, high tunnels, indoor container gardening, composting, cold frames, canning, etc.) through the market’s community outreach project.<br><br>
<?php
}
?>