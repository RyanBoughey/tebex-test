<?php

namespace App\Interfaces;

interface LookupAdapterInterface
{
    public function setId($userId);
    public function setUsername($username);
    public function formatLookup($match);
    public function getUrl();
}
