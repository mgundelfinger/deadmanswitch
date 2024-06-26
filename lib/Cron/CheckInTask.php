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

    // TODO prototype only
    public const INTERVAL_TEST = 0;

    public const INTERVAL_DAILY = 1;
    public const INTERVAL_WEEKLY = 7;
    public const INTERVAL_FOUR_WEEKLY = 28;


    public function __construct(ITimeFactory $time, MailService $mailService, IConfig $config) {
        parent::__construct($time);
        $this->mailService = $mailService;
        $this->config = $config;

        // TODO run daily
        $this->setInterval(300);
    }

    protected function run($arguments) {
        /** @var IUser $user */
        $email = $arguments['email'];
        $uid = $arguments['uid'];

        $interval = (int) $this->config->getUserValue($uid, Application::APP_ID, PageController::CHECK_IN_INTERVAL_CONFIG_KEY);
        $lastCheckInString = $this->config->getUserValue($uid, Application::APP_ID, PageController::LAST_CHECK_IN_CONFIG_KEY);
        $switchArmed = (bool) $this->config->getUserValue($uid, Application::APP_ID, PageController::SWITCH_ARMED_CONFIG_KEY);
        $switchComplete = (bool) $this->config->getUserValue($uid, Application::APP_ID, PageController::SWITCH_COMPLETE_CONFIG_KEY);
        $transferEmail = $this->config->getUserValue($uid, Application::APP_ID, PageController::TRANSFER_EMAIL_CONFIG_KEY);
        $lastCheckIn = new DateTime($lastCheckInString);
        $now = new DateTime();
        $daysSinceLastCheckIn = $lastCheckIn->diff($now)->days;

        // TODO prototype only
        if ($interval == self::INTERVAL_TEST)
        {
            if ($switchArmed and !$switchComplete) {
                $this->mailService->sendFinalEmail($transferEmail, $email);
                $this->config->setUserValue($uid, Application::APP_ID, PageController::SWITCH_COMPLETE_CONFIG_KEY, '1');
            } else if (!$switchArmed) {
                $this->mailService->sendCheckInEmail($email);
                $this->config->setUserValue($uid, Application::APP_ID, PageController::SWITCH_ARMED_CONFIG_KEY, '1');
            }
            return;
        }

        if ($interval <= $daysSinceLastCheckIn and !$switchArmed)
        {
            $this->mailService->sendCheckInEmail($email);
            $this->config->setUserValue($uid, Application::APP_ID, PageController::SWITCH_ARMED_CONFIG_KEY, '1');
        } else if ($daysSinceLastCheckIn + 1 >= $interval and $switchArmed and !$switchComplete) {
            $this->mailService->sendFinalEmail($transferEmail, $email);
        }
    }

}