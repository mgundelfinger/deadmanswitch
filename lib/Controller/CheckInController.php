<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\DeadManSwitch\Controller;

use OCA\DeadManSwitch\Cron\DailyCheckInTask;
use OCA\DeadManSwitch\Cron\FourWeeklyCheckInTask;
use OCA\DeadManSwitch\Cron\WeeklyCheckInTask;
use OCP\AppFramework\Controller;
use OCP\BackgroundJob\IJobList;
use OCP\IRequest;

class CheckInController extends Controller {

    private IJobList $jobList;

    public const INTERVAL_DAILY = 0;
    public const INTERVAL_WEEKLY = 1;
    public const INTERVAL_FOUR_WEEKLY = 2;

    public function __construct(string $appName, IRequest $request, IJobList $jobList) {
        parent::__construct($appName, $request);

        $this->jobList = $jobList;
    }

    public function addJob(string $email, int $interval) {
        switch($interval) {
            case self::INTERVAL_DAILY:
                $this->jobList->add(DailyCheckInTask::class, ['email' => $email]);
            case self::INTERVAL_WEEKLY:
                $this->jobList->add(WeeklyCheckInTask::class, ['email' => $email]);
            case self::INTERVAL_FOUR_WEEKLY:
                $this->jobList->add(FourWeeklyCheckInTask::class, ['email' => $email]);
        }
    }

    public function removeJob(string $email, int $interval) {
        switch($interval) {
            case self::INTERVAL_DAILY:
                $this->jobList->remove(DailyCheckInTask::class, ['email' => $email]);
            case self::INTERVAL_DAILY:
                $this->jobList->remove(WeeklyCheckInTask::class, ['email' => $email]);
            case self::INTERVAL_DAILY:
                $this->jobList->remove(FourWeeklyCheckInTask::class, ['email' => $email]);
        }
    }
}