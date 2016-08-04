<?php

/*
 * This file is part of the Push Notifications Plugin.
 *
 * (c) Paweł Mikołajczuk <mikolajczuk.private@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AHS\PushNotificationsPluginBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Subscription form type.
 */
class SubscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('playerId', null, array(
                'error_bubbling' => true,
                'required' => true,
            ))
            ->add('articleLanguage', null, array(
                'error_bubbling' => true,
                'required' => true,
            ))
            ->add('articleNumber', null, array(
                'error_bubbling' => true,
                'required' => true,
            ))
            ->add('commentId', null, array(
                'error_bubbling' => true,
                'required' => true,
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'subscription';
    }
}
