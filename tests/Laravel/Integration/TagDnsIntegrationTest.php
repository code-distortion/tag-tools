<?php

namespace CodeDistortion\TagTools\Tests\Laravel\Integration;

use App;
use CodeDistortion\TagTools\Laravel\Middleware;
use CodeDistortion\TagTools\Settings;
use CodeDistortion\TagTools\Tests\Laravel\TestCase;
use Config;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use TagDns;

/**
 * Test TagJs' Laravel integration.
 *
 * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */
class TagDnsIntegrationTest extends TestCase
{
    /**
     * Provide input for the test_that_middleware_puts_js_in test
     *
     * @return array[]
     */
    public function middlewareOutputDataProvider(): array
    {
        return [

            'test1' => [
                'view' => 'test::view-dns-1',
                'callback' => function () {
                    TagDns::dnsPrefetch('//test.com/');
                },
                'expectedContains' =>
                    '<title>View 1</title>'.PHP_EOL
                    .'        <link rel="dns-prefetch" href="//test.com/" />'.PHP_EOL
                    .PHP_EOL
                    .'    </head>'.PHP_EOL,
            ],

            'test2' => [
                'view' => 'test::view-dns-2',
                'callback' => function () {
                    TagDns::dnsPrefetch('//test.com/');
                },
                'expectedContains' =>
                    '<title>View 2</title>'.PHP_EOL
                    .'        <link rel="dns-prefetch" href="//test.com/" />'.PHP_EOL
                    .'    </head>'.PHP_EOL,
            ],

            'test3' => [
                'view' => 'test::view-dns-3',
                'callback' => function () {
                },
                'expectedContains' =>
                    '<title>View 3</title>'.PHP_EOL
                    .'        <link rel="dns-prefetch" href="https://test1.com/" />'.PHP_EOL
                    .'        <link rel="dns-prefetch" href="http://test2.com/" />'.PHP_EOL
                    .'        <link rel="dns-prefetch" href="//test3.com/" />'.PHP_EOL
                    .'        <link rel="dns-prefetch" href="http://javascript.com/" />'.PHP_EOL
                    .'    </head>'.PHP_EOL,
            ],

            'test4' => [
                'view' => 'test::view-dns-3',
                'callback' => function () {
                    Config::set(Settings::LARAVEL_CONFIG_NAME.'.dns_prefetch_tags', ['*']);
                },
                'expectedContains' =>
                    '<title>View 3</title>'.PHP_EOL
                    .'        <link rel="dns-prefetch" href="https://test1.com/" />'.PHP_EOL
                    .'        <link rel="dns-prefetch" href="http://test2.com/" />'.PHP_EOL
                    .'        <link rel="dns-prefetch" href="//test3.com/" />'.PHP_EOL
                    .'        <link rel="dns-prefetch" href="http://javascript.com/" />'.PHP_EOL
                    .'    </head>'.PHP_EOL,
            ],

            'test5' => [
                'view' => 'test::view-dns-3',
                'callback' => function () {
                    Config::set(Settings::LARAVEL_CONFIG_NAME.'.dns_prefetch_tags', []);
                },
                'expectedContains' =>
                    '<title>View 3</title>'.PHP_EOL
                    .'            </head>'.PHP_EOL,
            ],

            'test6' => [
                'view' => 'test::view-dns-3',
                'callback' => function () {
                    Config::set(Settings::LARAVEL_CONFIG_NAME.'.dns_prefetch_tags', null);
                },
                'expectedContains' =>
                    '<title>View 3</title>'.PHP_EOL
                    .'            </head>'.PHP_EOL,
            ],

            'test7' => [
                'view' => 'test::view-dns-3',
                'callback' => function () {
                    Config::set(Settings::LARAVEL_CONFIG_NAME.'.dns_prefetch_tags', ['blah']);
                },
                'expectedContains' =>
                    '<title>View 3</title>'.PHP_EOL
                    .'            </head>'.PHP_EOL,
            ],


            'test8' => [
                'view' => 'test::view-dns-1',
                'callback' => function () {
                    TagDns::preConnect('//test.com/');
                },
                'expectedContains' =>
                    '<title>View 1</title>'.PHP_EOL
                    .'        <link rel="preconnect" href="//test.com/" />'.PHP_EOL
                    .PHP_EOL
                    .'    </head>'.PHP_EOL,
            ],


            'test9' => [
                'view' => 'test::view-dns-1',
                'callback' => function () {
                    TagDns::preFetch('//test.com/');
                },
                'expectedContains' =>
                    '<title>View 1</title>'.PHP_EOL
                    .'        <link rel="prefetch" href="//test.com/" />'.PHP_EOL
                    .PHP_EOL
                    .'    </head>'.PHP_EOL,
            ],


            'test10' => [
                'view' => 'test::view-dns-1',
                'callback' => function () {
                    TagDns::preRender('//test.com/');
                },
                'expectedContains' =>
                    '<title>View 1</title>'.PHP_EOL
                    .'        <link rel="prerender" href="//test.com/" />'.PHP_EOL
                    .PHP_EOL
                    .'    </head>'.PHP_EOL,
            ],
        ];
    }

    /**
     * Test that the middleware replaces the content properly.
     *
     * @test
     * @dataProvider middlewareOutputDataProvider
     *
     * @param string          $view             The blade template to use.
     * @param callable        $callback         A callback that will apply various javascript.
     * @param string|string[] $expectedContains The expected output.
     * @return void
     */
    public function test_that_middleware_puts_dns_in(
        string $view,
        callable $callback,
        $expectedContains
    ): void {

        $callback();

        $response = (new Middleware())->handle(
            new Request(),
            function ($request) use ($view) {
                return new Response((string) view($view));
            }
        );

        $expectedContains = is_array($expectedContains) ? $expectedContains : [$expectedContains];
        $content = $response->getContent();

        foreach ($expectedContains as $expectedContain) {
            $this->assertStringContainsString($expectedContain, $content);
        }
    }
}
