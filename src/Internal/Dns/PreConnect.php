<?php

namespace CodeDistortion\TagTools\Internal\Dns;

use CodeDistortion\TagTools\Internal\DnsSourceInterface;
use CodeDistortion\TagTools\Internal\Traits\HasPriorityTrait;

/**
 * Represent a source of dns that will linked to.
 */
class PreConnect implements DnsSourceInterface
{
    use HasPriorityTrait;


    /**
     * The scheme + domain to connect to.
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
     * Add the "crossorigin" attribute?.
     *
     * @var boolean
     */
    protected $crossorigin = false;


    /**
     * Constructor.
     *
     * @param string  $url         The scheme + domain to connect to.
     * @param boolean $crossorigin Add the "crossorigin" attribute?
     */
    public function __construct(string $url, bool $crossorigin = false)
    {
        $this->url = $url;
        $this->host = mb_strtolower(parse_url($url, PHP_URL_HOST));
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
                .'rel="preconnect" '
                .'href="'.e($this->url).'" '
                .($this->crossorigin ? 'crossorigin ' : '')
            .'/>'
            .PHP_EOL;
    }
}
