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
 * Notification form type.
 */
class NotificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, array(
                'error_bubbling' => true,
                'required' => true,
            ))
            ->add('content', null, array(
                'error_bubbling' => true,
                'required' => true,
            ))
            ->add('applications', 'entity', array(
                'class' => 'AHS\PushNotificationsPluginBundle\Entity\Application',
                'error_bubbling' => true,
                'required' => true,
                'multiple' => true,
                'expanded' => true
            ))
            ->add('articleLanguage', 'hidden', array(
                'error_bubbling' => true,
                'required' => false,
            ))
            ->add('articleNumber', 'hidden', array(
                'error_bubbling' => true,
                'required' => false,
            ))
            ->add('publishDate', 'datetime', array(
                'widget' => 'choice',
                'format' => 'yyyy-MM-dd h:i:s',
                'attr' => array(
                    'class' => 'form-control input-inline datepicker',
                ),
                'error_bubbling' => true,
                'required' => true
            ))
            ->add('url', 'text', array(
                'error_bubbling' => true,
                'required' => false,
            ))
            ->add('schedule', 'submit', array(
                'attr' => array('class' => 'btn btn-primary col-md-12 js-send-notification'),
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'notification';
    }
}
