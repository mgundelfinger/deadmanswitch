<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
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
			<a href="/index.php/apps/deadmanswitch/" class="nav-link active" aria-current="page">
				<svg class="bi me-2" width="16" height="16"><use xlink:href="#home"></use></svg>
				Home
			</a>
		</li>
		<li>
			<a href="/index.php/apps/deadmanswitch/contacts" class="nav-link text-white">
				<svg class="bi me-2" width="16" height="16"><use xlink:href="#speedometer2"></use></svg>
				Contacts
			</a>
		</li>
		<li>
			<a href="/index.php/apps/deadmanswitch/tasks" class="nav-link text-white">
				<svg class="bi me-2" width="16" height="16"><use xlink:href="#table"></use></svg>
				Tasks
			</a>
		</li>
		<li>
			<a href="/index.php/apps/deadmanswitch/jobs" class="nav-link text-white">
				<svg class="bi me-2" width="16" height="16"><use xlink:href="#grid"></use></svg>
				Jobs
			</a>
		</li>
	</ul>
<!--	<hr>-->
<!--	<div class="dropdown">-->
<!--		<a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">-->
<!--			<img src="https://github.com/mdo.png" alt="" width="32" height="32" class="rounded-circle me-2">-->
<!--			<strong>mdo</strong>-->
<!--		</a>-->
<!--		<ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1" style="">-->
<!--			<li><a class="dropdown-item" href="#">New project...</a></li>-->
<!--			<li><a class="dropdown-item" href="#">Settings</a></li>-->
<!--			<li><a class="dropdown-item" href="#">Profile</a></li>-->
<!--			<li><hr class="dropdown-divider"></li>-->
<!--			<li><a class="dropdown-item" href="#">Sign out</a></li>-->
<!--		</ul>-->
<!--	</div>-->
</div>




<div id="app-content">


    <form>

    <div>
        <input id="onOffSwitch" type="checkbox">Dead Man Switch</input>
    </div>
    <div id="config">
        <select id="intervalSelector" disabled>
            <!-- TODO prototype only /-->
            <option value=0>Instant</option>

            <option value=1>Daily</option>
            <option value=7>Weekly</option>
            <option value=28>Every Four Weeks</option>
        </select>
    </div>
    <div>
        <button id="saveButton" disabled>Save</button>
        <button id="cancelButton" disabled>Cancel</button>
    </div>
    </form>
</div>
