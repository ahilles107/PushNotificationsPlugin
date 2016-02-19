<?php

/*
 * This file is part of the Push Notifications Plugin.
 *
 * (c) Paweł Mikołajczuk <mikolajczuk.private@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace AHS\PushNotificationsPluginBundle\Service;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use AHS\PushNotificationsPluginBundle\Entity\PushHandler;

class PushHandlerService
{
    /**s
    * @param $doctrine
    */
    public function __construct($doctrine)
    {
        $this->em = $doctrine->getManager();
    }

    /**
    * Refresh and register Push Handlers
    *
    * @return void
    *
    * @throws Exception
    */
    public function refreshPushHandlers()
    {
      $fs = new Filesystem();
      $finder = new Finder();
      try {
          $pluginsDir = __DIR__ . '/../../../';
          $namespaces = array();
          $iterator = $finder
              ->ignoreUnreadableDirs()
              ->files()
              ->name('*Handler.php')
              ->notName('AbstractPushHandler.php');
          try {
              $iterator = $iterator
                  ->in($pluginsDir . '*/*/PushHandlers/');
          } catch(\Exception $e) {/*Catch exception if no such directory exists*/}

          foreach ($iterator as $file) {
              $classNamespace = str_replace(realpath($pluginsDir), '', substr($file->getRealPath(), 0, -4));
              $namespace = str_replace('/', '\\', $classNamespace);
              $namespaces[] = (string) $namespace;
              $parserName = substr($file->getFilename(), 0, -4);
              $pushHandler = $this->em->getRepository('AHS\PushNotificationsPluginBundle\Entity\PushHandler')
                  ->findOneByNamespace($namespace);

              if (!$pushHandler) {
                  $pushHandler = new PushHandler();
              }

              $pushHandler
                  ->setName($namespace::getPushHandlerName())
                  ->setDescription($namespace::getPushHandlerDescription())
                  ->setNamespace($namespace);
              $this->em->persist($pushHandler);
          }

          // Remove parser which we didn't find anymore
          $parsersToRemove = $this->em
              ->createQuery('
                  DELETE FROM AHS\PushNotificationsPluginBundle\Entity\PushHandler AS p
                  WHERE p.namespace NOT IN (:namespaces)
              ')
              ->setParameter('namespaces', $namespaces)
              ->getResult();

          $this->em->flush();
      } catch (\Exception $e) {
          throw new \Exception($e->getMessage());
      }
    }
}
