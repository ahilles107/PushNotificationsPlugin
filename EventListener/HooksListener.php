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

use Newscoop\EventDispatcher\Events\PluginHooksEvent;
use AHS\PushNotificationsPluginBundle\Form\NotificationType;
use AHS\PushNotificationsPluginBundle\Entity\Notification;

class HooksListener
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function sidebar(PluginHooksEvent $event)
    {
        $user = $this->container->get('user')->getCurrentUser();
        if (!$user->hasPermission('plugin_pushnotifications_create')) {
            return;
        }

        $translator = $this->container->get('translator');
        if (!$event->getArgument('article')->isPublished()) {
            $response = $this->container->get('templating')->renderResponse(
                'AHSPushNotificationsPluginBundle:Hooks:notPublishedSidebar.html.twig',
                array(
                    'pluginName' => $translator->trans('pushnotifications.menu.name'),
                )
            );
            $event->addHookResponse($response);
            return;
        }

        $em = $this->container->get('em');
        $article = $em->getRepository('Newscoop\Entity\Article')
            ->getArticle($event->getArgument('article')->getArticleNumber(), $event->getArgument('article')->getLanguageId())
            ->getSingleResult();
        $articleNotifications = $em->getRepository('AHS\PushNotificationsPluginBundle\Entity\Notification')->getAllByArticle(
            $article->getNumber(), $article->getLanguageId()
        )->getResult();
        $preferencesService = $this->container->get('system_preferences_service');
        $linkService = $this->container->get('article.link');

        $notification = new Notification();
        $notification->setTitle($article->getTitle());
        $notification->setContent($article->getData($preferencesService->ahs_pushnotifications_content_field));
        $notification->setUrl($linkService->getLink($article));
        $notification->setArticleNumber($article->getNumber());
        $notification->setArticleLanguage($article->getLanguageId());

        $form = $this->container->get('form.factory')->create(new NotificationType(), $notification, array(
            'action' => $this->container->get('router')->generate('ahs_pushnotificationsplugin_notification_create')
        ));

        $response = $this->container->get('templating')->renderResponse(
            'AHSPushNotificationsPluginBundle:Hooks:sidebar.html.twig',
            array(
                'pluginName' => $translator->trans('pushnotifications.menu.name'),
                'article' => $event->getArgument('article'),
                'form' => $form->createView(),
                'articleNotifications' => $articleNotifications
            )
        );
        $event->addHookResponse($response);
    }
}
