<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Adapters\LookupMinecraftAdapter;
use App\Adapters\LookupSteamAdapter;
use App\Adapters\LookupXblAdapter;

class LookupService
{
    protected $url;
    protected $http;
    protected $type = false;
    protected $userId = null;
    protected $username = null;
    protected $adapter;

    public function __construct(Request $request, Client $client)
    {
        $this->setType(($request->get('type') ? $request->get('type') : false));
        $this->setUsername(($request->get('username') ? $request->get('username') : false));
        $this->setId(($request->get('id') ? $request->get('id') : false));
        $this->http = $client;
    }

    private function getAdapter($type)
    {
        switch ($type) {
            case 'minecraft':
                // use LookupMinecraftAdapter
                $this->adapter = new LookupMinecraftAdapter();
                break;
            case 'steam':
                // use LookupSteamAdapter
                $this->adapter = new LookupSteamAdapter();
                break;
            case 'xbl':
                // use LookupXblAdapter
                $this->adapter = new LookupXblAdapter();
                break;
            default:
                throw new \Exception("{$type} is not a recognised type", 400);
                break;
        }
    }

    public function getLookup()
    {
        $this->url = $this->adapter->getUrl();
        $match = $this->doLookup();
        return $this->adapter->formatLookup($match);
    }

    public function doLookup()
    {
            return json_decode($this->http->get($this->url)->getBody()->getContents());
    }

    public function setType($type)
    {
        $this->type = $type;
        $this->getAdapter($this->type);
        if (empty($this->adapter->username)) {
            $this->adapter->setUsername($this->username);
        }
        if (empty($this->adapter->userId)) {
            $this->adapter->setUsername($this->userId);
        }
    }

    public function setUsername($username)
    {
        $this->username = $username;
        $this->adapter->setUsername($this->username);
    }

    public function setId($userId)
    {
        $this->userId = $userId;
        $this->adapter->setId($this->userId);
    }
}
