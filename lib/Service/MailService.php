<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\DeadManSwitch\Service;

use OCP\IURLGenerator;
use OCP\Mail\IMailer;

class MailService {

    private IMailer $mailer;

    private IURLGenerator $urlGenerator;

    public function __construct(IMailer $mailer, IURLGenerator $urlGenerator) {
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
    }

    public function sendCheckInEmail(string $email) {
        $subject = "Nextcloud Dead Man Switch: Check In";
        $htmlBody = "<doctype html><html><body><div>Bitte klicken Sie hier um ihren Dead Man Switch zurückzusetzen:</div><div><a href='" . $this->urlGenerator->linkToRouteAbsolute('deadmanswitch.page.checkInPage') . "';>Reset</a></div></body></html>";
        $this->notify($email, $subject, htmlBody:$htmlBody);
    }

    public function sendFinalEmail(string $email, string $originalEmail) {
        $subject = "Nextcloud Dead Man Switch: Account Transfer";
        $htmlBody = "<doctype html><html><body><div>Der Nextcloud Account von $originalEmail steht Ihnen jetzt zur Verfügung/div></body></html>";
        
    }

    public function notify(string $email, string $subject, string $body = "", $htmlBody = ""): void {
        $message = $this->mailer->createMessage();
        $message->setSubject($subject);
        if (!empty($htmlBody)) {
            $message->setHtmlBody(
                $htmlBody
            );
        } else {
            $message->setPlainBody($body);
        }
        $message->setTo([$email]);
        $this->mailer->send($message);
    }
}