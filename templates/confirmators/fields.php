
<?php
/**
 * @var \OCA\DeadManSwitch\Db\Confirmator $confirmator
 * @var array $currentGroups
 * @var array $groupsList
 * @var array $contactList
 * @var array $checkupIntervalList
 * @var \OCA\DeadManSwitch\Db\Contact $currentContact
 * @var \OCA\DeadManSwitch\Db\CheckupInterval $currentInterval
 */
?>

<div class="form-group">
	<label>Contact</label>
	<select name="contactId" class="form-control">
		<?php foreach($contactList as $id => $label): ?>
		<option value="<?= $id ?>" <?= ($currentContact->getId() == $id) ? 'selected' : '' ?>>
			<?= $label ?>
		</option>
		<?php endforeach; ?>
	</select>

	<small class="form-text text-muted error">
		<?= !empty($errors['name']) ? $errors['name'] : '' ?>
	</small>
</div>

<div class="form-group">
	<label>Checkup interval</label>
	<select name="intervalId" class="form-control">
		<?php foreach($checkupIntervalList as $id => $label): ?>
			<option value="<?= $id ?>" <?= ($currentInterval->getId() == $id) ? 'selected' : '' ?>>
				<?= $label ?>
			</option>
		<?php endforeach; ?>
	</select>

	<small class="form-text text-muted error">
		<?= !empty($errors['name']) ? $errors['name'] : '' ?>
	</small>
</div>

<div class="form-group">
	<label>Confirmator groups</label>

	<select name="contactGroups[]" multiple class="form-control" style="min-height: 150px">
		<?php foreach($groupsList as $id => $name): ?>
			<option value="<?= $id ?>" <?= in_array($id, $currentGroups) ? 'selected' : '' ?>><?= $name ?></option>
		<?php endforeach; ?>
	</select>

	<small class="form-text text-muted error"></small>
</div>

<button type="submit" class="btn btn-primary">Submit</button>

