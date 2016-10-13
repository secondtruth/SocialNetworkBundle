<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Bundle\SocialNetworkBundle\Form\Handler\Messenger;

use Kiboko\Bundle\SocialNetworkBundle\Entity\MessageTarget;
use Kiboko\Bundle\SocialNetworkBundle\Entity\User;
use Kiboko\Bundle\SocialNetworkBundle\Mailer\MessengerMailer;
use Symfony\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class NewMessageFormHandler
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
     * @param User $user
     *
     * @return bool
     */
    public function process(User $user)
    {
        if ($this->request->getMethod() === 'POST') {
            $this->form->bindRequest($this->request);
            if ($this->form->isValid()) {
                $message = $this->form->getData();
                $message->setSender($user);
                $targets = $message->getTarget();
                foreach ($targets as $target) {
                    $this->mailer->sendMessageEmailMessage($target->getTarget(), $message);
                }
                $messageTarget = new MessageTarget();
                $messageTarget->setTarget($user);
                $messageTarget->setMessage($message);
                $messageTarget->setHasRead(true);
                $message->addMessageTarget($messageTarget);
                $em = $this->doctrine->getEntityManager();
                $em->persist($messageTarget);
                $em->persist($message);
                $em->flush();

                return true;
            }
        }

        return false;
    }
}
