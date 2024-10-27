<?php
// SPDX-FileCopyrightText: Mikael Nazarenko <miknazarenko@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
?>

<?php
/**
 * @var \OCA\DeadManSwitch\Db\Task $task
 * @var array $currentGroups
 * @var array $groupsList
 * @var array $contactGroups
 * @var array $jobGroups
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
	<label><?php p($l->t('Name')) ?></label>
	<input type="text" name="name" class="form-control" value="<?= $task->getName() ?>">
	<small class="form-text text-muted error">
		<?= !empty($errors['name']) ? $errors['name'] : '' ?>
	</small>
</div>

<div class="form-group">
	<label><?php p($l->t('Active')) ?></label>
	<input type="hidden" name="active" class="form-control" value="0">
	<input type="checkbox" name="active" class="form-control" value="1" <?= $task->getActive() ? 'checked' : '' ?>>
	<small class="form-text text-muted error">
		<?= !empty($errors['name']) ? $errors['name'] : '' ?>
	</small>
</div>

<div class="form-group">
	<label><?php p($l->t('Contact Group')) ?></label>
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
	<label><?php p($l->t('Job Group')) ?></label>
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
<label><?php p($l->t('Days after death')) ?></label>
	<input type="number" name="deathDays" class="form-control" value="<?= $task->getDeathDays() ?>">
	<small class="form-text text-muted error">
		<?= !empty($errors['name']) ? $errors['name'] : '' ?>
	</small>
</div>


<button type="submit" class="btn btn-primary">Submit</button>

