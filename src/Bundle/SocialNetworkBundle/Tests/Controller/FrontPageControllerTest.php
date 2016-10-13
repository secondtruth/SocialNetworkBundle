<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Bundle\SocialNetworkBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

/**
 * Front page controller tests.
 *
 * @author Vincent GUERARD <v.guerard@fulgurio.net>
 */
class FrontPageControllerTest extends BaseWebTestCase
{
    /**
     * Homepage test.
     */
    public function testHomepageAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');
        $this->assertCount(
                1,
                $crawler->filter('h1:contains("kiboko_social.socialnetwork.homepage")')
        );
    }
}
