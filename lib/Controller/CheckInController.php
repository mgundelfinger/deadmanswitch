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

    public const SUBJECT_DAILY = 'every 5 minutes';
    public const SUBJECT_WEEKLY = 'every 10 minutes';
    public const SUBJECT_FOUR_WEEKLY = 'every 15 minutes';

    public function __construct(string $appName, IRequest $request, IJobList $jobList) {
        parent::__construct($appName, $request);

        $this->jobList = $jobList;
    }

    public function addJob(string $email, int $interval) {
        switch($interval) {
            case self::INTERVAL_DAILY:
                $job = DailyCheckInTask::class;
                $text = self::SUBJECT_DAILY;
                break;
            case self::INTERVAL_WEEKLY:
                $job = WeeklyCheckInTask::class;
                $text = self::SUBJECT_WEEKLY;
                break;
            case self::INTERVAL_FOUR_WEEKLY:
                $job = FourWeeklyCheckInTask::class;
                $text = self::SUBJECT_FOUR_WEEKLY;
                break;
            default:
                return 0;
        }
        $this->jobList->add($job, ['email' => $email, 'text' => $text]);
    }

    public function removeJob(string $email, int $interval) {
        switch($interval) {
            case self::INTERVAL_DAILY:
                $job = DailyCheckInTask::class;
                $text = self::SUBJECT_DAILY;
                break;
            case self::INTERVAL_WEEKLY:
                $job = WeeklyCheckInTask::class;
                $text = self::SUBJECT_WEEKLY;
                break;
            case self::INTERVAL_FOUR_WEEKLY:
                $job = FourWeeklyCheckInTask::class;
                $text = self::SUBJECT_FOUR_WEEKLY;
                break;
            default:
                return 0;
        }
        $this->jobList->remove($job, ['email' => $email, 'text' => $text]);
    }
}