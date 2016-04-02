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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use AHS\PushNotificationsPluginBundle\Form\ApplicationType;
use AHS\PushNotificationsPluginBundle\Entity\Application;
use AHS\PushNotificationsPluginBundle\Form\NotificationType;
use AHS\PushNotificationsPluginBundle\Entity\Notification;
use AHS\PushNotificationsPluginBundle\Criteria\NotificationCriteria;

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
     * @Route("/admin/pushnotifications/notifications/{notificationId}/duplicate", name="ahs_pushnotificationsplugin_notification_duplicate")
     * @Template()
     * @Method("GET|POST")
     */
    public function createNotificationAction(Request $request, $notificationId = null)
    {
        $user = $this->container->get('user')->getCurrentUser();
        if (!$user->hasPermission('plugin_pushnotifications_create')) {
            throw new AccessDeniedException();
        }

        $em = $this->container->get('em');
        $notification = new Notification();

        if ($notificationId) {
            $notification = clone $em->getRepository('AHS\PushNotificationsPluginBundle\Entity\Notification')
                ->findOneById($notificationId);
            $notification->setCreatedAt(new \DateTime('now'));
            $notification->setCreatedAt(new \DateTime('now'));
            $notification->setPublishDate(new \DateTime('now'));
            $notification->setPushHandlerResponse(array());
            $notification->setRecipientsNumber(0);
            $notification->setStatus(Notification::NOTIFICATION_NOTPROCESSED);
        }

        $form = $this->createForm(new NotificationType(), $notification);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $notification->setUser($user);
                $notification->setIsActive(true);
                $notification->addPushHandlerResponse(array());
                $em->persist($notification);

                if ($user->hasPermission('plugin_pushnotifications_publish') && !$form->get('schedule')->isClicked()) {
                    $this->handleNotification($notification);
                }

                $em->flush();

                if($request->isXmlHttpRequest()) {
                    $notification->setApplications(null);
                    return new Response($this->get('jms_serializer')->serialize($notification, 'json'));
                }

                return new RedirectResponse($this->generateUrl('ahs_pushnotificationsplugin_admin_index'));
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/admin/pushnotifications/notifications/{notificationId}/edit", name="ahs_pushnotificationsplugin_notification_edit")
     * @Template("AHSPushNotificationsPluginBundle:Admin:createNotification.html.twig")
     * @Method("GET|POST")
     */
    public function editNotificationAction(Request $request, $notificationId)
    {
        $user = $this->container->get('user')->getCurrentUser();
        if (!$user->hasPermission('plugin_pushnotifications_create')) {
            throw new AccessDeniedException();
        }

        $em = $this->container->get('em');
        $notification = $em->getRepository('AHS\PushNotificationsPluginBundle\Entity\Notification')
            ->findOneById($notificationId);

        if (!$notificationId || $notification->getStatus() !== Notification::NOTIFICATION_NOTPROCESSED) {
            return new RedirectResponse($this->generateUrl('ahs_pushnotificationsplugin_admin_index'));
        }

        $form = $this->createForm(new NotificationType(), $notification);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                if ($user->hasPermission('plugin_pushnotifications_publish') && !$form->get('schedule')->isClicked()) {
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
     * @Route("/admin/pushnotifications/notifications/{notificationId}/reject", name="ahs_pushnotificationsplugin_notification_reject")
     * @Method("POST|GET")
     */
    public function rejectNotificationAction($notificationId)
    {
        $user = $this->container->get('user')->getCurrentUser();
        if (!$user->hasPermission('plugin_pushnotifications_publish')) {
            throw new AccessDeniedException();
        }

        $em = $this->container->get('em');
        $notification = $em->getRepository('AHS\PushNotificationsPluginBundle\Entity\Notification')
            ->findOneById($notificationId);
        $notification->setStatus(Notification::NOTIFICATION_REJECTED);
        $em->flush();

        return new RedirectResponse($this->generateUrl('ahs_pushnotificationsplugin_admin_index'));
    }

    /**
     * @Route("/admin/pushnotifications/handlers/refresh", name="ahs_pushnotificationsplugin_handlers_refresh")
     * @Method("POST|GET")
     */
    public function refreshPushHandlersAction()
    {
        $user = $this->container->get('user')->getCurrentUser();
        if (!$user->hasPermission('plugin_pushnotifications_settings')) {
            throw new AccessDeniedException();
        }

        $pushHandlerService = $this->container->get('ahs_pushnotifications_plugin.service.push_handler');
        $pushHandlerService->refreshPushHandlers();

        return new JsonResponse(array('status' => 'OK'));
    }

    /**
     * @Route("/admin/pushnotifications/load.json", options={"expose"=true}, name="ahs_pushnotificationsplugin_notification_load")
     */
    public function loadNotificationsAction(Request $request)
    {
        $em = $this->container->get('em');
        $zendRouter = $this->container->get('zend_router');
        $userService = $this->container->get('user');
        $user = $userService->getCurrentUser();

        $criteria = $this->processRequest($request);
        $notifications = $em->getRepository('AHS\PushNotificationsPluginBundle\Entity\Notification')->getListByCriteria($criteria);
        $pocessed = array();
        foreach ($notifications as $notification) {
            $pocessed[] = $this->processNotification($notification, $zendRouter, $this->container->get('translator'), $user);
        }
        $responseArray = array(
            'records' => $pocessed,
            'queryRecordCount' => count($notifications),
            'totalRecordCount' => count($notifications),
        );

        return new JsonResponse($responseArray);
    }

    /**
     * Process request parameters.
     *
     * @param Request $request Request object
     *
     * @return AnnouncementCriteria
     */
    private function processRequest(Request $request)
    {
        $criteria = new NotificationCriteria();

        if ($request->query->has('sorts')) {
            foreach ($request->get('sorts') as $key => $value) {
                $criteria->orderBy[$key] = $value == '-1' ? 'desc' : 'asc';
            }
        }

        if ($request->query->has('queries')) {
            $queries = $request->query->get('queries');

            if (array_key_exists('search', $queries)) {
                $criteria->query = $queries['search'];
            }
        }

        $criteria->maxResults = $request->query->get('perPage', 10);
        if ($request->query->has('offset')) {
            $criteria->firstResult = $request->query->get('offset');
        }

        return $criteria;
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
            Notification::NOTIFICATION_ERRORED => $translator->trans('pushnotifications.statuses.errored'),
            Notification::NOTIFICATION_REJECTED => $translator->trans('pushnotifications.statuses.rejected'),
        );

        $links = array();
        if ($user->hasPermission('plugin_pushnotifications_publish')) {
            $links[] = array(
                'rel' => 'accept',
                'href' => $this->generateUrl('ahs_pushnotificationsplugin_notification_accept', array(
                    'notificationId' => $notification->getId()
                ))
            );

            if ($notification->getStatus() == Notification::NOTIFICATION_NOTPROCESSED) {
                $links[] = array(
                    'rel' => 'reject',
                    'href' => $this->generateUrl('ahs_pushnotificationsplugin_notification_reject', array(
                        'notificationId' => $notification->getId()
                    ))
                );
                $links[] = array(
                    'rel' => 'edit',
                    'href' => $this->generateUrl('ahs_pushnotificationsplugin_notification_edit', array(
                        'notificationId' => $notification->getId()
                    ))
                );
            }

            if ($notification->getStatus() == Notification::NOTIFICATION_PROCESSED) {
                $links[] = array(
                    'rel' => 'duplicate',
                    'href' => $this->generateUrl('ahs_pushnotificationsplugin_notification_duplicate', array(
                        'notificationId' => $notification->getId()
                    ))
                );
            }
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
            $date = new \DateTime('now');
            $notification->setPublishDate($date->modify('+1 minute'));
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
            if (is_null($response)) {
                $response = array();
            }
            $notification->addPushHandlerResponse($response);
            $notification->setRecipientsNumber($notification->getRecipientsNumber()+$pushHandler->getRecipientsNumber($response));
        }
        $notification->setStatus(Notification::NOTIFICATION_PROCESSED);
    }
}
