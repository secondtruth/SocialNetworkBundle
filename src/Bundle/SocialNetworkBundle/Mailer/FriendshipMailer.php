<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Bundle\SocialNetworkBundle\Mailer;

use Kiboko\Bundle\SocialNetworkBundle\Entity\User;

/**
 * Friendship mailer.
 *
 * @author Vincent Guerard <v.guerard@fulgurio.net>
 */
class FriendshipMailer extends AbstractMailer
{
    /**
     * Invit invitation message.
     *
     * @param User $user
     */
    public function sendInvitMessage(User $user)
    {
        $subject = $this->templating->render(
                $this->parameters['invit.subject'],
                ['user' => $user]
        );
        $data = ['user' => $user, 'subject' => $subject];
        $bodyText = $this->templating->render(
                $this->parameters['invit.template.text'], $data
        );
        $bodyHTML = $this->templating->render(
                $this->parameters['invit.template.html'], $data
        );
        $bodyMsn = $this->templating->render(
                $this->parameters['invit.template.html'], $data
        );
        if ($user->getSendMsgToEmail()) {
            $this->sendEmailMessage(
                    $this->parameters['from'],
                    $user->getEmail(),
                    $subject,
                    $bodyText,
                    $bodyHTML
            );
        }
        $this->messenger->sendMessage($user, $subject, $bodyMsn, true, 'friendship-invit');
    }

    /**
     * Accept invitation message.
     *
     * @param User $user
     */
    public function sendAcceptMessage(User $user)
    {
        $subject = $this->templating->render(
                $this->parameters['accept.subject'],
                ['user' => $user]
        );
        $data = ['user' => $user, 'subject' => $subject];
        $bodyText = $this->templating->render(
                $this->parameters['accept.template.text'], $data
        );
        $bodyHTML = $this->templating->render(
                $this->parameters['accept.template.html'], $data
        );
        $bodyMsn = $this->templating->render(
                $this->parameters['accept.template.msn'], $data
        );
        if ($user->getSendMsgToEmail()) {
            $this->sendEmailMessage(
                    $this->parameters['from'],
                    $user->getEmail(),
                    $subject,
                    $bodyText,
                    $bodyHTML
            );
        }
        $this->messenger->sendMessage($user, $subject, $bodyMsn, true, 'friendship-accept');
    }

    /**
     * Remove invitation message.
     *
     * @param User $user
     */
    public function sendRemoveInvitMessage(User $user)
    {
        $subject = $this->templating->render(
                $this->parameters['remove.subject'],
                ['user' => $user]
        );
        $data = ['user' => $user, 'subject' => $subject];
        $bodyText = $this->templating->render(
                $this->parameters['remove.template.text'], $data
        );
        $bodyHTML = $this->templating->render(
                $this->parameters['remove.template.html'], $data
        );
        $bodyMsn = $this->templating->render(
                $this->parameters['remove.template.msn'], $data
        );
        if ($user->getSendMsgToEmail()) {
            $this->sendEmailMessage(
                    $this->parameters['from'],
                    $user->getEmail(),
                    $subject,
                    $bodyText,
                    $bodyHTML
                );
        }
        $this->messenger->sendMessage($user, $subject, $bodyMsn, true, 'friendship-remove');
    }

    /**
     * Refusal invitation message.
     *
     * @param User $user
     */
    public function sendRefusalMessage(User $user)
    {
        $subject = $this->templating->render(
                $this->parameters['refuse.subject'],
                ['user' => $user]
        );
        $data = ['user' => $user, 'subject' => $subject];
        $bodyText = $this->templating->render(
                $this->parameters['refuse.template.text'], $data
        );
        $bodyHTML = $this->templating->render(
                $this->parameters['refuse.template.html'], $data
        );
        $bodyMsn = $this->templating->render(
                $this->parameters['refuse.template.msn'], $data
        );
        if ($user->getSendMsgToEmail()) {
            $this->sendEmailMessage(
                    $this->parameters['from'],
                    $user->getEmail(),
                    $subject,
                    $bodyText,
                    $bodyHTML
            );
        }
        $this->messenger->sendMessage($user, $subject, $bodyMsn, true, 'friendship-refusal');
    }
}
