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

/**
 * User avatar function for Twig.
 *
 * @author Vincent Guerard <v.guerard@fulgurio.net>
 */
class AvatarExtension extends \Twig_Extension
{
    /**
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * (non-PHPdoc).
     *
     * @see Twig_Extension::initRuntime()
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * Init Twig functions.
     */
    public function getFunctions()
    {
        return [
            'avatar' => new \Twig_Function_Method($this, 'getAvatar', ['is_safe' => ['html']]),
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

        return $this->environment->getExtension('assets')->getAssetUrl('bundles/kiboko_socialsocialnetwork/images/avatar.png');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'avatar';
    }
}
