<?php

namespace CodeDistortion\TagTools\Tests\Laravel\Unit;

use App;
use CodeDistortion\TagTools\TagFav;
use CodeDistortion\TagTools\Tests\Laravel\TestCase;

/**
 * Test TagFav
 *
 * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */
class TagFavUnitTest extends TestCase
{
    /**
     * Provides data for the test_fav_generation test below.
     *
     * @return array[]
     */
    public function unitTestDataProvider()
    {

        return [
            'test1' => [
                'callback' => function (TagFav $fav) {
                    $fav->add('<abc>');
                },
                'expected' =>
                    '<abc>'.PHP_EOL,
            ],

            'test2' => [
                'callback' => function (TagFav $fav) {
                    $fav->add('<abc>');
                    $fav->add('<abc2>');
                },
                'expected' =>
                    '<abc>'.PHP_EOL
                    .'<abc2>'.PHP_EOL,
            ],

            'test3' => [
                'callback' => function (TagFav $fav) {
                    $fav->add('<abc>');
                    $fav->add('<abc>');
                },
                'expected' =>
                    '<abc>'.PHP_EOL,
            ],
        ];
    }

    /**
     * Test the output that TagFav generates.
     *
     * @test
     * @dataProvider unitTestDataProvider
     *
     * @param callable $callback A callback that will apply various inputs.
     * @param string   $expected The expected output.
     * @return void
     */
    public function test_fav_generation(callable $callback, string $expected): void
    {
        $tagFav = new TagFav();
        $callback($tagFav);
        $this->assertSame($expected, $tagFav->render(''));
    }
}
