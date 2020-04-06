<?php

namespace CodeDistortion\TagTools\Tests\Laravel\Unit;

use App;
use CodeDistortion\TagTools\TagDns;
use CodeDistortion\TagTools\Tests\Laravel\TestCase;

/**
 * Test TagDns
 *
 * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */
class TagDnsUnitTest extends TestCase
{
    /**
     * Provides data for the test_js_generation test below.
     *
     * @return array[]
     */
    public function unitTestDataProvider()
    {

        return [
            '[dns-prefetch] 1' => [
                'callback' => function (TagDns $dns) {
                    $dns->dnsPrefetch('//test.com');
                },
                'expected' =>
                    '<link rel="dns-prefetch" href="//test.com" />'.PHP_EOL,
            ],

            '[dns-prefetch] 2' => [
                'callback' => function (TagDns $dns) {
                    $dns->dnsPrefetch('//test.com');
                    $dns->dnsPrefetch('//test.com');
                },
                'expected' =>
                    '<link rel="dns-prefetch" href="//test.com" />'.PHP_EOL,
            ],

            '[dns-prefetch] 3' => [
                'callback' => function (TagDns $dns) {
                    $dns->dnsPrefetch('//test.com');
                    $dns->dnsPrefetch('//test2.com');
                },
                'expected' =>
                    '<link rel="dns-prefetch" href="//test.com" />'.PHP_EOL
                    .'<link rel="dns-prefetch" href="//test2.com" />'.PHP_EOL,
            ],


            '[preconnect] 1' => [
                'callback' => function (TagDns $dns) {
                    $dns->preConnect('//test.com');
                },
                'expected' =>
                    '<link rel="preconnect" href="//test.com" />'.PHP_EOL,
            ],

            '[preconnect] 2' => [
                'callback' => function (TagDns $dns) {
                    $dns->preConnect('//test.com', false);
                },
                'expected' =>
                    '<link rel="preconnect" href="//test.com" />'.PHP_EOL,
            ],

            '[preconnect] 3' => [
                'callback' => function (TagDns $dns) {
                    $dns->preConnect('//test.com', true);
                },
                'expected' =>
                    '<link rel="preconnect" href="//test.com" crossorigin />'.PHP_EOL,
            ],

            '[preconnect] 4' => [
                'callback' => function (TagDns $dns) {
                    $dns->preConnect('//test.com');
                    $dns->preConnect('//test.com');
                },
                'expected' =>
                    '<link rel="preconnect" href="//test.com" />'.PHP_EOL,
            ],

            '[preconnect] 5' => [
                'callback' => function (TagDns $dns) {
                    $dns->preConnect('//test.com');
                    $dns->preConnect('//test2.com');
                },
                'expected' =>
                    '<link rel="preconnect" href="//test.com" />'.PHP_EOL
                    .'<link rel="preconnect" href="//test2.com" />'.PHP_EOL,
            ],


            '[prefetch] 1' => [
                'callback' => function (TagDns $dns) {
                    $dns->preFetch('//test.com/blah');
                },
                'expected' =>
                    '<link rel="prefetch" href="//test.com/blah" />'.PHP_EOL,
            ],

            '[prefetch] 2' => [
                'callback' => function (TagDns $dns) {
                    $dns->preFetch('//test.com/blah', 'audio');
                },
                'expected' =>
                    '<link rel="prefetch" href="//test.com/blah" as="audio" />'.PHP_EOL,
            ],

            '[prefetch] 3' => [
                'callback' => function (TagDns $dns) {
                    $dns->preFetch('//test.com/blah', 'audio', true);
                },
                'expected' =>
                    '<link rel="prefetch" href="//test.com/blah" as="audio" crossorigin />'.PHP_EOL,
            ],

            '[prefetch] 4' => [
                'callback' => function (TagDns $dns) {
                    $dns->preFetch('//test.com/blah', 'audio', 'use-credentials');
                },
                'expected' =>
                    '<link rel="prefetch" href="//test.com/blah" as="audio" crossorigin="use-credentials" />'.PHP_EOL,
            ],

            '[prefetch] 5' => [
                'callback' => function (TagDns $dns) {
                    $dns->preFetch('//test.com/blah');
                    $dns->preFetch('//test.com/blah');
                },
                'expected' =>
                    '<link rel="prefetch" href="//test.com/blah" />'.PHP_EOL,
            ],

            '[prefetch] 6' => [
                'callback' => function (TagDns $dns) {
                    $dns->preFetch('//test.com/blah');
                    $dns->preFetch('//test.com/blah', 'audio', 'use-credentials');
                },
                'expected' =>
                    '<link rel="prefetch" href="//test.com/blah" as="audio" crossorigin="use-credentials" />'.PHP_EOL,
            ],

            '[prefetch] 7' => [
                'callback' => function (TagDns $dns) {
                    $dns->preFetch('//test.com/blah');
                    $dns->preFetch('//test2.com/blah', 'audio', 'use-credentials');
                },
                'expected' =>
                    '<link rel="prefetch" href="//test.com/blah" />'.PHP_EOL
                    .'<link rel="prefetch" href="//test2.com/blah" as="audio" crossorigin="use-credentials" />'.PHP_EOL,
            ],


            '[prerender] 1' => [
                'callback' => function (TagDns $dns) {
                    $dns->preRender('//test.com');
                },
                'expected' =>
                    '<link rel="prerender" href="//test.com" />'.PHP_EOL,
            ],

            '[prerender] 2' => [
                'callback' => function (TagDns $dns) {
                    $dns->preRender('//test.com');
                    $dns->preRender('//test.com');
                },
                'expected' =>
                    '<link rel="prerender" href="//test.com" />'.PHP_EOL,
            ],

            '[prerender] 3' => [
                'callback' => function (TagDns $dns) {
                    $dns->preRender('//test.com');
                    $dns->preRender('//test2.com');
                },
                'expected' =>
                    '<link rel="prerender" href="//test.com" />'.PHP_EOL
                    .'<link rel="prerender" href="//test2.com" />'.PHP_EOL,
            ],
        ];
    }

    /**
     * Test the output that TagDns generates.
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
        $tagDns = new TagDns();
        $callback($tagDns);
        $this->assertSame($expected, $tagDns->render(''));
    }
}
