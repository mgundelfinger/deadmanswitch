<?php
// SPDX-FileCopyrightText: Mikael Nazarenko <miknazarenko@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

/**
 * @var \OCA\DeadManSwitch\Db\AliveStatus $aliveStatus
 * @var \OCA\DeadManSwitch\Db\UserSettings $userSettings
 * @var array $contactGroups
 */
?>

<?php require_once  dirname(__FILE__) . "/../menu.php" ?>

<div id="app-content">
	<div class="container custom-container">
		<form method="post" action="/index.php/apps/deadmanswitch/settings/update">

			<div class="form-group">
				<label><?php p($l->t('Check-in interval (days)')) ?></label>
				<input type="number" name="aliveDays" class="form-control" value="<?= $aliveStatus->getAliveDays() ?>">
				<small class="form-text text-muted error">
					<?= !empty($errors['aliveDays']) ? $errors['aliveDays'] : '' ?>
				</small>
			</div>

			<div class="form-group">
				<label><?php p($l->t('Confirmation time (days)')) ?></label>
				<input type="number" name="pendingDays" class="form-control" value="<?= $aliveStatus->getPendingDays() ?>">
				<small class="form-text text-muted error">
					<?= !empty($errors['pendingDays']) ? $errors['pendingDays'] : '' ?>
				</small>
			</div>

			<div class="form-group">
				<label><?php p($l->t('Contact Group')) ?></label>
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

			<div class="form-group">
				<label><?php p($l->t('Text color')) ?></label>
				<select class="form-control" name="color">
					<option value="1" <?= ($userSettings->getColor() == 1) ? 'selected' : '' ?>>
						<?php p($l->t('Black')) ?>
					</option>
					<option value="2" <?= ($userSettings->getColor() == 2) ? 'selected' : '' ?>>
						<?php p($l->t('White')) ?>
					</option>
				</select>
			</div>

			<button type="submit" class="btn btn-primary"><?php p($l->t('Submit')) ?></button>

		</form>
	</div>
</div>
