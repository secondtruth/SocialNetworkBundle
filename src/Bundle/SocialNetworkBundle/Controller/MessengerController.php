<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Bundle\SocialNetworkBundle\Controller;

use Kiboko\Bundle\SocialNetworkBundle\Entity\Message;
use Kiboko\Bundle\SocialNetworkBundle\Form\Handler\Messenger\AnswerMessageFormHandler;
use Kiboko\Bundle\SocialNetworkBundle\Form\Handler\Messenger\NewMessageFormHandler;
use Kiboko\Bundle\SocialNetworkBundle\Form\Type\Messenger\AnswerMessageFormType;
use Kiboko\Bundle\SocialNetworkBundle\Form\Type\Messenger\NewMessageFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MessengerController extends Controller
{
    /**
     * Messenger list page.
     *
     * @return Response
     */
    public function listAction()
    {
        return $this->render(
                'KibokoSocialNetworkBundle:Messenger:list.html.twig',
                [
                    'messages' => $this->getMessagesList(),
                ]
        );
    }

    /**
     * New message page.
     *
     * @param number $userId
     *
     * @return Response
     */
    public function newAction(Request $request, $userId = null)
    {
        $request = $this->get('request');
        $currentUser = $this->getUser();
        $message = new Message();

        $selectedUsers = [];
        if (!is_null($userId)) {
            $selectedUsers = $this->getDoctrine()->getRepository('KibokoSocialNetworkBundle:User')->findBy(['id' => $userId]);
        } elseif ($selectedUsers = $request->get('users')) {
            $selectedUsers = $this->getDoctrine()->getRepository('KibokoSocialNetworkBundle:User')->findBy(['id' => $selectedUsers]);
        }
        $form = $this->createForm(new NewMessageFormType($currentUser, $this->getDoctrine()), $message);
        $formHandler = new NewMessageFormHandler(
                $form,
                $request,
                $this->getDoctrine(),
                $this->container->get('kiboko_social_network.messenger_mailer')
        );
        if ($formHandler->process($currentUser)) {
            $this->get('session')->setFlash(
                    'success',
                    $this->get('translator')->trans(
                            'kiboko_social.socialnetwork.new_message.success_msg',
                            [],
                            'messenger')
            );

            return $this->redirect($this->generateUrl('kiboko_social_network_messenger_list'));
        }

        return $this->render('KibokoSocialNetworkBundle:Messenger:new.html.twig', [
            'form' => $form->createView(),
            'selectedUsers' => $selectedUsers,
        ]);
    }

    /**
     * Messenger reply page.
     *
     * @param number $msgId
     *
     * @return Response
     */
    public function showAction(Request $request, $msgId)
    {
        $currentUser = $this->getUser();
        $message = $this->getMessage($msgId, true);
        $data = ['message' => $message];
        $messageRepository = $this->getDoctrine()
                ->getRepository('KibokoSocialNetworkBundle:Message');
        $data['participants'] = $messageRepository->findParticipants($message);
        if ($message->getAllowAnswer()) {
            $answer = new Message();
            $answer->setSubject('###RESPONSE###');
            $form = $this->createForm(new AnswerMessageFormType(), $answer);
            $formHandler = new AnswerMessageFormHandler(
                    $form,
                    $request,
                    $this->getDoctrine(),
                    $this->container->get('kiboko_social_network.messenger_mailer')
            );
            if ($formHandler->process($message, $currentUser, $data['participants'])) {
                $this->get('session')->setFlash(
                        'success',
                        $this->get('translator')->trans(
                                'kiboko_social.socialnetwork.answer_message.success_msg',
                                [],
                                'messenger'));

                return $this->redirect(
                        $this->generateUrl(
                                'kiboko_social_network_messenger_show_message',
                                ['msgId' => $msgId])
                );
            }
            $data['form'] = $form->createView();
        }
        $tmpFriends = $this->getDoctrine()
                ->getRepository('KibokoSocialNetworkBundle:UserFriendship')
                ->findAcceptedFriends($currentUser);
        $data['friends'] = [];
        foreach ($tmpFriends as &$tmpFriend) {
            $data['friends'][$tmpFriend['id']] = $tmpFriend;
        }

        return $this->render(
                'KibokoSocialNetworkBundle:Messenger:show.html.twig',
                $data
        );
    }

    /**
     * Messenger remove page.
     *
     * @param number $msgId
     *
     * @return Response
     */
    public function removeAction($msgId)
    {
        $request = $this->container->get('request');
        $currentUser = $this->getUser();
        $message = $this->getMessage($msgId);
        if ($request->request->get('confirm') === 'yes') {
            $messageRepository = $this->getDoctrine()
                    ->getRepository('KibokoSocialNetworkBundle:Message');
            if (count($message->getTarget()) === 1) {
                // If we are the last (or only) user on message conversation,
                // we remove message user links, and the message with answer
                $em = $this->getDoctrine()->getEntityManager();
                $em->remove($message);
                $em->flush();
            } else {
                // If there s some users who don't remove message, we just remove current user link with message
                $messageRepository->removeUserMessageRelation($msgId, $currentUser);
            }
            $this->get('session')->setFlash(
                    'success',
                    $this->get('translator')->trans(
                            'kiboko_social.socialnetwork.remove_message.success_msg',
                            [],
                            'messenger')
                    );

            return $this->redirect($this->generateUrl('kiboko_social_network_messenger_list'));
        } elseif ($request->request->get('confirm') === 'no') {
            return $this->redirect($this->generateUrl('kiboko_social_network_messenger_list'));
        }
        $templateName = 'KibokoSocialNetworkBundle::confirm'.($request->isXmlHttpRequest() ? 'Ajax' : '').'.html.twig';

        return $this->render($templateName, [
            'action' => $this->generateUrl(
                    'kiboko_social_network_messenger_remove_message',
                    ['msgId' => $msgId]
            ),
            'confirmationMessage' => $this->get('translator')->trans(
                    'kiboko_social.socialnetwork.remove_message.confirm_msg',
                    [],
                    'messenger'),
        ]);
    }

    /**
     * Get messages root of current user.
     */
    private function getMessagesList()
    {
        return $this->getDoctrine()
                ->getRepository('KibokoSocialNetworkBundle:Message')
                ->findRootMessages($this->getUser());
    }

    /**
     * Get message and check if current user can see it.
     *
     * @param int  $msgId
     * @param bool $updateHasRead
     *
     * @throws NotFoundHttpException
     *
     * @return Message
     */
    private function getMessage($msgId, $updateHasRead = false)
    {
        $currentUser = $this->getUser();
        $relation = $this->getDoctrine()
                ->getRepository('KibokoSocialNetworkBundle:MessageTarget')
                ->findOneBy([
                    'message' => $msgId,
                    'target' => $currentUser->getId(), ]
        );
        if (!$relation) {
            throw new NotFoundHttpException();
        }
        if ($updateHasRead && $relation->getHasRead() === false) {
            $relation->setHasRead(true);
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($relation);
            $em->flush();
        }

        return $this->getDoctrine()
                ->getRepository('KibokoSocialNetworkBundle:Message')
                ->find($msgId);
    }

    /**
     * Get current user.
     *
     * @return Fulgurio\SocialNetworkBundle\Entity\User
     */
    private function getUser()
    {
        return $this->get('security.context')->getToken()->getUser();
    }
}
