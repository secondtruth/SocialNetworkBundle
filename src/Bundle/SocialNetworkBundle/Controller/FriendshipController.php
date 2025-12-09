<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Bundle\SocialNetworkBundle\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Kiboko\Bundle\SocialNetworkBundle\Entity\User;
use Kiboko\Bundle\SocialNetworkBundle\Entity\UserFriendship;
use Kiboko\Bundle\SocialNetworkBundle\Mailer\FriendshipMailer;
use Kiboko\Bundle\SocialNetworkBundle\Repository\UserFriendshipRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Friendship controller.
 *
 * @author Vincent Guerard <v.guerard@fulgurio.net>
 */
class FriendshipController extends AbstractController
{
    public function __construct(
        private ManagerRegistry $doctrine,
        private PaginatorInterface $paginator,
        private FriendshipMailer $friendshipMailer,
        private TranslatorInterface $translator,
        private int $maxRefusals
    ) {
    }
    /**
     * Friend user list page.
     */
    public function listAction(Request $request)
    {
        $currentUser = $this->getUser();
        $page = $request->query->get('page', 1);

        /** @var UserFriendshipRepository $friendshipRepository */
        $friendshipRepository = $this->doctrine->getRepository('KibokoSocialNetworkBundle:UserFriendship');

        return $this->render('KibokoSocialNetworkBundle:Friendship:list.html.twig',
            [
                'friendsAsking' => $friendshipRepository->findAskingFriends($currentUser),
                'friends' => $friendshipRepository->findAcceptedAndPendingFriends($currentUser, $page, $this->paginator),
            ]
        );
    }

    /**
     * Search to add new friend action.
     */
    public function searchToAddAction(Request $request)
    {
        $pendingFriendshipsIDs = [];
        $users = null;
        $searchValue = $request->get('search');
        if (trim($searchValue) !== '') {
            $currentUser = $this->getUser();
            $userRepository = $this->doctrine->getRepository('KibokoSocialNetworkBundle:User');
            $friendshipRepository = $this->doctrine->getRepository('KibokoSocialNetworkBundle:UserFriendship');
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
            $userRepository = $this->doctrine->getRepository('KibokoSocialNetworkBundle:User');
            $friendshipRepository = $this->doctrine->getRepository('KibokoSocialNetworkBundle:UserFriendship');
            $em = $this->doctrine->getManager();
            foreach ($selectedFriends as $selectedFriendId) {
                $mayBeFriend = $userRepository->findOneById($selectedFriendId);
                if ($usersFriendship = $friendshipRepository->findByUserAndFriendUser($currentUser, $mayBeFriend)) {
                    if ($usersFriendship[0]->getUserSrc() === $currentUser) {
                        if ($usersFriendship[0]->getNbRefusals() >= $this->maxRefusals) {
                            continue;
                        }
                        $friendship = $usersFriendship[0];
                        $friendship2 = $usersFriendship[1];
                    } else {
                        if ($usersFriendship[1]->getNbRefusals() >= $this->maxRefusals) {
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
                $this->friendshipMailer->sendInvitMessage($mayBeFriend);
            }
            $em->persist($currentUser);
            $em->flush();
            $this->addFlash('notice',
                    $this->translator->trans(
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
    public function invitAction(Request $request, $userId)
    {
        $em = $this->doctrine->getManager();
        $currentUser = $this->getUser();
        $user = $this->doctrine->getRepository('KibokoSocialNetworkBundle:User')->find($userId);
        if (!$user->hasRole('ROLE_ADMIN')
          && !$user->hasRole('ROLE_SUPER_ADMIN')
          && !$user->hasRole('ROLE_GHOST')
          && !$this->doctrine->getRepository('KibokoSocialNetworkBundle:UserFriendship')->areFriends($currentUser, $user)
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
        $userRepository = $this->doctrine->getRepository('KibokoSocialNetworkBundle:User');
        if (!$user = $userRepository->find($userId)) {
            throw new NotFoundHttpException();
        }
        $friendshipRepository = $this->doctrine->getRepository('KibokoSocialNetworkBundle:UserFriendship');
        if (!$usersFriendship = $friendshipRepository->findByUserAndFriendUser($currentUser, $user)) {
            throw new NotFoundHttpException();
        }
        $em = $this->doctrine->getManager();
        foreach ($usersFriendship as $userFriendship) {
            $userFriendship->setNbRefusals(0);
            $userFriendship->setStatus(UserFriendship::ACCEPTED_STATUS);
            $em->persist($userFriendship);
        }
        $em->flush();

        $this->friendshipMailer->sendAcceptMessage($user);
        $this->addFlash('notice',
                    $this->translator->trans(
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
    public function refuseAction(Request $request, $userId)
    {
        $currentUser = $this->getUser();
        $userRepository = $this->doctrine->getRepository('KibokoSocialNetworkBundle:User');
        if (!$user = $userRepository->find($userId)) {
            throw new NotFoundHttpException();
        }
        $friendshipRepository = $this->doctrine->getRepository('KibokoSocialNetworkBundle:UserFriendship');
        if (!$usersFriendship = $friendshipRepository->findByUserAndFriendUser($currentUser, $user)) {
            throw new NotFoundHttpException();
        }
        $hasAcceptedBefore = false;
        $em = $this->doctrine->getManager();
        foreach ($usersFriendship as $userFriendship) {
            if ($userFriendship->getStatus() === UserFriendship::ACCEPTED_STATUS) {
                $hasAcceptedBefore = true;
            }
            $userFriendship->setStatus(UserFriendship::REFUSED_STATUS);
            if ($userFriendship->getUserTgt() === $currentUser) {
                $nbRefusals = $userFriendship->getNbRefusals();
                if ($nbRefusals >= $this->maxRefusals) {
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
                $this->friendshipMailer->sendRemoveInvitMessage($user);
            } else {
                $message = 'kiboko_social.socialnetwork.add.refused_msg';
                $this->friendshipMailer->sendRefusalMessage($user);
            }
            $em->flush();
            $this->addFlash('notice',
                    $this->translator->trans(
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
            'confirmationMessage' => $this->translator->trans(
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
            $foundedFriends = $this->doctrine
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
}
