<?php

namespace App\Adapters;

use App\Interfaces\LookupAdapterInterface;

class LookupGenericAdapter implements LookupAdapterInterface
{

    protected $userId;
    protected $username;

    public function setId($userId)
    {
        $this->userId = $userId;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function formatLookup($match)
    {
        return $match;
    }

    public function getUrl()
    {
        throw new \Exception("Unable to complete search, no username or user id provided", 400);
    }
}
