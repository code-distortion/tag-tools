<?php

namespace CodeDistortion\TagTools\Tests\Laravel\Unit;

use App;
use CodeDistortion\TagTools\TagJs;
use CodeDistortion\TagTools\Tests\Laravel\TestCase;
use Illuminate\Filesystem\Filesystem;

/**
 * Test TagJs
 *
 * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */
class TagJsUnitTest extends TestCase
{
    /**
     * Provides data for the test_js_generation test below.
     *
     * @return array[]
     */
    public function unitTestDataProvider()
    {

        return [
            '[link js] 1' => [
                'callback' => function (TagJs $js) {
                    $js->link('/js/1.js');
                },
                'expected' =>
                    '<script src="/js/1.js"></script>'.PHP_EOL,
            ],

            '[link js] 2' => [
                'callback' => function (TagJs $js) {
                    $js->link('/js/1.js');
                    $js->link('/js/1.js');
                },
                'expected' =>
                    '<script src="/js/1.js"></script>'.PHP_EOL,
            ],

            '[link js] 3' => [
                'callback' => function (TagJs $js) {
                    $js->link('/js/1.js');
                    $js->link('/js/2.js');
                },
                'expected' =>
                    '<script src="/js/1.js"></script>'.PHP_EOL
                    .'<script src="/js/2.js"></script>'.PHP_EOL,
            ],

            '[link js] 4' => [
                'callback' => function (TagJs $js) {
                    $js->link('/js/1.js');
                    $js->link('/js/2.js')->priority(1);
                    $js->link('/js/3.js');
                },
                'expected' =>
                    '<script src="/js/2.js"></script>'.PHP_EOL
                    .'<script src="/js/1.js"></script>'.PHP_EOL
                    .'<script src="/js/3.js"></script>'.PHP_EOL,
            ],

            '[defer js] 1' => [
                'callback' => function (TagJs $js) {
                    $js->defer('/js/1.js');
                },
                'expected' =>
                    '<script src="/js/1.js" defer></script>'.PHP_EOL,
            ],

            '[async js] 1' => [
                'callback' => function (TagJs $js) {
                    $js->async('/js/1.js');
                },
                'expected' =>
                    '<script src="/js/1.js" async></script>'.PHP_EOL,
            ],

            '[embed js] 1' => [
                'callback' => function (TagJs $js) {
                    $js->embed('console.log("hello");');
                },
                'expected' =>
                    '<script>'.PHP_EOL
                    .'console.log("hello");'.PHP_EOL
                    .'</script>'.PHP_EOL,
            ],

            '[embed js] 2' => [
                'callback' => function (TagJs $js) {
                    $js->embed(__DIR__.'/js/1.js');
                },
                'expected' =>
                    '<script>'.PHP_EOL
                    .'console.log("1.js");'.PHP_EOL
                    .'</script>'.PHP_EOL,
            ],
        ];
    }

    /**
     * Test the output that TagJs generates.
     *
     * @test
     * @dataProvider unitTestDataProvider
     *
     * @param callable $callback A callback that will apply various inputs.
     * @param string   $expected The expected output.
     * @return void
     */
    public function test_js_generation(callable $callback, string $expected): void
    {
        $tagJs = new TagJs(new Filesystem());
        $callback($tagJs);
        $this->assertSame($expected, $tagJs->render(''));
    }
}
