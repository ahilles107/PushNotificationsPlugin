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
use AHS\PushNotificationsPluginBundle\Criteria\NotificationCriteria;

/**
 * Notification repository
 */
class NotificationRepository extends EntityRepository
{
    public function getAllByArticle($articleNumber, $articleLanguage)
    {
        $queryBuilder = $this->createQueryBuilder('n')
            ->select('n')
            ->where('n.articleNumber = :articleNumber')
            ->andWhere('n.articleLanguage = :articleLanguage')
            ->setParameters(array(
                'articleNumber' => $articleNumber,
                'articleLanguage' => $articleLanguage
            ))
            ->orderBy('n.createdAt', 'desc');

        return $queryBuilder->getQuery();
    }

    /**
     * Get list for given criteria.
     *
     * @param AHS\PushNotificationsPluginBundle\Criteria\NotificationCriteria $criteria
     *
     * @return Newscoop\ListResult
     */
    public function getListByCriteria(NotificationCriteria $criteria, $showResults = true)
    {
        $qb = $this->createQueryBuilder('n');
        $list = new \Newscoop\ListResult();

        $qb->select('n');

        if ($criteria->query) {
            $qb->andWhere($qb->expr()->orX('(n.title LIKE :query)', '(n.content LIKE :query)'));
            $qb->setParameter('query', '%'.trim($criteria->query, '%').'%');
        }

        foreach ($criteria->perametersOperators as $key => $operator) {
            $qb->andWhere('n.'.$key.' '.$operator.' :'.$key)
                ->setParameter($key, $criteria->$key);
        }

        $countQb = clone $qb;
        if ($criteria->firstResult != 0) {
            $qb->setFirstResult($criteria->firstResult);
        }

        if ($criteria->maxResults != 0) {
            $qb->setMaxResults($criteria->maxResults);
        }

        $metadata = $this->getClassMetadata();
        foreach ($criteria->orderBy as $key => $order) {
            if (array_key_exists($key, $metadata->columnNames)) {
                $key = 'n.'.$key;
            }

            $qb->orderBy($key, $order);
        }

        $list->count = (int) $countQb->select('COUNT(DISTINCT n)')->getQuery()->getSingleScalarResult();
        if (!$showResults) {
            return array($qb->getQuery(), $list->count);
        }

        $list->items = $qb->getQuery()->getResult();

        return $list;
    }
}
