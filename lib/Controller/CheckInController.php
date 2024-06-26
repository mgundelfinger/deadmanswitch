<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\DeadManSwitch\Controller;

use OCA\DeadManSwitch\Cron\CheckInTask;
use OCP\AppFramework\Controller;
use OCP\BackgroundJob\IJobList;
use OCP\IRequest;

class CheckInController extends Controller {

    private IJobList $jobList;

    public function __construct(string $appName, IRequest $request, IJobList $jobList) {
        parent::__construct($appName, $request);

        $this->jobList = $jobList;
    }

    public function addJob(string $email, string $uid) {
        $this->jobList->add(CheckInTask::class, ['email' => $email, 'uid' => $uid]);
    }

    public function removeJob(string $email, string $uid) {
        $this->jobList->remove(CheckInTask::class, ['email' => $email, 'uid' => $uid]);
    }

    public function hasJob(string $email, string $uid) {
        return $this->jobList->has(CheckInTask::class, ['email' => $email, 'uid' => $uid]);
    }
}