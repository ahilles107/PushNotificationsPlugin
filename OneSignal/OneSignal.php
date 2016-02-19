<?php

namespace AHS\PushNotificationsPluginBundle\OneSignal;

use Guzzle\Http\Client;
use Guzzle\Http\Exception\RequestException;
use Guzzle\Http\Message\Response;
use OneSignal\Exception\OneSignalException;

/**
 * @property-read Apps          $apps          Applications API service.
 * @property-read Devices       $devices       Devices API service.
 * @property-read Notifications $notifications Notifications API service.
 */
class OneSignal
{
    const API_URL = 'https://onesignal.com/api/v1';

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $services = array();

    /**
     * Constructor.
     *
     * @param Config $config
     * @param Client $client
     */
    public function __construct(Config $config = null, Client $client = null)
    {
        $this->config = ($config ?: new Config());
        $this->client = ($client ?: new Client(array(
            'defaults' => array(
                'headers' => array(
                    'Accept' => 'application/json',
                ),
            ),
        )));
    }

    /**
     * Set config.
     *
     * @param Config $config
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Get config.
     *
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set client.
     *
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get client.
     *
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Make a custom api request.
     *
     * @param string $method  HTTP Method
     * @param string $uri     URI template
     * @param array  $options Array of request options to apply.
     *
     * @throws OneSignalException
     *
     * @return Response
     */
    public function request($method, $uri, array $options = array())
    {
        try {
            $request = $this->client->createRequest($method, self::API_URL . $uri, $options['headers'], json_encode($options['json']));

            return $this->client->send($request)->json();
        } catch (RequestException $e) {
            $response = $e->getResponse();
            if ($response) {
                $headers = $response->getHeaders()->toArray();

                if (!empty($headers['Content-Type']) && false !== strpos($headers['Content-Type'][0], 'application/json')) {
                    $body = $response->json();
                    $errors = (isset($body['errors']) ? $body['errors'] : array());

                    if (404 === $response->getStatusCode()) {
                        $errors[] = 'Not Found';
                    }

                    throw new \Exception($response->getMessage(), $e->getCode(), $e);
                }
            }

            throw $e;
        }
    }
}
