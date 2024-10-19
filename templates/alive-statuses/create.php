<?php require_once  dirname(__FILE__) . "/../menu.php" ?>

<?php
/**
 * @var \OCA\DeadManSwitch\Db\AliveStatus $aliveStatus
 */
?>

<div id="app-content">
	<div class="container custom-container">
		<form method="post" action="/index.php/apps/deadmanswitch/alive-statuses/store">
			<?php require_once  dirname(__FILE__) . "/fields.php" ?>
		</form>
	</div>
</div>
