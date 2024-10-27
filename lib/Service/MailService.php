<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\DeadManSwitch\Service;

use OCA\DeadManSwitch\Db\AliveStatus;
use OCA\DeadManSwitch\Db\Contact;
use OCA\DeadManSwitch\Db\ResetTokenMapper;
use OCP\IURLGenerator;
use OCP\IUserManager;
use OCP\Mail\IMailer;

class MailService {

    private IMailer $mailer;

    private IURLGenerator $urlGenerator;

    private IUserManager $userManager;

    private ResetTokenMapper $tokenMapper;

    public function __construct(IMailer $mailer, IURLGenerator $urlGenerator, IUserManager $userManager, ResetTokenMapper $tokenMapper) {
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->userManager = $userManager;
        $this->tokenMapper = $tokenMapper;
    }

    public function sendCheckInEmail(Contact $contact, AliveStatus $aliveStatus) {
        $user = $this->userManager->get($aliveStatus->getUserId());
        $resetToken = $this->tokenMapper->createResetToken($contact->getId(), $aliveStatus->getId());

        $subject = "Nextcloud Dead Man Switch: Check In";
        $htmlBody = "<doctype html><html><body><div>Bitte klicken Sie hier um den Dead Man Switch für " . $user->getDisplayName() . " zurückzusetzen:</div><div><a href='" . $this->urlGenerator->linkToRouteAbsolute('deadmanswitch.checkin.reset', ['token' => $resetToken->getToken()]) . "';>Reset</a></div></body></html>";
        $this->sendEmail($contact->getEmail(), $subject, htmlBody:$htmlBody);
    }

    public function sendEmail(string $email, string $subject = "", string $body = "", $htmlBody = ""): void {
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