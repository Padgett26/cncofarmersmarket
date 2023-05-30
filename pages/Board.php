<?php
$mseOpen = 0;
$mse = (filter_input(INPUT_POST, 'mse', FILTER_SANITIZE_NUMBER_INT) >= 1) ? filter_input(
        INPUT_POST, 'mse', FILTER_SANITIZE_NUMBER_INT) : 0;
?>
<div
	style="text-align: center; color: #cc4541; font-size: 1.5em; margin-bottom: 20px; text-decoration: underline;">The
	Cheyenne Co Farmers' Market Board</div>
<table style="width: 350px; margin: auto;">
    <?php
    $bList = array(
            "Director",
            "Vice-Director",
            "Secretary",
            "Treasurer",
            "Public Relations"
    );

    foreach ($bList as $v) {
        $b = $db->prepare(
                "SELECT displayName FROM board WHERE role = 'board' && position = ?");
        $b->execute(array(
                $v
        ));
        $bRow = $b->fetch();
        if ($bRow) {
            $name = $bRow['displayName'];
            if ($name != "" && $name != " ") {
                echo "<tr><td style='text-align: left; padding: 5px;'>" . $v .
                        "</td><td style='text-align: left; padding: 5px;'>" .
                        $name . "</td></tr>";
            }
        }
    }
    ?>
</table>
<div
	style="text-align: center; color: #cc4541; font-size: 1.5em; margin: 20px 0px; text-decoration: underline;">The
	Cheyenne Co Farmers' Market Staff</div>
<table style="width: 350px; margin: auto;">
    <?php
    $sList = array(
            "Coordinator",
            "Event Planner"
    );

    foreach ($sList as $v) {
        $s = $db->prepare(
                "SELECT displayName FROM board WHERE role = 'board' && position = ?");
        $s->execute(array(
                $v
        ));
        $sRow = $s->fetch();
        if ($sRow) {
            $name = $sRow['displayName'];
            if ($name != "" && $name != " ") {
                echo "<tr><td style='text-align: left; padding: 5px;'>" . $v .
                        "</td><td style='text-align: left; padding: 5px;'>" .
                        $name . "</td></tr>";
            }
        }
    }
    $pList = array_merge($bList, $sList);
    ?>
</table>

<?php
if (isBoard($myId)) {
    ?>
<div
	style="text-align: left; color: #cc4541; font-size: 1.25em; margin: 40px 0px 20px 0px; cursor: pointer;"
	onclick="toggleview('boardInfo')">Change board info</div>
<table style="display: none;" id="boardInfo">
        <?php
    $boardList = array();
    $b = $db->query("SELECT id,position,displayName FROM board");
    while ($bRow = $b->fetch()) {
        $boardList[] = array(
                $bRow['id'],
                $bRow['position'],
                $bRow['displayName']
        );
    }
    foreach ($pList as $v) {
        echo "<form action='index.php?page=Board' method='post'>\n";
        echo "<tr>";
        echo "<td style='text-align: left; padding: 5px;'>" . $v . "</td>\n";
        echo "<td style='text-align: left; padding: 5px;'><select name='displayName'>\n";
        echo "<option value='0'></option>\n";
        foreach ($boardList as $val) {
            echo "<option value='$val[0]'";
            echo ($val[1] == $v) ? " selected" : "";
            echo ">$val[2]</option>\n";
        }
        echo "</select></td>\n";
        echo "<td style='text-align: center; padding: 5px;' colspan='3'><input type='hidden' name='updateBoard' value='$v' /><input type='submit' value=' Change ' /></td>\n";
        echo "</tr>";
        echo "</form>";
    }
    ?>
    </table>
<div
	style="text-align: left; color: #cc4541; font-size: 1.25em; margin: 20px 0px; cursor: pointer;"
	onclick="toggleview('generalInfo')">Change general info</div>
<form action='index.php?page=Board' method='post'>
	<table style="display: none;" id="generalInfo">
            <?php
    $gi = $db->prepare("SELECT * FROM generalInfo WHERE id = ?");
    $gi->execute(array(
            '1'
    ));
    $giR = $gi->fetch();
    $saicn = $giR['SAICN_taxRate'];
    $cnsai = $giR['CNSAI_taxRate'];
    $fm = $giR['fmFee'];
    $bf = $giR['boothFee'];
    $st = $giR['stateTaxId'];
    $bo = $giR['boardEmail'];
    $co = $giR['coordinatorEmail'];

    echo "<tr><td style='text-align: left; padding: 5px;'>SAICN Tax Rate<br>Non-food or prepared foods</td><td style='text-align: left; padding: 5px;'><input type='text' name='saicntaxRate' value='$saicn' size='8'>%</td></tr>";
    echo "<tr><td style='text-align: left; padding: 5px;'>CNSAI Tax Rate<br>fresh food or baked goods</td><td style='text-align: left; padding: 5px;'><input type='text' name='cnsaitaxRate' value='$cnsai' size='8'>%</td></tr>";
    echo "<tr><td style='text-align: left; padding: 5px;'>FM Fee</td><td style='text-align: left; padding: 5px;'><input type='text' name='fmFee' value='$fm' size='8'>%</td></tr>";
    echo "<tr><td style='text-align: left; padding: 5px;'>Booth Fee</td><td style='text-align: left; padding: 5px;'>$<input type='text' name='boothFee' value='$bf' size='8'></td></tr>";
    echo "<tr><td style='text-align: left; padding: 5px;'>State Tax Id</td><td style='text-align: left; padding: 5px;'><input type='text' name='stateTaxId' value='$st' size='15'></td></tr>";
    echo "<tr><td style='text-align: left; padding: 5px;'>Board Email</td><td style='text-align: left; padding: 5px;'><input type='text' name='boardEmail' value='$bo' size='30'></td></tr>";
    echo "<tr><td style='text-align: left; padding: 5px;'>Coordinator Email</td><td style='text-align: left; padding: 5px;'><input type='text' name='coordinatorEmail' value='$co' size='30'></td></tr>";
    echo "<tr><td style='text-align: left; padding: 5px;'>Website title</td><td style='text-align: left; padding: 5px;'><input type='text' name='siteTitle' value='$siteTitle' size='30'></td></tr>";
    echo "<tr><td style='text-align: center; padding: 5px;' colspan='2'><input type='hidden' name='giUp' value='1'><input type='submit' value=' Update Info '></td></tr>";
    ?>
        </table>
</form>
<div
	style="text-align: left; color: #cc4541; font-size: 1.25em; margin: 20px 0px; text-decoration: underline;">Finances</div>
<div
	style="text-align: left; color: #cc4541; font-size: 1em; margin: 20px 0px; cursor: pointer;"
	onclick="toggleview('marketSales')">Market Sales Entry</div>
<table id="marketSales" style="display:<?php

    if ($mse >= 1) {
        echo "block";
    } elseif ($mseOpen == 1) {
        echo "block";
    } else {
        echo "none";
    }
    ?>; border:1px solid black;" cellpadding="5px" cellspacing="2px">
	<tr>
		<td>Vendor:</td>
		<td>
			<form action="index.php?page=Board" method="post">
				<select name="mse" size="1">
                        <?php
    $getVen = $db->prepare(
            "SELECT id, displayName FROM vendors ORDER BY displayName");
    $getVen->execute();
    while ($getVenR = $getVen->fetch()) {
        $gvId = $getVenR['id'];
        $gvDisplayName = $getVenR['displayName'];
        echo "<option value='$gvId'";
        echo ($mse == $gvId) ? " selected" : "";
        echo ">$gvDisplayName</option>\n";
    }
    ?>
                    </select> <input type="hidden" name="getMSEVendor"
					value="1"><input type="submit" value=" Get Vendor ">
			</form>
		</td>
		<td></td>
	</tr>
        <?php
    if ($mse >= 1) {
        $getMse = $db->prepare(
                "SELECT handlingOwnTaxes FROM vendors WHERE id = ?");
        $getMse->execute(array(
                $mse
        ));
        $getMseR = $getMse->fetch();
        $hot = $getMseR['handlingOwnTaxes'];
        ?>
            <tr>
		<td>Date:</td>
		<td>
			<form action="index.php?page=Board" method="post">
				<input type="date" name="date"
					value="<?php

        echo date("Y-m-d", $time);
        ?>">

		</td>
		<td></td>
	</tr>
	<tr>
		<td>Sales - Non-food items / prepared foods:</td>
		<td>$<input type="number" min="0.00" step=".01" name="SAICNsales"
			value="" id="MStotalSAICN" oninput="updateMS()">
		</td>
		<td></td>
	</tr>
	<tr>
		<td>Sales - Fresh foods / baked items / ingred:</td>
		<td>$<input type="number" min="0.00" step=".01" name="CNSAIsales"
			value="" id="MStotalCNSAI" oninput="updateMS()">
		</td>
		<td></td>
	</tr>
            <?php
        $d1 = ($hot == 0) ? "block" : "none";
        ?>
            <tr id="taxAndFee" style="display:<?php

        echo $d1;
        ?>;">

		<td>Taxes and FM fee due:</td>
		<td>
			<div id="MSdue"></div>
		</td>
		<td></td>
	</tr>
            <?php
        $d2 = ($hot == 1) ? "block" : "none";
        ?>
            <tr id="feeOnly" style="display:<?php

        echo $d2;
        ?>;">
		<td>FM fee due:</td>
		<td>
			<div id="MSfeedue"></div>
		</td>
		<td></td>
	</tr>
	<tr>
		<td>Vendor is responsible<br>for paying their own taxes,<br>and is
			paying the FM fee only.
		</td>
		<td><input type="checkbox" name="feeOnly" value="1"
			<?php

        echo ($hot == 1) ? "checked " : "";
        ?>
			onchange='taxFeeToggle()'></td>
		<td></td>
	</tr>
	<tr>
		<td></td>
		<td>Check # (0 for cash or card):</td>
		<td><input type="text" name="checkNum" value=""></td>
	</tr>
	<tr>
		<td></td>
		<td>Received payment:</td>
		<td><input type="checkbox" name="paid" onchange='moneyrcvd()'></td>
	</tr>
	<tr>
		<td></td>
		<td></td>
		<td><input type="hidden" name="vendor"
			value="<?php

        echo $mse;
        ?>"><input type="hidden" name="MSup" value="1"><input
			id='invoiceSubmit' disabled type="submit" value=" Submit ">
			</form></td>
	</tr>
            <?php
    }
    ?>
    </table>

<div
	style="text-align: left; color: #cc4541; font-size: 1em; margin: 20px 0px; cursor: pointer;"
	onclick="toggleview('donation')">Receive Donation</div>
<div id="donation" style="display: none;">
	<form action='index.php?page=Board' method='post'>
		Amount: <input type='text' name='donationAmt' value='' size='8'> /
		Check# <input type='text' name='ckNumber' value='' size='6'> <input
			type='hidden' name='donation' value='1'> <input type='submit'
			value=' Add Donation '>
	</form>
</div>
<div
	style="text-align: left; color: #cc4541; font-size: 1em; margin: 20px 0px; cursor: pointer;"
	onclick="toggleview('vendor')">Vendor money owed</div>
<table id="vendor" style="display: none; border: 1px solid black;"
	cellpadding="5px" cellspacing="2px">
	<tr>
		<td>Vendor Name</td>
		<td>Address</td>
		<td>Amt Owed</td>
		<td>Paid</td>
		<td>Check #<br>0 for CC
		</td>
		<td>Pay</td>
	</tr>
	<tr>
		<td colspan='6'><div
				style='width: 100%; height: 3px; background-color: #cc4541;'></div></td>
	</tr>
        <?php
    $getV = $db->query(
            "SELECT id, displayName, agreementAddress1, agreementAddress2 FROM vendors ORDER BY displayName");
    while ($getVR = $getV->fetch()) {
        $vendorId = $getVR['id'];
        $displayName = $getVR['displayName'];
        $address1 = $getVR['agreementAddress1'];
        $address2 = $getVR['agreementAddress2'];
        $amtOwed1 = 0;
        $amtPaid1 = 0;
        $getV1 = $db->prepare(
                "SELECT amount, paid FROM transactions WHERE vendorId = ? && amount <> paid && xType = '1'");
        $getV1->execute(array(
                $vendorId
        ));
        while ($getV1R = $getV1->fetch()) {
            $amtOwed1 += $getV1R['amount'];
            $amtPaid1 += $getV1R['paid'];
        }
        if ($amtOwed1 != $amtPaid1) {
            echo "<tr>";
            echo "<td>$displayName</td>";
            echo "<td>$address1<br>$address2</td>";
            echo "<td>" . money($amtOwed1 - $amtPaid1) . "</td>";
            echo "<td><form action='index.php?page=Board' method='post'><input type='checkbox' name='paid' value='1'></td>";
            echo "<td><input type='text' name='ckNumber' value='0'></td>";
            echo "<td><input type='hidden' name='payVendors' value='$vendorId'><input type='submit' value=' Pay '></form></td>";
            echo "</tr>\n";
        }
    }
    ?>
    </table>

<div
	style="text-align: left; color: #cc4541; font-size: 1em; margin: 20px 0px; cursor: pointer;"
	onclick="toggleview('sales')">Sales Numbers</div>
<div id="sales" style="display: none;">
	<?php
    $getBegin = $db->prepare(
            "SELECT paidDate FROM transactions ORDER BY paidDate LIMIT 1");
    $getBegin->execute();
    $getBR = $getBegin->fetch();
    $beginY = ($getBR) ? date("Y", $getBR['paidDate']) : date("Y", $time);
    $endY = date("Y", $time);

    for ($beginY; $beginY <= $endY; ++ $beginY) {
        $yearXactions = array();
        $monthXactions = array();
        $weekXactions = array();
        $startJan = mktime(0, 0, 0, 1, 1, $beginY);
        $endDec = mktime(23, 59, 59, 12, 31, $beginY);

        // Get weekly xactions
        for ($i = $startJan, $j = 1; $i <= $endDec; $i = $i + 604800) {
            $getT = $db->prepare(
                    "SELECT xType, baseSales, amount, ref FROM transactions WHERE paidDate > ? && paidDate < ?");
            $getT->execute(array(
                    $i,
                    $i + 604800
            ));
            while ($getTR = $getT->fetch()) {
                $x = $getTR['xType'];
                $ref = $getTR['ref'];

                switch ($x) {
                    case '2':
                        if ($ref == 'cnsai')
                            $weekXactions[$j]['cnsai'] += $getTR['baseSales'];
                        else
                            $weekXactions[$j]['saicn'] += $getTR['baseSales'];
                        $weekXactions[$j]['tax'] += $getTR['amount'];
                        break;
                    case '3':
                        $weekXactions[$j]['sales'] += $getTR['baseSales'];
                        $weekXactions[$j]['fee'] += $getTR['amount'];
                        break;
                    case '4':
                        $weekXactions[$j]['donation'] += $getTR['amount'];
                        break;
                }
            }
            $j ++;
        }

        // Get monthly xactions
        for ($k = 1; $k <= 12; $k ++) {
            $start = mktime(0, 0, 0, $k, 1, $beginY);
            $end = mktime(23, 59, 59, $k + 1, - 1, $beginY);
            $getV1 = $db->prepare(
                    "SELECT xType, baseSales, amount, paid, ref FROM transactions WHERE paidDate >= ? && paidDate <= ?");
            $getV1->execute(array(
                    $start,
                    $end
            ));
            while ($getV1R = $getV1->fetch()) {
                $t = $getV1R['xType'];
                switch ($t) {
                    case '2':
                        $monthXactions[$k]['paid'] += $getV1R['paid'];
                        if ($getV1R['ref'] == 'cnsai') {
                            $monthXactions[$k]['cnsai'] += $getV1R['baseSales'];
                        } else {
                            $monthXactions[$k]['saicn'] += $getV1R['baseSales'];
                        }
                        $monthXactions[$k]['tax'] += $getV1R['amount'];
                        break;
                    case '3':
                        $monthXactions[$k]['sales'] += $getV1R['baseSales'];
                        $monthXactions[$k]['fee'] += $getV1R['amount'];
                        break;
                    case '4':
                        $monthXactions[$k]['donation'] += $getV1R['amount'];
                        break;
                }
            }
        }
        echo "<div style='font-weight:bold; font-size:1.25em; text-align:left; cursor:pointer;' onclick='toggleview(\"year$beginY\")'>$beginY</div>\n";
        echo "<table id='year$beginY' style='display:";
        echo ($beginY == date("Y", $time)) ? "block" : "none";
        echo "; border:1px solid black;' cellpadding='5px' cellspacing='0px'>";
        ?>
        <tr>
    		<td style='font-weight: bold; text-align: center;'>Year</td>
    		<td style='font-weight: bold; text-align: center;'>Month<br>/<br>week</td>
    		<td style='font-weight: bold; text-align: center;'>Total Sales</td>
    		<td style='font-weight: bold; text-align: center;'>SAICN<br>Taxed<br>Sales</td>
    		<td style='font-weight: bold; text-align: center;'>CNSAI<br>Taxed<br>Sales</td>
    		<td style='font-weight: bold; text-align: center;'>Taxes<br>Collected</td>
    		<td style='font-weight: bold; text-align: center;'>Pay Taxes</td>
    		<td style='font-weight: bold; text-align: center;'>FM fee<br>Collected</td>
    		<td style='font-weight: bold; text-align: center;'>Donations</td>
    	</tr>
    	<tr>
    		<td colspan='8' style='font-weight: bold; text-align: left; color: #cc4541;'>Yearly Numbers</td>
    	</tr>
        <?php
        for ($l = 1; $l <= 12; $l ++) {
            $yearXactions['paid'] += $monthXactions[$l]['paid'];
            $yearXactions['cnsai'] += $monthXactions[$l]['cnsai'];
            $yearXactions['saicn'] += $monthXactions[$l]['saicn'];
            $yearXactions['sales'] += $monthXactions[$l]['sales'];
            $yearXactions['tax'] += $monthXactions[$l]['tax'];
            $yearXactions['fee'] += $monthXactions[$l]['fee'];
            $yearXactions['donation'] += $monthXactions[$l]['donation'];
        }
        if ($yearXactions['sales'] > 0) {
            echo "<tr>\n";
            echo "<td style='border:1px solid black; text-align:center;'>$beginY</td>\n";
            echo "<td style='border:1px solid black; text-align:center;'></td>\n";
            echo "<td style='border:1px solid black; text-align:center;'>" .
                    money($yearXactions['sales']) . "</td>\n";
            echo "<td style='border:1px solid black; text-align:center;'>" .
                    money($yearXactions['saicn']) . "</td>\n";
            echo "<td style='border:1px solid black; text-align:center;'>" .
                    money($yearXactions['cnsai']) . "</td>\n";
            echo "<td style='border:1px solid black; text-align:center;'>" .
                    money($yearXactions['tax']) . "</td>\n";
            echo "<td style='border:1px solid black; text-align:center;'></td>\n";
            echo "<td style='border:1px solid black; text-align:center;'>" .
                    money($yearXactions['fee']) . "</td>\n";
            echo "<td style='border:1px solid black; text-align:center;'>" .
                    money($yearXactions['donation']) . "</td>\n";
            echo "</tr>\n";
        }
        ?>
        <tr>
    		<td colspan='8' style='font-weight: bold; text-align: left; color: #cc4541;'>Monthly Numbers</td>
    	</tr>
        <?php
        for ($l = 1; $l <= 12; $l ++) {
            $start = mktime(0, 0, 0, $l, 1, $beginY);
            $end = mktime(23, 59, 59, $l + 1, - 1, $beginY);
            if ($monthXactions[$l]['sales'] > 0) {
                echo "<tr>\n";
                echo "<td style='border:1px solid black; text-align:center;'>$beginY</td>\n";
                echo "<td style='border:1px solid black; text-align:center;'>$months[$l]</td>\n";
                echo "<td style='border:1px solid black; text-align:center;'>" .
                        money($monthXactions[$l]['sales']) . "</td>\n";
                echo "<td style='border:1px solid black; text-align:center;'>" .
                        money($monthXactions[$l]['saicn']) . "</td>\n";
                echo "<td style='border:1px solid black; text-align:center;'>" .
                        money($monthXactions[$l]['cnsai']) . "</td>\n";
                echo "<td style='border:1px solid black; text-align:center;'>" .
                        money($monthXactions[$l]['tax']) . "</td>\n";
                echo "<td style='border:1px solid black; text-align:center;'>";
                if ($monthXactions[$l]['paid'] == $monthXactions[$l]['tax']) {
                    echo "Paid " . money($monthXactions[$l]['tax']);
                } else {
                    echo "Pay " . money($monthXactions[$l]['tax']) .
                            " <form action='index.php?page=Board' method='post'><input type='checkbox' name='paid' value='1'>";
                    echo " CK# <input type='text' name='ckNumber' value='' size='5'> <input type='hidden' name='payTaxes' value='1'><input type='hidden' name='start' value='$start'><input type='hidden' name='end' value='$end'><input type='submit' value=' Pay '></form>";
                }
                echo "</td>\n";
                echo "<td style='border:1px solid black; text-align:center;'>" .
                        money($monthXactions[$l]['fee']) . "</td>\n";
                echo "<td style='border:1px solid black; text-align:center;'>" .
                        money($monthXactions[$l]['donation']) . "</td>\n";
                echo "</tr>\n";
            }
        }
        ?>
        <tr>
    		<td colspan='8' style='font-weight: bold; text-align: left; color: #cc4541;'>Weekly Numbers</td>
    	</tr>
        <?php
        for ($m = 1; $m <= 53; $m ++) {
            if ($weekXactions[$m]['sales'] > 0) {
                echo "<tr>\n";
                echo "<td style='border:1px solid black; text-align:center;'>$beginY</td>\n";
                echo "<td style='border:1px solid black; text-align:center;'>$m</td>\n";
                echo "<td style='border:1px solid black; text-align:center;'>" .
                        money($weekXactions[$m]['sales']) . "</td>\n";
                echo "<td style='border:1px solid black; text-align:center;'>" .
                        money($weekXactions[$m]['saicn']) . "</td>\n";
                echo "<td style='border:1px solid black; text-align:center;'>" .
                        money($weekXactions[$m]['cnsai']) . "</td>\n";
                echo "<td style='border:1px solid black; text-align:center;'>" .
                        money($weekXactions[$m]['tax']) . "</td>\n";
                echo "<td style='border:1px solid black; text-align:center;'></td>\n";
                echo "<td style='border:1px solid black; text-align:center;'>" .
                        money($weekXactions[$m]['fee']) . "</td>\n";
                echo "<td style='border:1px solid black; text-align:center;'>" .
                        money($weekXactions[$m]['donation']) . "</td>\n";
                echo "</tr>\n";
            }
        }
        ?>
</table>
<?php
    }
    ?>
</div>
<?php
}
