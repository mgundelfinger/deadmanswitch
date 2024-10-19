
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

	<button type="submit" class="btn btn-primary">Submit</button>

