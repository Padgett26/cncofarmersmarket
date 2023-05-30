<div id="topNav" style="display: none;">
    <a href="index.php?page=News">News</a>
    <a href="index.php?page=Vendors">Vendors</a>
    <?php
    if ($useStore == 1) {
        echo '<a href="index.php?page=Store">Store</a>';
    }
    if ($custId >= 1) {
        echo '<a href="index.php?page=Invoice">Purchase History</a>';
    }
    ?>
    <a href="index.php?page=Calendar">Calendar</a>
    <a href="index.php?page=Pics">Pictures</a>
    <a href="index.php?page=Legal">Legal & Minutes</a>
    <a href="index.php?page=Feedback">Feedback</a>
    <a href="index.php?page=Board">About the Board</a>
    <a href="index.php?page=Links">Links</a>
</div>
<div id="mySidenav" class="sidenav">
    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
    <a href="index.php?page=News">News</a>
    <a href="index.php?page=Vendors">Vendors</a>
    <?php
    if ($useStore == 1) {
        echo '<a href="index.php?page=Store">Store</a>';
    }
    if ($custId >= 1) {
        echo '<a href="index.php?page=Invoice">Purchase History</a>';
    }
    ?>
    <a href="index.php?page=Calendar">Calendar</a>
    <a href="index.php?page=Pics">Pictures</a>
    <a href="index.php?page=Legal">Legal & Minutes</a>
    <a href="index.php?page=Feedback">Feedback</a>
    <a href="index.php?page=Board">About the Board</a>
    <a href="index.php?page=Links">Links</a>
</div>

<div id="main">
    <span style="font-size:30px;cursor:pointer" onclick="openNav()">&#9776; open</span>
</div>

<script>
    function openNav() {
        document.getElementById("mySidenav").style.width = "250px";
        document.getElementById("main").style.marginLeft = "250px";
        document.body.style.backgroundColor = "rgba(0,0,0,0.4)";
    }

    function closeNav() {
        document.getElementById("mySidenav").style.width = "0";
        document.getElementById("main").style.marginLeft = "0";
        document.body.style.backgroundColor = "white";
    }
</script>