<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Bundle\SocialNetworkBundle\Controller;

use Kiboko\Bundle\SocialNetworkBundle\Entity\User;
use Kiboko\Bundle\SocialNetworkBundle\Entity\UserFriendship;
use Kiboko\Bundle\SocialNetworkBundle\Repository\UserFriendshipRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Friendship controller.
 *
 * @author Vincent Guerard <v.guerard@fulgurio.net>
 */
class FriendshipController extends Controller
{
    /**
     * Friend user list page.
     */
    public function listAction()
    {
        $request = $this->get('request');
        $currentUser = $this->getUser();
        $page = $request->query->get('page', 1);

        /** @var UserFriendshipRepository $friendshipRepository */
        $friendshipRepository = $this->getDoctrine()->getRepository('KibokoSocialNetworkBundle:UserFriendship');

        return $this->render('KibokoSocialNetworkBundle:Friendship:list.html.twig',
            [
                'friendsAsking' => $friendshipRepository->findAskingFriends($currentUser),
                'friends' => $friendshipRepository->findAcceptedAndPendingFriends($currentUser, $page, $this->get('knp_paginator')),
            ]
        );
    }

    /**
     * Search to add new friend action.
     */
    public function searchToAddAction()
    {
        $request = $this->get('request');
        $pendingFriendshipsIDs = [];
        $users = null;
        $searchValue = $request->get('search');
        if (trim($searchValue) !== '') {
            $currentUser = $this->getUser();
            $userRepository = $this->getDoctrine()->getRepository('KibokoSocialNetworkBundle:User');
            $friendshipRepository = $this->getDoctrine()->getRepository('KibokoSocialNetworkBundle:UserFriendship');
            $excludeIDs = [$currentUser->getId()];
            $friendships = $friendshipRepository->findAcceptedAndRefusedFriends($currentUser);
            foreach ($friendships as $friendship) {
                $excludeIDs[] = $friendship['id'];
            }
            $users = $userRepository->findOnlyInEnabledSubscribers($searchValue, $excludeIDs);
            $pendingFriendships = $friendshipRepository->findPendingFriends($currentUser);
            foreach ($pendingFriendships as $pendingFriendship) {
                $id = $pendingFriendship['id'];
                $pendingFriendshipsIDs[$id] = $id;
            }
        }

        return $this->render('KibokoSocialNetworkBundle:Friendship:add.html.twig',
            [
                'searchValue' => $searchValue,
                'users' => $users,
                'pendingFriendshipsIDs' => $pendingFriendshipsIDs,
            ]
        );
    }

    /**
     * Friend user add page.
     */
    public function addAction(Request $request)
    {
        if ($selectedFriends = $request->get('friends_id')) {
            $currentUser = $this->getUser();
            $userRepository = $this->getDoctrine()->getRepository('KibokoSocialNetworkBundle:User');
            $friendshipRepository = $this->getDoctrine()->getRepository('KibokoSocialNetworkBundle:UserFriendship');
            $em = $this->getDoctrine()->getEntityManager();
            foreach ($selectedFriends as $selectedFriendId) {
                $mayBeFriend = $userRepository->findOneById($selectedFriendId);
                if ($usersFriendship = $friendshipRepository->findByUserAndFriendUser($currentUser, $mayBeFriend)) {
                    if ($usersFriendship[0]->getUserSrc() === $currentUser) {
                        if ($usersFriendship[0]->getNbRefusals() >= $this->container->getParameter('kiboko_social_network.friendship.nb_refusals')) {
                            continue;
                        }
                        $friendship = $usersFriendship[0];
                        $friendship2 = $usersFriendship[1];
                    } else {
                        if ($usersFriendship[1]->getNbRefusals() >= $this->container->getParameter('kiboko_social_network.friendship.nb_refusals')) {
                            continue;
                        }
                        $friendship = $usersFriendship[1];
                        $friendship2 = $usersFriendship[0];
                    }
                } else {
                    $friendship = new UserFriendship();
                    $friendship->setUserSrc($currentUser);
                    $friendship->setUserTgt($mayBeFriend);
                    $friendship2 = new UserFriendship();
                    $friendship2->setUserSrc($mayBeFriend);
                    $friendship2->setUserTgt($currentUser);
                }
                $friendship->setStatus(UserFriendship::PENDING_STATUS);
                $friendship2->setStatus(UserFriendship::ASKING_STATUS);
                $em->persist($friendship);
                $em->persist($friendship2);
                $this->get('kiboko_social_network.friendship_mailer')->sendInvitMessage($mayBeFriend);
            }
            $em->persist($currentUser);
            $em->flush();
            $this->get('session')->setFlash('notice',
                    $this->get('translator')->trans(
                            'kiboko_social.socialnetwork.invitation.success_msg',
                            [],
                            'friendship'
            ));
        }

        return $this->redirect($this->generateUrl('kiboko_social_network_friendship_list'));
    }

    /**
     * Friend user invit page.
     */
    public function invitAction($userId)
    {
        $request = $this->get('request');
        $em = $this->getDoctrine()->getEntityManager();
        $currentUser = $this->getUser();
        $user = $this->getDoctrine()->getRepository('KibokoSocialNetworkBundle:User')->find($userId);
        if (!$user->hasRole('ROLE_ADMIN')
          && !$user->hasRole('ROLE_SUPER_ADMIN')
          && !$user->hasRole('ROLE_GHOST')
          && !$this->getDoctrine()->getRepository('KibokoSocialNetworkBundle:UserFriendship')->areFriends($currentUser, $user)
        ) {
            $friendship = new UserFriendship();
            $friendship->setUserSrc($currentUser);
            $friendship->setUserTgt($user);
            $friendship->setStatus(UserFriendship::PENDING_STATUS);
            $em->persist($friendship);
            $friendship2 = new UserFriendship();
            $friendship2->setUserSrc($user);
            $friendship2->setUserTgt($currentUser);
            $friendship2->setStatus(UserFriendship::ASKING_STATUS);
            $em->persist($friendship2);
            $em->flush();
        }
        if ($request->headers->get('referer') !== '') {
            return $this->redirect($request->headers->get('referer'));
        }

        return $this->redirect($this->generateUrl('kiboko_social_network_friendship_list'));
    }

    /**
     * Accept invitation action.
     *
     * @param int $userId
     *
     * @throws NotFoundHttpException
     */
    public function acceptAction($userId)
    {
        $currentUser = $this->getUser();
        $userRepository = $this->getDoctrine()->getRepository('KibokoSocialNetworkBundle:User');
        if (!$user = $userRepository->find($userId)) {
            throw new NotFoundHttpException();
        }
        $friendshipRepository = $this->getDoctrine()->getRepository('KibokoSocialNetworkBundle:UserFriendship');
        if (!$usersFriendship = $friendshipRepository->findByUserAndFriendUser($currentUser, $user)) {
            throw new NotFoundHttpException();
        }
        $em = $this->getDoctrine()->getEntityManager();
        foreach ($usersFriendship as $userFriendship) {
            $userFriendship->setNbRefusals(0);
            $userFriendship->setStatus(UserFriendship::ACCEPTED_STATUS);
            $em->persist($userFriendship);
        }
        $em->flush();

        $this->get('kiboko_social_network.friendship_mailer')->sendAcceptMessage($user);
        $this->get('session')->setFlash('notice',
                    $this->get('translator')->trans(
                            'kiboko_social.socialnetwork.add.accepted_msg',
                            ['%username%' => $user->getUsername()],
                            'friendship'
            ));

        return $this->redirect($this->generateUrl('kiboko_social_network_friendship_list'));
    }

    /**
     * Refuse invitation action.
     *
     * @param int $userId
     *
     * @throws NotFoundHttpException
     *
     * @todo Send an email
     */
    public function refuseAction($userId)
    {
        $request = $this->get('request');
        $currentUser = $this->getUser();
        $userRepository = $this->getDoctrine()->getRepository('KibokoSocialNetworkBundle:User');
        if (!$user = $userRepository->find($userId)) {
            throw new NotFoundHttpException();
        }
        $friendshipRepository = $this->getDoctrine()->getRepository('KibokoSocialNetworkBundle:UserFriendship');
        if (!$usersFriendship = $friendshipRepository->findByUserAndFriendUser($currentUser, $user)) {
            throw new NotFoundHttpException();
        }
        $hasAcceptedBefore = false;
        $em = $this->getDoctrine()->getEntityManager();
        foreach ($usersFriendship as $userFriendship) {
            if ($userFriendship->getStatus() === UserFriendship::ACCEPTED_STATUS) {
                $hasAcceptedBefore = true;
            }
            $userFriendship->setStatus(UserFriendship::REFUSED_STATUS);
            if ($userFriendship->getUserTgt() === $currentUser) {
                $nbRefusals = $userFriendship->getNbRefusals();
                if ($nbRefusals >= $this->container->getParameter('kiboko_social_network.friendship.nb_refusals')) {
                    $userFriendship->setStatus(UserFriendship::REMOVED_STATUS);
                } else {
                    $userFriendship->setNbRefusals($userFriendship->getNbRefusals() + 1);
                }
            }
            $em->persist($userFriendship);
        }
        if ($request->get('confirm') === 'yes') {
            if ($hasAcceptedBefore) {
                $message = 'kiboko_social.socialnetwork.add.remove_msg';
                $this->get('kiboko_social_network.friendship_mailer')->sendRemoveInvitMessage($user);
            } else {
                $message = 'kiboko_social.socialnetwork.add.refused_msg';
                $this->get('kiboko_social_network.friendship_mailer')->sendRefusalMessage($user);
            }
            $em->flush();
            $this->get('session')->setFlash('notice',
                    $this->get('translator')->trans(
                            $message,
                            ['%username%' => $user->getUsername()],
                            'friendship'
            ));

            return $this->redirect($this->generateUrl('kiboko_social_network_friendship_list'));
        } elseif ($request->get('confirm') === 'no') {
            return $this->redirect($this->generateUrl('kiboko_social_network_friendship_list'));
        }
        $templateName = 'KibokoSocialNetworkBundle::confirm'.($request->isXmlHttpRequest() ? 'Ajax' : '').'.html.twig';

        return $this->render($templateName, [
            'action' => $this->generateUrl('kiboko_social_network_friendship_refuse', ['userId' => $userId]),
            'confirmationMessage' => $this->get('translator')->trans(
                    $hasAcceptedBefore ? 'kiboko_social.socialnetwork.add.confirm_remove_msg' : 'kiboko_social.socialnetwork.add.confirm_refuse_msg',
                    [],
                    'friendship'
            ),
        ]);
    }

    /**
     * Search friend with ajax call.
     *
     * @param Request $request
     *
     * @throws AccessDeniedException
     */
    public function searchAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $foundedFriends = $this->getDoctrine()
                    ->getRepository('KibokoSocialNetworkBundle:UserFriendship')
                    ->searchFriend(
                            $this->getUser(),
                            $request->get('q')
            );
            foreach ($foundedFriends as &$friend) {
                $friend['avatar'] = User::getAvatarUrl($friend);
            }
            $response = new Response(json_encode(
                    ['friends' => $foundedFriends]));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }
        throw new AccessDeniedException();
    }

    /**
     * Get current user.
     *
     * @return User
     */
    protected function getUser()
    {
        return $this->get('security.context')->getToken()->getUser();
    }
}
