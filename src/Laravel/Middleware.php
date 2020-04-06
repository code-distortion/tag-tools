<?php

namespace CodeDistortion\TagTools\Laravel;

use Closure;
use CodeDistortion\TagTools\Settings;
use CodeDistortion\TagTools\TagCss;
use CodeDistortion\TagTools\TagDns;
use CodeDistortion\TagTools\TagFav;
use CodeDistortion\TagTools\TagJs;
use Config;
use Illuminate\Http\Request;

/**
 * Performs the necessary replacements into the output.
 */
class Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // take the output
        $response = $next($request);
        $content = $response->getContent();


        // inject custom css based on the contents of the html
        $tagCss = app(TagCss::getAlias());
        /** @var TagCss $tagCss */
        $content = $tagCss->replaceStubs(
            $content,
            (bool) Config::get(Settings::LARAVEL_CONFIG_NAME.'.css_timings')
        );

        // inject custom favs
        $tagFav = app(TagFav::getAlias());
        /** @var TagFav $tagFav */
        $content = $tagFav->replaceStubs($content);

        // inject custom javascript
        $tagJs = app(TagJs::getAlias());
        /** @var TagJs $tagJs */
        $content = $tagJs->replaceStubs($content);

        // inject custom dns-prefeth
        $tagDns = app(TagDns::getAlias());
        /** @var TagDns $tagDns */
        $content = $tagDns->replaceStubs(
            $content,
            $request->getHost(),
            (array) Config::get(Settings::LARAVEL_CONFIG_NAME.'.dns_prefetch_tags'));

        $response->setContent($content);
        return $response;
    }
}
