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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Newscoop\EventDispatcher\Events\GenericEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Event lifecycle management
 */

class LifecycleSubscriber implements EventSubscriberInterface
{
    private $container;

    protected $em;

    protected $scheduler;

    protected $cronjobs;

    protected $preferences;

    protected $pluginsService;

    protected $translator;

    public function __construct(ContainerInterface $container)
    {
        $appDirectory = realpath(__DIR__.'/../../../../application/console');
        $this->container = $container;
        $this->em = $this->container->get('em');
        $this->scheduler = $this->container->get('newscoop.scheduler');
        $this->preferences = $this->container->get('system_preferences_service');
        $this->pluginsService = $this->container->get('newscoop.plugins.service');
        $this->translator = $this->container->get('translator');
        $this->cronjobs = array();
    }

    public function install(GenericEvent $event)
    {
        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $tool->updateSchema($this->getClasses(), true);
        $this->setPermissions();

        // Set default preferences here

        // Generate proxies for entities
        $this->em->getProxyFactory()->generateProxyClasses($this->getClasses(), __DIR__ . '/../../../../library/Proxy');
        $this->addJobs();
    }

    public function update(GenericEvent $event)
    {
        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $tool->updateSchema($this->getClasses(), true);
        $this->setPermissions();

        // Generate proxies for entities
        $this->em->getProxyFactory()->generateProxyClasses($this->getClasses(), __DIR__ . '/../../../../library/Proxy');
    }

    public function remove(GenericEvent $event)
    {
        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $tool->dropSchema($this->getClasses(), true);
        $this->removeJobs();
        $this->removePermissions();
    }

    public static function getSubscribedEvents()
    {
        return array(
            'plugin.install.ahs_pushnotifications_plugin_bundle' => array('install', 1),
            'plugin.update.ahs_pushnotifications_plugin_bundle' => array('update', 1),
            'plugin.remove.ahs_pushnotifications_plugin_bundle' => array('remove', 1),
        );
    }

    /**
     * Add plugin cron jobs
     */
    private function addJobs()
    {
        foreach ($this->cronjobs as $jobName => $jobConfig) {
            $this->scheduler->registerJob($jobName, $jobConfig);
        }
    }

    /**
     * Remove plugin cron jobs
     */
    private function removeJobs()
    {
        foreach ($this->cronjobs as $jobName => $jobConfig) {
            $this->scheduler->removeJob($jobName, $jobConfig);
        }
    }

    private function getClasses()
    {
        return array(
            $this->em->getClassMetadata('AHS\PushNotificationsPluginBundle\Entity\Application'),
            $this->em->getClassMetadata('AHS\PushNotificationsPluginBundle\Entity\Notification'),
            $this->em->getClassMetadata('AHS\PushNotificationsPluginBundle\Entity\PushHandler'),
            $this->em->getClassMetadata('AHS\PushNotificationsPluginBundle\Entity\Subscription'),
        );
    }

    /**
     * Save plugin permissions into database.
     */
    private function setPermissions()
    {
        $this->pluginsService->savePluginPermissions($this->pluginsService->collectPermissions($this->translator->trans('pushnotifications.menu.name')));
    }

    /**
     * Remove plugin permissions.
     */
    private function removePermissions()
    {
        $this->pluginsService->removePluginPermissions($this->pluginsService->collectPermissions($this->translator->trans('pushnotifications.menu.name')));
    }
}
