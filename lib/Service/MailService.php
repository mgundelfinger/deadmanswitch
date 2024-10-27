<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\DeadManSwitch\Service;

use OCA\DeadManSwitch\Db\AliveStatus;
use OCA\DeadManSwitch\Db\Contact;
use OCA\DeadManSwitch\Db\ResetTokenMapper;
use OCP\IURLGenerator;
use OCP\IUserManager;
use OCP\L10N\IFactory;
use OCP\Mail\IMailer;

class MailService {

    private IMailer $mailer;

    private IURLGenerator $urlGenerator;

    private IUserManager $userManager;

    private ResetTokenMapper $tokenMapper;

    private IFactory $l10nFactory;

    public function __construct(IMailer $mailer, IURLGenerator $urlGenerator, IUserManager $userManager, ResetTokenMapper $tokenMapper, IFactory $l10nFactory) {
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->userManager = $userManager;
        $this->tokenMapper = $tokenMapper;
        $this->l10nFactory = $l10nFactory;
    }

    public function sendCheckInEmail(Contact $contact, AliveStatus $aliveStatus) {
        $user = $this->userManager->get($aliveStatus->getUserId());
        $lang = $this->l10nFactory->getUserLanguage($user);
        $l = $this->l10nFactory->get('deadmanswitch', $lang);
        $resetToken = $this->tokenMapper->createResetToken($contact->getId(), $aliveStatus->getId());
        $resetLink = $this->urlGenerator->linkToRouteAbsolute('deadmanswitch.checkin.reset', ['token' => $resetToken->getToken()]);
        $subject = "Nextcloud ". $l->t('Dead Man Switch') . ": " . $l->t('Check-in');
        $htmlBody = "<doctype html><html><body><div>" . $l->t('Click here to confirm that %1$s is still alive', [$user->getDisplayName()]) . ":</div><div><a href='" . $resetLink . "';>" . $l->t('Confirm') . "</a></div></body></html>";
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