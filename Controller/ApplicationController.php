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
use Symfony\Component\HttpFoundation\Request;
use AHS\PushNotificationsPluginBundle\Form\ApplicationType;
use AHS\PushNotificationsPluginBundle\Entity\Application;
use AHS\PushNotificationsPluginBundle\Form\PushHandlerSettingsType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApplicationController extends Controller
{
    /**
     * @Route("/admin/pushnotifications/applications/", name="ahs_pushnotificationsplugin_applications_index")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        return array();
    }

    /**
     * @Route("/admin/pushnotifications/applications/create", name="ahs_pushnotificationsplugin_applications_create")
     * @Template()
     */
    public function createApplicationAction(Request $request)
    {
        $em = $this->container->get('em');
        $application = new Application();

        $form = $this->createForm(new ApplicationType(), $application);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($application);
                $em->flush();
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/admin/pushnotifications/applications/{applicationId}/edit", name="ahs_pushnotificationsplugin_applications_edit")
     * @Template("AHSPushNotificationsPluginBundle:Application:createApplication.html.twig")
     */
    public function editApplicationAction(Request $request, $applicationId)
    {
        $em = $this->container->get('em');
        $application = $em->getRepository('AHS\PushNotificationsPluginBundle\Entity\Application')
            ->findOneById($applicationId);

        $form = $this->createForm(new ApplicationType(), $application);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($application);
                $em->flush();

                return new RedirectResponse($this->generateUrl('ahs_pushnotificationsplugin_applications_pushhandler_settings', array(
                    'applicationId' => $application->getId()
                )));
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/admin/pushnotifications/applications/{applicationId}/pushHandlerSettings", name="ahs_pushnotificationsplugin_applications_pushhandler_settings")
     * @Template()
     */
    public function applicationPushHandlerSettingsAction(Request $request, $applicationId)
    {
        $em = $this->container->get('em');
        $application = $em->getRepository('AHS\PushNotificationsPluginBundle\Entity\Application')->findOneById($applicationId);
        $pushHandlerNamespace = $application->getPushHandler()->getNamespace();
        $pushHandler = new $pushHandlerNamespace();
        $pushHandlerRequiredSettings = $pushHandler->getPushHandlerRequiredSettings();

        foreach ($application->getPushHandlerSettings() as $key => $value) {
            $pushHandlerRequiredSettings[$key] = $value;
        }

        $form = $this->createForm(new PushHandlerSettingsType(), $pushHandlerRequiredSettings);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $application->setPushHandlerSettings($form->getData());
                $em->flush();

                return new RedirectResponse($this->generateUrl('ahs_pushnotificationsplugin_admin_index'));
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/admin/pushnotifications/applications/load.json", options={"expose"=true}, name="ahs_pushnotificationsplugin_applications_load")
     */
    public function loadApplicationsAction(Request $request)
    {
        $em = $this->container->get('em');
        $zendRouter = $this->container->get('zend_router');
        $userService = $this->container->get('user');
        $user = $userService->getCurrentUser();

        $applications = $em->getRepository('AHS\PushNotificationsPluginBundle\Entity\Application')->findAll();
        $pocessed = array();
        foreach ($applications as $application) {
            $pocessed[] = $this->processApplications($application, $zendRouter);
        }

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
     * @param Application  $application
     * @param Zend_Router   $zendRouter
     *
     * @return array
     */
    private function processApplications(Application $application, $zendRouter)
    {
        $links = array();
        $links[] = array(
            'rel' => 'edit',
            'href' => $this->generateUrl('ahs_pushnotificationsplugin_applications_edit', array(
                'applicationId' => $application->getId()
            ))
        );

        return array(
            'id' => $application->getId(),
            'title' => $application->getTitle(),
            'pushhandler' => array(
                'name' => $application->getPushHandler()->getName(),
                'description' => $application->getPushHandler()->getDescription(),
            ),
            'links' => $links
        );
    }
}
