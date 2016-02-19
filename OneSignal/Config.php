<?php

namespace AHS\PushNotificationsPluginBundle\OneSignal;

class Config
{
    /**
     * @var string
     */
    protected $applicationId;

    /**
     * @var string
     */
    protected $applicationAuthKey;

    /**
     * @param string $applicationId
     * @param string $applicationAuthKey
     * @param string $userAuthKey
     */
    public function __construct($applicationId, $applicationAuthKey, $userAuthKey)
    {
        $this->setApplicationId($applicationId);
        $this->setApplicationAuthKey($applicationAuthKey);
    }

    /**
     * Set OneSignal application id.
     *
     * @param string $applicationId
     */
    public function setApplicationId($applicationId)
    {
        $this->applicationId = $applicationId;
    }

    /**
     * Get OneSignal application id.
     *
     * @return string
     */
    public function getApplicationId()
    {
        return $this->applicationId;
    }

    /**
     * Set OneSignal application authentication key.
     *
     * @param string $applicationAuthKey
     */
    public function setApplicationAuthKey($applicationAuthKey)
    {
        $this->applicationAuthKey = $applicationAuthKey;
    }

    /**
     * Get OneSignal application authentication key.
     *
     * @return string
     */
    public function getApplicationAuthKey()
    {
        return $this->applicationAuthKey;
    }
}
