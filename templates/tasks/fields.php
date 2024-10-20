
<?php
/**
 * @var \OCA\DeadManSwitch\Db\Task $task
 * @var array $currentGroups
 * @var array $groupsList
 * @var array $contactGroups
 * @var array $jobGroups
 * @var array $confirmatorGroups
 * @var array $triggers
 * @var \OCA\DeadManSwitch\Db\Task $task
 */

?>

<style>
	input[type="checkbox"] {
		cursor: pointer;
	}
	input[type="checkbox"]:checked {
		background-color: #0a53be;
	}
</style>

<div class="form-group">
	<label>Name</label>
	<input type="text" name="name" class="form-control" value="<?= $task->getName() ?>">
	<small class="form-text text-muted error">
		<?= !empty($errors['name']) ? $errors['name'] : '' ?>
	</small>
</div>

<div class="form-group">
	<label>Active</label>
	<input type="hidden" name="active" class="form-control" value="0">
	<input type="checkbox" name="active" class="form-control" value="1" <?= $task->getActive() ? 'checked' : '' ?>>
	<small class="form-text text-muted error">
		<?= !empty($errors['name']) ? $errors['name'] : '' ?>
	</small>
</div>

<div class="form-group">
	<label>Contact group</label>
	<select class="form-control" name="contactsGroupId">
		<?php foreach($contactGroups as $id => $label): ?>
		<option value="<?= $id ?>" <?= ($id == $task->getContactsGroupId()) ? 'selected' : '' ?>>
			<?= $label ?>
		</option>
		<?php endforeach; ?>
	</select>
	<small class="form-text text-muted error">
		<?= !empty($errors['name']) ? $errors['name'] : '' ?>
	</small>
</div>

<div class="form-group">
	<label>Job group</label>
	<select class="form-control" name="jobsGroupId">
		<?php foreach($jobGroups as $id => $label): ?>
			<option value="<?= $id ?>" <?= ($id == $task->getJobsGroupId()) ? 'selected' : '' ?>>
				<?= $label ?>
			</option>
		<?php endforeach; ?>
	</select>
	<small class="form-text text-muted error">
		<?= !empty($errors['name']) ? $errors['name'] : '' ?>
	</small>
</div>

<div class="form-group">
	<label>Confirmator group</label>
	<select class="form-control" name="confirmatorsGroupId">
		<?php foreach($confirmatorGroups as $id => $label): ?>
			<option value="<?= $id ?>" <?= ($id == $task->getConfirmatorsGroupId()) ? 'selected' : '' ?>>
				<?= $label ?>
			</option>
		<?php endforeach; ?>
	</select>
	<small class="form-text text-muted error">
		<?= !empty($errors['name']) ? $errors['name'] : '' ?>
	</small>
</div>

<div class="form-group">
	<label>Trigger</label>
	<select class="form-control" name="triggerId">
		<?php foreach($triggers as $id => $label): ?>
			<option value="<?= $id ?>" <?= ($id == $task->getTriggerId()) ? 'selected' : '' ?>>
				<?= $label ?>
			</option>
		<?php endforeach; ?>
	</select>
	<small class="form-text text-muted error">
		<?= !empty($errors['name']) ? $errors['name'] : '' ?>
	</small>
</div>


<button type="submit" class="btn btn-primary">Submit</button>

