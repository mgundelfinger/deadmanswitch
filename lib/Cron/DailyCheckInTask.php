<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\DeadManSwitch\Cron;

use OCA\DeadManSwitch\Service\MailService;
use OCP\BackgroundJob\TimedJob;
use OCP\AppFramework\Utility\ITimeFactory;

class DailyCheckInTask extends TimedJob {

    private MailService $mailService;

    public function __construct(ITimeFactory $time, MailService $mailService) {
        parent::__construct($time);
        $this->mailService = $mailService;

        $this->setInterval(300 - 60);
    }

    protected function run($arguments) {
        $this->mailService->notify($arguments['email'], $arguments['text']);
    }

}