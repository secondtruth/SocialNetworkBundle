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
use Symfony\Component\Form\FormBuilder;

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
    protected function buildUserForm(FormBuilder $builder, array $options)
    {
        parent::buildUserForm($builder, $options);
        $builder->add('plainPassword', 'repeated', [
            'type' => 'password',
            'invalid_message' => 'kiboko_social.socialnetwork.profile.edit_profil.password_no_match',
        ])
        ->add('avatarFile', 'file', ['required' => false])
        ->add('send_msg_to_email', 'checkbox', ['required' => false]);
    }
}
