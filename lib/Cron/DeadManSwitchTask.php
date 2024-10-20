<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\DeadManSwitch\Cron;

use OCA\DeadManSwitch\Db\Task;
use OCA\DeadManSwitch\Db\TaskMapper;
use OCA\DeadManSwitch\Service\DbRelationService;
use OCA\DeadManSwitch\Service\MailService;
use OCP\BackgroundJob\TimedJob;
use OCP\AppFramework\Utility\ITimeFactory;

class DeadManSwitchTask extends TimedJob {

    private MailService $mailService;

    private DbRelationService $relationService;

    private TaskMapper $taskMapper;

    // TODO test only
    public const INTERVAL_TEST = 0;


    public function __construct(ITimeFactory $time, MailService $mailService, DbRelationService $relationService, TaskMapper $taskMapper) {
        parent::__construct($time);
        $this->mailService = $mailService;
        $this->relationService = $relationService;
        $this->taskMapper = $taskMapper;

        // TODO run daily
        $this->setInterval(300);
    }

    protected function run($arguments) {
        $activeTasks = $this->taskMapper->getActiveTasks();
        // TODO process Tasks
    }

    private function processTask(Task $task) {
        $trigger = $this->relationService->getTaskTrigger($task);
        $delay = $trigger->getDelay();
        // TODO process Task
    }

}