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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

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
                'label' => 'pushnotifications.form.notification.title',
                'required' => true,
            ))
            ->add('content', null, array(
                'label' => 'pushnotifications.form.notification.content',
                'error_bubbling' => true,
                'required' => true,
            ))
            ->add('applications', 'entity', array(
                'label' => 'pushnotifications.form.notification.appplications',
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
                'label' => 'pushnotifications.form.notification.publishDate',
                'widget' => 'choice',
                'format' => 'yyyy-MM-dd h:i:s',
                'attr' => array(
                    'class' => 'form-control input-inline datepicker',
                ),
                'error_bubbling' => true,
                'required' => true
            ))
            ->add('url', 'text', array(
                'label' => 'pushnotifications.form.notification.url',
                'error_bubbling' => true,
                'required' => false,
            ))
            ->add('schedule', 'submit', array(
                'label' => 'pushnotifications.form.notification.schedule',
                'attr' => array('class' => 'btn btn-primary col-md-12 js-send-notification'),
            ));

            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $notification = $event->getData();
                $form = $event->getForm();

                if ($notification->getSwitches() != null) {
                    $form->add('switches', 'collection', array(
                        'type' => 'checkbox',
                        'options' => array('required'  => false)
                    ));
                }
            });
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'notification';
    }
}
