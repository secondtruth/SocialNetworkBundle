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
use Doctrine\Persistence\ManagerRegistry;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FriendshipExtension extends AbstractExtension
{
    /**
     * @var ManagerRegistry
     */
    protected $doctrine;

    /**
     * Constructor.
     *
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
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
            'nbOfPendingUser' => new TwigFunction('nbOfPendingUser', [$this, 'nbOfPendingUser'], ['is_safe' => ['html']]),
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
