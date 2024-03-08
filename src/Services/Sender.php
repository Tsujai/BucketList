<?php

namespace App\Services;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class Sender {

    public function __construct(private MailerInterface $mailer){

    }

    public function sendEmail(string $subject, string $text, string $destinataire){
        $email = new Email();
        $email->subject($subject)
            ->text($text)
            ->from('no-reply@bucket-list.fr')
            ->to($destinataire);

        $this->mailer->send($email);

    }


}