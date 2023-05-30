<!-- Beginning of Footer -->
<footer id="footer" style="margin-top:20px; height: 650px; background-image: url('img/footer.jpg'); background-repeat: no-repeat; background-size: 100%;">
    <table cellspacing="0px" style="width:100%;">
        <tr>
            <td style="width:10%; text-align:center;"></td>
            <td style="width:20%; text-align:center;"><a href='index.php?page=Feedback'>Contact Us</a></td>
            <td style="width:20%; text-align:center;"><a href='index.php?page=faq'>FAQ</a></td>
            <td style="width:20%; text-align:center;"><a href='index.php?page=privacyPolicy'>Privacy Policy</a></td>
            <td style="width:20%; text-align:center;">
                <?php
                echo ($custId == 0) ? "<a href='index.php?page=LogIn'>Log in</a>" : "<a href='index.php?page=Home&logout=yep'>Log Out</a>";
                ?>
            </td>
        </tr>
    </table>
</footer>
<!-- End of Footer -->