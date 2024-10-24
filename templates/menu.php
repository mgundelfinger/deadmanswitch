<?php
// SPDX-FileCopyrightText: Mikael Nazarenko <miknazarenko@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

use OCP\Util;
$appId = OCA\DeadManSwitch\AppInfo\Application::APP_ID;
Util::addScript($appId, $appId . '-mainScript');
Util::addStyle($appId, 'main');

$version = !empty($_['app_version']) ? 'v. ' . $_['app_version'] : '';

?>

<div class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark" style="width: 280px;">
	<a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
		<svg class="bi me-2" width="40" height="32"><use xlink:href="#bootstrap"></use></svg>
		<span class="fs-4">DMS <?= $version ?></span>
	</a>
	<hr>
	<ul class="nav nav-pills flex-column mb-auto">
		<li class="nav-item">
			<a href="/index.php/apps/deadmanswitch/" class="nav-link text-white <?= empty($page) ? 'active' : '' ?>" aria-current="page">
				<svg class="bi me-2" width="16" height="16"><use xlink:href="#home"></use></svg>
				Home
			</a>
		</li>
		<li>
			<a href="/index.php/apps/deadmanswitch/contacts" class="nav-link text-white <?= $page == 'contacts' ? 'active' : '' ?>">
				<svg class="bi me-2" width="16" height="16"><use xlink:href="#speedometer2"></use></svg>
				Contacts
			</a>
		</li>

		<li>
			<a href="/index.php/apps/deadmanswitch/contact-groups" class="nav-link text-white <?= $page == 'contact-groups' ? 'active' : '' ?>">
				<svg class="bi me-2" width="16" height="16"><use xlink:href="#grid"></use></svg>
				Contact groups
			</a>
		</li>

		<li>
			<a href="/index.php/apps/deadmanswitch/tasks" class="nav-link text-white <?= $page == 'tasks' ? 'active' : '' ?>">
				<svg class="bi me-2" width="16" height="16"><use xlink:href="#table"></use></svg>
				Tasks
			</a>
		</li>

		<li>
			<a href="/index.php/apps/deadmanswitch/jobs" class="nav-link text-white <?= $page == 'jobs' ? 'active' : '' ?>">
				<svg class="bi me-2" width="16" height="16"><use xlink:href="#grid"></use></svg>
				Jobs
			</a>
		</li>

		<li>
			<a href="/index.php/apps/deadmanswitch/job-groups" class="nav-link text-white <?= $page == 'job-groups' ? 'active' : '' ?>">
				<svg class="bi me-2" width="16" height="16"><use xlink:href="#grid"></use></svg>
				Job groups
			</a>
		</li>

		<li>
			<a href="/index.php/apps/deadmanswitch/settings" class="nav-link text-white <?= $page == 'settings' ? 'active' : '' ?>">
				<svg class="bi me-2" width="16" height="16"><use xlink:href="#grid"></use></svg>
				Settings
			</a>
		</li>

	</ul>

</div>
