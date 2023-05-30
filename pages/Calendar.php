<div style="text-align: center; padding:5px; font-size: 1.5em; color: #cc4541; margin-bottom: 40px; text-decoration: underline;">Upcoming Events</div>
<?php
$editCal = (filter_input(INPUT_POST, 'editCal', FILTER_SANITIZE_NUMBER_INT) >= 1) ? filter_input(
        INPUT_POST, 'editCal', FILTER_SANITIZE_NUMBER_INT) : 0;

if (isBoard($myId)) {
    ?>
    <table style='margin:20px 0px;'>
        <?php
    if ($editCal >= 1) {
        $cal = $db->prepare("SELECT * FROM calendar WHERE id = ?");
        $cal->execute(array(
                $editCal
        ));
        $calRow = $cal->fetch();
        $eventId = $calRow['id'];
        $eventTime = $calRow['eventTime'];
        $eventTitle = html_entity_decode($calRow['eventTitle'], ENT_QUOTES);
        $eventDesc = html_entity_decode($calRow['eventDesc'], ENT_QUOTES);
        $picName = $calRow['picName'];

        echo "<tr><td style='padding:5px;'>" .
                "<form action='index.php?page=Calendar' method='post' enctype='multipart/form-data'><input type='date' name='eDate' value='" .
                date("Y-m-d", $eventTime) .
                "' /><br><br><input type='time' name='eTime' value='" .
                date("H:i", $eventTime) . "' /></td>" .
                "<td style='padding:5px;'><input type='text' name='eventTitle' value='" .
                $eventTitle . "' /></td>" . "</tr>\n" . "<tr>" .
                "<td style='padding:5px;' colspan='2'>";
        echo "<div style='text=align: center; border: 2px solid #cc4541; float: left; margin: 0px 10px 10px 0px; padding: 3px;'><input type='file' name='image' /><br><br><input type='radio' name='calPic' value='new'></div>\n";
        $it = new DirectoryIterator(dirname("img/calendar/thumb"));
        foreach ($it as $fileinfo) {
            if ($fileinfo->isFile()) {
                $cName = $fileinfo->getFilename();
                echo "<div style='text=align: center; border: 2px solid #cc4541; float: left; margin: 0px 10px 10px 0px; padding: 3px;'><img src='img/calendar/thumb/$cName' title='' /><br>";
                if ($picName == "noPic.png") {
                    echo "No Pic<br>";
                }
                echo "<input type='radio' name='calPic' value='$cName'";
                if ($cName == $picName) {
                    echo " checked";
                }
                echo "></div>\n";
            }
        }
        echo "<div style=''><textarea name='eventDesc' rows='10' cols='100'>$eventDesc</textarea></div>";
        echo "<div style='text-align: center;'><input type='hidden' name='changeEvent' value='$eventId' /><input type='submit' value=' Make Changes ' /></form></div>";
        echo "</td></tr>\n";
    } else {
        $cal = $db->prepare(
                "SELECT id, eventTime, eventTitle FROM calendar WHERE eventTime > ? ORDER BY eventTime");
        $cal->execute(array(
                $time
        ));
        while ($calRow = $cal->fetch()) {
            $eventId = $calRow['id'];
            $eventTime = $calRow['eventTime'];
            $time = date("D, M jS @ g:i a", $eventTime);
            $eventTitle = html_entity_decode($calRow['eventTitle'], ENT_QUOTES);

            echo "<tr><td style='padding:5px;'>$time - $eventTitle</td>";
            echo "<td style='padding:5px;'><div style='text-align: center;'><form action='index.php?page=Calendar' method='post'><input type='hidden' name='editCal' value='$eventId' /><input type='submit' value=' Edit ' /></form></div></td>";
            echo "</tr>\n";
        }
        echo "<tr><td colspan='2'><div style='font-weight:bold; text-align:center; padding:20px; color: #cc4541;'>New Calendar Event</div></td></tr>";
        echo "<tr><td style='padding:5px;'><form action='index.php?page=Calendar' method='post' enctype='multipart/form-data'><input type='date' name='eDate' value='" .
                date("Y-m-d") .
                "' /><br><br><input type='time' name='eTime' value='" .
                date("H:i") . "' /></td>";
        echo "<td style='padding:5px;'><input type='text' name='eventTitle' value='title' size='100' /></td>";
        echo "</tr>\n";
        echo "<tr>";
        echo "<td style='padding:5px;' colspan='2'>";
        echo "<div style='text=align: center; border: 2px solid #cc4541; float: left; margin: 0px 10px 10px 0px; padding: 3px;'><input type='file' name='image' /><br><br><input type='radio' name='calPic' value='new'></div>\n";
        $it = new DirectoryIterator(dirname("img/calendar/thumb"));
        foreach ($it as $fileinfo) {
            if ($fileinfo->isFile()) {
                echo "<div style='text=align: center; border: 2px solid #cc4541; float: left; margin: 0px 10px 10px 0px; padding: 3px;'><img src='img/calendar/thumb/" .
                        $fileinfo->getFilename() . "' title='' /><br>";
                if ($fileinfo->getFilename() == "noPic.png") {
                    echo "No Pic<br>";
                }
                echo "<input type='radio' name='calPic' value='" .
                        $fileinfo->getFilename() . "'";
                if ($fileinfo->getFilename() == "noPic.png") {
                    echo " checked";
                }
                echo "></div>\n";
            }
        }
        echo "<div style=''><textarea name='eventDesc' rows='10' cols='100'>Description</textarea></div>";
        echo "<div style='text-align: center;'><input type='hidden' name='changeEvent' value='new' /><input type='submit' value=' New Calendar Event ' /></form></div>";
        echo "</td>";
        echo "</tr>\n";
    }
    echo "</table>";
} else {
    ?>
        <div style="width: 100%;">
            <?php
    $highlight = (filter_input(INPUT_GET, 'highlight',
            FILTER_SANITIZE_NUMBER_INT) >= 1) ? filter_input(INPUT_GET,
            'highlight', FILTER_SANITIZE_NUMBER_INT) : 0;
    $cal = $db->prepare(
            "SELECT * FROM calendar WHERE eventTime > ? ORDER BY eventTime");
    $cal->execute(array(
            $time
    ));
    while ($calRow = $cal->fetch()) {
        $eventId = $calRow['id'];
        $eventTime = $calRow['eventTime'];
        $eventTitle = html_entity_decode($calRow['eventTitle'], ENT_QUOTES);
        $eventDesc = nl2br(
                make_links_clickable(
                        html_entity_decode($calRow['eventDesc'], ENT_QUOTES)));
        $picName = $calRow['picName'];
        ?>
                <div style="text-align:center; padding:10px; cursor:pointer;" onclick="toggleview('event<?php

        echo $eventId;
        ?>')">
                    <?php
        echo date("D, M jS @ g:i a", $eventTime);
        ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php
        echo $eventTitle;
        ?>
                </div>
                <div class="clearfix" id="event<?php
        echo $eventId;
        ?>" style="text-align:center; padding:10px; color:#cc4541; <?php
        echo ($highlight == $eventId) ? "display:block;" : "display:none;";
        ?>">
                    <?php
        if (file_exists("img/calendar/$picName") && $picName != "noPic.png") {
            echo "<img src='img/calendar/$picName' title='' style='float: left; margin-right: 10px; border: 1px solid #cc4541; padding: 3px;' />\n";
        }
        echo "$eventDesc\n";
        ?>
                </div>
                <?php
    }
    ?>
        </div>
        <?php
}