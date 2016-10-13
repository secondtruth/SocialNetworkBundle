<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Bundle\SocialNetworkBundle\Controller;

use FOS\UserBundle\Controller\RegistrationController as Controller;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Controller registration page.
 *
 * @author Vincent Guerard <v.guerard@fulgurio.net>
 */
class RegistrationController extends Controller
{
    /**
     * @see FOS\UserBundle\Controller\RegistrationController::confirmedAction()
     */
    public function confirmedAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        // We set an notice flash and an email
        $this->container->get('kiboko_social_network.fos_mailer')->sendRegistrationEmailMessage($user);
        $this->container->get('session')->setFlash('notice', 'kiboko_social.socialnetwork.register.welcome_msg');
        // We redirect to homepage
        return new RedirectResponse($this->container->get('router')->generate('kiboko_social_network_homepage'));
    }
}
