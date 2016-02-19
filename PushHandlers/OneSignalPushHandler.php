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

use AHS\PushNotificationsPluginBundle\PushHandlers\AbstractPushHandler;
use AHS\PushNotificationsPluginBundle\OneSignal\Config;
use AHS\PushNotificationsPluginBundle\OneSignal\OneSignal;
use AHS\PushNotificationsPluginBundle\OneSignal\Notifications;

/**
 * OneSignal.com Push Handler class.
 */
class OneSignalPushHandler extends AbstractPushHandler
{
    /**
     * Push Handler name
     *
     * @var string
     */
    protected static $pushHandlerName = "OneSignal.com Push Handler";

    /**
     * Push Handler description
     *
     * @var string
     */
    protected static $pushHandlerDescription = "Provides basic support for OneSignal.com push notifications service";

    /**
     * OneSignal.com Push Handler required settings.
     *
     * @var array
     */
    protected static $pushHandlerRequiredSettings = array(
        'applicationId' => '',
        'applicationAuthKey' => ''
    );

    /**
     * Send push noticiation to OneSignal.com
     *
     * @return array
     */
    public function sendNotification($notification, $application)
    {
        $settings = $application->getPushHandlerSettings();
        $config = new Config($settings['applicationId'], $settings['applicationAuthKey'], $settings['userAuthKey']);
        $oneSignalApi = new OneSignal($config);
        $notifications = new Notifications($oneSignalApi);

        return $notifications->add($this->getData($notification));
    }

    public function getRecipientsNumber($response)
    {
        return $response['recipients'];
    }

    protected function getData($notification)
    {
         $data = array(
            'included_segments' => array('All'),
            'contents' => array('en' => $notification->getContent()),
            'headings' => array('en' => $notification->getTitle()),
            'send_after' => $notification->getPublishDate()->format('Y-m-d H:i:sP')
        );

        if ($notification->getUrl() !== null) {
            $data['url'] = $notification->getUrl();
        }

        return $data;
    }
}
