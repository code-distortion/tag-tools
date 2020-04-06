<?php

namespace CodeDistortion\TagTools\Laravel;

use CodeDistortion\TagTools\TagJs;
use Illuminate\Support\Facades\Facade;

class TagJsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return TagJs::getAlias();
    }
}
