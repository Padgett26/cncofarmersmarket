<div style="text-align: center; color: #cc4541; font-size: 1.5em; margin-bottom: 20px; text-decoration: underline;">Legal Documents</div>

<?php
if (filter_input(INPUT_POST, 'show', FILTER_SANITIZE_STRING)) {
    $show = filter_input(INPUT_POST, 'show', FILTER_SANITIZE_STRING);
} else {
    $show = 'x';
}

if (isBoard($myId)) {
    ?>
    <div style="font-weight:bold; color: #cc4541; font-size: 1.25em; margin: 40px 0px 10px 0px; text-decoration:underline;">Positions</div>
    <div style="margin-left: 10px;">
        <div style='margin-left:20px;'><span style='cursor:pointer; font-size:1.25em; font-weight:bold; color: #cc4541;' onclick="toggleview('jobNew')">Upload New Position</span></div>
        <div style='margin-left:20px; border:1px solid #cc4541; padding:10px; display:none;' id='jobNew'>
            <form action="index.php?page=Legal" method="post">
                <div style="padding:10px; text-align: left; color: #cc4541; font-size: 1.25em; margin-top: 20px;">
                    Position Title: <input type="text" name="jobName" value="">
                </div>
                <div style="padding:10px; text-align: left; color: #cc4541; font-size: 1.25em; margin-top: 20px;">
                    Display position on website? YES:<input type="radio" name="display" value="1" checked> NO:<input type="radio" name="display" value="0">
                </div>
                <div style="padding:10px; text-align: left; color: #cc4541; font-size: 1.25em; margin-top: 20px;">
                    Applications must be submitted by: Date: M <select name="jobM" size="1">
                        <?php
    for ($i = 1; $i <= 12; $i ++) {
        echo "<option value='$i'>$months[$i]</option>\n";
    }
    ?>
                    </select> D <select name="jobD" size="1">
                        <?php
    for ($i = 1; $i <= 31; $i ++) {
        echo "<option value='$i'>$i</option>\n";
    }
    ?>
                    </select> Y <select name="jobY" size="1">
                        <?php
    for ($i = 2019; $i <= 2030; $i ++) {
        echo "<option value='$i'>$i</option>\n";
    }
    ?>
                    </select>
                </div>
                <div style="padding:10px; border:2px solid #cc4541;">
                    <textarea name="jobDesc" cols="60" rows="20">Position Description</textarea>
                </div>
                <div style="padding-top: 10px;">
                    <input type="hidden" name="jobUp" value="new">
                    <input type="submit" value=" Upload ">
                </div>
            </form>
        </div>
        <div style="padding:10px; text-align: left; margin-top: 20px;">
            <span style='color: #cc4541; font-size: 1.25em;'>Edit position listings</span><br><br>
            <?php
    $g = $db->query("SELECT * FROM jobs ORDER BY jobName");
    while ($gRow = $g->fetch()) {
        $jobId = $gRow['id'];
        $jobName = $gRow['jobName'];
        $jobDesc = $gRow['jobDesc'];
        $display = $gRow['display'];
        $endDate = $gRow['endDate'];
        $jobM = date("n", $endDate);
        $jobD = date("j", $endDate);
        $jobY = date("Y", $endDate);
        ?>
                <div style='margin-left:20px;'><span style='cursor:pointer; font-size:1.25em; font-weight:bold; color: #cc4541;' onclick="toggleview('job<?php

        echo $jobId;
        ?>')"><?php

        echo $jobName;
        ?></span></div>
                <div style='margin-left:20px; border:1px solid #cc4541; padding:10px; display:none;' id='job<?php

        echo $jobId;
        ?>'>
                    <form action="index.php?page=Legal" method="post">
                        <div style="padding:10px; text-align: left; color: #cc4541; font-size: 1.25em; margin-top: 20px;">
                            Position Title: <input type="text" name="jobName" value="<?php

        echo $jobName;
        ?>">
                        </div>
                        <div style="padding:10px; text-align: left; color: #cc4541; font-size: 1.25em; margin-top: 20px;">
                            Display position on website? YES:<input type="radio" name="display" value="1"<?php

        echo ($display == '1') ? " checked" : "";
        ?>> NO:<input type="radio" name="display" value="0"<?php

        echo ($display == '0') ? " checked" : "";
        ?>>
                        </div>
                        <div style="padding:10px; text-align: left; color: #cc4541; font-size: 1.25em; margin-top: 20px;">
                            Applications must be submitted by: Date: M <select name="jobM" size="1">
                                <?php
        for ($i = 1; $i <= 12; $i ++) {
            echo "<option value='$i'";
            echo ($i == $jobM) ? " selected" : "";
            echo ">$months[$i]</option>\n";
        }
        ?>
                            </select> D <select name="jobD" size="1">
                                <?php
        for ($i = 1; $i <= 31; $i ++) {
            echo "<option value='$i'";
            echo ($i == $jobD) ? " selected" : "";
            echo ">$i</option>\n";
        }
        ?>
                            </select> Y <select name="jobY" size="1">
                                <?php
        for ($i = 2019; $i <= 2030; $i ++) {
            echo "<option value='$i'";
            echo ($i == $jobY) ? " selected" : "";
            echo ">$i</option>\n";
        }
        ?>
                            </select>
                        </div>
                        <div style="padding:10px; border:2px solid #cc4541;">
                            <textarea name="jobDesc" cols="60" rows="20"><?php

        echo $jobDesc;
        ?></textarea>
                        </div>
                        <div style="padding-top: 10px;">
                            <input type="hidden" name="jobUp" value="<?php

        echo $jobId;
        ?>">
                            <input type="submit" value=" Upload ">
                        </div>
                    </form>
                </div>
                <?php
        $appQuery = $db->prepare(
                "SELECT * FROM applications WHERE jobId = ? ORDER BY RAND()");
        $appQuery->execute(array(
                $jobId
        ));
        while ($appRow = $appQuery->fetch()) {
            $appId = $appRow['id'];
            $appName = $appRow['name'];
            $appAddress = $appRow['address'];
            $appEmail = $appRow['email'];
            $appPhone = $appRow['phone'];
            $appAbout = nl2br(
                    make_links_clickable(
                            html_entity_decode($appRow['about'], ENT_QUOTES)));
            $appResume = nl2br(
                    make_links_clickable(
                            html_entity_decode($appRow['resume'], ENT_QUOTES)));
            ?>
                    <div style='margin-left:40px;'><span style='cursor:pointer; font-size:1em; font-weight:bold; color: #cc4541;' onclick="toggleview('app<?php

            echo $appId;
            ?>')"><?php

            echo $appName;
            ?></span></div>
                    <div style='margin-left:40px; border:1px solid #cc4541; padding:10px; display:none;' id='app<?php

            echo $appId;
            ?>'>
                        <?php
            echo $appAddress . "<br><br>" . $appEmail . "<br><br>" . $appPhone .
                    "<br><br>" . $appAbout . "<br><br>" . $appResume;
            ?>
                    </div>
                    <?php
        }
    }
    ?>
        </div></div>
    <?php
} else {
    $i = $db->prepare(
            "SELECT COUNT(*) FROM jobs WHERE endDate >= ? && display = ?");
    $i->execute(array(
            $time,
            "1"
    ));
    $iRow = $i->fetch();
    $x = $iRow[0];
    if ($x >= 1) {
        ?>
        <div style="font-weight:bold; color: #cc4541; font-size: 1.25em; margin: 40px 0px 10px 0px; text-decoration:underline;">Positions Available</div>
        <div style="margin-left: 10px;">
            <?php
        $h = $db->prepare(
                "SELECT * FROM jobs WHERE endDate >= ? && display = ? ORDER BY endDate");
        $h->execute(array(
                $time,
                '1'
        ));
        while ($hRow = $h->fetch()) {
            $jobId = $hRow['id'];
            $jobName = $hRow['jobName'];
            $text = html_entity_decode($hRow['jobDesc'], ENT_QUOTES);
            $jobDesc = nl2br(make_links_clickable($text));
            echo "<div style='margin-left:20px;'><span style='cursor:pointer; font-size:1.25em; font-weight:bold; color: #cc4541;' onclick=\"toggleview('jobText$jobId')\">$jobName</span></div>";
            echo "<div style='margin-left:20px; border:1px solid #cc4541; padding:10px; display:none;' id='jobText$jobId'>$jobDesc</div>";
        }
        ?>
        </div>
        <div style="margin-left: 10px;">
            <?php
        echo "<div style='margin:20px 0px 0px 20px;'><span style='cursor:pointer; font-size:1.25em; font-weight:bold; color: #cc4541;' onclick=\"toggleview('jobApplication')\">Position Application</span></div>";
        echo "<div style='margin-left:20px; border:1px solid #cc4541; padding:10px; display:none;' id='jobApplication'>";
        ?>
            <form action="index.php?page=Legal" method="post">
                <div style="padding:10px; text-align: left; color: #cc4541; font-size: 1.25em; margin-top: 20px;">
                    Position Applying for: <select name="jobId" size="1">
                        <?php
        $j = $db->prepare(
                "SELECT id, jobName FROM jobs WHERE endDate >= ? && display = ? ORDER BY endDate");
        $j->execute(array(
                $time,
                '1'
        ));
        while ($jRow = $j->fetch()) {
            $jobId = $jRow['id'];
            $jobName = $jRow['jobName'];
            echo "<option value='$jobId'>$jobName</option>\n";
        }
        ?>
                    </select>
                </div>
                <div style="padding:10px; text-align: left; color: #cc4541; font-size: 1.25em; margin-top: 20px;">
                    Name: <input type="text" name="name" value="">
                </div>
                <div style="padding:10px; text-align: left; color: #cc4541; font-size: 1.25em; margin-top: 20px;">
                    Address: <input type="text" name="address" value="">
                </div>
                <div style="padding:10px; text-align: left; color: #cc4541; font-size: 1.25em; margin-top: 20px;">
                    Email: <input type="email" name="email" value="">
                </div>
                <div style="padding:10px; text-align: left; color: #cc4541; font-size: 1.25em; margin-top: 20px;">
                    Phone: <input type="text" name="phone" value="">
                </div>
                <div style="padding:10px; text-align: left; color: #cc4541; font-size: 1.25em; margin-top: 20px;">
                    Who are you / your qualifications / how are you perfect for this position:<br><textarea name="about" cols="60" rows="20"></textarea>
                </div>
                <div style="padding:10px; text-align: left; color: #cc4541; font-size: 1.25em; margin-top: 20px;">
                    Resume:<br><textarea name="resume" cols="60" rows="20"></textarea>
                </div>
                <div style="padding-top: 10px;">
                    <input type="hidden" name="appUp" value="1">
                    <input type="submit" value=" Submit your application ">
                </div>
            </form>
            <?php
        echo "</div>";
        ?>
        </div>
        <?php
    }
}
?>
<div style="font-weight:bold; color: #cc4541; font-size: 1.25em; margin: 40px 0px 10px 0px; text-decoration:underline;">Minutes</div>
<div style="margin-left: 10px;">

    <?php
    if (isBoard($myId)) {
        ?>
        <div style="text-align: left; color: #cc4541; font-size: 1.25em; margin: 20px 0px 10px 0px;">Upload Minutes</div>
        <form action="index.php?page=Legal" method="post">
            <div style="padding:10px; text-align: left; color: #cc4541; font-size: 1.25em; margin-top: 20px;">
                Meeting Date: M <select name="minM" size="1">
                    <?php
        for ($i = 1; $i <= 12; $i ++) {
            echo "<option value='$i'>$months[$i]</option>\n";
        }
        ?>
                </select> D <select name="minD" size="1">
                    <?php
        for ($i = 1; $i <= 31; $i ++) {
            echo "<option value='$i'>$i</option>\n";
        }
        ?>
                </select> Y <select name="minY" size="1">
                    <?php
        for ($i = 2019; $i <= 2030; $i ++) {
            echo "<option value='$i'>$i</option>\n";
        }
        ?>
                </select>
            </div>
            <div style="padding:10px; border:2px solid #cc4541;">
                <textarea name="text" cols="60" rows="20">Minutes text</textarea>
            </div>
            <div style="padding-top: 10px;">
                <input type="hidden" name="minUp" value="new">
                <input type="submit" value=" Upload ">
            </div>
        </form>

        <div style="padding:10px; text-align: left; margin-top: 20px;">
            <span style='color: #cc4541; font-size: 1.25em;'>Remove past minutes</span><br><br>
            <?php
        $f = $db->query("SELECT * FROM minutes ORDER BY meetingDate");
        while ($fRow = $f->fetch()) {
            $pid = $fRow['id'];
            $date = $fRow['meetingDate'];
            $meetingDate = date("M jS, Y", $date);

            echo "<form action='index.php?page=Legal' method='post'><span style='color: #cc4541;'>$meetingDate</span> <input type='hidden' name='delId' value='$pid'> <input type='hidden' name='minUp' value='del'> <input type='submit' value=' Delete '></form><br>";
        }
        ?>
        </div>
        <?php
    } else {
        $mYears = [];
        $g = $db->query("SELECT meetingDate FROM minutes ORDER BY meetingDate");
        while ($gRow = $g->fetch()) {
            $md = $gRow['meetingDate'];
            $mYears[] = date("Y", $md);
        }
        $mY = array_unique($mYears);
        foreach ($mY as $y) {
            $start = mktime(0, 0, 0, 1, 1, $y);
            $end = mktime(23, 59, 59, 12, 31, $y);
            echo "<span style='font-weight:bold; color: #cc4541;'>- $y</span>";
            $h = $db->prepare(
                    "SELECT * FROM minutes WHERE meetingDate >= ? && meetingDate <= ? ORDER BY meetingDate");
            $h->execute(array(
                    $start,
                    $end
            ));
            while ($hRow = $h->fetch()) {
                $mId = $hRow['id'];
                $mMd = $hRow['meetingDate'];
                $displayDate = date("M jS, Y", $mMd);
                $text = html_entity_decode($hRow['text'], ENT_QUOTES);
                $displayText = nl2br(make_links_clickable($text));
                echo "<div style='margin-left:20px;'><span style='cursor:pointer; color: #cc4541;' onclick=\"toggleview('minText$mId')\">$displayDate</span></div>";
                echo "<div style='margin-left:20px; border:1px solid #cc4541; padding:10px; display:none;' id='minText$mId'>$displayText</div>";
            }
        }
    }
    ?>
</div>
<div style="font-weight:bold; color: #cc4541; font-size: 1.25em; margin: 40px 0px 10px 0px; text-decoration:underline;">Documents</div>
<?php
if (isBoard($myId)) {
    ?>

    <div style="text-align: left; color: #cc4541; font-size: 1.25em; margin: 20px 0px 10px 0px;">New Legal Document</div>
    <form action="index.php?page=Legal" method="post">
        <div style="padding:10px; text-align: left; color: #cc4541; font-size: 1.25em; margin-top: 20px;">
            <input type="text" name="docName" value="" placeholder="New doc name" >
        </div>
        <div style="padding:10px; border:2px solid #cc4541;">
            <textarea name="text" cols="60" rows="20">New doc text</textarea>
        </div>
        <div style="">
            Display this document on the website? Yes:<input type="radio" name='display' value='1' checked> No:<input type="radio" name='display' value='0'><br><br>
            <input type="hidden" name="docUp" value="new">
            <input type="submit" value=" Upload ">
        </div>
    </form>
    <?php
}

$a = $db->query("SELECT * FROM legalDocs ORDER BY docName");
while ($aRow = $a->fetch()) {
    $id = $aRow['id'];
    $docName = $aRow['docName'];
    $text = html_entity_decode($aRow['text'], ENT_QUOTES);
    $displayText = nl2br(make_links_clickable($text));
    $display = $aRow['display'];

    if (isBoard($myId)) {
        echo "<div style='margin:20px 0px 0px 10px;'><span style='cursor:pointer; font-size: 1.25em; color: #cc4541;' onclick=\"toggleview('editDoc$id')\">$docName</span></div>";
        echo "<div id='editDoc$id' style='display:none;'>";
        ?>
        <form action="index.php?page=Legal" method="post">
            <div style="padding:10px; text-align: left; color: #cc4541; font-size: 1.25em; margin-top: 10px;">
                <input type="text" name="docName" value="<?php

        echo $docName;
        ?>">
            </div>
            <div style="padding:10px; border:2px solid #cc4541;">
                <textarea name="text" cols="60" rows="20"><?php

        echo $text;
        ?></textarea>
            </div>
            <div style="">
                Display this document on the website? Yes:<input type="radio" name='display' value='1'
                <?php
        if ($display == '1') {
            echo " checked";
        }
        echo "> No:<input type='radio' name='display' value='0'";
        if ($display == '0') {
            echo " checked";
        }
        ?>
                                                                 ><br><br>
                <input type="hidden" name="docUp" value="<?php

        echo $id;
        ?>">
                <input type="submit" value=" Upload ">
            </div>
        </form>
        <?php
        echo "</div>";
    } else {
        ?>
        <div style="padding:10px; text-align: left; color: #cc4541; font-size: 1.25em; margin-top: 10px; cursor:pointer;" onclick="toggleview('docText<?php

        echo $id;
        ?>')">
            <?php

        echo $docName;
        ?>
        </div>
        <div id="docText<?php

        echo $id;
        ?>" style="padding:10px; border:2px solid #cc4541; display:none;">
            <?php

        echo $displayText;
        ?>
        </div>
        <?php
    }
}
?>
<div style="padding:10px; text-align: left; color: #cc4541; font-size: 1.25em; margin-top: 10px; cursor:pointer;" onclick="toggleview('vendorApp')">Vendor Agreement</div>
<?php
$display = ($show == "vendorApp") ? "block" : "none";
?>
<div id="vendorApp" style="padding:10px; border:2px solid #cc4541; display:<?php

echo $display;
?>;">
    <?php

    include "includes/vendorApp.php";
    ?>
</div>
<div style="font-weight:bold; color: #cc4541; font-size: 1.25em; margin: 40px 0px 10px 0px; text-decoration:underline;">Available PDF's</div>
<div style="font-size: 1em; margin-left: 10px;">
    <?php
    if (isBoard($myId)) {
        echo "<span style='color: #cc4541;'>Add a new pdf:</span><br><form action='index.php?page=Legal' method='post' enctype='multipart/form-data'><input type='file' name='pdfImage' value=''><input type='text' name='pdfName' value='' placeholder='PDF name'><input type='hidden' name='pdfUp' value='new'><input type='submit' value=' Upload '></form><br>";
    }
    $e = $db->query("SELECT * FROM pdf ORDER BY pdfName");
    while ($eRow = $e->fetch()) {
        $pid = $eRow['id'];
        $pdfName = $eRow['pdfName'];
        $uploaded = $eRow['uploaded'];
        $upDate = date("M jS, Y", $uploaded);

        if (isBoard($myId)) {
            echo "<form action='index.php?page=Legal' method='post'><span style='color: #cc4541;'>$pdfName</span> <span style='font-size: 0.75em;'>- uploaded $upDate</span> <input type='hidden' name='pdfDel' value='$pid'><input type='submit' value=' Delete '></form><br>";
        } else {
            echo "<a href='pdf/" . $pdfName .
                    ".pdf' target='_blank' style='color: #cc4541;'>" . $pdfName .
                    "</a> <span style='font-size: 0.75em;'>- uploaded $upDate</span><br>";
        }
    }
    ?>
</div>

<div style="color: #cc4541; font-size: 1.25em; margin: 40px 0px 10px 0px; cursor:pointer;" onclick="toggleview('unsubscribe')">Unsubscibe from the CCFM mailing list</div>
<div id="unsubscribe" style="font-size: 1em; margin-left: 10px; display:none;">
    Please enter the email address to which you are receiving the mailing, and we will uncheck the 'send emails to' box:<br><br>
    <form action='index.php?page=Legal' method='post'>
        <input type="text" name="unsubEmail" value=""><br><br>
        <input type="hidden" name="unsubscribe" value="1"><input type="submit" value=" Unsubscribe ">
    </form>
</div>