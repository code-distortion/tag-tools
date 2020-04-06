<?php

namespace CodeDistortion\TagTools\Tests\Laravel\Integration;

use App;
use CodeDistortion\TagTools\Laravel\Middleware;
use CodeDistortion\TagTools\Tests\Laravel\TestCase;
use Config;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use TagJs;

/**
 * Test TagJs' Laravel integration.
 *
 * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */
class TagJsIntegrationTest extends TestCase
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
                'view' => 'test::view-js-1',
                'callback' => function () {
                    TagJs::embed('console.log("hello");');
                },
                'expectedContains' =>
                    '<title>View 1</title>'.PHP_EOL
                    .'        <script>'.PHP_EOL
                    .'console.log("hello");'.PHP_EOL
                    .'        </script>'.PHP_EOL
                    .PHP_EOL
                    .'    </head>'.PHP_EOL,
            ],

            'test2' => [
                'view' => 'test::view-js-1',
                'callback' => function () {
                    TagJs::embed('console.log("hello");');
                    TagJs::embed('console.log("hello2");');
                },
                'expectedContains' =>
                    '<title>View 1</title>'.PHP_EOL
                    .'        <script>'.PHP_EOL
                    .'console.log("hello");'.PHP_EOL
                    .'        </script>'.PHP_EOL
                    .'        <script>'.PHP_EOL
                    .'console.log("hello2");'.PHP_EOL
                    .'        </script>'.PHP_EOL
                    .PHP_EOL
                    .'    </head>'.PHP_EOL,
            ],

            'test3' => [
                'view' => 'test::view-js-2',
                'callback' => function () {
                    TagJs::embed('console.log("hello");');
                },
                'expectedContains' =>
                    '<title>View 2</title>'.PHP_EOL
                    .'        <script>'.PHP_EOL
                    .'console.log("hello");'.PHP_EOL
                    .'        </script>'.PHP_EOL
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
    public function test_that_middleware_puts_js_in(
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
