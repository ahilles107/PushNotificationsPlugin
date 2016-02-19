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
 * @ORM\Entity()
 * @ORM\Table(name="plugin_ahspushnotifications_notification")
 */
class Notification
{
    const NOTIFICATION_NOTPROCESSED = 0;
    const NOTIFICATION_PROCESSED = 1;
    const NOTIFICATION_ERRORED = 9;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", name="id")
     * @var integer
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var integer
     */
    protected $articleNumber;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var integer
     */
    protected $articleLanguage;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $title;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    protected $content;

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

    /**
     * @ORM\Column(type="datetime", name="publish_at")
     *
     * @var datetime
     */
    protected $publishDate;

    /**
     * @ORM\Column(type="boolean", name="is_active")
     *
     * @var string
     */
    protected $isActive;

    /**
     * @ORM\Column(type="integer", name="status")
     *
     * @var string
     */
    protected $status;

    /**
     * Optional notification groups used by Push Handler
     *
     * @var string
     */
    protected $groups;

    /**
     * @ORM\ManyToMany(targetEntity="Application")
     * @ORM\JoinTable(name="plugin_ahspushnotifications_notification_applications", joinColumns={
     * 	 	@ORM\JoinColumn(name="app_id", referencedColumnName="id")
     * })
     */
    protected $applications;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    protected $pushHandlerResponse;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    protected $url;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var integer
     */
    protected $recipientsNumber;

    /**
     * Article object for push handler (if set)
     *
     *  @var \Newscoop\Entity\Article
     */
    protected $article;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->publishDate = new \DateTime();
        $this->active = true;
        $this->status = self::NOTIFICATION_NOTPROCESSED;
    }

    public function getId()
    {
        return $this->id;
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
        $this->$articleLanguage = $articleLanguage;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;

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

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getIsActive()
    {
        return $this->isActive;
    }

    public function isActive()
    {
        return $this->isActive;
    }

    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function getApplications()
    {
        return $this->applications;
    }

    public function setApplications($applications)
    {
        $this->applications = $applications;

        return $this;
    }

    public function getPublishDate()
    {
        return $this->publishDate;
    }

    public function setPublishDate($publishDate)
    {
        $this->publishDate = $publishDate;

        return $this;
    }

    public function getPushHandlerResponse()
    {
        return $this->pushHandlerResponse;
    }

    public function setPushHandlerResponse(array $pushHandlerResponse)
    {
        $this->pushHandlerResponse = json_encode($pushHandlerResponse);

        return $this;
    }

    public function addPushHandlerResponse(array $pushHandlerResponse)
    {
        $this->pushHandlerResponse = $this->pushHandlerResponse .' || '. json_encode($pushHandlerResponse);

        return $this;
    }

    public function getRecipientsNumber()
    {
        if (is_null($this->recipientsNumber)) {
            return 0;
        }

        return $this->recipientsNumber;
    }

    public function setRecipientsNumber($recipientsNumber)
    {
        $this->recipientsNumber = $recipientsNumber;

        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    public function setArticle(\Newscoop\Entity\Article $article)
    {
        $this->article = $article;
    }

    public function getArticle()
    {
        return $this->article;
    }
}
