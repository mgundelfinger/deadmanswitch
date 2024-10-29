<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\DeadManSwitch\Cron;

use DateTime;
use OCA\DeadManSwitch\Db\AliveStatus;
use OCA\DeadManSwitch\Db\AliveStatusMapper;
use OCA\DeadManSwitch\Db\ContactMapper;
use OCA\DeadManSwitch\Db\JobMapper;
use OCA\DeadManSwitch\Db\ResetTokenMapper;
use OCA\DeadManSwitch\Db\TaskMapper;
use OCA\DeadManSwitch\Service\MailService;
use OCP\BackgroundJob\TimedJob;
use OCP\AppFramework\Utility\ITimeFactory;

class DeadManSwitchTask extends TimedJob {

    private MailService $mailService;

    private TaskMapper $taskMapper;

    private ContactMapper $contactMapper;

    private JobMapper $jobMapper;

    private AliveStatusMapper $aliveStatusMapper;

    private ResetTokenMapper $tokenMapper;

    public function __construct(ITimeFactory $time, MailService $mailService, TaskMapper $taskMapper, ContactMapper $contactMapper, JobMapper $jobMapper, AliveStatusMapper $aliveStatusMapper, ResetTokenMapper $tokenMapper) {
        parent::__construct($time);
        $this->mailService = $mailService;
        $this->taskMapper = $taskMapper;
        $this->contactMapper = $contactMapper;
        $this->jobMapper= $jobMapper;
        $this->aliveStatusMapper = $aliveStatusMapper;
        $this->tokenMapper = $tokenMapper;

        // run daily
        $this->setInterval(86400);
    }

    protected function run($arguments = []) {
        $today = new DateTime();
        $users = $this->taskMapper->getUserIdsWithActiveTasks();
        foreach ($users as $user) {
            $userId = $user['user_id'];
            $aliveStatus = $this->aliveStatusMapper->getAliveStatusOfUser($userId);
            $daysDifference = $aliveStatus->getLastChangeAsDate()->diff($today)->format("%r%a");
            if ($aliveStatus->getStatus() == AliveStatusMapper::STATUS_ALIVE) 
            {
                $this->runCheckIns($aliveStatus, $daysDifference);
            } else if ($aliveStatus->getStatus() == AliveStatusMapper::STATUS_PENDING) {
                $this->runDeathStatus($aliveStatus, $daysDifference);
            } else if ($aliveStatus->getStatus() == AliveStatusMapper::STATUS_DEAD) {
                $this->runJobs($aliveStatus, $daysDifference);
            }
        }
    }

    /**
     * @param AliveStatus $aliveStatus
     * @param int $daysDifference
     */
    private function runCheckIns(AliveStatus $aliveStatus, int $daysDifference) {
        $aliveDays = $aliveStatus->getAliveDays();

        if ($daysDifference >= $aliveDays) {
            $confirmatorContacts = $this->contactMapper->getContactsOfGroup($aliveStatus->getContactsGroupId());
            foreach ($confirmatorContacts as $contact) {
                $this->mailService->sendCheckInEmail($contact, $aliveStatus);
            }
            $this->aliveStatusMapper->updateAliveStatus($aliveStatus->getId(), AliveStatusMapper::STATUS_PENDING);
        }
    }

    /**
     * @param AliveStatus $aliveStatus
     * @param int $daysDifference
     */
    private function runDeathStatus(AliveStatus $aliveStatus, int $daysDifference) {
        $pendingDays = $aliveStatus->getPendingDays();

        if($daysDifference >= $pendingDays) {
            $this->aliveStatusMapper->updateAliveStatus($aliveStatus->getId(), AliveStatusMapper::STATUS_DEAD);
            $this->tokenMapper->deleteResetTokensOfAliveStatus($aliveStatus->getId());
        }


    }

    /**
     * @param AliveStatus $aliveStatus
     * @param int $daysDifference
     */
    private function runJobs(AliveStatus $aliveStatus, int $daysDifference) {
        $tasks = $this->taskMapper->getActiveTasksOfUser($aliveStatus->getUserId());

        foreach ($tasks as $task) {
            $deathDays = $task->getDeathDays();

            if($daysDifference >= $deathDays) {
                foreach($this->jobMapper->getJobsOfGroup($task->getJobsGroupId()) as $job) {
                    foreach($this->contactMapper->getContactsOfGroup($task->getContactsGroupId()) as $contact) {
                        $this->mailService->sendEmail($contact->getEmail(), $job->getEmailSubject(), $job->getEmailBody());
                    }  
                }

                // Set task to inactive
                $this->taskMapper->toggleActive($task->getId(), false);
            }
        }
    }
}