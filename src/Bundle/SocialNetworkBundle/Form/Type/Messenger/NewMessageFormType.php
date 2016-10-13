<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Bundle\SocialNetworkBundle\Form\Type\Messenger;

use Kiboko\Bundle\SocialNetworkBundle\Entity\MessageTarget;
use Kiboko\Bundle\SocialNetworkBundle\Entity\User;
use Symfony\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackValidator;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

class NewMessageFormType extends AbstractType
{
    /**
     * @var Kiboko\Bundle\SocialNetworkBundle\Entity\User
     */
    private $currentUser;

    /**
     * @var Symfony\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;

    /**
     * Constructor.
     *
     * @param User     $currentUser
     * @param Registry $doctrine
     */
    public function __construct(User $currentUser, Registry $doctrine)
    {
        $this->currentUser = $currentUser;
        $this->doctrine = $doctrine;
    }

    /**
     * (non-PHPdoc).
     *
     * @see Symfony\Component\Form\FormTypeInterface::buildForm()
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('username_target', 'text', [
                'required' => false,
                'property_path' => false,
            ])
            ->add('id_targets', 'choice', [
                'multiple' => true,
                'property_path' => false,
            ])
            ->add('subject', 'text')
            ->add('content', 'text')
            ->add('file', 'file', ['required' => false])
            ->addValidator(new CallbackValidator([$this, 'checkTarget']))
            ;
    }

    /**
     * Check targets value.
     *
     * @param FormInterface $form
     */
    public function checkTarget(FormInterface $form)
    {
        $userRepository = $this->doctrine
                ->getRepository('KibokoSocialNetworkBundle:User');
        $idTargets = $form->get('id_targets');
        $usersId = (count($idTargets->getData()) > 0) ? $idTargets->getData() : [];
        $usernameTarget = $form->get('username_target');
        if (trim($usernameTarget->getData()) !== '') {
            $usernames = preg_split('/[;,]/', strtolower($usernameTarget->getData()));
            $users = $userRepository->findBy(['username' => $usernames]);
            foreach ($users as $user) {
                if (!in_array($user->getId(), $usersId, true)) {
                    $usersId[] = $user->getId();
                }
            }
        }
        if (!empty($usersId)) {
            // Filter to get only friends
            $friends = $this->getOnlyFriends($usersId);
            $message = $form->getData();
            foreach ($friends as $friend) {
                $target = new MessageTarget();
                $target->setMessage($message);
                $target->setTarget($friend);
                $this->doctrine->getEntityManager()->persist($target);
            }
            $message->addMessageTarget($target);

            return;
        }
        $usernameTarget->addError(new FormError('kiboko_social.socialnetwork.new_message.no_friend_found'));
    }

    /**
     * Get friends from username typed value.
     *
     * @param array $usersId
     *
     * @return array
     */
    private function getOnlyFriends($usersId)
    {
        $myFriends = $this->doctrine
                ->getRepository('KibokoSocialNetworkBundle:UserFriendship')
                ->findAcceptedFriends($this->currentUser);
        $foundedFriends = [];
        if (!empty($myFriends)) {
            foreach ($myFriends as $myFriend) {
                foreach ($usersId as $id) {
                    if ($id === $myFriend['id']) {
                        $friend = $this->doctrine
                                ->getRepository('KibokoSocialNetworkBundle:User')
                                ->findOneById($myFriend['id']);
                        $foundedFriends[] = $friend;
                    }
                }
            }
        }

        return $foundedFriends;
    }

    /**
     * (non-PHPdoc).
     *
     * @see Symfony\Component\Form\FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'message';
    }
}
