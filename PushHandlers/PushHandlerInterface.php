<?php

/*
 * This file is part of the Push Notifications Plugin.
 *
 * (c) Paweł Mikołajczuk <mikolajczuk.private@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace AHS\PushNotificationsPluginBundle\PushHandlers;

use AHS\PushNotificationsPluginBundle\Entity\Notification;
use AHS\PushNotificationsPluginBundle\Entity\Application;

interface PushHandlerInterface
{
    /**
     * @return string
     */
    public static function getPushHandlerName();

    /**
     * @return string
     */
    public static function getPushHandlerDescription();

    /**
     * @param  Notification
     * @param  Application
     *
     * @return mixed Service provider response if available
     */
    public function sendNotification($notification, $application);

    /**
     * @return array
     */
    public static function getPushHandlerRequiredSettings();

    /**
     * @param mixed Response from provider
     *
     * @return integer Recipients number if available else 0
     */
    public function getRecipientsNumber($response);
}
