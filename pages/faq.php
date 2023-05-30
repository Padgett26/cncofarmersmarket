<div style="text-align: center; color: #cc4541; font-size: 1.5em; margin-bottom: 20px;">Frequently Asked Questions</div>
<table style="width: 75%; margin: auto;">
    <?php
    if (isBoard($myId)) {
        echo "<tr><td style='padding:10px;' colspan='2'><form action='index.php?page=faq' method='post'>Question: <input type='text' name='title' value='' size='80'></td></tr>\n";
        echo "<tr><td style='padding:10px;' colspan='2'>Description:<br><textarea name='description' cols='60' rows='10'></textarea></td></tr>\n";
        echo "<tr><td style='width:50%; padding:10px 0px 50px 0px;' colspan='2'>";
        echo "<input type='hidden' name='faqUp' value='new' /><input type='submit' value=' Upload faq ' /></form>";
        echo "</td></tr>\n";

        $a = $db->query("SELECT * FROM faq ORDER BY rand()");
        while ($aRow = $a->fetch()) {
            $fId = $aRow['id'];
            $title = $aRow['title'];
            $description = html_entity_decode($aRow['description'], ENT_QUOTES);
            echo "<tr><td style='padding:10px;'><div style='text-align:left;'><span style='font-size:1.25em; cursor:pointer; color: #cc4541' onclick=\"toggleview('faq$fId')\">$title</span></div></td></tr>\n";
            echo "<tr id='faq$fId' style='display:none;'><td colspan='2'><table>";
            echo "<tr><td style='padding:10px;' colspan='2'><form action='index.php?page=faq' method='post'>Question: <input type='text' name='title' value='$title' size='80'></td></tr>\n";
            echo "<tr><td style='padding:10px;' colspan='2'>Description:<br><textarea name='description' cols='60' rows='10'>$description</textarea></td></tr>\n";
            echo "<tr><td style='width:50%; padding:10px 0px 50px 0px;'>";
            echo "<input type='hidden' name='faqUp' value='$fId' /><input type='submit' value=' Update faq ' /></form>";
            echo "</td><td style='width:50%; padding:10px 0px 50px 0px;'>";
            echo "<form action='index.php?page=faq' method='post'>";
            echo "<input type='hidden' name='faqDown' value='$fId' /><input type='submit' value=' Delete faq ' /></form>";
            echo "</td></tr>\n";
            echo "</table></td></tr>";
        }
    } else {
        $a = $db->query("SELECT * FROM faq ORDER BY rand()");
        while ($aRow = $a->fetch()) {
            $fId = $aRow['id'];
            $title = $aRow['title'];
            $description = nl2br(
                    make_links_clickable(
                            html_entity_decode($aRow['description'], ENT_QUOTES)));
            echo "<tr><td style='padding:10px;'><div style='text-align:left;'><span style='font-size:1.25em; cursor:pointer; color: #cc4541' onclick=\"toggleview('faq$fId')\">$title</span></div></td></tr>\n";
            echo "<tr id='faq$fId' style='display:none;'><td style='padding:20px;'><div style='text-align:justify; font-size:1em;'>$description</div></td></tr>\n";
        }
    }
    ?>
</table>