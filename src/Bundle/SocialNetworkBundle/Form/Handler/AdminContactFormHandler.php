<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Bundle\SocialNetworkBundle\Form\Handler;

use Kiboko\Bundle\SocialNetworkBundle\Mailer\ContactMailer;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class AdminContactFormHandler
{
    /**
     * @var ContactMailer
     */
    private $mailer;

    /**
     * @var Symfony\Component\Form\Form
     */
    private $form;

    /**
     * @var Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     * Constructor.
     *
     * @param ContactMailer                            $mailer
     * @param Symfony\Component\Form\Form              $form
     * @param Symfony\Component\HttpFoundation\Request $request
     */
    public function __construct(ContactMailer $mailer, Form $form, Request $request)
    {
        $this->mailer = $mailer;
        $this->form = $form;
        $this->request = $request;
    }

    /**
     * Processing form values.
     *
     * @param $user
     *
     * @return bool
     */
    public function process($user)
    {
        if ($this->request->getMethod() === 'POST') {
            $this->form->bindRequest($this->request);
            if ($this->form->isValid()) {
                $data = $this->form->getData();
                $this->mailer->sendAdminMessage(
                        $user,
                        $data['subject'],
                        $data['message']
                );

                return true;
            }
        }

        return false;
    }
}
