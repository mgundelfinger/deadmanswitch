<?php 
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
require_once  dirname(__FILE__) . "/menu.php" ?>



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
