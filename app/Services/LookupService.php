<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class LookupService
{
    protected $url;
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
        $this->url = false;
    }

    public function minecraft()
    {
        if ($this->username) {
            $this->url = "https://api.mojang.com/users/profiles/minecraft/{$this->username}";
        }
        if ($this->userId) {
            $this->url = "https://sessionserver.mojang.com/session/minecraft/profile/{$this->userId}";
        }
    }

    public function steam()
    {
        if ($this->username) {
            //@TODO - Make this an exception
            return response()->json(
                ['message' => "Unable to complete search, steam only supports IDs"],
                400
            );
        }
        if ($this->userId) {
            $this->url = "https://ident.tebex.io/usernameservices/4/username/{$this->userId}";
        }
    }

    public function xbl()
    {
        if ($this->username) {
            $this->url = "https://ident.tebex.io/usernameservices/3/username/{$this->username}?type=username";
        }
        if ($this->userId) {
            $this->url = "https://ident.tebex.io/usernameservices/3/username/{$this->userId}";
        }
    }

    public function getLookup()
    {
        if (!method_exists($this, $this->type)) {
            //We can't handle this - maybe provide feedback?
            return response()->json(
                ['message' => "Unable to find requested user, {$this->type} is not a recognised type"],
                404
            );
        }
        $this->{$this->type}();
        if (!$this->url) {
            // We've come across an issue where the url has not been set.
            // set error asking for either username or id
            return response()->json(
                ['message' => "Unable to complete search, no username or user id provided"],
                400
            );
        }
        try {
            $match = json_decode($this->http->get($this->url)->getBody()->getContents());
        } catch (\Exception $e) {
            report($e);
            return response()->json(['message' => 'Unable to find requested user'], 404);
        }
        if ($this->type == 'minecraft') {
            $returnUsername = $match->name;
            $avatar = "https://crafatar.com/avatars/{$match->id}";
        } else {
            $returnUsername = $match->username;
            $avatar = $match->meta->avatar;
        }
        return [
            'username' => $returnUsername,
            'id' => $match->id,
            'avatar' => $avatar
        ];
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
