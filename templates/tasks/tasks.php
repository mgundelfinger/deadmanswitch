<?php
// SPDX-FileCopyrightText: Mikael Nazarenko <miknazarenko@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
?>

<?php require_once  dirname(__FILE__) . "/../menu.php" ?>

<div id="app-content">

	<div class="buttons-container">
		<a type="button" class="btn btn-primary" href="/index.php/apps/deadmanswitch/tasks/create">
			<th><?php p($l->t('Add Task')) ?></th>
		</a>
	</div>

	<table id="tasks-table" class="display">
		<thead>
		<tr>
			<th><?php p($l->t('Name')) ?></th>
			<th><?php p($l->t('Active')) ?></th>
			<th><?php p($l->t('Contact Group')) ?> </th>
			<th><?php p($l->t('Job Group')) ?></th>
			<th><?php p($l->t('Days until execution after death (days)')) ?></th>
			<th><?php p($l->t('Actions')) ?></th>
		</tr>
		</thead>
		<tbody>

		</tbody>
	</table>

</div>


