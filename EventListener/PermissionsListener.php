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

use Newscoop\EventDispatcher\Events\PluginPermissionsEvent;
use Symfony\Component\Translation\Translator;

class PermissionsListener
{
    /**
     * Translator
     * @var Translator
     */
    protected $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Register plugin permissions in Newscoop ACL
     *
     * @param PluginPermissionsEvent $event
     */
    public function registerPermissions(PluginPermissionsEvent $event)
    {
        $event->registerPermissions($this->translator->trans('pushnotifications.menu.name'), array(
            'plugin_pushnotifications_create' => $this->translator->trans('pushnotifications.permissions.create'),
            'plugin_pushnotifications_publish' => $this->translator->trans('pushnotifications.permissions.publish'),
            'plugin_pushnotifications_manage' => $this->translator->trans('pushnotifications.permissions.manage'),
            'plugin_pushnotifications_settings' => $this->translator->trans('pushnotifications.permissions.settings')
        ));
    }
}
