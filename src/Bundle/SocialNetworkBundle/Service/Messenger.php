<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Bundle\SocialNetworkBundle\Service;

use Kiboko\Bundle\SocialNetworkBundle\Entity\Message;
use Kiboko\Bundle\SocialNetworkBundle\Entity\MessageTarget;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

class Messenger
{
    /**
     * Doctrine object.
     *
     * @var ManagerRegistry
     */
    protected $doctrine;

    /**
     * Security service.
     *
     * @var Security
     */
    private $security;

    /**
     * Constructor.
     *
     * @param ManagerRegistry $doctrine
     * @param Security        $security
     */
    public function __construct(ManagerRegistry $doctrine, Security $security)
    {
        $this->doctrine = $doctrine;
        $this->security = $security;
    }

    /**
     * Send message on messenger box.
     *
     * @param User   $userTgt
     * @param string $subject
     * @param string $content
     * @param bool   $canNotAnswer
     * @param string $typeOfMessage
     */
    public function sendMessage($userTgt, $subject, $content, $canNotAnswer = false, $typeOfMessage = null)
    {
        $message = new Message();
        $message->setSender($this->security->getUser());
        $message->setSubject($subject);
        $message->setContent($content);
        $message->setAllowAnswer(!$canNotAnswer);
        if (!is_null($typeOfMessage)) {
            $message->setTypeOfMessage($typeOfMessage);
        }
        $messageTarget = new MessageTarget();
        $messageTarget->setTarget($userTgt);
        $messageTarget->setMessage($message);
        $messageTarget->setHasRead(false);
        $message->addMessageTarget($messageTarget);
        $em = $this->doctrine->getManager();
        $em->persist($messageTarget);
        $em->persist($message);
        $em->flush();
    }
}
