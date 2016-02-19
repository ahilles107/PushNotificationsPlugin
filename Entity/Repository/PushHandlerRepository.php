<?php

/*
 * This file is part of the Push Notifications Plugin.
 *
 * (c) Paweł Mikołajczuk <mikolajczuk.private@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AHS\PushNotificationsPluginBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * PushHandler repository
 */
class PushHandlerRepository extends EntityRepository
{
    public function getParsers($activeOnly = true, $returnQb = false)
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->select('p');

        if ($activeOnly) {
            $queryBuilder->where('p.active = :active')->setParameter('active', true);
        }

        if ($returnQb) {
            return $queryBuilder;
        }

        $pushHandlers =$queryBuilder->getQuery()->getResult();

        return $pushHandlers;
    }

    public function getParserNamespaces()
    {
        $query = $this->getEntityManager()
            ->createQuery('SELECT p.namespace FROM '.$this->getEntityName().' AS p');
        $pushHandler = $query->getScalarResult();

        if (empty($pushHandler)) {
            return null;
        }

        return $pushHandler;
    }
}
