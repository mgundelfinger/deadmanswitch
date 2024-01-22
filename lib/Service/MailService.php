<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\DeadManSwitch\Service;

use OCP\Mail\IMailer;

class MailService {

    private IMailer $mailer;

    public function __construct(IMailer $mailer) {
        $this->mailer = $mailer;
    }

    public function notify(string $email, string $text): void {
        $message = $this->mailer->createMessage();
        $message->setSubject($text);
        $message->setPlainBody("This message is being sent " . $text . ".");
        // $message->setHtmlBody(
        //     "<!doctype html><html><body>This is some <b>text</b></body></html>"
        // );
        $message->setTo([$email]);
        $this->mailer->send($message);
    }
}