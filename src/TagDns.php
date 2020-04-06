<?php

namespace CodeDistortion\TagTools;

use CodeDistortion\TagTools\Internal\Dns\DnsPrefetch;
use CodeDistortion\TagTools\Internal\Dns\PreConnect;
use CodeDistortion\TagTools\Internal\Dns\PreFetch;
use CodeDistortion\TagTools\Internal\Dns\PreRender;
use CodeDistortion\TagTools\Internal\TagAbstract;
use CodeDistortion\TagTools\Internal\Traits\HasCanPrioritiseSourcesTrait;

/**
 * Manage the dns sources that need to be added to the page.
 */
class TagDns extends TagAbstract
{
    use HasCanPrioritiseSourcesTrait;


    /**
     * @avr string[]
     */
    protected $sources = [];

    /**
     * The alias registered for this class in Laravel.
     *
     * @var string
     */
    protected static $alias = 'TagDns';

    /**b
     * The directive registered for use in blade.
     *
     * @var string
     */
    protected static $bladeDirective = 'tagdns';


    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->templateStub = '@TagDns-'.md5(uniqid((string) mt_rand(), true));
    }


    /**
     * Add a domain to prefetch.
     *
     * @param string $url The scheme + domain to pre-fetch.
     * @return DnsPrefetch
     */
    public function dnsPrefetch(string $url): DnsPrefetch
    {
        $source = new DnsPrefetch($url);
        $this->sources[$source->duplicationHash()] = $source;
        return $source;
    }

    /**
     * Add a domain to pre-connect to.
     *
     * @param string  $url         The scheme + domain to connect to.
     * @param boolean $crossorigin Add the "crossorigin" attribute?
     * @return PreConnect
     */
    public function preConnect(string $url, bool $crossorigin = false): PreConnect
    {
        $source = new PreConnect($url, $crossorigin);
        $this->sources[$source->duplicationHash()] = $source;
        return $source;
    }

    /**
     * Add a url to prefetch.
     *
     * @param string         $url         The url to prefetch.
     * @param string         $as          The "as" attribute value to add (eg. "script", "document").
     * @param string|boolean $crossorigin The "crossorigin" attribute value to add (true for present but empty).
     * @return PreFetch
     */
    public function preFetch(string $url, string $as = null, $crossorigin = null): PreFetch
    {
        $source = new PreFetch($url, $as, $crossorigin);
        $this->sources[$source->duplicationHash()] = $source;
        return $source;
    }

    /**
     * Add a url to pre-render.
     *
     * @param string $url The url to pre-render.
     * @return PreRender
     */
    public function preRender(string $url): PreRender
    {
        $source = new PreRender($url);
        $this->sources[$source->duplicationHash()] = $source;
        return $source;
    }


    /**
     * Replace the stubs with favs.
     *
     * @param string   $html           The content to process.
     * @param string   $currentHost    The current request host-name (so it can be excluded from the pre-fetch list).
     * @param string[] $allowedSrcTags The tag types allowed when searching for src='xyz' hosts.
     * @return string
     */
    public function replaceStubs(
        string $html,
        string $currentHost,
        array $allowedSrcTags = []
    ): string {

        $pos = mb_strpos($html, $this->templateStub);
        if ($pos === false) {
            return $html;
        }

        if (count($allowedSrcTags)) {
            $this->detectHosts($html, $allowedSrcTags);
        }

        $currentHost = mb_strtolower($currentHost);
        $count = 20;
        do {
            // render the replacement javascript
            $leadingWhitespace = $this->determineLeadingWhitespace($html, $pos);
            $replacement = $this->render($leadingWhitespace, $currentHost);

            // remove the leading whitespace from the first line
            $replacement = mb_substr($replacement, mb_strlen($leadingWhitespace));

            // replace the first stub
            $html = substr_replace($html, $replacement, $pos, strlen($this->templateStub));

            // look for the next
            $pos = mb_strpos($html, $this->templateStub);
        } while (($pos !== false) && ($count-- > 0));

        return $html;
    }

    /**
     * Generate the output to be injected.
     *
     * @param string      $leadingWhitespace The whitespace to add to the beginning of each line.
     * @param string|null $currentHost       Tags will be ignored if they are for this same host.
     * @return string
     */
    public function render(string $leadingWhitespace = '', ?string $currentHost = null): string
    {
        $return = '';
        foreach ($this->prioritisedSources() as $source) {
            if ($source->getHost() != $currentHost) {
                $return .= $source->render($leadingWhitespace);
            }
        }
        return $return;
    }

    /**
     * Search for host-names in the given html to pre-fetch.
     *
     * @param string   $html
     * @param string[] $allowedSrcTags The tag types allowed when searching for src='xyz' hosts.
     * @return void
     */
    public function detectHosts(
        string $html,
        array $allowedSrcTags
    ): void {

        // search for src="xyz" attributes in html tags
        $regex = '/'
            .'<' // start tag
            .'([^ >]*) ' // tag type
            .'[^>]*' // space
            .'(?:src|href) *= *(?:"([^"]*)"|\'([^\']*)\')' // the src attribute we're searching for
            .'[^>]*' // space
            .'>'  // end of tag
            .'/i';
        preg_match_all($regex, $html, $matches);
        foreach (array_keys($matches[0]) as $index) {

            $tagType = mb_strtolower($matches[1][$index]);
            $url = ($matches[2][$index] ? $matches[2][$index] : $matches[3][$index]);
            $scheme = mb_strtolower(parse_url($url, PHP_URL_SCHEME));
            $host = mb_strtolower(parse_url($url, PHP_URL_HOST));

            if ($host) {
                if ((in_array('*', $allowedSrcTags)) || (in_array($tagType, $allowedSrcTags))) {
                    $this->dnsPrefetch(($scheme ? $scheme.':' : '').'//'.$host.'/');
                }
            }
        }
    }
}
