<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Bundle\SocialNetworkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller usual pages.
 *
 * @author Vincent Guerard <v.guerard@fulgurio.net>
 */
class FrontPageController extends AbstractController
{
    public function homepageAction()
    {
        return $this->render('@KibokoSocialNetwork/FrontPage/homepage.html.twig', []);
    }
}
