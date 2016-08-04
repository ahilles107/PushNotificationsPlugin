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
 * Notification entity
 *
 * @ORM\Entity(repositoryClass="AHS\PushNotificationsPluginBundle\Entity\Repository\SubscriptionRepository")
 * @ORM\Table(name="plugin_ahspushnotifications_subscription")
 */
class Subscription
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
    protected $playerId;

    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $articleNumber;

    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $articleLanguage;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var integer
     */
    protected $commentId;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="Id")
     *
     * @var Newscoop\Entity\User
     */
    protected $user;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     *
     * @var datetime
     */
    protected $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPlayerId()
    {
        return $this->playerId;
    }

    public function setPlayerId($playerId)
    {
        $this->playerId = $playerId;

        return $this;
    }

    public function getCommentId()
    {
        return $this->commentId;
    }

    public function setCommentId($commentId)
    {
        $this->commentId = $commentId;

        return $this;
    }

    public function getArticleNumber()
    {
        return $this->articleNumber;
    }

    public function setArticleNumber($articleNumber)
    {
        $this->articleNumber = $articleNumber;

        return $this;
    }

    public function getArticleLanguage()
    {
        return $this->articleLanguage;
    }

    public function setArticleLanguage($articleLanguage)
    {
        $this->articleLanguage = $articleLanguage;

        return $this;
    }
    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }
    public function setCreatedAt(\DateTime $date)
    {
        $this->createdAt = $date;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
