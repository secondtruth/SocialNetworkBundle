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
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller admin pages.
 *
 * @author Vincent Guerard <v.guerard@fulgurio.net>
 */
class AdminController extends AbstractController
{
    /**
     * Index page action.
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('KibokoSocialNetworkBundle:Admin:index.html.twig');
    }
}
