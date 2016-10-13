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
 * Admin controller tests.
 *
 * @author Vincent GUERARD <v.guerard@fulgurio.net>
 */
class AdminControllerTest extends WebTestCase
{
    /**
     * Admin index test.
     */
    public function testIndexAction()
    {
        $client = $this->getAdminLoggedClient();

        $client->request('GET', '/admin/');
        // Authentified
        $security = $client->getContainer()->get('security.context');
        $this->assertTrue($security->isGranted('ROLE_ADMIN'));
    }
}
