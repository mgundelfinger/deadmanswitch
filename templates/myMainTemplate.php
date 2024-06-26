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