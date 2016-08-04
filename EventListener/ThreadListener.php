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

use Newscoop\EventDispatcher\Events\GenericEvent;
use AHS\ExtraPluginBundle\Entity\Thread;
use AHS\PushNotificationsPluginBundle\Entity\Application;
use AHS\PushNotificationsPluginBundle\Entity\Notification;
use AHS\PushNotificationsPluginBundle\PushHandlers\ThreadNotificationsInterface;

class ThreadListener
{
    protected $em;

    protected $linkService;

    public function __construct($em, $linkService)
    {
        $this->em = $em;
        $this->linkService = $linkService;
    }

    public function sendNotifications(GenericEvent $event)
    {
        $idArray = $event->getArgument('id');
        $commentId = false;

        if (array_key_exists('id', $idArray)) {
            $commentId = $idArray['id'];
        }

        $comment = $this->em->getRepository('Newscoop\Entity\Comment')
            ->getComment($commentId)
            ->getOneOrNullResult();

        if ($comment) {
            $article = $this->em->getRepository('Newscoop\Entity\Article')
                ->getArticle($comment->getThread(), $comment->getLanguage()->getId())
                ->getOneOrNullResult();

            // get subscriptions
            $subscriptions = $this->em->getRepository('AHS\PushNotificationsPluginBundle\Entity\Subscription')
                ->getSubscriptionsForThread(
                    $comment->getThread(),
                    $comment->getLanguage()->getId()
                )
                ->getArrayResult();

            $applications = $this->em->getRepository('AHS\PushNotificationsPluginBundle\Entity\Application')
                ->findBy(array('useForThreadNotifications' => true));

            if (0 == count($applications) || 0 == count($subscriptions)) {
                return;
            }

            // Create Notification
            $notification = new Notification();
            $notification->setTitle('Nowy komentarz pod artykułem');
            $notification->setContent('Artykuł: '. $article->getTitle());
            $notification->setArticleNumber($article->getNumber());
            $notification->setArticleLanguage($article->getLanguage()->getId());
            $notification->setArticle($article);
            $notification->setPublishDate(new \DateTime('now'));
            $notification->setUrl($this->linkService->getLink($article));
            $notification->setIsActive(true);
            $notification->addPushHandlerResponse(array());
            $notification->setIsThreadNotification(true);
            $notification->setApplications($applications);
            $this->em->persist($notification);
            $this->em->flush();

            foreach ($applications as $application) {
                $pushHandlerNamespace = $application->getPushHandler()->getNamespace();
                $pushHandler = new $pushHandlerNamespace();

                if (!$pushHandler instanceof ThreadNotificationsInterface) {
                    continue;
                }

                $players = array_map(function($subscription) {
                    return $subscription['playerId'];
                }, $subscriptions);

                $response = $pushHandler->sendThreadNotification($notification, $application, $article, $players);
                if (is_null($response)) {
                    $response = array();
                }
                $notification->addPushHandlerResponse($response);
                $notification->setRecipientsNumber($notification->getRecipientsNumber()+$pushHandler->getRecipientsNumber($response));
            }
            $notification->setStatus(Notification::NOTIFICATION_PROCESSED);
            $this->em->flush();
        }
    }
}
