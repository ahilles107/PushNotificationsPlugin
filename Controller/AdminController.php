<?php

/*
 * This file is part of the Push Notifications Plugin.
 *
 * (c) Paweł Mikołajczuk <mikolajczuk.private@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace AHS\PushNotificationsPluginBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use AHS\PushNotificationsPluginBundle\Form\ApplicationType;
use AHS\PushNotificationsPluginBundle\Entity\Application;
use AHS\PushNotificationsPluginBundle\Form\NotificationType;
use AHS\PushNotificationsPluginBundle\Entity\Notification;

class AdminController extends Controller
{
    /**
     * @Route("/admin/pushnotifications/", name="ahs_pushnotificationsplugin_admin_index")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        return array();
    }

    /**
     * @Route("/admin/pushnotifications/notifications/create", name="ahs_pushnotificationsplugin_notification_create")
     * @Template()
     * @Method("GET|POST")
     */
    public function createNotifficationAction(Request $request)
    {
        $user = $this->container->get('user')->getCurrentUser();
        if (!$user->hasPermission('plugin_pushnotifications_create')) {
            throw new AccessDeniedException();
        }

        $em = $this->container->get('em');
        $notification = new Notification();

        $form = $this->createForm(new NotificationType(), $notification);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $notification->setUser($user);
                $notification->setIsActive(true);
                $em->persist($notification);

                if ($user->hasPermission('plugin_pushnotifications_publish')) {
                    $this->handleNotification($notification);
                }

                $em->flush();

                return new RedirectResponse($this->generateUrl('ahs_pushnotificationsplugin_admin_index'));
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/admin/pushnotifications/notifications/{notificationId}/accept", name="ahs_pushnotificationsplugin_notification_accept")
     * @Method("POST|GET")
     */
    public function acceptNotificationAction($notificationId)
    {
        $user = $this->container->get('user')->getCurrentUser();
        if (!$user->hasPermission('plugin_pushnotifications_publish')) {
            throw new AccessDeniedException();
        }

        $em = $this->container->get('em');
        $notification = $em->getRepository('AHS\PushNotificationsPluginBundle\Entity\Notification')
            ->findOneById($notificationId);
        $this->handleNotification($notification);
        $em->flush();

        return new RedirectResponse($this->generateUrl('ahs_pushnotificationsplugin_admin_index'));
    }

    /**
     * @Route("/admin/pushnotifications/handlers/refresh")
     * @Method("POST|GET")
     */
    public function refreshPushHandlersAction()
    {
        $pushHandlerService = $this->container->get('ahs_pushnotifications_plugin.service.push_handler');
        $pushHandlerService->refreshPushHandlers();

        return new JsonResponse(array('status' => 'OK'));
    }

    /**
     * @Route("/admin/pushnotifications/load.json", options={"expose"=true}, name="ahs_pushnotificationsplugin_notification_load")
     */
    public function loadAdsAction(Request $request)
    {
        $em = $this->container->get('em');
        $zendRouter = $this->container->get('zend_router');
        $userService = $this->container->get('user');
        $user = $userService->getCurrentUser();

        $notifications = $em->getRepository('AHS\PushNotificationsPluginBundle\Entity\Notification')->findAll();
        $pocessed = array();
        foreach ($notifications as $notification) {
            $pocessed[] = $this->processNotification($notification, $zendRouter, $this->container->get('translator'), $user);
        }
        rsort($pocessed);
        $responseArray = array(
            'records' => $pocessed,
            'queryRecordCount' => count($notifications),
            'totalRecordCount' => count($notifications),
        );

        return new JsonResponse($responseArray);
    }

    /**
     * Process single notification.
     *
     * @param Notification  $notification Notification
     * @param Zend_Router   $zendRouter   Zend Router
     *
     * @return array
     */
    private function processNotification(Notification $notification, $zendRouter, $translator, $user)
    {
        $humanStatuses = array(
            Notification::NOTIFICATION_NOTPROCESSED => $translator->trans('pushnotifications.statuses.notprocesses'),
            Notification::NOTIFICATION_PROCESSED => $translator->trans('pushnotifications.statuses.processes'),
            Notification::NOTIFICATION_ERRORED => $translator->trans('pushnotifications.statuses.errored')
        );

        $links = array();
        if ($user->hasPermission('plugin_pushnotifications_publish')) {
            $links[] = array(
                'rel' => 'accept',
                'href' => $this->generateUrl('ahs_pushnotificationsplugin_notification_accept', array(
                    'notificationId' => $notification->getId()
                ))
            );
        }

        $applications = array();
        foreach ($notification->getApplications() as $application) {
            $applications[] = $application->getTitle();
        }

        return array(
            'id' => $notification->getId(),
            'title' => $notification->getTitle(),
            'content' => $notification->getContent(),
            'application' => implode(', ', $applications),
            'user' => array(
                'href' => $zendRouter->assemble(array(
                    'module' => 'admin',
                    'controller' => 'user',
                    'action' => 'edit',
                    'user' => $notification->getUser()->getId(),
                ), 'default', true),
                'username' => $notification->getUser()->getUsername(),
                'name' => $notification->getUser()->getFirstName().' '.$notification->getUser()->getLastName()
            ),
            'publishDate' => $notification->getPublishDate(),
            'created' => $notification->getCreatedAt(),
            'status' => $humanStatuses[$notification->getStatus()],
            'recipients' => $notification->getRecipientsNumber(),
            'statusCode' => $notification->getStatus(),
            'links' => $links
        );
    }

    private function handleNotification($notification)
    {
        if ($notification->getPublishDate() < new \DateTime('now')) {
            $notification->setPublishDate(new \DateTime('now'));
        }

        $article = $this->container->get('em')->getRepository('Newscoop\Entity\Article')
                ->getArticle($notification->getArticleNumber(), $notification->getArticleLanguage())
                ->getOneOrNullResult();
        if ($article) {
            $notification->setArticle($article);
        }

        foreach ($notification->getApplications() as $application) {
            $pushHandlerNamespace = $application->getPushHandler()->getNamespace();
            $pushHandler = new $pushHandlerNamespace();

            $response = $pushHandler->sendNotification($notification, $application);
            $notification->addPushHandlerResponse($response);
            $notification->setRecipientsNumber($notification->getRecipientsNumber()+$pushHandler->getRecipientsNumber($response));
        }
        $notification->setStatus(Notification::NOTIFICATION_PROCESSED);
    }
}
