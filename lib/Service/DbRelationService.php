<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\DeadManSwitch\Service;

use OCA\DeadManSwitch\Db\Task;
use OCA\DeadManSwitch\Db\Trigger;
use OCA\DeadManSwitch\Db\TriggerMapper;

class DbRelationService {

    /**
     * @var TriggerMapper
     */
    private $triggerMapper;

    public function __construct(
        TriggerMapper $triggerMapper,
        ) {
        $this->triggerMapper = $triggerMapper;
    }

    public function getTaskTrigger(Task $task): Trigger {
        return $this->triggerMapper->getTrigger($task->getTriggerId());
    }

}