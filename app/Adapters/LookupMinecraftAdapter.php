<?php

namespace App\Adapters;

class LookupMinecraftAdapter extends LookupGenericAdapter
{
    public function formatLookup($match)
    {
        return [
            'username' => $match->name,
            'id' => $match->id,
            'avatar' => "https://crafatar.com/avatars/{$match->id}",
        ];
    }

    public function getUrl()
    {
        if ($this->userId) {
            return "https://sessionserver.mojang.com/session/minecraft/profile/{$this->userId}";
        } elseif ($this->username) {
            return "https://api.mojang.com/users/profiles/minecraft/{$this->username}";
        }
        throw new \Exception("Unable to complete search, no username or user id provided", 400);
    }
}
