<?php
declare(strict_types=1);

namespace App\Services;

use App\DTO\Mails\AddresseeInterface;
use App\DTO\Mails\MailInterface;
use PHPMailer\PHPMailer\PHPMailer;

class MailService
{
    private PHPMailer $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        // $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $this->mail->isSMTP();

        $this->mail->Host       = 'mailhog';
        $this->mail->SMTPAuth   = false;
        $this->mail->Username   = 'user@example.com';                     //SMTP username
        $this->mail->Password   = 'secret';                               //SMTP password
        // $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $this->mail->Port       = 1025;
    }

    public function send(AddresseeInterface $from, AddresseeInterface $to, MailInterface $mail)
    {
        $this->mail->setFrom($from->address, $from->name);
        $this->mail->addAddress($to->address, $to->name);
        // try {
        $this->mail->isHTML($mail->isHTML());
        $this->mail->Subject = $mail->getSubject();
        $this->mail->Body = $mail->getBody();

        return $this->mail->send();
        // } catch (\Exception $e) {
            // better error handling
            // throw $e;
        // }
    }
}
