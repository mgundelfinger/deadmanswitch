<?php
// SPDX-FileCopyrightText: Mikael Nazarenko <miknazarenko@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
?>

<?php
/**
 * @var \OCA\DeadManSwitch\Db\Job $job
 * @var array $currentGroups
 * @var array $groupsList
 */
?>

<div class="form-group">
	<label>Name</label>
	<input type="text" name="name" class="form-control" value="<?= $job->getName() ?>">
	<small class="form-text text-muted error">
		<?= !empty($errors['name']) ? $errors['name'] : '' ?>
	</small>
</div>
<div class="form-group">
	<label>Subject</label>
	<input name="emailSubject" class="form-control" value="<?= $job->getEmailSubject() ?>">
	<small class="form-text text-muted error">
		<?= !empty($errors['emailSubject']) ? $errors['emailSubject'] : '' ?>
	</small>
</div>
<div class="form-group">
	<label>Body</label>
	<input name="emailBody" class="form-control" value="<?= $job->getEmailBody() ?>">
	<small class="form-text text-muted error">
		<?= !empty($errors['emailBody']) ? $errors['emailBody'] : '' ?>
	</small>
</div>

<div class="form-group">
	<label>Job groups</label>

	<select name="jobGroups[]" multiple class="form-control" style="min-height: 150px">
		<?php foreach($groupsList as $id => $name): ?>
			<option value="<?= $id ?>" <?= in_array($id, $currentGroups) ? 'selected' : '' ?>><?= $name ?></option>
		<?php endforeach; ?>
	</select>

	<small class="form-text text-muted error"></small>
</div>

<button type="submit" class="btn btn-primary">Submit</button>

