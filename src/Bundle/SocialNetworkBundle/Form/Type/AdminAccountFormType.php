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
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdminAccountFormType extends AbstractType
{
    private bool $isUpdate;

    /**
     * Constructor.
     *
     * @param bool $isUpdate Whether this is an update (true) or create (false) operation
     */
    public function __construct(bool $isUpdate = false)
    {
        $this->isUpdate = $isUpdate;
    }

    /**
     * (non-PHPdoc).
     *
     * @see Symfony\Component\Form\FormTypeInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'kiboko_social.socialnetwork.add.username.not_blank',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'kiboko_social.socialnetwork.add.email.not_blank',
                    ]),
                ],
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'kiboko_social.socialnetwork.add.password.no_match',
                'required' => !$this->isUpdate,
                'property_path' => false,
                'constraints' => !$this->isUpdate ? [
                    new NotBlank([
                        'message' => 'kiboko_social.socialnetwork.add.password.not_blank',
                    ]),
                ] : [],
            ])
            ->add('avatarFile', FileType::class, ['required' => false]);
    }
}
