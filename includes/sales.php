<?php
if (filter_input(INPUT_POST, 'delVen', FILTER_SANITIZE_NUMBER_INT) >= 1) {
	$delVen = filter_input(INPUT_POST, 'delVen', FILTER_SANITIZE_NUMBER_INT);
	$delWeek = filter_input(INPUT_POST, 'delWeek', FILTER_SANITIZE_NUMBER_INT);
	$del = $db->prepare("DELETE FROM transactions WHERE vendorId = ? AND paidDate > ? AND paidDate < ? AND (xType = ? OR xType = ?)");
	$del->execute(array(
			$delVen,
			$delWeek,
			$delWeek + 604800,
			'2',
			'3'
	));
}

$getBegin = $db->prepare("SELECT paidDate FROM transactions ORDER BY paidDate LIMIT 1");
$getBegin->execute();
$getBR = $getBegin->fetch();
$beginY = ($getBR) ? date("Y", $getBR['paidDate']) : date("Y", $time);
$endY = date("Y", $time);

for ($beginY; $beginY <= $endY; ++ $beginY) {
	$yearXactions = array(
			'paid' => 0,
			'cnsai' => 0,
			'saicn' => 0,
			'tax' => 0,
			'sales' => 0,
			'fee' => 0,
			'donation' => 0
	);
	$monthXactions = array(
			1 => array(
					'paid' => 0,
					'cnsai' => 0,
					'saicn' => 0,
					'tax' => 0,
					'sales' => 0,
					'fee' => 0,
					'donation' => 0
			),
			2 => array(
					'paid' => 0,
					'cnsai' => 0,
					'saicn' => 0,
					'tax' => 0,
					'sales' => 0,
					'fee' => 0,
					'donation' => 0
			),
			3 => array(
					'paid' => 0,
					'cnsai' => 0,
					'saicn' => 0,
					'tax' => 0,
					'sales' => 0,
					'fee' => 0,
					'donation' => 0
			),
			4 => array(
					'paid' => 0,
					'cnsai' => 0,
					'saicn' => 0,
					'tax' => 0,
					'sales' => 0,
					'fee' => 0,
					'donation' => 0
			),
			5 => array(
					'paid' => 0,
					'cnsai' => 0,
					'saicn' => 0,
					'tax' => 0,
					'sales' => 0,
					'fee' => 0,
					'donation' => 0
			),
			6 => array(
					'paid' => 0,
					'cnsai' => 0,
					'saicn' => 0,
					'tax' => 0,
					'sales' => 0,
					'fee' => 0,
					'donation' => 0
			),
			7 => array(
					'paid' => 0,
					'cnsai' => 0,
					'saicn' => 0,
					'tax' => 0,
					'sales' => 0,
					'fee' => 0,
					'donation' => 0
			),
			8 => array(
					'paid' => 0,
					'cnsai' => 0,
					'saicn' => 0,
					'tax' => 0,
					'sales' => 0,
					'fee' => 0,
					'donation' => 0
			),
			9 => array(
					'paid' => 0,
					'cnsai' => 0,
					'saicn' => 0,
					'tax' => 0,
					'sales' => 0,
					'fee' => 0,
					'donation' => 0
			),
			10 => array(
					'paid' => 0,
					'cnsai' => 0,
					'saicn' => 0,
					'tax' => 0,
					'sales' => 0,
					'fee' => 0,
					'donation' => 0
			),
			11 => array(
					'paid' => 0,
					'cnsai' => 0,
					'saicn' => 0,
					'tax' => 0,
					'sales' => 0,
					'fee' => 0,
					'donation' => 0
			),
			12 => array(
					'paid' => 0,
					'cnsai' => 0,
					'saicn' => 0,
					'tax' => 0,
					'sales' => 0,
					'fee' => 0,
					'donation' => 0
			)
	);
	$startJan = mktime(0, 0, 0, 1, 1, $beginY);
	$endDec = mktime(23, 59, 59, 12, 31, $beginY);

	// Get monthly xactions
	for ($k = 1; $k <= 12; $k ++) {
		$start = mktime(0, 0, 0, $k, 1, $beginY);
		$end = mktime(23, 59, 59, $k + 1, - 1, $beginY);
		$getV1 = $db->prepare("SELECT xType, baseSales, amount, paid, ref FROM transactions WHERE paidDate >= ? && paidDate <= ?");
		$getV1->execute(array(
				$start,
				$end
		));
		while ($getV1R = $getV1->fetch()) {
			$t = $getV1R['xType'];
			switch ($t) {
				case '2':
					$monthXactions[$k]['paid'] += $getV1R['paid'];
					$yearXactions['paid'] += $getV1R['paid'];
					if ($getV1R['ref'] == 'cnsai') {
						$monthXactions[$k]['cnsai'] += $getV1R['baseSales'];
						$yearXactions['cnsai'] += $getV1R['baseSales'];
					} else {
						$monthXactions[$k]['saicn'] += $getV1R['baseSales'];
						$yearXactions['saicn'] += $getV1R['baseSales'];
					}
					$monthXactions[$k]['tax'] += $getV1R['amount'];
					$yearXactions['tax'] += $getV1R['amount'];
					break;
				case '3':
					$monthXactions[$k]['sales'] += $getV1R['baseSales'];
					$yearXactions['sales'] += $getV1R['baseSales'];
					$monthXactions[$k]['fee'] += $getV1R['amount'];
					$yearXactions['fee'] += $getV1R['amount'];
					break;
				case '4':
					$monthXactions[$k]['donation'] += $getV1R['amount'];
					$yearXactions['donation'] += $getV1R['amount'];
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
		<td style='font-weight: bold; text-align: center;'>FM fee<br>Collected</td>
		<td style='font-weight: bold; text-align: center;'>Donations</td>
		<td style='font-weight: bold; text-align: center;'>Pay Taxes</td>
	</tr>
	<tr>
		<td colspan='9' style='font-weight: bold; text-align: left; color: #cc4541;'>Yearly Numbers</td>
	</tr>
    <?php
	if ($yearXactions['sales'] > 0) {
		echo "<tr>\n";
		echo "<td style='border:1px solid black; text-align:center;'>$beginY</td>\n";
		echo "<td style='border:1px solid black; text-align:center;'></td>\n";
		echo "<td style='border:1px solid black; text-align:center;'>";
		echo ($yearXactions['sales'] > 0) ? money($yearXactions['sales']) : "&nbsp;";
		echo "</td>\n";
		echo "<td style='border:1px solid black; text-align:center;'>";
		echo ($yearXactions['saicn'] > 0) ? money($yearXactions['saicn']) : "&nbsp;";
		echo "</td>\n";
		echo "<td style='border:1px solid black; text-align:center;'>";
		echo ($yearXactions['cnsai'] > 0) ? money($yearXactions['cnsai']) : "&nbsp;";
		echo "</td>\n";
		echo "<td style='border:1px solid black; text-align:center;'>";
		echo ($yearXactions['tax'] > 0) ? money($yearXactions['tax']) : "&nbsp;";
		echo "</td>\n";
		echo "<td style='border:1px solid black; text-align:center;'>";
		echo ($yearXactions['fee'] > 0) ? money($yearXactions['fee']) : "&nbsp;";
		echo "</td>\n";
		echo "<td style='border:1px solid black; text-align:center;'>";
		echo ($yearXactions['donation'] > 0) ? money($yearXactions['donation']) : "&nbsp;";
		echo "</td>\n";
		echo "<td></td>\n";
		echo "</tr>\n";
	}
	?>
        <tr>
    		<td colspan='9' style='font-weight: bold; text-align: left; color: #cc4541;'>Monthly Numbers</td>
    	</tr>
        <?php
	for ($l = 1; $l <= 12; $l ++) {
		$start = mktime(0, 0, 0, $l, 1, $beginY);
		$end = mktime(23, 59, 59, $l + 1, - 1, $beginY);
		if ($monthXactions[$l]['sales'] > 0) {
			echo "<tr>\n";
			echo "<td style='border:1px solid black; text-align:center;'>$beginY</td>\n";
			echo "<td style='border:1px solid black; text-align:center;'>$months[$l]</td>\n";
			echo "<td style='border:1px solid black; text-align:center;'>";
			echo ($monthXactions[$l]['sales'] > 0) ? money($monthXactions[$l]['sales']) : "&nbsp;";
			echo "</td>\n";
			echo "<td style='border:1px solid black; text-align:center;'>";
			echo ($monthXactions[$l]['saicn'] > 0) ? money($monthXactions[$l]['saicn']) : "&nbsp;";
			echo "</td>\n";
			echo "<td style='border:1px solid black; text-align:center;'>";
			echo ($monthXactions[$l]['cnsai'] > 0) ? money($monthXactions[$l]['cnsai']) : "&nbsp;";
			echo "</td>\n";
			echo "<td style='border:1px solid black; text-align:center;'>";
			echo ($monthXactions[$l]['tax'] > 0) ? money($monthXactions[$l]['tax']) : "&nbsp;";
			echo "</td>\n";
			echo "<td style='border:1px solid black; text-align:center;'>";
			echo ($monthXactions[$l]['fee'] > 0) ? money($monthXactions[$l]['fee']) : "&nbsp;";
			echo "</td>\n";
			echo "<td style='border:1px solid black; text-align:center;'>";
			echo ($monthXactions[$l]['donation'] > 0) ? money($monthXactions[$l]['donation']) : "&nbsp;";
			echo "</td>\n";
			echo "<td style='border:1px solid black; text-align:center;'>";
			if ($monthXactions[$l]['paid'] == $monthXactions[$l]['tax']) {
				echo "Paid " . money($monthXactions[$l]['tax']);
			} else {
				echo "Pay " . money($monthXactions[$l]['tax']) . " <form action='index.php?page=Board' method='post'><input type='checkbox' name='paid' value='1'>";
				echo " CK# <input type='text' name='ckNumber' value='' size='5'> <input type='hidden' name='payTaxes' value='1'><input type='hidden' name='start' value='$start'><input type='hidden' name='end' value='$end'><input type='submit' value=' Pay '></form>";
			}
			echo "</td>\n";
			echo "</tr>\n";
		}
	}
	?>
        <tr>
    		<td colspan='9' style='font-weight: bold; text-align: left; color: #cc4541;'>Weekly Numbers</td>
    	</tr>
        <?php

	// Get weekly xactions
	for ($i = $startJan, $j = 1; $i <= $endDec; $i = $i + 604800) {
		$cnsai = 0.00;
		$saicn = 0.00;
		$tax = 0.00;
		$sales = 0.00;
		$fee = 0.00;
		$donation = 0.00;
		$vendors = array();
		$getT = $db->prepare("SELECT xType, baseSales, amount, vendorId, ref FROM transactions WHERE paidDate > ? && paidDate < ?");
		$getT->execute(array(
				$i,
				$i + 604800
		));
		while ($getTR = $getT->fetch()) {
			$x = $getTR['xType'];
			$ref = $getTR['ref'];

			$vendors[] = $getTR['vendorId'];

			switch ($x) {
				case '2':
					if ($ref == 'cnsai') {
						$cnsai += $getTR['baseSales'];
					} else {
						$saicn += $getTR['baseSales'];
					}
					$tax += $getTR['amount'];
					break;
				case '3':
					$sales += $getTR['baseSales'];
					$fee += $getTR['amount'];
					break;
				case '4':
					$donation += $getTR['amount'];
					break;
			}
		}
		if ($sales > 0) {
			echo "<tr style='cursor:pointer;' onclick='toggleclass(\"weekview" . $beginY . $j . "\")'>\n";
			echo "<td style='border:1px solid black; text-align:center; margin:0px;'>$beginY</td>\n";
			echo "<td style='border:1px solid black; text-align:center; margin:0px;'>$j</td>\n";
			echo "<td style='border:1px solid black; text-align:center; margin:0px;'>";
			echo ($sales > 0) ? money($sales) : "&nbsp;";
			echo "</td>\n";
			echo "<td style='border:1px solid black; text-align:center; margin:0px;'>";
			echo ($saicn > 0) ? money($saicn) : "&nbsp;";
			echo "</td>\n";
			echo "<td style='border:1px solid black; text-align:center; margin:0px;'>";
			echo ($cnsai > 0) ? money($cnsai) : "&nbsp;";
			echo "</td>\n";
			echo "<td style='border:1px solid black; text-align:center; margin:0px;'>";
			echo ($tax > 0) ? money($tax) : "&nbsp;";
			echo "</td>\n";
			echo "<td style='border:1px solid black; text-align:center; margin:0px;'>";
			echo ($fee > 0) ? money($fee) : "&nbsp;";
			echo "</td>\n";
			echo "<td style='border:1px solid black; text-align:center; margin:0px;'>";
			echo ($donation > 0) ? money($donation) : "&nbsp;";
			echo "</td>\n";
			echo "<td></td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td colspan='8'>\n";
			echo "<table cellspacing = '0px' class='weekview" . $beginY . $j . "' style='display:none;'><tr>\n";
			echo "<td><div style='text-align:center; font-weight:bold; padding-top:10px; margin:0px;'>Customer</div></td>\n";
			echo "<td><div style='text-align:center; font-weight:bold; padding-top:10px; margin:0px;'>Total<br>Sales</div></td>\n";
			echo "<td><div style='text-align:center; font-weight:bold; padding-top:10px; margin:0px;'>SAICN<br>Sales</div></td>\n";
			echo "<td><div style='text-align:center; font-weight:bold; padding-top:10px; margin:0px;'>CNSAI<br>Sales</div></td>\n";
			echo "<td><div style='text-align:center; font-weight:bold; padding-top:10px; margin:0px;'>Tax<br>Collected</div></td>\n";
			echo "<td><div style='text-align:center; font-weight:bold; padding-top:10px; margin:0px;'>Fee<br>Collected</div></td>\n";
			echo "<td><div style='text-align:center; font-weight:bold; padding-top:10px; margin:0px;'>Del cust<br>from week</div></td>\n";
			echo "</tr>\n";
			foreach (array_unique($vendors) as $v) {
				if ($v >= 1) {
					$cnsai2 = 0.00;
					$saicn2 = 0.00;
					$tax2 = 0.00;
					$sales2 = 0.00;
					$fee2 = 0.00;
					$getv = $db->prepare("SELECT xType, baseSales, amount, ref FROM transactions WHERE paidDate > ? && paidDate < ? && vendorId = ?");
					$getv->execute(array(
							$i,
							$i + 604800,
							$v
					));
					while ($getvR = $getv->fetch()) {
						$x2 = $getvR['xType'];
						$ref2 = $getvR['ref'];

						switch ($x2) {
							case '2':
								if ($ref2 == 'cnsai') {
									$cnsai2 += $getvR['baseSales'];
								} else {
									$saicn2 += $getvR['baseSales'];
								}
								$tax2 += $getvR['amount'];
								break;
							case '3':
								$sales2 += $getvR['baseSales'];
								$fee2 += $getvR['amount'];
								break;
						}
					}
					$getVname = $db->prepare("SELECT displayName FROM vendors WHERE id = ?");
					$getVname->execute(array(
							$v
					));
					$getVnameR = $getVname->fetch();
					$vName = ($getVnameR) ? $getVnameR['displayName'] : "";
					echo "<tr>\n";
					echo "<td><div style='border:1px solid black; text-align:center; padding:12px 10px; margin:0px;'>$vName</div></td>\n";
					echo "<td><div style='border:1px solid black; text-align:center; padding:12px 10px; margin:0px;'>";
					echo ($sales2 > 0) ? money($sales2) : "&nbsp;";
					echo "</div></td>\n";
					echo "<td><div style='border:1px solid black; text-align:center; padding:12px 10px; margin:0px;'>";
					echo ($saicn2 > 0) ? money($saicn2) : "&nbsp;";
					echo "</div></td>\n";
					echo "<td><div style='border:1px solid black; text-align:center; padding:12px 10px; margin:0px;'>";
					echo ($cnsai2 > 0) ? money($cnsai2) : "&nbsp;";
					echo "</div></td>\n";
					echo "<td><div style='border:1px solid black; text-align:center; padding:12px 10px; margin:0px;'>";
					echo ($tax2 > 0) ? money($tax2) : "&nbsp;";
					echo "</div></td>\n";
					echo "<td><div style='border:1px solid black; text-align:center; padding:12px 10px; margin:0px;'>";
					echo ($fee2 > 0) ? money($fee2) : "&nbsp;";
					echo "</div></td>\n";
					echo "<td><div style='border:1px solid black; text-align:center; padding:10px; margin:0px;'>";
					echo "<form action='index.php?page=Board' method='post'>";
					echo "<button>Delete</button>";
					echo "<input type='hidden' name='delVen' value='$v'>";
					echo "<input type='hidden' name='delWeek' value='$i'>";
					echo "</form></div></td>\n";
					echo "</tr>\n";
				}
			}
			echo "</table></td><td></td></tr>";
		}
		$j ++;
	}
	?>
</table>
<?php
}
?>