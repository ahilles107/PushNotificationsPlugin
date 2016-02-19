<?php

namespace AHS\PushNotificationsPluginBundle\OneSignal;

use AHS\PushNotificationsPluginBundle\OneSignal\OneSignal;

class Notifications
{
    const NOTIFICATIONS_LIMIT = 50;

    /**
     * @var OneSignal
     */
    protected $api;

    /**
     * Constructor.
     *
     * @param OneSignal $api
     */
    public function __construct(OneSignal $api)
    {
        $this->api = $api;
    }

    /**
     * Get information about notification with provided ID.
     *
     * Application authentication key and ID must be set.
     *
     * @param string $id Notification ID
     *
     * @return array
     */
    public function getOne($id)
    {
        $url = '/notifications/' . $id . '?app_id=' . $this->api->getConfig()->getApplicationId();

        return $this->api->request('GET', $url, array(
            'headers' => array(
                'Authorization' => 'Basic ' . $this->api->getConfig()->getApplicationAuthKey(),
            ),
        ));
    }

    /**
     * Get information about all notifications.
     *
     * Application authentication key and ID must be set.
     *
     * @param int $limit  How many notifications to return (max 50)
     * @param int $offset Results offset (results are sorted by ID)
     *
     * @return array
     */
    public function getAll($limit = self::NOTIFICATIONS_LIMIT, $offset = 0)
    {
        return $this->api->request('GET', '/notifications?' . http_build_query(array(
             'limit' => max(0, min(self::NOTIFICATIONS_LIMIT, filter_var($limit, FILTER_VALIDATE_INT))),
             'offset' => max(0, min(self::NOTIFICATIONS_LIMIT, filter_var($offset, FILTER_VALIDATE_INT))),
        )), array(
            'headers' => array(
                'Authorization' => 'Basic ' . $this->api->getConfig()->getApplicationAuthKey(),
            ),
            'json' => array(
                'app_id' => $this->api->getConfig()->getApplicationId(),
            ),
        ));
    }

    /**
     * Send new notification with provided data.
     *
     * Application authentication key and ID must be set.
     *
     * @param array $data
     *
     * @return array
     */
    public function add(array $data)
    {
        $data['app_id'] = $this->api->getConfig()->getApplicationId();
        return $this->api->request('POST', '/notifications', array(
            'headers' => array(
                'Authorization' => 'Basic ' . $this->api->getConfig()->getApplicationAuthKey(),
                'Content-Type' => 'application/json',
            ),
            'json' => $data
        ));
    }

    /**
     * Open notification.
     *
     * Application authentication key and ID must be set.
     *
     * @param string $id Notification ID
     *
     * @return array
     */
    public function open($id)
    {
        return $this->api->request('PUT', '/notifications/' . $id, array(
            'headers' => array(
                'Authorization' => 'Basic ' . $this->api->getConfig()->getApplicationAuthKey(),
            ),
            'json' => array(
                'app_id' => $this->api->getConfig()->getApplicationId(),
                'opened' => true,
            ),
        ));
    }

    /**
     * Cancel notification.
     *
     * Application authentication key and ID must be set.
     *
     * @param string $id Notification ID
     *
     * @return array
     */
    public function cancel($id)
    {
        return $this->api->request('DELETE', '/notifications/' . $id, array(
            'headers' => array(
                'Authorization' => 'Basic ' . $this->api->getConfig()->getApplicationAuthKey(),
            ),
            'json' => array(
                'app_id' => $this->api->getConfig()->getApplicationId(),
            ),
        ));
    }
}
