<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Bundle\SocialNetworkBundle\Form\Type;

use FOS\UserBundle\Form\Type\ResettingFormType as BaseType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Resetting form type.
 *
 * @author Vincent Guerard <v.guerard@fulgurio.net>
 */
class ResettingFormType extends BaseType
{
    /**
     * (non-PHPdoc).
     *
     * @see Symfony\Component\Form\FormTypeInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('new', 'repeated', [
            'type' => 'password',
            'invalid_message' => 'kiboko_social.socialnetwork.lost_password.password_no_match',
        ]);
    }
}
