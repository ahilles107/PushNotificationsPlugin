<?php

namespace AHS\PushNotificationsPluginBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PushNotificationsControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/pushnotifications/Tester');

        $this->assertTrue($crawler->filter('html:contains("Hello Tester")')->count() > 0);
    }
}
