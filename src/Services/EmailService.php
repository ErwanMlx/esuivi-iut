<?php

namespace App\Services;

use FOS\UserBundle\Util\TokenGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Entity\User;
use Symfony\Component\DependencyInjection\Container;

class EmailService
{

    protected $mailer;
    protected $twig;
    private $email;
    private $name;
    private $container;
    private $tokenGenerator;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, $email, $name, Container $container, TokenGenerator $tokenGenerator)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->email = $email;
        $this->name = $name;
        $this->container = $container;
        $this->tokenGenerator = $tokenGenerator;
    }

    public function nouveau_compte(User $user) {
        $encoder = $this->container->get('security.password_encoder');

        $password = substr($this->tokenGenerator->generateToken(), 0, 8);

        $encoded = $encoder->encodePassword($user, $password);

        $user->setPassword($encoded);

        $message = (new \Swift_Message('Esuivi-IUT - Compte créé'))
            ->setFrom([$this->email => $this->name])
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render(
                    'email/nouveau_compte.html.twig',
                    array('user' => $user, 'password' => $password)
                ),
                'text/html'
            )
        ;

        $this->mailer->send($message);
    }

    public function relance_apprenti(User $user) {

        $message = (new \Swift_Message('Esuivi-IUT - Rappel validation étape'))
            ->setFrom([$this->email => $this->name])
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render(
                    'email/relance_apprenti.html.twig',
                    array('user' => $user)
                ),
                'text/html'
            )
        ;

        $this->mailer->send($message);
    }

}
