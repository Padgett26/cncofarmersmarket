<!-- Beginning of Head -->

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-158774307-1"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'UA-158774307-1');
</script>


<title>Cheyenne County KS Farmer's Market</title>
<meta charset="UTF-8">
<meta http-equiv='Content-Type'     content='text/html; charset=UTF-8' />
<meta name="viewport"               content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=1" />
<meta name="keywords"               content="cheyenne county kansas, farmers market, local food, local arts, local crafts, st francis kansas" />
<meta name="description"            content="To help strengthen our local food system, through educating and empowering community support of a marketplace that supports healthy, local, and sustainable food and homemade items to contribute to our local economy." />
<?php
if ($page == "News") {
    ?>
    <meta property="fb:app_id"          content="659827678160110" />
    <meta property="og:site_name"       content="<?php
    echo $siteTitle;
    ?>" />
    <meta property="og:type"            content="article" />
    <meta property="og:url"         content="https://cncofarmersmarket.com/index.php?page=News&articleId=<?php

    echo $artId;
    ?>">
    <meta property="og:title"       content="<?php
    echo $artTitle;
    ?>">
    <meta property="og:description" content="<?php
    echo $artContent;
    ?>">
    <?php
    list ($widthm, $heightm) = (getimagesize("img/pagePics/$artPic") != null) ? getimagesize(
            "img/pagePics/$artPic") : null;
    echo "<meta property='og:image'       content='https://cncofarmersmarket.com/img/pagePics/$artPic'>\n";
    echo "<meta property='og:image:width'       content='$widthm'>\n";
    echo "<meta property='og:image:height'       content='$heightm'>\n";
}
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<link rel="stylesheet" href="includes/lightbox2/css/lightbox.min.css">
<style>
    * {
        box-sizing: border-box;
    }

    @font-face {
        font-family: script;
        src: url(includes/GreatVibes-Regular.otf);
    }

    /* Style the body */
    body {
        font-family: Arial;
        margin: 0;
        background-color: #e2f6f7;
    }

    td {
        vertical-align:top;
    }

    th {
        text-align:center;
        font-weight:bold;
    }

    a {
        color: #cc4541;
        text-decoration: none;
    }

    /* Header/logo Title */
    .header {
        padding: 0px;
        text-align: center;
        background: #1abc9c;
        color: white;
    }

    /* Style the top navigation bar */
    .navbar {
        display: flex;
        background-color: #333;
        text-align: center;
    }

    /* Style the navigation bar links */
    .navbar a {
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        text-align: center;
    }

    /* Change color on hover */
    .navbar a:hover {
        background-color: #ddd;
        color: black;
    }

    /* Column container */
    .row {
        display: flex;
        flex-wrap: wrap;
    }

    /* Create two unequal columns that sits next to each other */
    /* Sidebar/left column */
    .side {
        flex: 20%;
        background-color: #e2f6f7;
        padding: 20px;
    }

    /* Main column */
    .main {
        flex: 80%;
        background-color: white;
        padding: 40px;
    }

    /* Fake image, just for this example */
    .fakeimg {
        background-color: #aaa;
        width: 100%;
        padding: 20px;
    }

    /* Footer */
    .footer {
        text-align: center;
    }

    /* Responsive layout - when the screen is less than 700px wide, make the two columns stack on top of each other instead of next to each other */
    @media screen and (max-width: 700px) {
        .row, .navbar {
            flex-direction: column;
        }
        .sidenav {padding-top: 15px;}
        .sidenav a {font-size: 18px;}
    }

    .sidenav {
        height: 100%;
        width: 0;
        position: fixed;
        z-index: 1;
        top: 0;
        left: 0;
        background-color: #111;
        overflow-x: hidden;
        transition: 0.5s;
        padding-top: 60px;
    }

    .sidenav a {
        padding: 8px 8px 8px 32px;
        text-decoration: none;
        font-size: 25px;
        color: #818181;
        display: block;
        transition: 0.3s;
    }

    .sidenav a:hover {
        color: #f1f1f1;
    }

    .sidenav .closebtn {
        position: absolute;
        top: 0;
        right: 25px;
        font-size: 36px;
        margin-left: 50px;
    }

    #main {
        transition: margin-left .5s;
        padding: 16px;
    }

    .clearfix {
        overflow: auto;
    }

    /* Tooltip container */
    .tooltip {
        position: relative;
        display: inline-block;
        border-bottom: 1px dotted black; /* If you want dots under the hoverable text */
    }

    /* Tooltip text */
    .tooltip .tooltiptext {
        visibility: hidden;
        width: 120px;
        background-color: black;
        color: #fff;
        text-align: center;
        padding: 5px 0;
        border-radius: 6px;

        /* Position the tooltip text - see examples below! */
        position: absolute;
        z-index: 1;
    }

    /* Show the tooltip text when you mouse over the tooltip container */
    .tooltip:hover .tooltiptext {
        visibility: visible;
    }
</style>

<script type="text/javascript">
    function toggleview(itm) {
        var itmx = document.getElementById(itm);
        if (itmx.style.display === "none") {
            itmx.style.display = "block";
        } else {
            itmx.style.display = "none";
        }
    }

    var height = window.innerHeight
            || document.documentElement.clientHeight
            || document.body.clientHeight;
    var width = window.innerWidth
            || document.documentElement.clientWidth
            || document.body.clientWidth;

    function whichMenu() {
        var topNav = document.getElementById("topNav");
        if (width < 800) {
            topNav.style.display = "none";
        } else {
            topNav.style.display = "block";
        }
        var sideNav = document.getElementById("main");
        if (width < 800) {
            sideNav.style.display = "block";
        } else {
            sideNav.style.display = "none";
        }
    }

    function Vpwd() {
        var Vpwd1 = document.getElementById("Vpwd1");
        var Vpwd2 = document.getElementById("Vpwd2");
        var VpwdMatch = document.getElementById("VpwdMatch");
        if (Vpwd1.value === Vpwd2.value) {
            VpwdMatch.style.display = "none";
        } else {
            VpwdMatch.style.display = "block";
        }
    }

    function updateMS() {
        var vS = Number(document.getElementById("MStotalSAICN").value);
        var vC = Number(document.getElementById("MStotalCNSAI").value);
        var saicn = vS * <?php
        echo $SAICNtax;
        ?>;
		var cnsai = vC * <?php
echo $CNSAItax;
?>;
		var tot = saicn + cnsai;
		var msdue = document.getElementById("MSdue");
		var msfeedue = document.getElementById("MSfeedue");
<?php
if ($FMFee >= .001) {
    ?>
	var feeDue = (vS + vC) * <?php
    echo $FMFee;
    ?>;
	var totalDue = tot + feeDue;
            msdue.innerHTML = totalDue.toFixed(2);
            msfeedue.innerHTML = feeDue.toFixed(2);
    <?php
} elseif ($boothFee >= .01) {
    ?>
	var totalDue = tot + <?php
    echo $boothFee;
    ?>;
            msdue.innerHTML = totalDue.toFixed(2);
            msfeedue.innerHTML = <?php

    echo $boothFee;
    ?>.toFixed(2);
    <?php
} else {
    ?>
            msdue.innerHTML = tot.toFixed(2);
            msfeedue.innerHTML = 0.00;
    <?php
}
?>
    }

    function moneyrcvd() {
        var d = document.getElementById("invoiceSubmit");
        d.disabled = (d.disabled === true) ? false : true;
    }

    function taxFeeToggle() {
        var itm = document.getElementById("taxAndFee");
        var itmx = document.getElementById("feeOnly");
        if (itmx.style.display === "none") {
            itmx.style.display = "block";
        } else {
            itmx.style.display = "none";
        }
        if (itm.style.display === "none") {
            itm.style.display = "block";
        } else {
            itm.style.display = "none";
        }
    }
</script>
<!-- End of Head -->