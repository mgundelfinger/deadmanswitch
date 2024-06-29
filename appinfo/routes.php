<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
return [
	'routes' => [
		// this tells Nextcloud to link GET requests to /index.php/apps/deadmanswitch/ with the "mainPage" method of the PageController class
		['name' => 'page#mainPage', 'url' => '/', 'verb' => 'GET'],
		// this tells Nextcloud to link PUT requests to /index.php/apps/deadmanswitch/config with the "saveConfig" method of the PageController class
		['name' => 'page#saveConfig', 'url' => '/config/{interval}/{active}', 'verb' => 'PUT'],
		// this tells Nextcloud to link GET requests to /index.php/apps/deadmanswitch/checkin with the "checkInPage" method of the PageController class
		['name' => 'page#checkInPage', 'url' => '/checkin', 'verb' => 'GET'],
	],
];