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
 * Subscription repository
 */
class SubscriptionRepository extends EntityRepository
{
    public function getSubscription($playerId, $articleNumber, $articleLanguage, $commentId = null)
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.articleNumber = :articleNumber')
            ->andWhere('s.articleLanguage = :articleLanguage')
            ->andWhere('s.playerId = :playerId')
            ->setParameters(array(
                'articleNumber' => $articleNumber,
                'articleLanguage' => $articleLanguage,
                'playerId' => $playerId
            ))
            ->orderBy('s.createdAt', 'desc');

        if (null !== $commentId) {
            $queryBuilder->andWhere('s.commentId = :commentId')
                ->setPrameter('commentId', $commentId);
        }

        return $queryBuilder->getQuery();
    }

    public function getSubscriptionsForThread($articleNumber, $articleLanguage)
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.articleNumber = :articleNumber')
            ->andWhere('s.articleLanguage = :articleLanguage')
            ->setParameters(array(
                'articleNumber' => $articleNumber,
                'articleLanguage' => $articleLanguage,
            ))
            ->orderBy('s.createdAt', 'desc');

        return $queryBuilder->getQuery();
    }
}
