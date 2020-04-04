<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class Mailer
{
    public const FROM_ADDRESS = 'alex617180@mail.ru';

    private $mailer;

    public function __construct(MailerInterface $mailer)  {

        $this->mailer = $mailer;       
    }

    public function sendConfirmationEmail(User $user)
    {
        $email = (new TemplatedEmail())
            ->from(self::FROM_ADDRESS)
            ->to($user->getEmail())
            ->subject('Вы успешно Зарегистрировались!')

            // path of the Twig template to render
            ->htmlTemplate('security/confirmation.html.twig')

            // pass variables (name => value) to the template
            ->context(['user' => $user]);

        $this->mailer->send($email);
    }
}
