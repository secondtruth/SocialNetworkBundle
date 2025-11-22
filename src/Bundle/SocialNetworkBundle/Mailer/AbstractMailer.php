<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Bundle\SocialNetworkBundle\Mailer;

use Kiboko\Bundle\SocialNetworkBundle\Service\Messenger;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 * Admin contact form type.
 *
 * @author Vincent Guerard <v.guerard@fulgurio.net>
 */
abstract class AbstractMailer
{
    /**
     * @var MailerInterface
     */
    protected $mailer;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var Environment
     */
    protected $templating;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * @var Messenger
     */
    protected $messenger;

    /**
     * Constructor.
     *
     * @param MailerInterface $mailer
     * @param RouterInterface $router
     * @param Environment     $templating
     * @param array           $parameters
     */
    public function __construct(MailerInterface $mailer, RouterInterface $router, Environment $templating, array $parameters)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->templating = $templating;
        $this->parameters = $parameters;
    }

    /**
     * Send html and text email.
     *
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param string $bodyHTML
     * @param string $bodyText
     * @param string $fromName
     */
    protected function sendEmailMessage($from, $to, $subject, $bodyHTML, $bodyText, $fromName = null)
    {
        if (is_array($from)) {
            $fromName = current($from);
            $from = key($from);
        }
        $email = (new \Symfony\Component\Mime\Email())
            ->from($fromName ? new \Symfony\Component\Mime\Address($from, $fromName) : $from)
            ->to($to)
            ->subject($subject)
            ->html($bodyHTML)
            ->text($bodyText);
        $this->mailer->send($email);
    }

    /**
     * $messenger setter.
     *
     * @param Messenger $messenger
     */
    public function setMessenger(Messenger $messenger)
    {
        $this->messenger = $messenger;
    }
}
