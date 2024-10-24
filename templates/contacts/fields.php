<?php
// SPDX-FileCopyrightText: Mikael Nazarenko <miknazarenko@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
?>

<?php
/**
 * @var \OCA\DeadManSwitch\Db\Contact $contact
 * @var array $currentGroups
 * @var array $groupsList
 */
?>

<div class="form-group">
	<label>First Name</label>
	<input type="text" name="firstName" class="form-control" value="<?= $contact->getFirstName() ?>">
	<small class="form-text text-muted error">
		<?= !empty($errors['firstName']) ? $errors['firstName'] : '' ?>
	</small>
</div>
<div class="form-group">
	<label>Last Name</label>
	<input name="lastName" class="form-control" value="<?= $contact->getLastName() ?>">
	<small class="form-text text-muted error">
		<?= !empty($errors['lastName']) ? $errors['lastName'] : '' ?>
	</small>
</div>
<div class="form-group">
	<label>Email</label>
	<input name="email" class="form-control" value="<?= $contact->getEmail() ?>">
	<small class="form-text text-muted error">
		<?= !empty($errors['email']) ? $errors['email'] : '' ?>
	</small>
</div>

<div class="form-group">
	<label>Contact groups</label>

	<select name="contactGroups[]" multiple class="form-control" style="min-height: 150px">
		<?php foreach($groupsList as $id => $name): ?>
			<option value="<?= $id ?>" <?= in_array($id, $currentGroups) ? 'selected' : '' ?>><?= $name ?></option>
		<?php endforeach; ?>
	</select>

	<small class="form-text text-muted error"></small>
</div>

<button type="submit" class="btn btn-primary">Submit</button>

