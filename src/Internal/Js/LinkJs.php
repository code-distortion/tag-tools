<?php

namespace CodeDistortion\TagTools\Internal\Js;

use CodeDistortion\TagTools\Internal\Traits\HasPriorityTrait;
use CodeDistortion\TagTools\Internal\JsSourceInterface;

/**
 * Represent a source of javascript that will linked to.
 */
class LinkJs implements JsSourceInterface
{
    use HasPriorityTrait;


    /**
     * The url to the js file.
     *
     * @var string
     */
    protected $url;


    /**
     * Constructor.
     *
     * @param string $url The js file url.
     */
    public function __construct(string $url)
    {
        $this->url = $url;
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
     * Generate the output to use.
     *
     * @param string $leadingWhitespace The whitespace to add to the beginning of each line.
     * @return string
     */
    public function render(string $leadingWhitespace = ''): string
    {
        return $leadingWhitespace.'<script src="'.e($this->url).'"></script>'.PHP_EOL;
    }
}
