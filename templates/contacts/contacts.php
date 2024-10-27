<?php
// SPDX-FileCopyrightText: Mikael Nazarenko <miknazarenko@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
?>

<?php require_once  dirname(__FILE__) . "/../menu.php" ?>

<div id="app-content">

	<div class="buttons-container">
		<a type="button" class="btn btn-primary" href="/index.php/apps/deadmanswitch/contacts/create">
			<?php p($l->t('Add Contact')) ?>
		</a>
	</div>

	<table id="contacts-table" class="display">

		<thead>
		<tr>
			<th><?php p($l->t('First Name')) ?></th>
			<th><?php p($l->t('Last Name')) ?></th>
			<th><?php p($l->t('Email')) ?></th>
			<th><?php p($l->t('Actions')) ?></th>
		</tr>
		</thead>
		<tbody>

		</tbody>
	</table>

</div>


