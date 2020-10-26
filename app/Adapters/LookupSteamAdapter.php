<?php

namespace App\Adapters;

class LookupSteamAdapter extends LookupGenericAdapter
{
    public function formatLookup($match)
    {
        return [
            'username' => $match->username,
            'id' => $match->id,
            'avatar' =>  $match->meta->avatar,
        ];
    }

    public function getUrl()
    {
        if ($this->userId) {
            return "https://ident.tebex.io/usernameservices/4/username/{$this->userId}";
        } elseif ($this->username) {
            throw new \Exception('Unable to complete search, steam only supports IDs', 400);
        }
        throw new \Exception("Unable to complete search, no username or user id provided", 400);
    }
}
