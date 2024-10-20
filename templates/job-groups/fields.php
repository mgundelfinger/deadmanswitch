<?php
/**
 * @var \OCA\DeadManSwitch\Db\JobsGroup $jobsGroup
 */
?>

	<div class="form-group">
		<label>Name</label>
		<input type="text" name="name" class="form-control" value="<?= $jobsGroup->getName() ?>">
		<small class="form-text text-muted error">
			<?= !empty($errors['name']) ? $errors['name'] : '' ?>
		</small>
	</div>

	<button type="submit" class="btn btn-primary">Submit</button>

