<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Bundle\SocialNetworkBundle\Twig\Extension;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * LogoutUrlHelper provides generator functions for the logout URL to Twig.
 *
 * @author Vincent Guerard <v.guerard@fulgurio.net>
 */
class LoginFormExtension extends AbstractExtension
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    /**
     * Constructor.
     *
     * @param Session                   $session
     * @param CsrfTokenManagerInterface $csrfTokenManager
     */
    public function __construct(Session $session, CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->session = $session;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * Init Twig functions.
     */
    public function getFunctions()
    {
        return [
            'last_username' => new TwigFunction('last_username', [$this, 'getLastUsername']), //, array('is_safe' => array('html'))),
            'csrf_token' => new TwigFunction('csrf_token', [$this, 'getCsrfLoginToken']), //, array('is_safe' => array('html'))),
        ];
    }

    /**
     * Return last username for login form.
     *
     * @return string
     */
    public function getLastUsername()
    {
        return null !== $this->session ? $this->session->get(Security::LAST_USERNAME) : '';
    }

    /**
     * Return csrf_token for login form.
     *
     * @return string
     */
    public function getCsrfLoginToken()
    {
        return $this->csrfTokenManager->getToken('authenticate');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'login_form';
    }
}
