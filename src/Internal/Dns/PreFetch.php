<?php

namespace CodeDistortion\TagTools\Internal\Dns;

use CodeDistortion\TagTools\Internal\DnsSourceInterface;
use CodeDistortion\TagTools\Internal\Traits\HasPriorityTrait;

/**
 * Represent a source of dns that will linked to.
 */
class PreFetch implements DnsSourceInterface
{
    use HasPriorityTrait;


    /**
     * The url to pre-fetch.
     *
     * @var string
     */
    protected $url;

    /**
     * The domain currently being used.
     *
     * @var string
     */
    protected $host;

    /**
     * The "as" attribute value to add (eg. "script", "document").
     *
     * @see https://www.w3.org/TR/preload/#as-attribute .
     *
     * @var string|null
     */
    protected $as = null;

    /**
     * The "crossorigin" attribute value to add (true for present but empty).
     *
     * @var string|boolean|null
     */
    protected $crossorigin = null;


    /**
     * Constructor.
     *
     * @param string         $url         The url to pre-fetch.
     * @param string         $as          The "as" attribute value to add (eg. "script", "document").
     * @param string|boolean $crossorigin The "crossorigin" attribute value to add (true for present but empty).
     */
    public function __construct(string $url, string $as = null, $crossorigin = null)
    {
        $this->url = $url;
        $this->host = mb_strtolower(parse_url($url, PHP_URL_HOST));
        $this->as = $as;
        $this->crossorigin = $crossorigin;
    }


    /**
     * Generate a hash to detect multiple uses.
     *
     * @return string
     */
    public function duplicationHash(): string
    {
        return __CLASS__.'-'.$this->url;
    }


    /**
     * Retrieve the host.
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Generate the output to use.
     *
     * @param string $leadingWhitespace The whitespace to add to the beginning of each line.
     * @return string
     */
    public function render(string $leadingWhitespace = ''): string
    {
        return $leadingWhitespace
            .'<link '
            .'rel="prefetch" '
            .'href="'.e($this->url).'" '
            .($this->as ? 'as="'.e($this->as).'" ' : '')
            .($this->crossorigin
                ? ($this->crossorigin === true
                    ? 'crossorigin '
                    : 'crossorigin="'.e($this->crossorigin).'" ')
                : '')
            .'/>'
            .PHP_EOL;
    }
}
