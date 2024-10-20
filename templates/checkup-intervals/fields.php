<?php
/**
 * @var \OCA\DeadManSwitch\Db\CheckupInterval $checkupInterval
 */
?>
	<div class="form-group">
		<label>Name</label>
		<input type="text" name="name" class="form-control" value="<?= $checkupInterval->getName() ?>">
		<small class="form-text text-muted error">
			<?= !empty($errors['name']) ? $errors['name'] : '' ?>
		</small>
	</div>
	<div class="form-group">
		<label>Interval</label>
		<input type="number" name="interval" class="form-control" value="<?= $checkupInterval->getInterval() ?>">
		<small class="form-text text-muted error">
			<?= !empty($errors['interval']) ? $errors['interval'] : '' ?>
		</small>
	</div>


	<button type="submit" class="btn btn-primary">Submit</button>

