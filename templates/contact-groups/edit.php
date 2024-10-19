<?php require_once  dirname(__FILE__) . "/../menu.php" ?>

<?php
/**
 * @var \OCA\DeadManSwitch\Db\Contact $contactsGroup
 */
?>


<div id="app-content">
	<div class="container custom-container">
		<form method="post" action="/index.php/apps/deadmanswitch/contact-groups/update">
			<input type="hidden" name="id" value="<?= $contactsGroup->getId() ?>"/>

			<?php require_once  dirname(__FILE__) . "/fields.php" ?>
		</form>
	</div>
</div>
