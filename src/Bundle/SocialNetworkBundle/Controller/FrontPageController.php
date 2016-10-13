<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Bundle\SocialNetworkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Controller usual pages.
 *
 * @author Vincent Guerard <v.guerard@fulgurio.net>
 */
class FrontPageController extends Controller
{
    /**
     * Homepage.
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function homepageAction()
    {
        return $this->render('KibokoSocialNetworkBundle:FrontPage:homepage.html.twig', []);
    }
}
