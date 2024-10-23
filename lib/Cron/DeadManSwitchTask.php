<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\DeadManSwitch\Cron;

use DateTime;
use DateTimeImmutable;
use OCA\DeadManSwitch\Db\AliveStatus;
use OCA\DeadManSwitch\Db\AliveStatusMapper;
use OCA\DeadManSwitch\Db\Contact;
use OCA\DeadManSwitch\Db\ConfirmatorsMapper;
use OCA\DeadManSwitch\Db\ContactMapper;
use OCA\DeadManSwitch\Db\JobMapper;
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

    private ContactMapper $contactMapper;

    private JobMapper $jobMapper;

    private ConfirmatorsMapper $confirmatorsMapper;

    private AliveStatusMapper $aliveStatusMapper;

    public function __construct(ITimeFactory $time, MailService $mailService, DbRelationService $relationService, TaskMapper $taskMapper, ContactMapper $contactMapper, JobMapper $jobMapper, ConfirmatorsMapper $confirmatorsMapper, AliveStatusMapper $aliveStatusMapper) {
        parent::__construct($time);
        $this->mailService = $mailService;
        $this->relationService = $relationService;
        $this->taskMapper = $taskMapper;
        $this->contactMapper = $contactMapper;
        $this->jobMapper= $jobMapper;
        $this->confirmatorsMapper = $confirmatorsMapper;
        $this->aliveStatusMapper = $aliveStatusMapper;

        // TODO run daily
        $this->setInterval(300);
    }

    protected function run($arguments = []) {
        $users = $this->taskMapper->getUserIdsWithActiveTasks();
        foreach ($users as $user) {
            $userId = $user['user_id'];
            $aliveStatus = $this->aliveStatusMapper->getOrCreateAliveStatusOfUser($userId);
            if ($aliveStatus->getStatus() == AliveStatusMapper::STATUS_ALIVE) 
            {
                $this->runCheckups($userId, $aliveStatus);
            } else if ($aliveStatus->getStatus() == AliveStatusMapper::STATUS_PENDING) {
                $this->runDeathStatus($userId, $aliveStatus);
            } else {
                $this->runJobs($userId, $aliveStatus);
            }
        }
    }

    /**
     * @param string $userId
     * @param AliveStatus $aliveStatus
     */
    private function runCheckups(string $userId, AliveStatus $aliveStatus) {
        $today = new DateTime();
        $diff = $aliveStatus->getLastCheckupAsDate()->diff($today)->format("%r%a");
        $aliveDays = $aliveStatus->getInterval(); //TODO rename interval to aliveDays

        if ($diff >= $aliveDays) {
            $confirmatorContacts = $this->contactMapper->getContactsOfGroup($this->confirmatorsMapper->getConfirmatorsOfUser($userId)->getContactsGroupId());
            foreach ($confirmatorContacts as $contact) {
                $this->mailService->sendCheckUpEmail($contact, $userId);
            }
            $this->aliveStatusMapper->updateAliveStatus($aliveStatus->getId(), AliveStatusMapper::STATUS_PENDING);
        }
    }

    /**
     * @param string $userId
     * @param AliveStatus $aliveStatus
     */
    private function runDeathStatus(string $userId, AliveStatus $aliveStatus) {
        $today = new DateTime();
        $diff = $aliveStatus->getLastCheckupAsDate()->diff($today)->format("%r%a"); // TODO rename lastCheckup to lastChange
        $pendingDays = 1; // $aliveStatus->getPendingDays(); TODO add field pendingDays to AliveStatus

        if($diff >= $pendingDays) {
            $this->aliveStatusMapper->updateAliveStatus($aliveStatus->getId(), AliveStatusMapper::STATUS_DEAD);
        }


    }

    /**
     * @param string $userId
     * @param AliveStatus $aliveStatus
     */
    private function runJobs(string $userId, AliveStatus $aliveStatus) {
        $tasks = $this->taskMapper->getActiveTasksOfUser($userId);
        $today = new DateTime();
        $diff = $aliveStatus->getLastCheckupAsDate()->diff($today)->format("%r%a");

        foreach ($tasks as $task) {
            $delay = $this->relationService->getTaskTrigger($task)->getDelay(); // TODO remove Trigger entity, and make it a field instead
            $contacts = $this->contactMapper->getContactsOfGroup($task->getContactsGroupId());

            if($diff >= $delay) {
                foreach($this->jobMapper->getJobsOfGroup($task->getJobsGroupId()) as $job) {
                    foreach($contacts as $contact) {
                        $this->mailService->sendEmail($contact->getEmail(), $job->getEmailSubject(), $job->getEmailBody());
                    }  
                }
            }
        }
    }
}