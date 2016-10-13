<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Bundle\SocialNetworkBundle\Tests\Controller;

/**
 * Security controller tests.
 *
 * @author Vincent GUERARD <v.guerard@fulgurio.net>
 */
class SecurityControllerTest extends WebTestCase
{
    /**
     * Login access test.
     */
    public function testLoginAction()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $this->assertCount(1, $crawler->filter('form legend:contains("kiboko_social.socialnetwork.signin.legend")'));
    }

    /**
     * Empty login form test.
     */
    public function testLoginAtionWithEmptyValue()
    {
        $data = [
            '_username' => '',
            '_password' => '',
        ];
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $form = $crawler->filter('form[action$="login_check"].form-horizontal button[type="submit"]')->form();
        $client->submit($form, $data);
        $crawler = $client->followRedirect();
        $this->assertSame('Bad credentials', $crawler->filter('div.alert.alert-error')->text());
    }

    /**
     * Login form with unknow user test.
     */
    public function testLoginActionWithUnknowUser()
    {
        $data = [
            '_username' => 'unknowuser',
            '_password' => '',
        ];
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $form = $crawler->filter('form[action$="login_check"].form-horizontal button[type="submit"]')->form();
        $client->submit($form, $data);
        $crawler = $client->followRedirect();
        $this->assertSame('Bad credentials', $crawler->filter('div.alert.alert-error')->text());
        $security = $client->getContainer()->get('security.context');
        $this->assertFalse($security->isGranted('ROLE_USER'));
    }

    /**
     * Login form with bad password test.
     */
    public function testLoginActionWithBadPassword()
    {
        $data = [
            '_username' => 'user1',
            '_password' => 'badpassword',
        ];
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $form = $crawler->filter('form[action$="login_check"].form-horizontal button[type="submit"]')->form();
        $client->submit($form, $data);
        $crawler = $client->followRedirect();
        $this->assertSame('The presented password is invalid.', $crawler->filter('div.alert.alert-error')->text());
        $security = $client->getContainer()->get('security.context');
        $this->assertFalse($security->isGranted('ROLE_USER'));
    }

    /**
     * Login form with disabled user test.
     */
    public function testLoginActionWithDisabledUser()
    {
        $data = [
            '_username' => 'userDisabled',
            '_password' => 'userDisabled',
        ];
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $form = $crawler->filter('form[action$="login_check"].form-horizontal button[type="submit"]')->form();
        $client->submit($form, $data);
        $crawler = $client->followRedirect();
        $this->assertSame('User account is disabled.', $crawler->filter('div.alert.alert-error')->text());
    }

    /**
     * Login form with existing user test (logged).
     */
    public function testLoginActionWithExistingUser()
    {
        $data = [
            '_username' => 'user1',
            '_password' => 'user1',
        ];
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $form = $crawler->filter('form[action$="login_check"].form-horizontal button[type="submit"]')->form();
        $crawler = $client->submit($form, $data);
        // Authentified
        $security = $client->getContainer()->get('security.context');
        $this->assertTrue($security->isGranted('ROLE_USER'));
    }

    public function toto()
    {
        //        // We check all URLs that authentied user still not use
//        //$client->followRedirects(FALSE);
//        $crawler = $client->request('GET', '/login');
//        $this->assertTrue($client->getResponse()->isRedirect('/'));
//        $crawler = $client->request('GET', '/register/');
//        $this->assertTrue($client->getResponse()->isRedirect('/'));
    }

    /**
     * Login test.
     */
    public function testLogoutAction()
    {
        $data = [
            '_username' => 'user1',
            '_password' => 'user1',
        ];
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->filter('form[action$="login_check"].form-horizontal button[type="submit"]')->form();
        $crawler = $client->submit($form, $data);
        // Authentified
        $security = $client->getContainer()->get('security.context');
        $this->assertTrue($security->isGranted('ROLE_USER'));

        $client->request('GET', '/logout');
        $crawler = $client->followRedirect();
        $security = $client->getContainer()->get('security.context');
        $this->assertFalse($security->isGranted('ROLE_USER'));
    }
}
