<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

/**
 * @var \OCA\DeadManSwitch\Db\AliveStatus $aliveStatus
 * @var \OCA\DeadManSwitch\Db\UserSettings $userSettings
 * @var array $contactGroups
 */
?>

<div id="app-content">
	<div class="container custom-container">
        <?php if ($name != null) {
            echo "You have confirmed that $name is still alive.";
        } else {
            echo "Your token is invalid.";
        }
        ?>
	</div>
</div>
