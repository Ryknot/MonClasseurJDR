<?php

namespace App\Service;


use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendMailCodeActivation($toEmail, $content){
        $email = (new Email())
            ->from('contact.monclasseurjdr@gmail.com')
            ->to($toEmail)
            //->cc('exemple@mail.com')
            //->bcc('exemple@mail.com')
            //->replyTo('test42@gmail.com')
            ->priority(Email::PRIORITY_HIGH)
            ->subject('Ne pas répondre: Envoi du code d\'activation')
            // If you want use text mail only
            //->text('text')
            // Raw html
            ->html($content)
        ;
        $this->mailer->send($email);
    }

    public function sendUserValidated($toEmail, $content){
        $email = (new Email())
            ->from('contact.monclasseurjdr@gmail.com')
            ->to($toEmail)
            //->cc('exemple@mail.com')
            //->bcc('exemple@mail.com')
            //->replyTo('test42@gmail.com')
            ->priority(Email::PRIORITY_HIGH)
            ->subject('Ne pas répondre: Compte validé')
            // If you want use text mail only
            //->text('text')
            // Raw html
            ->html($content)
        ;
        $this->mailer->send($email);
    }

    public function sendMailContactUser($toEmail, $content)
    {
        $email = (new Email())
            ->from('contact.monclasseurjdr@gmail.com')
            ->to($toEmail)
            //->cc('exemple@mail.com')
            //->bcc('exemple@mail.com')
            //->replyTo('test42@gmail.com')
            ->priority(Email::PRIORITY_HIGH)
            ->subject('Ne pas répondre: Message d\'un administrateur')
            // If you want use text mail only
            //->text('text')
            // Raw html
            ->html($content)
        ;
        $this->mailer->send($email);
    }

}