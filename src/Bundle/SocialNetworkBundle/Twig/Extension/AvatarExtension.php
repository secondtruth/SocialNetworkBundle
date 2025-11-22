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
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * User avatar function for Twig.
 *
 * @author Vincent Guerard <v.guerard@fulgurio.net>
 */
class AvatarExtension extends AbstractExtension
{
    /**
     * Init Twig functions.
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('avatar', [$this, 'getAvatar'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Return user avatar.
     *
     * @param User|array $user
     */
    public function getAvatar($user)
    {
        if (is_array($user)) {
            return User::getAvatarUrl($user);
        }
        if ($user->getAvatar() !== '') {
            return $user->displayAvatar();
        }

        return 'bundles/kiboko_socialsocialnetwork/images/avatar.png';
    }
}
