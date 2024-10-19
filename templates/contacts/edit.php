<?php require_once  dirname(__FILE__) . "/../menu.php" ?>

<?php
/**
 * @var \OCA\DeadManSwitch\Db\Job $job
 */
?>


<div id="app-content">
	<div class="container custom-container">
		<form method="post" action="/index.php/apps/deadmanswitch/contacts/update">
			<input type="hidden" name="id" value="<?= $contact->getId() ?>"/>

			<?php require_once  dirname(__FILE__) . "/fields.php" ?>
		</form>
	</div>
</div>
