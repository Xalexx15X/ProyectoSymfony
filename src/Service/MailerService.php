<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendWelcomeEmail(User $user): void
    {
        $email = (new Email())
            ->from('aplicaciondepreguntas@gmail.com') // Dirección del remitente
            ->to($user->getEmail()) // Dirección del destinatario
            ->subject('Registro exitoso') // Asunto del correo
            ->html('<p>Te has registrado correctamente. Ahora puedes iniciar sesión en tu cuenta.</p>'); // Contenido del mensaje

        $this->mailer->send($email);
    }
}
