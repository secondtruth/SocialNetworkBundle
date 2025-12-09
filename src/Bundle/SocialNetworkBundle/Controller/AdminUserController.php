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
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Kiboko\Bundle\SocialNetworkBundle\Entity\User;
use Kiboko\Bundle\SocialNetworkBundle\Form\Handler\AdminAccountFormHandler;
use Kiboko\Bundle\SocialNetworkBundle\Form\Handler\AdminContactFormHandler;
use Kiboko\Bundle\SocialNetworkBundle\Form\Type\AdminAccountFormType;
use Kiboko\Bundle\SocialNetworkBundle\Form\Type\AdminContactFormType;
use Kiboko\Bundle\SocialNetworkBundle\Mailer\AvatarMailer;
use Kiboko\Bundle\SocialNetworkBundle\Mailer\ContactMailer;
use Kiboko\Bundle\SocialNetworkBundle\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Users admin controller.
 *
 * @author Vincent Guerard <v.guerard@fulgurio.net>
 */
class AdminUserController extends AbstractController
{
    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private PaginatorInterface $paginator,
        private UserManagerInterface $userManager,
        private MailerInterface $fosMailer,
        private ContactMailer $contactMailer,
        private AvatarMailer $avatarMailer,
        private ManagerRegistry $doctrine,
        private ContainerInterface $container
    ) {
    }
    /**
     * Users listing action.
     *
     * @return Response
     */
    public function listAction(Request $request)
    {
        $search = trim($request->get('s', ''));
        $page = $request->get('page', 1);

        /** @var UserRepository $repository */
        $repository = $this->doctrine
            ->getRepository('KibokoSocialNetworkBundle:User');

        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            $users = $repository->findWithPagination(
                $this->paginator,
                $page,
                $search
            );
        } else {
            $users = $repository->findOnlySubscribers(
                $this->paginator,
                $page,
                $search
            );
        }
        if (count($users) === 0 && $page > 1) {
            return $this->redirect(
                $this->generateUrl(
                    'kiboko_social_network_admin_users',
                    [
                        'page' => $page - 1,
                    ]
                )
            );
        }

        return $this->render(
            'KibokoSocialNetworkBundle:AdminUsers:list.html.twig',
            [
                'users' => $users,
                'searchQuery' => $search,
            ]
        );
    }

    /**
     * Users view action.
     *
     * @param number $userId
     *
     * @return Response
     */
    public function viewAction($userId)
    {
        $user = $this->getSpecifiedUser($userId);

        return $this->render(
            'KibokoSocialNetworkBundle:AdminUsers:view.html.twig',
            [
                'user' => $user,
            ]
        );
    }

    /**
     * Add or edit user action.
     *
     * @param Request $request
     * @param int     $userId
     *
     * @throws AccessDeniedHttpException
     *
     * @return Response
     */
    public function addAction(Request $request, $userId = null)
    {
        if (!$this->security->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedHttpException();
        }

        $user = $userId === null ? new User() : $this->getSpecifiedUser($userId);
        $form = $this->createForm(new AdminAccountFormType($this->container), $user);
        $formHandler = new AdminAccountFormHandler(
            $this->userManager,
            $form,
            $request
        );

        if ($formHandler->process($user)) {
            $this->addFlash('notice',
                $this->translator
                    ->trans('kiboko_social.socialnetwork.'.($userId === null ? 'add' : 'edit').'.success', [], 'admin_user')
            );

            return new RedirectResponse($this->generateUrl('kiboko_social_network_admin_users'));
        }

        return $this->render('KibokoSocialNetworkBundle:AdminUsers:add.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * User remove action.
     *
     * @param Request $request
     * @param int     $userId
     *
     * @return Response
     */
    public function removeAction(Request $request, $userId)
    {
        if (!$this->security->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedHttpException();
        }

        $user = $this->getSpecifiedUser($userId);
        if ($request->get('confirm')) {
            if ($request->get('confirm') === 'yes') {
                $this->userManager->deleteUser($user);
                $this->addFlash(
                    'success',
                    $this->translator->trans(
                        'kiboko_social.socialnetwork.remove.success',
                        [
                            '%username%' => $user->getUsername(),
                        ],
                        'admin_user'
                    )
                );
            }

            if ($request->get('referer')) {
                return $this->redirect($request->get('referer'));
            }

            return $this->redirect($this->generateUrl('kiboko_social_network_admin_users'));
        }
        $templateName = 'KibokoSocialNetworkBundle:Admin:confirm'.($request->isXmlHttpRequest() ? 'Ajax' : '').'.html.twig';

        return $this->render(
            $templateName,
            [
                'url_referer' => $request->server->get('HTTP_REFERER'),
                'action' => $this->generateUrl('kiboko_social_network_admin_users_remove', ['userId' => $userId]),
                'confirmationMessage' => $this->translator->trans(
                    'kiboko_social.socialnetwork.remove.confirm',
                    [
                        '%username%' => $user->getUsername(),
                    ],
                    'admin_user'
                ),
            ]
        );
    }

    /**
     * Users ban or unban action.
     *
     * @param Request $request
     * @param int     $userId
     *
     * @return Response
     */
    public function banAction(Request $request, $userId)
    {
        $user = $this->getSpecifiedUser($userId);
        $isEnabled = $user->isEnabled();
        if ($request->get('confirm')) {
            if ($request->get('confirm') === 'yes') {
                $user->setEnabled(!$user->isEnabled());
                $em = $this->doctrine->getManager();
                $em->persist($user);
                $em->flush();
                $this->addFlash(
                    'success',
                    $this->translator->trans(
                        'kiboko_social.socialnetwork.'.($isEnabled ? 'ban' : 'unban').'.success',
                        ['%username%' => $user->getUsername()],
                        'admin_user'
                    )
                );
            }
            if ($request->get('referer')) {
                return $this->redirect($request->get('referer'));
            }

            return $this->redirect($this->generateUrl('kiboko_social_network_admin_users'));
        }

        $templateName = 'KibokoSocialNetworkBundle:Admin:confirm'.($request->isXmlHttpRequest() ? 'Ajax' : '').'.html.twig';

        return $this->render(
            $templateName,
            [
                'url_referer' => $request->server->get('HTTP_REFERER'),
                'action' => $this->generateUrl('kiboko_social_network_admin_users_'.($isEnabled ? 'ban' : 'unban'), ['userId' => $userId]),
                'confirmationMessage' => $this->translator->trans(
                    'kiboko_social.socialnetwork.'.($isEnabled ? 'ban' : 'unban').'.confirm',
                    ['%username%' => $user->getUsername()],
                    'admin_user'
                ),
            ]
        );
    }

    /**
     * User contact action.
     *
     * @param Request $request
     * @param int     $userId
     *
     * @return Response
     */
    public function contactAction(Request $request, $userId)
    {
        $user = $this->getSpecifiedUser($userId);
        $form = $this->createForm(new AdminContactFormType());
        $formHandler = new AdminContactFormHandler(
            $this->contactMailer,
            $form,
            $request
        );
        if ($formHandler->process($user)) {
            $this->addFlash(
                'notice',
                $this->translator->trans(
                    'kiboko_social.socialnetwork.contact.success',
                    [],
                    'admin_user'
                )
            );

            return $this->redirect($this->generateUrl('kiboko_social_network_admin_users'));
        }

        return $this->render('KibokoSocialNetworkBundle:AdminUsers:contact.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Init user password.
     *
     * @param number $userId
     *
     * @return Response
     */
    public function initPasswordAction($userId)
    {
        $user = $this->getSpecifiedUser($userId);
        $user->generateConfirmationToken();
        $this->fosMailer->sendResettingEmailMessage($user);
        $user->setPasswordRequestedAt(new \DateTime());
        $this->userManager->updateUser($user);
        $this->addFlash(
            'success',
            $this->translator->trans(
                'kiboko_social.socialnetwork.password_init.success',
                ['%email%' => $user->getEmail()],
                'admin_user'
            )
        );

        return $this->redirect($this->generateUrl('kiboko_social_network_admin_user'));
    }

    /**
     * Remove user avatar.
     *
     * @param Request $request
     * @param int     $userId
     *
     * @todo : XmlRequest ?
     * @todo : back to initial user page (with pagination)
     */
    public function removeAvatarAction(Request $request, $userId)
    {
        $user = $this->getSpecifiedUser($userId);
        if ($user->getAvatar() === null) {
            throw new AccessDeniedHttpException();
        }
        if ($request->get('confirm') === 'yes') {
            //@todo: remove file ?
            $user->setAvatar(null);
            $em = $this->doctrine->getManager();
            $em->persist($user);
            $em->flush();
            $this->avatarMailer->sendAdminMessage($user);
            $this->addFlash(
                'success',
                $this->translator->trans(
                    'kiboko_social.socialnetwork.remove_avatar.success',
                    ['%username%' => $user->getUsername()],
                    'admin_user'
                )
            );

            return $this->redirect($this->generateUrl('kiboko_social_network_admin_users'));
        } elseif ($request->get('confirm') === 'no') {
            return $this->redirect($this->generateUrl('kiboko_social_network_admin_users'));
        }

        return $this->render(
            'KibokoSocialNetworkBundle:Admin:confirm.html.twig',
            [
                'action' => $this->generateUrl('kiboko_social_network_admin_users_remove_avatar', ['userId' => $userId]),
                'confirmationMessage' => $this->translator->trans(
                    'kiboko_social.socialnetwork.remove_avatar.confirm',
                    ['%username%' => $user->getUsername()],
                    'admin_user'
                ),
            ]
        );
    }

    /**
     * Get user from given ID, and ckeck if he exists.
     *
     *
     * @param number $userId
     *
     * @throws NotFoundHttpException
     *
     * @return User
     */
    private function getSpecifiedUser($userId)
    {
        if (!$user = $this->doctrine->getRepository('KibokoSocialNetworkBundle:User')->find($userId)) {
            throw new NotFoundHttpException(
                $this->translator->trans('kiboko_social.socialnetwork.user_not_found', [], 'admin_user')
            );
        }

        return $user;
    }
}
