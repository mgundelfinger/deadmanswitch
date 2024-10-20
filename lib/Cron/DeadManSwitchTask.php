<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\DeadManSwitch\Cron;

use OCA\DeadManSwitch\Db\TaskMapper;
use OCA\DeadManSwitch\Service\MailService;
use OCP\BackgroundJob\TimedJob;
use OCP\AppFramework\Utility\ITimeFactory;

class DeadManSwitchTask extends TimedJob {

    private MailService $mailService;

    private TaskMapper $taskMapper;

    // TODO test only
    public const INTERVAL_TEST = 0;


    public function __construct(ITimeFactory $time, MailService $mailService, TaskMapper $taskMapper) {
        parent::__construct($time);
        $this->mailService = $mailService;
        $this->taskMapper = $taskMapper;

        // TODO run daily
        $this->setInterval(300);
    }

    protected function run($arguments) {
        $activeTasks = $this->taskMapper->getActiveTasks();
        return 1;
    }

}