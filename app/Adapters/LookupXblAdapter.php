<?php

namespace App\Adapters;

class LookupXblAdapter extends LookupGenericAdapter
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
            return "https://ident.tebex.io/usernameservices/3/username/{$this->userId}";
        } elseif ($this->username) {
            return "https://ident.tebex.io/usernameservices/3/username/{$this->username}?type=username";
        }
        throw new \Exception("Unable to complete search, no username or user id provided", 400);
    }
}
