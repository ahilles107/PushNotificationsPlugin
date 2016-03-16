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

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('content_field', 'text', array(
            'error_bubbling' => true,
            'label' => 'pushnotifications.form.settings.default_article_type_field',
            'required' => true
        ));

    }

    public function getName()
    {
        return 'settings';
    }
}
