<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Bundle\SocialNetworkBundle\Mailer;

use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserInterface;

/**
 * Fos overrider mailer.
 *
 * @author Vincent Guerard <v.guerard@fulgurio.net>
 */
class FosMailer extends AbstractMailer implements MailerInterface
{
    /**
     * Welcome message email.
     *
     * @param UserInterface $user
     */
    public function sendRegistrationEmailMessage(UserInterface $user)
    {
        $subject = $this->templating->render(
                $this->parameters['registration.subject'],
                ['user' => $user]);
        $bodyText = $this->templating->render(
                $this->parameters['registration.template.text'],
                ['user' => $user]);
        $bodyHTML = $this->templating->render(
                $this->parameters['registration.template.html'],
                ['user' => $user]);
        $this->sendEmailMessage(
                $this->parameters['registration.from_mail'],
                $user->getEmail(),
                $subject,
                $bodyText,
                $bodyHTML
                );
    }

    /**
     * Confirmation email sender.
     *
     * @param UserInterface $user
     */
    public function sendConfirmationEmailMessage(UserInterface $user)
    {
        $url = $this->router->generate('fos_user_registration_confirm',
                ['token' => $user->getConfirmationToken()], true);
        $data = [
            'user' => $user,
            'confirmationUrl' => $url, ];
        $subject = $this->templating->render(
                $this->parameters['confirmation.subject']
        );
        $bodyText = $this->templating->render(
                $this->parameters['confirmation.template.txt'], $data
        );
        $bodyHTML = $this->templating->render(
                $this->parameters['confirmation.template.html'], $data
        );
        $this->sendEmailMessage(
                $this->parameters['confirmation.from_mail'],
                $user->getEmail(),
                $subject,
                $bodyHTML,
                $bodyText
        );
    }

    /**
     * Resetting email sender.
     *
     * @param UserInterface $user
     */
    public function sendResettingEmailMessage(UserInterface $user)
    {
        $url = $this->router->generate('fos_user_resetting_reset',
                ['token' => $user->getConfirmationToken()], true);
        $data = [
            'user' => $user,
            'confirmationUrl' => $url, ];
        $subject = $this->templating->render(
                $this->parameters['resetting.subject']
        );
        $bodyText = $this->templating->render(
                $this->parameters['resetting.template.text'], $data
        );
        $bodyHTML = $this->templating->render(
                $this->parameters['resetting.template.html'], $data
        );
        $this->sendEmailMessage(
                $this->parameters['resetting.from_mail'],
                $user->getEmail(),
                $subject,
                $bodyHTML,
                $bodyText
        );
    }
}
