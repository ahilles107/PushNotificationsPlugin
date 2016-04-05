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
use AHS\PushNotificationsPluginBundle\Form\SettingsType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SettingsController extends Controller
{
    /**
     * @Route("/admin/pushnotifications/settings/", name="ahs_pushnotificationsplugin_settings")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $user = $this->container->get('user')->getCurrentUser();
        if (!$user->hasPermission('plugin_pushnotifications_settings')) {
            throw new AccessDeniedException();
        }

        $preferencesService = $this->container->get('system_preferences_service');
        $form = $this->createForm(new SettingsType(), array(
            'content_field' => $preferencesService->ahs_pushnotifications_content_field,
            'custom_switches' => $preferencesService->ahs_pushnotifications_custom_switches
        ));
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $data = $form->getData();

                $preferencesService->ahs_pushnotifications_content_field = $data['content_field'];
                $preferencesService->ahs_pushnotifications_custom_switches = $data['custom_switches'];
                $this->get('session')->getFlashBag()->add('success', 'Settings are saved');
            }
        }

        return array(
            'form' => $form->createView()
        );
    }
}
