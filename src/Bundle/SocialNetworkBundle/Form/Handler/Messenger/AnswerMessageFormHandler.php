<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Bundle\SocialNetworkBundle\Form\Handler\Messenger;

use Kiboko\Bundle\SocialNetworkBundle\Entity\Message;
use Kiboko\Bundle\SocialNetworkBundle\Entity\MessageTarget;
use Kiboko\Bundle\SocialNetworkBundle\Entity\User;
use Kiboko\Bundle\SocialNetworkBundle\Mailer\MessengerMailer;
use Symfony\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class AnswerMessageFormHandler
{
    /**
     * @var Symfony\Component\Form\Form
     */
    private $form;

    /**
     * @var Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     * @var Symfony\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;

    /**
     * @var MessengerMailer
     */
    private $mailer;

    /**
     * Constructor.
     *
     * @param Symfony\Component\Form\Form              $form
     * @param Symfony\Component\HttpFoundation\Request $request
     * @param Symfony\Bundle\DoctrineBundle\Registry   $doctrine
     */
    public function __construct(Form $form, Request $request, Registry $doctrine, MessengerMailer $mailer)
    {
        $this->form = $form;
        $this->request = $request;
        $this->doctrine = $doctrine;
        $this->mailer = $mailer;
    }

    /**
     * Processing form values.
     *
     * @param Message $message
     * @param User    $user
     * @param $participants
     *
     * @return bool
     */
    public function process(Message $message, User $user, $participants)
    {
        if ($this->request->getMethod() === 'POST') {
            $this->form->bindRequest($this->request);
            if ($this->form->isValid()) {
                $answer = $this->form->getData();
                $answer->setParent($message);
                $answer->setSender($user);
                $em = $this->doctrine->getEntityManager();
                $em->persist($answer);
                $unreadMessageUsers = [];
                foreach ($participants as $participant) {
                    $answerTarget = new MessageTarget();
                    $answerTarget->setHasRead(true);
                    $answerTarget->setTarget($participant);
                    $answerTarget->setMessage($answer);
                    $em->persist($answerTarget);
                    // We do not set unread message for current user
                    if ($participant->getId() !== $user->getId()) {
                        $targets = $message->getTarget();
                        foreach ($targets as $target) {
                            $this->mailer->sendAnswerEmailMessage($target->getTarget(), $message, $answer);
                        }
                        $unreadMessageUsers[] = $participant;
                    }
                }
                $em->persist($message);
                $em->flush();
                $this->doctrine
                        ->getRepository('KibokoSocialNetworkBundle:Message')
                        ->markRootAsUnread($message, $unreadMessageUsers);

                return true;
            }
        }

        return false;
    }
}
