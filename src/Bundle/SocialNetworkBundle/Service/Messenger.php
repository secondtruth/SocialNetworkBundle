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
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\SecurityContext;

class Messenger
{
    /**
     * Doctrine object.
     *
     * @var Doctrine
     */
    protected $doctrine;

    /**
     * Security contect.
     *
     * @var SecurityContext
     */
    private $securityContext;

    /**
     * Constructor.
     *
     * @param RegistryInterface $doctrine
     * @param SecurityContext   $securityContext
     */
    public function __construct(RegistryInterface $doctrine, SecurityContext $securityContext)
    {
        $this->doctrine = $doctrine;
        $this->securityContext = $securityContext;
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
        $message->setSender($this->securityContext->getToken()->getUser());
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
        $em = $this->doctrine->getEntityManager();
        $em->persist($messageTarget);
        $em->persist($message);
        $em->flush();
    }
}
