<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Bundle\SocialNetworkBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class KibokoSocialNetworkBundle extends Bundle
{
    /**
     * (non-PHPdoc).
     *
     * @see \Symfony\Component\HttpKernel\Bundle\Bundle::getParent()
     */
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
