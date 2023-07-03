<div style="text-align: center; padding:5px; font-size: 1.5em; color: #cc4541; text-decoration: underline;">News</div>
<?php
$newsYear = (filter_input(INPUT_POST, 'newsYear', FILTER_SANITIZE_NUMBER_INT)) ? filter_input(
        INPUT_POST, 'newsYear', FILTER_SANITIZE_NUMBER_INT) : date("Y", $time);

if (isBoard($myId)) {
    echo "<div style='text-align:center; font-size:1.25em; margin:20px;'>View news from: ";
    $stmt = $db->query("SELECT time FROM news ORDER BY time DESC");
    $menuYear = array();
    while ($row = $stmt->fetch()) {
        $menuYear[] = date("Y", $row['time']);
    }
    $years = array_unique($menuYear);
    foreach ($years as $year) {
        echo ($year == $newsYear) ? " " . "<span style=''>$year</span>" . "" : " " .
                "<a href='index.php?page=News&newsYear=$year' style=''>$year</a>" .
                " ";
    }
    echo "</div>\n";

    echo "<div style='font-weight:bold; font-size:1em; text-decoration:none; font-family:sans-serif;'>\n";
    echo "<form action='index.php?page=News' method='post' enctype='multipart/form-data'>\n";
    echo "Insert a new post:<br>\n";
    echo "Title: <input type='text' name='title' maxlength='190' /><br>\n";
    echo "Content:<br>\n";
    echo "<textarea name='content' cols='75' rows='15'></textarea><br><br>\n";
    echo "Insert picture 1: <input type='file' name='image1' /><br><br>";
    echo "Insert picture 2: <input type='file' name='image2' /><br><br>";
    echo "<input type='hidden' name='postnote' value='new' />\n";
    echo "<input type='submit' value=' Upload ' /></form></div><br><br>\n";
}

$start = ($newsYear == date("Y", $time)) ? ($time - 31536000) : mktime(0, 0, 0,
        1, 1, $newsYear);
$end = ($newsYear == date("Y", $time)) ? $time : mktime(23, 59, 59, 12, 31,
        $newsYear);
$stmt10 = $db->prepare(
        "SELECT * FROM news WHERE time >= ? && time <= ? ORDER BY time DESC");
$stmt10->execute(array(
        $start,
        $end
));
$t = 1;
while ($row10 = $stmt10->fetch()) {
    $id = $row10['id'];
    $highlight = ($h == 0) ? ($t == 1) ? $id : 0 : $h;
    $title = $row10['title'];
    $content = nl2br(
            make_links_clickable(html_entity_decode($row10['text'], ENT_QUOTES)));
    $pic1 = $row10['pic1Name'];
    $pic2 = $row10['pic2Name'];
    $updated = date('l jS \of F Y', $row10['time']);
    if (isBoard($myId)) {
        echo "<div style='font-weight:bold; font-size:1em; text-decoration:none; font-family:sans-serif;'>\n";
        echo "<form action='index.php?page=News' method='post' enctype='multipart/form-data'>\n";
        echo "<div style='cursor:pointer;' onclick='toggleview(\"L$id\")'>Edit this post: <span style='text-decoration:underline;'>$title</span></div><br>\n";
        echo "<div id='L$id' style='display:none; margin-top:10px;'>Title: <input type='text' name='title' maxlength='190' value='$title' /><br><br>\n";
        echo "Last updated: $updated<br><br>\n";
        echo "Content:<br>\n";
        echo "<textarea name='content' cols='75' rows='15'>" . $row10['text'] .
                "</textarea><br><br>\n";
        echo "Picture 1:<br>";
        if (file_exists("img/pagePics/$pic1")) {
            echo "<img src='img/pagePics/thumb/$pic1' alt='' /><br><input type='checkbox' name='delpic1' value='1' /> Delete this pic<br>";
        }
        echo "<input type='file' name='image1' /><br><br>";
        echo "Picture 2:<br>";
        if (file_exists("img/pagePics/$pic2")) {
            echo "<img src='img/pagePics/$pic2' alt='' /><br><input type='checkbox' name='delpic2' value='1' /> Delete this pic<br>";
        }
        echo "<input type='file' name='image2' /><br><br>";
        echo "Delete this post: <input type='checkbox' name='delpost' value='1' /><br><br>\n";
        echo "<input type='hidden' name='postnote' value='$id' />\n";
        echo "<input type='submit' value=' Upload ' /></div></form></div><br><hr /><br>\n";
    } else {
        if ($highlight == $id) {
            echo "<div class='clearfix'>";
            echo "<div style='text-align:center; font-weight:bold; font-size:1.5em; padding:40px; font-family:sans-serif; text-decoration:underline;'>$title</div>\n";
            echo (file_exists("img/pagePics/$pic1") && $pic1 != "noPic.png") ? "<img src='img/pagePics/$pic1' alt='' style='float:right; margin:10px; max-width:400px; max-height:400px;' />" : "";
            echo "<div style='text-align:justify; font-family:sans-serif; padding:10px;'>$content</div>";
            echo (file_exists("img/pagePics/$pic2") && $pic2 != "noPic.png") ? "<div style='margin:auto; width:300px;'><img src='img/pagePics/$pic2' alt='' style='width:300px;' /></div>" : "";
            echo "</div><hr /><br>\n";
        } else {
            echo "<div style='text-align:center; font-weight:bold; font-size:1.5em; padding:10px; font-family:sans-serif; text-decoration:underline;'><a href='index.php?page=News&articleId=$id'>$title</a></div>\n";
            echo "<hr /><br>\n";
        }
    }
}