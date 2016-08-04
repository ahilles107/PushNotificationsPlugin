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
use Newscoop\Entity\Article;

interface ThreadNotificationsInterface
{
    /**
     * @param Notification
     * @param Application
     * @param Article
     * @param array
     *
     * @return mixed Service provider response if available
     */
    public function sendThreadNotification($notification, $application, $thread, $players = array());
}
