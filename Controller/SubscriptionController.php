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

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use AHS\PushNotificationsPluginBundle\Form\SubscriptionType;
use AHS\PushNotificationsPluginBundle\Entity\Subscription;
use Doctrine\ORM\EntityNotFoundException;
use Newscoop\Exception\ResourcesConflictException;

class SubscriptionController extends FOSRestController
{
    /**
     * @Route("/ahs/pushnotifications/subscription.{_format}", defaults={"_format"="json"}, options={"expose"=true}, name="ahs_pushnotifications_subscription_create")
     * @Method("POST")
     * @View(serializerGroups={"details"})
     */
    public function createAction(Request $request)
    {
        $em = $this->container->get('em');
        $subscription = new Subscription();

        $form = $this->createForm(new SubscriptionType(), $subscription);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $existingSubscription = $em->getRepository('AHS\PushNotificationsPluginBundle\Entity\Subscription')
                ->getSubscription(
                    $subscription->getPlayerId(),
                    $subscription->getArticleNumber(),
                    $subscription->getArticleLanguage(),
                    $subscription->getCommentId()
                )
                ->getOneOrNullResult();

            if (null !== $existingSubscription) {
                throw new ResourcesConflictException("This subscription already exists");
            }

            $em->persist($subscription);
            $em->flush();

            return $subscription;
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/ahs/pushnotifications/subscription/{playerId}/{articleNumber}-{articleLanguage}-{commentId}.{_format}", defaults={"_format"="json"}, options={"expose"=true}, name="ahs_pushnotifications_subscription_delete")
     * @Method("DELETE")
     * @View()
     */
    public function deleteAction(Request $request, $playerId, $articleNumber, $articleLanguage, $commentId = null)
    {
        $em = $this->container->get('em');
        $existingSubscription = $em->getRepository('AHS\PushNotificationsPluginBundle\Entity\Subscription')
            ->getSubscription(
                $playerId,
                $articleNumber,
                $articleLanguage,
                $commentId
            )
            ->getOneOrNullResult();

        if (null === $existingSubscription) {
            throw new EntityNotFoundException('Subscription was not found');
        }

        $em->remove($existingSubscription);
        $em->flush();

        return array('status' => 'success');
    }

    /**
     * @Route("/ahs/pushnotifications/subscription/{playerId}.{_format}", defaults={"_format"="json"}, options={"expose"=true}, name="ahs_pushnotifications_subscription_get")
     * @Method("GET")
     * @View(serializerGroups={"details"})
     */
    public function getAction(Request $request, $playerId)
    {
        $em = $this->container->get('em');

        $subscription = $em->getRepository('AHS\PushNotificationsPluginBundle\Entity\Subscription')->findOneBy(array('playerId' => $playerId));
        if (!$subscription) {
            throw new EntityNotFoundException('Subscription was not found');
        }

        return $subscription;
    }
}
