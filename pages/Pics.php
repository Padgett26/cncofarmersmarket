<div style="text-align: center; color: #cc4541; font-size: 1.5em; margin-bottom: 20px; text-decoration: underline;">Media</div>

<?php
if (isBoard($myId)) {
    echo "<table style='margin:auto;'><tr><td style='padding:10px; color: #cc4541;'>YouTube<br>based videos</td><td style='padding:10px;'>Date</td><td style='padding:10px;'>YouTube Code</td><td style='padding:10px;'>Caption</td><td style='padding:10px;'></td></tr>\n";
    echo "<tr>\n";
    echo "<td style='padding:10px;'>NEW</td>\n";
    echo "<td style='padding:10px;'><form action='index.php?page=Pics' method='post'>\n";
    echo "D:<select name='d' size='1'>";
    for ($z = 1; $z <= 31; $z ++) {
        echo "<option value='$z'";
        echo ($z == date("j", $time)) ? " selected" : "";
        echo ">$z</option>";
    }
    echo "</select>\n";
    echo "M:<select name='m' size='1'>";
    for ($x = 1; $x <= 12; $x ++) {
        echo "<option value='$x'";
        echo ($x == date("n", $time)) ? " selected" : "";
        echo ">$x</option>";
    }
    echo "</select>\n";
    echo "Y:<select name='y' size='1'>";
    for ($w = 2019; $w <= 2040; $w ++) {
        echo "<option value='$w'";
        echo ($w == date("Y", $time)) ? " selected" : "";
        echo ">$w</option>";
    }
    echo "</select>\n";
    echo "</td>\n";
    echo "<td style='padding:10px;'><input type='text' name='youtubeCode' value='' /></td>\n";
    echo "<td style='padding:10px;'><input type='text' name='caption' value='' /></td>\n";
    echo "<td style='padding:10px;'><input type='hidden' name='ytUp' value='new' /><input type='submit' value=' Upload ' /></form></td>\n";
    echo "</tr>\n";

    $v = $db->query(
            "SELECT * FROM media WHERE youtubeCode != '0' ORDER BY displayTime DESC");
    while ($vRow = $v->fetch()) {
        $vid = $vRow['id'];
        $displayTime = $vRow['displayTime'];
        $d = date("j", $displayTime);
        $m = date("n", $displayTime);
        $y = date("Y", $displayTime);

        $youtubeCode = $vRow['youtubeCode'];
        $caption = html_entity_decode($vRow['caption'], ENT_QUOTES);
        echo "<tr>\n";
        echo "<td style='padding:10px;'><iframe width='280' height='158' src='https://www.youtube.com/embed/$youtubeCode' frameborder='0' allow='accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe></td>\n";
        echo "<td style='padding:10px;'><form action='index.php?page=Pics' method='post'>\n";
        echo "D:<select name='d' size='1'>";
        for ($z = 1; $z <= 31; $z ++) {
            echo "<option value='$z'";
            echo ($d == $z) ? " selected" : "";
            echo ">$z</option>";
        }
        echo "</select>\n";
        echo "M:<select name='m' size='1'>";
        for ($x = 1; $x <= 12; $x ++) {
            echo "<option value='$x'";
            echo ($m == $x) ? " selected" : "";
            echo ">$x</option>";
        }
        echo "</select>\n";
        echo "Y:<select name='y' size='1'>";
        for ($w = 2019; $w <= 2040; $w ++) {
            echo "<option value='$w'";
            echo ($y == $w) ? " selected" : "";
            echo ">$w</option>";
        }
        echo "</select>\n";
        echo "</td>\n";
        echo "<td style='padding:10px;'><input type='text' name='youtubeCode' value='$youtubeCode' /></td>\n";
        echo "<td style='padding:10px;'><input type='text' name='caption' value='$caption' /></td>\n";
        echo "<td style='padding:10px;'>Delete:&nbsp;<input type='checkbox' name='del' value='1' /><br><br><input type='hidden' name='ytUp' value='$vid' /><input type='submit' value=' Update ' /></form></td>\n";
        echo "</tr>\n";
    }
    echo "</table>";

    echo "<table style='margin:auto;'><tr><td style='padding:10px; color: #cc4541;'>Pictures</td><td style='padding:10px;'>Date</td><td style='padding:10px;'>Caption</td><td style='padding:10px;'></td></tr>\n";
    echo "<tr>\n";
    echo "<td style='padding:10px;'><form action='index.php?page=Pics' method='post' enctype='multipart/form-data'><input type='file' name='image1' /></td>\n";
    echo "<td style='padding:10px;'>";
    echo "D:<select name='d' size='1'>";
    for ($z = 1; $z <= 31; $z ++) {
        echo "<option value='$z'";
        echo ($z == date("j", $time)) ? " selected" : "";
        echo ">$z</option>";
    }
    echo "</select>\n";
    echo "M:<select name='m' size='1'>";
    for ($x = 1; $x <= 12; $x ++) {
        echo "<option value='$x'";
        echo ($x == date("n", $time)) ? " selected" : "";
        echo ">$x</option>";
    }
    echo "</select>\n";
    echo "Y:<select name='y' size='1'>";
    for ($w = 2019; $w <= 2040; $w ++) {
        echo "<option value='$w'";
        echo ($w == date("Y", $time)) ? " selected" : "";
        echo ">$w</option>";
    }
    echo "</select>\n";
    echo "</td>\n";
    echo "<td style='padding:10px;'><input type='text' name='caption' value='' /></td>\n";
    echo "<td style='padding:10px;'><input type='hidden' name='picUp' value='new' /><input type='submit' value=' Upload ' /></form></td>\n";
    echo "</tr>\n";

    $v = $db->query(
            "SELECT * FROM media WHERE picName != '0' ORDER BY displayTime DESC");
    while ($vRow = $v->fetch()) {
        $pid = $vRow['id'];
        $displayTime = $vRow['displayTime'];
        $d = date("j", $displayTime);
        $m = date("n", $displayTime);
        $y = date("Y", $displayTime);

        $picName = $vRow['picName'];
        $caption = html_entity_decode($vRow['caption'], ENT_QUOTES);
        echo "<tr>\n";
        echo "<td style='padding:10px;'><img src='img/pagePics/$picName' alt='' max-height='200px' max-width='200px' /></td>\n";
        echo "<td style='padding:10px;'><form action='index.php?page=Pics' method='post'>\n";
        echo "D:<select name='d' size='1'>";
        for ($z = 1; $z <= 31; $z ++) {
            echo "<option value='$z'";
            echo ($d == $z) ? " selected" : "";
            echo ">$z</option>";
        }
        echo "</select>\n";
        echo "M:<select name='m' size='1'>";
        for ($x = 1; $x <= 12; $x ++) {
            echo "<option value='$x'";
            echo ($m == $x) ? " selected" : "";
            echo ">$x</option>";
        }
        echo "</select>\n";
        echo "Y:<select name='y' size='1'>";
        for ($w = 2018; $w <= 2040; $w ++) {
            echo "<option value='$w'";
            echo ($y == $w) ? " selected" : "";
            echo ">$w</option>";
        }
        echo "</select>\n";
        echo "</td>\n";
        echo "<td style='padding:10px;'><input type='text' name='caption' value='$caption' /></td>\n";
        echo "<td style='padding:10px;'>Delete:&nbsp;<input type='checkbox' name='del' value='1' /><br><br><input type='hidden' name='picUp' value='$pid' /><input type='submit' value=' Update ' /></form></td>\n";
        echo "</tr>\n";
    }
    echo "</table>";
} else {
    ?>
    <table style="margin: auto;">
        <tr>
            <?php
    $t = 0;
    $v = $db->query(
            "SELECT * FROM media WHERE youtubeCode != '0' ORDER BY displayTime DESC");
    while ($vRow = $v->fetch()) {
        $displayTime = $vRow['displayTime'];
        $youtubeCode = $vRow['youtubeCode'];
        $caption = nl2br(
                make_links_clickable(
                        html_entity_decode($vRow['caption'], ENT_QUOTES)));
        echo "<td style='width:50%; padding:auto:'>";
        echo "<iframe width='560' height='315' src='https://www.youtube.com/embed/$youtubeCode' frameborder='0' allow='accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe><br>";
        echo "<div style='font-size: .75em; margin: 10px 0px;'>" .
                date('l jS \of F Y', $displayTime) . "</div>";
        echo "<div style='font-size: 1.0em; margin: 10px 0px;'>$caption</div>";
        echo "</td>\n";
        $t ++;
        echo ($t % 2 == 0) ? "</tr><tr>\n" : "";
    }
    ?>
        </tr>
        <?php
    $pYears = array();
    $p = $db->query(
            "SELECT displayTime FROM media WHERE picName != '0' ORDER BY displayTime DESC");
    while ($pRow = $p->fetch()) {
        $displayTime = $pRow['displayTime'];
        $pYears[] = date('Y', $displayTime);
    }

    $years = array_unique($pYears);

    foreach ($years as $key => $value) {
        $start = mktime(0, 0, 0, 1, 1, $value);
        $end = mktime(23, 59, 59, 12, 31, $value);
        echo "<tr><td colspan = '2' style='text-align: center; font-size:1.5em; padding:10px;'>$value</td></tr>\n";
        echo "<tr><td colspan = '2' style='font-size:1.0em; padding:10px;'>";
        $p2 = $db->prepare(
                "SELECT COUNT(*) FROM media WHERE picName != '0' && displayTime >= ? && displayTime <= ?");
        $p2->execute(array(
                $start,
                $end
        ));
        $p2Row = $p2->fetch();
        $count = $p2Row[0];
        $p3 = $db->prepare(
                "SELECT * FROM media WHERE picName != '0' && displayTime >= ? && displayTime <= ? ORDER BY displayTime DESC");
        $p3->execute(array(
                $start,
                $end
        ));
        while ($p3Row = $p3->fetch()) {
            $picId = $p3Row['id'];
            $displayTime = $p3Row['displayTime'];
            $picName = $p3Row['picName'];
            $caption = nl2br(
                    make_links_clickable(
                            html_entity_decode($p3Row['caption'], ENT_QUOTES)));
            echo "<div style='position:absolute; margin-left: " . ($count * 20) .
                    "px; margin-top: " . ($count * 5) . "px;'>";
            echo "<a href='img/pagePics/$picName' data-lightbox='picSet$value' data-title='$caption'><img src='img/pagePics/thumb/$picName' alt='' /></a>";
            echo "</div>\n";
            $count --;
        }
        echo "</td></tr>\n";
    }
    ?>
    </table>
<?php
}
?>