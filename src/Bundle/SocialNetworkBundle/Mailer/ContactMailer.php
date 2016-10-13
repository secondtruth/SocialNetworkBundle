<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Bundle\SocialNetworkBundle\Mailer;

use FOS\UserBundle\Model\UserInterface;

/**
 * Admin mailer.
 *
 * @author Vincent Guerard <v.guerard@fulgurio.net>
 */
class ContactMailer extends AbstractMailer
{
    /**
     * Contact email sender.
     *
     * @param UserInterface $user
     * @param string        $subject
     * @param string        $message
     */
    public function sendAdminMessage(UserInterface $user, $subject, $message)
    {
        $data = [
            'user' => $user,
            'subject' => $subject,
            'content' => $message,
        ];
        $bodyText = $this->templating->render(
                $this->parameters['admin.template.text'], $data
        );
        $bodyHTML = $this->templating->render(
                $this->parameters['admin.template.html'], $data
        );
        $bodyMsn = $this->templating->render(
                $this->parameters['admin.template.msn'], $data
        );
        $this->sendEmailMessage(
                $this->parameters['admin.from'],
                $user->getEmail(),
                $subject,
                $bodyHTML,
                $bodyText,
                $this->parameters['admin.from_name']
        );
        $this->messenger->sendMessage($user, $subject, $bodyMsn, true, 'admin-contact');
    }
}
