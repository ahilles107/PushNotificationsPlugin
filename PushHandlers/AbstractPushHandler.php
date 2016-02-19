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

/**
 * Abstract PushHandler class.
 */
abstract class AbstractPushHandler
{
    /**
     * Push Handler name
     *
     * @var string
     */
    protected static $pushHandlerName;

    /**
     * Push Handler description
     *
     * @var string
     */
    protected static $pushHandlerDescription;

    /**
     * Push Handler required settings
     *
     * Array with values for required settings.
     * Settings will be saved in application which is using this Push Handler.
     *
     * @var string
     */
    protected static $pushHandlerRequiredSettings = array();

    /**
     * Push Handler settings used for current application.
     *
     * @var array
     */
    private $pushHandlerSettings = array();

    public function __construct(array $pushHandlerSettings = array())
    {
        $this->pushHandlerSettings = $pushHandlerSettings;
    }

    /**
     * Returns the Push Handler name
     *
     * @return string
     */
    public static function getPushHandlerName()
    {
        return static::$pushHandlerName;
    }

    /**
     * Returns the Push Handler description
     *
     * @return string
     */
    public static function getPushHandlerDescription()
    {
        return static::$pushHandlerDescription;
    }

    /**
     * Send push noticiation to service provider
     *
     * @return mixed Service provider response if available
     */
    public function sendNotification($notification, $application)
    {
        return true;
    }

    /**
     * Returns the Push Handler description
     *
     * @return string
     */
    public static function getPushHandlerRequiredSettings()
    {
        return static::$pushHandlerRequiredSettings;
    }

    /**
     * Uf possible read PushHandler service provider response and return recipients number
     * @param mixed $response
     *
     * @return integer Recipients number
     */
    public function getRecipientsNumber($response)
    {
        return null;
    }
}
