<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\DeadManSwitch\Cron;

use DateTime;
use OCA\DeadManSwitch\AppInfo\Application;
use OCA\DeadManSwitch\Controller\PageController;
use OCA\DeadManSwitch\Service\MailService;
use OCP\BackgroundJob\TimedJob;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\IConfig;
use OCP\IUser;

class CheckInTask extends TimedJob {

    private MailService $mailService;

    private IConfig $config;

    public const INTERVAL_DAILY = 1;
    public const INTERVAL_WEEKLY = 7;
    public const INTERVAL_FOUR_WEEKLY = 28;



    public function __construct(ITimeFactory $time, MailService $mailService, IConfig $config) {
        parent::__construct($time);
        $this->mailService = $mailService;
        $this->config = $config;

        $this->setInterval(300 - 60);
    }

    protected function run($arguments) {
        /** @var IUser $user */
        $email = $arguments['email'];
        $uid = $arguments['uid'];

        $lastCheckIn = $this->config->getUserValue($uid, Application::APP_ID, PageController::LAST_CHECK_IN_CONFIG_KEY);
        $interval = $this->config->getUserValue($uid, Application::APP_ID, PageController::CHECK_IN_INTERVAL_CONFIG_KEY);
        $now = date_format(new DateTime(), 'Ymd');

        if ($interval <= $now - $lastCheckIn)
        {
            $this->mailService->notify($email, "SUCCESS: {$interval} - {$now}, {$lastCheckIn}");
        } else {
            $this->mailService->notify($email, "FAILURE: {$interval} - {$now}, {$lastCheckIn}");
        }
    }

}