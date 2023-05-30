<div style="text-align: center; color: #cc4541; font-size: 1.5em; margin-bottom: 20px; text-decoration: underline;">Links</div>
<table style="width: 350px; margin: auto;">
    <?php
    if (isBoard($myId)) {
        echo "<tr><td style='padding:10px;' colspan='2'><form action='index.php?page=Links' method='post'>Link title: <input type='text' name='title' value='' /></td></tr>\n";
        echo "<tr><td style='padding:10px;' colspan='2'>Link address: <input type='text' name='link' value='' /></td></tr>\n";
        echo "<tr><td style='padding:10px;' colspan='2'>Description:<br><textarea name='description' cols='60' rows='10'></textarea></td></tr>\n";
        echo "<tr><td style='width:50%; padding:10px 0px 50px 0px;' colspan='2'>";
        echo "<input type='hidden' name='linkUp' value='new' /><input type='submit' value=' Upload link ' /></form>";
        echo "</td></tr>\n";

        $a = $db->query("SELECT * FROM links ORDER BY rand()");
        while ($aRow = $a->fetch()) {
            $lId = $aRow['id'];
            $title = $aRow['title'];
            $description = nl2br(
                    make_links_clickable(
                            html_entity_decode($aRow['description'], ENT_QUOTES)));
            $link = $aRow['link'];
            echo "<tr><td style='padding:10px;' colspan='2'><div style='text-align:center; font-size:1.25em; cursor:pointer;' onclick='toggleview(\"link" .
                    $lId . "\")'>$title</div></td></tr>\n";
            echo "<tr id='link$lId' style='display:none;'><td colspan='2'><table><tr><td style='padding:10px;' colspan='2'><form action='index.php?page=Links' method='post'>Link title: <input type='text' name='title' value='$title' /></td></tr>\n";
            echo "<tr><td style='padding:10px;' colspan='2'>Link address: <input type='text' name='link' value='$link' /></td></tr>\n";
            echo "<tr><td style='padding:10px;' colspan='2'>Description:<br><textarea name='description' cols='60' rows='10'>$description</textarea></td></tr>\n";
            echo "<tr><td style='width:50%; padding:10px 0px 50px 0px;'>";
            echo "<input type='hidden' name='linkUp' value='$lId' /><input type='submit' value=' Update link ' /></form>";
            echo "</td><td style='width:50%; padding:10px 0px 50px 0px;'>";
            echo "<form action='index.php?page=Links' method='post'>";
            echo "<input type='hidden' name='linkDown' value='$lId' /><input type='submit' value=' Delete link ' /></form>";
            echo "</td></tr></table></td></tr>\n";
        }
    } else {
        $a = $db->query("SELECT * FROM links ORDER BY rand()");
        while ($aRow = $a->fetch()) {
            $title = $aRow['title'];
            $description = nl2br(
                    make_links_clickable(
                            html_entity_decode($aRow['description'], ENT_QUOTES)));
            $link = $aRow['link'];
            echo "<tr><td style='padding:10px;'><div style='text-align:center; font-size:1.25em;'><a href='$link' target='_blank'>$title</a></div></td></tr>";
            echo "<tr><td style='padding:10px;'><div style='text-align:center; font-size:1em;'><a href='$link' target='_blank'>$link</a></div></td></tr>";
            echo "<tr><td style='padding:10px 0px 50px 0px;'><div style='text-align:justify; font-size:1em;'>$description</div></td></tr>";
        }
    }
    ?>
</table>