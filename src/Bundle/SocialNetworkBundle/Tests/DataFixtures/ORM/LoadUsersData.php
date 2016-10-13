<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Bundle\SocialNetworkBundle\Tests\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Kiboko\Bundle\SocialNetworkBundle\Entity\User;
use Kiboko\Bundle\SocialNetworkBundle\Entity\UserFriendship;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Users data fixtures for tests.
 *
 * @author Vincent GUERARD <v.guerard@fulgurio.net>
 */
class LoadUsersData implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('fos_user.user_manager');

        $userDisabled = $this->createUser('userDisabled', null, false);

        $user1 = $this->createUser('user1');
        $user2 = $this->createUser('user2');
        $user2->setAvatar('icon.png');
        $userManager->updateUser($user2);

        $user3 = $this->createUser('user3');
        $user4 = $this->createUser('user4');
        $user5 = $this->createUser('user5');

        $this->createFriendship($user1, $user3, UserFriendship::ACCEPTED_STATUS);
        $this->createFriendship($user4, $user1, UserFriendship::ASKING_STATUS);

        $user10 = $this->createUser('user10');
        $this->createFriendship($user1, $user10, UserFriendship::ACCEPTED_STATUS);
        $user11 = $this->createUser('user11');
        $this->createFriendship($user1, $user11, UserFriendship::ACCEPTED_STATUS);
        $user12 = $this->createUser('user12');
        $this->createFriendship($user1, $user12, UserFriendship::ACCEPTED_STATUS);
        $user13 = $this->createUser('user13');
        $this->createFriendship($user1, $user13, UserFriendship::ACCEPTED_STATUS);
        $user14 = $this->createUser('user14');
        $this->createFriendship($user1, $user14, UserFriendship::ACCEPTED_STATUS);

        $admin = $this->createUser('admin', ['ROLE_ADMIN']);

        $superadmin = $this->createUser('superadmin', ['ROLE_SUPER_ADMIN']);
    }

    /**
     * Create a user.
     *
     * @param string $username
     * @param array  $roles
     * @param bool   $enabled
     *
     * @return User
     */
    private function createUser($username, array $roles = null, $enabled = true)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $user->setUsername($username);
        $user->setPlainPassword($username);
        $user->setEmail($username.'@example.com');
        $user->setEnabled($enabled);
        if ($roles) {
            foreach ($roles as $role) {
                $user->addRole($role);
            }
        }
        $userManager->updateUser($user);

        return $user;
    }

    /**
     * Create a friendship between two users.
     *
     * @param User   $userSource
     * @param User   $userTarget
     * @param string $status
     */
    private function createFriendship(User $userSource, User $userTarget, $status)
    {
        $em = $this->container->get('doctrine')->getEntityManager();
        if ($status === UserFriendship::ACCEPTED_STATUS) {
            $status1 = UserFriendship::ACCEPTED_STATUS;
            $status2 = UserFriendship::ACCEPTED_STATUS;
        } elseif ($status === UserFriendship::ASKING_STATUS) {
            $status1 = UserFriendship::PENDING_STATUS;
            $status2 = UserFriendship::ASKING_STATUS;
        }
        $friendship1 = new UserFriendship();
        $friendship1->setUserSrc($userSource);
        $friendship1->setUserTgt($userTarget);
        $friendship1->setStatus($status1);

        $friendship2 = new UserFriendship();
        $friendship2->setUserSrc($userTarget);
        $friendship2->setUserTgt($userSource);
        $friendship2->setStatus($status2);
        $em->persist($friendship1);
        $em->persist($friendship2);
        $em->flush();
    }
}
