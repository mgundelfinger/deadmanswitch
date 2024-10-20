<?php
/**
 * @var \OCA\DeadManSwitch\Db\Trigger $trigger
 */
?>
	<div class="form-group">
		<label>Name</label>
		<input type="text" name="name" class="form-control" value="<?= $trigger->getName() ?>">
		<small class="form-text text-muted error">
			<?= !empty($errors['name']) ? $errors['name'] : '' ?>
		</small>
	</div>
	<div class="form-group">
		<label>Delay</label>
		<input type="number" name="delay" class="form-control" value="<?= $trigger->getDelay() ?>">
		<small class="form-text text-muted error">
			<?= !empty($errors['delay']) ? $errors['delay'] : '' ?>
		</small>
	</div>

	<button type="submit" class="btn btn-primary">Submit</button>

