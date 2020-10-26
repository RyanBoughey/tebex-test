<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class LookupService
{
    protected $url = false;
    protected $http;
    protected $type;
    protected $userId;
    protected $username;

    public function __construct(Request $request, Client $client)
    {
        $this->type = $request->get('type');
        $this->username = ($request->get('username') ? $request->get('username') : false);
        $this->userId = ($request->get('id') ? $request->get('id') : false);
        $this->http = $client;
    }

    public function getLookup()
    {
        $this->checkType();
        return $this->{$this->type}();
    }

    public function minecraft()
    {
        if ($this->username) {
            $this->url = "https://api.mojang.com/users/profiles/minecraft/{$this->username}";
        }
        if ($this->userId) {
            $this->url = "https://sessionserver.mojang.com/session/minecraft/profile/{$this->userId}";
        }
        $this->checkUrl();
        $match = $this->doLookup();
        return [
            'username' => $match->name,
            'id' => $match->id,
            'avatar' => "https://crafatar.com/avatars/{$match->id}",
        ];
    }

    public function steam()
    {
        if ($this->username) {
            //@TODO - Make this an exception
            throw new \Exception('Unable to complete search, steam only supports IDs', 400);
        }
        if ($this->userId) {
            $this->url = "https://ident.tebex.io/usernameservices/4/username/{$this->userId}";
        }
        $this->checkUrl();
        $match = $this->doLookup();
        return [
            'username' => $match->username,
            'id' => $match->id,
            'avatar' =>  $match->meta->avatar,
        ];
    }

    public function xbl()
    {
        if ($this->username) {
            $this->url = "https://ident.tebex.io/usernameservices/3/username/{$this->username}?type=username";
        }
        if ($this->userId) {
            $this->url = "https://ident.tebex.io/usernameservices/3/username/{$this->userId}";
        }
        $this->checkUrl();
        $match = $this->doLookup();
        return [
            'username' => $match->username,
            'id' => $match->id,
            'avatar' =>  $match->meta->avatar,
        ];
    }

    public function doLookup()
    {
            return json_decode($this->http->get($this->url)->getBody()->getContents());
    }

    public function checkUrl()
    {
        if (!$this->url) {
            throw new \Exception("Unable to complete search, no username or user id provided", 400);
        }
    }

    public function checkType()
    {
        if (!method_exists($this, $this->type)) {
            throw new \Exception("{$this->type} is not a recognised type", 400);
        }
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }
}
