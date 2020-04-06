<?php

namespace CodeDistortion\TagTools\Laravel;

use CodeDistortion\TagTools\TagFav;
use Illuminate\Support\Facades\Facade;

class TagFavFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return TagFav::getAlias();
    }
}
