<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Bundle\SocialNetworkBundle\Twig\Extension;

use Symfony\Component\Form\Extension\Csrf\CsrfProvider\SessionCsrfProvider;
use Symfony\Component\HttpFoundation\Session;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * LogoutUrlHelper provides generator functions for the logout URL to Twig.
 *
 * @author Vincent Guerard <v.guerard@fulgurio.net>
 */
class LoginFormExtension extends \Twig_Extension
{
    /**
     * @var Symfony\Component\HttpFoundation\Session
     */
    private $session;

    /**
     * @var Symfony\Component\Form\Extension\Csrf\CsrfProvider\SessionCsrfProvider
     */
    private $csrfProvider;

    /**
     * Constructor.
     *
     * @param Symfony\Component\HttpFoundation\Session                               $session
     * @param Symfony\Component\Form\Extension\Csrf\CsrfProvider\SessionCsrfProvider $csrfProvider
     */
    public function __construct(Session $session, SessionCsrfProvider $csrfProvider)
    {
        $this->session = $session;
        $this->csrfProvider = $csrfProvider;
    }

    /**
     * Init Twig functions.
     */
    public function getFunctions()
    {
        return [
            'last_username' => new \Twig_Function_Method($this, 'getLastUsername'), //, array('is_safe' => array('html'))),
            'csrf_token' => new \Twig_Function_Method($this, 'getCsrfLoginToken'), //, array('is_safe' => array('html'))),
        ];
    }

    /**
     * Return last username for login form.
     *
     * @return string
     */
    public function getLastUsername()
    {
        return (null === $this->session) ? '' : $this->session->get(SecurityContext::LAST_USERNAME);
    }

    /**
     * Return csrf_token for login form.
     *
     * @return string
     */
    public function getCsrfLoginToken()
    {
        return $this->csrfProvider->generateCsrfToken('authenticate');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'login_form';
    }
}
