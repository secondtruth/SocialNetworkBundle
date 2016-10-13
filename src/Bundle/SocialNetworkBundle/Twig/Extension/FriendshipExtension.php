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

class FriendshipExtension extends \Twig_Extension
{
    /**
     * @var RegistryInterface
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
            'nbOfPendingUser' => new \Twig_Function_Method($this, 'nbOfPendingUser', ['is_safe' => ['html']]),
        ];
    }

    /**
     * Display pending invitation.
     *
     * @param User $user
     *
     * @return number
     */
    public function nbOfPendingUser(User $user)
    {
        return $this->doctrine
            ->getRepository('KibokoSocialNetworkBundle:UserFriendship')
            ->countPendingUserOfFrienship($user);
    }

    /**
     * (non-PHPdoc).
     *
     * @see Twig_ExtensionInterface::getName()
     */
    public function getName()
    {
        return 'friendship';
    }
}
