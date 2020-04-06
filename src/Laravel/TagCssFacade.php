<?php

namespace CodeDistortion\TagTools\Laravel;

use CodeDistortion\TagTools\TagCss;
use Illuminate\Support\Facades\Facade;

class TagCssFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return TagCss::getAlias();
    }
}
