<?php


namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
class EmailService //Création de la class EmailService
{
    private $mailer; //Je déclare la propriété Mailer de symfony, elle stocke les instance du service de messagerie 

    public function __construct(MailerInterface $mailer) //Le constructeur injecte MailerInterface, fournit par symfony
    {

        $this->mailer = $mailer;
}


//Création de la méthode permettant d'envoyer un email, elle prend 3 paramètres
public function sendEmail(string $to, string $subject, string $content): void{
        $email = (new Email())
            ->from('admin@zoo-arcadia.com')
            ->to($to)
            ->subject($subject)
            ->text($content);

        $this->mailer->send($email);
}

}
