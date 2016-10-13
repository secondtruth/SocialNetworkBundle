<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Bundle\SocialNetworkBundle\Controller;

use FOS\UserBundle\Controller\ResettingController as Controller;
use FOS\UserBundle\Model\UserInterface;

/**
 * Controller resetting pages.
 *
 * @author Vincent Guerard <v.guerard@fulgurio.net>
 */
class ResettingController extends Controller
{
    /**
     * (non-PHPdoc).
     *
     * @see \FOS\UserBundle\Controller\ResettingController:getRedirectionUrl()
     */
    protected function getRedirectionUrl(UserInterface $user)
    {
        return $this->container->get('router')->generate('kiboko_social_network_homepage');
    }
}
