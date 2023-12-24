<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
use OCP\Util;
$appId = OCA\DeadManSwitch\AppInfo\Application::APP_ID;
Util::addScript($appId, $appId . '-mainScript');
Util::addStyle($appId, 'main');
?>

<div id="app-content">
<?php
if ($_['app_version']) {
    // you can get the values you injected as template parameters in the "$_" array
    echo '<h3>Dead Man Switch app version: ' . $_['app_version'] . '</h3>';
}
?>
    <div id="catgifs"></div>
</div>