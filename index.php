<?php
include "cgi-bin/config.php";
include "cgi-bin/functions.php";
include "includes/formProcessing.php";
?>

<!DOCTYPE html>
<html manifest="includes/cache.appcache">
<head>
<?php
include "includes/head.php";
?>
</head>
<body onload="whichMenu()">

	<!-- Header -->
	<div class="header">
		<?php
include "includes/header.php";
?>
	</div>

	<!-- Navigation Bar -->
	<div class="navbar">
		<?php
include "includes/menu.php";
?>
	</div>

	<div class="row">
		<div class="side">
			<div class="calendar" style="margin: 20px 0px;">
				<div style="text-align: center; padding: 5px; font-size: 1.25em;">
					<a href='index.php?page=Home'>Home</a>
				</div>
			</div>
			<div class="calendar" style="margin: 20px 0px;">
				<div style="text-align: center; padding: 5px; font-size: 1.5em;">
					<a href='index.php?page=Calendar'>Upcoming Events</a>
				</div>
				<table>
					<?php
    upcomingEvents();
    ?>
				</table>
			</div>
			<div class="vendor" style="margin: 20px 0px;">
				<div
					style="text-align: center; padding: 5px; color: #cc4541; font-size: 1.25em; text-decoration: underline; margin-bottom: 15px;">Vendor Highlight</div>
				<?php
    vendorHighlight();
    ?>
			</div>
		</div>
		<div class="main">
			<?php
include "pages/$page.php";
?>
		</div>
	</div>

	<!-- Footer -->
	<div class="footer">
		<?php
include "includes/footer.php";
?>
	</div>
	<script src="includes/lightbox2/js/lightbox-plus-jquery.min.js"></script>

</body>
</html>