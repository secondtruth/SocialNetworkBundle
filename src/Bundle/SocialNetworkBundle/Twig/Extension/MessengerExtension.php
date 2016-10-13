<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Bundle\SocialNetworkBundle\Twig\Extension;

use Kiboko\Bundle\SocialNetworkBundle\Entity\User;
use Symfony\Bridge\Doctrine\RegistryInterface;

class MessengerExtension extends \Twig_Extension
{
    /**
     * @var Symfony\Bridge\Doctrine\RegistryInterface
     */
    protected $doctrine;

    /**
     * Constructor.
     *
     * @param RegistryInterface $doctrine
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * (non-PHPdoc).
     *
     * @see Twig_Extension::getFunctions()
     */
    public function getFunctions()
    {
        return [
            'nbOfUnreadMessage' => new \Twig_Function_Method($this, 'nbOfUnreadMessage', ['is_safe' => ['html']]),
        ];
    }

    /**
     * Display number or unread message.
     *
     * @param User $user
     *
     * @return number
     */
    public function nbOfUnreadMessage(User $user)
    {
        return $this->doctrine
                ->getRepository('KibokoSocialNetworkBundle:Message')
                ->countUnreadMessage($user);
    }

    /**
     * (non-PHPdoc).
     *
     * @see Twig_ExtensionInterface::getName()
     */
    public function getName()
    {
        return 'messenger';
    }
}
