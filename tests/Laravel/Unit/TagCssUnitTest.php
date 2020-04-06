<?php

namespace CodeDistortion\TagTools\Tests\Laravel\Unit;

use App;
use CodeDistortion\TagTools\TagCss;
use CodeDistortion\TagTools\Tests\Laravel\TestCase;
use Illuminate\Filesystem\Filesystem;

/**
 * Test TagCss
 *
 * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */
class TagCssUnitTest extends TestCase
{
    /**
     * Provides data for the test_css_generation test below.
     *
     * @return array[]
     */
    public function unitTestDataProvider()
    {
        $css1 = 'html { background-color: white; }'.PHP_EOL
            .'body { padding: 0px; }'.PHP_EOL
            .'input { background-color: grey; }'.PHP_EOL
            .'button { background-color: yellow; color: black; }';

        return [

            '[embed css file - remove unused setting] 1' => [
                'pageContent' => '',
                'callback' => function (TagCss $css) {
                    $css->embed(__DIR__.'/css/1.css')->format();
                },
                'expected' =>
                    '<style>'.PHP_EOL
                    .'    html{background-color:white}'.PHP_EOL
                    .'    body{padding:0px}'.PHP_EOL
                    .'    input{background-color:grey}'.PHP_EOL
                    .'    button{background-color:yellow;color:black}'.PHP_EOL
                    .'</style>'.PHP_EOL,
            ],

            '[embed css file - remove unused setting] 2' => [
                'pageContent' => '',
                'callback' => function (TagCss $css) {
                    $css->embed(__DIR__.'/css/1.css')->removeUnused()->format();
                },
                'expected' => '',
            ],

            '[embed css file - remove unused setting] 3' => [
                'pageContent' => '',
                'callback' => function (TagCss $css) {
                    $css->embed(__DIR__.'/css/1.css')->removeUnused(true)->format();
                },
                'expected' => '',
            ],

            '[embed css file - remove unused setting] 4' => [
                'pageContent' => '',
                'callback' => function (TagCss $css) {
                    $css->embed(__DIR__.'/css/1.css')->removeUnused(false)->format();
                },
                'expected' =>
                    '<style>'.PHP_EOL
                    .'    html{background-color:white}'.PHP_EOL
                    .'    body{padding:0px}'.PHP_EOL
                    .'    input{background-color:grey}'.PHP_EOL
                    .'    button{background-color:yellow;color:black}'.PHP_EOL
                    .'</style>'.PHP_EOL,
            ],

            '[embed css file - remove unused setting] 4b' => [
                'pageContent' => '<html><input></html>',
                'callback' => function (TagCss $css) {
                    $css->embed(__DIR__.'/css/1.css')->removeUnused(true);
                },
                'expected' =>
                    '<style>'.PHP_EOL
                    .'    html{background-color:white}'.PHP_EOL
                    .'    input{background-color:grey}'.PHP_EOL
                    .'</style>'.PHP_EOL,
            ],

            '[embed css file - remove unused setting] 5' => [
                'pageContent' => '<html></html>',
                'callback' => function (TagCss $css) {
                    $css->embed(__DIR__.'/css/1.css')->removeUnused()->format();
                },
                'expected' =>
                    '<style>'.PHP_EOL
                    .'    html{background-color:white}'.PHP_EOL
                    .'</style>'.PHP_EOL,
            ],


            '[embed css string - remove unused setting] 1' => [
                'pageContent' => '',
                'callback' => function (TagCss $css) use ($css1) {
                    $css->embed($css1)->format();
                },
                'expected' =>
                    '<style>'.PHP_EOL
                    .'    html{background-color:white}'.PHP_EOL
                    .'    body{padding:0px}'.PHP_EOL
                    .'    input{background-color:grey}'.PHP_EOL
                    .'    button{background-color:yellow;color:black}'.PHP_EOL
                    .'</style>'.PHP_EOL,
            ],

            '[embed css string - remove unused setting] 2' => [
                'pageContent' => '',
                'callback' => function (TagCss $css) use ($css1) {
                    $css->embed($css1)->removeUnused()->format();
                },
                'expected' => '',
            ],

            '[embed css string - remove unused setting] 3' => [
                'pageContent' => '',
                'callback' => function (TagCss $css) use ($css1) {
                    $css->embed($css1)->removeUnused(true)->format();
                },
                'expected' => '',
            ],

            '[embed css string - remove unused setting] 4' => [
                'pageContent' => '',
                'callback' => function (TagCss $css) use ($css1) {
                    $css->embed($css1)->removeUnused(false)->format();
                },
                'expected' =>
                    '<style>'.PHP_EOL
                    .'    html{background-color:white}'.PHP_EOL
                    .'    body{padding:0px}'.PHP_EOL
                    .'    input{background-color:grey}'.PHP_EOL
                    .'    button{background-color:yellow;color:black}'.PHP_EOL
                    .'</style>'.PHP_EOL,
            ],

            '[embed css string - remove unused setting] 5' => [
                'pageContent' => '<html></html>',
                'callback' => function (TagCss $css) use ($css1) {
                    $css->embed($css1)->removeUnused()->format();
                },
                'expected' =>
                    '<style>'.PHP_EOL
                    .'    html{background-color:white}'.PHP_EOL
                    .'</style>'.PHP_EOL,
            ],


            '[link to css url] 1' => [
                'pageContent' => '<html></html>',
                'callback' => function (TagCss $css) {
                    $css->link('/css/1.css');
                },
                'expected' => '<link href="/css/1.css" rel="stylesheet" />'.PHP_EOL,
            ],

            '[link to css url] 2' => [
                'pageContent' => '<html></html>',
                'callback' => function (TagCss $css) {
                    $css->link('/css/1.css');
                    $css->link('/css/2.css');
                },
                'expected' =>
                    '<link href="/css/1.css" rel="stylesheet" />'.PHP_EOL
                    .'<link href="/css/2.css" rel="stylesheet" />'.PHP_EOL,
            ],


            '[embed css file - remove unused, embed css file - remove unused] 1' => [
                'pageContent' => '<html></html>',
                'callback' => function (TagCss $css) {
                    $css->embed(__DIR__.'/css/1.css')->removeUnused(true)->format();
                    $css->embed(__DIR__.'/css/2.css')->removeUnused(true)->format();
                },
                'expected' =>
                    '<style>'.PHP_EOL
                    .'    html{background-color:white}'.PHP_EOL
                    .'</style>'.PHP_EOL,
            ],

            '[embed css file - remove unused, embed css file - remove unused] 2' => [
                'pageContent' => '',
                'callback' => function (TagCss $css) {
                    $css->link('/css/1.css');
                    $css->embed(__DIR__.'/css/1.css')->removeUnused(true)->format();
                    $css->link('/css/2.css');
                },
                'expected' =>
                    '<link href="/css/1.css" rel="stylesheet" />'.PHP_EOL
                    .'<link href="/css/2.css" rel="stylesheet" />'.PHP_EOL,
            ],

            '[embed css file - remove unused, embed css file - all css]' => [
                'pageContent' => '<html></html>',
                'callback' => function (TagCss $css) {
                    $css->embed(__DIR__.'/css/1.css')->removeUnused(true)->format();
                    $css->embed(__DIR__.'/css/2.css')->removeUnused(false)->format();
                },
                'expected' =>
                    '<style>'.PHP_EOL
                    .'    html{background-color:white}'.PHP_EOL
                    .'    input{background-color:blue}'.PHP_EOL
                    .'</style>'.PHP_EOL,
            ],


            '[embed css file - remove unused, link to css url, embed css file - all css] 1' => [
                'pageContent' => '<html></html>',
                'callback' => function (TagCss $css) {
                    $css->embed(__DIR__.'/css/1.css')->removeUnused(true)->format();
                    $css->link('/css/1.css');
                    $css->embed(__DIR__.'/css/2.css')->removeUnused(false)->format();
                },
                'expected' =>
                    '<style>'.PHP_EOL
                    .'    html{background-color:white}'.PHP_EOL
                    .'</style>'.PHP_EOL
                    .'<link href="/css/1.css" rel="stylesheet" />'.PHP_EOL
                    .'<style>'.PHP_EOL
                    .'    input{background-color:blue}'.PHP_EOL
                    .'</style>'.PHP_EOL,
            ],


            '[embed css file - remove unused, link to css url, embed css file - all css] 2' => [
                'pageContent' => '<html></html>',
                'callback' => function (TagCss $css) {
                    $css->embed(__DIR__.'/css/1.css')->removeUnused(true)->format();
                    $css->link('/css/1.css');
                    $css->embed(__DIR__.'/css/2.css')->removeUnused(false)->format();
                },
                'expected' =>
                    '<style>'.PHP_EOL
                    .'    html{background-color:white}'.PHP_EOL
                    .'</style>'.PHP_EOL
                    .'<link href="/css/1.css" rel="stylesheet" />'.PHP_EOL
                    .'<style>'.PHP_EOL
                    .'    input{background-color:blue}'.PHP_EOL
                    .'</style>'.PHP_EOL,
            ],


            '[embed css file - remove unused, link to css url, embed css file - all css] different priorities 1' => [
                'pageContent' => '<html></html>',
                'callback' => function (TagCss $css) {
                    $css->embed(__DIR__.'/css/1.css')->priority(1)->removeUnused(true)->format();
                    $css->link('/css/1.css');
                    $css->embed(__DIR__.'/css/2.css')->priority(1)->removeUnused(false)->format();
                },
                'expected' =>
                    '<style>'.PHP_EOL
                    .'    html{background-color:white}'.PHP_EOL
                    .'    input{background-color:blue}'.PHP_EOL
                    .'</style>'.PHP_EOL
                    .'<link href="/css/1.css" rel="stylesheet" />'.PHP_EOL,
            ],


            '[embed css file - remove unused, link to css url, embed css file - all css] different priorities 2' => [
                'pageContent' => '<html></html>',
                'callback' => function (TagCss $css) {
                    $css->embed(__DIR__.'/css/1.css')->removeUnused(true)->format();
                    $css->link('/css/1.css')->priority(1);
                    $css->embed(__DIR__.'/css/2.css')->removeUnused(false)->format();
                },
                'expected' =>
                    '<link href="/css/1.css" rel="stylesheet" />'.PHP_EOL
                    .'<style>'.PHP_EOL
                    .'    html{background-color:white}'.PHP_EOL
                    .'    input{background-color:blue}'.PHP_EOL
                    .'</style>'.PHP_EOL,
            ],


            '[embed css file - remove unused] additional html from file' => [
                'pageContent' => '<html></html>',
                'callback' => function (TagCss $css) {
                    $css->embed(__DIR__.'/css/1.css')->removeUnused()->format();
                    $css->additionalHtml(__DIR__.'/html/1.html');
                },
                'expected' =>
                    '<style>'.PHP_EOL
                    .'    html{background-color:white}'.PHP_EOL
                    .'    body{padding:0px}'.PHP_EOL
                    .'    input{background-color:grey}'.PHP_EOL
                    .'</style>'.PHP_EOL,
            ],

            '[embed css file - remove unused] additional html from 2 files' => [
                'pageContent' => '<html></html>',
                'callback' => function (TagCss $css) {
                    $css->embed(__DIR__.'/css/1.css')->format();
                    $css->additionalHtml(__DIR__.'/html/1.html');
                    $css->additionalHtml(__DIR__.'/html/2.html');
                },
                'expected' =>
                    '<style>'.PHP_EOL
                    .'    html{background-color:white}'.PHP_EOL
                    .'    body{padding:0px}'.PHP_EOL
                    .'    input{background-color:grey}'.PHP_EOL
                    .'    button{background-color:yellow;color:black}'.PHP_EOL
                    .'</style>'.PHP_EOL,
            ],


            '[embed css file - remove unused] additional html from string' => [
                'pageContent' => '<html></html>',
                'callback' => function (TagCss $css) {
                    $css->embed(__DIR__.'/css/1.css')->removeUnused()->format();
                    $css->additionalHtml('<body><input></body>');
                },
                'expected' =>
                    '<style>'.PHP_EOL
                    .'    html{background-color:white}'.PHP_EOL
                    .'    body{padding:0px}'.PHP_EOL
                    .'    input{background-color:grey}'.PHP_EOL
                    .'</style>'.PHP_EOL,
            ],

            '[embed css file - remove unused] additional html from string 2' => [
                'pageContent' => '<html></html>',
                'callback' => function (TagCss $css) {
                    $css->embed(__DIR__.'/css/1.css')->format();
                    $css->additionalHtml('<body><input></body>');
                },
                'expected' =>
                    '<style>'.PHP_EOL
                    .'    html{background-color:white}'.PHP_EOL
                    .'    body{padding:0px}'.PHP_EOL
                    .'    input{background-color:grey}'.PHP_EOL
                    .'    button{background-color:yellow;color:black}'.PHP_EOL
                    .'</style>'.PHP_EOL,
            ],

            '[embed 2 times with media queries]' => [
                'pageContent' => '<html><input></html>',
                'callback' => function (TagCss $css) {
                    $css->embed(
                        'input { background-color: blue; }'.PHP_EOL
                        .'@media print and (-ms-high-contrast: active) {'.PHP_EOL
                        .'input { background-color: red; }'.PHP_EOL
                        .'}'
                    )->format();
                    $css->embed(
                        'input { background-color: green; }'.PHP_EOL
                        .'@media print and (-ms-high-contrast: active) {'.PHP_EOL
                        .'input { background-color: orange; }'.PHP_EOL
                        .'}'
                    )->format();
                },
                'expected' =>
                    '<style>'.PHP_EOL
                    .'    input{background-color:blue}'.PHP_EOL
                    .'    input{background-color:green}'.PHP_EOL
                    .'    @media print and (-ms-high-contrast: active){'.PHP_EOL
                    .'        input{background-color:red}'.PHP_EOL
                    .'        input{background-color:orange}'.PHP_EOL
                    .'    }'.PHP_EOL
                    .'</style>'.PHP_EOL,
            ],

            '[embed 2 times with media queries and a link]' => [
                'pageContent' => '<html><input></html>',
                'callback' => function (TagCss $css) {
                    $css->embed(
                        'input { background-color: blue; }'.PHP_EOL
                        .'@media print and (-ms-high-contrast: active) {'.PHP_EOL
                        .'input { background-color: red; }'.PHP_EOL
                        .'}'
                    )->format();
                    $css->link('/css/1.css');
                    $css->embed(
                        'input { background-color: green; }'.PHP_EOL
                        .'@media print and (-ms-high-contrast: active) {'.PHP_EOL
                        .'input { background-color: orange; }'.PHP_EOL
                        .'}'
                    )->format();
                },
                'expected' =>
                    '<style>'.PHP_EOL
                    .'    input{background-color:blue}'.PHP_EOL
                    .'    @media print and (-ms-high-contrast: active){'.PHP_EOL
                    .'        input{background-color:red}'.PHP_EOL
                    .'    }'.PHP_EOL
                    .'</style>'.PHP_EOL
                    .'<link href="/css/1.css" rel="stylesheet" />'.PHP_EOL
                    .'<style>'.PHP_EOL
                    .'    input{background-color:green}'.PHP_EOL
                    .'    @media print and (-ms-high-contrast: active){'.PHP_EOL
                    .'        input{background-color:orange}'.PHP_EOL
                    .'    }'.PHP_EOL
                    .'</style>'.PHP_EOL
                ,
            ],

            '[embed css raw]' => [
                'pageContent' => '',
                'callback' => function (TagCss $css) {
                    $css->embed(__DIR__.'/css/1.css')->raw();
                },
                'expected' =>
                    '<style>'.PHP_EOL
                    .'html { background-color: white; }'.PHP_EOL
                    .'body { padding: 0px; }'.PHP_EOL
                    .'input { background-color: grey; }'.PHP_EOL
                    .'button { background-color: yellow; color: black; }'.PHP_EOL
                    .'</style>'.PHP_EOL,
            ],

            '[embed css raw x 2]' => [
                'pageContent' => '',
                'callback' => function (TagCss $css) {
                    $css->embed(__DIR__.'/css/1.css')->raw();
                    $css->embed(__DIR__.'/css/2.css')->raw();
                },
                'expected' =>
                    '<style>'.PHP_EOL
                    .'html { background-color: white; }'.PHP_EOL
                    .'body { padding: 0px; }'.PHP_EOL
                    .'input { background-color: grey; }'.PHP_EOL
                    .'button { background-color: yellow; color: black; }'.PHP_EOL
                    .'input { background-color: blue; }'.PHP_EOL
                    .'</style>'.PHP_EOL,
            ],

            '[embed css formatted]' => [
                'pageContent' => '',
                'callback' => function (TagCss $css) {
                    $css->embed(__DIR__.'/css/1.css')->format();
                },
                'expected' =>
                    '<style>'.PHP_EOL
                    .'    html{background-color:white}'.PHP_EOL
                    .'    body{padding:0px}'.PHP_EOL
                    .'    input{background-color:grey}'.PHP_EOL
                    .'    button{background-color:yellow;color:black}'.PHP_EOL
                    .'</style>'.PHP_EOL,
            ],

            '[embed css formatted x 2]' => [
                'pageContent' => '',
                'callback' => function (TagCss $css) {
                    $css->embed(__DIR__.'/css/1.css')->format();
                    $css->embed(__DIR__.'/css/2.css')->format();
                },
                'expected' =>
                    '<style>'.PHP_EOL
                    .'    html{background-color:white}'.PHP_EOL
                    .'    body{padding:0px}'.PHP_EOL
                    .'    input{background-color:grey}'.PHP_EOL
                    .'    button{background-color:yellow;color:black}'.PHP_EOL
                    .'    input{background-color:blue}'.PHP_EOL
                    .'</style>'.PHP_EOL,
            ],

            '[embed css minified]' => [
                'pageContent' => '',
                'callback' => function (TagCss $css) {
                    $css->embed(__DIR__.'/css/1.css')->minify();
                },
                'expected' =>
                    '<style>'.PHP_EOL
                    .'    html{background-color:white}'
                    .'body{padding:0px}'
                    .'input{background-color:grey}'
                    .'button{background-color:yellow;'
                    .'color:black}'.PHP_EOL
                    .'</style>'.PHP_EOL,
            ],

            '[embed css minified x 2]' => [
                'pageContent' => '',
                'callback' => function (TagCss $css) {
                    $css->embed(__DIR__.'/css/1.css')->minify();
                    $css->embed(__DIR__.'/css/2.css')->minify();
                },
                'expected' =>
                    '<style>'.PHP_EOL
                    .'    html{background-color:white}'
                    .'body{padding:0px}'
                    .'input{background-color:grey}'
                    .'button{background-color:yellow;'
                    .'color:black}'
                    .'input{background-color:blue}'.PHP_EOL
                    .'</style>'.PHP_EOL,
            ],
        ];
    }

    /**
     * Test the output that TagCss generates.
     *
     * @test
     * @dataProvider unitTestDataProvider
     *
     * @param string   $pageContent The html page that css will be injected in to.
     * @param callable $callback    A callback that will apply various inputs.
     * @param string   $expected    The expected output.
     * @return void
     */
    public function test_css_generation(string $pageContent, callable $callback, string $expected): void
    {
        $tagCss = new TagCss(new Filesystem(), '');
        $callback($tagCss);
        $this->assertSame($expected, $tagCss->render($pageContent));
    }
}
