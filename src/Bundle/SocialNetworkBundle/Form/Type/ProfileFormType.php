<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Bundle\SocialNetworkBundle\Form\Type;

use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Profile form type.
 *
 * @author Vincent Guerard <v.guerard@fulgurio.net>
 */
class ProfileFormType extends BaseType
{
    /**
     * (non-PHPdoc).
     *
     * @see Symfony\Component\Form\FormTypeInterface::getName()
     */
    protected function buildUserForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildUserForm($builder, $options);
        $builder->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'kiboko_social.socialnetwork.profile.edit_profil.password_no_match',
        ])
        ->add('avatarFile', FileType::class, ['required' => false])
        ->add('send_msg_to_email', CheckboxType::class, ['required' => false]);
    }
}
