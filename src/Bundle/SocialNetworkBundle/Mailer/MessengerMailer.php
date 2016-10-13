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
use Kiboko\Bundle\SocialNetworkBundle\Entity\Message;

class MessengerMailer extends AbstractMailer
{
    /**
     * Message.
     *
     * @param UserInterface $user
     * @param Message       $message
     */
    public function sendMessageEmailMessage(UserInterface $user, Message $message)
    {
        if (!$user->getSendMsgToEmail()) {
            return;
        }
        $data = [
            'user' => $user,
            'message' => $message,
        ];
        $subject = $this->templating->render(
                $this->parameters['message.subject'],
                $data
        );
        $bodyText = $this->templating->render(
                $this->parameters['message.template.text'],
                $data
        );
        $bodyHTML = $this->templating->render(
                $this->parameters['message.template.html'],
                $data
        );
        $this->sendEmailMessage(
                $this->parameters['message.from'],
                $user->getEmail(),
                $subject,
                $bodyText,
                $bodyHTML,
                $this->parameters['message.from_name']
        );
    }

    /**
     * Answer of a message.
     *
     * @param UserInterface $user
     * @param Message       $message
     * @param Message       $answer
     */
    public function sendAnswerEmailMessage(UserInterface $user, Message $message, Message $answer)
    {
        if (!$user->getSendMsgToEmail()) {
            return;
        }
        $data = [
            'user' => $user,
            'message' => $message,
            'answer' => $answer,
        ];
        $subject = $this->templating->render(
                $this->parameters['answer.subject'],
                $data
        );
        $bodyText = $this->templating->render(
                $this->parameters['answer.template.text'],
                $data
        );
        $bodyHTML = $this->templating->render(
                $this->parameters['answer.template.html'],
                $data
        );
        $this->sendEmailMessage(
                $this->parameters['answer.from'],
                $user->getEmail(),
                $subject,
                $bodyText,
                $bodyHTML,
                $this->parameters['answer.from_name']
        );
    }
}
