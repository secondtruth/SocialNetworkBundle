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
 * Registration controller tests.
 *
 * @author Vincent GUERARD <v.guerard@fulgurio.net>
 */
class RegistrationControllerTest extends WebTestCase
{
    /**
     * Register form access test.
     */
    public function testRegisterAction()
    {
        $data = [
            'fos_user_registration_form[username]' => 'user100',
            'fos_user_registration_form[email]' => 'user100@example.com',
            'fos_user_registration_form[plainPassword]' => 'user100',
        ];
        $client = static::createClient();
        $crawler = $client->request('GET', '/register/');

        $this->assertSame('kiboko_social.socialnetwork.register.legend', $crawler->filter('form legend')->text());

        $form = $crawler->filter('form[action$="register/"] button[name="_submit"]')->form();
        $crawler = $client->submit($form, $data);
        $this->assertTrue($client->getResponse()->isRedirect('/register/confirmed'));

        // Authentified
        $security = $client->getContainer()->get('security.context');
        $this->assertTrue($security->isGranted('ROLE_USER'));
    }

    /**
     * Register form with empty form test.
     */
    public function testRegisterWithEmptyForm()
    {
        $data = [
            'fos_user_registration_form[username]' => '',
            'fos_user_registration_form[email]' => '',
            'fos_user_registration_form[plainPassword]' => '',
        ];
        $client = static::createClient();
        $crawler = $client->request('GET', '/register/');
        $form = $crawler->filter('form[action$="register/"] button[name="_submit"]')->form();

        $crawler = $client->submit($form, $data);
        $this->assertCount(1, $crawler->filter('div.alert.alert-error:contains("fos_user.username.blank")'));
        $this->assertCount(1, $crawler->filter('div.alert.alert-error:contains("fos_user.email.blank")'));
        $this->assertCount(1, $crawler->filter('div.alert.alert-error:contains("fos_user.password.blank")'));
    }

    /**
     * Register form with bad email test.
     */
    public function testRegisterWithBadEmail()
    {
        $data = [
            'fos_user_registration_form[username]' => '',
            'fos_user_registration_form[email]' => 'bademail',
            'fos_user_registration_form[plainPassword]' => '',
        ];
        $client = static::createClient();
        $crawler = $client->request('GET', '/register/');
        $form = $crawler->filter('form[action$="register/"] button[name="_submit"]')->form();

        $crawler = $client->submit($form, $data);
        $this->assertCount(1, $crawler->filter('div.alert.alert-error:contains("fos_user.email.invalid")'));
    }

    /**
     * Register form with existing user.
     */
    public function testRegisterWithExistingUser()
    {
        $data = [
            'fos_user_registration_form[username]' => 'user1',
            'fos_user_registration_form[email]' => 'user1@example.com',
            'fos_user_registration_form[plainPassword]' => 'user1',
        ];
        $client = static::createClient();
        $crawler = $client->request('GET', '/register/');
        $form = $crawler->filter('form[action$="register/"] button[name="_submit"]')->form();

        $crawler = $client->submit($form, $data);
        $this->assertCount(1, $crawler->filter('div.alert.alert-error:contains("fos_user.email.already_used")'));
        $this->assertCount(1, $crawler->filter('div.alert.alert-error:contains("fos_user.username.already_used")'));
    }
}
