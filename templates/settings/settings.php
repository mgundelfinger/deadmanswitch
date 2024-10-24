<?php
// SPDX-FileCopyrightText: Mikael Nazarenko <miknazarenko@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

/**
 * @var \OCA\DeadManSwitch\Db\AliveStatus $aliveStatus
 * @var array $contactGroups
 */
?>

<?php require_once  dirname(__FILE__) . "/../menu.php" ?>

<div id="app-content">
	<div class="container custom-container">
		<form method="post" action="/index.php/apps/deadmanswitch/settings/update">

			<div class="form-group">
				<label>Alive Days</label>
				<input type="number" name="aliveDays" class="form-control" value="<?= $aliveStatus->getAliveDays() ?>">
				<small class="form-text text-muted error">
					<?= !empty($errors['aliveDays']) ? $errors['aliveDays'] : '' ?>
				</small>
			</div>

			<div class="form-group">
				<label>Pending Days</label>
				<input type="number" name="pendingDays" class="form-control" value="<?= $aliveStatus->getPendingDays() ?>">
				<small class="form-text text-muted error">
					<?= !empty($errors['pendingDays']) ? $errors['pendingDays'] : '' ?>
				</small>
			</div>

			<div class="form-group">
				<label>Contact group</label>
				<select class="form-control" name="contactGroup">
					<?php foreach($contactGroups as $value => $label): ?>
					<option value="<?= $value ?>" <?= ($aliveStatus->getContactsGroupId() == $value) ? 'selected' : '' ?>>
						<?= $label ?>
					</option>
					<?php endforeach; ?>
				</select>
				<small class="form-text text-muted error">
					<?= !empty($errors['contactGroup']) ? $errors['contactGroup'] : '' ?>
				</small>

			</div>

			<button type="submit" class="btn btn-primary">Submit</button>

		</form>
	</div>
</div>
