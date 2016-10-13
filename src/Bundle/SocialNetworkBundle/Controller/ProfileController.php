<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Bundle\SocialNetworkBundle\Controller;

use FOS\UserBundle\Controller\ProfileController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Controller profile pages.
 *
 * @author Vincent Guerard <v.guerard@fulgurio.net>
 */
class ProfileController extends Controller
{
    /**
     * Show page action.
     */
    public function showAction($userId = null)
    {
        if (!$this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new RedirectResponse($this->container->get('router')->generate('fos_user_security_login'));
        }
        $currentUser = $this->getUser();
        if (is_null($userId)) {
            $userToDisplay = $currentUser;
            $areFriend = false;
            $havePendingInvit = false;
        } else {
            $doctrine = $this->container->get('doctrine');
            $userToDisplay = $doctrine->getRepository('KibokoSocialNetworkBundle:User')->find($userId);
            if ($currentUser !== $userToDisplay && ($userToDisplay->hasRole('ROLE_ADMIN') || $userToDisplay->hasRole('ROLE_SUPER_ADMIN'))) {
                throw new NotFoundHttpException();
            }
            $areFriend = $doctrine->getRepository('KibokoSocialNetworkBundle:UserFriendship')->areFriends($currentUser, $userToDisplay);
            $havePendingInvit = ($areFriend === false) ? $doctrine->getRepository('KibokoSocialNetworkBundle:UserFriendship')->havePendingInvitation($currentUser, $userToDisplay) : false;
        }

        return $this->container->get('templating')->renderResponse(
                'KibokoSocialNetworkBundle:Profile:show.html.twig',
                [
                    'user' => $userToDisplay,
                    'areFriend' => $areFriend,
                    'havePendingInvit' => $havePendingInvit,
                ]
        );
    }

    /**
     * Unsubscribe action.
     */
    public function unsubscribeAction()
    {
        $currentUser = $this->getUser();
        if (!$currentUser) {
            throw new AccessDeniedException();
        }
        $request = $this->container->get('request');
        if ($request->get('confirm')) {
            if ($request->get('confirm') === 'yes') {
                $userManager = $this->container->get('fos_user.user_manager');
                $userManager->deleteUser($currentUser);

                return new RedirectResponse($this->container->get('router')->generate('fos_user_security_logout'));
            }
            if ($request->get('referer')) {
                return $this->redirect($request->get('referer'));
            }

            return new RedirectResponse($this->container->get('router')->generate('fos_user_profile_show'));
        }
        $templateName = 'KibokoSocialNetworkBundle::confirm'.($request->isXmlHttpRequest() ? 'Ajax' : '').'.html.twig';

        return $this->container->get('templating')->renderResponse(
                $templateName,
                [
                    'url_referer' => $request->server->get('HTTP_REFERER'),
                    'action' => $this->container->get('router')->generate('kiboko_social_network_unsubscribe'),
                    'confirmationMessage' => $this->container->get('translator')->trans(
                            'kiboko_social.socialnetwork.profile.unsubscribe.confirm'
                    ),
                ]
        );
    }

    /**
     * Get current user.
     *
     * @return type
     */
    private function getUser()
    {
        return $this->container->get('security.context')->getToken()->getUser();
    }
}
