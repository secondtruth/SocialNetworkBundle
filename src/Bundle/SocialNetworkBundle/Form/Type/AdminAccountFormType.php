<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Bundle\SocialNetworkBundle\Form\Type;

use Kiboko\Bundle\SocialNetworkBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackValidator;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

class AdminAccountFormType extends AbstractType
{
    private $container;

    /**
     * Constructor.
     *
     * @param object $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * (non-PHPdoc).
     *
     * @see Symfony\Component\Form\FormTypeInterface::buildForm()
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $container = $this->container;
        $builder
            ->add('username', 'text', [
                'required' => true,
            ])
            ->add('email', 'email', [
                'required' => true,
            ])
            ->add('newPassword', 'repeated', [
                'type' => 'password',
                'invalid_message' => 'kiboko_social.socialnetwork.add.password.no_match',
                'required' => false,
                'property_path' => false,
            ])
            ->add('avatarFile', 'file', ['required' => false])
            ->addValidator(new CallbackValidator(function (FormInterface $form) use ($container) {
                $request = $container->get('request');
                $isUpdate = $request->get('userId') ? true : false;
                $usernameField = $form->get('username');
                if (trim($usernameField->getData()) === '') {
                    $usernameField->addError(new FormError('kiboko_social.socialnetwork.add.username.not_blank'));
                }
                $emailField = $form->get('email');
                if (trim($emailField->getData()) === '') {
                    $emailField->addError(new FormError('kiboko_social.socialnetwork.add.email.not_blank'));
                }
                $newPasswordField = $form->get('newPassword')->get('first');
                if (!$isUpdate && trim($newPasswordField->getData()) === '') {
                    $newPasswordField->addError(new FormError('kiboko_social.socialnetwork.add.password.not_blank'));
                }
            })
        );
    }

    /**
     * (non-PHPdoc).
     *
     * @see Symfony\Component\Form\FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'user';
    }
}
