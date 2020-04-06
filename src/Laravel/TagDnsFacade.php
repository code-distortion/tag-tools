<?php

namespace CodeDistortion\TagTools\Laravel;

use CodeDistortion\TagTools\TagDns;
use Illuminate\Support\Facades\Facade;

class TagDnsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return TagDns::getAlias();
    }
}
