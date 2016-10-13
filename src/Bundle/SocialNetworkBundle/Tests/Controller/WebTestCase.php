<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Bundle\SocialNetworkBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase as BaseWebTestCase;

class WebTestCase extends BaseWebTestCase
{
    /**
     * Test setup.
     */
    public function setUp()
    {
        // add all your doctrine fixtures classes
        $classes = [
            'Kiboko\Bundle\SocialNetworkBundle\Tests\DataFixtures\ORM\LoadUsersData',
        ];
        $this->loadFixtures($classes);
    }

    /**
     * Get a logged client.
     *
     * @param string $userName
     * @param string $userPassword
     *
     * @return Symfony\Bundle\FrameworkBundle\Client
     */
    protected function getUserLoggedClient($userName, $userPassword)
    {
        $data = [
            '_username' => $userName,
            '_password' => $userPassword,
        ];
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->filter('form[action$="login_check"].form-horizontal button[type="submit"]')->form();
        $client->submit($form, $data);

        return $client;
    }

    /**
     * Get a admin logged client.
     *
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    protected function getAdminLoggedClient()
    {
        $data = [
            '_username' => 'admin',
            '_password' => 'admin',
        ];
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->filter('form[action$="login_check"].form-horizontal button[type="submit"]')->form();
        $client->submit($form, $data);

        return $client;
    }

    /**
     * Get a super admin logged client.
     *
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    protected function getSuperAdminLoggedClient()
    {
        $data = [
            '_username' => 'superadmin',
            '_password' => 'superadmin',
        ];
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->filter('form[action$="login_check"].form-horizontal button[type="submit"]')->form();
        $client->submit($form, $data);

        return $client;
    }
}
