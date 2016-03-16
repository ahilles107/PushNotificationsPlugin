<?php

/*
 * This file is part of the Push Notifications Plugin.
 *
 * (c) Paweł Mikołajczuk <mikolajczuk.private@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace AHS\PushNotificationsPluginBundle\EventListener;

use Newscoop\NewscoopBundle\Event\ConfigureMenuEvent;
use Symfony\Component\Translation\Translator;

class ConfigureMenuListener
{
    protected $translator;

    /**
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param \Newscoop\NewscoopBundle\Event\ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();
        $pluginName = $this->translator->trans('pushnotifications.menu.name');
        $menu[$this->translator->trans('Plugins')]->addChild(
            $pluginName,
            array('uri' => $event->getRouter()->generate('ahs_pushnotificationsplugin_admin_index'))
        )
        ->setAttribute('rightdrop', true)
        ->setLinkAttribute('data-toggle', 'rightdrop');

        $menu[$this->translator->trans('Plugins')][$pluginName]->addChild(
            $this->translator->trans('pushnotifications.menu.create_notification'),
            array('uri' => $event->getRouter()->generate('ahs_pushnotificationsplugin_notification_create'))
        );

        $menu[$this->translator->trans('Plugins')][$pluginName]->addChild(
            $this->translator->trans('pushnotifications.menu.list_notifications'),
            array('uri' => $event->getRouter()->generate('ahs_pushnotificationsplugin_admin_index'))
        );

        $menu[$this->translator->trans('Plugins')][$pluginName]->addChild(
            $this->translator->trans('pushnotifications.menu.list_applications'),
            array('uri' => $event->getRouter()->generate('ahs_pushnotificationsplugin_applications_index'))
        );

        $menu[$this->translator->trans('Plugins')][$pluginName]->addChild(
            $this->translator->trans('pushnotifications.menu.settings'),
            array('uri' => $event->getRouter()->generate('ahs_pushnotificationsplugin_settings'))
        );

    }
}
