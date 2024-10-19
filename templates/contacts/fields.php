<?php
/**
 * @var \OCA\DeadManSwitch\Db\Contact $contact
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

<button type="submit" class="btn btn-primary">Submit</button>

