<?php 
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
require_once  dirname(__FILE__) . "/menu.php" ?>

<div id="app-content">

	<div class="buttons-container">
		<a type="button" class="btn btn-primary" href="/index.php/apps/deadmanswitch/jobs/create">
			Add job
		</a>
	</div>

	<table id="jobsTable" class="display">
		<thead>
		<tr>
			<th>Name</th>
			<th>Email subject</th>
		</tr>
		</thead>
		<tbody>

		</tbody>
	</table>

</div>


