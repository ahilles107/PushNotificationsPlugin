<?php

/*
 * This file is part of the Push Notifications Plugin.
 *
 * (c) Paweł Mikołajczuk <mikolajczuk.private@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace AHS\PushNotificationsPluginBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Application entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="plugin_ahspushnotifications_application")
 */
class Application
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", name="id")
     * @var integer
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $title;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    protected $pushHandlerSettings;

    /**
     * @ORM\OneToMany(targetEntity="Notification", mappedBy="appliacation")
     */
    protected $notifications;

    /**
     * @ORM\ManyToOne(targetEntity="PushHandler", inversedBy="applications", fetch="EAGER")
     * @ORM\JoinColumn(name="push_handler_id", referencedColumnName="id")
     */
    protected $pushHandler;

    public function __construct()
    {
        $this->notifications = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setPushHandlerSettings(array());
    }

    public function getId()
    {
        return $this->id;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getNotifications()
    {
        return $this->notifications;
    }

    public function setPushHandler($pushHandler)
    {
        $this->pushHandler = $pushHandler;

        return $this;
    }

    public function getPushHandler()
    {
        return $this->pushHandler;
    }

    public function getPushHandlerSettings()
    {
        return json_decode($this->pushHandlerSettings, true);
    }

    public function setPushHandlerSettings(array $settings)
    {
        $this->pushHandlerSettings = json_encode($settings);
    }

    public function __toString()
    {
        return $this->title;
    }
}
