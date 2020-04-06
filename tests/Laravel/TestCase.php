<?php

namespace CodeDistortion\TagTools\Tests\Laravel;

use CodeDistortion\TagTools\Laravel\ServiceProvider;
use CodeDistortion\TagTools\Laravel\TagCssFacade;
use CodeDistortion\TagTools\Laravel\TagDnsFacade;
use CodeDistortion\TagTools\Laravel\TagFavFacade;
use CodeDistortion\TagTools\Laravel\TagJsFacade;
use CodeDistortion\TagTools\TagCss;
use CodeDistortion\TagTools\TagDns;
use CodeDistortion\TagTools\TagFav;
use CodeDistortion\TagTools\TagJs;
use Jchook\AssertThrows\AssertThrows;
use Orchestra\Testbench\TestCase as BaseTestCase;

/**
 * The test case that unit tests extend from.
 */
class TestCase extends BaseTestCase
{
    use AssertThrows;

    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app The Laravel app.
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }

    /**
     * Get package aliases.
     *
     * @param \Illuminate\Foundation\Application $app The Laravel app.
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            TagCss::getAlias() => TagCssFacade::class,
            TagDns::getAlias() => TagDnsFacade::class,
            TagFav::getAlias() => TagFavFacade::class,
            TagJs::getAlias() => TagJsFacade::class,
        ];
    }
}
