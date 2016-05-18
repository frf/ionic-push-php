<?php

namespace Fabiorf\IonicPush;

use Fabiorf\IonicPush\Exception\BadRequestException;
use Fabiorf\IonicPush\Exception\PermissionDeniedException;
use Fabiorf\IonicPush\Exception\RequestException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;

/**
 * Class PushProcessor
 *
 * @package Fabiorf\IonicPush
 */
class PushProcessor
{
    /** @var string */
    protected $appId;

    /** @var string Description:  API Tokens */
    protected $apiToken;

    /** @var string */
    protected $ionicPushEndPoint;

    /** @var string Default: fake_push_profile you create in ionic.io Cirtificates*/
    /*
     * @TODO Create certificates in ionic.io fake_push_profile or other name and set in construct method
     */
    protected $appProfile;

    /**
     * @param string $appId
     * @param string $appApiSecret
     * @param string $ionicPushEndPoint
     */
    public function __construct(
        $appId,
        $apiToken,
        $appProfile = 'fake_push_profile',
        $ionicPushEndPoint = 'https://api.ionic.io/push/notifications'
    ) {
        $this->appId = $appId;
        $this->apiToken = $apiToken;
        $this->appProfile = $appProfile;
        $this->ionicPushEndPoint = $ionicPushEndPoint;
    }

    public function getAppId()
    {
        return $this->appId;
    }

    protected function getAppApiToken()
    {
        return $this->apiToken;
    }

    public function getAppApiProfile()
    {
        return $this->appProfile;
    }



    public function getPushEndPoint()
    {
        return $this->ionicPushEndPoint;
    }

    public function setPushEndPoint($ionicPushEndpoint)
    {
        $this->ionicPushEndPoint = $ionicPushEndpoint;
    }

    /**
     * @param array $devices
     * @param array $notification
     *
     * @return mixed
     */
    public function notify(array $devices, array $notification)
    {
        $headers   = $this->getNotificationHeaders();
        $profile   = $this->getAppApiProfile();
        $body      = $this->getNotificationBody($devices,$profile,$notification);

        return $this->sendRequest($headers, $body);
    }

    protected function getNotificationHeaders()
    {
        $appToken = $this->getAppApiToken();
        $authorization = sprintf("Bearer %s", $appToken);

        return array(
            'Authorization'          => $authorization,
            'Content-Type'           => 'application/json',
            'X-Ionic-Application-Id' => $this->appId
        );
    }

    /**
     * @param array $devices
     * @param array $notification
     *
     * @return string
     */
    protected function getNotificationBody(array $devices, $profile, array $notification)
    {
        $body = array(
            'tokens'        => $devices,
            'profile'       => $profile,
            'notification'  => $notification
        );

        return json_encode($body);
    }

    /**
     * @param $headers
     * @param $body
     *
     * @return mixed|null
     * @throws BadRequestException
     * @throws PermissionDeniedException
     * @throws RequestException
     */
    protected function sendRequest($headers, $body)
    {
        $request = new Request(
            'POST',
            $this->ionicPushEndPoint,
            $headers,
            $body
        );
        $client = new Client();

        try {
            $response = $client->send($request);
            return $response;
        } catch (ClientException $e) {
            switch ($e->getCode()) {
                case 403: {
                    throw new PermissionDeniedException(
                        "Permission denied sending push", 403, $e
                    );
                }
                case 400: {
                    throw new BadRequestException(
                        "Bad request sending push", 400, $e
                    );
                }
            }
        } catch (\Exception $e) {
            throw new RequestException(
                "An error occurred when sending push request with message: {$e->getMessage()}",
                $e->getCode(),
                $e
            );
        }

        return null;
    }
}
