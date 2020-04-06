<?php

namespace CodeDistortion\TagTools\Tests\Laravel\Integration;

use App;
use CodeDistortion\TagTools\Laravel\Middleware;
use CodeDistortion\TagTools\Settings;
use CodeDistortion\TagTools\Tests\Laravel\TestCase;
use Config;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use TagCss;

/**
 * Test TagCss' Laravel integration.
 *
 * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */
class TagCssIntegrationTest extends TestCase
{
    /**
     * Provide input for the test_that_middleware_puts_css_in test
     *
     * @return array[]
     */
    public function middlewareOutputDataProvider(): array
    {
        return [

            'test1' => [
                'view' => 'test::view-css-1',
                'addTimings' => false,
                'callback' => function () {
                    TagCss::embed('input { color:black; } body { background-color: red; }');
                },
                'expectedContains' =>
                    '        <title>View 1</title>'.PHP_EOL
                    .'        <style>'.PHP_EOL
                    .'input { color:black; } body { background-color: red; }'.PHP_EOL
                    .'        </style>'.PHP_EOL
                    .PHP_EOL
                    .'    </head>'.PHP_EOL,
            ],

            'test2' => [
                'view' => 'test::view-css-1',
                'addTimings' => false,
                'callback' => function () {
                    TagCss::embed('input { color:black; } body { background-color: red; }')->raw();
                },
                'expectedContains' =>
                    '        <title>View 1</title>'.PHP_EOL
                    .'        <style>'.PHP_EOL
                    .'input { color:black; } body { background-color: red; }'.PHP_EOL
                    .'        </style>'.PHP_EOL
                    .PHP_EOL
                    .'    </head>'.PHP_EOL,
            ],

            'test3' => [
                'view' => 'test::view-css-1',
                'addTimings' => false,
                'callback' => function () {
                    TagCss::embed('input { color:black; } body { background-color: red; }')->format();
                },
                'expectedContains' =>
                    '        <title>View 1</title>'.PHP_EOL
                    .'        <style>'.PHP_EOL
                    .'            input{color:black}'.PHP_EOL
                    .'            body{background-color:red}'.PHP_EOL
                    .'        </style>'.PHP_EOL
                    .PHP_EOL
                    .'    </head>'.PHP_EOL,
            ],

            'test4' => [
                'view' => 'test::view-css-1',
                'addTimings' => false,
                'callback' => function () {
                    TagCss::embed('input { color:black; } body { background-color: red; }')->minify();
                },
                'expectedContains' =>
                    '        <title>View 1</title>'.PHP_EOL
                    .'        <style>'.PHP_EOL
                    .'            input{color:black}body{background-color:red}'.PHP_EOL
                    .'        </style>'.PHP_EOL
                    .PHP_EOL
                    .'    </head>'.PHP_EOL,
            ],

            'test5' => [
                'view' => 'test::view-css-1',
                'addTimings' => false,
                'callback' => function () {
                    TagCss::link('/css/1.css');
                },
                'expectedContains' =>
                    '<title>View 1</title>'.PHP_EOL
                    .'        <link href="/css/1.css" rel="stylesheet" />'.PHP_EOL
                    .PHP_EOL
                    .'    </head>'.PHP_EOL,
            ],

            'test6' => [
                'view' => 'test::view-css-1',
                'addTimings' => true,
                'callback' => function () {
                    TagCss::link('/css/1.css');
                },
                'expectedContains' => ['<!--', 'ms -->',],
            ],

            'test7' => [
                'view' => 'test::view-css-2',
                'addTimings' => false,
                'callback' => function () {
                    TagCss::embed('input { color:black; } body { background-color: red; }');
                },
                'expectedContains' =>
                    '        <title>View 2</title>'.PHP_EOL
                    .'    </head>'.PHP_EOL
            ],

            'test8' => [
                'view' => 'test::view-css-3',
                'addTimings' => false,
                'callback' => function () {
                    TagCss::embed('input { color:black; }')->minify();
                },
                'expectedContains' =>
                    '<title>View 3</title>'.PHP_EOL
                    .'        <style>'.PHP_EOL
                    .'            input{color:black}'.PHP_EOL
                    .'        </style>'.PHP_EOL.PHP_EOL
                    .'        <style>'.PHP_EOL
                    .'            input{color:black}'.PHP_EOL
                    .'        </style>'.PHP_EOL
                    .PHP_EOL
                    .'    </head>'.PHP_EOL,
            ],

            'test9' => [
                'view' => 'test::view-css-4',
                'addTimings' => 'break',
                'callback' => function () {
                    TagCss::embed('input { color:black; }')->minify();
                },
                'expectedContains' => [
                    '<title>View 4</title>'.PHP_EOL
                    .'        <style>'.PHP_EOL
                    .'            input{color:black}'.PHP_EOL
                    .'        </style>'.PHP_EOL
                    .'        <!-- ',
                    '-->'.PHP_EOL
                    .'    </head>'.PHP_EOL,
                ],
            ],

            'test10' => [
                'view' => 'test::view-css-1',
                'addTimings' => true,
                'callback' => function () {
                },
                'expectedContains' =>
                    '<title>View 1</title>'.PHP_EOL
                    .'        '.PHP_EOL
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
     * @param mixed           $addTimings       Add timings to the css output?.
     * @param callable        $callback         A callback that will apply various css.
     * @param string|string[] $expectedContains The expected output.
     * @return void
     */
    public function test_that_middleware_puts_css_in(
        string $view,
        $addTimings,
        callable $callback,
        $expectedContains
    ): void {

        Config::set(Settings::LARAVEL_CONFIG_NAME.'.css_timings', $addTimings);

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

    /**
     * Test that the config contains values.
     *
     * @test
     * @return void
     */
    public function test_that_the_config_contains_values(): void
    {
        $this->assertNotNull(Config::get(Settings::LARAVEL_CONFIG_NAME.'.css_timings'));
    }
}
