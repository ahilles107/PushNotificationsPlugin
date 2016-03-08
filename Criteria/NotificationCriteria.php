<?php

/*
 * This file is part of the Push Notifications Plugin.
 *
 * (c) Paweł Mikołajczuk <mikolajczuk.private@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @author Paweł Mikołajczuk <mikolajczuk.private@gmail.com>
 */
namespace AHS\PushNotificationsPluginBundle\Criteria;

use Newscoop\Criteria;

/**
 * Available criteria for notifications listing.
 */
class NotificationCriteria extends Criteria
{
    /**
     * @var array
     */
    public $orderBy = array('createdAt' => 'desc');
}
