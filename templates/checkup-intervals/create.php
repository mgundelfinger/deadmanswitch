<?php require_once  dirname(__FILE__) . "/../menu.php" ?>

<?php
/**
 * @var \OCA\DeadManSwitch\Db\CheckupInterval $checkupInterval
 */
?>

<div id="app-content">
	<div class="container custom-container">
		<form method="post" action="/index.php/apps/deadmanswitch/checkup-intervals/store">
			<?php require_once  dirname(__FILE__) . "/fields.php" ?>
		</form>
	</div>
</div>
