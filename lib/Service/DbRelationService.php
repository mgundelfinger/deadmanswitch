<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\DeadManSwitch\Service;

use OCA\DeadManSwitch\Db\CheckupInterval;
use OCA\DeadManSwitch\Db\CheckupIntervalMapper;
use OCA\DeadManSwitch\Db\Task;
use OCA\DeadManSwitch\Db\Trigger;
use OCA\DeadManSwitch\Db\TriggerMapper;

class DbRelationService {

    /**
     * @var TriggerMapper
     */
    private $triggerMapper;

    /**
     * @var CheckupIntervalMapper
     */
    private $intervalMapper;

    public function __construct(
        TriggerMapper $triggerMapper,
        CheckupIntervalMapper $intervalMapper,
        ) {
        $this->triggerMapper = $triggerMapper;
        $this->intervalMapper = $intervalMapper;
    }

    public function getTaskTrigger(Task $task): Trigger {
        return $this->triggerMapper->getTrigger($task->getTriggerId());
    }

    public function getTaskInterval(Task $task): CheckupInterval {
        return $this->intervalMapper->getCheckupInterval($task->getIntervalId());
    }
}