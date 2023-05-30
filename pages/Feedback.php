<?php
if (isBoard($myId)) {
    echo "<div style='text-align: center; color: #cc4541; font-size: 1.5em; margin:40px 0px 20px 0px; text-decoration: underline;'>Feedback</div>";
    echo "<div style='text-align:center; margin:20px;'>View <a href='index.php?page=Feedback&limitQuan=10&v=0'>Last 10</a> ~ <a href='index.php?page=Feedback&limitQuan=25&v=0'>Last 25</a> ~ <a href='index.php?page=Feedback&limitQuan=999&v=0'>All</a> ~ <a href='index.php?page=Feedback&limitQuan=999&v=1'>Hidden</a></div>";
    $q = (filter_input(INPUT_GET, 'limitQuan', FILTER_SANITIZE_NUMBER_INT) >= 5) ? filter_input(
            INPUT_GET, 'limitQuan', FILTER_SANITIZE_NUMBER_INT) : 10;
    $v = (filter_input(INPUT_GET, 'v', FILTER_SANITIZE_NUMBER_INT) == 1) ? 1 : 0;
    $h = $db->prepare(
            "SELECT * FROM feedback WHERE hidden = ? ORDER BY time DESC LIMIT $q");
    $h->execute(array(
            $v
    ));
    while ($hRow = $h->fetch()) {
        $fId = $hRow['id'];
        $fTime = $hRow['time'];
        $fName = $hRow['name'];
        $fText = $hRow['feedback'];
        $fReply = $hRow['reply'];
        $fRepliedTo = $hRow['repliedTo'];
        $fEmail = $hRow['email'];
        $fPhone = $hRow['phone'];

        echo "<div style='text-align:center; border:3px solid #cc4541; padding:10px;'>\n";
        echo "From:<br>$fName<br>" . date("D, M jS @ g:i a", $fTime) .
                "<br>$fEmail<br>$fPhone<br><br>";
        echo "Feedback:<br><p style=''>$fText</p><br><br>";
        if ($fRepliedTo == 1) {
            echo "Reply:<br><p style=''>$fReply</p><br><br>";
        } else {
            echo "Reply:<br><form action='index.php?page=Feedback' method='post'><textarea name='reply' cols='60' rows='10'></textarea><br><input type='hidden' name='fReply' value='$fId' /><input type='submit' value=' Reply ' /><br><br></form>";
        }
        if ($v == 1) {
            echo "<div style='text-align: center;'><form action='index.php?page=Feedback' method='post'><input type='hidden' name='deleteReply' value='$fId' /><input type='submit' value=' Perminately Delete ' /></form></div></div>";
        } else {
            echo "<div style='text-align: center;'><form action='index.php?page=Feedback' method='post'><input type='hidden' name='dReply' value='$fId' /><input type='submit' value=' Delete this feedback ' /></form></div></div>";
        }
    }
} elseif ($vendorId >= 1 || $custId >= 1) {
    ?>
    <div style="text-align: center; color: #cc4541; font-size: 1.5em; margin:40px 0px 20px 0px; text-decoration: underline;">Feedback</div>
    <div style="text-align: center; color: #cc4541; font-size: 1.25em; margin:20px 0px 20px 0px;">We welcome any feedback you may have.</div>
    <?php
    echo $submitted;
    ?>
    <form id='feedbackForm' action="index.php?page=Feedback" method="post">
        <table style="width: 350px; margin: auto;">
            <tr>
                <td style="text-align: right; height:30px; padding:auto;">
                    * Name:
                </td>
                <td style="text-align: left; height:30px; padding:auto;">
                    <input type="text" name="fName" value="" required />
                </td>
            </tr>
            <tr>
                <td style="text-align: right; height:30px; padding:auto;">
                    * Email:
                </td>
                <td style="text-align: left; height:30px; padding:auto;">
                    <input type="email" name="fEmail" required />
                </td>
            </tr>
            <tr>
                <td style="text-align: right; height:30px; padding:auto;">
                    Phone:
                </td>
                <td style="text-align: left; height:30px; padding:auto;">
                    <input type="text" name="fPhone" value="">
                </td>
            </tr>
            <tr>
                <td style="text-align: left; height:30px; padding:auto;" colspan="2">
                    * Text:
                </td>
            </tr>
            <tr>
                <td style="text-align: left;" colspan="2">
                    <textarea name="fText" rows="10" cols="40"></textarea>
                </td>
            </tr>
            <tr>
                <td style="text-align: center;" colspan="2">
                    <input type="hidden" name="feedbackUp" value="1" /><input style="width:100px; margin:auto;" type="submit" value=" Send feedback " />
                </td>
            </tr>
            <tr>
                <td colspan='2'>

                </td>
            </tr>
        </table>
    </form>
    <?php
} else {
    echo "<div style='text-align:center; font-weight:bold; font-size: 1.5em;'>Please <a href='index.php?page=LogIn' target='_self'>Log In</a> to submit feedback.</div>";
}