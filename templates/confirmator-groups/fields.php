<?php
/**
 * @var \OCA\DeadManSwitch\Db\ConfirmatorsGroup $confirmatorsGroup
 */
?>

	<div class="form-group">
		<label>Name</label>
		<input type="text" name="name" class="form-control" value="<?= $confirmatorsGroup->getName() ?>">
		<small class="form-text text-muted error">
			<?= !empty($errors['name']) ? $errors['name'] : '' ?>
		</small>
	</div>

	<button type="submit" class="btn btn-primary">Submit</button>

