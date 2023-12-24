<?php
namespace OCA\DeadManSwitch\Service;

use OCP\Mail\IMailer;

class MailService {

    private IMailer $mailer;

    public function __construct(IMailer $mailer) {
        $this->mailer = $mailer;
    }

    public function notify(string $email): void {
        $message = $this->mailer->createMessage();
        $message->setSubject("Hello from Nextcloud");
        $message->setPlainBody("This is some text");
        $message->setHtmlBody(
            "<!doctype html><html><body>This is some <b>text</b></body></html>"
        );
        $message->setTo([$email]);
        $this->mailer->send($message);
    }
}